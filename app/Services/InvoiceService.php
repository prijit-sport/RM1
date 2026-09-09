<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Room;
use App\Support\AuditLogger;
use App\Support\CacheKeys;
use App\Support\MeterUsageCalculator;
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
        /** @var \Illuminate\Support\Collection<int, int> $roomIds */
        $roomIds = $bookings
            ->pluck('room_id')
            ->filter()
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();

        $utilityData = collect();
        if ($roomIds->isEmpty()) {
            return $utilityData;
        }

        // months: current + previous
        $prevMonth = $month === 1 ? 12 : $month - 1;
        $prevYear = $month === 1 ? $year - 1 : $year;

        // Dedupe + batch load meters and readings
        /** @var \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<int, \App\Models\Meter>> $meters */
        $meters = \App\Models\Meter::query()
            ->whereIn('room_id', $roomIds)
            ->whereIn('type', ['electric', 'water'])
            ->get()
            ->groupBy(fn (\App\Models\Meter $m): int => (int) $m->room_id);

        /** @var \Illuminate\Support\Collection<int, int> $meterIds */
        $meterIds = $meters
            ->flatten()
            ->pluck('id')
            ->filter()
            ->unique()
            ->values();

        if ($meterIds->isEmpty()) {
            return $utilityData;
        }

        /** @var \Illuminate\Support\Collection<int, \App\Models\MeterReading> $readings */
        $readings = \App\Models\MeterReading::query()
            ->whereIn('meter_id', $meterIds)
            ->where(function ($q) use ($month, $year, $prevMonth, $prevYear): void {
                $q->where(function ($sub) use ($month, $year): void {
                    $sub->where('period_month', $month)
                        ->where('period_year', $year);
                })->orWhere(function ($sub) use ($prevMonth, $prevYear): void {
                    $sub->where('period_month', $prevMonth)
                        ->where('period_year', $prevYear);
                });
            })
            ->get();

        /** @var \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, \App\Models\MeterReading>> $readingsByKey */
        $readingsByKey = $readings->groupBy(function (\App\Models\MeterReading $r): string {
            return $r->meter_id.':'.$r->period_month.':'.$r->period_year;
        });

        foreach ($bookings as $booking) {
            $roomId = (int) $booking->room_id;

            $electricData = $this->buildMeterBillDataFromBatch(
                roomId: $roomId,
                meterType: 'electric',
                meterMap: $meters,
                readingsByKey: $readingsByKey,
                month: $month,
                year: $year,
                prevMonth: $prevMonth,
                prevYear: $prevYear,
            );

            $waterData = $this->buildMeterBillDataFromBatch(
                roomId: $roomId,
                meterType: 'water',
                meterMap: $meters,
                readingsByKey: $readingsByKey,
                month: $month,
                year: $year,
                prevMonth: $prevMonth,
                prevYear: $prevYear,
            );

            $electricCost = (float) ($electricData['cost'] ?? 0);
            $waterCost = (float) ($waterData['cost'] ?? 0);

            $baseCost = round($electricCost + $waterCost, 2);
            $tax = round($baseCost * self::DEFAULT_TAX_RATE, 2);
            $total = round($baseCost + $tax, 2);

            $hasReading = (bool) ($electricData['has_reading'] ?? false) || (bool) ($waterData['has_reading'] ?? false);

            $utilityData->put($booking->id, [
                'electric' => $electricData,
                'water' => $waterData,
                'base_cost' => $baseCost,
                'tax' => $tax,
                'total' => $total,
                'has_reading' => $hasReading,
            ]);
        }

        return $utilityData;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<int, \App\Models\Meter>>  $meterMap
     * @param  \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, \App\Models\MeterReading>>  $readingsByKey
     * @return array<string, mixed>
     */
    private function buildMeterBillDataFromBatch(
        int $roomId,
        string $meterType,
        \Illuminate\Support\Collection $meterMap,
        \Illuminate\Support\Collection $readingsByKey,
        int $month,
        int $year,
        int $prevMonth,
        int $prevYear,
    ): array {
        /** @var \App\Models\Meter|null $meter */
        $meter = $meterMap->get($roomId)?->first(function (\App\Models\Meter $m) use ($meterType): bool {
            return $m->type === $meterType;
        });

        if (! $meter) {
            return ['has_reading' => false, 'cost' => 0];
        }

        $currentKey = $meter->id.':'.$month.':'.$year;
        $previousKey = $meter->id.':'.$prevMonth.':'.$prevYear;

        /** @var \Illuminate\Support\Collection<int, \App\Models\MeterReading> $currentReadings */
        $currentReadings = $readingsByKey->get($currentKey, collect());
        /** @var \Illuminate\Support\Collection<int, \App\Models\MeterReading> $previousReadings */
        $previousReadings = $readingsByKey->get($previousKey, collect());

        /** @var \App\Models\MeterReading|null $current */
        $current = $currentReadings->first();
        if (! $current) {
            return [
                'has_reading' => false,
                'cost' => 0,
                'meter_number' => (string) ($meter->meter_number ?? '-'),
            ];
        }

        /** @var \App\Models\MeterReading|null $previous */
        $previous = $previousReadings->first();

        $currentVal = (float) $current->reading_value;
        $previousVal = $previous ? (float) $previous->reading_value : 0;
        $isMeterReset = (bool) ($current->is_meter_reset ?? false);
        // ✅ FIX (meter rollover): ใช้ helper กลางแทนสูตร max(0, current - previous)
        // เดิม เพื่อรองรับกรณีมิเตอร์ถูกเปลี่ยนตัวใหม่ (ดู MeterUsageCalculator)
        $usage = MeterUsageCalculator::calculate($previousVal, $currentVal, $isMeterReset);

        $rate = (float) ($meter->rate_per_unit ?? 0);
        $cost = round($usage * $rate, 2);

        return [
            'has_reading' => true,
            'meter_number' => (string) ($meter->meter_number ?? '-'),
            'previous_value' => $previousVal,
            'current_value' => $currentVal,
            'usage' => $usage,
            'rate' => $rate,
            'cost' => $cost,
            'is_meter_reset' => $isMeterReset,
            'unflagged_rollover' => MeterUsageCalculator::isUnflaggedRollover($previousVal, $currentVal, $isMeterReset),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getMeterBillData(int $roomId, string $type, int $month, int $year): array
    {
        /** @var \App\Models\Meter|null $meter */
        $meter = \App\Models\Meter::where('room_id', $roomId)
            ->where('type', $type)
            ->first();

        if (! $meter) {
            return ['has_reading' => false, 'cost' => 0];
        }

        /** @var \App\Models\MeterReading|null $current */
        $current = \App\Models\MeterReading::where('meter_id', $meter->id)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->first();

        if (! $current) {
            return ['has_reading' => false, 'cost' => 0, 'meter_number' => $meter->meter_number];
        }

        /** @var \App\Models\MeterReading|null $previous */
        $previous = \App\Models\MeterReading::where('meter_id', $meter->id)
            ->where(function ($q) use ($month, $year): void {
                if ($month === 1) {
                    $q->where('period_month', 12)
                        ->where('period_year', $year - 1);
                } else {
                    $q->where('period_month', $month - 1)
                        ->where('period_year', $year);
                }
            })
            ->first();

        $currentVal = (float) $current->reading_value;
        $previousVal = $previous ? (float) $previous->reading_value : 0;
        $isMeterReset = (bool) ($current->is_meter_reset ?? false);
        // ✅ FIX (meter rollover): ใช้ helper กลางแทนสูตร max(0, current - previous) เดิม
        $usage = MeterUsageCalculator::calculate($previousVal, $currentVal, $isMeterReset);

        $rate = (float) ($meter->rate_per_unit ?? 0);
        $cost = round($usage * $rate, 2);

        return [
            'has_reading' => true,
            'meter_number' => (string) ($meter->meter_number ?? '-'),
            'previous_value' => $previousVal,
            'current_value' => $currentVal,
            'usage' => $usage,
            'rate' => $rate,
            'cost' => $cost,
            'is_meter_reset' => $isMeterReset,
            'unflagged_rollover' => MeterUsageCalculator::isUnflaggedRollover($previousVal, $currentVal, $isMeterReset),
        ];
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
        return Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', Carbon::today())
            ->with(['booking.guest', 'booking.room', 'guest', 'room'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * @return array<string, int|float>
     */
    public function getRevenueReport(Carbon $startDate, Carbon $endDate): array
    {
        $invoices = Invoice::where('status', 'paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        return [
            'total_revenue' => (float) $invoices->sum('total'),
            'total_amount' => (float) $invoices->sum('amount'),
            'total_tax' => (float) $invoices->sum('tax'),
            'total_late_fees' => (float) $invoices->sum('late_fee'),
            'invoice_count' => $invoices->count(),
            'average_invoice' => (float) ($invoices->avg('total') ?? 0),
        ];
    }

    public function getPendingPaymentsTotal(): float
    {
        return (float) Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', Carbon::today())
            ->sum('total');
    }

    /**
     * ✅ getStats — สถิติสำหรับ index page รองรับ invoice_type filter
     *
     * @return array<string, int|float>
     */
    public function getStats(?string $invoiceType = null): array
    {
        $base = Invoice::query();
        if ($invoiceType) {
            $base = $base->where('invoice_type', $invoiceType);
        }

        $now = now();
        $prev = now()->subMonth();

        $thisMonth = (clone $base)->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)->count();
        $lastMonth = (clone $base)->whereYear('created_at', $prev->year)
            ->whereMonth('created_at', $prev->month)->count();

        return [
            'total' => (clone $base)->count(),
            'paid_count' => (clone $base)->where('status', 'paid')->count(),
            'paid_amount' => (float) (clone $base)->where('status', 'paid')->sum('total'),
            'sent_count' => (clone $base)->where('status', 'sent')->count(),
            'sent_amount' => (float) (clone $base)->where('status', 'sent')->sum('total'),
            'overdue_count' => (clone $base)->where('status', 'overdue')->count(),
            'monthly_diff' => $thisMonth - $lastMonth,
            // ✅ count แยกประเภทเสมอ (ไม่ขึ้นกับ filter)
            'rent_count' => Invoice::where('invoice_type', 'rent')->count(),
            'utility_count' => Invoice::where('invoice_type', 'utility')->count(),
        ];
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
        $rows = [
            ['Invoice Number', 'Type', 'Booking ID', 'Guest Name', 'Room', 'Amount', 'Tax', 'Total', 'Issue Date', 'Due Date', 'Status', 'Notes'],
        ];
        foreach ($invoices as $invoice) {
            $rows[] = [
                $invoice->invoice_number ?? '-',
                $invoice->invoice_type === 'utility' ? 'ค่าน้ำ/ไฟ' : 'ค่าห้อง',
                $invoice->booking_id ?? '-',
                trim(($invoice->booking?->guest?->first_name ?? '').' '.($invoice->booking?->guest?->last_name ?? '')) ?: '-',
                $invoice->booking?->room?->room_number ?? '-',
                number_format((float) ($invoice->amount ?? 0), 2),
                number_format((float) ($invoice->tax ?? 0), 2),
                number_format((float) ($invoice->total ?? 0), 2),
                $invoice->issue_date ?? '-',
                $invoice->due_date ?? '-',
                $invoice->status ?? '-',
                $invoice->notes ?? '-',
            ];
        }

        return $rows;
    }
}
