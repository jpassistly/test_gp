<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'quantity_id',
        'measurement_id',
        'price',
        'description',
        'pic',
        'status',
        'created_by',
        'updated_by',
        'product_id',
        'subscription'
    ];
}
