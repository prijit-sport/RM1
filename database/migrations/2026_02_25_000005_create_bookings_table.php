<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    /**
     * Run the migrations.
     * (สำหรับ fresh migration - ถ้าเริ่มใหม่)
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('guest_id')->constrained()->onDelete('cascade');
            
            // ค่าเช่าและมัดจำ
            $table->decimal('rent_amount', 10, 2)->nullable()->comment('ค่าเช่าเดือนแรก');
            $table->decimal('deposit_amount', 10, 2)->nullable()->comment('เงินมัดจำ (2 เดือน)');
            
            // วันที่เช็ค อิน/เช็ค เอาท์
            $table->date('check_in_date');
            $table->date('check_out_date')->nullable();
            
            // ยอดรวม
            $table->decimal('total_price', 10, 2)->comment('ยอดรวมที่ต้องชำระ');
            
            // มิเตอร์เริ่มต้น
            $table->integer('electric_meter_start')->default(0)->comment('เลขมิเตอร์ไฟเริ่มต้น');
            $table->integer('water_meter_start')->default(0)->comment('เลขมิเตอร์น้ำเริ่มต้น');
            
            // สถานะและหมายเหตุ
            $table->enum('status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
 