<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class delivery_sechedule extends Model
{
    use HasFactory;
    protected $table = 'delivery_sechedules';

    protected $fillable = [
        'customer_delivery_lines_id',
        'deliveryperson_id',
        'delivery_date',
        'created_by',
        'updated_by',
        'del',
        'created_at',
        'updated_at'
    ];
}
