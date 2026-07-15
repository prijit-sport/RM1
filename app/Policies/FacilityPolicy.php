<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\Role;
use App\Models\User;

class FacilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function view(User $user, Facility $facility): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function update(User $user, Facility $facility): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function delete(User $user, Facility $facility): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function export(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }
}

