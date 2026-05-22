<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Contract;
use App\Models\Facility;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Maintenance;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * ReportService
 * * ✅ NEW FILE - ย้ายมาจาก ReportController
 * Handles all report data aggregation and calculations
 * * Lines: 450+ (moved from controller)
 */
class ReportService
{
    /**
     * Build complete report data with all 8 sections
     */
    public function buildReportData(): array
    {
        return array_merge(
            $this->getOverviewData(),
            $this->getFinancialData(),
            $this->getRoomsData(),
            $this->getGuestsData(),
            $this->getContractsData(),
            $this->getMetersData(),
            $this->getMaintenanceData(),
            $this->getFacilitiesData()
        );
    }

    /**
     * ═══════════════════════════════════════════════════════
     * 1) OVERVIEW - ภาพรวมห้อง การจอง รายได้
     * ═══════════════════════════════════════════════════════
     */
    private function getOverviewData(): array
    {
        $total_rooms = Room::count();
        $occupied_rooms = Room::where('status', 'occupied')->count();
        $available_rooms = Room::where('status', 'available')->count();
        $maintenance_rooms = Room::where('status', 'maintenance')->count();

        $occupancy_rate = $total_rooms > 0
            ? round(($occupied_rooms / $total_rooms) * 100, 2)
            : 0;

        $total_bookings = Booking::count();
        $pending_bookings = Booking::where('status', 'pending')->count();
        $confirmed_bookings = Booking::where('status', 'confirmed')->count();
        $cancelled = Booking::where('status', 'cancelled')->count();
        $bookings_this_month = Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();

        $total_revenue = Booking::where('status', '!=', 'cancelled')->sum('total_price');

        return compact(
            'total_rooms', 'occupied_rooms', 'available_rooms', 'maintenance_rooms',
            'occupancy_rate', 'total_bookings', 'pending_bookings', 'confirmed_bookings',
            'cancelled', 'bookings_this_month', 'total_revenue'
        );
    }

    /**
     * ═══════════════════════════════════════════════════════
     * 2) FINANCIAL - รายได้ invoices
     * ═══════════════════════════════════════════════════════
     */
    private function getFinancialData(): array
    {
        // รายได้รายเดือน 12 เดือนย้อนหลัง
        $monthly_revenue = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthly_revenue->push([
                'label' => $date->format('M Y'),
                'value' => (float)Booking::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->where('status', '!=', 'cancelled')
                    ->sum('total_price'),
            ]);
        }

        // รายได้แยกประเภทห้อง
        $revenue_by_room_type = Booking::join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->where('bookings.status', '!=', 'cancelled')
            ->selectRaw('rooms.room_type, SUM(bookings.total_price) as total')
            ->groupBy('rooms.room_type')
            ->pluck('total', 'room_type')
            ->toArray();

        // Invoices stats
        $invoices_paid_amount = 0;
        $invoices_pending_amount = 0;
        $invoices_overdue_amount = 0;
        $top_overdue_guests = collect();

        try {
            $invoices_paid_amount = (float)Invoice::where('status', 'paid')->sum('total');
            $invoices_pending_amount = (float)Invoice::whereIn('status', ['pending', 'sent'])->sum('total');
            $invoices_overdue_amount = (float)Invoice::where('status', 'overdue')->sum('total');

            $top_overdue_guests = Invoice::with('guest')
                ->where('status', 'overdue')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
        } catch (\Throwable $e) {
            \Log::error('[ReportService] getFinancialData invoices query failed', [
                'error' => $e->getMessage(),
            ]);
        }

