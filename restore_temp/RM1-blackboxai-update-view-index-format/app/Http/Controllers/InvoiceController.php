<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Booking;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    private const STATUSES = ['draft', 'sent', 'paid', 'overdue', 'cancelled'];

    public function __construct(protected readonly InvoiceService $invoiceService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);

        $query = Invoice::query()->with(['booking.room', 'booking.guest']);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search): void {
                $q->where('invoice_number', 'like', '%' . $search . '%')
                    ->orWhere('booking_id', $search);
            });
        }

        if ($request->filled('status') && in_array($request->input('status'), self::STATUSES, true)) {
            $query->where('status', $request->input('status'));
        }

        $invoices = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $this->authorize('create', Invoice::class);

        $bookings = Booking::with(['room', 'guest'])->orderByDesc('id')->get();
        $invoiceNumber = $this->invoiceService->generateInvoiceNumber();

        return view('invoices.create', compact('bookings', 'invoiceNumber'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $this->authorize('create', Invoice::class);

        $validated = $request->validated();
        if (empty($validated['invoice_number'])) {
            $validated['invoice_number'] = $this->invoiceService->generateInvoiceNumber();
        }
        $validated['total'] = $validated['total'] ?? $this->invoiceService->calculateTotal(
            (float) $validated['amount'],
            (float) $validated['tax']
        );

        Invoice::create($validated);

        return redirect()->route('invoices.index')->with('success', __('ui.invoice.created'));
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->loadMissing(['booking.room', 'booking.guest']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $bookings = Booking::with(['room', 'guest'])->orderByDesc('id')->get();

        return view('invoices.edit', compact('invoice', 'bookings'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $validated = $request->validated();
        $validated['total'] = $validated['total'] ?? $this->invoiceService->calculateTotal(
            (float) $validated['amount'],
            (float) $validated['tax']
        );

        $invoice->update($validated);

        return redirect()->route('invoices.show', $invoice)->with('success', __('ui.invoice.updated'));
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);

        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', __('ui.invoice.deleted'));
    }

    public function bulkCreate()
    {
        $this->authorize('create', Invoice::class);

        $bookings = Booking::with(['room', 'guest'])->orderByDesc('id')->get();

        return view('invoices.bulk-create', compact('bookings'));
    }

    public function bulkStore(Request $request)
    {
        $this->authorize('create', Invoice::class);

        $validated = $request->validate([
            'invoices' => ['required', 'array', 'min:1'],
            'invoices.*.booking_id' => ['required', 'exists:bookings,id'],
            'invoices.*.invoice_number' => ['required', 'string', 'max:50', 'distinct', 'unique:invoices,invoice_number'],
            'invoices.*.amount' => ['required', 'numeric', 'min:0'],
            'invoices.*.tax' => ['required', 'numeric', 'min:0'],
            'invoices.*.total' => ['required', 'numeric', 'min:0'],
            'invoices.*.issue_date' => ['required', 'date'],
            'invoices.*.due_date' => ['required', 'date'],
            'invoices.*.status' => ['required', 'in:' . implode(',', self::STATUSES)],
            'invoices.*.payment_method' => ['nullable', 'in:cash,bank_transfer,credit_card,e_wallet,other'],
            'invoices.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated['invoices'] as $index => $invoiceData) {
            if ($invoiceData['due_date'] <= $invoiceData['issue_date']) {
                throw ValidationException::withMessages([
                    "invoices.$index.due_date" => 'Due date must be after issue date.',
                ]);
            }
        }

        DB::transaction(function () use ($validated): void {
            foreach ($validated['invoices'] as $invoiceData) {
                $booking = Booking::find($invoiceData['booking_id']);
                
                Invoice::create([
                    'booking_id' => $invoiceData['booking_id'],
                    'guest_id' => $booking->guest_id ?? null,
                    'room_id' => $booking->room_id ?? null,
                    'invoice_number' => $invoiceData['invoice_number'],
                    'amount' => $invoiceData['amount'],
                    'tax' => $invoiceData['tax'],
                    'total' => $invoiceData['total'],
                    'issue_date' => $invoiceData['issue_date'],
                    'due_date' => $invoiceData['due_date'],
                    'status' => $invoiceData['status'],
                    'payment_method' => $invoiceData['payment_method'] ?? null,
                    'notes' => $invoiceData['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('invoices.index')->with('success', __('ui.invoice.bulk_created', ['count' => count($validated['invoices'])]));
    }

    public function export(Request $request)
    {
        $this->authorize('export', Invoice::class);

        $filename = 'invoices_export_' . date('Y-m-d') . '.xlsx';
        $rows = [
            ['Invoice Number', 'Booking ID', 'Amount', 'Tax', 'Total', 'Issue Date', 'Due Date', 'Status', 'Notes'],
        ];

        Invoice::query()
            ->orderBy('id')
            ->chunk(500, function ($invoices) use (&$rows): void {
                foreach ($invoices as $invoice) {
                    $rows[] = [
                        $invoice->invoice_number,
                        $invoice->booking_id,
                        $invoice->amount,
                        $invoice->tax,
                        $invoice->total,
                        $invoice->issue_date,
                        $invoice->due_date,
                        $invoice->status,
                        $invoice->notes,
                    ];
                }
            });

        return xlsx_download($filename, $rows);
    }

    public function markAsPaid(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        if (! in_array($invoice->status, ['sent', 'overdue'], true)) {
            return redirect()->route('invoices.show', $invoice)->with('warning', __('ui.invoice.mark_paid_only_sent_overdue'));
        }

        $invoice->update(['status' => 'paid']);
        AuditLogger::log('invoice.marked_paid', $invoice);

        return redirect()->route('invoices.show', $invoice)->with('success', __('ui.invoice.marked_paid'));
    }

    public function generatePdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        return redirect()->route('invoices.show', $invoice)->with('info', __('ui.common.pdf_coming_soon'));
    }

    public function remindAll()
    {
        $this->authorize('export', Invoice::class);

        $dueInvoices = Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', now())
            ->get();

        // TODO: Implement actual notification sending (email/SMS)
        // For now, this just shows the count of invoices that would be reminded

        return redirect()->route('invoices.index')->with('success', __('ui.invoice.reminders_sent', ['count' => $dueInvoices->count()]));
    }
}
