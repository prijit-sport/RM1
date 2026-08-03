<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            if (! Schema::hasColumn('meter_readings', 'booking_id')) {
                $table->foreignId('booking_id')->nullable()->after('meter_id');
            }

            if (! Schema::hasColumn('meter_readings', 'period_month')) {
                $table->unsignedTinyInteger('period_month')->after('booking_id')->nullable();
            }

            if (! Schema::hasColumn('meter_readings', 'period_year')) {
                $table->unsignedSmallInteger('period_year')->after('period_month')->nullable();
            }

            // Keep compatibility if older rows have no period fields.
            // Unique constraint will apply when both period fields exist.
            // MySQL unique treats NULLs as distinct, so rows without period won't conflict.
            $table->unique(['meter_id', 'booking_id', 'period_month', 'period_year'], 'meter_readings_unique_monthly');
        });

        // Foreign key: meter_readings.booking_id -> bookings.id
        // If booking_id already existed (unlikely), avoid duplicate constraints.
        Schema::table('meter_readings', function (Blueprint $table) {
            if (Schema::hasColumn('meter_readings', 'booking_id')) {
                // If foreign key already exists, Laravel may throw.
                // This migration is designed to be idempotent for columns.
                $table->foreign('booking_id')
                    ->references('id')
                    ->on('bookings')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('meter_readings', function (Blueprint $table) {
            // Drop unique index
            $table->dropUnique('meter_readings_unique_monthly');

            if (Schema::hasColumn('meter_readings', 'period_year')) {
                $table->dropColumn('period_year');
            }
            if (Schema::hasColumn('meter_readings', 'period_month')) {
                $table->dropColumn('period_month');
            }
            if (Schema::hasColumn('meter_readings', 'booking_id')) {
                // drop FK first if exists
                try {
                    $table->dropForeign(['booking_id']);
                } catch (Throwable $e) {
                    // ignore
                }
                $table->dropColumn('booking_id');
            }
        });
    }
};
