<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    /**
     * Run the migrations - เพิ่มคอลัมน์ที่ขาดไปในตาราง bookings
     * ถ้ายังไม่มี rent_amount, deposit_amount, electric_meter_start, water_meter_start
     */
    public function up(): void
    {
        // ป้องกันปัญหา sqlite :memory ใน test ที่ยังไม่มีตาราง bookings
        // บางเวอร์ชันของ sqlite/Schema::hasTable อาจไม่เสถียร เลยทำ guard แบบ try/catch เพิ่ม
        try {
            if (!Schema::hasTable('bookings')) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'rent_amount')) {
                $table->decimal('rent_amount', 10, 2)->nullable()->comment('ค่าเช่าเดือนแรก');
            }

            if (!Schema::hasColumn('bookings', 'deposit_amount')) {
                $table->decimal('deposit_amount', 10, 2)->nullable()->comment('เงินมัดจำ (1 เดือน)');
            }

            if (!Schema::hasColumn('bookings', 'electric_meter_start')) {
                $table->integer('electric_meter_start')->default(0)->comment('เลขมิเตอร์ไฟเริ่มต้น');
            }

            if (!Schema::hasColumn('bookings', 'water_meter_start')) {
                $table->integer('water_meter_start')->default(0)->comment('เลขมิเตอร์น้ำเริ่มต้น');
            }

            if (Schema::hasColumn('bookings', 'check_out_date')) {
                $table->date('check_out_date')->nullable()->change();
            }
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumnIfExists([
                'rent_amount',
                'deposit_amount',
                'electric_meter_start',
                'water_meter_start',
            ]);
        });
    }
};
 