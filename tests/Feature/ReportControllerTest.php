<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Guest;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_ok_for_manager_or_admin(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));

        $response = $this->get(route('reports.index'));

        $response->assertOk();
        $response->assertViewIs('reports.index');
        $response->assertSeeText('รายงานสถิติและรายได้');
    }

    public function test_index_returns_forbidden_for_non_manager_or_admin(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => null]));

        $response = $this->get(route('reports.index'));

        $response->assertStatus(403);
    }

    public function test_unauthenticated_redirects_to_login(): void
    {
        $response = $this->get(route('reports.index'));
        $response->assertStatus(302);
    }

    public function test_dashboard_correctly_counts_expired_contracts(): void
    {
        $user = $this->createUserWithRole('Admin');

        $room = Room::create([
            'room_number' => 'EXP101',
            'room_type' => 'fan',
            'price_per_month' => 3000,
            'capacity' => 1,
            'status' => 'available',
        ]);

        $guest = Guest::create([
            'first_name' => 'Test',
            'last_name' => 'Expired',
            'email' => 'expired@example.com',
            'phone' => '0812345678',
            'id_number' => 'ID-EXP-001',
        ]);

        // สร้างสัญญาที่ end_date ผ่านไปแล้วแต่ status ยังเป็น active
        Contract::create([
            'contract_number' => 'EXP-TEST-001',
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->subDay(),
            'status' => 'active',
            'monthly_rent' => 3000,
        ]);

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertOk();
        $response->assertViewHas('contracts_expired', 1);
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}

