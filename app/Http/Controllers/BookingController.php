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
    public function __construct(private readonly BookingService $bookingService)
    {
    }
 
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
            ->latest('id')
            ->paginate(10)
            ->withQueryString();
 
        return view('bookings.index', compact('bookings'));
    }
 
    // ─────────────────────────────────────────
    //  CREATE FORM
    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Booking::class);
 
        // ส่งเฉพาะห้องว่าง พร้อม fields ที่ Blade ต้องการ
        $rooms = Room::where('status', 'available')
            ->orderBy('zone')
            ->orderBy('room_number')
            ->get(['id', 'zone', 'room_number', 'room_type', 'price_per_month', 'status']);
 
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
    //  SHOW
    // ─────────────────────────────────────────
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
 
        $booking->load(['room', 'guest']);
 
        return view('bookings.show', compact('booking'));
    }
 
    // ─────────────────────────────────────────
    //  EDIT FORM
    // ─────────────────────────────────────────
    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);
 
        // แสดงห้องว่าง + ห้องที่การจองนี้ใช้อยู่ (กรณีแก้ไข)
        $rooms = Room::where(function (Builder $query) use ($booking) {
                $query->where('status', 'available')
                      ->orWhere('id', $booking->room_id);
            })
            ->orderBy('zone')
            ->orderBy('room_number')
            ->get(['id', 'zone', 'room_number', 'room_type', 'price_per_month', 'status']);
 
        $guests = Guest::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
 
        return view('bookings.edit', compact('booking', 'rooms', 'guests'));
    }
 
    // ─────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $this->authorize('update', $booking);
 
        $this->bookingService->update($booking, $request->validated());
 
        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', __('ui.booking.updated'));
    }
 
    // ─────────────────────────────────────────
    //  DELETE
    // ─────────────────────────────────────────
    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);
 
        $this->bookingService->destroy($booking);
 
        return redirect()
            ->route('bookings.index')
            ->with('success', __('ui.booking.deleted'));
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
    //  CANCEL
    // ─────────────────────────────────────────
    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);
 
        $this->bookingService->cancel($booking);
 
        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', __('ui.booking.cancelled'));
    }
 
    // ─────────────────────────────────────────
    //  EXPORT
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $this->authorize('export', Booking::class);
 
        $filename = 'bookings_export_' . date('Y-m-d') . '.xlsx';
 
        $rows = [
            [
                __('ui.booking.export_id'),
                __('ui.booking.export_room'),
                __('ui.booking.export_guest'),
                __('ui.booking.export_check_in'),
                __('ui.booking.export_check_out'),
                __('ui.booking.export_rent'),
                __('ui.booking.export_deposit'),
                __('ui.booking.export_total'),
                __('ui.booking.export_electric_meter'),
                __('ui.booking.export_water_meter'),
                __('ui.booking.export_status'),
                __('ui.booking.export_notes'),
            ],
        ];
 
        Booking::with(['room', 'guest'])
            ->orderBy('id')
            ->chunk(500, function ($bookings) use (&$rows): void {
                foreach ($bookings as $booking) {
                    $rows[] = [
                        $booking->id,
                        $booking->room->room_number ?? '-',
                        trim(($booking->guest->first_name ?? '') . ' ' . ($booking->guest->last_name ?? '')) ?: '-',
                        optional($booking->check_in_date)->format('d/m/Y'),
                        optional($booking->check_out_date)->format('d/m/Y'),
                        $booking->rent_amount ?? '-',
                        $booking->deposit_amount ?? '-',
                        $booking->total_price ?? '-',
                        $booking->electric_meter_start ?? '-',
                        $booking->water_meter_start ?? '-',
                        $booking->status,
                        $booking->notes ?? '-',
                    ];
                }
            });
 
        return xlsx_download($filename, $rows);
    }
}