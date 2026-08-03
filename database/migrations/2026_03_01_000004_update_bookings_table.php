<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add actual check-in/check-out timestamps
            $table->timestamp('actual_check_in')->nullable()->after('check_out_date');
            $table->timestamp('actual_check_out')->nullable()->after('actual_check_in');

            // Add soft deletes
            $table->softDeletes();

            // Add indexes
            $table->index(['status', 'check_in_date']);
            $table->index(['room_id', 'status']);
            $table->index(['guest_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['status', 'check_in_date']);
            $table->dropIndex(['room_id', 'status']);
            $table->dropIndex(['guest_id', 'status']);

            $table->dropColumn([
                'actual_check_in',
                'actual_check_out',
            ]);
        });
    }
};
