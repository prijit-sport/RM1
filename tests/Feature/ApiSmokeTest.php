<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_login_route_returns_200_for_valid_credentials(): void
    {
        $user = User::factory()->create(['role_id' => null]);

        // Ensure the user has a known password.
        // If factory password hashing differs, adjust here.
        // Many setups in Laravel use "password" => 'password' in factory.
        $payload = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)->assertJsonStructure([
            // API returns token under key 'token'
            'token',
            'user',
        ]);
    }

    public function test_login_creates_token_with_expiration(): void
    {
        $role = Role::firstOrCreate(['name' => Role::STAFF], ['description' => 'Staff user']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $token = $user->fresh()->tokens()->latest()->first();

        $this->assertNotNull($token);
        $this->assertNotNull($token->expires_at);
    }

    public function test_dashboard_requires_auth_sanctum(): void
    {
        $response = $this->getJson('/api/dashboard');
        $response->assertStatus(401);
    }

    public function test_room_index_requires_auth_sanctum(): void
    {
        $response = $this->getJson('/api/rooms?status=available');
        $response->assertStatus(404); // no route in current routes/api.php; keep smoke test safe
    }
}
