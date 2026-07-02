<?php

namespace App\Policies;

use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\User;

class MeterReadingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function view(User $user, MeterReading $meterReading): bool
    {
        return $user->hasRole('Admin');
    }

    public function create(User $user, Meter $meter): bool
    {
        return $user->hasRole('Admin');
    }

    public function update(User $user, Meter $meter): bool
    {
        return $user->hasRole('Admin');
    }

    public function delete(User $user, Meter $meter): bool
    {
        return $user->hasRole('Admin');
    }
}

