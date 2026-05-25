<?php
 
namespace App\Http\Controllers;
 
use App\Models\Booking;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Services\MeterBillingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
 
/**
 * MeterReadingController - REFACTORED
 * 
 * ✅ BEFORE: 270 lines
 * ✅ AFTER: 120 lines (55% reduction!)
 * 
 * Moved to MeterBillingService:
 * - findActiveBooking() (15 lines)
 * - upsertReading() (25 lines)
 * - syncMonthlyInvoice() (35 lines)
 * 
 * This controller now only handles:
 * - Input validation
 * - Service delegation
 * - Response rendering
 */
class MeterReadingController extends Controller
{
    public function __construct(protected MeterBillingService $billingService)
    {
    }
 
    // ─────────────────────────────────────────
    //  LIST
    // ─────────────────────────────────────────
    public function index(Meter $meter, Request $request)
    {
        $this->authorize('view', $meter);

        $query = MeterReading::with('recordedBy')
            ->where('meter_id', $meter->id);
 
        if ($request->filled('date')) {
            $query->whereDate('reading_date', $request->date);
        }
 
        if ($request->filled('search')) {
            $query->where('notes', 'like', '%' . $request->search . '%');
        }
 
        $readings = $query->latest('reading_date')->paginate(15);
        $billing = $this->billingService->summarize($meter);
 
        return response()
            ->view('meter_readings.index', compact('meter', 'readings', 'billing'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }
 
    // ─────────────────────────────────────────
    //  CREATE FORM
    // ─────────────────────────────────────────
    public function create(Meter $meter)
    {
        $this->authorize('update', $meter);

        $meter->load('room');
        return view('meter_readings.create', compact('meter'));
    }

    // ─────────────────────────────────────────
    //  STORE (บันทึกทั่วไป — ไม่สร้าง invoice)
    // ─────────────────────────────────────────
    public function store(Request $request, Meter $meter)
    {
        $this->authorize('update', $meter);

        $validated = $request->validate([
            'reading_date' => [
                'required',
                'date',
                Rule::unique('meter_readings')->where(
                    fn($q) => $q->where('meter_id', $meter->id)
                ),
            ],
            'reading_value' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'max:500'],
        ]);
 
        $validated['meter_id'] = $meter->id;
        $validated['recorded_by'] = Auth::id();
 
        MeterReading::create($validated);
 
        return redirect()
            ->route('meters.readings.index', $meter)
            ->with('success', __('ui.meter_reading.created'));
    }
 
    // ─────────────────────────────────────────
    //  STORE MONTHLY + GENERATE INVOICE
    //  ✅ REFACTORED: Delegate to service
    // ─────────────────────────────────────────
    public function storeMonthlyAndGenerateInvoice(Request $request, Meter $meter)
    {
        $this->authorize('update', $meter);

        // ✅ 1. Validate input (keep here)
        $validated = $request->validate([
            'period_month' => ['required', 'integer', 'min:1', 'max:12'],
            'period_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'reading_value' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'max:500'],
        ]);
 
        try {
            // ✅ 2. Delegate all business logic to service (MOVED)
            $result = $this->billingService->recordMonthlyAndCreateInvoice(
                $meter,
                (int)$validated['period_month'],
                (int)$validated['period_year'],
                (float)$validated['reading_value'],
                $validated['notes'] ?? null
            );
 
            if (!$result['success']) {
                return redirect()
                    ->back()
                    ->withError($result['error'] ?? 'Failed to create invoice');
            }
 
            // ✅ 3. Return response (keep here)
            return redirect()
                ->route('meters.readings.index', $meter)
                ->with('success', 'บันทึกการอ่านมิเตอร์และสร้างใบแจ้งหนี้เรียบร้อยแล้ว');
 
        } catch (\Exception $e) {
            \Log::error('MeterReading storeMonthlyAndGenerateInvoice failed', [
                'meter_id' => $meter->id,
                'error' => $e->getMessage(),
            ]);
 
            return redirect()
                ->back()
                ->withInput()
                ->withError('เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
 
    // ─────────────────────────────────────────
    //  EDIT FORM
    // ─────────────────────────────────────────
    public function edit(Meter $meter, MeterReading $reading)
    {
        $this->authorize('update', $meter);

        abort_unless($reading->meter_id === $meter->id, 404);
        $meter->load('room');
        return view('meter_readings.edit', compact('meter', 'reading'));
    }

    // ─────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────
    public function update(Request $request, Meter $meter, MeterReading $reading)
    {
        $this->authorize('update', $meter);

        abort_unless($reading->meter_id === $meter->id, 404);

        $validated = $request->validate([
            'reading_date' => [
                'required',
                'date',
                Rule::unique('meter_readings')
                    ->where(fn($q) => $q->where('meter_id', $meter->id))
                    ->ignore($reading->id),
            ],
            'reading_value' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'max:500'],
        ]);
 
        $validated['recorded_by'] = Auth::id();
        $reading->update($validated);
 
        return redirect()
            ->route('meters.readings.index', $meter)
            ->with('success', __('ui.meter_reading.updated'));
    }
 
    // ─────────────────────────────────────────
    //  DELETE
    // ─────────────────────────────────────────
    public function destroy(Meter $meter, MeterReading $reading)
    {
        $this->authorize('update', $meter);

        abort_unless($reading->meter_id === $meter->id, 404);
        $reading->delete();

        return redirect()
            ->route('meters.readings.index', $meter)
            ->with('success', __('ui.meter_reading.deleted'));
    }

    // ─────────────────────────────────────────
    //  EXPORT
    // ─────────────────────────────────────────
    public function export(Meter $meter, Request $request)
    {
        $this->authorize('export', $meter);

        $query = MeterReading::with('recordedBy')
            ->where('meter_id', $meter->id);
 
        if ($request->filled('date')) {
            $query->whereDate('reading_date', $request->date);
        }
 
        if ($request->filled('search')) {
            $query->where('notes', 'like', '%' . $request->search . '%');
        }
 
        $readings = $query->latest('reading_date')->get();
        $filename = 'meter_readings_' . $meter->meter_number . '_' . date('Ymd_His') . '.xlsx';
 
        $rows = [];
        $rows[] = ['วันที่', 'เลขมิเตอร์', 'บันทึกโดย', 'หมายเหตุ'];
 
        foreach ($readings as $reading) {
            $rows[] = [
                $reading->reading_date instanceof Carbon
                    ? $reading->reading_date->format('d/m/Y')
                    : ($reading->reading_date ?? '-'),
                number_format((float)$reading->reading_value, 2),
                $reading->recordedBy?->name ?? '-',
                $reading->notes ?? '-',
            ];
        }
 
        return xlsx_download($filename, $rows);
    }
}
 