<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = ['room_id', 'issue_type', 'description', 'reported_date', 'completed_date', 'status', 'assigned_to', 'cost', 'notes'];
    protected $dates = ['reported_date', 'completed_date'];
    
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
