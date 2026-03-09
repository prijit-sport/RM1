<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'room_id',
        'guest_id',
        'check_in_date',
        'check_out_date',
        'actual_check_in',
        'actual_check_out',
        'total_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'check_in_date' => 'datetime',
        'check_out_date' => 'datetime',
        'actual_check_in' => 'datetime',
        'actual_check_out' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the room for the booking.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the guest for the booking.
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /**
     * Get the invoices for the booking.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Check if booking is active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'confirmed', 'checked_in']);
    }

    /**
     * Calculate actual price based on actual stay.
     */
    public function calculateActualPrice(): float
    {
        if (!$this->actual_check_in || !$this->actual_check_out) {
            return $this->total_price;
        }

        $days = $this->actual_check_in->diffInDays($this->actual_check_out);
        if ($days <= 0) {
            return $this->total_price;
        }

        // Use consistent daily rate calculation (30.44 days per month)
        $dailyRate = $this->room->price_per_month / 30.44;
        return round($dailyRate * $days, 2);
    }

    /**
     * Check in the guest.
     */
    public function checkIn(): void
    {
        $this->update([
            'status' => 'checked_in',
            'actual_check_in' => now(),
        ]);
    }

    /**
     * Check out the guest.
     */
    public function checkOut(): void
    {
        $this->update([
            'status' => 'checked_out',
            'actual_check_out' => now(),
        ]);
    }
}
