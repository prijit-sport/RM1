<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Guest;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Services\ContractService;
use Illuminate\Validation\ValidationException;

class ContractFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_contract_sets_room_to_occupied(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));

        $room = $this->createRoom('R-101', 'available');
        $guest = $this->createGuest('John', 'Doe', 'john@example.com', 'ID-1001');

        $payload = [
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'title' => 'สัญญาเช่าห้องพัก',
            'contract_date' => Carbon::today()->toDateString(),
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::today()->addMonth()->toDateString(),
            'monthly_rent' => 1200,
            'deposit' => 1200,
            'advance_payment_months' => 1,
            'advance_payment' => 1200,
            'status' => 'active',
            'terms' => 'test',
        ];

        $response = $this->post(route('contracts.store'), $payload);
        $response->assertRedirect(route('contracts.index'));

        $this->assertSame('occupied', $room->fresh()->status);
        $this->assertDatabaseHas('contracts', [
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'status' => 'active',
        ]);
    }

    public function test_terminate_contract_sets_room_to_available(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));

        $room = $this->createRoom('R-102', 'occupied');
        $guest = $this->createGuest('Jane', 'Smith', 'jane@example.com', 'ID-1002');

        /** @var Contract $contract */
        $contract = Contract::create([
            'contract_number' => 'CNT0001',
            'title' => 'terminate test',
            'contract_date' => Carbon::today()->toDateString(),
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::today()->addMonth()->toDateString(),
            'monthly_rent' => 1200,
            'deposit' => 1200,
            'advance_payment' => 1200,
            'advance_payment_months' => 1,
            'status' => 'active',
            'terms' => 'test',
            'room_id' => $room->id,
            'guest_id' => $guest->id,
        ]);

        $service = new ContractService();
        $service->cancel($contract);

        $this->assertSame('available', $room->fresh()->status);

        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_cannot_create_contract_for_occupied_room(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));

        $room = $this->createRoom('R-103', 'occupied');
        $guest = $this->createGuest('Paul', 'Green', 'paul@example.com', 'ID-1003');

        $payload = [
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'title' => 'สัญญาเช่าห้องพัก',
            'contract_date' => Carbon::today()->toDateString(),
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::today()->addMonth()->toDateString(),
            'monthly_rent' => 1200,
            'deposit' => 1200,
            'advance_payment_months' => 1,
            'advance_payment' => 1200,
            'status' => 'active',
            'terms' => 'test',
        ];

        // เนื่องจากใน code ที่อ่านมา ยังไม่เห็นเงื่อนไขห้ามสร้าง contract เมื่อ room ถูก occupied โดยตรง
        // เทสต์นี้จะยืนยันว่าอย่างน้อยต้องมี validation error หรือ session error
        // (ถ้าพฤติกรรมจริงเป็นอย่างอื่น เทสต์นี้อาจต้องปรับตาม behavior ของแอป)
        $response = $this->post(route('contracts.store'), $payload);

        $response->assertSessionHasErrors();
        $this->assertSame(0, Contract::query()->count());
    }

    public function test_contract_expiry_date_must_be_after_start_date(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));

        $room = $this->createRoom('R-104', 'available');
        $guest = $this->createGuest('Neo', 'Flow', 'neo@example.com', 'ID-1004');

        $start = Carbon::today()->toDateString();
        $end = Carbon::today()->subDay()->toDateString();

        $payload = [
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'title' => 'สัญญาเช่าห้องพัก',
            'contract_date' => Carbon::today()->toDateString(),
            'start_date' => $start,
            'end_date' => $end,
            'monthly_rent' => 1200,
            'deposit' => 1200,
            'advance_payment_months' => 1,
            'advance_payment' => 1200,
            'status' => 'active',
            'terms' => 'test',
        ];

        $this->post(route('contracts.store'), $payload)
            ->assertSessionHasErrors(['end_date']);

        $this->assertSame(0, Contract::query()->count());
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

