<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = ['contractor_name', 'contract_number', 'description', 'start_date', 'end_date', 'amount', 'status', 'notes'];
    protected $dates = ['start_date', 'end_date'];
}
