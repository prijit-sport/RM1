<?php

namespace App\Policies;

use App\Models\Maintenance;
use App\Models\Role;
use App\Models\User;

class MaintenancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::MANAGER);
    }

    public function view(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::MANAGER);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::MANAGER);
    }

    public function update(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::MANAGER);
    }

    public function delete(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::MANAGER);
    }

    public function export(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::MANAGER);
    }

    public function startWork(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::MANAGER);
    }

    public function completeWork(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::MANAGER);
    }

    public function byRoom(User $user): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->hasRole(Role::MANAGER);
    }
}

