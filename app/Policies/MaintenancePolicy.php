<?php

namespace App\Policies;

use App\Models\Maintenance;
use App\Models\Role;
use App\Models\User;

class MaintenancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }

    public function view(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }

    public function update(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }

    public function delete(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }

    public function export(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }

    public function startWork(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }

    public function completeWork(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }

    public function byRoom(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::STAFF);
    }
}
