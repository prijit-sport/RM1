<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Manager') || $user->hasRole('Admin');
    }


    public function view(User $user, Booking $booking): bool
    {
        return $user->hasRole('Manager') || $user->hasRole('Admin');
    }


    public function create(User $user): bool
    {
        return $user->hasRole('Manager') || $user->hasRole('Admin');
    }


    public function update(User $user, Booking $booking): bool
    {
        return $user->hasRole('Manager') || $user->hasRole('Admin');
    }


    public function delete(User $user, Booking $booking): bool
    {
        return $user->hasRole('Manager') || $user->hasRole('Admin');
    }


    public function confirm(User $user, Booking $booking): bool
    {
        return $user->hasRole('Manager') || $user->hasRole('Admin');
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $user->hasRole('Manager') || $user->hasRole('Admin');
    }

    public function export(User $user): bool
    {
        return $user->hasRole('Manager') || $user->hasRole('Admin');
    }

}
