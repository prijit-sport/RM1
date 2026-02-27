<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['room', 'guest'])->paginate(10);
        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $rooms = Room::where('status', 'available')->get();
        $guests = Guest::all();
        return view('bookings.create', compact('rooms', 'guests'));
    }

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

        $room = Room::find($validated['room_id']);
        $checkInDate = new \DateTime($validated['check_in_date']);
        $checkOutDate = new \DateTime($validated['check_out_date']);
        $days = $checkOutDate->diff($checkInDate)->days;
        $validated['total_price'] = $room->price_per_night * $days;

        Booking::create($validated);
        return redirect()->route('bookings.index')->with('success', 'การจองถูกสร้างสำเร็จ');
    }

    public function show(Booking $booking)
    {
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $rooms = Room::all();
        $guests = Guest::all();
        return view('bookings.edit', compact('booking', 'rooms', 'guests'));
    }

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

        $room = Room::find($validated['room_id']);
        $checkInDate = new \DateTime($validated['check_in_date']);
        $checkOutDate = new \DateTime($validated['check_out_date']);
        $days = $checkOutDate->diff($checkInDate)->days;
        $validated['total_price'] = $room->price_per_night * $days;

        $booking->update($validated);
        return redirect()->route('bookings.show', $booking)->with('success', 'การจองถูกอัปเดตสำเร็จ');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'การจองถูกลบสำเร็จ');
    }

    public function export(Request $request)
    {
        $bookings = Booking::with(['room', 'guest'])->orderBy('id', 'desc')->get();
        $filename = 'bookings_export_' . date('Y-m-d') . '.xlsx';

        $rows = [];
        $rows[] = ['Booking ID (รหัสการจอง)', 'Room (ห้อง)', 'Guest (ผู้เข้าพัก)', 'Check In (วันเข้า)', 'Check Out (วันออก)', 'Total Price (ราคารวม)', 'Status (สถานะ)', 'Notes (หมายเหตุ)'];

        foreach ($bookings as $booking) {
            $rows[] = [
                $booking->id,
                $booking->room->room_number ?? '-',
                trim(($booking->guest->first_name ?? '') . ' ' . ($booking->guest->last_name ?? '')) ?: '-',
                optional($booking->check_in_date)->format('d/m/Y'),
                optional($booking->check_out_date)->format('d/m/Y'),
                $booking->total_price,
                $booking->status,
                $booking->notes ?? '-',
            ];
        }

        return xlsx_download($filename, $rows);
    }
}

