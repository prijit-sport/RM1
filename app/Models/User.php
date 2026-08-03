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
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermission(string $permission)
    {
        return $this->role && $this->role->permissions()->where('name', $permission)->exists();
    }

    public function hasRole(string $role)
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
     * @param  array|string  $permissions
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
