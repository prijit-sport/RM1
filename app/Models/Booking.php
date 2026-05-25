<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
 
/**
 * Booking Model
 *
 * @property int $id
 * @property int $guest_id
 * @property int $room_id
 * @property Carbon $check_in_date
 * @property Carbon|null $check_out_date
 * @property float $rent_amount
 * @property float $deposit_amount
 * @property float $total_price
 * @property int|null $electric_meter_start
 * @property int|null $water_meter_start
 * @property string $status (confirmed, cancelled)
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * Relations:
 * @property-read Guest $guest
 * @property-read Room $room
 * @property-read \Illuminate\Database\Eloquent\Collection<Invoice> $invoices
 */
class Booking extends Model
{
    use SoftDeletes;
 
    // Status constants
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
 
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
        'total_price'          => 'decimal:2',
        'electric_meter_start' => 'integer',
        'water_meter_start'    => 'integer',
    ];
 
    // ═══════════════════════════════════════════════════════════════
    //  RELATIONSHIPS
    // ═══════════════════════════════════════════════════════════════
 
    /**
     * Get the guest that owns the booking.
     *
     * @return BelongsTo<Guest, Booking>
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
 
    /**
     * Get the room that owns the booking.
     *
     * @return BelongsTo<Room, Booking>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
 
    /**
     * Get invoices for this booking.
     *
     * @return HasMany<Invoice>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
 
    // ═══════════════════════════════════════════════════════════════
    //  ACCESSORS / MUTATORS
    // ═══════════════════════════════════════════════════════════════
 
    /**
     * Get status label in Thai
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_CONFIRMED => 'ยืนยันแล้ว',
            self::STATUS_CANCELLED => 'ยกเลิก',
            default                => 'ยืนยันแล้ว',
        };
    }
 
    /**
     * Get status badge color for Bootstrap
     *
     * @return string
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_CONFIRMED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default                => 'secondary',
        };
    }
 
    // ═══════════════════════════════════════════════════════════════
    //  HELPER METHODS
    // ═══════════════════════════════════════════════════════════════
 
    /**
     * Check if booking is active (confirmed and not cancelled)
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }
 
    /**
     * Check if booking is cancelled
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
 
    /**
     * Get booking duration in days
     *
     * @return int
     */
    public function getDurationInDays(): int
    {
        $endDate = $this->check_out_date ?? now();
        return (int) $this->check_in_date->diffInDays($endDate);
    }
}