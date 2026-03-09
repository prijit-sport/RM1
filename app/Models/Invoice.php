<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

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

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return in_array($this->status, ['sent', 'overdue']) 
            && $this->due_date->isPast();
    }

    /**
     * Calculate late fee based on days overdue.
     */
    public function calculateLateFee(float $rate = 0.01): float
    {
        if (!$this->due_date->isPast()) {
            return 0;
        }

        $daysOverdue = now()->diffInDays($this->due_date);
        return $this->total * $rate * $daysOverdue;
    }

    /**
     * Mark invoice as paid.
     */
    public function markAsPaid(string $method, ?float $amount = null): void
    {
        $this->update([
            'status' => 'paid',
            'payment_method' => $method,
            'payment_date' => now(),
            'paid_amount' => $amount ?? $this->total,
        ]);
    }
}
