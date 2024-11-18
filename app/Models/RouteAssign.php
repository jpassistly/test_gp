<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteAssign extends Model
{
    use HasFactory;

    protected $table="route_master";

    protected $fillable = [
        'delivery_line_id',
        'pincode_id',
        'area_id',
        'del'
    ];

}
