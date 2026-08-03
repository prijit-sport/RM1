<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            if (! Schema::hasColumn('maintenances', 'facility_id')) {
                $table->foreignId('facility_id')->nullable()->after('room_id')
                    ->constrained('facilities')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            if (Schema::hasColumn('maintenances', 'facility_id')) {
                $table->dropForeign(['facility_id']);
                $table->dropColumn('facility_id');
            }
        });
    }
};
