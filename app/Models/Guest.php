<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

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
        'nationality',
        'id_number',
        'date_of_birth',
        'emergency_contact',   // ✅ แก้จาก emergency_contact_name
        'emergency_phone',     // ✅ แก้จาก emergency_contact_phone
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // ─────────────────────────────────────────
    //  Relationships
    // ─────────────────────────────────────────

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /** ดึงสัญญาที่ active อยู่ (ล่าสุด 1 รายการ) */
    public function activeContract()
    {
        return $this->hasOne(Contract::class)->where('status', 'active')->latest();
    }

    /** ดึงห้องที่พักอยู่ตอนนี้ */
    public function currentRoom()
    {
        return $this->hasOneThrough(
            Room::class,
            Contract::class,
            'guest_id',
            'id',
            'id',
            'room_id'
        )->where('contracts.status', 'active');
    }

    // ─────────────────────────────────────────
    //  Accessors
    // ─────────────────────────────────────────

    /** ชื่อ-นามสกุลเต็ม */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /** อายุ */
    public function getAgeAttribute(): ?int
    {
        if (! $this->date_of_birth instanceof DateTimeInterface) {
            return null;
        }

        return Carbon::instance($this->date_of_birth)->age;
    }

    /** มีห้องพักอยู่ไหม */
    public function getHasRoomAttribute(): bool
    {
        return $this->contracts()->where('status', 'active')->exists();
    }
}
