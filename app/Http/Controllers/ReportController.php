<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;

class ReportController extends Controller
{
    /**
     * Build report data used by index, revenue and occupancy pages.
     *
     * @return array<string, mixed>
     */
    private function buildReportData(): array
    {
        $total_rooms = Room::count();
        $occupied_rooms = Room::where('status', 'occupied')->count();
        $available_rooms = Room::where('status', 'available')->count();
        $maintenance_rooms = Room::where('status', 'maintenance')->count();

        $total_bookings = Booking::count();
        $pending_bookings = Booking::where('status', 'pending')->count();
        $confirmed_bookings = Booking::where('status', 'confirmed')->count();
        $cancelled = Booking::where('status', 'cancelled')->count();

        $occupancy_rate = $total_rooms > 0
            ? round(($occupied_rooms / $total_rooms) * 100, 2)
            : 0;

        $total_revenue = Booking::where('status', '!=', 'cancelled')->sum('total_price');

        $bookings_this_month = Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $popular_rooms = Room::withCount('bookings')
            ->orderBy('bookings_count', 'DESC')
            ->limit(5)
            ->get();

        return compact(
            'total_rooms',
            'occupied_rooms',
            'available_rooms',
            'maintenance_rooms',
            'total_bookings',
            'pending_bookings',
            'confirmed_bookings',
            'cancelled',
            'occupancy_rate',
            'total_revenue',
            'bookings_this_month',
            'popular_rooms',
        );
    }

    public function index()
    {
        return view('reports.index', $this->buildReportData());
    }

    public function revenue()
    {
        return view('reports.index', $this->buildReportData() + ['report_focus' => 'revenue']);
    }

    public function occupancy()
    {
        return view('reports.index', $this->buildReportData() + ['report_focus' => 'occupancy']);
    }

    public function export()
    {
        $data = $this->buildReportData();
        $total_rooms = $data['total_rooms'];
        $occupied_rooms = $data['occupied_rooms'];
        $available_rooms = $data['available_rooms'];
        $maintenance_rooms = $data['maintenance_rooms'];
        $total_bookings = $data['total_bookings'];
        $pending_bookings = $data['pending_bookings'];
        $confirmed_bookings = $data['confirmed_bookings'];
        $cancelled = $data['cancelled'];
        $occupancy_rate = $data['occupancy_rate'];
        $total_revenue = $data['total_revenue'];


        $filename = 'report_' . date('Ymd_His') . '.xlsx';

        $rows = [
            ['Report Summary (สรุปรายงาน)'],
            ['Generated At (วันที่สร้างรายงาน)', now()->format('d/m/Y H:i')],
            [],
            ['Category (หมวด)', 'Metric (รายการ)', 'Value (ค่า)'],

            ['Room (ห้อง)', 'Total Rooms (ห้องทั้งหมด)', $total_rooms],
            ['Room (ห้อง)', 'Available Rooms (ห้องว่าง)', $available_rooms],
            ['Room (ห้อง)', 'Occupied Rooms (ห้องมีผู้พัก)', $occupied_rooms],
            ['Room (ห้อง)', 'Maintenance Rooms (ห้องซ่อมบำรุง)', $maintenance_rooms],
            ['Room (ห้อง)', 'Occupancy Rate (อัตราการเข้าพัก)', $occupancy_rate . '%'],

            [],

            ['Booking (การจอง)', 'Total Bookings (การจองทั้งหมด)', $total_bookings],
            ['Booking (การจอง)', 'Pending (รอดำเนินการ)', $pending_bookings],
            ['Booking (การจอง)', 'Confirmed (ยืนยันแล้ว)', $confirmed_bookings],
            ['Booking (การจอง)', 'Cancelled (ยกเลิก)', $cancelled],

            [],
            ['Revenue (รายได้)', 'Total Revenue (รายได้รวม)', number_format($total_revenue, 2)],
        ];

        return xlsx_download($filename, $rows);
    }
}

