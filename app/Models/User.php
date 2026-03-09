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
        Log::info('[User.hasRole] Checking user: ' . $this->name . ' for role: ' . $role);
        
        // Ensure role is loaded
        if (!$this->relationLoaded('role')) {
            Log::info('[User.hasRole] Role not loaded, loading now...');
            $this->load('role');
        }
        
        $result = $this->role && $this->role->name === $role;
        Log::info('[User.hasRole] Result: ' . ($result ? 'true' : 'false') . ', User role: ' . ($this->role ? $this->role->name : 'null'));
        
        return $result;
    }
}
