<?php

namespace Database\Seeders;

use App\Models\Role;
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
        $adminRole = Role::where('name', 'Admin')->first();

        // Determine default password from .env, or generate if missing
        $adminPassword = env('ADMIN_DEFAULT_PASSWORD');

        $generated = [];

        if (empty($adminPassword)) {
            $adminPassword = Str::random(16);
            $generated['ADMIN_DEFAULT_PASSWORD'] = $adminPassword;
        }

        // Show generated password in console (only when random is used)
        if (! empty($generated)) {
            $this->command->line('🔐 Generated default password (missing in .env):');
            foreach ($generated as $key => $value) {
                $this->command->line(sprintf(' - %s=%s', $key, $value));
            }
        }

        // ✅ FIX (production readiness): เดิมมีการสร้าง "Test User" อีกคนที่
        // ค้นหา role ชื่อ 'User' ซึ่งไม่เคยถูกสร้างเลยในระบบนี้ (RoleSeeder สร้างแค่
        // 'Admin' กับ 'Staff') ทำให้ user นั้นได้ role_id = null เสมอ — เป็นบั๊ก
        // แบบเดียวกับที่เคยเกิดขึ้นจริงกับ Staff account มาก่อน ตัดออกเพราะ
        // user@example.com ไม่ใช่บัญชีที่ควรมีอยู่จริงบน production อยู่แล้ว
        //
        // ใช้ firstOrCreate() แทน create() ตรง ๆ เพื่อให้รัน `db:seed` ซ้ำได้
        // อย่างปลอดภัย (idempotent) — ไม่ error เรื่อง email ซ้ำถ้าเผลอรันซ้ำ
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt($adminPassword),
                'role_id' => $adminRole?->id,
                'is_active' => true,
            ]
        );
    }
}
