<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    
    protected $table = "customer_wallet";
    
    protected $fillable = [
        'customers_id',
        'current_amount',
        'recharged_amount_till',
        'spends_amount_till',
        'status',
        'created_by',
        'last_gift_at',
        ];
}
