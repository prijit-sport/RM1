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
        // ดึงข้อมูลสดใหม่จาก Database เพื่อให้หน้า Dashboard อัปเดตทันที
        $data = [
            // 1. สถิติภาพรวมด้านบน (KPI Cards)
            'roomCount' => Room::count(),
            'occupiedCount' => Room::where('status', 'occupied')->count(),
            'guestCount' => Guest::count(), // ดึงจำนวนผู้เช่าจริง
            'bookingCount' => Booking::where('status', 'pending')->count(),
            'maintenanceCount' => Maintenance::whereIn('status', ['pending', 'in_progress'])->count(),
            
            // 2. สถิติแยกประเภทห้อง (สำหรับส่วนโซนพัดลม/แอร์)
            'roomTypeStatsFilling' => $this->getRoomTypeStats(),
            
            // 3. ข้อมูลตารางด้านล่าง
            'recentBookings' => Booking::with(['room', 'guest'])->latest()->take(5)->get(),
            'pendingInvoices' => Invoice::with(['booking.guest'])
                ->whereIn('status', ['sent', 'overdue'])
                ->latest()
                ->take(5)
                ->get(),
        ];

        // สร้างการแจ้งเตือน (Notifications)
        $notifications = $this->buildNotifications($data['maintenanceCount']);

        return view('dashboard.index', array_merge($data, [
            'notifications' => $notifications,
            'notificationCount' => count($notifications)
        ]));
    }

    /**
     * ฟังก์ชันคำนวณสถิติแยกตามประเภทห้องและสถานะ
     */
    private function getRoomTypeStats()
    {
        $stats = Room::select('room_type', 'status', DB::raw('count(*) as count'))
            ->groupBy('room_type', 'status')
            ->get();

        $formatted = [];
        // จัดรูปแบบข้อมูลเพื่อให้ Blade นำไปวนลูปแสดงผลได้ง่าย
        foreach ($stats as $stat) {
            $formatted[$stat->room_type][$stat->status] = $stat->count;
        }

        return $formatted;
    }

    /**
     * ฟังก์ชันสร้างรายการแจ้งเตือนสำหรับ Dashboard
     */
    private function buildNotifications(int $pendingMaintenance): array
    {
        $notifications = [];
        if ($pendingMaintenance > 0) {
            $notifications[] = [
                'message' => "มีงานซ่อมรอดำเนินการ {$pendingMaintenance} รายการ",
                'url' => route('maintenances.index', ['status' => 'pending']),
                'icon' => 'bi-tools text-warning',
            ];
        }
        return $notifications;
    }
}