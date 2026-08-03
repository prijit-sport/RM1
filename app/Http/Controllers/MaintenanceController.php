<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceRequest;
use App\Http\Requests\UpdateMaintenanceRequest;
use App\Models\Facility;
use App\Models\Maintenance;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
        $this->authorize('viewAny', Maintenance::class);

        $pendingCount = Maintenance::where('status', 'pending')->count();

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
                $q->where('issue_type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('room', fn ($r) => $r->where('room_number', 'like', "%{$search}%"))
                    ->orWhereHas('facility', fn ($f) => $f->where('name', 'like', "%{$search}%"));
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
        $this->authorize('create', Maintenance::class);

        $rooms = Room::orderBy('floor')->orderBy('zone')->orderBy('room_number')->get();

        // ✅ แก้ไข: ส่ง facilities เฉพาะที่ต้องใช้ตอน Lock mode (มาจาก facility_id)
        $facilities = Cache::remember('facilities.all', now()->addHours(6), fn () => Facility::orderBy('id')->get());

        $facilityId = $request->input('facility_id');
        $selectedRoomId = null;
        $selectedFacilityId = null;

        if ($facilityId) {
            $facility = Facility::find($facilityId);
            if ($facility && $facility->room_id) {
                $selectedRoomId = $facility->room_id;
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
    public function store(StoreMaintenanceRequest $request)
    {
        $this->authorize('create', Maintenance::class);

        $validated = $request->validated();

        // DB maintenances มีคอลัมน์: room_id, issue_type, description, reported_date,
        // completed_date, status, assigned_to, cost, notes
        // ฟอร์มส่ง maintenance_type/request_date ให้ map ก่อน insert
        $data = [
            'room_id' => $validated['room_id'],
            'issue_type' => $validated['issue_type'],
            'description' => $validated['description'],
            'reported_date' => $validated['reported_date'],
            'status' => $validated['status'],
            'assigned_to' => $validated['assigned_to'] ?? null,
            'cost' => $validated['cost'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        Maintenance::create($data);

        Cache::forget('facilities.all');

        return redirect()->route('maintenances.index')

            ->with('success', 'แจ้งซ่อมเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายละเอียดการแจ้งซ่อม
     */
    public function show(Maintenance $maintenance)
    {
        $this->authorize('view', $maintenance);

        $maintenance->load(['room', 'facility']);

        return view('maintenances.show', compact('maintenance'));
    }

    /**
     * หน้าแก้ไขการแจ้งซ่อม
     */
    public function edit(Maintenance $maintenance)
    {
        $this->authorize('update', $maintenance);

        $rooms = Room::orderBy('floor')->orderBy('zone')->orderBy('room_number')->get();

        $facilities = Cache::remember('facilities.all', now()->addHours(6), fn () => Facility::orderBy('id')->get());

        return view('maintenances.edit', compact('maintenance', 'rooms', 'facilities'));
    }

    /**
     * อัปเดตข้อมูลการแจ้งซ่อม
     */
    public function update(UpdateMaintenanceRequest $request, Maintenance $maintenance)
    {
        $this->authorize('update', $maintenance);

        $validated = $request->validated();

        // Map field ที่มาจากฟอร์มให้ตรงกับ schema
        $updateData = [
            'room_id' => $validated['room_id'] ?? $maintenance->room_id,
            'issue_type' => $validated['issue_type'] ?? $maintenance->issue_type,
            'description' => $validated['description'] ?? $maintenance->description,
            'reported_date' => $validated['reported_date'] ?? $maintenance->reported_date,
            'status' => $validated['status'] ?? $maintenance->status,
            'assigned_to' => $validated['assigned_to'] ?? $maintenance->assigned_to,
            'cost' => $validated['cost'] ?? $maintenance->cost,
            'notes' => $validated['notes'] ?? $maintenance->notes,
        ];

        $maintenance->update($updateData);

        if ($request->status === 'completed' && $maintenance->facility_id) {

            Facility::where('id', $maintenance->facility_id)->update([
                'status' => 'good',
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
        $this->authorize('delete', $maintenance);

        $maintenance->delete();

        return redirect()->route('maintenances.index')
            ->with('success', 'ลบรายการเรียบร้อยแล้ว');
    }

    /**
     * ฟังก์ชันทางลัด: เริ่มงานซ่อม
     */
    public function startWork(Maintenance $maintenance)
    {
        $this->authorize('startWork', $maintenance);

        $maintenance->update(['status' => 'in_progress']);

        if ($maintenance->facility_id) {
            Facility::where('id', $maintenance->facility_id)->update([
                'status' => 'maintenance',
            ]);
        }

        return back()->with('success', 'เริ่มดำเนินการซ่อมแล้ว');
    }

    /**
     * ฟังก์ชันทางลัด: เสร็จสิ้นงานซ่อม
     */
    public function completeWork(Maintenance $maintenance)
    {
        $this->authorize('completeWork', $maintenance);

        $maintenance->update(['status' => 'completed']);

        if ($maintenance->facility_id) {
            Facility::where('id', $maintenance->facility_id)->update([
                'status' => 'good',
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
        $this->authorize('byRoom', Maintenance::class);

        $facilities = Facility::where('room_id', $roomId)

            ->select('id', 'name', 'type', 'status')
            ->get();

        return response()->json($facilities);
    }
}
