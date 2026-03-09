<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Add new fields
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('guest_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title')->nullable()->after('contract_number');
            $table->decimal('monthly_rent', 12, 2)->nullable()->after('end_date');
            $table->decimal('deposit', 12, 2)->nullable()->after('monthly_rent');
            $table->decimal('advance_payment', 12, 2)->nullable()->after('deposit');
            
            // Rename contractor_name to something more appropriate or keep it
            // Change status enum to include more options
            $table->enum('status', ['draft', 'pending', 'active', 'completed', 'cancelled'])->default('draft')->change();
            
            // Add soft deletes
            $table->softDeletes();
            
            // Update indexes
            $table->index(['room_id', 'status']);
            $table->index(['guest_id', 'status']);
            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['guest_id']);
            $table->dropSoftDeletes();
            $table->dropIndex(['room_id', 'status']);
            $table->dropIndex(['guest_id', 'status']);
            $table->dropIndex(['end_date']);
            
            $table->dropColumn([
                'room_id',
                'guest_id',
                'title',
                'monthly_rent',
                'deposit',
                'advance_payment',
            ]);
            
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft')->change();
        });
    }
};
