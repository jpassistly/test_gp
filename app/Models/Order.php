<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table="orders";

   protected $fillable = [
        'order_id',
        'customer_id',
        'price',
        'delivery_date',
        'products_id',
        'quantity',
        'pincode',
        'area',
        'cus_lat_lon',
        'delivery_status',
        'delivered_by',
        'delivery_at',
        'created_at'
    ];


}
