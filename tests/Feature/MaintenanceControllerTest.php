<?php

namespace Tests\Feature;

use App\Models\Maintenance;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_ok_for_authenticated_manager(): void
    {
        $this->actingAs($this->createUserWithRole('Manager'));

        $room = $this->createRoom('M102');
        $maintenance = $this->createMaintenance($room);

        $response = $this->get(route('maintenances.index'));

        $response->assertOk();
        $response->assertViewIs('maintenances.index');
        $response->assertSeeText('M102');
    }

    public function test_store_rejects_empty_payload_and_creates_no_record(): void
    {
        $user = $this->createUserWithRole('Admin');

        $response = $this->actingAs($user)
            ->from(route('maintenances.create'))
            ->post(route('maintenances.store'), []);

        $response->assertRedirect(route('maintenances.create'));

        $response->assertSessionHasErrors();

        $this->assertSame(0, Maintenance::count());
    }

    public function test_store_creates_maintenance_with_valid_data(): void
    {
        $user = $this->createUserWithRole('Manager');
        $room = $this->createRoom('M101');

        $response = $this->actingAs($user)
            ->post(route('maintenances.store'), [
                'room_id' => $room->id,
                'issue_type' => 'ไฟฟ้า',
                'reported_date' => Carbon::today()->toDateString(),
                'status' => 'pending',
                'description' => 'ทดสอบ',


            ]);

        $response->assertRedirect(route('maintenances.index'));
        $this->assertSame(1, Maintenance::count());
    }


    public function test_show_displays_existing_maintenance_details(): void
    {
        $this->actingAs($this->createUserWithRole('Manager'));

        $room = $this->createRoom('M103');
        $maintenance = $this->createMaintenance($room);

        $response = $this->get(route('maintenances.show', $maintenance));

        $response->assertOk();
        $response->assertViewIs('maintenances.show');
        $response->assertSeeText('M103');
    }

    public function test_unauthorized_user_gets_forbidden(): void
    {
        $this->actingAs($this->createUserWithRole('User'));

        $response = $this->get(route('maintenances.index'));

        $response->assertStatus(403);
    }

    private function createMaintenance(Room $room): Maintenance
    {
        return Maintenance::create([
            'room_id' => $room->id,
            'issue_type' => 'ไฟฟ้า',
            'reported_date' => Carbon::today()->toDateString(),
            'status' => 'pending',
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

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
