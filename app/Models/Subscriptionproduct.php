<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriptionproduct extends Model
{
    use HasFactory;

    protected $table = 'subcriptionproducts';

    protected $fillable = [
        'name',
        'quantity_id',
        'measurement_id',
        'price',
        'pic',
        'status',
        'created_by',
        'updated_by',
        'description',
        'expiry_date'
    ];
}
