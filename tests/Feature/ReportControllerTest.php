<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_ok_for_manager_or_admin(): void
    {
        $this->actingAs($this->createUserWithRole('Manager'));

        $response = $this->get(route('reports.index'));

        $response->assertOk();
        $response->assertViewIs('reports.index');
        $response->assertSeeText('รายงานสถิติและรายได้');
    }

    public function test_index_returns_forbidden_for_non_manager_or_admin(): void
    {
        $this->actingAs($this->createUserWithRole('User'));

        $response = $this->get(route('reports.index'));

        $response->assertStatus(403);
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

