<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('guest_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title')->nullable()->after('contract_number');
            $table->decimal('monthly_rent', 12, 2)->nullable()->after('end_date');
            $table->decimal('deposit', 12, 2)->nullable()->after('monthly_rent');
            $table->decimal('advance_payment', 12, 2)->nullable()->after('deposit');
            $table->softDeletes();
            $table->index(['room_id', 'status']);
            $table->index(['guest_id', 'status']);
            $table->index('end_date');
        });

        // SQLite doesn't support `ALTER TABLE ... ADD CONSTRAINT ... CHECK`.
        // Only apply the constraint when supported (e.g. MySQL/PostgreSQL).
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE contracts ADD CONSTRAINT CHK_contracts_status CHECK (status IN ('draft', 'pending', 'active', 'completed', 'cancelled'))");
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE contracts DROP CONSTRAINT CHK_contracts_status');

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropForeign(['guest_id']);
            $table->dropSoftDeletes();
            $table->dropIndex(['room_id', 'status']);
            $table->dropIndex(['guest_id', 'status']);
            $table->dropIndex(['end_date']);
            $table->dropColumn(['room_id', 'guest_id', 'title', 'monthly_rent', 'deposit', 'advance_payment']);
        });
    }
};
