<?php

namespace App\Policies;

use App\Models\Meter;
use App\Models\Role;
use App\Models\User;

class MeterPolicy
{
    // ✅ FIX: เปิดให้ Staff เข้าใช้งานมิเตอร์น้ำ/ไฟได้ด้วย (เดิมจำกัดแค่ Admin)
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }

    public function view(User $user, Meter $meter): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }

    public function update(User $user, Meter $meter): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }

    public function delete(User $user, Meter $meter): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }

    public function export(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }
}
