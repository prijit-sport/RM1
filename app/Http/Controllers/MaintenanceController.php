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

    public function export(Request $request)
    {
        $maintenances = Maintenance::with('room')->orderBy('id', 'desc')->get();
        $filename = 'maintenances_export_' . date('Y-m-d') . '.csv';

        return response()->stream(function () use ($maintenances) {
            $rows = [];
            $rows[] = ['Room', 'Issue Type', 'Description', 'Reported Date', 'Completed Date', 'Status', 'Assigned To', 'Cost', 'Notes'];

            foreach ($maintenances as $maintenance) {
                $rows[] = [
                    $maintenance->room->room_number ?? '-',
                    $maintenance->issue_type,
                    $maintenance->description ?? '-',
                    optional($maintenance->reported_date)->format('d/m/Y'),
                    optional($maintenance->completed_date)->format('d/m/Y'),
                    $maintenance->status,
                    $maintenance->assigned_to ?? '-',
                    $maintenance->cost ?? '-',
                    $maintenance->notes ?? '-',
                ];
            }

            $handle = fopen('php://output', 'wb');
            fwrite($handle, chr(0xFF) . chr(0xFE));
            foreach ($rows as $row) {
                $line = '"' . implode('","', array_map(function ($value) {
                    return str_replace('"', '""', (string) $value);
                }, $row)) . '"' . "\r\n";
                fwrite($handle, mb_convert_encoding($line, 'UTF-16LE', 'UTF-8'));
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-16LE',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function startWork($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update(['status' => 'in_progress']);
        return redirect()->route('maintenances.show', $maintenance)->with('success', 'เริ่มงานซ่อมแล้ว');
    }

    public function completeWork($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->update([
            'status' => 'completed',
            'completed_date' => now()->toDateString(),
        ]);

        return redirect()->route('maintenances.show', $maintenance)->with('success', 'ปิดงานซ่อมสำเร็จ');
    }
}
