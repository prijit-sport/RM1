<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Guest;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the bookings.
     */
    public function index()
    {
        $bookings = Booking::with(['room', 'guest'])->paginate(10);
        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $rooms = Room::where('status', 'available')->get();
        $guests = Guest::all();
        return view('bookings.create', compact('rooms', 'guests'));
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'required|exists:guests,id',
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
            'notes' => 'nullable|max:500',
        ]);

        // Calculate total price
        $room = Room::find($validated['room_id']);
        $checkInDate = new \DateTime($validated['check_in_date']);
        $checkOutDate = new \DateTime($validated['check_out_date']);
        $days = $checkOutDate->diff($checkInDate)->days;
        $validated['total_price'] = $room->price_per_night * $days;

        Booking::create($validated);
        return redirect()->route('bookings.index')->with('success', 'การจองถูกสร้างสำเร็จ');
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        return view('bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking)
    {
        $rooms = Room::all();
        $guests = Guest::all();
        return view('bookings.edit', compact('booking', 'rooms', 'guests'));
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'required|exists:guests,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
            'notes' => 'nullable|max:500',
        ]);

        // Recalculate total price
        $room = Room::find($validated['room_id']);
        $checkInDate = new \DateTime($validated['check_in_date']);
        $checkOutDate = new \DateTime($validated['check_out_date']);
        $days = $checkOutDate->diff($checkInDate)->days;
        $validated['total_price'] = $room->price_per_night * $days;

        $booking->update($validated);
        return redirect()->route('bookings.show', $booking)->with('success', 'การจองถูกอัปเดตสำเร็จ');
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'การจองถูกลบสำเร็จ');
    }
}
