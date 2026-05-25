<?php
 
namespace App\Services;
 
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Meter;
use App\Models\MeterReading;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
 
/**
 * MeterBillingService - COMPLETE & ENHANCED
 *
 * ✅ ORIGINAL METHODS (from existing codebase):
 * - summarize()
 * - compute()
 * - calculateMonthlyTotals()
 * - generateInvoiceNumber()
 *
 * ✅ NEW METHODS (added for controller refactor):
 * - recordMonthlyAndCreateInvoice()
 * - findActiveBooking()
 * - upsertReading()
 * - syncMonthlyInvoice()
 */
class MeterBillingService
{
    /**
     * ═══════════════════════════════════════════════════════
     *  ORIGINAL METHODS - From existing codebase
     * ═══════════════════════════════════════════════════════
     */
 
    /**
     * SUMMARIZE (ใช้ใน show page)
     */
    public function summarize(Meter $meter): array
    {
        $readings = MeterReading::with('recordedBy')
            ->where('meter_id', $meter->id)
            ->orderByDesc('reading_date')
            ->take(2)
            ->get();
 
        /** @var MeterReading|null $current */
        $current = $readings->first();
 
        /** @var MeterReading|null $previous */
        $previous = $readings->skip(1)->first();
 
        return $this->compute($meter, $previous, $current);
    }
 
    /**
     * COMPUTE (คำนวณจาก 2 readings)
     */
    public function compute(Meter $meter, ?MeterReading $previous, ?MeterReading $current): array
    {
        $previousValue = (float) ($previous?->reading_value ?? 0);
        $currentValue = (float) ($current?->reading_value ?? 0);
        $usage = max(0, $currentValue - $previousValue);
        $rate = (float) ($meter->rate_per_unit ?? 0);
        $taxRate = (float) ($meter->tax_rate ?? 0);
        $base = round($usage * $rate, 2);
        $tax = round($base * ($taxRate / 100), 2);
        $total = round($base + $tax, 2);
 
        // แปลง reading_date เป็น string อย่างปลอดภัย
        $currentDate = null;
        $previousDate = null;
 
        if ($current?->reading_date !== null) {
            $currentDate = $current->reading_date instanceof Carbon
                ? $current->reading_date->format('d/m/Y')
                : Carbon::parse($current->reading_date)->format('d/m/Y');
        }
 
        if ($previous?->reading_date !== null) {
            $previousDate = $previous->reading_date instanceof Carbon
                ? $previous->reading_date->format('d/m/Y')
                : Carbon::parse($previous->reading_date)->format('d/m/Y');
        }
 
        return [
            'previous' => round($previousValue, 2),
            'current' => round($currentValue, 2),
            'usage' => round($usage, 2),
            'rate' => round($rate, 2),
            'tax_rate' => round($taxRate, 2),
            'tax' => $tax,
            'base' => $base,
            'total' => $total,
            'current_date' => $currentDate,
            'previous_date' => $previousDate,
            'recorder' => $current?->recordedBy?->name,
            'has_reading' => $current !== null,
            'formula' => 'Usage × Rate + Tax',
        ];
    }
 
    /**
     * CALCULATE MONTHLY TOTALS
     * คำนวณค่าไฟ + ค่าน้ำรวมสำหรับ booking ในเดือนนั้น
     */
    public function calculateMonthlyTotals(Booking $booking, int $month, int $year): array
    {
        $breakdown = $this->calculateMonthlyBreakdown($booking, $month, $year);

        return [
            'electric' => round($breakdown['electric']['total'], 2),
            'water' => round($breakdown['water']['total'], 2),
        ];
    }

    /**
     * Calculate base, tax, and total for each meter in the selected month.
     */
    private function calculateMonthlyBreakdown(Booking $booking, int $month, int $year): array
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $breakdown = [
            'electric' => ['base' => 0.0, 'tax' => 0.0, 'total' => 0.0],
            'water' => ['base' => 0.0, 'tax' => 0.0, 'total' => 0.0],
        ];

        foreach (['electric', 'water'] as $type) {
            /** @var Meter|null $meter */
            $meter = Meter::where('room_id', $booking->room_id)
                ->where('type', $type)
                ->first();

            if (!$meter) {
                continue;
            }

            /** @var MeterReading|null $reading */
            $reading = MeterReading::where('meter_id', $meter->id)
                ->where('booking_id', $booking->id)
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->first();

            if (!$reading) {
                continue;
            }

            $initValue = $type === 'electric'
                ? (float) ($booking->electric_meter_start ?? 0)
                : (float) ($booking->water_meter_start ?? 0);

            /** @var MeterReading|null $prev */
            $prev = MeterReading::where('meter_id', $meter->id)
                ->whereDate('reading_date', '<', $periodStart->toDateString())
                ->orderByDesc('reading_date')
                ->first();

            $prevValue = (float) ($prev?->reading_value ?? $initValue);
            $usage = max(0, (float) $reading->reading_value - $prevValue);
            $base = round($usage * (float) ($meter->rate_per_unit ?? 0), 2);
            $tax = round($base * ((float) ($meter->tax_rate ?? 0) / 100), 2);

            $breakdown[$type] = [
                'base' => $base,
                'tax' => $tax,
                'total' => round($base + $tax, 2),
            ];
        }

