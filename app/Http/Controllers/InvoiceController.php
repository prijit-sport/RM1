<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Services\InvoiceService;
use App\Services\MeterBillingService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
 
class InvoiceController extends Controller
{
    private const STATUSES = ['draft', 'sent', 'paid', 'overdue', 'cancelled'];
 
    public function __construct(
        protected readonly InvoiceService $invoiceService,
        protected readonly MeterBillingService $billingService,
    ) {}
 
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Invoice::class);
 
        /** @var Builder<Invoice> $query */
        $query = Invoice::query()->with(['booking.room', 'booking.guest']);
 
        $invoiceType = $request->input('invoice_type', '');
        if ($invoiceType && in_array($invoiceType, ['rent', 'utility'], true)) {
            $query->where('invoice_type', $invoiceType);
        }
 
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function (Builder $q) use ($search): void {
                $q->where('invoice_number', 'like', '%' . $search . '%')
                    ->orWhere('booking_id', $search);
            });
        }
 
        $status = (string) $request->input('status', '');
        if ($request->filled('status') && in_array($status, self::STATUSES, true)) {
            $query->where('status', $status);
        }
 
        /** @var LengthAwarePaginator $invoices */
        $invoices = $query->orderByDesc('id')->paginate(10)->withQueryString();
        $stats    = $this->invoiceService->getStats($invoiceType ?: null);
 
        return view('invoices.index', compact('invoices', 'stats'));
    }
 
    public function create(Request $request): View
    {
        $this->authorize('create', Invoice::class);
 
        /** @var Collection<int, Booking> $bookings */
        $bookings      = Booking::with(['room', 'guest'])->orderByDesc('id')->get();
        $invoiceNumber = $this->invoiceService->generateInvoiceNumber();
 
        if (!$request->boolean('from_meter') || !$request->filled('invoice_id')) {
            return view('invoices.create', compact('bookings', 'invoiceNumber'));
        }
 
        /** @var Invoice|null $draftInvoice */
        $draftInvoice = Invoice::with(['booking.room', 'booking.guest'])
            ->find((int) $request->input('invoice_id'));
 
        if (!$draftInvoice) {
            return view('invoices.create', compact('bookings', 'invoiceNumber'))
                ->with('warning', 'ไม่พบใบแจ้งหนี้ที่บันทึกจากมิเตอร์ — กรุณากรอกข้อมูลด้วยตนเอง');
        }
 
        $meterData = $this->buildMeterDataForInvoice($draftInvoice);
 
        if (empty($draftInvoice->invoice_number)) {
            $draftInvoice->invoice_number = $invoiceNumber;
        }
 
        return view('invoices.create', compact('bookings', 'invoiceNumber', 'draftInvoice', 'meterData'));
    }
 
    /**
     * @return array<string, mixed>
     */
    private function buildMeterDataForInvoice(Invoice $invoice): array
    {
        $booking = $invoice->booking;
        if (!$booking) return [];
 
        /** @var Collection<int, Meter> $meters */
        $meters = Meter::where('room_id', (int) $booking->room_id)
            ->with(['readings' => function ($q): void {
                $q->latest('reading_date')->limit(2);
            }])
            ->get();
 
        /** @var array<string, mixed> $result */
        $result = [
            'electric' => null,
            'water'    => null,
            'room'     => $booking->room?->room_number ?? '-',
            'tenant'   => trim(($booking->guest?->first_name ?? '') . ' ' . ($booking->guest?->last_name ?? '')),
            'period'   => null,
        ];
 
        foreach ($meters as $meter) {
            /** @var Collection<int, MeterReading> $readings */
            $readings = $meter->readings;
            if ($readings->count() < 1) continue;
 
            /** @var MeterReading $current */
            $current     = $readings->first();
            /** @var MeterReading|null $previous */
            $previous    = $readings->skip(1)->first();
            $currentVal  = (float) $current->reading_value;
            $previousVal = $previous ? (float) $previous->reading_value : null;
            $usage       = $previousVal !== null ? max(0, $currentVal - $previousVal) : 0;
            $rate        = (float) ($meter->rate_per_unit ?? 0);
            $cost        = round($usage * $rate, 2);
 
            $entry = [
                'meter_number'   => (string) ($meter->meter_number ?? '-'),
                'current_value'  => $currentVal,
                'previous_value' => $previousVal,
                'usage'          => $usage,
                'rate'           => $rate,
                'cost'           => $cost,
                'reading_date'   => $current->reading_date,
            ];
 
            if (!$result['period'] && $current->reading_date) {
                $result['period'] = \Carbon\Carbon::parse($current->reading_date)
                    ->locale('th')->isoFormat('MMMM YYYY');
            }
 
            $type = (string) ($meter->type ?? '');
            if ($type === 'electric') $result['electric'] = $entry;
            elseif ($type === 'water') $result['water'] = $entry;
        }
 
        return $result;
    }
 
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $this->authorize('create', Invoice::class);
        $validated = $request->validated();
 
        if ($request->filled('draft_invoice_id')) {
            /** @var Invoice|null $draft */
            $draft = Invoice::find((int) $request->input('draft_invoice_id'));
            if ($draft) {
                $prepared = $this->invoiceService->prepareForUpdate($validated);
                $draft->update($prepared);
                return redirect()->route('invoices.show', $draft)
                    ->with('success', 'ยืนยันใบแจ้งหนี้ค่าน้ำ/ค่าไฟเรียบร้อยแล้ว');
            }
        }
 
        $prepared = $this->invoiceService->prepareForCreate($validated);
        Invoice::create($prepared);
        return redirect()->route('invoices.index')->with('success', __('ui.invoice.created'));
    }
 
    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);
        $invoice->loadMissing(['booking.room', 'booking.guest']);
        return view('invoices.show', compact('invoice'));
    }
 
    public function edit(Invoice $invoice): View
    {
        $this->authorize('update', $invoice);
        /** @var Collection<int, Booking> $bookings */
        $bookings = Booking::with(['room', 'guest'])->orderByDesc('id')->get();
        return view('invoices.edit', compact('invoice', 'bookings'));
    }
 
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);
        $validated = $request->validated();
        $prepared  = $this->invoiceService->prepareForUpdate($validated);
        $invoice->update($prepared);
        return redirect()->route('invoices.show', $invoice)->with('success', __('ui.invoice.updated'));
    }
 
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', __('ui.invoice.deleted'));
    }
 
    // ─────────────────────────────────────────────────────────────
    //  BULK CREATE — รองรับ 2 ประเภท: rent และ utility
    // ─────────────────────────────────────────────────────────────
    public function bulkCreate(Request $request): View
    {
        $this->authorize('create', Invoice::class);
 
        $type  = $request->input('type', 'rent'); // rent | utility
        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year', now()->year);
 
        // ✅ ดึง bookings ที่ confirmed
        /** @var Collection<int, Booking> $bookings */
        $bookings = Booking::with(['room', 'guest'])
            ->where('status', 'confirmed')
            ->orderBy('room_id')
            ->get();
 
        // ✅ ถ้าเป็น utility: คำนวณยอดมิเตอร์แต่ละห้อง
        $utilityData = collect();
        if ($type === 'utility') {
            foreach ($bookings as $booking) {
                $roomId = (int) $booking->room_id;
 
                // ดึง meter readings เดือนนี้และเดือนก่อนหน้า
                $electricData = $this->getMeterBillData($roomId, 'electric', $month, $year);
                $waterData    = $this->getMeterBillData($roomId, 'water', $month, $year);
 
                // รวมยอด
                $electricCost = $electricData['cost'] ?? 0;
                $waterCost    = $waterData['cost'] ?? 0;
                $baseCost     = round($electricCost + $waterCost, 2);
                $taxRate      = 0.07;
                $tax          = round($baseCost * $taxRate, 2);
                $total        = round($baseCost + $tax, 2);
 
                // เฉพาะห้องที่มีข้อมูลมิเตอร์เดือนนี้เท่านั้น
                $hasReading = ($electricData['has_reading'] ?? false) || ($waterData['has_reading'] ?? false);
 
                $utilityData->put($booking->id, [
                    'electric'    => $electricData,
                    'water'       => $waterData,
                    'base_cost'   => $baseCost,
                    'tax'         => $tax,
                    'total'       => $total,
                    'has_reading' => $hasReading,
                ]);
            }
        }
 
        return view('invoices.bulk-create', compact('bookings', 'type', 'month', 'year', 'utilityData'));
    }
 
    /**
     * คำนวณยอดมิเตอร์แต่ละห้องสำหรับเดือนที่กำหนด
     *
     * @return array<string, mixed>
     */
    private function getMeterBillData(int $roomId, string $type, int $month, int $year): array
    {
        /** @var Meter|null $meter */
        $meter = Meter::where('room_id', $roomId)->where('type', $type)->first();
 
        if (!$meter) {
            return ['has_reading' => false, 'cost' => 0];
        }
 
        // reading เดือนนี้
        /** @var MeterReading|null $current */
        $current = MeterReading::where('meter_id', $meter->id)
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->first();
 
        if (!$current) {
            return ['has_reading' => false, 'cost' => 0, 'meter_number' => $meter->meter_number];
        }
 
        // reading เดือนก่อนหน้า
        /** @var MeterReading|null $previous */
        $previous = MeterReading::where('meter_id', $meter->id)
            ->where(function ($q) use ($month, $year): void {
                // เดือนก่อนหน้า
                if ($month === 1) {
                    $q->where('period_month', 12)->where('period_year', $year - 1);
                } else {
                    $q->where('period_month', $month - 1)->where('period_year', $year);
                }
            })
            ->first();
 
        $currentVal  = (float) $current->reading_value;
        $previousVal = $previous ? (float) $previous->reading_value : 0;
        $usage       = max(0, $currentVal - $previousVal);
        $rate        = (float) ($meter->rate_per_unit ?? 0);
        $cost        = round($usage * $rate, 2);
 
        return [
            'has_reading'    => true,
            'meter_number'   => (string) ($meter->meter_number ?? '-'),
            'previous_value' => $previousVal,
            'current_value'  => $currentVal,
            'usage'          => $usage,
            'rate'           => $rate,
            'cost'           => $cost,
        ];
    }
 
    // ─────────────────────────────────────────────────────────────
    //  BULK STORE — รองรับ invoice_type จาก form
    // ─────────────────────────────────────────────────────────────
    public function bulkStore(Request $request): RedirectResponse
    {
        $this->authorize('create', Invoice::class);
 
        $invoiceType = in_array($request->input('invoice_type'), ['rent', 'utility'], true)
            ? $request->input('invoice_type')
            : 'rent';
 
        $validated = $request->validate([
            'invoices'                   => ['required', 'array', 'min:1'],
            'invoices.*.booking_id'      => ['required', 'exists:bookings,id'],
            'invoices.*.invoice_number'  => ['required', 'string', 'max:50', 'distinct', 'unique:invoices,invoice_number'],
            'invoices.*.amount'          => ['required', 'numeric', 'min:0'],
            'invoices.*.tax'             => ['required', 'numeric', 'min:0'],
            'invoices.*.total'           => ['required', 'numeric', 'min:0'],
            'invoices.*.issue_date'      => ['required', 'date'],
            'invoices.*.due_date'        => ['required', 'date'],
            'invoices.*.status'          => ['required', 'in:' . implode(',', self::STATUSES)],
            'invoices.*.notes'           => ['nullable', 'string', 'max:500'],
        ]);
 
        foreach ($validated['invoices'] as $index => $invoiceData) {
            if ($invoiceData['due_date'] <= $invoiceData['issue_date']) {
                throw ValidationException::withMessages([
                    "invoices.$index.due_date" => 'Due date must be after issue date.',
                ]);
            }
        }
 
        $count = 0;
        try {
            DB::transaction(function () use ($validated, $invoiceType, &$count): void {
                foreach ($validated['invoices'] as $invoiceData) {
                    $booking = Booking::findOrFail((int) $invoiceData['booking_id']);
                    Invoice::create([
                        'booking_id'     => $invoiceData['booking_id'],
                        'guest_id'       => $booking->guest_id ?? null,
                        'room_id'        => $booking->room_id ?? null,
                        'invoice_number' => $invoiceData['invoice_number'],
                        'amount'         => $invoiceData['amount'],
                        'tax'            => $invoiceData['tax'],
                        'total'          => $invoiceData['total'],
                        'issue_date'     => $invoiceData['issue_date'],
                        'due_date'       => $invoiceData['due_date'],
                        'status'         => $invoiceData['status'],
                        'invoice_type'   => $invoiceType,
                        'notes'          => $invoiceData['notes'] ?? null,
                    ]);
                    $count++;
                }
            });
        } catch (\Exception $e) {
            \Log::error('InvoiceController bulkStore failed', [
                'count' => count($validated['invoices']),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
 
        $redirectType = $invoiceType === 'utility' ? 'utility' : 'rent';
        return redirect()
            ->route('invoices.index', ['invoice_type' => $redirectType])
            ->with('success', "สร้างใบแจ้งหนี้สำเร็จ {$count} ใบ");
    }
 
    public function export(Request $request): mixed
    {
        $this->authorize('export', Invoice::class);
        $filename = 'invoices_export_' . date('Y-m-d') . '.xlsx';
        /** @var Collection<int, Invoice> $invoices */
        $invoices = Invoice::query()->with(['booking', 'guest'])->orderBy('id')->get();
        $rows = $this->invoiceService->formatForExport($invoices);
        return xlsx_download($filename, $rows);
    }
 
    public function markAsPaid(Invoice $invoice): RedirectResponse
    {
        $this->authorize('update', $invoice);
        $status = (string) $invoice->status;
        if (!in_array($status, ['sent', 'overdue'], true)) {
            return redirect()->route('invoices.show', $invoice)
                ->with('warning', __('ui.invoice.mark_paid_only_sent_overdue'));
        }
        try {
            $this->invoiceService->markAsPaid($invoice, 'cash');
            return redirect()->route('invoices.show', $invoice)
                ->with('success', __('ui.invoice.marked_paid'));
        } catch (\Exception $e) {
            \Log::error('InvoiceController markAsPaid failed', [
                'invoice_id' => (int) $invoice->id,
                'error'      => $e->getMessage(),
            ]);
            return redirect()->back()->withError('Failed to mark invoice as paid');
        }
    }
 
    public function generatePdf(Invoice $invoice): RedirectResponse
    {
        $this->authorize('view', $invoice);
        return redirect()->route('invoices.show', $invoice)->with('info', __('ui.common.pdf_coming_soon'));
    }
 
    public function remindAll(): RedirectResponse
    {
        $this->authorize('export', Invoice::class);
        try {
            /** @var Collection<int, Invoice> $dueInvoices */
            $dueInvoices = Invoice::whereIn('status', ['sent', 'overdue'])
                ->whereDate('due_date', '<', now())
                ->get();
            \Log::info('Invoice reminders sent', ['count' => $dueInvoices->count()]);
            return redirect()->route('invoices.index')
                ->with('success', __('ui.invoice.reminders_sent', ['count' => $dueInvoices->count()]));
        } catch (\Exception $e) {
            \Log::error('InvoiceController remindAll failed', ['error' => $e->getMessage()]);
            return redirect()->back()->withError('Failed to send reminders');
        }
    }
}
 