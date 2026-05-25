<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
 
/**
 * Invoice Model
 * 
 * @property int $id
 * @property int|null $booking_id
 * @property int|null $guest_id
 * @property int|null $room_id
 * @property string $invoice_number
 * @property float $amount
 * @property float $tax
 * @property float $total
 * @property float|null $late_fee
 * @property float|null $paid_amount
 * @property string|null $payment_method
 * @property Carbon|null $issue_date (casted)
 * @property Carbon|null $due_date (casted)
 * @property Carbon|null $payment_date (casted)
 * @property string $status (draft, sent, paid, overdue, cancelled)
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * 
 * Relations:
 * @property-read Booking|null $booking
 * @property-read Guest|null $guest
 * @property-read Room|null $room
 */
class Invoice extends Model
{
    use SoftDeletes;
 
    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CANCELLED = 'cancelled';
 
    protected $fillable = [
        'booking_id',
        'guest_id',
        'room_id',
        'invoice_number',
        'amount',
        'tax',
        'total',
        'late_fee',
        'paid_amount',
        'payment_method',
        'payment_date',
        'issue_date',
        'due_date',
        'status',
        'notes',
    ];
 
    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];
 
    // ═══════════════════════════════════════════════════════════════
    //  RELATIONSHIPS
    // ═══════════════════════════════════════════════════════════════
 
    /**
     * Get the booking that owns the invoice.
     * 
     * @return BelongsTo<Booking, Invoice>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
 
    /**
     * Get the guest that owns the invoice.
     * 
     * @return BelongsTo<Guest, Invoice>
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
 
    /**
     * Get the room that owns the invoice.
     * 
     * @return BelongsTo<Room, Invoice>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
 
    // ═══════════════════════════════════════════════════════════════
    //  SCOPES
    // ═══════════════════════════════════════════════════════════════
 
    /**
     * Scope to get paid invoices.
     * 
     * @param Builder<Invoice> $query
     * @return Builder<Invoice>
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PAID);
    }
 
    /**
     * Scope to get pending invoices.
     * 
     * @param Builder<Invoice> $query
     * @return Builder<Invoice>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_SENT, self::STATUS_OVERDUE]);
    }
 
    /**
     * Scope to get overdue invoices.
     * 
     * @param Builder<Invoice> $query
     * @return Builder<Invoice>
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OVERDUE);
    }
 
    /**
     * Scope to get draft invoices.
     * 
     * @param Builder<Invoice> $query
     * @return Builder<Invoice>
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }
 
    /**
     * Scope to get invoices by month.
     * 
     * @param Builder<Invoice> $query
     * @param int $month
     * @param int $year
     * @return Builder<Invoice>
     */
    public function scopeByMonth(Builder $query, int $month, int $year): Builder
    {
        return $query->whereMonth('issue_date', $month)
                     ->whereYear('issue_date', $year);
    }
 
    // ═══════════════════════════════════════════════════════════════
    //  HELPER METHODS
    // ═══════════════════════════════════════════════════════════════
 
    /**
     * Check if invoice is overdue.
     * 
     * @return bool
     */
    public function isOverdue(): bool
    {
        // ตรวจสอบว่า due_date มีค่าและเป็นวันที่ผ่านไปแล้ว
        return $this->due_date 
            && in_array($this->status, [self::STATUS_SENT, self::STATUS_OVERDUE])
            && $this->due_date->isPast();
    }
 
    /**
     * Get overdue days count.
     * 
     * @return int
     */
    public function getOverdueDaysCount(): int
    {
        if (!$this->due_date || !$this->isOverdue()) {
            return 0;
        }
 
        return (int) abs(now()->startOfDay()->diffInDays($this->due_date->startOfDay(), false));
    }
 
    /**
     * Calculate late fee based on days overdue.
     * 
     * @param float $rate Daily rate (default: 1% per day)
     * @return float
     */
    public function calculateLateFee(float $rate = 0.01): float
    {
        // ถ้า due_date ไม่มีหรือยังไม่เกินกำหนด ไม่มีค่าปรับ
        if (!$this->due_date || !$this->due_date->isPast()) {
            return 0;
        }
 
        $daysOverdue = now()->diffInDays($this->due_date);
        return (float) ($this->total * $rate * $daysOverdue);
    }
 
    /**
     * Get remaining amount to pay.
     * 
     * @return float
     */
    public function getRemainingAmount(): float
    {
        $paid = $this->paid_amount ?? 0;
        return max(0, (float) ($this->total - $paid));
    }
 
    /**
     * Check if invoice is fully paid.
     * 
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID 
            || ($this->paid_amount >= $this->total);
    }
 
    /**
     * Check if invoice is partially paid.
     * 
     * @return bool
     */
    public function isPartiallyPaid(): bool
    {
        $paid = $this->paid_amount ?? 0;
        return $paid > 0 && $paid < $this->total;
    }
 
    /**
     * Get payment percentage.
     * 
     * @return float (0-100)
     */
    public function getPaymentPercentage(): float
    {
        if ($this->total == 0) {
            return 0;
        }
 
        $paid = $this->paid_amount ?? 0;
        return min(100, round(($paid / $this->total) * 100, 2));
    }
 
    // ═══════════════════════════════════════════════════════════════
    //  ACTIONS
    // ═══════════════════════════════════════════════════════════════
 
    /**
     * Mark invoice as paid.
     * 
     * @param string $method Payment method (cash, bank_transfer, credit_card, e_wallet, other)
     * @param float|null $amount Amount paid (default: total)
     * @return void
     */
    public function markAsPaid(string $method = 'cash', ?float $amount = null): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'payment_method' => $method,
            'payment_date' => now(),
            'paid_amount' => $amount ?? $this->total,
        ]);
    }
 
    /**
     * Mark invoice as sent.
     * 
     * @return void
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
        ]);
    }
 
    /**
     * Mark invoice as overdue.
     * 
     * @return void
     */
    public function markAsOverdue(): void
    {
        if ($this->due_date && $this->due_date->isPast() && $this->status === self::STATUS_SENT) {
            $this->update([
                'status' => self::STATUS_OVERDUE,
            ]);
        }
    }
 
    /**
     * Cancel invoice.
     * 
     * @return void
     */
    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }
 
    /**
     * Revert to draft.
     * 
     * @return void
     */
    public function revertToDraft(): void
    {
        $this->update([
            'status' => self::STATUS_DRAFT,
            'paid_amount' => 0,
            'payment_method' => null,
            'payment_date' => null,
        ]);
    }
 
    /**
     * Get status label in Thai.
     * 
     * @return string
     */
    public function getStatusLabelThai(): string
    {
        $labels = [
            self::STATUS_DRAFT => 'ร่าง',
            self::STATUS_SENT => 'รอชำระ',
            self::STATUS_PAID => 'ชำระแล้ว',
            self::STATUS_OVERDUE => 'เกินกำหนด',
            self::STATUS_CANCELLED => 'ยกเลิก',
        ];
 
        return $labels[$this->status] ?? $this->status;
    }
}
 