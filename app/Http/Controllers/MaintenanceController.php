<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $pendingCount = Maintenance::where('status', 'pending')->count();
        $inProgressCount = Maintenance::where('status', 'in_progress')->count();
        $completedThisMonth = Maintenance::where('status', 'completed')
            ->whereMonth('completion_date', Carbon::now()->month)
            ->whereYear('completion_date', Carbon::now()->year)
            ->count();

        $pending = Maintenance::with('room')
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderByRaw("FIELD(status, 'pending', 'in_progress')")
            ->orderByDesc('request_date')
            ->take(5)
            ->get();

        $recentlyCompleted = Maintenance::with('room')
            ->where('status', 'completed')
            ->orderByDesc('completion_date')
            ->take(5)
            ->get();

        $query = Maintenance::with('room');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('maintenance_type', 'like', "%{$search}%")
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', "%{$search}%"));
            });
        }

        $maintenances = $query->orderByDesc('id')->paginate(10);

        return view('maintenances.index', compact(
            'maintenances', 'pendingCount', 'inProgressCount', 
            'completedThisMonth', 'pending', 'recentlyCompleted'
        ));
    }

    public function create()
    {
        $rooms = Room::all();
        return view('maintenances.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'maintenance_type' => 'required',
            'description'      => 'required',
            'request_date'     => 'required|date',
            'status'           => 'required',
            'notes'            => 'nullable',
        ]);

        Maintenance::create($validated);
        return redirect()->route('maintenances.index')->with('success', 'แจ้งซ่อมเรียบร้อยแล้ว');
    }

    public function show(Maintenance $maintenance)
    {
        $maintenance->load('room');
        return view('maintenances.show', compact('maintenance'));
    }

    public function edit(Maintenance $maintenance)
    {
        $rooms = Room::all();
        return view('maintenances.edit', compact('maintenance', 'rooms'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'maintenance_type' => 'required',
            'description' => 'nullable',
            'assigned_to' => 'nullable',
            'cost' => 'nullable|numeric',
            'request_date' => 'required|date',
            'status' => 'required',
            'notes' => 'nullable',
            'completion_date' => 'nullable|date',
        ]);

        if ($request->status == 'completed' && !$maintenance->completion_date) {
            $validated['completion_date'] = now();
        }

        $maintenance->update($validated);
        return redirect()->route('maintenances.index')->with('success', 'อัปเดตเรียบร้อย');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return redirect()->route('maintenances.index')->with('success', 'ลบรายการเรียบร้อยแล้ว');
    }

    /**
     * ฟังก์ชันเริ่มงาน (ปุ่มในรูปที่ Error)
     */
    public function startWork(Maintenance $maintenance)
    {
        $maintenance->update([
            'status' => 'in_progress'
        ]);
        return back()->with('success', 'เริ่มดำเนินการซ่อมแล้ว');
    }

    /**
     * ฟังก์ชันเสร็จสิ้นงาน
     */
    public function completeWork(Maintenance $maintenance)
    {
        $maintenance->update([
            'status' => 'completed',
            'completion_date' => now()
        ]);
        return back()->with('success', 'ดำเนินการซ่อมเสร็จสิ้น');
    }
}