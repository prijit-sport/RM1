<?php

namespace App\Support;

use Carbon\Carbon;

final class BillingCalculator
{
    private const MINIMUM_MONTHS = 1;

    /**
     * Calculate the total charge using a monthly rate.
     */
    public static function calculateMonthlyCharge(float $monthlyRate, Carbon $start, Carbon $end): float
    {
        if ($monthlyRate <= 0) {
            return 0;
        }

        $months = self::monthsBetween($start, $end);
        if ($months <= 0) {
            return 0;
        }

        return round($monthlyRate * $months, 2);
    }

    /**
     * Count how many billing months overlap the given period.
     */
    public static function monthsBetween(Carbon $start, Carbon $end): int
    {
        $start = $start->copy()->startOfDay();
        $end = $end->copy()->startOfDay();

        if ($end <= $start) {
            return 0;
        }

        $months = 0;
        $pointer = $start->copy();

        while ($pointer->lt($end)) {
            $pointer->addMonthNoOverflow();
            $months++;
        }

        return max(self::MINIMUM_MONTHS, $months);
    }
}
