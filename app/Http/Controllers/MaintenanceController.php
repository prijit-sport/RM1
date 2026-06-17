<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Maintenance;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * หมายเหตุ: โค้ดนี้เป็น Laravel (access model fields เป็น property)
 * ปัญหา "Call to unknown function: status/zone/room_number/..." ที่ VSCode พบ
 * มักเป็น false-positive จาก PHP Language Server/IDE helper ไม่เกี่ยวกับ runtime
 */
class MaintenanceController extends Controller
{
    /**
     * หน้าแรก: แสดงรายการแจ้งซ่อมและสรุปสถิติ
     */
    public function index(Request $request)
    {
        $pendingCount    = Maintenance::where('status', 'pending')->count();
        $inProgressCount = Maintenance::where('status', 'in_progress')->count();

        $completedThisMonth = Maintenance::where('status', 'completed')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->count();

        $pending = Maintenance::with(['room', 'facility'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'in_progress' THEN 2 ELSE 3 END")
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $recentlyCompleted = Maintenance::with(['room', 'facility'])
            ->where('status', 'completed')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $query = Maintenance::with(['room', 'facility']);
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('maintenance_type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', "%{$search}%"))
                    ->orWhereHas('facility', fn($f) => $f->where('name', 'like', "%{$search}%"));
            });
        }

        $maintenances = $query->orderByDesc('id')->paginate(10);

        return view('maintenances.index', compact(
            'maintenances',
            'pendingCount',
            'inProgressCount',
            'completedThisMonth',
            'pending',
            'recentlyCompleted'
        ));
    }

    /**
     * หน้าฟอร์มแจ้งซ่อมใหม่
     */
    public function create(Request $request)
    {
        $rooms      = Room::orderBy('floor')->orderBy('zone')->orderBy('room_number')->get();

        // ✅ แก้ไข: ส่ง facilities เฉพาะที่ต้องใช้ตอน Lock mode (มาจาก facility_id)
        $facilities = Facility::all();

        $facilityId         = $request->input('facility_id');
        $selectedRoomId     = null;
        $selectedFacilityId = null;

        if ($facilityId) {
            $facility = Facility::find($facilityId);
            if ($facility && $facility->room_id) {
                $selectedRoomId     = $facility->room_id;
                $selectedFacilityId = $facilityId;
            }
        }

        return view('maintenances.create', compact(
            'rooms',
            'facilities',
            'selectedRoomId',
            'selectedFacilityId'
        ));
    }

    /**
     * บันทึกข้อมูลการแจ้งซ่อม
     *
     * ✅ แก้ไข: รองรับทั้ง 2 โหมด
     *   - โหมด Lock (มาจาก Facility): ใช้ facility_id จาก hidden input
     *   - โหมดปกติ: ใช้ facility_type (6 ประเภทคงที่) ไม่มี facility_id
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'facility_id'      => 'nullable|exists:facilities,id',
            'facility_type'    => 'nullable|in:bed,mattress,wardrobe,dressing_table,tv_stand,clothes_rack',
            'maintenance_type' => 'required',
            'description'      => 'required',
            'status'           => 'required|in:pending,in_progress,completed,cancelled',
            'priority'         => 'nullable|in:ทั่วไป,ด่วน,ด่วนมาก',
            'notes'            => 'nullable',
        ]);

        // ✅ ตัด facility_type ออก ไม่ส่งลง DB (เก็บแค่ facility_id)
        // facility_type ใช้เพื่อ display เท่านั้น ไม่มี column นี้ใน maintenances table
        $data = [
            'room_id'          => $validated['room_id'],
            'facility_id'      => $validated['facility_id'] ?? null,
            'maintenance_type' => $validated['maintenance_type'],
            'description'      => $validated['description'],
            'status'           => $validated['status'],
            'priority'         => $validated['priority'] ?? 'ทั่วไป',
            'notes'            => $validated['notes'] ?? null,
        ];

        Maintenance::create($data);

        // ✅ อัปเดต facility status เมื่อมา Lock mode
        if (! empty($validated['facility_id'])) {
            Facility::where('id', $validated['facility_id'])->update([
                'status' => 'needs_repair'
            ]);
        }

        return redirect()->route('maintenances.index')
            ->with('success', 'แจ้งซ่อมเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายละเอียดการแจ้งซ่อม
     */
    public function show(Maintenance $maintenance)
    {
        $maintenance->load(['room', 'facility']);
        return view('maintenances.show', compact('maintenance'));
    }

    /**
     * หน้าแก้ไขการแจ้งซ่อม
     */
    public function edit(Maintenance $maintenance)
    {
        $rooms      = Room::orderBy('floor')->orderBy('zone')->orderBy('room_number')->get();
        $facilities = Facility::all();
        return view('maintenances.edit', compact('maintenance', 'rooms', 'facilities'));
    }

    /**
     * อัปเดตข้อมูลการแจ้งซ่อม
     */
    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'facility_id'      => 'nullable|exists:facilities,id',
            'maintenance_type' => 'required',
            'description'      => 'nullable',
            'assigned_to'      => 'nullable',
            'cost'             => 'nullable|numeric',
            'status'           => 'required|in:pending,in_progress,completed,cancelled',
            'priority'         => 'nullable|in:ทั่วไป,ด่วน,ด่วนมาก',
            'notes'            => 'nullable',
        ]);

        $maintenance->update($validated);

        if ($request->status === 'completed' && $maintenance->facility_id) {
            Facility::where('id', $maintenance->facility_id)->update([
                'status'                => 'good',
                'last_maintenance_date' => now(),
            ]);
        }

        return redirect()->route('maintenances.index')
            ->with('success', 'อัปเดตข้อมูลเรียบร้อย');
    }

    /**
     * ลบรายการแจ้งซ่อม
     */
    public function destroy(Maintenance $maintenance)
    {
        if (! auth()->check() || ! auth()->user()->isManagerOrAdmin()) {
            abort(403, 'คุณไม่มีสิทธิ์ดำเนินการนี้');
        }

        $maintenance->delete();
        return redirect()->route('maintenances.index')
            ->with('success', 'ลบรายการเรียบร้อยแล้ว');
    }

    /**
     * ฟังก์ชันทางลัด: เริ่มงานซ่อม
     */
    public function startWork(Maintenance $maintenance)
    {
        $maintenance->update(['status' => 'in_progress']);

        if ($maintenance->facility_id) {
            Facility::where('id', $maintenance->facility_id)->update([
                'status' => 'maintenance'
            ]);
        }

        return back()->with('success', 'เริ่มดำเนินการซ่อมแล้ว');
    }

    /**
     * ฟังก์ชันทางลัด: เสร็จสิ้นงานซ่อม
     */
    public function completeWork(Maintenance $maintenance)
    {
        $maintenance->update(['status' => 'completed']);

        if ($maintenance->facility_id) {
            Facility::where('id', $maintenance->facility_id)->update([
                'status'                => 'good',
                'last_maintenance_date' => now(),
            ]);
        }

        return back()->with('success', 'ดำเนินการซ่อมเสร็จสิ้น');
    }

    /**
     * API: ดึง facilities ของห้องที่เลือก (สำหรับ dependent dropdown)
     */
    public function byRoom(int $roomId)
    {
        $facilities = Facility::where('room_id', $roomId)
            ->select('id', 'name', 'type', 'status')
            ->get();

        return response()->json($facilities);
    }
}
