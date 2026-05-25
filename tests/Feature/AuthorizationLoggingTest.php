<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AuthorizationLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_only_denied_access_is_logged(): void
    {
        $role = Role::firstOrCreate(['name' => 'User'], ['description' => 'Regular user']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $logPath = storage_path('logs/laravel.log');
        File::put($logPath, '');

        $this->actingAs($user);

        $this->get(route('roles.index'))->assertStatus(403);

        $logContents = file_get_contents($logPath);

        $this->assertStringContainsString('Authorization denied', $logContents);
        $this->assertStringContainsString('"route":"roles.index"', $logContents);
        $this->assertStringContainsString('"user_id":'.$user->id, $logContents);
        $this->assertStringContainsString('"role":"User"', $logContents);
    }
}
