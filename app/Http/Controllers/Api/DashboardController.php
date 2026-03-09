<?php

namespace App\Http\Controllers\Api;

use App\Models\Room;
use App\Models\Guest;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Maintenance;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // KPI Data
        $roomCount = Room::count() ?: 0;
        $guestCount = Guest::count() ?: 0;
        $bookingCount = Booking::count() ?: 0;
        $occupiedCount = Room::where('status', 'occupied')->count() ?: 0;
        $maintenanceCount = Room::where('status', 'maintenance')->count() ?: 0;

        // Pending notifications
        $pendingPayments = Invoice::whereIn('status', ['sent', 'overdue'])
            ->whereDate('due_date', '<', Carbon::today())
            ->count();
        
        $pendingMaintenance = Maintenance::whereIn('status', ['pending', 'in_progress'])
            ->count();

        // Expiring contracts (within 30 days)
        $expiringContracts = Contract::where('status', 'active')
            ->whereDate('end_date', '>=', Carbon::today())
            ->whereDate('end_date', '<=', Carbon::today()->copy()->addDays(30))
            ->count();

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

        return response()->json([
            'roomCount' => $roomCount,
            'guestCount' => $guestCount,
            'bookingCount' => $bookingCount,
            'occupiedCount' => $occupiedCount,
            'maintenanceCount' => $maintenanceCount,
            'pendingPayments' => $pendingPayments,
            'pendingMaintenance' => $pendingMaintenance,
            'expiringContracts' => $expiringContracts,
            'currentMonthRevenue' => $currentMonthRevenue,
            'revenuePercentChange' => round($revenuePercentChange, 2),
        ]);
    }
}

