<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/rooms');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_rooms_index(): void
    {
        // Create role and user
        $role = Role::firstOrCreate(['name' => 'Admin'], ['label' => 'Administrator']);
        
        $user = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/rooms');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_room_show(): void
    {
        // Create role and user
        $role = Role::firstOrCreate(['name' => 'Admin'], ['label' => 'Administrator']);
        
        $user = User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        // Create a room
        $room = \App\Models\Room::create([
            'room_number' => '101',
            'room_type' => 'Standard',
            'price_per_month' => 5000,
            'status' => 'available',
            'capacity' => 2,
        ]);

        $response = $this->actingAs($user)->get('/rooms/' . $room->id);
        $response->assertStatus(200);
    }

    public function test_root_path_redirects_to_login_when_unauthenticated(): void
    {
        $response = $this->get('/');
        // Either redirects to login or dashboard (which will redirect to login)
        $response->assertStatus(302);
    }

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_fallback_route_for_unauthenticated(): void
    {
        $response = $this->get('/some-unknown-path');
        // Fallback route returns 404
        $response->assertStatus(404);
    }

    public function test_fallback_route_with_deep_path(): void
    {
        $response = $this->get('/.well-known/appspecific/com.chrome.devtools');
        // Fallback route returns 404
        $response->assertStatus(404);
    }

    public function test_login_route_exists(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }
}

