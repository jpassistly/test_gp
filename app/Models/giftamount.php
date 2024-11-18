<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class giftamount extends Model
{
    use HasFactory;
    protected $table = 'giftamounts';

    protected $fillable = [
    'walet_id' ,
    'customer_id',
    'debit_credit_status',
    'amount',
    'notes',
    'created_by',
    'updated_by',
    'date',
    'current_amount'
    
];
}
