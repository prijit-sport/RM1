<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Booking::with(['room', 'guest']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('guest', function ($q) use ($request) {
                    $q->where('first_name', 'like', '%' . $request->search . '%')
                      ->orWhere('last_name', 'like', '%' . $request->search . '%');
                })->orWhereHas('room', function ($q) use ($request) {
                    $q->where('room_number', 'like', '%' . $request->search . '%');
                });
            });
        }

        $bookings = $query->latest()->paginate(10);

        return response()->json($bookings);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'required|exists:guests,id',
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
            'notes' => 'nullable|max:500',
        ]);

        $this->bookingService->create($validated);

        return response()->json(['message' => 'Booking created successfully'], 201);
    }

    public function show(Booking $booking): JsonResponse
    {
        return response()->json($booking->load(['room', 'guest', 'invoices']));
    }

    public function update(Request $request, Booking $booking): JsonResponse
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_id' => 'required|exists:guests,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
            'notes' => 'nullable|max:500',
        ]);

        $this->bookingService->update($booking, $validated);

        return response()->json(['message' => 'Booking updated successfully']);
    }

    public function destroy(Booking $booking): JsonResponse
    {
        $this->bookingService->destroy($booking);

        return response()->json(['message' => 'Booking deleted successfully']);
    }

    public function confirm(Booking $booking): JsonResponse
    {
        $this->bookingService->confirm($booking);

        return response()->json(['message' => 'Booking confirmed successfully']);
    }

    public function cancel(Booking $booking): JsonResponse
    {
        $this->bookingService->cancel($booking);

        return response()->json(['message' => 'Booking cancelled successfully']);
    }
}

