<?php
 
namespace App\Http\Controllers;
 
use App\Models\Room;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Maintenance;
use App\Models\Guest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
 
class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            // ─── KPI Cards ───
            'roomCount'       => Room::count(),
            'availableCount'  => Room::where('status', 'available')->count(),
            'occupiedCount'   => Room::where('status', 'occupied')->count(),
            'guestCount'      => Guest::count(),
 
            // ✅ แก้: เปลี่ยนจาก 'pending' → 'confirmed'
            'bookingCount'    => Booking::where('status', 'confirmed')->count(),
 
            'maintenanceCount' => Maintenance::whereIn('status', ['pending', 'in_progress'])->count(),
 
            // ─── สถิติแยกประเภทห้อง ───
            'roomTypeStats'   => $this->getRoomTypeStats(),
 
            // ✅ แก้: ใช้ 'air' แทน 'air_conditioning'
            'fanStats'        => $this->getRoomStatsByType('fan'),
            'airStats'        => $this->getRoomStatsByType('air'),
 
            // ─── รายได้/การเงิน ───
            'monthlyRevenue'  => $this->getMonthlyRevenue(),
 
            // ─── ตารางล่าง ───
            'recentBookings'  => Booking::with(['room', 'guest'])->latest()->take(5)->get(),
            'pendingInvoices' => Invoice::with(['booking.guest'])
                ->whereIn('status', ['sent', 'overdue'])
                ->latest()
                ->take(5)
                ->get(),
 
            // ─── กราฟรายเดือน ───
            'monthlyBookings' => $this->getMonthlyBookings(),
        ];
 
        $notifications = $this->buildNotifications(
            $data['maintenanceCount']
        );
 
        return view('dashboard.index', array_merge($data, [
            'notifications'     => $notifications,
            'notificationCount' => count($notifications),
        ]));
    }
 
    // ─────────────────────────────────────────
    //  สถิติรวมแยก room_type × status
    // ─────────────────────────────────────────
    private function getRoomTypeStats(): array
    {
        $stats = Room::select('room_type', 'status', DB::raw('count(*) as count'))
            ->groupBy('room_type', 'status')
            ->get();
 
        $formatted = [];
        foreach ($stats as $stat) {
            $formatted[$stat->room_type][$stat->status] = $stat->count;
        }
 
        return $formatted;
    }
 
    // ─────────────────────────────────────────
    //  สถิติตามประเภทห้องเดี่ยว
    //  ✅ แก้: 'air_conditioning' → 'air'
    // ─────────────────────────────────────────
    private function getRoomStatsByType(string $type): array
    {
        $rooms = Room::where('room_type', $type)->get();
 
        return [
            'total'       => $rooms->count(),
            'available'   => $rooms->where('status', 'available')->count(),
            'occupied'    => $rooms->where('status', 'occupied')->count(),
            'maintenance' => $rooms->where('status', 'maintenance')->count(),
        ];
    }
 
    // ─────────────────────────────────────────
    //  รายได้เดือนนี้ (จาก bookings confirmed)
    // ─────────────────────────────────────────
    private function getMonthlyRevenue(): float
    {
        return (float) Booking::where('status', 'confirmed')
            ->whereMonth('check_in_date', now()->month)
            ->whereYear('check_in_date', now()->year)
            ->sum('rent_amount');
    }
 
    // ─────────────────────────────────────────
    //  จำนวนการจองรายเดือน (6 เดือนล่าสุด)
    // ─────────────────────────────────────────
    private function getMonthlyBookings(): array
    {
        $results = Booking::select(
                DB::raw('MONTH(check_in_date) as month'),
                DB::raw('YEAR(check_in_date) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->where('check_in_date', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
 
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $key   = $date->format('Y-n');
            $label = $date->locale('th')->monthName . ' ' . ($date->year + 543);
 
            $found = $results->first(function ($r) use ($date) {
                return $r->month == $date->month && $r->year == $date->year;
            });
 
            $months[] = [
                'label' => $label,
                'count' => $found ? $found->count : 0,
            ];
        }
 
        return $months;
    }
 
    // ─────────────────────────────────────────
    //  การแจ้งเตือน
    // ─────────────────────────────────────────
    private function buildNotifications(int $pendingMaintenance): array
    {
        $notifications = [];
 
        if ($pendingMaintenance > 0) {
            $notifications[] = [
                'message' => "มีงานซ่อมรอดำเนินการ {$pendingMaintenance} รายการ",
                'url'     => route('maintenances.index', ['status' => 'pending']),
                'icon'    => 'bi-tools text-warning',
            ];
        }
 
        // แจ้งเตือนบิลค้าง
        $overdueInvoices = Invoice::where('status', 'overdue')->count();
        if ($overdueInvoices > 0) {
            $notifications[] = [
                'message' => "มีใบแจ้งหนี้ค้างชำระ {$overdueInvoices} รายการ",
                'url'     => route('invoices.index', ['status' => 'overdue']),
                'icon'    => 'bi-exclamation-triangle text-danger',
            ];
        }
 
        return $notifications;
    }
}
 