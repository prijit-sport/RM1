<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Guest;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Maintenance;
use App\Models\Contract;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // KPI Data
        $roomCount = Room::count();
        $guestCount = Guest::count();
        $bookingCount = Booking::count();
        $occupiedCount = Room::where('status', 'occupied')->count();
        $maintenanceCount = Room::where('status', 'maintenance')->count();

        // Pending notifications
        $pendingPayments = Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', Carbon::today())
            ->count();
        
        $pendingMaintenance = Maintenance::whereIn('status', ['pending', 'in_progress'])
            ->count();

        // Recent bookings
        $recentBookings = Booking::with(['room', 'guest'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Pending invoices
        $pendingInvoices = Invoice::with(['booking.guest'])
            ->whereIn('status', ['sent', 'overdue'])
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        // Monthly revenue calculation
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $currentMonthRevenue = Invoice::where('status', 'paid')
            ->whereMonth('issue_date', $currentMonth)
            ->whereYear('issue_date', $currentYear)
            ->sum('total');
        
        $lastMonth = Carbon::now()->subMonth()->month;
        $lastMonthYear = Carbon::now()->subMonth()->year;
        
        $lastMonthRevenue = Invoice::where('status', 'paid')
            ->whereMonth('issue_date', $lastMonth)
            ->whereYear('issue_date', $lastMonthYear)
            ->sum('total');
        
        // Calculate percentage change
        $revenuePercentChange = 0;
        if ($lastMonthRevenue > 0) {
            $revenuePercentChange = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
        }

        // Chart data - last 6 months
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $chartLabels[] = $month->shortMonthName;
            
            $count = Booking::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
            
            // Normalize to max 95%
            $chartData[] = min($count, 95);
        }

        // Get contracts expiring soon (within 30 days)
        $expiringContracts = Contract::whereDate('end_date', '>=', Carbon::today())
            ->whereDate('end_date', '<=', Carbon::today()->copy()->addDays(30))
            ->count();

        return view('dashboard.index', compact(
            'roomCount', 
            'guestCount', 
            'bookingCount', 
            'occupiedCount',
            'maintenanceCount',
            'pendingPayments',
            'pendingMaintenance',
            'recentBookings',
            'pendingInvoices',
            'currentMonthRevenue',
            'revenuePercentChange',
            'chartLabels',
            'chartData',
            'expiringContracts'
        ));
    }
}

