<?php

namespace App\Services;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * รายงาน, สถิติ, และ export สำหรับใบแจ้งหนี้ (แยกออกมาจาก InvoiceService เดิม
 * เพื่อลดขนาดไฟล์และแยกความรับผิดชอบ — ดู InvoiceService ที่ delegate มาที่นี่)
 */
class InvoiceReportService
{
    /**
     * @return Collection<int, Invoice>
     */
    public function getOverdueInvoices(): Collection
    {
        return Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', Carbon::today())
            ->with(['booking.guest', 'booking.room', 'guest', 'room'])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * @return array<string, int|float>
     */
    public function getRevenueReport(Carbon $startDate, Carbon $endDate): array
    {
        $invoices = Invoice::where('status', 'paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        return [
            'total_revenue' => (float) $invoices->sum('total'),
            'total_amount' => (float) $invoices->sum('amount'),
            'total_tax' => (float) $invoices->sum('tax'),
            'total_late_fees' => (float) $invoices->sum('late_fee'),
            'invoice_count' => $invoices->count(),
            'average_invoice' => (float) ($invoices->avg('total') ?? 0),
        ];
    }

    public function getPendingPaymentsTotal(): float
    {
        return (float) Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', Carbon::today())
            ->sum('total');
    }

    /**
     * ✅ getStats — สถิติสำหรับ index page รองรับ invoice_type filter
     *
     * @return array<string, int|float>
     */
    public function getStats(?string $invoiceType = null): array
    {
        $base = Invoice::query();
        if ($invoiceType) {
            $base = $base->where('invoice_type', $invoiceType);
        }

        $now = now();
        $prev = now()->subMonth();

        $thisMonth = (clone $base)->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)->count();
        $lastMonth = (clone $base)->whereYear('created_at', $prev->year)
            ->whereMonth('created_at', $prev->month)->count();

        return [
            'total' => (clone $base)->count(),
            'paid_count' => (clone $base)->where('status', 'paid')->count(),
            'paid_amount' => (float) (clone $base)->where('status', 'paid')->sum('total'),
            'sent_count' => (clone $base)->where('status', 'sent')->count(),
            'sent_amount' => (float) (clone $base)->where('status', 'sent')->sum('total'),
            'overdue_count' => (clone $base)->where('status', 'overdue')->count(),
            'monthly_diff' => $thisMonth - $lastMonth,
            // ✅ count แยกประเภทเสมอ (ไม่ขึ้นกับ filter)
            'rent_count' => Invoice::where('invoice_type', 'rent')->count(),
            'utility_count' => Invoice::where('invoice_type', 'utility')->count(),
        ];
    }

    /**
     * @param  Collection<int, Invoice>  $invoices
     * @return array<int, array<int, mixed>>
     */
    public function formatForExport(Collection $invoices): array
    {
        $rows = [
            ['Invoice Number', 'Type', 'Booking ID', 'Guest Name', 'Room', 'Amount', 'Tax', 'Total', 'Issue Date', 'Due Date', 'Status', 'Notes'],
        ];
        foreach ($invoices as $invoice) {
            $rows[] = [
                $invoice->invoice_number ?? '-',
                $invoice->invoice_type === 'utility' ? 'ค่าน้ำ/ไฟ' : 'ค่าห้อง',
                $invoice->booking_id ?? '-',
                trim(($invoice->booking?->guest->first_name ?? '').' '.($invoice->booking?->guest->last_name ?? '')) ?: '-',
                $invoice->booking?->room->room_number ?? '-',
                number_format((float) ($invoice->amount ?? 0), 2),
                number_format((float) ($invoice->tax ?? 0), 2),
                number_format((float) ($invoice->total ?? 0), 2),
                $invoice->issue_date ?? '-',
                $invoice->due_date ?? '-',
                $invoice->status ?? '-',
                $invoice->notes ?? '-',
            ];
        }

        return $rows;
    }
}
