<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Guest;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect to login if not authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $roomCount = Room::count();
        $guestCount = Guest::count();
        $bookingCount = Booking::count();
        $occupiedCount = Room::where('status', 'occupied')->count();

        return view('dashboard', compact('roomCount', 'guestCount', 'bookingCount', 'occupiedCount'));
    }
}
