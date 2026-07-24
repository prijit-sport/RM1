<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_ok_for_authenticated_user(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard.index');
    }

    public function test_unauthenticated_redirects_to_login(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertStatus(302);
    }

    public function test_dashboard_html_does_not_contain_cdn_chartjs(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertDontSee('cdn.jsdelivr.net/npm/chart.js');
    }

    public function test_dashboard_html_contains_canvas_booking_chart(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('bookingChart', false);
    }

    public function test_dashboard_html_contains_vite_built_asset_js(): void
    {
        $this->actingAs($this->createUserWithRole('Staff'));

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('/build/assets/app-', false);
    }

    public function test_vite_manifest_and_built_assets_exist(): void
    {
        $this->assertTrue(
            File::exists(public_path('build/manifest.json')),
            'Vite manifest not found at public/build/manifest.json. Run "npm run build" first.'
        );

        $manifest = json_decode(File::get(public_path('build/manifest.json')), true);

        $this->assertArrayHasKey('resources/js/app.js', $manifest, 'app.js not found in Vite manifest');
        $this->assertStringContainsString('app-', $manifest['resources/js/app.js']['file'] ?? '', 'app.js file pattern mismatch');
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}

