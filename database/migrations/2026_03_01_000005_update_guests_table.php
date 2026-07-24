<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            // Add new fields
            $table->date('date_of_birth')->nullable()->after('id_number');
            $table->string('nationality')->nullable()->after('country');
            $table->string('emergency_contact')->nullable()->after('nationality');
            $table->string('emergency_phone')->nullable()->after('emergency_contact');
            $table->text('notes')->nullable()->after('emergency_phone');
            
            // Add soft deletes
            $table->softDeletes();
            
            // Add indexes
            $table->index('email');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);
            
            $table->dropColumn([
                'date_of_birth',
                'nationality',
                'emergency_contact',
                'emergency_phone',
                'notes',
            ]);
        });
    }
};
