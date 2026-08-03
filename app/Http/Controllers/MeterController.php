<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\Room;
use App\Services\MeterBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * หมายเหตุ: โค้ดนี้เป็น Laravel (access model fields เป็น property)
 * ปัญหา "Call to unknown function: zone/room_number/type/..." ที่ VSCode พบ
 * มักเป็น false-positive จาก PHP Language Server/IDE helper ไม่เกี่ยวกับ runtime
 */
class MeterController extends Controller
{
    public function __construct(protected MeterBillingService $billingService) {}

    // ─────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Meter::class);

        $query = Room::with([
            'meters.latestReading.recordedBy',
            'currentBooking.guest',
        ])
            ->whereHas('meters')
            ->orderBy('zone')
            ->orderBy('room_number');

        if ($request->filled('search')) {
            $search = trim($request->string('search'));
            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                    ->orWhereHas(
                        'meters',
                        fn ($m) => $m->where('meter_number', 'like', "%{$search}%")
                    )
                    ->orWhereHas(
                        'currentBooking.guest',
                        fn ($g) => $g->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                    );
            });
        }

        if ($request->filled('type')) {
            $query->whereHas(
                'meters',
                fn ($m) => $m->where('type', $request->string('type'))
            );
        }

        if ($request->filled('status')) {
            $query->whereHas(
                'meters',
                fn ($m) => $m->where('is_active', $request->boolean('status'))
            );
        }

        $rooms = $query->paginate(12)->withQueryString();

        return view('meters.index', compact('rooms'));
    }

    // ─────────────────────────────────────────
    //  CREATE FORM
    // ─────────────────────────────────────────
    public function create(): View
    {
        $this->authorize('create', Meter::class);

        $rooms = Room::orderBy('zone')->orderBy('room_number')->get();

        return view('meters.create', compact('rooms'));
    }

    // ─────────────────────────────────────────
    //  STORE
    // ─────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Meter::class);

        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'type' => [
                'required',
                Rule::in(['water', 'electric']),
                Rule::unique('meters')->where(fn ($q) => $q
                    ->where('room_id', $request->input('room_id'))
                    ->where('type', $request->input('type'))),
            ],
            'meter_number' => ['required', 'max:100', 'unique:meters,meter_number'],
            'unit' => ['nullable', 'max:20'],
            'installed_at' => ['nullable', 'date'],
            'rate_per_unit' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'max:500'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        /** @var Meter $meter */
        $meter = Meter::create($validated);

        return redirect()->route('meters.show', $meter)->with('success', __('ui.meter.created'));
    }

    // ─────────────────────────────────────────
    //  SHOW
    // ─────────────────────────────────────────
    public function show(Meter $meter): View
    {
        $this->authorize('view', $meter);

        $meter->load([
            'room',
            'readings' => fn ($q) => $q->latest('reading_date')->limit(10)->with('recordedBy'),
        ]);
        $billing = $this->billingService->summarize($meter);

        return view('meters.show', compact('meter', 'billing'));
    }

    // ─────────────────────────────────────────
    //  EDIT FORM
    // ─────────────────────────────────────────
    public function edit(Meter $meter): View
    {
        $this->authorize('update', $meter);

        $rooms = Room::orderBy('zone')->orderBy('room_number')->get();

        return view('meters.edit', compact('meter', 'rooms'));
    }

    // ─────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────
    public function update(Request $request, Meter $meter): RedirectResponse
    {
        $this->authorize('update', $meter);

        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'type' => [
                'required',
                Rule::in(['water', 'electric']),
                Rule::unique('meters')->where(fn ($q) => $q
                    ->where('room_id', $request->input('room_id'))
                    ->where('type', $request->input('type'))
                    ->where('id', '!=', $meter->id)),
            ],
            'meter_number' => [
                'required',
                'max:100',
                Rule::unique('meters', 'meter_number')->ignore($meter->id),
            ],
            'unit' => ['nullable', 'max:20'],
            'installed_at' => ['nullable', 'date'],
            'rate_per_unit' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'max:500'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $meter->update($validated);

        return redirect()->route('meters.show', $meter)->with('success', __('ui.meter.updated'));
    }

    // ─────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────
    public function destroy(Meter $meter): RedirectResponse
    {
        $this->authorize('delete', $meter);

        $meter->delete();

        return redirect()->route('meters.index')->with('success', __('ui.meter.deleted'));
    }

    // ─────────────────────────────────────────
    //  EXPORT
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $this->authorize('export', Meter::class);

        $query = Meter::with('room');

        if ($request->filled('search')) {
            $query->where('meter_number', 'like', '%'.$request->string('search').'%');
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        $meters = $query->orderBy('id', 'desc')->get();
        $filename = 'meters_'.date('Ymd_His').'.xlsx';

        $rows = [];
        $rows[] = [
            'ห้องพัก',
            'ประเภท',
            'หมายเลขมิเตอร์',
            'หน่วย',
            'อัตรา/หน่วย',
            'ภาษี (%)',
            'สถานะ',
            'วันติดตั้ง',
            'หมายเหตุ',
        ];

        foreach ($meters as $meter) {
            /** @var Meter $meter */
            $rows[] = [
                $meter->room->room_number ?? '-',
                $meter->type === 'water' ? 'น้ำ' : 'ไฟฟ้า',
                $meter->meter_number,
                $meter->unit ?? '-',
                number_format((float) ($meter->rate_per_unit ?? 0), 2),
                number_format((float) ($meter->tax_rate ?? 0), 2),
                $meter->is_active ? 'ใช้งาน' : 'ปิดใช้',
                $meter->installed_at ? $meter->installed_at->format('d/m/Y') : '-',
                $meter->notes ?? '-',
            ];
        }

        return xlsx_download($filename, $rows);
    }
}
