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
        // NOTE: We intentionally do NOT use the 'encrypted' cast on the email /
        // id_number columns. The original plaintext columns are kept as a safety
        // net during the cutover period. Instead, PII is encrypted into dedicated
        // *_ciphertext columns (and looked up via *_hash blind-index columns)
        // using the custom mutators/accessors below.
    ];

    // ─────────────────────────────────────────
    //  PII Encryption (email, id_number)
    // ─────────────────────────────────────────

    private function piiHash(string $value): string
    {
        return hash_hmac('sha256', $value, (string) config('app.key'));
    }

    private function piiEncrypt(string $value): string
    {
        return encrypt($value);
    }

    private function decryptPii(?string $plaintext, string $cipherColumn): ?string
    {
        $cipher = $this->attributes[$cipherColumn] ?? null;
        if (! empty($cipher)) {
            try {
                return decrypt($cipher);
            } catch (\Throwable $e) {
                // fall back to the plaintext column (safety net)
            }
        }

        return $plaintext;
    }

    private function setPiiAttributes(string $field, ?string $value): void
    {
        $this->attributes[$field] = $value;

        if ($value === null || $value === '') {
            $this->attributes[$field.'_ciphertext'] = null;
            $this->attributes[$field.'_hash'] = null;

            return;
        }

        $this->attributes[$field.'_ciphertext'] = $this->piiEncrypt($value);
        $this->attributes[$field.'_hash'] = $this->piiHash($value);
    }

    public function getEmailAttribute(?string $value): ?string
    {
        return $this->decryptPii($value, 'email_ciphertext');
    }

    public function setEmailAttribute(?string $value): void
    {
        $this->setPiiAttributes('email', $value);
    }

    public function getIdNumberAttribute(?string $value): ?string
    {
        return $this->decryptPii($value, 'id_number_ciphertext');
    }

    public function setIdNumberAttribute(?string $value): void
    {
        $this->setPiiAttributes('id_number', $value);
    }

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
