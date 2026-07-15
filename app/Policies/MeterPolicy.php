<?php

namespace App\Policies;

use App\Models\Meter;
use App\Models\Role;
use App\Models\User;

class MeterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function view(User $user, Meter $meter): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function update(User $user, Meter $meter): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function delete(User $user, Meter $meter): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function export(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }
}

