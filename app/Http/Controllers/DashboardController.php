<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Maintenance;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ══════════════════════════════════════
        //  KPI Data
        // ══════════════════════════════════════
        $roomCount        = Room::count() ?: 0;
        $guestCount       = Guest::count() ?: 0;
        $bookingCount     = Booking::count() ?: 0;
        $occupiedCount    = Room::where('status', 'occupied')->count() ?: 0;
        $availableCount   = Room::where('status', 'available')->count() ?: 0;
        $maintenanceCount = Room::where('status', 'maintenance')->count() ?: 0;

        // ══════════════════════════════════════
        //  Pending Notifications
        // ══════════════════════════════════════
        $pendingPayments = Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', Carbon::today())
            ->count();

        $pendingMaintenance = Maintenance::whereIn('status', ['pending', 'in_progress'])
            ->count();

        // ══════════════════════════════════════
        //  Expiring Contracts (within 30 days)
        // ══════════════════════════════════════
        $expiringContracts = Contract::where('status', 'active')
            ->whereDate('end_date', '>=', Carbon::today())
            ->whereDate('end_date', '<=', Carbon::today()->addDays(30))
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

        $lastMonth     = Carbon::now()->subMonth()->month;
        $lastMonthYear = Carbon::now()->subMonth()->year;

        $lastMonthRevenue = Invoice::where('status', 'paid')
            ->whereMonth('issue_date', $lastMonth)
            ->whereYear('issue_date', $lastMonthYear)
            ->sum('total');

        $revenuePercentChange = 0;
        if ($lastMonthRevenue > 0) {
            $revenuePercentChange = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        // ══════════════════════════════════════
        //  Monthly Bookings
        // ══════════════════════════════════════
        $thaiMonths = [
            1 => 'ม.ค.',
            2 => 'ก.พ.',
            3 => 'มี.ค.',
            4 => 'เม.ย.',
            5 => 'พ.ค.',
            6 => 'มิ.ย.',
            7 => 'ก.ค.',
            8 => 'ส.ค.',
            9 => 'ก.ย.',
            10 => 'ต.ค.',
            11 => 'พ.ย.',
            12 => 'ธ.ค.',
        ];

        $monthlyBookings = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyBookings[] = [
                'label' => $thaiMonths[$date->month] . ' ' . ($date->year + 543),
                'count' => Booking::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        // ══════════════════════════════════════
        //  Room Type Stats (แก้ไขคอลัมน์เป็น 'room_type')
        // ══════════════════════════════════════
        $roomTypeStats = [];
        foreach (['fan', 'air'] as $type) {
            $roomTypeStats[$type] = [
                'available'   => Room::where('room_type', $type)->where('status', 'available')->count(),
                'occupied'    => Room::where('room_type', $type)->where('status', 'occupied')->count(),
                'maintenance' => Room::where('room_type', $type)->where('status', 'maintenance')->count(),
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
                    'room_number'   => $b->room !== null ? ($b->room->room_number ?? '-') : '-',
                    'room_id'       => $b->room !== null ? $b->room->id : null,
                    'guest_name'    => $b->guest !== null ? ($b->guest->full_name ?? '-') : '-',
                    'check_in_date' => $checkIn instanceof Carbon ? $checkIn->format('d/m/Y') : ($checkIn ?? '-'),
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
                    'guest_name'     => ($booking !== null && $booking->guest !== null) ? ($booking->guest->full_name ?? '-') : '-',
                    'total'          => $inv->total ?? 0,
                    'due_date'       => $dueDate instanceof Carbon ? $dueDate->format('d/m/Y') : ($dueDate ?? '-'),
                    'is_overdue'     => $dueDate instanceof Carbon && $dueDate->isPast(),
                ];
            });

        // ══════════════════════════════════════
        //  Response
        // ══════════════════════════════════════
        return view('dashboard.index', compact(
            'roomCount',
            'guestCount',
            'bookingCount',
            'occupiedCount',
            'availableCount',
            'maintenanceCount',
            'pendingPayments',
            'pendingMaintenance',
            'expiringContracts',
            'currentMonthRevenue',
            'revenuePercentChange',
            'monthlyBookings',
            'roomTypeStats',
            'recentBookings',
            'pendingInvoices'
        ));
    }
}
