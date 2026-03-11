<?php

namespace App\Services;

use App\Models\Meter;
use App\Models\MeterReading;

class MeterBillingService
{
    public function summarize(Meter $meter): array
    {
        $readings = MeterReading::with('recordedBy')
            ->where('meter_id', $meter->id)
            ->orderByDesc('reading_date')
            ->take(2)
            ->get();

        $current = $readings->first();
        $previous = $readings->skip(1)->first();

        return $this->compute($meter, $previous, $current);
    }

    public function compute(Meter $meter, ?MeterReading $previous, ?MeterReading $current): array
    {
        $previousValue = $previous?->reading_value ?? 0;
        $currentValue = $current?->reading_value ?? 0;
        $usage = max(0, $currentValue - $previousValue);
        $rate = $meter->rate_per_unit ?? 0;
        $taxRate = $meter->tax_rate ?? 0;
        $base = round($usage * $rate, 2);
        $tax = round($base * ($taxRate / 100), 2);
        $total = round($base + $tax, 2);

        return [
            'previous' => round($previousValue, 2),
            'current' => round($currentValue, 2),
            'usage' => round($usage, 2),
            'rate' => round($rate, 2),
            'tax_rate' => round($taxRate, 2),
            'tax' => $tax,
            'base' => $base,
            'total' => $total,
            'current_date' => $current?->reading_date?->format('d/m/Y'),
            'previous_date' => $previous?->reading_date?->format('d/m/Y'),
            'recorder' => $current?->recordedBy?->name,
            'has_reading' => $current !== null,
            'formula' => 'Usage × Rate + Tax',
        ];
    }
}
