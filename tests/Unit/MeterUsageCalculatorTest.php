<?php

namespace Tests\Unit;

use App\Support\MeterUsageCalculator;
use Tests\TestCase;

/**
 * เทสสำหรับ App\Support\MeterUsageCalculator — helper กลางที่รวม logic
 * คำนวณ usage ที่เคยกระจายซ้ำกันอยู่ 4 จุด (InvoiceService x2, MeterBillingService x2)
 * และแก้ปัญหา meter rollover (มิเตอร์ถูกเปลี่ยนตัวใหม่ เลขรีเซ็ตต่ำกว่าเดือนก่อน)
 */
class MeterUsageCalculatorTest extends TestCase
{
    public function test_calculates_normal_usage_as_difference(): void
    {
        $usage = MeterUsageCalculator::calculate(previousValue: 100, currentValue: 150);

        $this->assertSame(50.0, $usage);
    }

    public function test_treats_missing_previous_reading_as_zero(): void
    {
        $usage = MeterUsageCalculator::calculate(previousValue: 0, currentValue: 80);

        $this->assertSame(80.0, $usage);
    }

    public function test_clamps_usage_to_zero_when_current_less_than_previous_and_not_flagged_as_reset(): void
    {
        // พฤติกรรมเดิม (ก่อนมี is_meter_reset): เลขย้อนกลับโดยไม่ได้ระบุเหตุผล
        // ต้อง fallback เป็น 0 อย่างปลอดภัย ไม่เดาแล้วคิดเงินผิด
        $usage = MeterUsageCalculator::calculate(previousValue: 500, currentValue: 100, isMeterReset: false);

        $this->assertSame(0.0, $usage);
    }

    public function test_uses_current_value_directly_when_flagged_as_meter_reset(): void
    {
        // ✅ FIX: มิเตอร์ถูกเปลี่ยนตัวใหม่ — usage ควรคำนวณจากเลขที่กรอกทั้งหมด
        // (ไม่หักลบเลขเก่าซึ่งเป็นของมิเตอร์ตัวก่อนหน้า) แทนที่จะได้ usage = 0
        $usage = MeterUsageCalculator::calculate(previousValue: 9800, currentValue: 45, isMeterReset: true);

        $this->assertSame(45.0, $usage);
    }

    public function test_meter_reset_flag_still_works_when_current_happens_to_be_higher(): void
    {
        // แม้เลขมิเตอร์ใหม่จะบังเอิญสูงกว่าตัวเก่า (เช่น ตัวเก่าใกล้ล้นรอบ, ตัวใหม่เริ่มจาก
        // เลขที่สูงกว่าจากโรงงาน) ก็ยังต้องใช้เลขที่กรอกเป็น usage ตรง ๆ ไม่หักลบ
        $usage = MeterUsageCalculator::calculate(previousValue: 20, currentValue: 30, isMeterReset: true);

        $this->assertSame(30.0, $usage);
    }

    public function test_meter_reset_never_returns_negative_usage(): void
    {
        $usage = MeterUsageCalculator::calculate(previousValue: 100, currentValue: -5, isMeterReset: true);

        $this->assertSame(0.0, $usage);
    }

    public function test_is_unflagged_rollover_true_when_current_less_than_previous_and_not_reset(): void
    {
        $this->assertTrue(
            MeterUsageCalculator::isUnflaggedRollover(previousValue: 500, currentValue: 100, isMeterReset: false)
        );
    }

    public function test_is_unflagged_rollover_false_when_flagged_as_reset(): void
    {
        $this->assertFalse(
            MeterUsageCalculator::isUnflaggedRollover(previousValue: 500, currentValue: 100, isMeterReset: true)
        );
    }

    public function test_is_unflagged_rollover_false_for_normal_increasing_usage(): void
    {
        $this->assertFalse(
            MeterUsageCalculator::isUnflaggedRollover(previousValue: 100, currentValue: 150, isMeterReset: false)
        );
    }

    public function test_is_unflagged_rollover_false_when_values_are_equal(): void
    {
        $this->assertFalse(
            MeterUsageCalculator::isUnflaggedRollover(previousValue: 100, currentValue: 100, isMeterReset: false)
        );
    }
}
