<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = ['booking_id', 'guest_id', 'invoice_number', 'amount', 'tax', 'total', 'issue_date', 'due_date', 'status', 'notes', 'paid_at'];
    protected $dates = ['issue_date', 'due_date', 'paid_at'];
    
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
    
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
}
