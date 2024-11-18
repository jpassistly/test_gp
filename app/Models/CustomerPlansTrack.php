<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPlansTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'customers_id',
        'product_ids',
        'quantity',
        'plan_id',
        'start_date',
        'end_date',
        'total_amount',
        'discount',
        'final_price',
        'remarks',
        'created_by',
        'updated_by',
    ];
}
