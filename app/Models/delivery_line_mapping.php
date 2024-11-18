<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class delivery_line_mapping extends Model
{
    use HasFactory;
    protected $table = 'delivery_line_mappings';

    protected $fillable = [
        'customer_delivery_lines_id',
        'customers_id',
        'pincode',
        'area',
        'created_by',
        'updated_by',
        'del',
        'trip_start',
        'trip_end',
        'created_at',
        'updated_at'
    ];
}
