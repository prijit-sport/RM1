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

    public function create()
    {
        $this->authorize('create', Booking::class);

        $rooms = Room::where('status', 'available')->get();
        $guests = Guest::all();

        return view('bookings.create', compact('rooms', 'guests'));
    }

    public function store(StoreBookingRequest $request)
    {
        $this->authorize('create', Booking::class);

        $this->bookingService->create($request->validated());

        return redirect()->route('bookings.index')->with('success', __('ui.booking.created'));
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);

        $rooms = Room::all();
        $guests = Guest::all();

        return view('bookings.edit', compact('booking', 'rooms', 'guests'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $this->bookingService->update($booking, $request->validated());

        return redirect()->route('bookings.show', $booking)->with('success', __('ui.booking.updated'));
    }

    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);

        $this->bookingService->destroy($booking);

        return redirect()->route('bookings.index')->with('success', __('ui.booking.deleted'));
    }

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
                __('ui.booking.export_total'),
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
                        $booking->total_price,
                        $booking->status,
                        $booking->notes ?? '-',
                    ];
                }
            });

        return xlsx_download($filename, $rows);
    }

    public function confirm(Booking $booking)
    {
        $this->authorize('confirm', $booking);

        $this->bookingService->confirm($booking);

        return redirect()->route('bookings.show', $booking)->with('success', __('ui.booking.confirmed'));
    }

    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);

        $this->bookingService->cancel($booking);

        return redirect()->route('bookings.show', $booking)->with('success', __('ui.booking.cancelled'));
    }
}
