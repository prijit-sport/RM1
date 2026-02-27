<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_access_manager_level_modules(): void
    {
        $this->actingAs($this->createUserWithRole('Manager'));

        $this->get(route('contracts.index'))->assertOk();
        $this->get(route('invoices.index'))->assertOk();
        $this->get(route('maintenances.index'))->assertOk();
    }

    public function test_regular_user_cannot_access_manager_level_modules(): void
    {
        $this->actingAs($this->createUserWithRole('User'));

        $this->get(route('contracts.index'))->assertForbidden();
        $this->get(route('invoices.index'))->assertForbidden();
        $this->get(route('maintenances.index'))->assertForbidden();
    }

    private function createUserWithRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
