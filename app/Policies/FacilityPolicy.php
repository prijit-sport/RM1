<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;
use App\Models\Role;



class FacilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function view(User $user, Facility $facility): bool
    {
        return $user->hasRole('Admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function update(User $user, Facility $facility): bool
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, Facility $facility): bool
    {
        return $user->hasRole('Admin');
    }

    public function export(User $user): bool
    {
        return $user->hasRole('Admin');
    }
}
