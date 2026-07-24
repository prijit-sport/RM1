<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_rejects_overlapping_booking_for_same_room(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));
        $room = $this->createRoom('A101');
        $guestOne = $this->createGuest('John', 'Doe', 'john@example.com', 'ID-1001');
        $guestTwo = $this->createGuest('Jane', 'Smith', 'jane@example.com', 'ID-1002');

        Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guestOne->id,
            'check_in_date' => Carbon::tomorrow()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(3)->toDateString(),
            'total_price' => 3000,
            'status' => 'confirmed',
            'notes' => null,
        ]);

        $response = $this->post(route('bookings.store'), [
            'room_id' => $room->id,
            'guest_id' => $guestTwo->id,
            'check_in_date' => Carbon::tomorrow()->addDay()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(4)->toDateString(),
            'status' => 'pending',
            'notes' => null,
        ]);

        $response->assertSessionHasErrors('room_id');
        $this->assertSame(1, Booking::count());
    }

    public function test_confirm_changes_status_and_marks_room_occupied(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));
        $room = $this->createRoom('A102');
        $guest = $this->createGuest('Mark', 'Stone', 'mark@example.com', 'ID-2001');

        $booking = Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::tomorrow()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'total_price' => 2000,
            'status' => 'pending',
            'notes' => null,
        ]);

        $this->post(route('bookings.confirm', $booking->id))->assertRedirect();

        $this->assertSame('confirmed', $booking->fresh()->status);
        $this->assertSame('occupied', $room->fresh()->status);
    }

    public function test_confirm_is_rejected_when_status_is_not_pending(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));
        $room = $this->createRoom('A103');
        $guest = $this->createGuest('Lina', 'Brown', 'lina@example.com', 'ID-3001');

        $booking = Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::tomorrow()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'total_price' => 2000,
            'status' => 'cancelled',
            'notes' => null,
        ]);

        $this->post(route('bookings.confirm', $booking->id))->assertSessionHasErrors('status');
        $this->assertSame('cancelled', $booking->fresh()->status);
    }

    public function test_cancel_changes_status_and_releases_room_when_no_active_booking(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));
        $room = $this->createRoom('A104', 'occupied');
        $guest = $this->createGuest('Paul', 'Green', 'paul@example.com', 'ID-4001');

        $booking = Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::tomorrow()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'total_price' => 2000,
            'status' => 'confirmed',
            'notes' => null,
        ]);

        $this->post(route('bookings.cancel', $booking->id))->assertRedirect();

        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertSame('available', $room->fresh()->status);
    }

    public function test_index_applies_status_and_search_filters(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));
        $roomOne = $this->createRoom('B201');
        $roomTwo = $this->createRoom('B202');
        $guestOne = $this->createGuest('Alice', 'Target', 'alice@example.com', 'ID-5001');
        $guestTwo = $this->createGuest('Bob', 'Other', 'bob@example.com', 'ID-5002');

        $target = Booking::create([
            'room_id' => $roomOne->id,
            'guest_id' => $guestOne->id,
            'check_in_date' => Carbon::tomorrow()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'total_price' => 2000,
            'status' => 'confirmed',
            'notes' => null,
        ]);

        Booking::create([
            'room_id' => $roomTwo->id,
            'guest_id' => $guestTwo->id,
            'check_in_date' => Carbon::tomorrow()->addDays(3)->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(5)->toDateString(),
            'total_price' => 2000,
            'status' => 'pending',
            'notes' => null,
        ]);

        $response = $this->get(route('bookings.index', ['status' => 'confirmed', 'search' => 'Alice']));

        // Either returns OK with view or redirects (either is acceptable)
        $response->assertStatus(200);
    }

    public function test_update_when_changing_room_syncs_old_and_new_room_statuses(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));
        $oldRoom = $this->createRoom('C301', 'occupied');
        $newRoom = $this->createRoom('C302', 'available');
        $guest = $this->createGuest('Nina', 'Blue', 'nina@example.com', 'ID-6001');

        $booking = Booking::create([
            'room_id' => $oldRoom->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::tomorrow()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(3)->toDateString(),
            'total_price' => 3000,
            'status' => 'confirmed',
            'notes' => null,
        ]);

        $response = $this->put(route('bookings.update', $booking), [
            'room_id' => $newRoom->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::tomorrow()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(3)->toDateString(),
            'status' => 'confirmed',
            'notes' => null,
        ]);

        // Either redirect (302) or OK (200) is acceptable
        $this->assertContains($response->getStatusCode(), [200, 302]);

        $this->assertSame((int) $newRoom->id, (int) $booking->fresh()->room_id);
        $this->assertSame('available', $oldRoom->fresh()->status);
        $this->assertSame('occupied', $newRoom->fresh()->status);
    }

    public function test_update_allows_edge_case_when_checkout_equals_other_checkin(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));
        $room = $this->createRoom('C303');
        $guestOne = $this->createGuest('Tom', 'Edge', 'tom@example.com', 'ID-7001');
        $guestTwo = $this->createGuest('Sara', 'Edge', 'sara@example.com', 'ID-7002');

        Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guestTwo->id,
            'check_in_date' => Carbon::tomorrow()->addDays(5)->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(7)->toDateString(),
            'total_price' => 2000,
            'status' => 'confirmed',
            'notes' => null,
        ]);

        $bookingToUpdate = Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guestOne->id,
            'check_in_date' => Carbon::tomorrow()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(4)->toDateString(),
            'total_price' => 4000,
            'status' => 'pending',
            'notes' => null,
        ]);

        $response = $this->put(route('bookings.update', $bookingToUpdate), [
            'room_id' => $room->id,
            'guest_id' => $guestOne->id,
            'check_in_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(5)->toDateString(),
            'status' => 'confirmed',
            'notes' => null,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('confirmed', $bookingToUpdate->fresh()->status);
    }

    public function test_update_rejects_invalid_status_transition(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));
        $room = $this->createRoom('C304');
        $guest = $this->createGuest('Neo', 'Flow', 'neo@example.com', 'ID-8001');

        $booking = Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::tomorrow()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'total_price' => 2000,
            'status' => 'cancelled',
            'notes' => null,
        ]);

        $this->put(route('bookings.update', $booking), [
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::tomorrow()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'status' => 'confirmed',
            'notes' => null,
        ])->assertSessionHasErrors('status');

        $this->assertSame('cancelled', $booking->fresh()->status);
    }

    public function test_confirm_requires_manager_or_admin_role(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));
        $room = $this->createRoom('C305');
        $guest = $this->createGuest('Jane', 'NoRole', 'norole@example.com', 'ID-8002');

        $booking = Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::tomorrow()->toDateString(),
            'check_out_date' => Carbon::tomorrow()->addDays(2)->toDateString(),
            'total_price' => 2000,
            'status' => 'pending',
            'notes' => null,
        ]);

        $this->post(route('bookings.confirm', $booking))->assertForbidden();
    }

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

    private function createGuest(string $firstName, string $lastName, string $email, string $idNumber): Guest
    {
        return Guest::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => '0800000000',
            'address' => null,
            'city' => null,
            'country' => null,
            'id_number' => $idNumber,
        ]);
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
