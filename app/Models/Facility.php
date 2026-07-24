<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class Facility extends Model
{
    protected $table = 'facilities';
 
    protected $fillable = [
        'room_id',                  // ✅ เพิ่ม room_id
        'name',
        'type',
        'location',
        'description',
        'status',
        'maintenance_schedule',
        'last_maintenance_date',
        'next_maintenance_date',
    ];
 
    protected $casts = [
        'last_maintenance_date' => 'datetime',
        'next_maintenance_date' => 'datetime',
    ];
 
    /**
     * ✅ Relationship: Facility เป็นของ Room
     */
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
 
    /**
     * ✅ Relationship: Facility มี Maintenance หลายรายการ
     */
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'facility_id');
    }
}
 