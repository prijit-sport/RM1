<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\Role;
use App\Models\User;

class BookingPolicy
{
    /**
     * ทุก role ที่ login แล้วดูรายการจองได้
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * ทุก role ที่ login แล้วดูรายละเอียดจองได้
     */
    public function view(User $user, Booking $booking): bool
    {
        return true;
    }

    /**
     * ทุก role ที่ login แล้วสร้างจองได้
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * ทุก role ที่ login แล้วแก้ไขจองได้
     */
    public function update(User $user, Booking $booking): bool
    {
        return true;
    }

    /**
     * Admin หรือ Staff ลบจองได้
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->hasRole(\App\Models\Role::ADMIN)
            || $user->hasRole(\App\Models\Role::STAFF);
    }

    /**
     * ✅ confirm ต้องเป็น Admin หรือ Staff เท่านั้น
     */
    public function confirm(User $user, Booking $booking): bool
    {
        return $user->hasRole(\App\Models\Role::ADMIN)
            || $user->hasRole(\App\Models\Role::STAFF);
    }

    /**
     * ✅ cancel ต้องเป็น Admin หรือ Staff เท่านั้น
     */
    public function cancel(User $user, Booking $booking): bool
    {
        return $user->hasRole(\App\Models\Role::ADMIN)
            || $user->hasRole(\App\Models\Role::STAFF);
    }

    /**
     * Admin หรือ Staff export ได้
     */
    public function export(User $user): bool
    {
        return $user->hasRole(\App\Models\Role::ADMIN)
            || $user->hasRole(\App\Models\Role::STAFF);
    }
}
