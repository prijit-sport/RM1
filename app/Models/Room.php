<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

/**
 * Room Model
 *
 * @property int         $id
 * @property string      $room_number
 * @property string|null $room_type
 * @property string|null $zone
 * @property int|null    $floor
 * @property float|null  $price_per_month
 * @property int|null    $capacity
 * @property string|null $description
 * @property string      $status
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read Collection<int, Booking> $bookings
 * @property-read Collection<int, Meter> $meters
 * @property-read Booking|null $currentBooking
 * @property-read float|null $rent_amount
 */
class Room extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'room_number',
        'room_type',
        'zone',
        'floor',
        'building',
        'price_per_month',

        'capacity',
        'description',
        'status',
        'notes',
    ];

    protected $casts = [
        'price_per_month' => 'decimal:2',
        'capacity'        => 'integer',
        'floor'           => 'integer',
    ];

    // ─────────────────────────────────────────
    //  RELATIONSHIPS
    // ─────────────────────────────────────────

    public function contracts(): HasMany
    {
        return $this->hasMany(\App\Models\Contract::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(\App\Models\Invoice::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(\App\Models\Facility::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(\App\Models\Maintenance::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }


    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }

    public function currentBooking(): HasOne
    {
        return $this->hasOne(Booking::class)
            ->where('status', '!=', 'checked_out')
            ->whereNull('deleted_at')
            ->latest('check_in_date');
    }

    // ─────────────────────────────────────────
    //  ACCESSORS
    // ─────────────────────────────────────────

    public function getRentAmountAttribute(): ?float
    {
        return $this->price_per_month;
    }
}
