<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Room Model
 *
 * @property int         $id
 * @property string      $room_number
 * @property string|null $room_type   fan | air
 * @property string|null $zone        A | B
 * @property int|null    $floor       1–5
 * @property float|null  $price_per_month
 * @property int|null    $capacity
 * @property string|null $description
 * @property string      $status      available | occupied | maintenance
 * @property string|null $notes
 */
class Room extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'room_number',
        'room_type',
        'zone',
        'floor',           // ✅ แก้ไข: เพิ่ม floor เข้า fillable
        'price_per_month',
        'capacity',
        'description',
        'status',
        'notes',
    ];

    protected $casts = [
        'price_per_month' => 'decimal:2',
        'capacity'        => 'integer',
        'floor'           => 'integer',  // ✅ แก้ไข: cast floor เป็น integer
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

    /**
     * ดึงการจองปัจจุบันที่ยังไม่เช็คเอาต์
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
     * Alias สำหรับ price_per_month
     */
    public function getRentAmountAttribute(): ?float
    {
        return $this->price_per_month;
    }
}
