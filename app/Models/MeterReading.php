<?php
 
namespace App\Models;
 
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
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
 * @property bool $is_meter_reset
 * @property int|null $recorded_by
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
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
        'is_meter_reset',
        'recorded_by',
        'notes',
    ];
 
    protected $casts = [
        'reading_date' => 'date',
        'reading_value' => 'decimal:2',
        'is_meter_reset' => 'boolean',
    ];
 
    // ─────────────────────────────────────────
    //  RELATIONSHIPS
    // ─────────────────────────────────────────
 
    /**
     * @return BelongsTo<Meter, $this>
     */
    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }
 
    /**
     * @return BelongsTo<User, $this>
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
 
    /**
     * @return BelongsTo<User, $this>
     */
    public function recordedBy(): BelongsTo
    {
        return $this->recorder();
    }
}
 