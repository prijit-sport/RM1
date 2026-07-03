<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'building')) {
                $table->string('building')->nullable()->after('floor');

                // If floor/building indexes are expected, create building in a safe way.
                // We only add these indexes if they don't already exist.
                // (Laravel does not expose hasIndex reliably across drivers, so keep minimal.)
            }
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            if (Schema::hasColumn('rooms', 'building')) {
                $table->dropColumn('building');
            }
        });
    }
};

