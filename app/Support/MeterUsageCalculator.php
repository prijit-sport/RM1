<?php

namespace App\Support;

/**
 * ✅ FIX (meter rollover / meter replacement):
 *
 * เดิมทุกจุดที่คำนวณหน่วยการใช้ไฟ/น้ำใช้สูตร usage = max(0, current - previous)
 * ซ้ำกันกระจายอยู่ 4 จุด (InvoiceService x2, MeterBillingService x2) — ถ้ามิเตอร์
 * ถูกเปลี่ยนตัวใหม่จริง (เลขรีเซ็ตกลับไปต่ำกว่า reading เดือนก่อน) สูตรเดิมจะได้
 * usage = 0 เสมอ ทำให้ห้องนั้นไม่ถูกเก็บค่าน้ำ/ไฟในรอบบิลนั้นทั้งที่มีการใช้จริง
 *
 * คลาสนี้รวม logic การคำนวณ usage ไว้ที่เดียว พร้อมรองรับ flag `is_meter_reset`
 * (ที่พนักงานติ๊กตอนบันทึก reading เมื่อรู้ว่ามิเตอร์เพิ่งถูกเปลี่ยนตัวใหม่) —
 * เมื่อ flag นี้เป็นจริง usage จะคำนวณจากค่าที่กรอกทั้งหมด (สมมติว่ามิเตอร์ตัวใหม่
 * เริ่มนับจาก 0) แทนที่จะหักลบเลขเก่าซึ่งเป็นของมิเตอร์ตัวก่อนหน้า
 *
 * กรณีเลขย้อนกลับ (current < previous) โดยไม่ได้ติ๊ก is_meter_reset — ระบบยังคง
 * fallback เป็น usage = 0 เหมือนพฤติกรรมเดิม (ปลอดภัยไว้ก่อน ไม่เดาและคิดเงินผิด)
 * แต่จะ flag ว่าเป็น "unflagged rollover" ให้ผู้เรียกใช้ (controller/service) แสดง
 * คำเตือนแก่ผู้ใช้ ให้ไปตรวจสอบและติ๊ก is_meter_reset ถ้าถูกต้องว่ามิเตอร์เปลี่ยนใหม่จริง
 */
class MeterUsageCalculator
{
    /**
     * คำนวณหน่วยการใช้งาน (usage) จากเลขมิเตอร์ก่อนหน้าและปัจจุบัน
     *
     * @param  float  $previousValue  เลขมิเตอร์ครั้งก่อน (0 ถ้าไม่มี reading ก่อนหน้า)
     * @param  float  $currentValue  เลขมิเตอร์ครั้งปัจจุบัน
     * @param  bool  $isMeterReset  true ถ้ามิเตอร์ถูกเปลี่ยนตัวใหม่ในรอบนี้ (ไม่ต้องหักลบเลขเก่า)
     */
    public static function calculate(float $previousValue, float $currentValue, bool $isMeterReset = false): float
    {
        if ($isMeterReset) {
            // มิเตอร์ตัวใหม่ — ถือว่าค่าที่อ่านได้คือหน่วยที่ใช้ไปทั้งหมดนับจากติดตั้งใหม่
            // (ระบบยังไม่รองรับการกรอก "เลขเริ่มต้นของมิเตอร์ใหม่" แยกต่างหาก จึงสมมติว่า
            // เริ่มจาก 0 ซึ่งตรงกับมิเตอร์ใหม่ส่วนใหญ่ในทางปฏิบัติ)
            return max(0.0, $currentValue);
        }

        if ($currentValue < $previousValue) {
            // เลขย้อนกลับโดยไม่ได้ระบุว่ามิเตอร์เปลี่ยนใหม่ — น่าจะเป็น data entry ผิดพลาด
            // หรือมิเตอร์เปลี่ยนจริงแต่ลืมติ๊ก flag ปลอดภัยไว้ก่อนด้วยการคืน usage = 0
            // (พฤติกรรมเดิม) ผู้เรียกควรเช็ค isUnflaggedRollover() เพื่อแจ้งเตือนผู้ใช้ด้วย
            return 0.0;
        }

        return $currentValue - $previousValue;
    }

    /**
     * true ถ้าเข้าข่าย "เลขมิเตอร์ย้อนกลับโดยไม่ได้ติ๊กว่ามิเตอร์เปลี่ยนใหม่"
     * ผู้เรียกใช้ควรแสดงคำเตือนให้ผู้ใช้ตรวจสอบเมื่อเจอกรณีนี้
     */
    public static function isUnflaggedRollover(float $previousValue, float $currentValue, bool $isMeterReset): bool
    {
        return ! $isMeterReset && $currentValue < $previousValue;
    }
}
