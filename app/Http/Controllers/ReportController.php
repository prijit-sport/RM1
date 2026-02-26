<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
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

        $bookings_this_month = Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $popular_rooms = Room::withCount('bookings')
            ->orderBy('bookings_count', 'DESC')
            ->limit(5)
            ->get();

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
            'Content-Type' => 'text/csv; charset=UTF-16LE',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use (
            $total_rooms,
            $occupied_rooms,
            $available_rooms,
            $maintenance_rooms,
            $total_bookings,
            $pending_bookings,
            $confirmed_bookings,
            $checked_in,
            $checked_out,
            $cancelled,
            $occupancy_rate,
            $total_revenue
        ) {
            $rows = [
                ['รายงานสถิติและรายได้'],
                ['วันที่', now()->format('d/m/Y H:i')],
                [],
                ['หมวด', 'รายการ', 'จำนวน'],
                ['ห้อง', 'ห้องทั้งหมด', $total_rooms],
                ['ห้อง', 'ห้องว่าง', $available_rooms],
                ['ห้อง', 'ห้องใช้งาน', $occupied_rooms],
                ['ห้อง', 'ระหว่างซ่อม', $maintenance_rooms],
                ['ห้อง', 'อัตราการเข้าพัก', $occupancy_rate . '%'],
                [],
                ['การจอง', 'ทั้งหมด', $total_bookings],
                ['การจอง', 'รอการยืนยัน', $pending_bookings],
                ['การจอง', 'ยืนยันแล้ว', $confirmed_bookings],
                ['การจอง', 'เช็คอินแล้ว', $checked_in],
                ['การจอง', 'เช็คเอาท์แล้ว', $checked_out],
                ['การจอง', 'ยกเลิก', $cancelled],
                [],
                ['รายได้', 'รายได้รวม', number_format($total_revenue, 2) . ' บาท'],
            ];

            $file = fopen('php://output', 'wb');
            fwrite($file, chr(0xFF) . chr(0xFE));

            foreach ($rows as $row) {
                $line = '"' . implode('","', array_map(function ($value) {
                    return str_replace('"', '""', (string) $value);
                }, $row)) . '"' . "\r\n";

                fwrite($file, mb_convert_encoding($line, 'UTF-16LE', 'UTF-8'));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
