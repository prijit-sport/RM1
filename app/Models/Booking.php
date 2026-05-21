<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
/**
 * Booking Model
 * 
 * @property int $id
 * @property int $guest_id
 * @property int $room_id
 * @property \Carbon\Carbon $check_in_date
 * @property \Carbon\Carbon $check_out_date
 * @property \Carbon\Carbon|null $actual_check_in
 * @property \Carbon\Carbon|null $actual_check_out
 * @property float $rent_amount
 * @property float $deposit_amount
 * @property float|null $electric_meter_start
 * @property float|null $water_meter_start
 * @property string $status
 * @property string|null $notes
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * 
 * Accessors:
 * @property float $total_price (calculated: rent_amount + deposit_amount)
 * @property string $status_label (Thai: ยืนยันแล้ว, ยกเลิก, เช็คอินแล้ว, เช็คเอาต์แล้ว)
 * @property string $status_badge (Bootstrap color: success, danger, primary, secondary, warning)
 * 
 * Relations:
 * @property \App\Models\Guest $guest
 * @property \App\Models\Room $room
 */
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
        'rent_amount',
        'deposit_amount',
        'total_price',
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
        'electric_meter_start' => 'decimal:2',
        'water_meter_start' => 'decimal:2',
    ];
 
    // ─────────────────────────────────────────
    //  RELATIONSHIPS
    // ─────────────────────────────────────────
 
    /**
     * Get the guest associated with the booking.
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
 
    /**
     * Get the room associated with the booking.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
 
    // ─────────────────────────────────────────
    //  ACCESSORS
    // ─────────────────────────────────────────
 
    /**
     * ยอดรวม = ค่าเช่า + มัดจำ (คำนวณ real-time ไม่เก็บใน DB)
     * 
     * @return float
     */
    public function getTotalPriceAttribute(): float
    {
        return (float) ($this->rent_amount ?? 0) + (float) ($this->deposit_amount ?? 0);
    }
 
    /**
     * label สถานะภาษาไทย
     * 
     * @return string
     */
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
 
    /**
     * badge color สำหรับ Bootstrap
     * 
     * @return string
     */
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
 