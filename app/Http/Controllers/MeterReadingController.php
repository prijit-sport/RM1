<?php
 
namespace App\Http\Controllers;
 
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Services\MeterBillingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
 
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
        $meter->load('room');
 
        $query = MeterReading::with('recordedBy')
            ->where('meter_id', $meter->id);
 
        if ($request->filled('date')) {
            $query->whereDate('reading_date', $request->date);
        }
 
        if ($request->filled('search')) {
            $query->where('notes', 'like', '%' . $request->search . '%');
        }
 
        $readings = $query->latest('reading_date')->paginate(15);
        $billing  = $this->billingService->summarize($meter);
 
        return response()
            ->view('meter_readings.index', compact('meter', 'readings', 'billing'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }
 
    // ─────────────────────────────────────────
    //  CREATE FORM
    // ─────────────────────────────────────────
    public function create(Meter $meter)
    {
        $meter->load('room');
        return view('meter_readings.create', compact('meter'));
    }
 
    // ─────────────────────────────────────────
    //  STORE (บันทึกทั่วไป — ไม่สร้าง invoice)
    // ─────────────────────────────────────────
    public function store(Request $request, Meter $meter)
    {
        $validated = $request->validate([
            'reading_date' => [
                'required',
                'date',
                Rule::unique('meter_readings')->where(
                    fn ($q) => $q->where('meter_id', $meter->id)
                ),
            ],
            'reading_value' => ['required', 'numeric', 'min:0'],
            'notes'         => ['nullable', 'max:500'],
        ]);
 
        $validated['meter_id']    = $meter->id;
        $validated['recorded_by'] = Auth::id();
 
        MeterReading::create($validated);
 
        return redirect()
            ->route('meters.readings.index', $meter)
            ->with('success', __('ui.meter_reading.created'));
    }
 
    // ─────────────────────────────────────────
    //  STORE MONTHLY + GENERATE INVOICE
    //  บันทึกรายเดือน + สร้าง/อัปเดต invoice รวมไฟ+น้ำ
    // ─────────────────────────────────────────
    public function storeMonthlyAndGenerateInvoice(Request $request, Meter $meter)
    {
        $validated = $request->validate([
            'period_month'  => ['required', 'integer', 'min:1', 'max:12'],
            'period_year'   => ['required', 'integer', 'min:2000', 'max:2100'],
            'reading_value' => ['required', 'numeric', 'min:0'],
            'notes'         => ['nullable', 'max:500'],
        ]);
 
        $periodStart = Carbon::create($validated['period_year'], $validated['period_month'], 1)->startOfDay();
        $periodEnd   = $periodStart->copy()->endOfMonth();
 
        $booking = $this->findActiveBooking($meter, $periodStart, $periodEnd);
 
        $this->upsertReading(
            $meter,
            $booking,
            $validated['period_month'],
            $validated['period_year'],
            $periodEnd,
            (float) $validated['reading_value'],
            $validated['notes'] ?? null
        );
 
        $totals     = $this->billingService->calculateMonthlyTotals(
            $booking,
            $validated['period_month'],
            $validated['period_year']
        );
        $grandTotal = round($totals['electric'] + $totals['water'], 2);
 
        $this->syncMonthlyInvoice(
            $booking,
            $validated['period_month'],
            $validated['period_year'],
            $periodStart,
            $grandTotal
        );
 
        return redirect()
            ->route('meters.readings.index', $meter)
            ->with('success', 'บันทึกการอ่านมิเตอร์และสร้างใบแจ้งหนี้เรียบร้อยแล้ว');
    }
 
    // ─────────────────────────────────────────
    //  EDIT FORM
    // ─────────────────────────────────────────
    public function edit(Meter $meter, MeterReading $reading)
    {
        abort_unless($reading->meter_id === $meter->id, 404);
        $meter->load('room');
        return view('meter_readings.edit', compact('meter', 'reading'));
    }
 
    // ─────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────
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
            'notes'         => ['nullable', 'max:500'],
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
 
        $rows   = [];
        $rows[] = ['วันที่', 'เลขมิเตอร์', 'บันทึกโดย', 'หมายเหตุ'];
 
        foreach ($readings as $reading) {
            $rows[] = [
                $reading->reading_date instanceof Carbon
                    ? $reading->reading_date->format('d/m/Y')
                    : ($reading->reading_date ?? '-'),
                number_format((float) $reading->reading_value, 2),
                $reading->recordedBy?->name ?? '-',
                $reading->notes ?? '-',
            ];
        }
 
        return xlsx_download($filename, $rows);
    }
 
    // ═════════════════════════════════════════
    //  PRIVATE HELPERS
    // ═════════════════════════════════════════
 
    private function findActiveBooking(Meter $meter, Carbon $periodStart, Carbon $periodEnd): Booking
    {
        $model = Booking::query()
            ->where('room_id', $meter->room_id)
            ->where('status', 'confirmed')
            ->whereDate('check_in_date', '<=', $periodEnd)
            ->where(function ($q) use ($periodStart) {
                $q->whereNull('check_out_date')
                  ->orWhereDate('check_out_date', '>=', $periodStart);
            })
            ->orderByDesc('id')
            ->firstOrFail();
 
        assert($model instanceof Booking);
        return $model;
    }
 
    private function upsertReading(
        Meter   $meter,
        Booking $booking,
        int     $month,
        int     $year,
        Carbon  $readingDate,
        float   $readingValue,
        ?string $notes
    ): MeterReading {
        $model = MeterReading::firstOrNew([
            'meter_id'     => $meter->id,
            'booking_id'   => $booking->id,
            'period_month' => $month,
            'period_year'  => $year,
        ]);
 
        assert($model instanceof MeterReading);
 
        $model->reading_date = $readingDate;

        $model->reading_value = $readingValue;
        $model->recorded_by   = Auth::id();
        $model->notes         = $notes;
        $model->save();
 
        return $model;
    }
 
    private function syncMonthlyInvoice(
        Booking $booking,
        int     $month,
        int     $year,
        Carbon  $periodStart,
        float   $grandTotal
    ): Invoice {
        $existing = Invoice::query()
            ->where('booking_id', $booking->id)
            ->where('room_id', $booking->room_id)
            ->where('guest_id', $booking->guest_id)
            ->whereMonth('issue_date', $month)
            ->whereYear('issue_date', $year)
            ->first();
 
        if ($existing instanceof Invoice) {
            $existing->update([
                'amount' => $grandTotal,
                'tax'    => 0,
                'total'  => $grandTotal,
            ]);
            return $existing;
        }
 
        $dueDate = $periodStart->copy()->addDays(15);
 
        $invoice = Invoice::create([
            'booking_id'     => $booking->id,
            'guest_id'       => $booking->guest_id,
            'room_id'        => $booking->room_id,
            'invoice_number' => $this->billingService->generateInvoiceNumber(
                                    (int) $booking->id,
                                    $month,
                                    $year
                                ),
            'amount'         => $grandTotal,
            'tax'            => 0,
            'total'          => $grandTotal,
            'issue_date'     => $periodStart->toDateString(),
            'due_date'       => $dueDate->toDateString(),
            'status'         => 'sent',
            'notes'          => 'ค่าน้ำ/ไฟ ประจำเดือน ' . $month . '/' . $year,
        ]);
 
        assert($invoice instanceof Invoice);
        return $invoice;
    }
}
