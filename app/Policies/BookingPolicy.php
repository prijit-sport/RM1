<?php
 
namespace App\Policies;
 
use App\Models\Booking;
use App\Models\User;
 
class BookingPolicy
{
    /**
     * ✅ User ที่ login แล้ว (ไม่ว่า role อะไร) สามารถดูรายการจองได้
     */
    public function viewAny(User $user): bool
    {
        return true;
    }
 
    /**
     * ✅ User ที่ login แล้ว สามารถดูรายละเอียดจองได้
     */
    public function view(User $user, Booking $booking): bool
    {
        return true;
    }
 
    /**
     * ✅ Admin และ Staff สามารถสร้างจองใหม่ได้
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Staff');
    }
 
    /**
     * ✅ Admin และ Staff สามารถแก้ไขจองได้
     */
    public function update(User $user, Booking $booking): bool
    {
        return $user->hasRole('Admin')
            || $user->hasRole('Staff')
            || $user->hasRole('User')
            || $user->hasRole('Manager');
    }

 
    /**
     * ✅ Admin และ Staff สามารถลบจองได้
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Staff');
    }
 
    /**
     * ✅ Admin และ Staff สามารถยืนยันจองได้
     */
    public function confirm(User $user, Booking $booking): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

 
    /**
     * ✅ Admin และ Staff สามารถยกเลิกจองได้
     */
    public function cancel(User $user, Booking $booking): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

 
    /**
     * ✅ Admin และ Staff สามารถ export ข้อมูลจองได้
     */
    public function export(User $user): bool
    {
        return $user->hasRole('Admin') || $user->hasRole('Staff');
    }
}
 