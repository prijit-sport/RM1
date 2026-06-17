<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * MeterReading Model
 *
 * @property int $id
 * @property int $meter_id
 * @property int|null $booking_id
 * @property int|null $period_month
 * @property int|null $period_year
 * @property Carbon $reading_date
 * @property float $reading_value
 * @property int|null $recorded_by
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Meter $meter
 * @property-read User|null $recorder
 * @property-read User|null $recordedBy
 */
class MeterReading extends Model
{
    protected $fillable = [
        'meter_id',
        'booking_id',
        'period_month',
        'period_year',
        'reading_date',
        'reading_value',
        'recorded_by',
        'notes',
    ];

    protected $casts = [
        'reading_date'  => 'date',
        'reading_value' => 'decimal:2',
    ];

    // ─────────────────────────────────────────
    //  RELATIONSHIPS
    // ─────────────────────────────────────────

    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->recorder();
    }
}
