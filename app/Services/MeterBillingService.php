<?php
 
namespace App\Services;
 
use App\Models\Booking;
use App\Models\Meter;
use App\Models\MeterReading;
use Carbon\Carbon;
 
class MeterBillingService
{
    // ─────────────────────────────────────────
    //  SUMMARIZE (ใช้ใน show page)
    // ─────────────────────────────────────────
    public function summarize(Meter $meter): array
    {
        $readings = MeterReading::with('recordedBy')
            ->where('meter_id', $meter->id)
            ->orderByDesc('reading_date')
            ->take(2)
            ->get();
 
        /** @var MeterReading|null $current */
        $current  = $readings->first();
 
        /** @var MeterReading|null $previous */
        $previous = $readings->skip(1)->first();
 
        return $this->compute($meter, $previous, $current);
    }
 
    // ─────────────────────────────────────────
    //  COMPUTE (คำนวณจาก 2 readings)
    // ─────────────────────────────────────────
    public function compute(Meter $meter, ?MeterReading $previous, ?MeterReading $current): array
    {
        $previousValue = (float) ($previous?->reading_value ?? 0);
        $currentValue  = (float) ($current?->reading_value  ?? 0);
        $usage         = max(0, $currentValue - $previousValue);
        $rate          = (float) ($meter->rate_per_unit ?? 0);
        $taxRate       = (float) ($meter->tax_rate      ?? 0);
        $base          = round($usage * $rate, 2);
        $tax           = round($base * ($taxRate / 100), 2);
        $total         = round($base + $tax, 2);
 
        // แปลง reading_date เป็น string อย่างปลอดภัย
        $currentDate  = null;
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
            'previous'      => round($previousValue, 2),
            'current'       => round($currentValue, 2),
            'usage'         => round($usage, 2),
            'rate'          => round($rate, 2),
            'tax_rate'      => round($taxRate, 2),
            'tax'           => $tax,
            'base'          => $base,
            'total'         => $total,
            'current_date'  => $currentDate,
            'previous_date' => $previousDate,
            'recorder'      => $current?->recordedBy?->name,
            'has_reading'   => $current !== null,
            'formula'       => 'Usage × Rate + Tax',
        ];
    }
 
    // ─────────────────────────────────────────
    //  CALCULATE MONTHLY TOTALS
    //  คำนวณค่าไฟ + ค่าน้ำรวมสำหรับ booking ในเดือนนั้น
    // ─────────────────────────────────────────
    public function calculateMonthlyTotals(Booking $booking, int $month, int $year): array
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $totals      = ['electric' => 0.0, 'water' => 0.0];
 
        foreach (['electric', 'water'] as $type) {
            /** @var Meter|null $meter */
            $meter = Meter::where('room_id', $booking->room_id)
                ->where('type', $type)
                ->first();
 
            if (! $meter) {
                continue;
            }
 
            /** @var MeterReading|null $reading */
            $reading = MeterReading::where('meter_id', $meter->id)
                ->where('booking_id', $booking->id)
                ->where('period_month', $month)
                ->where('period_year', $year)
                ->first();
 
            if (! $reading) {
                continue;
            }
 
            // ค่าก่อนหน้า: reading ล่าสุดก่อนเดือนนี้ หรือ initial จากการจอง
            /** @var MeterReading|null $prev */
            $prev = MeterReading::where('meter_id', $meter->id)
                ->whereDate('reading_date', '<', $periodStart->toDateString())
                ->orderByDesc('reading_date')
                ->first();
 
            $initValue = $type === 'electric'
                ? (float) ($booking->electric_meter_start ?? 0)
                : (float) ($booking->water_meter_start   ?? 0);
 
            $prevValue = (float) ($prev?->reading_value ?? $initValue);
            $usage     = max(0, (float) $reading->reading_value - $prevValue);
            $base      = round($usage * (float) ($meter->rate_per_unit ?? 0), 2);
            $tax       = round($base * ((float) ($meter->tax_rate ?? 0) / 100), 2);
 
            $totals[$type] = round($base + $tax, 2);
        }
 
        return $totals;
    }
 
    // ─────────────────────────────────────────
    //  GENERATE INVOICE NUMBER
    // ─────────────────────────────────────────
    public function generateInvoiceNumber(int $bookingId, int $month, int $year): string
    {
        return sprintf('INV-%d%02d-%04d', $year, $month, $bookingId);
    }
}
 