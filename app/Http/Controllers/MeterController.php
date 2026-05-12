<?php
 
namespace App\Http\Controllers;
 
use App\Models\Meter;
use App\Models\Room;
use App\Models\MeterReading;
use App\Services\MeterBillingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
 
class MeterController extends Controller
{
    public function __construct(protected MeterBillingService $billingService)
    {
    }
 
    // ─────────────────────────────────────────
    //  INDEX — จัดกลุ่มตามห้อง
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        // ดึง rooms ที่มีมิเตอร์ พร้อม eager load
        $query = Room::with([
            'meters.latestReading.recordedBy',
            'currentBooking.guest',
        ])
        ->whereHas('meters')
        ->orderBy('zone')
        ->orderBy('room_number');
 
        // Filter: ค้นหาห้อง / หมายเลขมิเตอร์
        if ($request->filled('search')) {
            $search = trim($request->string('search'));
            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhereHas('meters', fn ($m) =>
                      $m->where('meter_number', 'like', "%{$search}%")
                  )
                  ->orWhereHas('currentBooking.guest', fn ($g) =>
                      $g->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name',  'like', "%{$search}%")
                  );
            });
        }
 
        // Filter: ประเภทมิเตอร์
        if ($request->filled('type')) {
            $query->whereHas('meters', fn ($m) =>
                $m->where('type', $request->string('type'))
            );
        }
 
        // Filter: สถานะ
        if ($request->filled('status')) {
            $query->whereHas('meters', fn ($m) =>
                $m->where('is_active', $request->boolean('status'))
            );
        }
 
        $rooms = $query->paginate(12)->withQueryString();
 
        return view('meters.index', compact('rooms'));
    }
 
    // ─────────────────────────────────────────
    //  CREATE FORM
    // ─────────────────────────────────────────
    public function create()
    {
        $rooms = Room::orderBy('zone')->orderBy('room_number')->get();
        return view('meters.create', compact('rooms'));
    }
 
    // ─────────────────────────────────────────
    //  STORE
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'      => ['required', 'exists:rooms,id'],
            'type'         => [
                'required',
                Rule::in(['water', 'electric']),
                Rule::unique('meters')->where(fn ($q) => $q
                    ->where('room_id', $request->input('room_id'))
                    ->where('type',    $request->input('type'))),
            ],
            'meter_number' => ['required', 'max:100', 'unique:meters,meter_number'],
            'unit'         => ['nullable', 'max:20'],
            'installed_at' => ['nullable', 'date'],
            'rate_per_unit'=> ['required', 'numeric', 'min:0'],
            'tax_rate'     => ['required', 'numeric', 'min:0'],
            'is_active'    => ['nullable', 'boolean'],
            'notes'        => ['nullable', 'max:500'],
        ]);
 
        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
        $meter = Meter::create($validated);
        assert($meter instanceof Meter);
 
        return redirect()->route('meters.show', $meter)->with('success', __('ui.meter.created'));
    }
 
    // ─────────────────────────────────────────
    //  SHOW
    // ─────────────────────────────────────────
    public function show(Meter $meter)
    {
        $meter->load(['room', 'readings' => fn ($q) =>
            $q->latest('reading_date')->limit(10)->with('recordedBy')
        ]);
        $billing = $this->billingService->summarize($meter);
        return view('meters.show', compact('meter', 'billing'));
    }
 
    // ─────────────────────────────────────────
    //  EDIT FORM
    // ─────────────────────────────────────────
    public function edit(Meter $meter)
    {
        $rooms = Room::orderBy('zone')->orderBy('room_number')->get();
        return view('meters.edit', compact('meter', 'rooms'));
    }
 
    // ─────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────
    public function update(Request $request, Meter $meter)
    {
        $validated = $request->validate([
            'room_id'      => ['required', 'exists:rooms,id'],
            'type'         => [
                'required',
                Rule::in(['water', 'electric']),
                Rule::unique('meters')->where(fn ($q) => $q
                    ->where('room_id', $request->input('room_id'))
                    ->where('type',    $request->input('type'))
                    ->where('id', '!=', $meter->id)),
            ],
            'meter_number' => ['required', 'max:100',
                               Rule::unique('meters', 'meter_number')->ignore($meter->id)],
            'unit'         => ['nullable', 'max:20'],
            'installed_at' => ['nullable', 'date'],
            'rate_per_unit'=> ['required', 'numeric', 'min:0'],
            'tax_rate'     => ['required', 'numeric', 'min:0'],
            'is_active'    => ['nullable', 'boolean'],
            'notes'        => ['nullable', 'max:500'],
        ]);
 
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $meter->update($validated);
 
        return redirect()->route('meters.show', $meter)->with('success', __('ui.meter.updated'));
    }
 
    // ─────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────
    public function destroy(Meter $meter)
    {
        $meter->delete();
        return redirect()->route('meters.index')->with('success', __('ui.meter.deleted'));
    }
 
    // ─────────────────────────────────────────
    //  EXPORT
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $query = Meter::with('room');
 
        if ($request->filled('search')) {
            $query->where('meter_number', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }
 
        $meters   = $query->orderBy('id', 'desc')->get();
        $filename = 'meters_' . date('Ymd_His') . '.xlsx';
 
        $rows   = [];
        $rows[] = ['ห้องพัก', 'ประเภท', 'หมายเลขมิเตอร์', 'หน่วย',
                   'อัตรา/หน่วย', 'ภาษี (%)', 'สถานะ', 'วันติดตั้ง', 'หมายเหตุ'];
 
        foreach ($meters as $meter) {
            $rows[] = [
                $meter->room->room_number ?? '-',
                $meter->type === 'water' ? 'น้ำ' : 'ไฟฟ้า',
                $meter->meter_number,
                $meter->unit ?? '-',
                number_format($meter->rate_per_unit ?? 0, 2),
                number_format($meter->tax_rate ?? 0, 2),
                $meter->is_active ? 'ใช้งาน' : 'ปิดใช้',
                $meter->installed_at ? $meter->installed_at->format('d/m/Y') : '-',
                $meter->notes ?? '-',
            ];
        }
 
        return xlsx_download($filename, $rows);
    }
}
 