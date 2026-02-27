<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::paginate(10);

        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms|max:10',
            'room_type' => 'required|max:50',
            'price_per_night' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|max:500',
        ]);

        $validated['description'] = $this->sanitizeNullableText($validated['description'] ?? null);
        Room::create($validated);

        return redirect()->route('rooms.index')->with('success', __('ui.room.created'));
    }

    public function show(Room $room)
    {
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|unique:rooms,room_number,' . $room->id . '|max:10',
            'room_type' => 'required|max:50',
            'price_per_night' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|max:500',
        ]);

        $validated['description'] = $this->sanitizeNullableText($validated['description'] ?? null);
        $room->update($validated);

        return redirect()->route('rooms.show', $room)->with('success', __('ui.room.updated'));
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()->route('rooms.index')->with('success', __('ui.room.deleted'));
    }

    public function bulkCreate()
    {
        return view('rooms.bulk-create');
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'rooms' => 'required|array|min:1',
            'rooms.*.room_number' => 'required|max:10|distinct|unique:rooms,room_number',
            'rooms.*.room_type' => 'required|max:50',
            'rooms.*.price_per_night' => 'required|numeric|min:0',
            'rooms.*.capacity' => 'required|integer|min:1',
            'rooms.*.status' => 'nullable|in:available,occupied,maintenance',
            'rooms.*.description' => 'nullable|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['rooms'] as $roomData) {
                Room::create([
                    'room_number' => $roomData['room_number'],
                    'room_type' => $roomData['room_type'],
                    'price_per_night' => $roomData['price_per_night'],
                    'capacity' => $roomData['capacity'],
                    'status' => $roomData['status'] ?? 'available',
                    'description' => $this->sanitizeNullableText($roomData['description'] ?? null),
                ]);
            }
        });

        return redirect()->route('rooms.index')->with('success', __('ui.room.bulk_created', ['count' => count($validated['rooms'])]));
    }

    public function export(Request $request)
    {
        $rooms = Room::all();

        $csvData = [];
        $csvData[] = ['Room Number', 'Room Type', 'Price/Night', 'Capacity', 'Status', 'Description'];

        foreach ($rooms as $room) {
            $csvData[] = [
                $room->room_number,
                $room->room_type,
                $room->price_per_night,
                $room->capacity,
                $room->status,
                csv_sanitize_text($room->description),
            ];
        }

        $filename = 'rooms_export_' . date('Y-m-d') . '.xlsx';

        return xlsx_download($filename, $csvData);
    }

    private function sanitizeNullableText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $clean = csv_sanitize_text($value);

        return $clean === '' ? null : $clean;
    }
}