        return compact(
            'monthly_revenue', 'revenue_by_room_type',
            'invoices_paid_amount', 'invoices_pending_amount', 'invoices_overdue_amount',
            'top_overdue_guests'
        );
    }

    /**
     * ═══════════════════════════════════════════════════════
     * 3) ROOMS - ข้อมูลห้องพัก
     * ═══════════════════════════════════════════════════════
     */
    private function getRoomsData(): array
    {
        $rooms_by_type = Room::selectRaw('room_type, COUNT(*) as count')
            ->groupBy('room_type')
            ->pluck('count', 'room_type')
            ->toArray();

        // ตัดการดึงข้อมูลจากคอลัมน์ zone ออก เพื่อแก้ปัญหา Error Column not found
        $rooms_by_zone = [];

        // Top 5 ห้องทำรายได้สูงสุด
        $top_revenue_rooms = Room::withSum([
            'bookings as revenue' => function ($q) {
                $q->where('status', '!=', 'cancelled');
            }
        ], 'total_price')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $popular_rooms = Room::withCount('bookings')
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

        return compact('rooms_by_type', 'rooms_by_zone', 'top_revenue_rooms', 'popular_rooms');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * 4) GUESTS - ข้อมูลผู้เช่า
     * ═══════════════════════════════════════════════════════
     */
    private function getGuestsData(): array
    {
        $total_guests = 0;
        $new_guests_this_month = 0;
        $guests_per_month = collect();
        $top_loyal_guests = collect();

        try {
            $total_guests = Guest::count();
            $new_guests_this_month = Guest::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count();

            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $guests_per_month->push([
                    'label' => $date->format('M'),
                    'value' => Guest::whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count(),
                ]);
            }

            $top_loyal_guests = Guest::withCount('bookings')
                ->orderByDesc('bookings_count')
                ->limit(10)
                ->get();
        } catch (\Throwable $e) {
            \Log::error('[ReportService] getGuestsData failed', ['error' => $e->getMessage()]);
        }

        return compact('total_guests', 'new_guests_this_month', 'guests_per_month', 'top_loyal_guests');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * 5) CONTRACTS - ข้อมูลสัญญา
     * ═══════════════════════════════════════════════════════
     */
    private function getContractsData(): array
    {
        $contracts_active = 0;
        $contracts_expired = 0;
        $contracts_pending = 0;
        $contracts_expiring_30 = collect();
        $contracts_expiring_60 = 0;
        $contracts_expiring_90 = 0;

        try {
            $contracts_active = Contract::where('status', 'active')->count();
            $contracts_expired = Contract::where('status', 'expired')->count();
            $contracts_pending = Contract::where('status', 'pending')->count();

            $contracts_expiring_30 = Contract::with(['guest', 'room'])
                ->where('status', 'active')
                ->whereDate('end_date', '>=', now())
                ->whereDate('end_date', '<=', now()->addDays(30))
                ->orderBy('end_date')
                ->get();

            $contracts_expiring_60 = Contract::where('status', 'active')
                ->whereDate('end_date', '>', now()->addDays(30))
                ->whereDate('end_date', '<=', now()->addDays(60))
                ->count();

            $contracts_expiring_90 = Contract::where('status', 'active')
                ->whereDate('end_date', '>', now()->addDays(60))
                ->whereDate('end_date', '<=', now()->addDays(90))
                ->count();
        } catch (\Throwable $e) {
            \Log::error('[ReportService] getContractsData failed', ['error' => $e->getMessage()]);
        }

        return compact(
            'contracts_active', 'contracts_expired', 'contracts_pending',
            'contracts_expiring_30', 'contracts_expiring_60', 'contracts_expiring_90'
        );
    }

    /**
     * ═══════════════════════════════════════════════════════
     * 6) METERS - ข้อมูลมิเตอร์ไฟฟ้า น้ำ
     * ═══════════════════════════════════════════════════════
     */
    private function getMetersData(): array
    {
        $current_month_electric = 0;
        $current_month_water = 0;
        $top_electric_rooms = collect();
        $top_water_rooms = collect();

        try {
            // คิดไฟฟ้า
            $current_month_electric = (float)MeterReading::join('meters', 'meter_readings.meter_id', '=', 'meters.id')
                ->where('meters.type', 'electric')
                ->whereMonth('meter_readings.reading_date', now()->month)
                ->whereYear('meter_readings.reading_date', now()->year)
                ->sum('meter_readings.reading_value');

            // คิดน้ำ
            $current_month_water = (float)MeterReading::join('meters', 'meter_readings.meter_id', '=', 'meters.id')
                ->where('meters.type', 'water')
                ->whereMonth('meter_readings.reading_date', now()->month)
                ->whereYear('meter_readings.reading_date', now()->year)
                ->sum('meter_readings.reading_value');

            // Top 5 ห้องใช้ไฟฟ้าเยอะสุด
            $top_electric_rooms = $this->getTopUtilityRooms('electric');

            // Top 5 ห้องใช้น้ำเยอะสุด
            $top_water_rooms = $this->getTopUtilityRooms('water');
        } catch (\Throwable $e) {
            \Log::error('[ReportService] getMetersData failed', ['error' => $e->getMessage()]);
        }

        return compact('current_month_electric', 'current_month_water', 'top_electric_rooms', 'top_water_rooms');
    }

    /**
     * Helper: Get top utility consuming rooms
     * ✅ Refactored: Removed code duplication
     */
    private function getTopUtilityRooms(string $type): Collection
    {
        return MeterReading::join('meters', 'meter_readings.meter_id', '=', 'meters.id')
            ->join('rooms', 'meters.room_id', '=', 'rooms.id')
            ->where('meters.type', $type)
            ->where('meter_readings.reading_value', '>', 0)
            ->whereNotNull('meter_readings.reading_value')
            ->whereMonth('meter_readings.reading_date', now()->month)
            ->whereYear('meter_readings.reading_date', now()->year)
            ->selectRaw('rooms.room_number, SUM(meter_readings.reading_value) as total')
            ->groupBy('rooms.id', 'rooms.room_number')
            ->havingRaw('SUM(meter_readings.reading_value) > 0')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

    /**
     * ═══════════════════════════════════════════════════════
     * 7) MAINTENANCE - ข้อมูลซ่อมบำรุง
     * ═══════════════════════════════════════════════════════
     */
    private function getMaintenanceData(): array
    {
        $maint_pending = 0;
        $maint_in_progress = 0;
        $maint_completed = 0;
        $maint_cancelled = 0;
        $maint_types = [];
        $maint_cost_this_month = 0;

        try {
            $maint_pending = Maintenance::where('status', 'pending')->count();
            $maint_in_progress = Maintenance::where('status', 'in_progress')->count();
            $maint_completed = Maintenance::where('status', 'completed')->count();
            $maint_cancelled = Maintenance::where('status', 'cancelled')->count();

            $maint_types = Maintenance::whereNotNull('maintenance_type')
                ->selectRaw('maintenance_type, COUNT(*) as cnt')
                ->groupBy('maintenance_type')
                ->orderByDesc('cnt')
                ->pluck('cnt', 'maintenance_type')
                ->toArray();

            $maint_cost_this_month = (float)Maintenance::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('cost');
        } catch (\Throwable $e) {
            \Log::error('[ReportService] getMaintenanceData failed', ['error' => $e->getMessage()]);
        }

        return compact(
            'maint_pending', 'maint_in_progress', 'maint_completed', 'maint_cancelled',
            'maint_types', 'maint_cost_this_month'
        );
    }

    /**
     * ═══════════════════════════════════════════════════════
     * 8) FACILITIES - ข้อมูลเฟอร์นิเจอร์
     * ═══════════════════════════════════════════════════════
     */
    private function getFacilitiesData(): array
    {
        $fac_status = [];
        $fac_total = 0;
        $fac_upcoming_maint = collect();

        try {
            $fac_total = Facility::count();
            $fac_status = Facility::selectRaw('status, COUNT(*) as cnt')
                ->groupBy('status')
                ->pluck('cnt', 'status')
                ->toArray();

            $fac_upcoming_maint = Facility::whereNotNull('next_maintenance_date')
                ->whereDate('next_maintenance_date', '>=', now())
                ->whereDate('next_maintenance_date', '<=', now()->addDays(30))
                ->orderBy('next_maintenance_date')
                ->limit(10)
                ->get();
        } catch (\Throwable $e) {
            \Log::error('[ReportService] getFacilitiesData failed', ['error' => $e->getMessage()]);
        }

        return compact('fac_status', 'fac_total', 'fac_upcoming_maint');
    }

    /**
     * Format report data for Excel export
     * ✅ Refactored: Moved from controller
     */
    public function formatForExport(array $data): array
    {
        $rows = [
            ['📊 รายงานสรุปประสิทธิภาพธุรกิจ'],
            ['วันที่สร้างรายงาน', now()->format('d/m/Y H:i')],
            [],
            ['Category', 'Metric', 'Value'],

            // ===== ห้องพัก =====
            ['ห้องพัก', 'ห้องทั้งหมด', $data['total_rooms'] ?? 0],
            ['ห้องพัก', 'ห้องว่าง', $data['available_rooms'] ?? 0],
            ['ห้องพัก', 'ห้องมีผู้พัก', $data['occupied_rooms'] ?? 0],
            ['ห้องพัก', 'ห้องซ่อมบำรุง', $data['maintenance_rooms'] ?? 0],
            ['ห้องพัก', 'อัตราการเข้าพัก', ($data['occupancy_rate'] ?? 0) . '%'],
            [],

            // ===== การจอง =====
            ['การจอง', 'การจองทั้งหมด', $data['total_bookings'] ?? 0],
            ['การจอง', 'รอดำเนินการ', $data['pending_bookings'] ?? 0],
            ['การจอง', 'ยืนยันแล้ว', $data['confirmed_bookings'] ?? 0],
            ['การจอง', 'ยกเลิก', $data['cancelled'] ?? 0],
            ['การจอง', 'การจองในเดือนนี้', $data['bookings_this_month'] ?? 0],
            [],

            // ===== รายได้ =====
            ['รายได้', 'รายได้รวมทั้งหมด', number_format($data['total_revenue'] ?? 0, 2)],
            ['รายได้', 'รายได้ใบแจ้งหนี้ (จ่ายแล้ว)', number_format($data['invoices_paid_amount'] ?? 0, 2)],
            ['รายได้', 'รายได้ค้างชำระ', number_format($data['invoices_overdue_amount'] ?? 0, 2)],
            ['รายได้', 'รายได้รอชำระ', number_format($data['invoices_pending_amount'] ?? 0, 2)],
            [],

            // ===== สัญญา =====
            ['สัญญา', 'สัญญา Active', $data['contracts_active'] ?? 0],
            ['สัญญา', 'สัญญาหมดอายุแล้ว', $data['contracts_expired'] ?? 0],
            ['สัญญา', 'ใกล้หมดอายุ 30 วัน', is_countable($data['contracts_expiring_30'] ?? []) ? count($data['contracts_expiring_30']) : 0],
            ['สัญญา', 'ใกล้หมดอายุ 60 วัน', $data['contracts_expiring_60'] ?? 0],
            ['สัญญา', 'ใกล้หมดอายุ 90 วัน', $data['contracts_expiring_90'] ?? 0],
            [],

            // ===== ผู้เช่า =====
            ['ผู้เช่า', 'ผู้เช่าทั้งหมด', $data['total_guests'] ?? 0],
            ['ผู้เช่า', 'ผู้เช่าใหม่ในเดือนนี้', $data['new_guests_this_month'] ?? 0],
            [],

            // ===== ซ่อมบำรุง =====
            ['ซ่อมบำรุง', 'รอดำเนินการ', $data['maint_pending'] ?? 0],
            ['ซ่อมบำรุง', 'กำลังดำเนินการ', $data['maint_in_progress'] ?? 0],
            ['ซ่อมบำรุง', 'เสร็จแล้ว', $data['maint_completed'] ?? 0],
            ['ซ่อมบำรุง', 'ค่าใช้จ่ายรวมเดือนนี้', number_format($data['maint_cost_this_month'] ?? 0, 2)],
            [],

            // ===== มิเตอร์ =====
            ['มิเตอร์', 'ไฟฟ้าใช้เดือนนี้ (kWh)', number_format($data['current_month_electric'] ?? 0, 2)],
            ['มิเตอร์', 'น้ำใช้เดือนนี้ (m³)', number_format($data['current_month_water'] ?? 0, 2)],
            [],

            // ===== เฟอร์นิเจอร์ =====
            ['เฟอร์นิเจอร์', 'ทั้งหมด', $data['fac_total'] ?? 0],
        ];

        return $rows;
    }
}