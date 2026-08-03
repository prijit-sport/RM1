<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles first
        $this->call(RoleSeeder::class);

        // Get admin role
        $adminRole = \App\Models\Role::where('name', 'Admin')->first();

        // Determine default passwords from .env, or generate if missing
        $adminPassword = env('ADMIN_DEFAULT_PASSWORD');
        $userPassword = env('USER_DEFAULT_PASSWORD');

        $generated = [];

        if (empty($adminPassword)) {
            $adminPassword = Str::random(16);
            $generated['ADMIN_DEFAULT_PASSWORD'] = $adminPassword;
        }

        if (empty($userPassword)) {
            $userPassword = Str::random(16);
            $generated['USER_DEFAULT_PASSWORD'] = $userPassword;
        }

        // Show generated passwords in console (only when random is used)
        if (! empty($generated)) {
            $this->command->line('🔐 Generated default passwords (missing in .env):');
            foreach ($generated as $key => $value) {
                $this->command->line(sprintf(' - %s=%s', $key, $value));
            }
        }

        // Create admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt($adminPassword),
            'role_id' => $adminRole?->id,
            'is_active' => true,
        ]);

        // Create regular user
        $userRole = \App\Models\Role::where('name', 'User')->first();
        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt($userPassword),
            'role_id' => $userRole?->id,
            'is_active' => true,
        ]);
    }
}
