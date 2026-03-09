<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'id_number',
        'date_of_birth',
        'nationality',
        'emergency_contact',
        'emergency_phone',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the bookings for the guest.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the contracts for the guest.
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get the invoices for the guest.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get active booking.
     */
    public function activeBooking()
    {
        return $this->bookings()->whereIn('status', ['confirmed', 'checked_in'])->first();
    }

    /**
     * Check if guest has active booking.
     */
    public function hasActiveBooking(): bool
    {
        return $this->bookings()->whereIn('status', ['confirmed', 'checked_in'])->exists();
    }
}
