<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::paginate(10);
        return view('facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('facilities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'type' => 'required|max:50',
            'location' => 'required|max:100',
            'description' => 'nullable|max:500',
            'status' => 'required|in:active,inactive,maintenance',
            'maintenance_schedule' => 'nullable|max:100',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date',
        ]);

        Facility::create($validated);
        return redirect()->route('facilities.index')->with('success', 'Facility เพิ่มสำเร็จ');
    }

    public function show(Facility $facility)
    {
        return view('facilities.show', compact('facility'));
    }

    public function edit(Facility $facility)
    {
        return view('facilities.edit', compact('facility'));
    }

    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'type' => 'required|max:50',
            'location' => 'required|max:100',
            'description' => 'nullable|max:500',
            'status' => 'required|in:active,inactive,maintenance',
            'maintenance_schedule' => 'nullable|max:100',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date',
        ]);

        $facility->update($validated);
        return redirect()->route('facilities.show', $facility)->with('success', 'Facility อัปเดตสำเร็จ');
    }

    public function destroy(Facility $facility)
    {
        $facility->delete();
        return redirect()->route('facilities.index')->with('success', 'Facility ลบสำเร็จ');
    }

    public function export(Request $request)
    {
        $facilities = Facility::orderBy('id', 'desc')->get();
        $filename = 'facilities_export_' . date('Y-m-d') . '.csv';

        return response()->stream(function () use ($facilities) {
            $rows = [];
            $rows[] = ['Name', 'Type', 'Location', 'Description', 'Status', 'Maintenance Schedule', 'Last Maintenance Date', 'Next Maintenance Date'];

            foreach ($facilities as $facility) {
                $rows[] = [
                    $facility->name,
                    $facility->type,
                    $facility->location,
                    $facility->description ?? '-',
                    $facility->status,
                    $facility->maintenance_schedule ?? '-',
                    optional($facility->last_maintenance_date)->format('d/m/Y'),
                    optional($facility->next_maintenance_date)->format('d/m/Y'),
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
}
