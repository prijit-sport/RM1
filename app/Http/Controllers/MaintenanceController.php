<?php
namespace App\Http\Controllers;
use App\Models\Maintenance;
use App\Models\Room;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::with('room')->paginate(10);
        return view('maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        $rooms = Room::all();
        return view('maintenances.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'issue_type' => 'required|max:50',
            'description' => 'nullable|max:500',
            'reported_date' => 'required|date',
            'completed_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|max:100',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|max:500',
        ]);
        Maintenance::create($validated);
        return redirect()->route('maintenances.index')->with('success', 'ซ่อมบำรุงเพิ่มสำเร็จ');
    }

    public function show(Maintenance $maintenance)
    {
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
            'issue_type' => 'required|max:50',
            'description' => 'nullable|max:500',
            'reported_date' => 'required|date',
            'completed_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'nullable|max:100',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|max:500',
        ]);
        $maintenance->update($validated);
        return redirect()->route('maintenances.show', $maintenance)->with('success', 'ซ่อมบำรุงอัปเดตสำเร็จ');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return redirect()->route('maintenances.index')->with('success', 'ซ่อมบำรุงลบสำเร็จ');
    }
}
