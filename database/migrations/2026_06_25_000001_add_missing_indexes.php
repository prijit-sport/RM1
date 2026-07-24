<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // invoices
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'guest_id')) {
                try {
                    $table->index('guest_id', 'invoices_guest_id_idx');
                } catch (\Throwable $e) {
                    // skip if index already exists
                }
            }

            if (Schema::hasColumn('invoices', 'room_id')) {
                try {
                    $table->index('room_id', 'invoices_room_id_idx');
                } catch (\Throwable $e) {
                    // skip if index already exists
                }
            }

            if (Schema::hasColumn('invoices', 'status') && Schema::hasColumn('invoices', 'due_date')) {
                try {
                    $table->index(['status', 'due_date'], 'invoices_status_due_date_idx');
                } catch (\Throwable $e) {
                    // skip if index already exists
                }
            }

            if (Schema::hasColumn('invoices', 'issue_date')) {
                try {
                    $table->index('issue_date', 'invoices_issue_date_idx');
                } catch (\Throwable $e) {
                    // skip if index already exists
                }
            }
        });

        // contracts
        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'guest_id')) {
                try {
                    $table->index('guest_id', 'contracts_guest_id_idx');
                } catch (\Throwable $e) {
                    // skip if index already exists
                }
            }

            if (Schema::hasColumn('contracts', 'room_id')) {
                try {
                    $table->index('room_id', 'contracts_room_id_idx');
                } catch (\Throwable $e) {
                    // skip if index already exists
                }
            }

            if (Schema::hasColumn('contracts', 'status') && Schema::hasColumn('contracts', 'end_date')) {
                try {
                    $table->index(['status', 'end_date'], 'contracts_status_end_date_idx');
                } catch (\Throwable $e) {
                    // skip if index already exists
                }
            }
        });

        // meter_readings
        Schema::table('meter_readings', function (Blueprint $table) {
            $hasMeterId = Schema::hasColumn('meter_readings', 'meter_id');
            $hasPeriodMonth = Schema::hasColumn('meter_readings', 'period_month');
            $hasPeriodYear = Schema::hasColumn('meter_readings', 'period_year');

            if ($hasMeterId && $hasPeriodMonth && $hasPeriodYear) {
                try {
                    $table->index(['meter_id', 'period_month', 'period_year'], 'meter_readings_meter_month_year_idx');
                } catch (\Throwable $e) {
                    // skip if index already exists
                }
            }

            if (Schema::hasColumn('meter_readings', 'reading_date')) {
                try {
                    $table->index('reading_date', 'meter_readings_reading_date_idx');
                } catch (\Throwable $e) {
                    // skip if index already exists
                }
            }
        });

        // maintenances
        Schema::table('maintenances', function (Blueprint $table) {
            if (Schema::hasColumn('maintenances', 'status') && Schema::hasColumn('maintenances', 'created_at')) {
                try {
                    $table->index(['status', 'created_at'], 'maintenances_status_created_at_idx');
                } catch (\Throwable $e) {
                    // skip if index already exists
                }
            }
        });
    }

    public function down(): void
    {
        // invoices
        Schema::table('invoices', function (Blueprint $table) {
            try {
                $table->dropIndex('invoices_guest_id_idx');
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                $table->dropIndex('invoices_room_id_idx');
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                $table->dropIndex('invoices_status_due_date_idx');
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                $table->dropIndex('invoices_issue_date_idx');
            } catch (\Throwable $e) {
                // ignore
            }
        });

        // contracts
        Schema::table('contracts', function (Blueprint $table) {
            try {
                $table->dropIndex('contracts_guest_id_idx');
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                $table->dropIndex('contracts_room_id_idx');
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                $table->dropIndex('contracts_status_end_date_idx');
            } catch (\Throwable $e) {
                // ignore
            }
        });

        // meter_readings
        Schema::table('meter_readings', function (Blueprint $table) {
            try {
                $table->dropIndex('meter_readings_meter_month_year_idx');
            } catch (\Throwable $e) {
                // ignore
            }

            try {
                $table->dropIndex('meter_readings_reading_date_idx');
            } catch (\Throwable $e) {
                // ignore
            }
        });

        // maintenances
        Schema::table('maintenances', function (Blueprint $table) {
            try {
                $table->dropIndex('maintenances_status_created_at_idx');
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }
};

