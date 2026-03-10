<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    public function __construct()
    {
        Log::info('[RoomController] Constructor called');
    }
    
    public function index(Request $request)
    {
        Log::info('[RoomController] index - User authenticated: ' . (auth()->check() ? 'Yes - ' . auth()->user()->name : 'No'));
        Log::info('[RoomController] index - Session ID: ' . $request->session()->getId());
        Log::info('[RoomController] index - Request URL: ' . $request->fullUrl());
        Log::info('[RoomController] index - Headers: ' . $request->header('Cookie'));
        
        $query = Room::query();

        if ($request->filled('search')) {
            $search = trim($request->string('search'));
            $query->where(function ($q) use ($search): void {
                $q->where('room_number', 'like', '%' . $search . '%')
                    ->orWhere('room_type', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('room_type')) {
            $query->where('room_type', $request->input('room_type'));
        }

        $rooms = $query->latest('id')->paginate(10)->withQueryString();

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
            'price_per_month' => 'required|numeric|min:0',
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
            'price_per_month' => 'required|numeric|min:0',
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
            'rooms.*.price_per_month' => 'required|numeric|min:0',
            'rooms.*.capacity' => 'required|integer|min:1',
            'rooms.*.status' => 'nullable|in:available,occupied,maintenance',
            'rooms.*.description' => 'nullable|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['rooms'] as $roomData) {
                Room::create([
                    'room_number' => $roomData['room_number'],
                    'room_type' => $roomData['room_type'],
                    'price_per_month' => $roomData['price_per_month'],
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
        $csvData[] = ['Room Number', 'Room Type', 'Price/Month', 'Capacity', 'Status', 'Description'];

        foreach ($rooms as $room) {
            $csvData[] = [
                $room->room_number,
                $room->room_type,
                $room->price_per_month,
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

