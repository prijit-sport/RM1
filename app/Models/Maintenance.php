<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $room_id
 * @property string $maintenance_type
 * @property string $description
 * @property string $status
 * @property string $assigned_to
 * @property float $cost
 * @property string $notes
 * @property \Carbon\Carbon $request_date
 * @property \App\Models\Room $room
 */
class Maintenance extends Model
{
    protected $fillable = [
        'room_id', 'maintenance_type', 'description', 'assigned_to', 
        'cost', 'request_date', 'completion_date', 'status', 'notes'
    ];

    protected $casts = [
        'request_date' => 'date',
        'completion_date' => 'date',
        'cost' => 'decimal:2'
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}