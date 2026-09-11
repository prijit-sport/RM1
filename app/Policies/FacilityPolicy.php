<?php
 
namespace App\Policies;
 
use App\Models\Facility;
use App\Models\Role;
use App\Models\User;
 
class FacilityPolicy
{
    // ✅ FIX: เปิดให้ Staff เข้าใช้งานสิ่งอำนวยความสะดวกได้ด้วย (เดิมจำกัดแค่ Admin)
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }
 
    public function view(User $user, Facility $facility): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }
 
    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }
 
    public function update(User $user, Facility $facility): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }
 
    public function delete(User $user, Facility $facility): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }
 
    public function export(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }
}
 