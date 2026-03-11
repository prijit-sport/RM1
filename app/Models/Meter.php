<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
}

