<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FacilityController extends Controller
{
    // ─────────────────────────────────────────
    //  LIST
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('viewAny', Facility::class);

        $query = Facility::with('room'); // ✅ เพิ่ม eager load

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
            // ✅ กรอง status = 'active' หรือ 'good' ให้ตรงกัน
            $statusFilter = $request->status;
            if ($statusFilter === 'good') {
                $query->whereIn('status', ['good', 'active']);
            } else {
                $query->where('status', $statusFilter);
            }
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%'.$request->location.'%');
        }

        $cacheKey = 'facilities.list.'.md5(json_encode([
            'search' => $request->search,
            'type' => $request->type,
            'status' => $request->status,
            'location' => $request->location,
            'page' => $request->get('page', 1),
        ]));

        $facilities = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($query) {
            return $query->latest('id')->paginate(20)->withQueryString();
        });

        // ✅ นับสถิติ รวม 'active' เข้ากับ 'good'

        $statusCounts = Facility::selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        $stats = [
            'total' => Facility::count(),
            // รวม active + good เข้าด้วยกัน
            'good' => ($statusCounts['good'] ?? 0) + ($statusCounts['active'] ?? 0),
            'fair' => $statusCounts['fair'] ?? 0,
            'needs_repair' => $statusCounts['needs_repair'] ?? 0,
            'maintenance' => $statusCounts['maintenance'] ?? 0,
            'damaged' => $statusCounts['damaged'] ?? 0,
            'retired' => $statusCounts['retired'] ?? 0,
        ];

        $locations = Facility::select('location')
            ->distinct()
            ->whereNotNull('location')
            ->orderBy('location')
            ->pluck('location');

        return view('facilities.index', compact('facilities', 'stats', 'locations'));
    }

    // ─────────────────────────────────────────
    //  CREATE
    // ─────────────────────────────────────────
    public function create(Request $request)
    {
        $this->authorize('create', Facility::class);

        $rooms = Room::orderBy('room_number')->get();

        // ✅ รับ room_id จาก query parameter เพื่อ auto-select
        // เช่น /facilities/create?room_id=5
        $selectedRoomId = $request->input('room_id');

        return view('facilities.create', compact('rooms', 'selectedRoomId'));

    }

    // ─────────────────────────────────────────
    //  STORE
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $this->authorize('create', Facility::class);

        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'name' => 'required|max:255',
            'type' => 'required|in:bed,mattress,wardrobe,dressing_table,tv_stand,clothes_rack',
            'location' => 'required|max:255',
            'description' => 'nullable|max:1000',
            'status' => 'required|in:good,fair,needs_repair,maintenance,damaged,retired',
            'maintenance_schedule' => 'nullable|max:255',
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
        $this->authorize('view', $facility);

        $facility->load('room');

        return view('facilities.show', compact('facility'));
    }

    // ─────────────────────────────────────────
    //  EDIT
    // ─────────────────────────────────────────
    public function edit(Facility $facility)
    {
        $this->authorize('update', $facility);

        $rooms = Room::orderBy('room_number')->get();

        return view('facilities.edit', compact('facility', 'rooms'));
    }

    // ─────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────
    public function update(Request $request, Facility $facility)
    {
        $this->authorize('update', $facility);

        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'name' => 'required|max:255',
            'type' => 'required|in:bed,mattress,wardrobe,dressing_table,tv_stand,clothes_rack',
            'location' => 'required|max:255',
            'description' => 'nullable|max:1000',
            'status' => 'required|in:good,fair,needs_repair,maintenance,damaged,retired',
            'maintenance_schedule' => 'nullable|max:255',
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
        $this->authorize('delete', $facility);

        $facility->delete();

        return redirect()->route('facilities.index')
            ->with('success', __('ui.facility.deleted'));

    }

    // ─────────────────────────────────────────
    //  EXPORT
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $this->authorize('export', Facility::class);

        $facilities = Facility::with('room')->orderBy('id', 'desc')->get(); // ✅ เพิ่ม with('room')
        $filename = 'facilities_export_'.date('Y-m-d').'.xlsx';

        $typeLabels = [
            'bed' => 'เตียง',
            'mattress' => 'ที่นอน',
            'wardrobe' => 'ตู้เสื้อผ้า',
            'dressing_table' => 'โต๊ะเครื่องแป้ง',
            'tv_stand' => 'ชั้นวางทีวี',
            'clothes_rack' => 'ราวแขวนผ้า',
        ];

        $statusLabels = [
            'active' => 'ใช้งานได้',
            'good' => 'ใช้งานได้',
            'fair' => 'สภาพปานกลาง',
            'needs_repair' => 'ต้องซ่อม',
            'maintenance' => 'กำลังซ่อมบำรุง',
            'damaged' => 'ชำรุด',
            'retired' => 'ปลดประจำการ',
        ];

        $rows = [];
        $rows[] = ['ชื่อ', 'ประเภท', 'ที่ตั้ง', 'ห้องพัก', 'ชั้น', 'โซน', 'คำอธิบาย', 'สถานะ', 'ตารางซ่อม', 'ซ่อมล่าสุด', 'ซ่อมครั้งต่อไป']; // ✅ เพิ่ม ชั้น โซน

        foreach ($facilities as $f) {
            $rows[] = [
                $f->name,
                $typeLabels[$f->type] ?? $f->type,
                $f->location,
                $f->room?->room_number ?? '-',
                $f->room?->floor ?? '-', // ✅ เพิ่ม floor
                $f->room?->zone ?? '-',  // ✅ เพิ่ม zone
                $f->description ?? '-',
                $statusLabels[$f->status] ?? $f->status,
                $f->maintenance_schedule ?? '-',
                optional($f->last_maintenance_date)->format('d/m/Y') ?? '-',
                optional($f->next_maintenance_date)->format('d/m/Y') ?? '-',
            ];
        }

        return xlsx_download($filename, $rows);
    }

    /**
     * API: ดึง facilities ของห้องที่เลือก
     * ใช้สำหรับ dependent dropdown ใน Maintenance form
     */
    public function byRoom(int $roomId)
    {
        $facilities = Facility::where('room_id', $roomId)
            ->select('id', 'name', 'type', 'status')
            ->get();

        return response()->json($facilities);
    }
}
