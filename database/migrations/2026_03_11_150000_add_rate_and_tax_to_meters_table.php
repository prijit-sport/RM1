<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('meters', function (Blueprint $table) {
            $table->decimal('rate_per_unit', 10, 2)->default(0)->after('notes');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('rate_per_unit');
        });
    }

    public function down(): void
    {
        Schema::table('meters', function (Blueprint $table) {
            $table->dropColumn(['rate_per_unit', 'tax_rate']);
        });
    }
};
