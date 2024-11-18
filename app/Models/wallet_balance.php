<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class wallet_balance extends Model
{
    use HasFactory;
    
    protected $table = 'wallet_balances';
    
    protected $fillables = [
        
        'name',
        'amount',
        'details',
        'banner',
        'status',
        'created_by',
        'updated_by'
        
        ];
}
