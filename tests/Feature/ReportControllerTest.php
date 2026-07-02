<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_ok_for_authenticated_user(): void
    {
        $this->actingAs($this->createUserWithRole('User'));

        $response = $this->get(route('reports.index'));

        $response->assertOk();
        $response->assertViewIs('reports.index');

        // อ่าน blade จริง: ต้องมีค่า total_revenue และ occupancy_rate
        $response->assertSeeText('รายงานสถิติและรายได้');
    }

    public function test_unauthenticated_redirects_to_login(): void
    {
        $response = $this->get(route('reports.index'));
        $response->assertStatus(302);
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}

