<?php

namespace App\Http\Controllers\Api;

use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Room::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_type')) {
            $query->where('room_type', $request->room_type);
        }

        if ($request->filled('search')) {
            $query->where('room_number', 'like', '%' . $request->search . '%');
        }

        $rooms = $query->paginate(10);

        return response()->json($rooms);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms|max:10',
            'room_type' => 'required|max:50',
            'price_per_month' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|max:500',
            'floor' => 'nullable|integer',
            'building' => 'nullable|string|max:100',
        ]);

        $room = Room::create($validated);

        return response()->json($room, 201);
    }

    public function show(Room $room): JsonResponse
    {
        return response()->json($room->load(['bookings', 'contracts', 'meters']));
    }

    public function update(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms,room_number,' . $room->id . '|max:10',
            'room_type' => 'required|max:50',
            'price_per_month' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|max:500',
            'floor' => 'nullable|integer',
            'building' => 'nullable|string|max:100',
        ]);

        $room->update($validated);

        return response()->json($room);
    }

    public function destroy(Room $room): JsonResponse
    {
        $room->delete();

        return response()->json(['message' => 'Room deleted successfully']);
    }
}

