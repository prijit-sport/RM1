<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Guest;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    private const STATUSES = ['draft', 'sent', 'paid', 'overdue', 'cancelled'];
    private const PAYMENT_METHODS = ['cash', 'bank_transfer', 'credit_card', 'e_wallet', 'other'];
    private const DEFAULT_TAX_RATE = 0.07; // 7% VAT
    private const DEFAULT_LATE_FEE_RATE = 0.01; // 1% per day

    /**
     * Create a new invoice from booking.
     */
    public function createFromBooking(Booking $booking, array $validated): Invoice
    {
        return DB::transaction(function () use ($booking, $validated) {
            // Auto-calculate total if not provided
            if (!isset($validated['total'])) {
                $validated['total'] = $this->calculateTotal(
                    $validated['amount'] ?? $booking->total_price,
                    $validated['tax'] ?? 0
                );
            }

            // Set default due date if not provided
            if (!isset($validated['due_date'])) {
                $validated['due_date'] = $this->calculateDueDate(
                    $validated['issue_date'] ?? now()
                );
            }

            // Auto-generate invoice number if not provided
            if (!isset($validated['invoice_number'])) {
                $validated['invoice_number'] = $this->generateInvoiceNumber();
            }

            // Link guest and room from booking
            $validated['guest_id'] = $booking->guest_id;
            $validated['room_id'] = $booking->room_id;
            $validated['booking_id'] = $booking->id;

            $invoice = Invoice::create($validated);
            AuditLogger::log('invoice.created', $invoice);

            return $invoice;
        });
    }

    /**
     * Create a new invoice with minimal defaults.
     */
    public function create(array $data): Invoice
    {
        $data['amount'] = $data['amount'] ?? 0;
        $data['tax'] = $data['tax'] ?? 0;
        $data['total'] = $data['total'] ?? $this->calculateTotal($data['amount'], $data['tax']);
        $data['issue_date'] = $data['issue_date'] ?? now();
        $data['due_date'] = $data['due_date'] ?? $this->calculateDueDate($data['issue_date']);
        $data['invoice_number'] = $data['invoice_number'] ?? $this->generateInvoiceNumber();

        return DB::transaction(function () use ($data) {
            $invoice = Invoice::create($data);
            AuditLogger::log('invoice.created', $invoice);
            return $invoice;
        });
    }

    /**
     * Create a recurring monthly invoice.
     */
    public function createMonthlyInvoice(Room $room, Guest $guest, int $month, int $year): ?Invoice
    {
        // Check if invoice already exists for this month
        $exists = Invoice::where('room_id', $room->id)
            ->where('guest_id', $guest->id)
            ->whereMonth('issue_date', $month)
            ->whereYear('issue_date', $year)
            ->exists();

        if ($exists) {
            return null;
        }

        $issueDate = Carbon::createFromDate($year, $month, 1);
        
        return $this->create([
            'booking_id' => null,
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'invoice_number' => $this->generateInvoiceNumber(),
            'amount' => $room->price_per_month,
            'tax' => $room->price_per_month * self::DEFAULT_TAX_RATE,
            'issue_date' => $issueDate,
            'due_date' => $issueDate->copy()->addDays(15),
            'status' => 'sent',
            'notes' => "ค่าเช่าห้อง {$room->room_number} ประจำเดือน {$issueDate->format('F Y')}",
        ]);
    }

    /**
     * Mark invoice as paid.
     */
    public function markAsPaid(Invoice $invoice, string $method, ?float $amount = null): Invoice
    {
        if (!in_array($method, self::PAYMENT_METHODS, true)) {
            throw ValidationException::withMessages([
                'payment_method' => 'Invalid payment method.',
            ]);
        }

        if (!in_array($invoice->status, ['sent', 'overdue'], true)) {
            throw ValidationException::withMessages([
                'status' => 'Can only mark sent or overdue invoices as paid.',
            ]);
        }

        $paidAmount = $amount ?? $invoice->total;

        DB::transaction(function () use ($invoice, $method, $paidAmount) {
            $invoice->update([
                'status' => 'paid',
                'payment_method' => $method,
                'payment_date' => now(),
                'paid_amount' => $paidAmount,
            ]);

            AuditLogger::log('invoice.marked_paid', $invoice, [
                'payment_method' => $method,
                'paid_amount' => $paidAmount,
            ]);
        });

        return $invoice->fresh();
    }

    /**
     * Calculate and apply late fee.
     */
    public function applyLateFee(Invoice $invoice): Invoice
    {
        $dueDate = $invoice->due_date;
        if (!($dueDate instanceof Carbon) || !$dueDate->isPast() || $invoice->status === 'paid') {
            return $invoice;
        }

        $daysOverdue = now()->diffInDays($dueDate);
        $amount = $invoice->amount ?? 0;
        $tax = $invoice->tax ?? 0;
        $existingLateFee = $invoice->late_fee ?? 0;
        
        // Calculate late fee on base amount (not including previous late fee to avoid double compounding)
        $subtotal = $amount + $tax;
        $lateFee = $subtotal * self::DEFAULT_LATE_FEE_RATE * $daysOverdue;

        $invoice->update([
            'late_fee' => $existingLateFee + $lateFee,
            'total' => $subtotal + $existingLateFee + $lateFee,
            'status' => 'overdue',
        ]);

        return $invoice->fresh();
    }

    /**
     * Send payment reminder for overdue invoices.
     */
    public function sendReminders(): int
    {
        $overdueInvoices = Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', Carbon::today())
            ->get();

        /** @var Invoice $invoice */
        foreach ($overdueInvoices as $invoice) {
            $this->applyLateFee($invoice);
            // TODO: Send email/SMS notification
            AuditLogger::log('invoice.reminder_sent', $invoice);
        }

        return $overdueInvoices->count();
    }

    /**
     * Calculate total from amount and tax.
     */
    public function calculateTotal(float $amount, float $tax): float
    {
        return round($amount + $tax, 2);
    }

    /**
     * Calculate due date (default 15 days after issue date).
     */
    public function calculateDueDate($issueDate, int $days = 15): Carbon
    {
        return Carbon::parse($issueDate)->addDays($days);
    }

    /**
     * Generate unique invoice number.
     */
    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = now()->format('Y');
        $month = now()->format('m');
        
        $lastInvoice = Invoice::whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->first();

        $sequence = $lastInvoice 
            ? (intval(substr($lastInvoice->invoice_number, -5)) + 1)
            : 1;

        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    /**
     * Get overdue invoices.
     */
    public function getOverdueInvoices(): \Illuminate\Database\Eloquent\Collection
    {
        return Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', Carbon::today())
            ->with(['booking.guest', 'booking.room', 'guest', 'room'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Get revenue report for a period.
     */
    public function getRevenueReport(Carbon $startDate, Carbon $endDate): array
    {
        $invoices = Invoice::where('status', 'paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        return [
            'total_revenue' => $invoices->sum('total'),
            'total_amount' => $invoices->sum('amount'),
            'total_tax' => $invoices->sum('tax'),
            'total_late_fees' => $invoices->sum('late_fee'),
            'invoice_count' => $invoices->count(),
            'average_invoice' => $invoices->avg('total') ?? 0,
        ];
    }

    /**
     * Get pending payments total.
     */
    public function getPendingPaymentsTotal(): float
    {
        return Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', Carbon::today())
            ->sum('total');
    }
}

