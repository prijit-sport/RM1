<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;
 
/**
 * Meter Model
 * 
 * @property int $id
 * @property int $room_id (FK)
 * @property string $type (electric, water)
 * @property string $meter_number
 * @property string|null $unit (kWh, m3)
 * @property-read Carbon|null $installed_at
 * @property bool $is_active
 * @property string|null $notes
 * @property float|null $rate_per_unit
 * @property float|null $tax_rate
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * 
 * Relations:
 * @property \App\Models\Room $room
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\MeterReading[] $readings
 * @property \App\Models\MeterReading|null $latestReading
 */
class Meter extends Model
{
    protected $fillable = [
        'room_id',
        'type',
        'meter_number',
        'unit',
        'installed_at',
        'is_active',
        'notes',
        'rate_per_unit',
        'tax_rate',
    ];
 
    protected $casts = [
        'installed_at' => 'date',
        'is_active' => 'boolean',
        'rate_per_unit' => 'decimal:2',
        'tax_rate' => 'decimal:2',
    ];
 
    // ─────────────────────────────────────────
    //  RELATIONSHIPS
    // ─────────────────────────────────────────
 
    /**
     * Get the room that owns the meter.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
 
    /**
     * Get all readings for this meter.
     */
    public function readings(): HasMany
    {
        return $this->hasMany(MeterReading::class);
    }
 
    /**
     * Get the latest reading for this meter.
     */
    public function latestReading(): HasOne
    {
        return $this->hasOne(MeterReading::class)->latestOfMany('reading_date');
    }
 
    // ─────────────────────────────────────────
    //  ACCESSORS / MUTATORS
    // ─────────────────────────────────────────
 
    /**
     * Get type label in Thai.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'water' => 'น้ำ',
            'electric' => 'ไฟฟ้า',
            default => $this->type,
        };
    }
 
    /**
     * Get status label in Thai.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'ใช้งาน' : 'ปิดใช้';
    }
}
 