        return $breakdown;
    }

    /**
     * GENERATE INVOICE NUMBER
     */
    public function generateInvoiceNumber(int $bookingId = 0, int $month = 0, int $year = 0): string
    {
        if ($bookingId && $month && $year) {
            return sprintf('INV-%04d-%02d-%02d-%d', $year, $month, $bookingId, time());
        }
 
        $lastInvoice = Invoice::latest('id')->first();
        $nextNumber = ($lastInvoice?->id ?? 0) + 1;
        return 'INV-' . date('Y') . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
 
    /**
     * ═══════════════════════════════════════════════════════
     *  NEW METHODS - Added for controller refactor
     * ═══════════════════════════════════════════════════════
     */
 
    /**
     * ✅ NEW: Record monthly reading and create invoice
     *
     * Orchestrates the entire monthly billing process:
     * 1. Find active booking for the period
     * 2. Upsert meter reading
     * 3. Calculate monthly totals
     * 4. Sync/create invoice
     */
    public function recordMonthlyAndCreateInvoice(
        Meter $meter,
        int $month,
        int $year,
        float $readingValue,
        ?string $notes = null
    ): array {
        try {
            // ✅ Step 1: Find active booking
            $periodStart = Carbon::create($year, $month, 1)->startOfDay();
            $periodEnd = $periodStart->copy()->endOfMonth();
 
            $booking = $this->findActiveBooking($meter, $periodStart, $periodEnd);
 
            // ✅ Step 2: Upsert meter reading
            $reading = $this->upsertReading(
                $meter,
                $booking,
                $month,
                $year,
                $periodEnd,
                $readingValue,
                $notes
            );
 
            // ✅ Step 3: Calculate totals (existing method)
            $breakdown = $this->calculateMonthlyBreakdown($booking, $month, $year);
            $amount = round($breakdown['electric']['base'] + $breakdown['water']['base'], 2);
            $tax = round($breakdown['electric']['tax'] + $breakdown['water']['tax'], 2);
            $grandTotal = round($amount + $tax, 2);

            if ($grandTotal <= 0) {
                throw new \InvalidArgumentException('No billable usage found for selected period.');
            }

            // ✅ Step 4: Sync invoice
            $invoice = $this->syncMonthlyInvoice(
                $booking,
                $month,
                $year,
                $periodStart,
                $amount,
                $tax,
                $grandTotal
            );

            return [
                'success' => true,
                'reading' => $reading,
                'invoice' => $invoice,
                'totals' => [
                    'electric' => round($breakdown['electric']['total'], 2),
                    'water' => round($breakdown['water']['total'], 2),
                ],
            ];
 
        } catch (\Exception $e) {
            \Log::error('MeterBillingService recordMonthlyAndCreateInvoice failed', [
                'meter_id' => $meter->id,
                'month' => $month,
                'year' => $year,
                'error' => $e->getMessage(),
            ]);
 
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
 
    /**
     * ✅ NEW: Find active booking for the meter during period
     */
    private function findActiveBooking(Meter $meter, Carbon $periodStart, Carbon $periodEnd): Booking
    {
        $model = Booking::query()
            ->where('room_id', $meter->room_id)
            ->where('status', 'confirmed')
            ->whereDate('check_in_date', '<=', $periodEnd)
            ->where(function ($q) use ($periodStart) {
                $q->whereNull('check_out_date')
                    ->orWhereDate('check_out_date', '>=', $periodStart);
            })
            ->orderByDesc('id')
            ->firstOrFail();
 
        assert($model instanceof Booking);
        return $model;
    }
 
    /**
     * ✅ NEW: Create or update meter reading for the month
     */
    private function upsertReading(
        Meter $meter,
        Booking $booking,
        int $month,
        int $year,
        Carbon $readingDate,
        float $readingValue,
        ?string $notes
    ): MeterReading {
        $model = MeterReading::updateOrCreate(
            [
                'meter_id' => $meter->id,
                'booking_id' => $booking->id,
                'period_month' => $month,
                'period_year' => $year,
            ],
            [
                'reading_date' => $readingDate,
                'reading_value' => $readingValue,
                'recorded_by' => Auth::id(),
                'notes' => $notes,
            ]
        );

        assert($model instanceof MeterReading);

        return $model;
    }

    /**
     * ✅ NEW: Create or update monthly invoice
     */
    private function syncMonthlyInvoice(
        Booking $booking,
        int $month,
        int $year,
        Carbon $periodStart,
        float $amount,
        float $tax,
        float $grandTotal
    ): Invoice {
        $existing = Invoice::query()
            ->where('booking_id', $booking->id)
            ->where('room_id', $booking->room_id)
            ->where('guest_id', $booking->guest_id)
            ->whereMonth('issue_date', $month)
            ->whereYear('issue_date', $year)
            ->first();
 
        // ✅ Update existing invoice
        if ($existing instanceof Invoice) {
            $existing->update([
                'amount' => $amount,
                'tax' => $tax,
                'total' => $grandTotal,
            ]);
            return $existing;
        }
 
        // ✅ Create new invoice
        $dueDate = $periodStart->copy()->addDays(15);
 
        $invoice = Invoice::create([
            'booking_id' => $booking->id,
            'guest_id' => $booking->guest_id,
            'room_id' => $booking->room_id,
            'invoice_number' => $this->generateInvoiceNumber(
                (int)$booking->id,
                $month,
                $year
            ),
            'amount' => $amount,
            'tax' => $tax,
            'total' => $grandTotal,
            'issue_date' => $periodStart->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'status' => 'sent',
            'notes' => 'ค่าน้ำ/ไฟ ประจำเดือน ' . $month . '/' . $year,
        ]);
 
        assert($invoice instanceof Invoice);
        return $invoice;
    }
}
