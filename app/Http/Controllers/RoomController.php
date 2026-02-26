<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the rooms.
     */
    public function index()
    {
        $rooms = Room::paginate(10);
        return view('rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new room.
     */
    public function create()
    {
        return view('rooms.create');
    }

    /**
     * Store a newly created room in storage.
     */
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

        Room::create($validated);
        return redirect()->route('rooms.index')->with('success', 'ห้องถูกสร้างสำเร็จ');
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room)
    {
        return view('rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    /**
     * Update the specified room in storage.
     */
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

        $room->update($validated);
        return redirect()->route('rooms.show', $room)->with('success', 'ห้องถูกอัปเดตสำเร็จ');
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'ห้องถูกลบสำเร็จ');
    }

    /**
     * Show the form for bulk creating rooms.
     */
    public function bulkCreate()
    {
        return view('rooms.bulk-create');
    }

    /**
     * Store multiple rooms at once.
     */
    public function bulkStore(Request $request)
    {
        $rooms = $request->input('rooms', []);
        
        foreach ($rooms as $roomData) {
            Room::create([
                'room_number' => $roomData['room_number'],
                'room_type' => $roomData['room_type'],
                'price_per_night' => $roomData['price_per_night'],
                'capacity' => $roomData['capacity'],
                'status' => $roomData['status'] ?? 'available',
                'description' => $roomData['description'] ?? null,
            ]);
        }

        return redirect()->route('rooms.index')->with('success', 'ห้องถูกสร้าง ' . count($rooms) . ' ห้องสำเร็จ');
    }

    /**
     * Export rooms to CSV.
     */
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
                $room->description,
            ];
        }

        $filename = 'rooms_export_' . date('Y-m-d') . '.csv';

        return response()->stream(function () use ($csvData) {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, chr(0xFF) . chr(0xFE));

            foreach ($csvData as $row) {
                $line = '"' . implode('","', array_map(function ($value) {
                    return str_replace('"', '""', (string) $value);
                }, $row)) . '"' . "\r\n";
                fwrite($handle, mb_convert_encoding($line, 'UTF-16LE', 'UTF-8'));
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-16LE',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
