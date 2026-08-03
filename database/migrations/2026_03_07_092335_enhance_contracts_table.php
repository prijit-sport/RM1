<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Enhanced contract fields to match Thai rental agreement requirements:
     * - contract_date: วันที่ทำสัญญา
     * - landlord_name: ชื่อผู้ให้เช่า
     * - landlord_address: ที่อยู่ผู้ให้เช่า
     * - electricity_rate: ค่าไฟต่อหน่วย
     * - water_rate: ค่าน้ำต่อหน่วย
     * - late_fee: ค่าปรับ
     * - other_fees: ค่าอื่นๆ
     * - terms: ข้อตกลง
     * - tenant_signature: ลงชื่อผู้เช่า
     * - landlord_signature: ลงชื่อผู้ให้เช่า
     * - witness_signature: ลงชื่อพยาน
     */
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // วันที่ทำสัญญา
            $table->date('contract_date')->nullable()->after('title');

            // ข้อมูลผู้ให้เช่า
            $table->string('landlord_name')->nullable()->after('contract_date');
            $table->text('landlord_address')->nullable()->after('landlord_name');

            // ราคาค่าเช่าและค่าบริการ
            $table->decimal('electricity_rate', 10, 2)->nullable()->after('advance_payment');
            $table->decimal('water_rate', 10, 2)->nullable()->after('electricity_rate');
            $table->decimal('late_fee', 10, 2)->nullable()->after('water_rate');
            $table->text('other_fees')->nullable()->after('late_fee');

            // ข้อตกลง
            $table->text('terms')->nullable()->after('other_fees');

            // ลายมือชื่อ
            $table->string('tenant_signature')->nullable()->after('terms');
            $table->string('landlord_signature')->nullable()->after('tenant_signature');
            $table->string('witness_signature')->nullable()->after('landlord_signature');

            // ลงวันที่
            $table->date('tenant_sign_date')->nullable()->after('witness_signature');
            $table->date('landlord_sign_date')->nullable()->after('tenant_sign_date');
            $table->date('witness_sign_date')->nullable()->after('landlord_sign_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'contract_date',
                'landlord_name',
                'landlord_address',
                'electricity_rate',
                'water_rate',
                'late_fee',
                'other_fees',
                'terms',
                'tenant_signature',
                'landlord_signature',
                'witness_signature',
                'tenant_sign_date',
                'landlord_sign_date',
                'witness_sign_date',
            ]);
        });
    }
};
