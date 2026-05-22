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
        'rent_amount',
        'deposit_amount',
        'total_price',
        'electric_meter_start',
        'water_meter_start',
        'status',
        'notes',
    ];
 
    protected $casts = [
        'check_in_date'        => 'date',
        'check_out_date'       => 'date',
        'rent_amount'          => 'decimal:2',
        'deposit_amount'       => 'decimal:2',
        'electric_meter_start' => 'integer',
        'water_meter_start'    => 'integer',
    ];
 
    // ─────────────────────────────────────────
    //  Relationships
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
    //  Accessors
    // ─────────────────────────────────────────
 
    /** สถานะเป็นภาษาไทย */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'confirmed' => 'ยืนยันแล้ว',
            'cancelled' => 'ยกเลิก',
            default     => 'ยืนยันแล้ว',
        };
    }
 
    /** Bootstrap badge color */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'confirmed' => 'success',
            'cancelled' => 'danger',
            default     => 'success',
        };
    }
}
 