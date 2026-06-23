<?php

namespace App\Policies;

use App\Models\Maintenance;
use App\Models\User;

class MaintenancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

    public function view(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

    public function update(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

    public function delete(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

    public function export(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

    public function startWork(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

    public function completeWork(User $user, Maintenance $maintenance): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

    public function byRoom(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }
}

