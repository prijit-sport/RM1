<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['water', 'electric']);
            $table->string('meter_number')->unique();
            $table->string('unit')->nullable(); // เช่น kWh, m3
            $table->date('installed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['room_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meters');
    }
};
