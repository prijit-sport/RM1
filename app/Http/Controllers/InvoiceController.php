<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Support\AuditLogger;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('booking')->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $bookings = Booking::all();

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
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $bookings = Booking::all();

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
        $bookings = Booking::all();

        return view('invoices.bulk-create', compact('bookings'));
    }

    public function bulkStore(Request $request)
    {
        $invoices = $request->input('invoices', []);

        foreach ($invoices as $invoiceData) {
            Invoice::create([
                'booking_id' => $invoiceData['booking_id'],
                'invoice_number' => $invoiceData['invoice_number'],
                'amount' => $invoiceData['amount'],
                'tax' => $invoiceData['tax'] ?? 0,
                'total' => $invoiceData['total'],
                'issue_date' => $invoiceData['issue_date'],
                'due_date' => $invoiceData['due_date'],
                'status' => $invoiceData['status'] ?? 'draft',
                'notes' => $invoiceData['notes'] ?? null,
            ]);
        }

        return redirect()->route('invoices.index')->with('success', __('ui.invoice.bulk_created', ['count' => count($invoices)]));
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
        $pendingInvoices = Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', now())
            ->get();

        return redirect()->route('invoices.index')->with('success', __('ui.invoice.reminders_sent', ['count' => $pendingInvoices->count()]));
    }
}

