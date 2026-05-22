<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make amount nullable since it's no longer required in the new system
        // The new system uses monthly_rent instead
        Schema::table('contracts', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->change();
        });
    }
};
