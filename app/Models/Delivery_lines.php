<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery_lines extends Model
{
    use HasFactory;

    protected $table = 'delivery_lines';

    protected $fillable = [
        'name',
        'status',
        'color_code',
        'pincode_id',
        'created_by',
        'updated_by',
    ];
}
