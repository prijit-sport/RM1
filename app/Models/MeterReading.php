<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
 
/**
 * MeterReading Model
 * 
 * @property int $id
 * @property int $meter_id (FK)
 * @property int|null $booking_id
 * @property int|null $period_month
 * @property int|null $period_year
 * @property Carbon $reading_date
 * @property float $reading_value (kWh หรือ m3)
 * @property int|null $recorded_by (FK users)
 * @property string|null $notes
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * 
 * Relations:
 * @property \App\Models\Meter $meter
 * @property \App\Models\User|null $recorder (User ที่บันทึก)
 * @property \App\Models\User|null $recordedBy (Alias สำหรับ backward compatibility)
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
        'reading_date' => 'date',
        'reading_value' => 'decimal:2',
    ];
 
    // ─────────────────────────────────────────
    //  RELATIONSHIPS
    // ─────────────────────────────────────────
 
    /**
     * Get the meter for this reading.
     */
    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }
 
    /**
     * Get the user who recorded this reading.
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
 
    /**
     * Alias สำหรับ backward compatibility
     * ใช้เพื่อให้ MeterController ทำงานได้ทั้ง recorder() และ recordedBy()
     */
    public function recordedBy(): BelongsTo
    {
        return $this->recorder();
    }
}
 