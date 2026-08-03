<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Add new fields for better tracking
            $table->foreignId('guest_id')->nullable()->constrained()->onDelete('no action');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('no action');
            $table->decimal('late_fee', 12, 2)->default(0)->after('total');
            $table->decimal('paid_amount', 12, 2)->nullable()->after('late_fee');
            $table->string('payment_method')->nullable()->after('paid_amount');
            $table->date('payment_date')->nullable()->after('payment_method');

            // Add soft deletes
            $table->softDeletes();

            // Add indexes
            $table->index(['guest_id', 'status']);
            $table->index(['room_id', 'status']);
            $table->index(['status', 'due_date']);
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['guest_id']);
            $table->dropForeign(['room_id']);
            $table->dropSoftDeletes();
            $table->dropIndex(['guest_id', 'status']);
            $table->dropIndex(['room_id', 'status']);
            $table->dropIndex(['status', 'due_date']);
            $table->dropIndex(['payment_date']);

            $table->dropColumn([
                'guest_id',
                'room_id',
                'late_fee',
                'paid_amount',
                'payment_method',
                'payment_date',
            ]);
        });
    }
};
