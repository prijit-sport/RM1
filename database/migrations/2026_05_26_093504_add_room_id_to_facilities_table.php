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
        Schema::table('facilities', function (Blueprint $table) {
            // ✅ เพิ่ม room_id column
            $table->unsignedBigInteger('room_id')->nullable()->after('id');

            // ✅ เพิ่ม foreign key constraint
            $table->foreign('room_id')
                ->references('id')
                ->on('rooms')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            // ✅ ลบ foreign key ก่อน
            $table->dropForeign(['room_id']);

            // ✅ ลบ column
            $table->dropColumn('room_id');
        });
    }
};
