<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

/**
 * Meter Model
 *
 * @property int $id
 * @property int $room_id
 * @property string $type
 * @property string $meter_number
 * @property string|null $unit
 * @property Carbon|null $installed_at
 * @property bool $is_active
 * @property string|null $notes
 * @property float|null $rate_per_unit
 * @property float|null $tax_rate
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Room $room
 * @property-read Collection<int, MeterReading> $readings
 * @property-read MeterReading|null $latestReading
 * @property-read string $type_label
 * @property-read string $status_label
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
        'installed_at'  => 'date',
        'is_active'     => 'boolean',
        'rate_per_unit' => 'decimal:2',
        'tax_rate'      => 'decimal:2',
    ];

    // ─────────────────────────────────────────
    //  RELATIONSHIPS
    // ─────────────────────────────────────────

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(MeterReading::class);
    }

    public function latestReading(): HasOne
    {
        return $this->hasOne(MeterReading::class)->latestOfMany('reading_date');
    }

    // ─────────────────────────────────────────
    //  ACCESSORS
    // ─────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'water'    => 'น้ำ',
            'electric' => 'ไฟฟ้า',
            default    => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'ใช้งาน' : 'ปิดใช้';
    }
}
