<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'room_id',
        'issue_type',
        'description',
        'reported_date',
        'completed_date',
        'status',
        'assigned_to',
        'cost',
        'priority',
        'notes',
    ];

    protected $casts = [
        'reported_date' => 'date',
        'completed_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Check if maintenance is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if maintenance is pending.
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'in_progress']);
    }

    /**
     * Calculate duration in days.
     */
    public function getDurationDays(): ?int
    {
        if (!$this->completed_date) {
            return null;
        }

        return $this->reported_date->diffInDays($this->completed_date);
    }
}
