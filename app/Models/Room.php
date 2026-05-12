<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'zone',
    ];
 
    protected $casts = [
        'price_per_month' => 'decimal:2',
    ];
 
    // ─────────────────────────────────────────
    //  RELATIONSHIPS
    // ─────────────────────────────────────────
 
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
 
    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }
 
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
 
    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }
 
    /** การจองที่ active อยู่ตอนนี้ (confirmed) */
    public function currentBooking(): HasOne
    {
        return $this->hasOne(Booking::class)
            ->where('status', 'confirmed')
            ->latestOfMany('id');
    }
 
    // ─────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────
 
    public function activeBookings(): HasMany
    {
        return $this->bookings()->whereIn('status', ['confirmed', 'checked_in']);
    }
 
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
 
    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }
}
 