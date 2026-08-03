<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contractor_name',
        'contract_number',
        'title',
        'contract_date',
        'landlord_name',
        'landlord_id_number',
        'landlord_phone',
        'landlord_address',
        'description',
        'start_date',
        'end_date',
        'duration',
        'monthly_rent',
        'monthly_rent_text',
        'deposit',
        'advance_payment',
        'advance_payment_months',
        'due_date_day',
        'electricity_rate',
        'water_rate',
        'late_fee',
        'other_fees',
        'terms',
        'tenant_signature',
        'landlord_signature',
        'witness_signature',
        'tenant_sign_date',
        'landlord_sign_date',
        'witness_sign_date',
        'amount',
        'status',
        'notes',
        'photo_count',
        'room_id',
        'guest_id',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'tenant_sign_date' => 'date',
        'landlord_sign_date' => 'date',
        'witness_sign_date' => 'date',
        'monthly_rent' => 'decimal:2',
        'deposit' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'electricity_rate' => 'decimal:2',
        'water_rate' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    // ─────────────────────────────────────────
    //  Relationships
    // ─────────────────────────────────────────

    /** ห้องพักที่ผูกกับสัญญานี้ */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /** ผู้เช่าที่ผูกกับสัญญานี้ */
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    // ─────────────────────────────────────────
    //  Scopes — ใส่ type hint ครบ ไม่มี warning
    // ─────────────────────────────────────────

    /** กรองเฉพาะสัญญาที่ active */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /** กรองเฉพาะสัญญาที่หมดอายุแล้ว */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('end_date', '<', now());
    }

    // ─────────────────────────────────────────
    //  Accessors
    // ─────────────────────────────────────────

    /** แสดงสถานะเป็นภาษาไทย */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'กำลังใช้งาน',
            'draft' => 'ร่าง',
            'completed' => 'สิ้นสุดแล้ว',
            'cancelled' => 'ยกเลิก',
            default => $this->status ?? '',
        };
    }

    // ─────────────────────────────────────────
    //  Helpers — แปลงตัวเลขเป็นภาษาไทย
    // ─────────────────────────────────────────

    public static function ThaiBaht(string $amount): string
    {
        $amount = (float) $amount;
        $thai = ['', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า', 'สิบ'];
        $unit = ['', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'];
        $intPart = (int) floor($amount);
        $decPart = (int) round(($amount - $intPart) * 100);

        $result = self::convertInt($intPart, $thai, $unit).'บาท';
        $result .= $decPart > 0
            ? self::convertInt($decPart, $thai, $unit).'สตางค์'
            : 'ถ้วน';

        return $result;
    }

    private static function convertInt(int $number, array $thai, array $unit): string
    {
        if ($number === 0) {
            return '';
        }
        $result = '';
        $digits = str_split((string) $number);
        $len = count($digits);
        foreach ($digits as $i => $d) {
            $d = (int) $d;
            $pos = $len - $i - 1;
            if ($d === 0) {
                continue;
            }
            if ($d === 1 && $pos === 1) {
                $result .= 'สิบ';
            } elseif ($d === 2 && $pos === 1) {
                $result .= 'ยี่สิบ';
            } else {
                $result .= $thai[$d].($pos > 0 ? $unit[$pos % 6] : '');
            }
        }

        return $result;
    }
}
