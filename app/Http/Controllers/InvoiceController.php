<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    private const STATUSES = ['draft', 'sent', 'paid', 'overdue', 'cancelled'];

    public function index(Request $request)
    {
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
        $bookings = Booking::with(['room', 'guest'])->orderByDesc('id')->get();

        return view('invoices.create', compact('bookings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'invoice_number' => 'required|unique:invoices|max:50',
            'amount' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'notes' => 'nullable|max:500',
        ]);

        Invoice::create($validated);

        return redirect()->route('invoices.index')->with('success', __('ui.invoice.created'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->loadMissing(['booking.room', 'booking.guest']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $bookings = Booking::with(['room', 'guest'])->orderByDesc('id')->get();

        return view('invoices.edit', compact('invoice', 'bookings'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'invoice_number' => 'required|unique:invoices,invoice_number,' . $invoice->id . '|max:50',
            'amount' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'notes' => 'nullable|max:500',
        ]);

        $invoice->update($validated);

        return redirect()->route('invoices.show', $invoice)->with('success', __('ui.invoice.updated'));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', __('ui.invoice.deleted'));
    }

    public function bulkCreate()
    {
        $bookings = Booking::with(['room', 'guest'])->orderByDesc('id')->get();

        return view('invoices.bulk-create', compact('bookings'));
    }

    public function bulkStore(Request $request)
    {
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
                Invoice::create([
                    'booking_id' => $invoiceData['booking_id'],
                    'invoice_number' => $invoiceData['invoice_number'],
                    'amount' => $invoiceData['amount'],
                    'tax' => $invoiceData['tax'],
                    'total' => $invoiceData['total'],
                    'issue_date' => $invoiceData['issue_date'],
                    'due_date' => $invoiceData['due_date'],
                    'status' => $invoiceData['status'],
                    'notes' => $invoiceData['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('invoices.index')->with('success', __('ui.invoice.bulk_created', ['count' => count($validated['invoices'])]));
    }

    public function export(Request $request)
    {
        $headers = ['Invoice Number', 'Booking ID', 'Amount', 'Tax', 'Total', 'Issue Date', 'Due Date', 'Status', 'Notes'];

        return csv_stream_download(
            'invoices_export_' . date('Y-m-d') . '.csv',
            $headers,
            static function (callable $push): void {
                Invoice::query()
                    ->orderBy('id')
                    ->chunk(500, function ($invoices) use ($push): void {
                        foreach ($invoices as $invoice) {
                            $push([
                                $invoice->invoice_number,
                                $invoice->booking_id,
                                $invoice->amount,
                                $invoice->tax,
                                $invoice->total,
                                $invoice->issue_date,
                                $invoice->due_date,
                                $invoice->status,
                                $invoice->notes,
                            ]);
                        }
                    });
            }
        );
    }

    public function markAsPaid(Invoice $invoice)
    {
        if (! in_array($invoice->status, ['sent', 'overdue'], true)) {
            return redirect()->route('invoices.show', $invoice)->with('warning', __('ui.invoice.mark_paid_only_sent_overdue'));
        }

        $invoice->update(['status' => 'paid']);
        AuditLogger::log('invoice.marked_paid', $invoice);

        return redirect()->route('invoices.show', $invoice)->with('success', __('ui.invoice.marked_paid'));
    }

    public function generatePdf(Invoice $invoice)
    {
        return redirect()->route('invoices.show', $invoice)->with('info', __('ui.common.pdf_coming_soon'));
    }

    public function remindAll()
    {
        $dueInvoices = Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', now())
            ->get();

        return redirect()->route('invoices.index')->with('success', __('ui.invoice.reminders_sent', ['count' => $dueInvoices->count()]));
    }
}

