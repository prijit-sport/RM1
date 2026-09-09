<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ✅ FIX (data integrity): เพิ่ม composite unique index บน meter_readings
 * (meter_id, period_month, period_year, booking_id)
 *
 * เดิมการป้องกัน "reading ซ้ำสำหรับมิเตอร์+รอบบิล+booking เดียวกัน" ทำแค่ระดับ
 * application ผ่าน MeterBillingService::upsertReading() ที่ใช้ updateOrCreate()
 * — แต่ updateOrCreate() ทำ SELECT แล้วค่อย INSERT/UPDATE แยกกัน ไม่ atomic
 * ถ้ามี request ชนกัน (เช่น double-click, เปิดหลาย tab กดบันทึกพร้อมกัน)
 * มีโอกาสเกิด race condition ได้ทั้งที่ application-level check ผ่านทั้งคู่
 *
 * unique index นี้เป็น safety net ระดับ DB (defense in depth) เสริมจาก
 * application-level check เดิม — ยืนยันแล้วว่าไม่มีข้อมูลซ้ำอยู่ก่อนก่อนรัน migration นี้
 * (เช็คด้วย SELECT ... GROUP BY ... HAVING COUNT(*) > 1 บนข้อมูลจริง = 0 แถว)
 *
 * หมายเหตุ: ตั้งใจไม่ครอบคลุม reading ที่ period_month/period_year เป็น NULL
 * (จากฟอร์ม "บันทึกทั่วไป" ที่ไม่ผูกกับรอบบิล) เพราะ MySQL unique index ถือว่า
 * ค่า NULL แต่ละแถวไม่เท่ากัน (distinct) อยู่แล้วโดยธรรมชาติ จึงไม่ชนกัน ไม่ต้องทำ
 * partial/filtered index เพิ่ม
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->unique(
                ['meter_id', 'period_month', 'period_year', 'booking_id'],
                'meter_readings_period_booking_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            $table->dropUnique('meter_readings_period_booking_unique');
        });
    }
};
