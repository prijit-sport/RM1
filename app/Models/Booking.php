<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class Booking extends Model
{
    use SoftDeletes;
 
    protected $fillable = [
        'guest_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'actual_check_in',
        'actual_check_out',
        // total_price ไม่ได้อยู่ใน fillable เพราะคำนวณจาก accessor
        'rent_amount',
        'deposit_amount',
        'electric_meter_start',
        'water_meter_start',
        'status',
        'notes',
    ];
 
    protected $casts = [
        'check_in_date'    => 'date',
        'check_out_date'   => 'date',
        'actual_check_in'  => 'date',
        'actual_check_out' => 'date',
        'rent_amount'      => 'decimal:2',
        'deposit_amount'   => 'decimal:2',
    ];
 
    // ─────────────────────────────────────────
    //  RELATIONSHIPS
    // ─────────────────────────────────────────
 
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
 
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
 
    // ─────────────────────────────────────────
    //  ACCESSORS
    // ─────────────────────────────────────────
 
    /** ยอดรวม = ค่าเช่า + มัดจำ (คำนวณ real-time ไม่เก็บใน DB) */
    public function getTotalPriceAttribute(): float
    {
        return (float) ($this->rent_amount ?? 0) + (float) ($this->deposit_amount ?? 0);
    }
 
    /** label สถานะภาษาไทย */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'confirmed'   => 'ยืนยันแล้ว',
            'cancelled'   => 'ยกเลิก',
            'checked_in'  => 'เช็คอินแล้ว',
            'checked_out' => 'เช็คเอาต์แล้ว',
            default       => 'รอยืนยัน',
        };
    }
 
    /** badge color สำหรับ Bootstrap */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'confirmed'   => 'success',
            'cancelled'   => 'danger',
            'checked_in'  => 'primary',
            'checked_out' => 'secondary',
            default       => 'warning',
        };
    }
}
 