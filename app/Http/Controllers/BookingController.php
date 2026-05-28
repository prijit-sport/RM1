<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService) {}

    // ─────────────────────────────────────────
    //  LIST
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('viewAny', Booking::class);

        $bookings = Booking::with(['room', 'guest'])
            ->when($request->filled('status'), function (Builder $query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $search = trim($request->string('search'));
                $query->where(function (Builder $subQuery) use ($search) {
                    $subQuery->whereHas('guest', function (Builder $guestQuery) use ($search) {
                        $guestQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })->orWhereHas('room', function (Builder $roomQuery) use ($search) {
                        $roomQuery->where('room_number', 'like', "%{$search}%");
                    });
                });
            })
            ->when($request->filled('room_type'), function (Builder $query) use ($request) {
                $query->whereHas('room', function (Builder $roomQuery) use ($request) {
                    $roomQuery->where('room_type', $request->string('room_type'));
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // ✅ แก้ room_type จาก 'air_conditioning' → 'air'
        $fanTotal = Room::where('room_type', 'fan')->count();
        $acTotal  = Room::where('room_type', 'air')->count();

        $availableFanRooms = Room::where('room_type', 'fan')
            ->where('status', 'available')
            ->orderBy('room_number')
            ->get(['id', 'room_number', 'price_per_month', 'zone']);

        $availableAcRooms = Room::where('room_type', 'air')
            ->where('status', 'available')
            ->orderBy('room_number')
            ->get(['id', 'room_number', 'price_per_month', 'zone']);

        return view('bookings.index', compact(
            'bookings',
            'fanTotal',
            'acTotal',
            'availableFanRooms',
            'availableAcRooms'
        ));
    }

    // ─────────────────────────────────────────
    //  CREATE FORM
    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Booking::class);

        // ✅ ดึงเฉพาะห้องที่ available (ว่างอยู่)
        $rooms = Room::where('status', 'available')
            ->orderBy('zone')
            ->orderBy('room_number')
            ->get(['id', 'room_number', 'room_type', 'price_per_month', 'status', 'zone']);

        $guests = Guest::orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        return view('bookings.create', compact('rooms', 'guests'));
    }

    // ─────────────────────────────────────────
    //  STORE
    // ─────────────────────────────────────────
    public function store(StoreBookingRequest $request)
    {
        $this->authorize('create', Booking::class);

        $booking = $this->bookingService->create($request->validated());

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', __('ui.booking.created'));
    }

    // ─────────────────────────────────────────
    //  SHOW (ดูรายละเอียด)
    // ─────────────────────────────────────────
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['room', 'guest']);

        return view('bookings.show', compact('booking'));
    }

    // ─────────────────────────────────────────
    //  CANCEL (ยกเลิกการจอง)
    // ─────────────────────────────────────────
    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);

        // 1. จำ ID ของห้องไว้ก่อนยกเลิกข้อมูลการจอง
        $roomId = $booking->room_id;

        // 2. ยกเลิกข้อมูลการจองผ่าน Service
        $this->bookingService->cancel($booking);

        // 3. บังคับอัปเดตตารางห้องพักให้เป็นว่างทันที
        if ($roomId) {
            Room::where('id', $roomId)->update(['status' => 'available']);
        }

        return redirect()
            ->route('bookings.index')
            ->with('success', 'ยกเลิกการจอง และคืนสถานะห้องกลับเป็นว่างเรียบร้อยแล้ว');
    }

    // ─────────────────────────────────────────
    //  CONFIRM
    // ─────────────────────────────────────────
    public function confirm(Booking $booking)
    {
        $this->authorize('confirm', $booking);

        $this->bookingService->confirm($booking);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', __('ui.booking.confirmed'));
    }

    // ─────────────────────────────────────────
    //  EXPORT
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $this->authorize('export', Booking::class);

        $filename = 'bookings_export_' . date('Y-m-d') . '.xlsx';

        $rows = [['ID', 'ห้อง', 'โซน', 'ผู้เช่า', 'วันเข้าพัก', 'ค่าเช่า', 'มัดจำ', 'มิเตอร์ไฟ', 'มิเตอร์น้ำ', 'สถานะ', 'หมายเหตุ']];

        Booking::with(['room', 'guest'])
            ->orderBy('id')
            ->chunk(500, function ($bookings) use (&$rows): void {
                foreach ($bookings as $booking) {
                    $rows[] = [
                        $booking->id,
                        $booking->room->room_number ?? '-',
                        $booking->room->zone ?? '-',
                        trim(($booking->guest->first_name ?? '') . ' ' . ($booking->guest->last_name ?? '')) ?: '-',
                        optional($booking->check_in_date)->format('d/m/Y'),
                        $booking->rent_amount ?? '-',
                        $booking->deposit_amount ?? '-',
                        $booking->electric_meter_start ?? '-',
                        $booking->water_meter_start ?? '-',
                        $booking->status_label,
                        $booking->notes ?? '-',
                    ];
                }
            });

        return xlsx_download($filename, $rows);
    }
}
