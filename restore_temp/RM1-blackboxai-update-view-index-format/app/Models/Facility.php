<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable = ['name', 'type', 'location', 'description', 'status', 'maintenance_schedule', 'last_maintenance_date', 'next_maintenance_date'];
    protected $dates = ['last_maintenance_date', 'next_maintenance_date'];
}
