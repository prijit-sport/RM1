<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // ข้อมูลผู้ให้เช่าเพิ่มเติม (ถ้ายังไม่มี)
            if (!Schema::hasColumn('contracts', 'landlord_id_number')) {
                $table->string('landlord_id_number', 50)->nullable()->after('landlord_name');
            }
            if (!Schema::hasColumn('contracts', 'landlord_phone')) {
                $table->string('landlord_phone', 20)->nullable()->after('landlord_id_number');
            }
            
            // ข้อมูลสัญญาเพิ่มเติม
            if (!Schema::hasColumn('contracts', 'advance_payment_months')) {
                $table->integer('advance_payment_months')->nullable()->after('advance_payment');
            }
            if (!Schema::hasColumn('contracts', 'due_date_day')) {
                $table->integer('due_date_day')->nullable()->after('advance_payment_months');
            }
            if (!Schema::hasColumn('contracts', 'monthly_rent_text')) {
                $table->string('monthly_rent_text', 255)->nullable()->after('monthly_rent');
            }
            if (!Schema::hasColumn('contracts', 'duration')) {
                $table->string('duration', 100)->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('contracts', 'photo_count')) {
                $table->integer('photo_count')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'landlord_id_number',
                'landlord_phone',
                'advance_payment_months',
                'due_date_day',
                'monthly_rent_text',
                'duration',
                'photo_count',
            ]);
        });
    }
};
