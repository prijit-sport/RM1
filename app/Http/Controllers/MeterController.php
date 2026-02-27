<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MeterController extends Controller
{
    public function index(Request $request)
    {
        $query = Meter::with('room');

        if ($request->filled('search')) {
            $query->where('meter_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $meters = $query->orderBy('id', 'desc')->paginate(10);
        return view('meters.index', compact('meters'));
    }

    public function create()
    {
        $rooms = Room::orderBy('room_number')->get();
        return view('meters.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'type' => [
                'required',
                Rule::in(['water', 'electric']),
                Rule::unique('meters')->where(fn ($q) => $q
                    ->where('room_id', $request->input('room_id'))
                    ->where('type', $request->input('type'))),
            ],
            'meter_number' => ['required', 'max:100', 'unique:meters,meter_number'],
            'unit' => ['nullable', 'max:20'],
            'installed_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'max:500'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
        $meter = Meter::create($validated);

        return redirect()->route('meters.show', $meter)->with('success', __('ui.meter.created'));
    }

    public function show(Meter $meter)
    {
        $meter->load(['room', 'readings' => fn ($q) => $q->latest('reading_date')->limit(10)]);
        return view('meters.show', compact('meter'));
    }

    public function edit(Meter $meter)
    {
        $rooms = Room::orderBy('room_number')->get();
        return view('meters.edit', compact('meter', 'rooms'));
    }

    public function update(Request $request, Meter $meter)
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'type' => [
                'required',
                Rule::in(['water', 'electric']),
                Rule::unique('meters')->where(fn ($q) => $q
                    ->where('room_id', $request->input('room_id'))
                    ->where('type', $request->input('type'))
                    ->where('id', '!=', $meter->id)),
            ],
            'meter_number' => ['required', 'max:100', Rule::unique('meters', 'meter_number')->ignore($meter->id)],
            'unit' => ['nullable', 'max:20'],
            'installed_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'max:500'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $meter->update($validated);

        return redirect()->route('meters.show', $meter)->with('success', __('ui.meter.updated'));
    }

    public function destroy(Meter $meter)
    {
        $meter->delete();
        return redirect()->route('meters.index')->with('success', __('ui.meter.deleted'));
    }

    public function export(Request $request)
    {
        $query = Meter::with('room');

        if ($request->filled('search')) {
            $query->where('meter_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $meters = $query->orderBy('id', 'desc')->get();
        $filename = 'meters_' . date('Ymd_His') . '.xlsx';

        $rows = [];
        $rows[] = ['Room', 'Type', 'Meter Number', 'Unit', 'Status', 'Installed Date', 'Notes'];

        foreach ($meters as $meter) {
            $rows[] = [
                $meter->room->room_number ?? '-',
                $meter->type === 'water' ? 'Water' : 'Electric',
                $meter->meter_number,
                $meter->unit ?? '-',
                $meter->is_active ? 'Active' : 'Inactive',
                $meter->installed_at ? $meter->installed_at->format('d/m/Y') : '-',
                $meter->notes ?? '-',
            ];
        }

        return xlsx_download($filename, $rows);
    }
}

