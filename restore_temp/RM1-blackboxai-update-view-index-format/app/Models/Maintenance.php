<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $facility_id  <-- เพิ่ม property นี้
 * @property int $room_id
 * @property string $maintenance_type
 * @property string $description
 * @property string $status
 * @property string $assigned_to
 * @property float $cost
 * @property string $notes
 * @property \Carbon\Carbon $request_date
 * @property \Carbon\Carbon|null $completion_date
 * @property \App\Models\Room $room
 * @property \App\Models\Facility $facility <-- เพิ่มความสัมพันธ์นี้
 */
class Maintenance extends Model
{
    protected $fillable = [
        'room_id', 
        'facility_id', // 1. ต้องเพิ่ม facility_id ลงในนี้เพื่อให้บันทึกข้อมูลได้ (Mass Assignment)
        'maintenance_type', 
        'description', 
        'assigned_to', 
        'cost', 
        'request_date', 
        'completion_date', 
        'status', 
        'notes'
    ];

    protected $casts = [
        'request_date' => 'date',
        'completion_date' => 'date',
        'cost' => 'decimal:2'
    ];

    /**
     * ความสัมพันธ์กับห้องพัก
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    /**
     * 2. เพิ่มความสัมพันธ์กับสิ่งอำนวยความสะดวก
     * ช่วยให้เราดึงชื่ออุปกรณ์ที่ซ่อมออกมาแสดงได้ เช่น $maintenance->facility->name
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }
}