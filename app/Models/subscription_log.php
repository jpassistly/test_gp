<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subscription_log extends Model
{
    use HasFactory;

    protected $table = 'subscription_logs';

    protected $fillable = [
        'customers_id',
        'name',
        'mobile',
        'total_price',
        'discount',
        'final_price',
        'status',
        'remarks',
        'created_by',
        'updated_by'
    ];
}
