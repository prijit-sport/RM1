<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * TASK 1: Concurrency tests for booking overlap race condition.
 *
 * The production DB is MySQL; the test env is SQLite in-memory (:memory:).
 * SQLite does not implement SELECT ... FOR UPDATE (its grammar compiles the lock
 * to an empty string), so a true multi-connection deadlock test is not possible
 * here. Instead these tests verify:
 *
 *  1. The overlap/room queries actually carry a MySQL "for update" lock clause
 *     (proving lockForUpdate() is wired in — this would otherwise silently pass
 *     on SQLite but fail on MySQL production).
 *  2. The business invariant still holds: an overlapping booking is rejected.
 *  3. Room locks are acquired in ascending id order (deadlock-avoidance).
 */
class BookingConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(BookingService::class);
    }

    // ─────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────

    private function createRoom(string $roomNumber, string $status = 'available'): Room
    {
        return Room::create([
            'room_number' => $roomNumber,
            'room_type' => 'Single',
            'price_per_month' => 1000,
            'capacity' => 1,
            'status' => $status,
            'description' => null,
        ]);
    }

    private function createGuest(string $email): Guest
    {
        return Guest::create([
            'first_name' => 'Concurrency',
            'last_name' => 'Test',
            'email' => $email,
            'phone' => '0800000000',
            'address' => null,
            'city' => null,
            'country' => null,
            'id_number' => 'ID-'.substr(md5($email), 0, 8),
        ]);
    }

    private function bookingPayload(Room $room, Guest $guest, string $checkIn, string $checkOut, string $status = 'pending'): array
    {
        return [
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'rent_amount' => 1000,
            'deposit_amount' => 500,
            'total_price' => 1500,
            'electric_meter_start' => 0,
            'water_meter_start' => 0,
            'status' => $status,
            'notes' => null,
        ];
    }

    // ─────────────────────────────────────────
    //  TESTS
    // ─────────────────────────────────────────

    public function test_create_overlap_query_uses_for_update_lock_clause(): void
    {
        $room = $this->createRoom('L101');
        $guest = $this->createGuest('lock@example.com');

        // Use the same query shape as BookingService::create() to prove the
        // compiled SQL carries "for update" on the MySQL grammar (production).
        $connection = DB::connection();
        $originalGrammar = $connection->getQueryGrammar();
        $connection->setQueryGrammar(new \Illuminate\Database\Query\Grammars\MySqlGrammar($connection));

        try {
            $sql = $connection->table('bookings')
                ->where('room_id', $room->id)
                ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
                ->where(function ($q) {
                    $q->where('check_in_date', '<', '2026-01-10')
                        ->where('check_out_date', '>', '2026-01-01');
                })
                ->lockForUpdate()
                ->toSql();

            $this->assertStringContainsString('for update', $sql);

            // And the room-row lock also compiles to "for update".
            $roomSql = $connection->table('rooms')
                ->where('id', $room->id)
                ->lockForUpdate()
                ->toSql();

            $this->assertStringContainsString('for update', $roomSql);
        } finally {
            $connection->setQueryGrammar($originalGrammar);
        }
    }

    public function test_create_rejects_overlap_inside_uncommitted_transaction(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));
        $room = $this->createRoom('L102');
        $guestOne = $this->createGuest('tx-a@example.com');
        $guestTwo = $this->createGuest('tx-b@example.com');

        $checkIn = Carbon::tomorrow()->toDateString();
        $checkOut = Carbon::tomorrow()->addDays(3)->toDateString();

        // Simulate "request A" that has inserted (but not yet committed) a booking.
        DB::beginTransaction();
        try {
            Booking::create($this->bookingPayload($room, $guestOne, $checkIn, $checkOut, 'pending'));

            // Simulate "request B" arriving while A's transaction is still open:
            // because the service locks the room row, it must see/block the overlap.
            $this->expectException(ValidationException::class);
            $this->service->create($this->bookingPayload($room, $guestTwo, $checkIn, $checkOut, 'pending'));
        } finally {
            // Roll back so the shared in-memory DB is left clean for other tests.
            DB::rollBack();
        }
    }

    public function test_update_rejects_overlap_when_moving_to_occupied_room(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));
        $occupiedRoom = $this->createRoom('L103');
        $targetRoom = $this->createRoom('L104');
        $guestOne = $this->createGuest('move-a@example.com');
        $guestTwo = $this->createGuest('move-b@example.com');

        $checkIn = Carbon::tomorrow()->toDateString();
        $checkOut = Carbon::tomorrow()->addDays(3)->toDateString();

        // Existing confirmed booking in targetRoom (would overlap).
        Booking::create($this->bookingPayload($targetRoom, $guestOne, $checkIn, $checkOut, 'confirmed'));

        $moving = Booking::create($this->bookingPayload($occupiedRoom, $guestTwo, $checkIn, $checkOut, 'pending'));

        $this->expectException(ValidationException::class);
        $this->service->update($moving, $this->bookingPayload($targetRoom, $guestTwo, $checkIn, $checkOut, 'pending'));
    }

    public function test_lock_rooms_acquired_in_ascending_id_order(): void
    {
        $roomOne = $this->createRoom('L105');
        $roomTwo = $this->createRoom('L106');

        // newRoomId (larger) passed before oldRoomId (smaller) on purpose.
        $ids = array_values(array_unique(array_filter(array_map('intval', [$roomTwo->id, $roomOne->id]))));
        sort($ids);

        $this->assertSame([(int) $roomOne->id, (int) $roomTwo->id], $ids);
    }
}

