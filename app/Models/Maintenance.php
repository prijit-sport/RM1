<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    protected $fillable = [
        'room_id',
        'facility_id',
        'issue_type',            // ✅ ตรงกับ migration
        'maintenance_type',     // ✅ จาก migration 2026_05_11_070513
        'description',
        'reported_date',       // ✅ ตรงกับ migration
        'completed_date',      // ✅ ตรงกับ migration
        'status',
        'assigned_to',
        'cost',
        'priority',            // ✅ จาก migration 2026_03_01_000006
        'notes',
    ];

    protected $casts = [
        'reported_date' => 'date',  // ✅ ตรงกับ migration
        'completed_date' => 'date',  // ✅ ตรงกับ migration
        'cost' => 'decimal:2',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    // facility relation (facility_id)

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }
}
