<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Room;
use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * หน้าแรก: แสดงรายการแจ้งซ่อมและสรุปสถิติ
     */
    public function index(Request $request)
    {
        $pendingCount = Maintenance::where('status', 'pending')->count();
        $inProgressCount = Maintenance::where('status', 'in_progress')->count();
        
        // ใช้ updated_at แทน completion_date
        $completedThisMonth = Maintenance::where('status', 'completed')
            ->whereMonth('updated_at', Carbon::now()->month)
            ->whereYear('updated_at', Carbon::now()->year)
            ->count();

        // ดึงรายการรอซ่อม 5 รายการล่าสุด
        $pending = Maintenance::with(['room', 'facility'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'in_progress' THEN 2 ELSE 3 END")
            ->orderByDesc('created_at') // ✅ เปลี่ยนจาก request_date เป็น created_at
            ->take(5)
            ->get();


        // ดึงรายการที่ซ่อมเสร็จแล้ว 5 รายการล่าสุด
        $recentlyCompleted = Maintenance::with(['room', 'facility'])
            ->where('status', 'completed')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        // ระบบค้นหา
        $query = Maintenance::with(['room', 'facility']);
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('maintenance_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', "%{$search}%"))
                  ->orWhereHas('facility', fn($f) => $f->where('name', 'like', "%{$search}%"));
            });
        }

        $maintenances = $query->orderByDesc('id')->paginate(10);

        return view('maintenances.index', compact(
            'maintenances', 'pendingCount', 'inProgressCount', 
            'completedThisMonth', 'pending', 'recentlyCompleted'
        ));
    }

    /**
     * หน้าฟอร์มแจ้งซ่อมใหม่
     */
    public function create(Request $request)
    {
        $rooms = Room::all();
        $facilities = Facility::all(); 
        
        $facility = null;
        if ($request->has('facility_id')) {
            $facility = Facility::find($request->facility_id);
        }

        return view('maintenances.create', compact('rooms', 'facility', 'facilities'));
    }

    /**
     * บันทึกข้อมูลการแจ้งซ่อม
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'facility_id'      => 'nullable|exists:facilities,id',
            'maintenance_type' => 'required',
            'description'      => 'required',
            // ✅ ลบ 'request_date' ออกจากการ Validate
            'status'           => 'required',
            'notes'            => 'nullable',
        ]);

        $maintenance = Maintenance::create($validated);

        if ($request->filled('facility_id')) {
            Facility::where('id', $request->facility_id)->update([
                'status' => 'needs_repair'
            ]);
        }

        return redirect()->route('maintenances.index')->with('success', 'แจ้งซ่อมเรียบร้อยแล้ว');
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
        $rooms = Room::all();
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
            // ✅ ลบ 'request_date' ออกจากการ Validate
            'status'           => 'required',
            'notes'            => 'nullable',
        ]);

        $maintenance->update($validated);

        if ($request->status == 'completed' && $maintenance->facility_id) {
            Facility::where('id', $maintenance->facility_id)->update([
                'status' => 'good',
                'last_maintenance_date' => now()
            ]);
        }

        return redirect()->route('maintenances.index')->with('success', 'อัปเดตข้อมูลเรียบร้อย');
    }

    /**
     * ลบรายการแจ้งซ่อม
     */
    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return redirect()->route('maintenances.index')->with('success', 'ลบรายการเรียบร้อยแล้ว');
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
        $maintenance->update([
            'status' => 'completed'
        ]);

        if ($maintenance->facility_id) {
            Facility::where('id', $maintenance->facility_id)->update([
                'status' => 'good',
                'last_maintenance_date' => now()
            ]);
        }

        return back()->with('success', 'ดำเนินการซ่อมเสร็จสิ้น');
    }
}