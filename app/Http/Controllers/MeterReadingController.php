<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MeterReadingController extends Controller
{
    public function index(Meter $meter)
    {
        $meter->load('room');
        $readings = MeterReading::with('recordedBy')
            ->where('meter_id', $meter->id)
            ->latest('reading_date')
            ->paginate(15);

        return view('meter_readings.index', compact('meter', 'readings'));
    }

    public function create(Meter $meter)
    {
        $meter->load('room');
        return view('meter_readings.create', compact('meter'));
    }

    public function store(Request $request, Meter $meter)
    {
        $validated = $request->validate([
            'reading_date' => [
                'required',
                'date',
                Rule::unique('meter_readings')->where(fn ($q) => $q->where('meter_id', $meter->id)),
            ],
            'reading_value' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'max:500'],
        ]);

        $validated['meter_id'] = $meter->id;
        $validated['recorded_by'] = auth()->id();

        MeterReading::create($validated);

        return redirect()->route('meters.readings.index', $meter)->with('success', 'บันทึกเลขมิเตอร์สำเร็จ');
    }

    public function edit(Meter $meter, MeterReading $reading)
    {
        abort_unless($reading->meter_id === $meter->id, 404);
        $meter->load('room');
        return view('meter_readings.edit', compact('meter', 'reading'));
    }

    public function update(Request $request, Meter $meter, MeterReading $reading)
    {
        abort_unless($reading->meter_id === $meter->id, 404);

        $validated = $request->validate([
            'reading_date' => [
                'required',
                'date',
                Rule::unique('meter_readings')
                    ->where(fn ($q) => $q->where('meter_id', $meter->id))
                    ->ignore($reading->id),
            ],
            'reading_value' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'max:500'],
        ]);

        $validated['recorded_by'] = auth()->id();

        $reading->update($validated);

        return redirect()->route('meters.readings.index', $meter)->with('success', 'แก้ไขเลขมิเตอร์สำเร็จ');
    }

    public function destroy(Meter $meter, MeterReading $reading)
    {
        abort_unless($reading->meter_id === $meter->id, 404);
        $reading->delete();
        return redirect()->route('meters.readings.index', $meter)->with('success', 'ลบรายการเลขมิเตอร์สำเร็จ');
    }
}

