<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\Guest;

class ReportController extends Controller
{
    public function index()
    {
        // สถิติทั่วไป
        $total_rooms = Room::count();
        $occupied_rooms = Room::where('status', 'occupied')->count();
        $available_rooms = Room::where('status', 'available')->count();
        $maintenance_rooms = Room::where('status', 'maintenance')->count();

        // สถิติการจอง
        $total_bookings = Booking::count();
        $pending_bookings = Booking::where('status', 'pending')->count();
        $confirmed_bookings = Booking::where('status', 'confirmed')->count();
        $checked_in = Booking::where('status', 'checked_in')->count();
        $checked_out = Booking::where('status', 'checked_out')->count();
        $cancelled = Booking::where('status', 'cancelled')->count();

        // อัตราการเข้าพัก
        $occupancy_rate = $total_rooms > 0 ? round(($occupied_rooms / $total_rooms) * 100, 2) : 0;

        // รายได้รวม
        $total_revenue = Booking::where('status', '!=', 'cancelled')->sum('total_price');

        // การจองเดือนนี้
        $bookings_this_month = Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ห้องยอดนิยม
        $popular_rooms = Room::withCount('bookings')
            ->orderBy('bookings_count', 'DESC')
            ->limit(5)
            ->get();

        // ประจำวัน
        $today_check_ins = Booking::whereDate('check_in_date', today())->count();
        $today_check_outs = Booking::whereDate('check_out_date', today())->count();

        return view('reports.index', compact(
            'total_rooms',
            'occupied_rooms',
            'available_rooms',
            'maintenance_rooms',
            'total_bookings',
            'pending_bookings',
            'confirmed_bookings',
            'checked_in',
            'checked_out',
            'cancelled',
            'occupancy_rate',
            'total_revenue',
            'bookings_this_month',
            'popular_rooms',
            'today_check_ins',
            'today_check_outs'
        ));
    }
}
