<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'room_id',
        'guest_id',
        'contract_number',
        'title',
        'contract_date',
        'description',
        'start_date',
        'end_date',
        'monthly_rent',
        'deposit',
        'advance_payment',
        'electricity_rate',
        'water_rate',
        'late_fee',
        'other_fees',
        'status',
        'terms',
        'tenant_signature',
        'landlord_signature',
        'landlord_name',
        'landlord_address',
        'witness_signature',
        'tenant_sign_date',
        'landlord_sign_date',
        'witness_sign_date',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'contract_date' => 'date',
        'tenant_sign_date' => 'date',
        'landlord_sign_date' => 'date',
        'witness_sign_date' => 'date',
        'monthly_rent' => 'decimal:2',
        'deposit' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'electricity_rate' => 'decimal:2',
        'water_rate' => 'decimal:2',
        'late_fee' => 'decimal:2',
    ];

    /**
     * Get the room for the contract.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the guest for the contract.
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /**
     * Check if contract is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' 
            && $this->start_date <= now() 
            && $this->end_date >= now();
    }

    /**
     * Check if contract is expiring soon (within 30 days).
     */
    public function isExpiringSoon(): bool
    {
        return $this->status === 'active'
            && $this->end_date >= now()
            && $this->end_date <= now()->addDays(30);
    }
}
