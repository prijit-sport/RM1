<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'guest_id_2')) {
                $table->foreignId('guest_id_2')->nullable()->after('guest_id')->constrained('guests')->nullOnDelete();
            }
            if (! Schema::hasColumn('bookings', 'guest_id_3')) {
                $table->foreignId('guest_id_3')->nullable()->after('guest_id_2')->constrained('guests')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'guest_id_3')) {
                $table->dropConstrainedForeignId('guest_id_3');
            }
            if (Schema::hasColumn('bookings', 'guest_id_2')) {
                $table->dropConstrainedForeignId('guest_id_2');
            }
        });
    }
};
