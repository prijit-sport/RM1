<?php

namespace App\Http\Controllers\Api;

use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::query()->with(['booking.room', 'booking.guest']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhere('booking_id', $request->search);
            });
        }

        $invoices = $query->orderByDesc('id')->paginate(10);

        return response()->json($invoices);
    }

    public function store(Request $request): JsonResponse
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

        $invoice = Invoice::create($validated);

        return response()->json($invoice, 201);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json($invoice->load(['booking.room', 'booking.guest']));
    }

    public function update(Request $request, Invoice $invoice): JsonResponse
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

        return response()->json($invoice);
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        $invoice->delete();

        return response()->json(['message' => 'Invoice deleted successfully']);
    }

    public function markAsPaid(Invoice $invoice): JsonResponse
    {
        if (!in_array($invoice->status, ['sent', 'overdue'], true)) {
            return response()->json([
                'message' => 'Invoice can only be marked as paid when status is sent or overdue'
            ], 400);
        }

        $invoice->update(['status' => 'paid']);

        return response()->json(['message' => 'Invoice marked as paid successfully']);
    }
}

