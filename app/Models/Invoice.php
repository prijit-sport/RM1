<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['booking_id', 'invoice_number', 'amount', 'tax', 'total', 'issue_date', 'due_date', 'status', 'notes'];
    protected $dates = ['issue_date', 'due_date'];
    
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
