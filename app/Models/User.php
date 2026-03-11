<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
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
        return $this->belongsTo(Role::class);
    }

    public function hasPermission($permission)
    {
        return $this->role && $this->role->permissions()->where('name', $permission)->exists();
    }

    public function hasRole($role)
    {
        // Ensure role is loaded
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        
        return $this->role && $this->role->name === $role;
    }

    /**
     * Check if user is Manager or Admin
     */
    public function isManagerOrAdmin(): bool
    {
        return $this->hasRole('Admin') || $this->hasRole('Manager');
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission($permissions): bool
    {
        if (!$this->role) {
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
}
