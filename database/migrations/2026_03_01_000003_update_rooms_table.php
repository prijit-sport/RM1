<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Add new fields
            $table->string('floor')->nullable()->after('description');
            $table->string('building')->nullable()->after('floor');

            // Add soft deletes
            $table->softDeletes();

            // Add indexes
            $table->index(['status', 'room_type']);
            $table->index(['floor', 'building']);
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['status', 'room_type']);
            $table->dropIndex(['floor', 'building']);

            $table->dropColumn([
                'floor',
                'building',
            ]);
        });
    }
};
