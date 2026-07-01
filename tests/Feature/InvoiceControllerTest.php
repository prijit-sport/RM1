<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_ok_for_authenticated_user(): void
    {
        $this->actingAs($this->createUserWithRole('Manager'));

        $invoice = $this->createInvoice();

        $response = $this->get(route('invoices.index'));

        $response->assertOk();
        $response->assertViewIs('invoices.index');
        $response->assertSeeText($invoice->invoice_number);
    }

    public function test_show_displays_existing_invoice_details(): void
    {
        $this->actingAs($this->createUserWithRole('Manager'));

        $invoice = $this->createInvoice();

        $response = $this->get(route('invoices.show', $invoice));

        $response->assertOk();
        $response->assertViewIs('invoices.show');
        $response->assertSeeText($invoice->invoice_number);
    }

    public function test_store_rejects_empty_payload_and_creates_no_record(): void
    {
        $user = $this->createUserWithRole('Admin');

        $response = $this->actingAs($user)
            ->from(route('invoices.create'))
            ->post(route('invoices.store'), []);

        $response->assertStatus(500);
        $this->assertSame(0, Invoice::count());
    }

    public function test_store_creates_invoice_with_valid_data(): void
    {
        $user = $this->createUserWithRole('Admin');
        $booking = $this->createBooking();

        $response = $this->actingAs($user)
            ->post(route('invoices.store'), [
                'booking_id' => $booking->id,
                'invoice_number' => 'INV-202601-00001',
                'amount' => 1000,
                'tax' => 70,
                'total' => 1070,
                'issue_date' => Carbon::today()->toDateString(),
                'due_date' => Carbon::today()->addDays(15)->toDateString(),
                'status' => 'sent',
                'notes' => 'Test invoice',
            ]);

        $response->assertRedirect(route('invoices.index'));
        $this->assertSame(1, Invoice::count());
    }

    public function test_mark_as_paid_changes_invoice_status_to_paid(): void
    {
        $this->actingAs($this->createUserWithRole('Manager'));

        $invoice = $this->createInvoice([
            'status' => 'sent',
            'amount' => 2000,
            'tax' => 140,
            'total' => 2140,
        ]);

        $response = $this->post(route('invoices.markAsPaid', $invoice));

        $response->assertRedirect(route('invoices.show', $invoice));
        $this->assertSame('paid', $invoice->fresh()->status);
        $this->assertSame('cash', $invoice->fresh()->payment_method);
        $this->assertNotNull($invoice->fresh()->payment_date);
    }

    public function test_unauthorized_user_gets_forbidden(): void
    {
        $user = $this->createUserWithRole('User');
        $invoice = $this->createInvoice();

        $response = $this->actingAs($user)
            ->get(route('invoices.index'));

        $response->assertStatus(403);
    }

    private function createInvoice(array $attributes = []): Invoice
    {
        $booking = array_key_exists('booking_id', $attributes) && $attributes['booking_id']
            ? Booking::find($attributes['booking_id'])
            : $this->createBooking();

        return Invoice::create(array_merge([
            'booking_id' => $booking->id,
            'guest_id' => $booking->guest_id,
            'room_id' => $booking->room_id,
            'invoice_number' => 'INV-' . now()->format('Ym') . '-00001',
            'amount' => 1000,
            'tax' => 70,
            'total' => 1070,
            'issue_date' => Carbon::today()->toDateString(),
            'due_date' => Carbon::today()->addDays(15)->toDateString(),
            'status' => 'sent',
            'notes' => 'Test invoice',
        ], $attributes));
    }

    private function createBooking(): Booking
    {
        $room = $this->createRoom('I101');
        $guest = $this->createGuest('Test', 'Guest', 'testguest@example.com', 'ID-9001');

        return Booking::create([
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addDays(5)->toDateString(),
            'rent_amount' => 1000,
            'deposit_amount' => 1000,
            'total_price' => 2000,
            'status' => 'confirmed',
            'notes' => 'Booking note',
        ]);
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
