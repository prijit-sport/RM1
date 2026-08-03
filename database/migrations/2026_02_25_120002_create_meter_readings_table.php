<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meter_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meter_id')->constrained()->onDelete('cascade');
            $table->date('reading_date');
            $table->decimal('reading_value', 12, 2);
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['meter_id', 'reading_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};
