<?php
// NOTE: Item feature removed from the project (ItemController/Item model/views/migration/lang keys).
 
namespace App\Http\Controllers;
 
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Guest;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
 
class InvoiceController extends Controller
{
    // ─────────────────────────────────────────
    //  LIST
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);
 
        $invoices = Invoice::with(['guest', 'room', 'booking'])
            ->when($request->filled('status'), fn (Builder $q) =>
                $q->where('status', $request->string('status'))
            )
            ->when($request->filled('search'), function (Builder $q) use ($request) {
                $search = trim($request->string('search'));
                $q->where(function (Builder $sub) use ($search) {
                    $sub->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('guest', fn ($g) =>
                            $g->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name',  'like', "%{$search}%")
                        )
                        ->orWhereHas('room', fn ($r) =>
                            $r->where('room_number', 'like', "%{$search}%")
                        );
                });
            })
            ->when($request->filled('month'), fn (Builder $q) =>
                $q->whereMonth('issue_date', $request->integer('month'))
            )
            ->when($request->filled('year'), fn (Builder $q) =>
                $q->whereYear('issue_date', $request->integer('year'))
            )
            ->latest('id')
            ->paginate(15)
            ->withQueryString();
 
        return view('invoices.index', compact('invoices'));
    }
 
    // ─────────────────────────────────────────
    //  CREATE FORM
    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Invoice::class);
 
        $guests   = Guest::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
        $bookings = Booking::with(['guest', 'room'])
            ->where('status', 'confirmed')
            ->orderByDesc('id')
            ->get();
 
        return view('invoices.create', compact('guests', 'bookings'));
    }
 
    // ─────────────────────────────────────────
    //  STORE
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $this->authorize('create', Invoice::class);
 
        $validated = $request->validate([
            'booking_id'     => ['required', 'exists:bookings,id'],
            'invoice_number' => ['required', 'max:50', 'unique:invoices,invoice_number'],
            'amount'         => ['required', 'numeric', 'min:0'],
            'tax'            => ['nullable', 'numeric', 'min:0'],
            'total'          => ['required', 'numeric', 'min:0'],
            'issue_date'     => ['required', 'date'],
            'due_date'       => ['required', 'date', 'after_or_equal:issue_date'],
            'status'         => ['required', 'in:draft,sent,paid,overdue,cancelled'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ]);
 
        // ดึง guest_id และ room_id จาก booking
        $booking = Booking::findOrFail($validated['booking_id']);
        assert($booking instanceof Booking);
 
        $validated['guest_id'] = $booking->guest_id;
        $validated['room_id']  = $booking->room_id;
        $validated['tax']      = $validated['tax'] ?? 0;
 
        $invoice = Invoice::create($validated);
        assert($invoice instanceof Invoice);
 
        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', __('ui.invoice.created'));
    }
 
    // ─────────────────────────────────────────
    //  SHOW
    // ─────────────────────────────────────────
    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
 
        $invoice->load(['guest', 'room', 'booking.room', 'booking.guest']);
 
        return view('invoices.show', compact('invoice'));
    }
 
    // ─────────────────────────────────────────
    //  EDIT FORM
    // ─────────────────────────────────────────
    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
 
        $bookings = Booking::with(['guest', 'room'])
            ->where('status', 'confirmed')
            ->orderByDesc('id')
            ->get();
 
        return view('invoices.edit', compact('invoice', 'bookings'));
    }
 
    // ─────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────
    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);
 
        $validated = $request->validate([
            'booking_id'     => ['required', 'exists:bookings,id'],
            'invoice_number' => ['required', 'max:50',
                                 "unique:invoices,invoice_number,{$invoice->id}"],
            'amount'         => ['required', 'numeric', 'min:0'],
            'tax'            => ['nullable', 'numeric', 'min:0'],
            'total'          => ['required', 'numeric', 'min:0'],
            'issue_date'     => ['required', 'date'],
            'due_date'       => ['required', 'date', 'after_or_equal:issue_date'],
            'status'         => ['required', 'in:draft,sent,paid,overdue,cancelled'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ]);
 
        $booking = Booking::findOrFail($validated['booking_id']);
        assert($booking instanceof Booking);
 
        $validated['guest_id'] = $booking->guest_id;
        $validated['room_id']  = $booking->room_id;
        $validated['tax']      = $validated['tax'] ?? 0;
 
        $invoice->update($validated);
 
        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', __('ui.invoice.updated'));
    }
 
    // ─────────────────────────────────────────
    //  DELETE
    // ─────────────────────────────────────────
    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
 
        $invoice->delete();
 
        return redirect()
            ->route('invoices.index')
            ->with('success', __('ui.invoice.deleted'));
    }
 
    // ─────────────────────────────────────────
    //  MARK AS PAID
    // ─────────────────────────────────────────
    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);
 
        if (! in_array($invoice->status, ['sent', 'overdue'], true)) {
            return back()->with('error', 'ใบแจ้งหนี้นี้ไม่สามารถเปลี่ยนสถานะเป็น "ชำระแล้ว" ได้');
        }
 
        $validated = $request->validate([
            'payment_method' => ['nullable', 'string', 'max:100'],
            'paid_amount'    => ['nullable', 'numeric', 'min:0'],
        ]);
 
        $invoice->markAsPaid(
            $validated['payment_method'] ?? 'cash',
            $validated['paid_amount']    ?? null
        );
 
        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', __('ui.invoice.paid'));
    }
 
    // ─────────────────────────────────────────
    //  BULK CREATE (สร้างใบแจ้งหนี้ค่าเช่าหลายห้องพร้อมกัน)
    // ─────────────────────────────────────────
    public function bulkCreate()
    {
        $this->authorize('create', Invoice::class);
 
        $bookings = Booking::with(['guest', 'room'])
            ->where('status', 'confirmed')
            ->orderBy('id')
            ->get();
 
        $currentMonth = now()->month;
        $currentYear  = now()->year;
 
        return view('invoices.bulk-create', compact('bookings', 'currentMonth', 'currentYear'));
    }
 
    // ─────────────────────────────────────────
    //  BULK STORE
    // ─────────────────────────────────────────
    public function bulkStore(Request $request)
    {
        $this->authorize('create', Invoice::class);
 
        $validated = $request->validate([
            'month'      => ['required', 'integer', 'min:1', 'max:12'],
            'year'       => ['required', 'integer', 'min:2000', 'max:2100'],
            'booking_ids' => ['required', 'array', 'min:1'],
            'booking_ids.*' => ['exists:bookings,id'],
        ]);
 
        $month    = $validated['month'];
        $year     = $validated['year'];
        $created  = 0;
        $skipped  = 0;
 
        $issueDate = Carbon::create($year, $month, 1)->startOfMonth();
        $dueDate   = $issueDate->copy()->addDays(15);
 
        DB::transaction(function () use ($validated, $month, $year, $issueDate, $dueDate, &$created, &$skipped) {
            foreach ($validated['booking_ids'] as $bookingId) {
                $booking = Booking::with('room')->find($bookingId);
                if (! $booking instanceof Booking) {
                    continue;
                }
 
                // เช็คว่ามี invoice เดือนนี้แล้วหรือยัง
                $exists = Invoice::where('booking_id', $booking->id)
                    ->whereMonth('issue_date', $month)
                    ->whereYear('issue_date', $year)
                    ->exists();
 
                if ($exists) {
                    $skipped++;
                    continue;
                }
 
                $rent   = (float) ($booking->rent_amount ?? $booking->room?->price_per_month ?? 0);
                $number = sprintf('INV-%d%02d-%04d', $year, $month, $booking->id);
 
                Invoice::create([
                    'booking_id'     => $booking->id,
                    'guest_id'       => $booking->guest_id,
                    'room_id'        => $booking->room_id,
                    'invoice_number' => $number,
                    'amount'         => $rent,
                    'tax'            => 0,
                    'total'          => $rent,
                    'issue_date'     => $issueDate->toDateString(),
                    'due_date'       => $dueDate->toDateString(),
                    'status'         => 'sent',
                    'notes'          => 'ค่าเช่าประจำเดือน ' . $month . '/' . $year,
                ]);
 
                $created++;
            }
        });
 
        return redirect()
            ->route('invoices.index')
            ->with('success', "สร้างใบแจ้งหนี้สำเร็จ {$created} ใบ" . ($skipped > 0 ? " (ข้าม {$skipped} ใบ เพราะมีอยู่แล้ว)" : ''));
    }
 
    // ─────────────────────────────────────────
    //  REMIND ALL (อัปเดต overdue อัตโนมัติ)
    // ─────────────────────────────────────────
    public function remindAll(Request $request)
    {
        $this->authorize('create', Invoice::class);
 
        // อัปเดต sent → overdue ถ้าเลย due_date
        $updated = Invoice::where('status', 'sent')
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);
 
        return redirect()
            ->route('invoices.index')
            ->with('success', "อัปเดตใบแจ้งหนี้เกินกำหนด {$updated} ใบเป็นสถานะ 'เกินกำหนด'");
    }
 
    // ─────────────────────────────────────────
    //  GENERATE PDF
    // ─────────────────────────────────────────
    public function generatePdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
 
        $invoice->load(['guest', 'room', 'booking']);
 
        // ถ้ามี PDF library (เช่น barryvdh/laravel-dompdf)
        // return PDF::loadView('invoices.pdf', compact('invoice'))->download("invoice_{$invoice->invoice_number}.pdf");
 
        // Fallback: แสดงหน้า print-friendly
        return view('invoices.pdf', compact('invoice'));
    }
 
    // ─────────────────────────────────────────
    //  EXPORT (Excel)
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);
 
        $invoices = Invoice::with(['guest', 'room', 'booking'])
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->string('status'))
            )
            ->orderByDesc('id')
            ->get();
 
        $filename = 'invoices_' . date('Ymd_His') . '.xlsx';
 
        $rows   = [];
        $rows[] = [
            'เลขที่ใบแจ้งหนี้', 'ห้อง', 'ผู้เช่า',
            'ยอดเงิน', 'ภาษี', 'ยอดรวม',
            'ค่าปรับ', 'ยอดชำระ', 'วิธีชำระ',
            'วันที่ออก', 'วันครบกำหนด', 'วันชำระ',
            'สถานะ', 'หมายเหตุ',
        ];
 
        foreach ($invoices as $inv) {
            $rows[] = [
                $inv->invoice_number,
                $inv->room?->room_number ?? '-',
                trim(($inv->guest?->first_name ?? '') . ' ' . ($inv->guest?->last_name ?? '')) ?: '-',
                number_format((float) $inv->amount, 2),
                number_format((float) $inv->tax, 2),
                number_format((float) $inv->total, 2),
                number_format((float) $inv->late_fee, 2),
                number_format((float) $inv->paid_amount, 2),
                $inv->payment_method ?? '-',
                $inv->issue_date?->format('d/m/Y') ?? '-',
                $inv->due_date?->format('d/m/Y')   ?? '-',
                $inv->payment_date?->format('d/m/Y') ?? '-',
                $inv->status,
                $inv->notes ?? '-',
            ];
        }
 
        return xlsx_download($filename, $rows);
    }
}
 