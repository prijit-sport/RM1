<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ✅ FIX (meter rollover): เพิ่ม is_meter_reset ให้ meter_readings
 *
 * ปัญหาเดิม: usage = max(0, current - previous) — ถ้ามิเตอร์ถูกเปลี่ยนตัวใหม่
 * (เลขรีเซ็ตกลับไปต่ำกว่า reading เดือนก่อน) ระบบจะคำนวณ usage = 0 ผิดพลาด
 * เพราะไม่มีทางแยกแยะระหว่าง "มิเตอร์เปลี่ยนใหม่" กับ "กรอกเลขผิด/ย้อนหลัง"
 *
 * เพิ่ม flag นี้ให้พนักงานติ๊กตอนบันทึก reading เมื่อรู้ว่ามิเตอร์เพิ่งถูกเปลี่ยน
 * เพื่อให้ระบบคำนวณ usage จากค่าที่กรอกทั้งหมด แทนที่จะหักลบเลขเก่า (ดู
 * App\Support\MeterUsageCalculator)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->boolean('is_meter_reset')->default(false)->after('reading_value');
        });
    }

    public function down(): void
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->dropColumn('is_meter_reset');
        });
    }
};
