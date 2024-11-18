<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryLineMapping extends Model
{
    use HasFactory;
    protected $table = 'delivery_line_mappings';

    protected $fillable = [
        'delivery_line_id',
        'delivery_staff_id',
        'date',
        'trip_start',
        'trip_end',
        'date',
        'created_by',
        'updated_by',
        'del',
    ];
}
