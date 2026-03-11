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
        // New fields for PDF
        'landlord_id_number',
        'landlord_phone',
        'advance_payment_months',
        'due_date_day',
        'monthly_rent_text',
        'duration',
        'photo_count',
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

    /**
     * Get formatted monthly rent in Thai text.
     */
    public function getMonthlyRentTextAttribute(): string
    {
        return $this->attributes['monthly_rent_text'] ?? $this->convertNumberToThaiText($this->monthly_rent);
    }

    /**
     * Convert number to Thai text.
     */
    private function convertNumberToThaiText(float|int|string $number): string
    {
        $normalized = (float) $number;
        $text = number_format($normalized, 2, '.', '');
        $ex = explode('.', $text);
        $baht = $this->convert($ex[0]);
        $satang = isset($ex[1]) ? $this->convert($ex[1]) : '';
        
        return $baht . 'บาท' . ($satang ? $satang . 'สตางค์' : '');
    }

    private function convert($number)
    {
        $values = ['', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า', 'สิบ'];
        $powers = ['', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'];
        $output = '';
        
        $number = str_pad($number, 7, '0', STR_PAD_LEFT);
        $len = strlen($number);
        
        for ($i = 0; $i < $len; $i++) {
            $n = (int)$number[$i];
            $p = $len - $i - 1;
            
            if ($n > 0) {
                if ($n == 1 && $p == 1) $output .= 'สิบ';
                elseif ($n == 2 && $p == 1) $output .= 'ยี่สิบ';
                else $output .= $values[$n] . $powers[$p];
            }
        }
        
        return $output;
    }
}
