<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // ✅ FIX: role_id ไม่เคยอยู่ใน fillable มาก่อน ทำให้ User::create([...])
        // ที่ใส่ role_id ไปด้วยถูก Laravel เงียบ ๆ เมินทิ้ง (mass assignment
        // protection) ไม่ error ให้เห็นเลย — เคยทำให้สร้าง Staff user แล้วไม่มี
        // role ผูกอยู่โดยไม่รู้ตัวมาแล้วครั้งหนึ่ง เพิ่มไว้กันไม่ให้เกิดซ้ำ
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Role, $this>
     */
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->role && $this->role->permissions()->where('name', $permission)->exists();
    }

    public function hasRole(string $role): bool
    {
        // Ensure role is loaded
        if (! $this->relationLoaded('role')) {
            $this->load('role');
        }

        return $this->role && $this->role->name === $role;
    }

    /**
     * Check if user is Manager or Admin
     */
    public function isManagerOrAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN) || $this->hasRole(Role::STAFF);
    }

    /**
     * Check if user has any of the given permissions
     */
    /**
     * Check if user has any of the given permissions.
     *
     * @param  array<int, string>|string  $permissions
     */
    public function hasAnyPermission($permissions): bool
    {
        if (! $this->role) {
            return false;
        }

        $permissions = is_array($permissions) ? $permissions : func_get_args();

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compatibility with Illuminate\Foundation\Auth\User::canAny signature.
     *
     * @param  iterable<int, string>|string  $abilities
     * @param  array<int, mixed>|mixed  $arguments
     */
    public function canAny($abilities, $arguments = []): bool
    {
        return $this->hasAnyPermission(is_array($abilities) ? $abilities : [$abilities]);
    }

    // Privileged setters (avoid mass-assignment for role/status)
    public function assignRole(int $roleId): void
    {
        $this->role_id = $roleId;
        $this->save();
    }

    public function setActive(bool $isActive): void
    {
        $this->is_active = $isActive;
        $this->save();
    }
}
