<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            // Add priority field
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('cost');

            // Add soft deletes
            $table->softDeletes();

            // Add indexes
            $table->index(['room_id', 'status']);
            $table->index(['status', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['room_id', 'status']);
            $table->dropIndex(['status', 'priority']);

            $table->dropColumn('priority');
        });
    }
};
