<?php

namespace App\Services;

use App\Support\MeterUsageCalculator;

/**
 * คำนวณค่าน้ำ-ไฟแบบ batch สำหรับหน้าออกใบแจ้งหนี้หลายใบพร้อมกัน
 * (แยกออกมาจาก InvoiceService เดิมเพื่อลดขนาดไฟล์และแยกความรับผิดชอบ —
 * ดู InvoiceService::calculateUtilityBulkData() ที่ delegate มาที่นี่)
 */
class InvoiceUtilityService
{
    private const DEFAULT_TAX_RATE = 0.07;

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
}
