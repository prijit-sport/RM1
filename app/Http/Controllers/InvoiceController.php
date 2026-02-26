<?php
namespace App\Http\Controllers;
use App\Models\Invoice;
use App\Models\Booking;
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
        return redirect()->route('invoices.index')->with('success', 'Invoice เพิ่มสำเร็จ');
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
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice อัปเดตสำเร็จ');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice ลบสำเร็จ');
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

        return redirect()->route('invoices.index')->with('success', 'Invoice ถูกสร้าง ' . count($invoices) . ' รายการสำเร็จ');
    }

    public function export(Request $request)
    {
        $invoices = Invoice::with('booking')->get();

        $csvData = [];
        $csvData[] = ['Invoice Number', 'Booking ID', 'Amount', 'Tax', 'Total', 'Issue Date', 'Due Date', 'Status', 'Notes'];

        foreach ($invoices as $invoice) {
            $csvData[] = [
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

        $filename = 'invoices_export_' . date('Y-m-d') . '.csv';

        return response()->stream(function () use ($csvData) {
            $handle = fopen('php://output', 'wb');
            fwrite($handle, chr(0xFF) . chr(0xFE));

            foreach ($csvData as $row) {
                $line = '"' . implode('","', array_map(function ($value) {
                    return str_replace('"', '""', (string) $value);
                }, $row)) . '"' . "\r\n";
                fwrite($handle, mb_convert_encoding($line, 'UTF-16LE', 'UTF-8'));
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-16LE',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid']);
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice ถูกชำระเงินแล้ว');
    }

    public function generatePdf(Invoice $invoice)
    {
        return redirect()->route('invoices.show', $invoice)->with('info', 'PDF generation coming soon');
    }

    public function remindAll()
    {
        $pendingInvoices = Invoice::where('status', 'pending')
            ->where('due_date', '<', now())
            ->get();
        
        return redirect()->route('invoices.index')->with('success', 'ส่งแจ้งเตือน ' . $pendingInvoices->count() . ' รายการแล้ว');
    }
}
