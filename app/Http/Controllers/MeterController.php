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

        // Search by meter number
        if ($request->filled('search')) {
            $query->where('meter_number', 'like', '%' . $request->search . '%');
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
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

        return redirect()->route('meters.show', $meter)->with('success', 'เพิ่มมิเตอร์สำเร็จ');
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

        return redirect()->route('meters.show', $meter)->with('success', 'อัปเดตมิเตอร์สำเร็จ');
    }

    public function destroy(Meter $meter)
    {
        $meter->delete();
        return redirect()->route('meters.index')->with('success', 'ลบมิเตอร์สำเร็จ');
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

        $filename = 'meters_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['ห้อง', 'ประเภท', 'เลขมิเตอร์', 'หน่วย', 'สถานะ', 'วันที่ติดตั้ง', 'หมายเหตุ'];

        $callback = function () use ($meters) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            
            fputcsv($file, ['ห้อง', 'ประเภท', 'เลขมิเตอร์', 'หน่วย', 'สถานะ', 'วันที่ติดตั้ง', 'หมายเหตุ']);

            foreach ($meters as $meter) {
                fputcsv($file, [
                    $meter->room->room_number ?? '-',
                    $meter->type === 'water' ? 'น้ำ' : 'ไฟฟ้า',
                    $meter->meter_number,
                    $meter->unit ?? '-',
                    $meter->is_active ? 'ใช้งาน' : 'ปิดใช้งาน',
                    $meter->installed_at ? $meter->installed_at->format('d/m/Y') : '-',
                    $meter->notes ?? '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

