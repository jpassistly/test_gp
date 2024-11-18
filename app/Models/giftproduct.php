<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class giftproduct extends Model
{
    use HasFactory;
    protected $table = 'giftproducts';

    protected $fillable = [
    'order_id' ,
    'customer_id',
    'price',
    'delivery_status',
    'delivery_date',
    'product_id',
    'quantity',
    'measurment',
    'unit',
    'created_by',
    'updated_by'
];
}
