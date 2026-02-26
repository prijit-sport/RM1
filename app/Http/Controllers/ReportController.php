<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\Guest;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // สถิติทั่วไป
        $total_rooms = Room::count();
        $occupied_rooms = Room::where('status', 'occupied')->count();
        $available_rooms = Room::where('status', 'available')->count();
        $maintenance_rooms = Room::where('status', 'maintenance')->count();

        // สถิติการจอง
        $total_bookings = Booking::count();
        $pending_bookings = Booking::where('status', 'pending')->count();
        $confirmed_bookings = Booking::where('status', 'confirmed')->count();
        $checked_in = Booking::where('status', 'checked_in')->count();
        $checked_out = Booking::where('status', 'checked_out')->count();
        $cancelled = Booking::where('status', 'cancelled')->count();

        // อัตราการเข้าพัก
        $occupancy_rate = $total_rooms > 0 ? round(($occupied_rooms / $total_rooms) * 100, 2) : 0;

        // รายได้รวม
        $total_revenue = Booking::where('status', '!=', 'cancelled')->sum('total_price');

        // การจองเดือนนี้
        $bookings_this_month = Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ห้องยอดนิยม
        $popular_rooms = Room::withCount('bookings')
            ->orderBy('bookings_count', 'DESC')
            ->limit(5)
            ->get();

        // ประจำวัน
        $today_check_ins = Booking::whereDate('check_in_date', today())->count();
        $today_check_outs = Booking::whereDate('check_out_date', today())->count();

        return view('reports.index', compact(
            'total_rooms',
            'occupied_rooms',
            'available_rooms',
            'maintenance_rooms',
            'total_bookings',
            'pending_bookings',
            'confirmed_bookings',
            'checked_in',
            'checked_out',
            'cancelled',
            'occupancy_rate',
            'total_revenue',
            'bookings_this_month',
            'popular_rooms',
            'today_check_ins',
            'today_check_outs'
        ));
    }

    public function export(Request $request)
    {
        $total_rooms = Room::count();
        $occupied_rooms = Room::where('status', 'occupied')->count();
        $available_rooms = Room::where('status', 'available')->count();
        $maintenance_rooms = Room::where('status', 'maintenance')->count();
        $total_bookings = Booking::count();
        $pending_bookings = Booking::where('status', 'pending')->count();
        $confirmed_bookings = Booking::where('status', 'confirmed')->count();
        $checked_in = Booking::where('status', 'checked_in')->count();
        $checked_out = Booking::where('status', 'checked_out')->count();
        $cancelled = Booking::where('status', 'cancelled')->count();
        $occupancy_rate = $total_rooms > 0 ? round(($occupied_rooms / $total_rooms) * 100, 2) : 0;
        $total_revenue = Booking::where('status', '!=', 'cancelled')->sum('total_price');

        $filename = 'report_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use (
            $total_rooms, $occupied_rooms, $available_rooms, $maintenance_rooms,
            $total_bookings, $pending_bookings, $confirmed_bookings,
            $checked_in, $checked_out, $cancelled, $occupancy_rate, $total_revenue
        ) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['รายงานสถิติและรายได้']);
            fputcsv($file, ['วันที่', now()->format('d/m/Y H:i')]);
            fputcsv($file, []);
            fputcsv($file, ['หมวด', 'รายการ', 'จำนวน']);
            fputcsv($file, ['ห้อง', 'ห้องทั้งหมด', $total_rooms]);
            fputcsv($file, ['ห้อง', 'ห้องว่าง', $available_rooms]);
            fputcsv($file, ['ห้อง', 'ห้องใช้งาน', $occupied_rooms]);
            fputcsv($file, ['ห้อง', 'ระหว่างซ่อม', $maintenance_rooms]);
            fputcsv($file, ['ห้อง', 'อัตราการเข้าพัก', $occupancy_rate . '%']);
            fputcsv($file, []);
            fputcsv($file, ['การจอง', 'ทั้งหมด', $total_bookings]);
            fputcsv($file, ['การจอง', 'รอการยืนยัน', $pending_bookings]);
            fputcsv($file, ['การจอง', 'ยืนยันแล้ว', $confirmed_bookings]);
            fputcsv($file, ['การจอง', 'เช็คอินแล้ว', $checked_in]);
            fputcsv($file, ['การจอง', 'เช็คเอาท์แล้ว', $checked_out]);
            fputcsv($file, ['การจอง', 'ยกเลิก', $cancelled]);
            fputcsv($file, []);
            fputcsv($file, ['รายได้', 'รายได้รวม', number_format($total_revenue, 2) . ' บาท']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
