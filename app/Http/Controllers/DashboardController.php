<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Guest;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Maintenance;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // Cache dashboard data for 5 minutes
        // Always use user ID if authenticated, with a unique key to prevent cache conflicts
        $userId = auth()->check() ? auth()->id() : 'guest';
        $cacheKey = 'dashboard_' . $userId . '_' . ($userId === 'guest' ? 'public' : 'private');
        
        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () {
            // KPI Data
            $roomCount = Room::count() ?: 0;
            $guestCount = Guest::count() ?: 0;
            $pendingBookingCount = Booking::where('status', 'pending')->count() ?: 0;
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

            // Recent bookings
            $recentBookings = Booking::with(['room', 'guest'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Pending invoices
            $pendingInvoices = Invoice::with(['booking.guest', 'booking.room'])
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

            return compact(
                'roomCount', 
                'guestCount', 
                'pendingBookingCount', 
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
            );
        });

        // Build notifications for dropdown
        $notifications = $this->buildNotifications(
            $data['pendingPayments'], 
            $data['pendingMaintenance'], 
            $data['expiringContracts']
        );
        $notificationCount = count($notifications);

        return view('dashboard.index', array_merge($data, [
            'notifications' => $notifications,
            'notificationCount' => $notificationCount
        ]));
    }

    /**
     * Clear dashboard cache.
     */
    public static function clearCache(): void
    {
        if (auth()->check()) {
            Cache::forget('dashboard_' . auth()->id() . '_private');
        } else {
            Cache::forget('dashboard_guest_public');
        }
    }

    /**
     * Build notifications array for dropdown.
     */
    private function buildNotifications(int $pendingPayments, int $pendingMaintenance, int $expiringContracts): array
    {
        $notifications = [];

        // Add pending payments notification
        if ($pendingPayments > 0) {
            $notifications[] = [
                'message' => "มีใบแจ้งหนี้เกินกำหนด {$pendingPayments} รายการ",
                'url' => route('invoices.index', ['status' => 'overdue']),
                'icon' => 'bi-receipt text-danger',
            ];
        }

        // Add pending maintenance notification
        if ($pendingMaintenance > 0) {
            $notifications[] = [
                'message' => "มีงานซ่อมที่ยังไม่เสร็จ {$pendingMaintenance} รายการ",
                'url' => route('maintenances.index', ['status' => 'pending']),
                'icon' => 'bi-tools text-warning',
            ];
        }

        // Add expiring contracts notification
        if ($expiringContracts > 0) {
            $notifications[] = [
                'message' => "มีสัญญาใกล้หมดอายุ {$expiringContracts} รายการ",
                'url' => route('contracts.expiring'),
                'icon' => 'bi-file-earmark-text text-info',
            ];
        }

        return $notifications;
    }
}

