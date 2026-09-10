<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Room;
use App\Support\AuditLogger;
use App\Support\CacheKeys;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    private const STATUSES = ['draft', 'sent', 'paid', 'overdue', 'cancelled'];

    private const PAYMENT_METHODS = ['cash', 'bank_transfer', 'credit_card', 'e_wallet', 'other'];

    private const DEFAULT_TAX_RATE = 0.07;

    private const DEFAULT_LATE_FEE_RATE = 0.01;

    /**
     * ✅ REFACTOR: คำนวณค่าน้ำ-ไฟ (InvoiceUtilityService) และรายงาน/export
     * (InvoiceReportService) ถูกแยกออกไปเป็นคลาสของตัวเองแล้ว — คลาสนี้แค่
     * "ส่งต่อ" งานให้ (delegate) เพื่อให้ public API เดิมของ InvoiceService
     * ยังคงเหมือนเดิมทุกจุด ไม่ต้องแก้ InvoiceController หรือเทสที่มีอยู่แล้วเลย
     */
    public function __construct(
        private readonly InvoiceUtilityService $utilityService,
        private readonly InvoiceReportService $reportService,
    ) {}

    /**
     * Utility invoice bulk-create calculation (batch-loaded).
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\Booking>  $bookings
     * @return \Illuminate\Support\Collection<int, array<string, mixed>> keyed by booking_id
     */
    public function calculateUtilityBulkData(
        \Illuminate\Support\Collection $bookings,
        int $month,
        int $year,
    ): \Illuminate\Support\Collection {
        return $this->utilityService->calculateUtilityBulkData($bookings, $month, $year);
    }

    /**
     * Bulk-create helper for invoices from selected bookings.
     *
     * @param  array<string, mixed>  $validated
     * @return array{created: int, skipped: array<int, array{booking_id: int, room_number: string, reason: string}>, rollover_warnings: array<int, array{booking_id: int, room_number: string, meter_types: array<int, string>}>}
     */
    public function bulkCreateFromBookings(
        array $validated,
        string $invoiceType,
        int $month,
        int $year,
    ): array {
        $count = 0;
        $skipped = [];
        $rolloverWarnings = [];

        DB::transaction(function () use ($validated, $invoiceType, $month, $year, &$count, &$skipped, &$rolloverWarnings): void {
            $bookingIds = array_map('intval', $validated['selected_bookings']);

            // ✅ FIX: โหลด booking ทั้งหมดครั้งเดียว (แทนที่จะ findOrFail ทีละตัวในลูป)
            $bookings = \App\Models\Booking::with(['room', 'guest'])
                ->whereIn('id', $bookingIds)
                ->get()
                ->keyBy('id');

            // รักษาพฤติกรรมเดิมของ findOrFail(): ถ้ามี booking id ที่ส่งมาไม่มีอยู่จริง
            // ให้ throw ทันทีแทนที่จะข้ามเงียบ ๆ (ป้องกัน bug บังหน้าโดยไม่รู้ตัว)
            $missingIds = array_values(array_diff($bookingIds, $bookings->keys()->all()));
            if (! empty($missingIds)) {
                throw (new \Illuminate\Database\Eloquent\ModelNotFoundException)
                    ->setModel(\App\Models\Booking::class, $missingIds);
            }

            // ✅ FIX: คำนวณข้อมูลค่าน้ำ-ไฟแบบ batch-loaded (calculateUtilityBulkData)
            // แทนการเรียก getMeterBillData() ทีละห้องในลูป (เดิมคือ N+1 query:
            // ~6 query ต่อห้องต่อรอบ สำหรับ utility invoice) — batch โหลด meters
            // + readings ทั้งหมดครั้งเดียวก่อนเข้าลูป
            $utilityData = $invoiceType === 'utility'
                ? $this->calculateUtilityBulkData($bookings->values(), $month, $year)
                : collect();

            foreach ($bookingIds as $bookingId) {
                /** @var \App\Models\Booking $booking */
                $booking = $bookings->get($bookingId);
                $roomNumber = (string) ($booking->room?->room_number ?? "booking#{$bookingId}");

                if ($invoiceType === 'utility') {
                    $data = $utilityData->get($bookingId);
                    $hasReading = (bool) ($data['has_reading'] ?? false);
                    if (! $hasReading) {
                        // ✅ FIX: บันทึกและ log ว่าห้องไหนถูกข้ามเพราะไม่มี meter reading
                        // ของรอบบิลนี้ แทนที่จะข้ามเงียบ ๆ แล้วนับแค่จำนวนที่สำเร็จ —
                        // ผู้ใช้/ผู้ดูแลระบบจะได้รู้ว่าห้องไหนต้องไปกรอกมิเตอร์เพิ่ม
                        $reason = 'ไม่มีข้อมูลมิเตอร์ของรอบบิลนี้ (ไม่มีทั้งค่าไฟและค่าน้ำ)';
                        $skipped[] = [
                            'booking_id' => $bookingId,
                            'room_number' => $roomNumber,
                            'reason' => $reason,
                        ];
                        \Log::info('InvoiceService::bulkCreateFromBookings skipped booking (no meter reading)', [
                            'booking_id' => $bookingId,
                            'room_number' => $roomNumber,
                            'month' => $month,
                            'year' => $year,
                        ]);

                        continue;
                    }

                    $baseCost = (float) ($data['base_cost'] ?? 0);
                    $tax = (float) ($data['tax'] ?? 0);
                    $total = (float) ($data['total'] ?? 0);
                    $amount = $baseCost;

                    // ✅ FIX (meter rollover): ถ้าเจอเลขมิเตอร์ย้อนกลับโดยไม่ได้ติ๊ก
                    // is_meter_reset ให้เตือนผู้ใช้ (usage ถูกคำนวณเป็น 0 อย่างปลอดภัย
                    // ไว้ก่อน แต่ผู้ใช้ควรไปตรวจสอบว่ามิเตอร์ถูกเปลี่ยนจริงหรือกรอกผิด)
                    $electricRollover = (bool) ($data['electric']['unflagged_rollover'] ?? false);
                    $waterRollover = (bool) ($data['water']['unflagged_rollover'] ?? false);
                    if ($electricRollover || $waterRollover) {
                        $meterTypes = array_filter([
                            $electricRollover ? 'ไฟฟ้า' : null,
                            $waterRollover ? 'น้ำ' : null,
                        ]);
                        $rolloverWarnings[] = [
                            'booking_id' => $bookingId,
                            'room_number' => $roomNumber,
                            'meter_types' => $meterTypes,
                        ];
                        \Log::info('InvoiceService::bulkCreateFromBookings detected unflagged meter rollover', [
                            'booking_id' => $bookingId,
                            'room_number' => $roomNumber,
                            'meter_types' => $meterTypes,
                            'month' => $month,
                            'year' => $year,
                        ]);
                    }
                } else {
                    $room = $booking->room;
                    $amount = (float) ($booking->rent_amount ?? $room?->price_per_month ?? 0);
                    $tax = round($amount * self::DEFAULT_TAX_RATE, 2);
                    $total = round($amount + $tax, 2);
                }

                $issueDate = $validated['issue_date'];
                $dueDate = $validated['due_date'];

                $invoiceNumber = $this->generateInvoiceNumber();

                \App\Models\Invoice::create([
                    'booking_id' => $bookingId,
                    'guest_id' => $booking->guest_id ?? null,
                    'room_id' => $booking->room_id ?? null,
                    'invoice_number' => $invoiceNumber,
                    'amount' => $amount,
                    'tax' => $tax,
                    'total' => $total,
                    'issue_date' => $issueDate,
                    'due_date' => $dueDate,
                    'status' => $validated['status'],
                    'invoice_type' => $invoiceType,
                    'notes' => null,
                ]);

                $count++;
            }
        });

        return [
            'created' => $count,
            'skipped' => $skipped,
            'rollover_warnings' => $rolloverWarnings,
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function createFromBooking(Booking $booking, array $validated): Invoice
    {
        return DB::transaction(function () use ($booking, $validated) {
            if (! isset($validated['total'])) {
                $baseAmount = $validated['amount'] ?? $booking->rent_amount ?? 0;
                $taxAmount = $validated['tax'] ?? ($baseAmount * self::DEFAULT_TAX_RATE);
                $validated['total'] = $this->calculateTotal($baseAmount, $taxAmount);
                $validated['amount'] = $baseAmount;
                $validated['tax'] = $taxAmount;
            }
            if (! isset($validated['due_date'])) {
                $issueDate = isset($validated['issue_date'])
                    ? Carbon::parse($validated['issue_date'])
                    : now();
                $validated['due_date'] = $this->calculateDueDate($issueDate);
            }
            if (! isset($validated['invoice_number'])) {
                $validated['invoice_number'] = $this->generateInvoiceNumber();
            }
            $validated['guest_id'] = $booking->guest_id;
            $validated['room_id'] = $booking->room_id;
            $validated['booking_id'] = $booking->id;
            $validated['status'] = $validated['status'] ?? 'draft';
            // ✅ default type = rent
            $validated['invoice_type'] = $validated['invoice_type'] ?? 'rent';

            $invoice = Invoice::create($validated);
            AuditLogger::log('invoice.created', $invoice);

            return $invoice;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Invoice
    {
        $data['amount'] = (float) ($data['amount'] ?? 0);
        $data['tax'] = (float) ($data['tax'] ?? 0);
        $data['total'] = (float) ($data['total'] ?? $this->calculateTotal($data['amount'], $data['tax']));
        $data['issue_date'] = isset($data['issue_date']) ? Carbon::parse($data['issue_date']) : now();
        $data['due_date'] = isset($data['due_date'])
            ? Carbon::parse($data['due_date'])
            : $this->calculateDueDate($data['issue_date']);
        $data['invoice_number'] = $data['invoice_number'] ?? $this->generateInvoiceNumber();
        $data['status'] = $data['status'] ?? 'draft';
        $data['invoice_type'] = $data['invoice_type'] ?? 'rent';

        return DB::transaction(function () use ($data) {
            $invoice = Invoice::create($data);
            AuditLogger::log('invoice.created', $invoice);

            return $invoice;
        });
    }

    public function createMonthlyInvoice(Room $room, Guest $guest, int $month, int $year): ?Invoice
    {
        $exists = Invoice::where('room_id', $room->id)
            ->where('guest_id', $guest->id)
            ->whereMonth('issue_date', $month)
            ->whereYear('issue_date', $year)
            ->exists();

        if ($exists) {
            return null;
        }

        $issueDate = Carbon::createFromDate($year, $month, 1);
        $pricePerMonth = (float) ($room->price_per_month ?? $room->rent_amount ?? 0);
        $taxAmount = $pricePerMonth * self::DEFAULT_TAX_RATE;

        return $this->create([
            'booking_id' => null,
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'invoice_number' => $this->generateInvoiceNumber(),
            'amount' => $pricePerMonth,
            'tax' => $taxAmount,
            'issue_date' => $issueDate,
            'due_date' => $issueDate->copy()->addDays(15),
            'status' => 'sent',
            'invoice_type' => 'rent',
            'notes' => "ค่าเช่าห้อง {$room->room_number} ประจำเดือน {$issueDate->format('F Y')}",
        ]);
    }

    public function markAsPaid(Invoice $invoice, string $method, ?float $amount = null): Invoice
    {
        if (! in_array($method, self::PAYMENT_METHODS, true)) {
            throw ValidationException::withMessages(['payment_method' => 'Invalid payment method.']);
        }
        if (! in_array($invoice->status, ['sent', 'overdue'], true)) {
            throw ValidationException::withMessages(['status' => 'Can only mark sent or overdue invoices as paid.']);
        }

        $paidAmount = $amount ?? $invoice->total;
        DB::transaction(function () use ($invoice, $method, $paidAmount) {
            Cache::forget(CacheKeys::layoutNotifications());

            $invoice->update([
                'status' => 'paid',
                'payment_method' => $method,
                'payment_date' => now(),
                'paid_amount' => $paidAmount,
            ]);
            AuditLogger::log('invoice.marked_paid', $invoice, [
                'payment_method' => $method,
                'paid_amount' => $paidAmount,
            ]);
        });

        return $invoice->fresh();
    }

    public function applyLateFee(Invoice $invoice): Invoice
    {
        $dueDate = $invoice->due_date;
        if (! ($dueDate instanceof Carbon) || ! $dueDate->isPast() || $invoice->status === 'paid') {
            return $invoice;
        }
        $daysOverdue = now()->diffInDays($dueDate);
        $amount = (float) ($invoice->amount ?? 0);
        $tax = (float) ($invoice->tax ?? 0);
        $existingLateFee = (float) ($invoice->late_fee ?? 0);
        $subtotal = $amount + $tax;
        $lateFee = $subtotal * self::DEFAULT_LATE_FEE_RATE * $daysOverdue;

        $invoice->update([
            'late_fee' => $existingLateFee + $lateFee,
            'total' => $subtotal + $existingLateFee + $lateFee,
            'status' => 'overdue',
        ]);

        return $invoice->fresh();
    }

    public function sendReminders(): int
    {
        $overdueInvoices = Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', Carbon::today())
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $this->applyLateFee($invoice);
            AuditLogger::log('invoice.reminder_sent', $invoice);
        }

        return $overdueInvoices->count();
    }

    public function calculateTotal(float $amount, float $tax): float
    {
        return round($amount + $tax, 2);
    }

    public function calculateDueDate(Carbon|string $issueDate, int $days = 15): Carbon
    {
        return Carbon::parse($issueDate)->addDays($days);
    }

    public function generateInvoiceNumber(): string
    {
        // ✅ FIX: lockForUpdate() ป้องกัน race condition เมื่อมีการออกใบแจ้งหนี้
        // พร้อมกันหลาย request (เช่น bulkCreateFromBookings ที่เรียกฟังก์ชันนี้ในลูป)
        // โดยไม่มี lock, สอง request อาจอ่านค่า $lastInvoice เดียวกันก่อนอีกฝั่ง
        // commit ทำให้ได้ invoice_number ซ้ำกัน → ชน unique constraint → 500 error
        // และ transaction ทั้ง batch จะ rollback
        //
        // ครอบด้วย DB::transaction() ของตัวเองเสมอ (Laravel ใช้ SAVEPOINT
        // เมื่อถูกเรียกซ้อนอยู่ใน transaction ที่เปิดอยู่แล้ว เช่นใน
        // bulkCreateFromBookings/create/createFromBooking) เพื่อให้ฟังก์ชันนี้
        // ปลอดภัยไม่ว่าจะถูกเรียกจากที่ไหนก็ตาม
        //
        // หมายเหตุ: กรณี edge case ที่สุด (ยังไม่เคยมี invoice ในปีนั้นเลย)
        // จะไม่มีแถวให้ lockForUpdate() ล็อก จึงยังมีโอกาสชนกันได้ในทางทฤษฎี
        // — ยัง fail-safe อยู่ด้วย unique constraint ระดับ DB (ไม่ทำให้ข้อมูลเพี้ยน)
        return DB::transaction(function (): string {
            $lastInvoice = Invoice::whereYear('created_at', now()->year)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $sequence = $lastInvoice
                ? (intval(substr($lastInvoice->invoice_number, -5)) + 1)
                : 1;

            return sprintf('INV-%s%s-%05d', now()->format('Y'), now()->format('m'), $sequence);
        });
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getOverdueInvoices(): Collection
    {
        return $this->reportService->getOverdueInvoices();
    }

    /**
     * @return array<string, int|float>
     */
    public function getRevenueReport(Carbon $startDate, Carbon $endDate): array
    {
        return $this->reportService->getRevenueReport($startDate, $endDate);
    }

    public function getPendingPaymentsTotal(): float
    {
        return $this->reportService->getPendingPaymentsTotal();
    }

    /**
     * @return array<string, int|float>
     */
    public function getStats(?string $invoiceType = null): array
    {
        return $this->reportService->getStats($invoiceType);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function prepareForCreate(array $data): array
    {
        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = $this->generateInvoiceNumber();
        }
        if (empty($data['total'])) {
            $data['total'] = $this->calculateTotal(
                (float) ($data['amount'] ?? 0),
                (float) ($data['tax'] ?? 0)
            );
        }
        $expectedTotal = $this->calculateTotal((float) $data['amount'], (float) $data['tax']);
        if (abs((float) $data['total'] - $expectedTotal) > 0.01) {
            $data['total'] = $expectedTotal;
        }
        // ✅ default invoice_type
        $data['invoice_type'] = $data['invoice_type'] ?? 'rent';

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function prepareForUpdate(array $data): array
    {
        if (isset($data['amount']) || isset($data['tax'])) {
            $data['total'] = $this->calculateTotal(
                (float) ($data['amount'] ?? 0),
                (float) ($data['tax'] ?? 0)
            );
        }
        unset($data['invoice_number']);

        return $data;
    }

    /**
     * @param  Collection<int, Invoice>  $invoices
     * @return array<int, array<int, mixed>>
     */
    public function formatForExport(Collection $invoices): array
    {
        return $this->reportService->formatForExport($invoices);
    }
}
