<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
 
            if (! Schema::hasColumn('bookings', 'rent_amount')) {
                $table->decimal('rent_amount', 10, 2)->nullable()->after('guest_id');
            }
 
            if (! Schema::hasColumn('bookings', 'deposit_amount')) {
                $table->decimal('deposit_amount', 10, 2)->nullable()->after('rent_amount');
            }
 
            if (! Schema::hasColumn('bookings', 'electric_meter_start')) {
                $table->integer('electric_meter_start')->default(0)->after('deposit_amount');
            }
 
            if (! Schema::hasColumn('bookings', 'water_meter_start')) {
                $table->integer('water_meter_start')->default(0)->after('electric_meter_start');
            }
        });
    }
 
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'rent_amount',
                'deposit_amount',
                'electric_meter_start',
                'water_meter_start',
            ]);
        });
    }
};
 