<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;
 
/**
 * Room Model
 * 
 * @property int $id
 * @property string $room_number
 * @property string|null $room_type (single, double, suite, etc.)
 * @property string|null $zone (A, B, C, etc.)
 * @property float|null $price_per_month
 * @property float|null $rent_amount (alias for price_per_month)
 * @property int|null $capacity
 * @property string|null $description
 * @property string $status (available, occupied, maintenance, etc.)
 * @property string|null $notes
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 * @property-read Carbon|null $deleted_at
 * 
 * Relations:
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Booking[] $bookings
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Meter[] $meters
 * @property \App\Models\Booking|null $currentBooking (ผู้พักอาศัยปัจจุบันที่ยังไม่เช็คเอาต์)
 */
class Room extends Model
{
    use SoftDeletes;
 
    protected $fillable = [
        'room_number',
        'room_type',
        'zone',
        'price_per_month',
        'capacity',
        'description',
        'status',
        'notes',
    ];
 
    protected $casts = [
        'price_per_month' => 'decimal:2',
        'capacity' => 'integer',
    ];
 
    // ─────────────────────────────────────────
    //  RELATIONSHIPS
    // ─────────────────────────────────────────
 
    /**
     * Get all bookings for this room.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
 
    /**
     * Get all meters for this room.
     */
    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }
 
    /**
     * Get current active booking (ยังไม่เช็คเอาต์).
     * ดึงการจองล่าสุดที่สถานะไม่ใช่ 'checked_out' และไม่ได้ถูกลบ (soft delete)
     */
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
 
    /**
     * Get alias for price_per_month.
     */
    public function getRentAmountAttribute(): ?float
    {
        return $this->price_per_month;
    }
}
 