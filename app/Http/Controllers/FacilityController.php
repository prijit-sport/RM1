<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Room;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    // ─────────────────────────────────────────
    //  LIST
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Facility::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $facilities = $query->latest('id')->paginate(10)->withQueryString();

        // ───── นับ status แต่ละตัว ใน 1 query (efficient) ─────
        $statusCounts = Facility::selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $stats = [
            'total'        => Facility::count(),
            'good'         => $statusCounts['good']         ?? 0,
            'fair'         => $statusCounts['fair']         ?? 0,
            'needs_repair' => $statusCounts['needs_repair'] ?? 0,
            'maintenance'  => $statusCounts['maintenance']  ?? 0,
            'damaged'      => $statusCounts['damaged']      ?? 0,
            'retired'      => $statusCounts['retired']      ?? 0,
        ];

        $locations = Facility::select('location')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        return view('facilities.index', compact('facilities', 'stats', 'locations'));
    }

    // ─────────────────────────────────────────
    //  CREATE
    // ─────────────────────────────────────────
    public function create()
    {
        // ดึงข้อมูลห้องทั้งหมดเพื่อไปแสดงใน Dropdown
        $rooms = Room::orderBy('room_number', 'asc')->get();
        return view('facilities.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|max:255',
            'type'                  => 'required|in:bed,mattress,wardrobe,dressing_table,tv_stand,clothes_rack',
            'location'              => 'required|max:255',
            'description'           => 'nullable|max:1000',
            'status'                => 'required|in:good,fair,needs_repair,maintenance,damaged,retired',
            'maintenance_schedule'  => 'nullable|max:255',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date|after_or_equal:last_maintenance_date',
        ]);

        Facility::create($validated);

        return redirect()->route('facilities.index')
            ->with('success', __('ui.facility.created'));
    }

    // ─────────────────────────────────────────
    //  SHOW
    // ─────────────────────────────────────────
    public function show(Facility $facility)
    {
        return view('facilities.show', compact('facility'));
    }

    // ─────────────────────────────────────────
    //  EDIT
    // ─────────────────────────────────────────
    public function edit(Facility $facility)
    {
        // ดึงข้อมูลห้องทั้งหมดเพื่อให้เลือกเปลี่ยนที่ตั้งได้
        $rooms = Room::orderBy('room_number', 'asc')->get();
        return view('facilities.edit', compact('facility', 'rooms'));
    }

    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name'                  => 'required|max:255',
            'type'                  => 'required|in:bed,mattress,wardrobe,dressing_table,tv_stand,clothes_rack',
            'location'              => 'required|max:255',
            'description'           => 'nullable|max:1000',
            'status'                => 'required|in:good,fair,needs_repair,maintenance,damaged,retired',
            'maintenance_schedule'  => 'nullable|max:255',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date|after_or_equal:last_maintenance_date',
        ]);

        $facility->update($validated);

        return redirect()->route('facilities.show', $facility)
            ->with('success', __('ui.facility.updated'));
    }

    // ─────────────────────────────────────────
    //  DELETE
    // ─────────────────────────────────────────
    public function destroy(Facility $facility)
    {
        $facility->delete();

        return redirect()->route('facilities.index')
            ->with('success', __('ui.facility.deleted'));
    }

    // ─────────────────────────────────────────
    //  EXPORT
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $facilities = Facility::orderBy('id', 'desc')->get();
        $filename = 'facilities_export_' . date('Y-m-d') . '.xlsx';

        $typeLabels = [
            'bed'            => 'เตียง',
            'mattress'       => 'ที่นอน',
            'wardrobe'       => 'ตู้เสื้อผ้า',
            'dressing_table' => 'โต๊ะเครื่องแป้ง',
            'tv_stand'       => 'ชั้นวางทีวี',
            'clothes_rack'   => 'ราวแขวนผ้า',
        ];

        $statusLabels = [
            'good'         => 'ใช้งานได้',
            'fair'         => 'สภาพปานกลาง',
            'needs_repair' => 'ต้องซ่อม',
            'maintenance'  => 'กำลังซ่อมบำรุง',
            'damaged'      => 'ชำรุด',
            'retired'      => 'ปลดประจำการ',
        ];

        $rows = [];
        $rows[] = [
            'ชื่อ',
            'ประเภท',
            'ที่ตั้ง',
            'คำอธิบาย',
            'สถานะ',
            'ตารางซ่อมบำรุง',
            'ซ่อมล่าสุด',
            'ซ่อมครั้งต่อไป',
        ];

        foreach ($facilities as $facility) {
            $rows[] = [
                $facility->name,
                $typeLabels[$facility->type] ?? $facility->type,
                $facility->location,
                $facility->description ?? '-',
                $statusLabels[$facility->status] ?? $facility->status,
                $facility->maintenance_schedule ?? '-',
                optional($facility->last_maintenance_date)->format('d/m/Y') ?? '-',
                optional($facility->next_maintenance_date)->format('d/m/Y') ?? '-',
            ];
        }

        return xlsx_download($filename, $rows);
    }
}