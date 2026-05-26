<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ตรวจสอบว่า floor และ zone columns มีอยู่
        if (!Schema::hasColumn('rooms', 'floor')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->string('floor')->nullable();
            });
        }

        if (!Schema::hasColumn('rooms', 'zone')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->string('zone')->nullable();
            });
        }

        // ถ้า floor และ zone ยังเป็น NULL ให้ update เป็นค่าเริ่มต้น
        DB::table('rooms')
            ->whereNull('floor')
            ->orWhereNull('zone')
            ->update([
                'floor' => DB::raw('COALESCE(floor, "1")'),
                'zone' => DB::raw('COALESCE(zone, "A")')
            ]);
    }

    public function down(): void
    {
        // Rollback - ไม่ต้องลบคอลัมน์
    }
};
