<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'room_number',
        'room_type',
        'price_per_month',
        'capacity',
        'status',
        'description',
        'floor',
        'building',
    ];

    protected $casts = [
        'price_per_month' => 'decimal:2',
    ];

    /**
     * Get the bookings for the room.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the meters for the room.
     */
    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }

    /**
     * Get the contracts for the room.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the maintenances for the room.
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    /**
     * Get active bookings for the room.
     */
    public function activeBookings()
    {
        return $this->bookings()->whereIn('status', ['confirmed', 'checked_in']);
    }

    /**
     * Check if room is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Check if room is occupied.
     */
    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }
}
