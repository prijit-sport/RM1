<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['room_id', 'status', 'check_in_date', 'check_out_date'], 'bookings_room_status_date_idx');
            $table->index(['status', 'created_at'], 'bookings_status_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_room_status_date_idx');
            $table->dropIndex('bookings_status_created_idx');
        });
    }
};
