<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Contract;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
 
class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // ══════════════════════════════════════
        //  KPI Data
        // ══════════════════════════════════════
        $roomStats = Room::query()
            ->selectRaw("
                COUNT(*)                                        AS total,
                SUM(status = 'occupied')                        AS occupied,
                SUM(status = 'available')                       AS available,
                SUM(status = 'maintenance')                     AS maintenance
            ")
            ->first();
 
        $roomCount        = (int) ($roomStats->total       ?? 0);
        $occupiedCount    = (int) ($roomStats->occupied    ?? 0);
        $availableCount   = (int) ($roomStats->available   ?? 0);
        $maintenanceCount = (int) ($roomStats->maintenance ?? 0);
 
        $guestCount   = Guest::count();
        $bookingCount = Booking::count();
 
        // ══════════════════════════════════════
        //  Expiring Contracts
        // ══════════════════════════════════════
        $expiringContracts = Contract::where('status', 'active')
            ->whereDate('end_date', '>=', Carbon::today())
            ->whereDate('end_date', '<=', Carbon::today()->addDays(
                config('rm1.contract_expiry_warning_days', 30)
            ))
            ->count();
 
        // ══════════════════════════════════════
        //  Monthly Revenue
        // ══════════════════════════════════════
        $currentMonth = Carbon::now()->month;
        $currentYear  = Carbon::now()->year;
 
        $currentMonthRevenue = Invoice::where('status', 'paid')
            ->whereMonth('issue_date', $currentMonth)
            ->whereYear('issue_date', $currentYear)
            ->sum('total');
 
        $lastMonthDate = Carbon::now()->subMonth();
        $lastMonth     = $lastMonthDate->month;
        $lastMonthYear = $lastMonthDate->year;
 
        $lastMonthRevenue = Invoice::where('status', 'paid')
            ->whereMonth('issue_date', $lastMonth)
            ->whereYear('issue_date', $lastMonthYear)
            ->sum('total');
 
        $revenuePercentChange = 0;
        if ($lastMonthRevenue > 0) {
            $revenuePercentChange = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }
 
        // ══════════════════════════════════════
        //  Monthly Bookings (12 เดือนย้อนหลัง)
        // ══════════════════════════════════════
        $thaiMonths = [
            1  => 'ม.ค.',
            2  => 'ก.พ.',
            3  => 'มี.ค.',
            4  => 'เม.ย.',
            5  => 'พ.ค.',
            6  => 'มิ.ย.',
            7  => 'ก.ค.',
            8  => 'ส.ค.',
            9  => 'ก.ย.',
            10 => 'ต.ค.',
            11 => 'พ.ย.',
            12 => 'ธ.ค.',
        ];
 
        $monthlyBookings = [];
        for ($i = 11; $i >= 0; $i--) {
            $date              = Carbon::now()->subMonths($i);
            $monthlyBookings[] = [
                'label' => $thaiMonths[$date->month] . ' ' . ($date->year + 543),
                'count' => Booking::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }
 
        // ══════════════════════════════════════
        //  Room Type Stats
        // ══════════════════════════════════════
        $roomTypeRaw = Room::query()
            ->selectRaw("
                room_type,
                SUM(status = 'available')   AS available,
                SUM(status = 'occupied')    AS occupied,
                SUM(status = 'maintenance') AS maintenance
            ")
            ->whereIn('room_type', ['fan', 'air'])
            ->groupBy('room_type')
            ->get()
            ->keyBy('room_type');
 
        $roomTypeStats = [];
        foreach (['fan', 'air'] as $type) {
            $row                  = $roomTypeRaw->get($type);
            $roomTypeStats[$type] = [
                'available'   => (int) ($row->available   ?? 0),
                'occupied'    => (int) ($row->occupied    ?? 0),
                'maintenance' => (int) ($row->maintenance ?? 0),
            ];
        }
 
        // ══════════════════════════════════════
        //  Recent Bookings
        // ══════════════════════════════════════
        $recentBookings = Booking::with(['room', 'guest'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function (Booking $b) {
                $checkIn = $b->check_in_date;
                return [
                    'id'            => $b->id,
                    'room_number'   => $b->room?->room_number ?? '-',
                    'room_id'       => $b->room?->id,
                    'guest_name'    => $b->guest?->full_name ?? '-',
                    'check_in_date' => $checkIn instanceof Carbon
                        ? $checkIn->format('d/m/Y')
                        : ($checkIn ?? '-'),
                    'status'        => $b->status ?? '-',
                ];
            });
 
        // ══════════════════════════════════════
        //  Pending Invoices
        // ══════════════════════════════════════
        $pendingInvoices = Invoice::with(['booking.guest'])
            ->whereIn('status', ['sent', 'overdue', 'pending'])
            ->orderBy('due_date')
            ->take(5)
            ->get()
            ->map(function (Invoice $inv) {
                $dueDate = $inv->due_date;
                $booking = $inv->booking;
                return [
                    'id'             => $inv->id,
                    'invoice_number' => $inv->invoice_number ?? '-',
                    'guest_name'     => $booking?->guest?->full_name ?? '-',
                    'total'          => $inv->total ?? 0,
                    'due_date'       => $dueDate instanceof Carbon
                        ? $dueDate->format('d/m/Y')
                        : ($dueDate ?? '-'),
                    'is_overdue'     => $dueDate instanceof Carbon && $dueDate->isPast(),
                ];
            });
 
        // ══════════════════════════════════════
        //  Response (JSON for API)
        // ══════════════════════════════════════
        return response()->json([
            'room_count'             => $roomCount,
            'occupied_count'         => $occupiedCount,
            'available_count'        => $availableCount,
            'maintenance_count'      => $maintenanceCount,
            'guest_count'            => $guestCount,
            'booking_count'          => $bookingCount,
            'expiring_contracts'     => (int) $expiringContracts,
            'current_month_revenue'  => (float) $currentMonthRevenue,
            'revenue_percent_change' => (float) round($revenuePercentChange, 2),
            'monthly_bookings'       => $monthlyBookings,
            'room_type_stats'        => $roomTypeStats,
            'recent_bookings'        => $recentBookings,
            'pending_invoices'       => $pendingInvoices,
        ]);
    }
}
 