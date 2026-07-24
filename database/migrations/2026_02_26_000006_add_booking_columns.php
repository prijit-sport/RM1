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
        // ตรวจสอบว่า columns มีอยู่แล้วหรือไม่
        Schema::table('bookings', function (Blueprint $table) {
            // เพิ่มคอลัมน์ที่ขาดไป (ใช้ whereNull/whereNotNull เพื่อตรวจสอบ)
            if (!Schema::hasColumn('bookings', 'rent_amount')) {
                $table->decimal('rent_amount', 10, 2)->nullable()->after('guest_id')->comment('ค่าเช่าเดือนแรก');
            }
            
            if (!Schema::hasColumn('bookings', 'deposit_amount')) {
                $table->decimal('deposit_amount', 10, 2)->nullable()->after('rent_amount')->comment('เงินมัดจำ (1 เดือน)');
            }
            
            if (!Schema::hasColumn('bookings', 'electric_meter_start')) {
                $table->integer('electric_meter_start')->default(0)->after('deposit_amount')->comment('เลขมิเตอร์ไฟเริ่มต้น');
            }
            
            if (!Schema::hasColumn('bookings', 'water_meter_start')) {
                $table->integer('water_meter_start')->default(0)->after('electric_meter_start')->comment('เลขมิเตอร์น้ำเริ่มต้น');
            }
            
            // ทำให้ check_out_date nullable (ยังไม่ check-out)
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
 