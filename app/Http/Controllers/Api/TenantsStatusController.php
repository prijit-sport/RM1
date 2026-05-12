<?php
 
namespace App\Http\Controllers;
 
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
 
/**
 * TenantsStatusController
 * รวมรายชื่อผู้เช่า + สถานะห้อง + สถานะบิล ในหน้าเดียว
 *
 * ⚠️ สมมุติฐาน schema (ปรับได้):
 * - rooms.room_type   : เก็บค่า "แอร์" / "พัดลม" (ภาษาไทย)
 *   ถ้าระบบเก็บเป็น 'air' / 'fan' ภาษาอังกฤษ ให้ค้นหาคำว่า "🔧 [ปรับตรงนี้]" แล้วแก้
 * - rooms.zone        : โซน เก็บเป็นข้อความหรือเลข
 * - rooms.price (หรือ rooms.monthly_rent) : ราคาต่อเดือน
 * - bookings.status   : ใช้ค่า 'checked_in' สำหรับผู้เช่าปัจจุบัน
 * - bookings.check_in_date : วันที่เข้าพัก
 * - invoices.status   : 'paid' / 'pending' / 'overdue'
 * - invoices.due_date : วันที่ครบกำหนดชำระ
 */
class TenantsStatusController extends Controller
{
    public function index(Request $request)
    {
        $today        = Carbon::today();
        $currentMonth = $today->month;
        $currentYear  = $today->year;
 
        // ── Query หลัก: ผู้เช่าที่กำลังพักอยู่ ──
        $query = Booking::with(['guest', 'room', 'invoices'])
            ->where('status', 'checked_in');  // 🔧 [ปรับตรงนี้] ถ้าใช้สถานะอื่น เช่น 'active'
 
        // ── Filter โซน ──
        if ($request->filled('zone')) {
            $query->whereHas('room', fn ($q) => $q->where('zone', $request->input('zone')));
        }
 
        // ── ค้นหา ──
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->whereHas('guest', function ($g) use ($s) {
                    $g->where('first_name', 'like', "%{$s}%")
                      ->orWhere('last_name', 'like', "%{$s}%");
                })
                ->orWhereHas('room', fn ($r) => $r->where('room_number', 'like', "%{$s}%"));
            });
        }
 
        $tenants = $query->orderBy('room_id')->paginate(15)->withQueryString();
 
        // ── สถิติ ──
        $activeQuery   = Booking::where('status', 'checked_in');
        $totalTenants  = (clone $activeQuery)->count();
        $acRoomsCount  = (clone $activeQuery)
            ->whereHas('room', fn ($q) => $q->where('room_type', 'like', '%แอร์%'))  // 🔧 [ปรับตรงนี้]
            ->count();
        $fanRoomsCount = (clone $activeQuery)
            ->whereHas('room', fn ($q) => $q->where('room_type', 'like', '%พัดลม%'))  // 🔧 [ปรับตรงนี้]
            ->count();
 
        // ── สถิติบิล (ใช้ try/catch กันกรณี schema ของ invoice ต่างจากที่คิด) ──
        try {
            $paidThisMonthCount = \App\Models\Invoice::where('status', 'paid')
                ->whereMonth('updated_at', $currentMonth)
                ->whereYear('updated_at', $currentYear)
                ->count();
 
            $overdueCount = \App\Models\Invoice::where('status', '!=', 'paid')
                ->whereDate('due_date', '<', $today)
                ->count();
        } catch (\Throwable $e) {
            $paidThisMonthCount = 0;
            $overdueCount       = 0;
        }
 
        // ── โซนทั้งหมดสำหรับ dropdown ──
        $zones = Room::query()
            ->whereNotNull('zone')
            ->distinct()
            ->orderBy('zone')
            ->pluck('zone');
 
        return view('tenants-status.index', compact(
            'tenants',
            'totalTenants',
            'acRoomsCount',
            'fanRoomsCount',
            'paidThisMonthCount',
            'overdueCount',
            'zones',
            'today'
        ));
    }
}
 