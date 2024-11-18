<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer_fcm extends Model
{
    use HasFactory;
    protected $table = 'customers_fcm';

    protected $fillable = [
        'customer_id',
        'fcm_key',
        'created_at',
    ];
}
