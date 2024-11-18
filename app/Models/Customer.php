<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use HasFactory;
    use HasApiTokens;

    protected $table = "customers";

    protected $fillable = [
        'name',
        'deliverylines_id',
        'gender',
        'home_img',
        'mobile',
        'address',
        'pincode_id',
        'area_id',
        'latlon',
        'sub_status',
        'from_date',
        'to_date',
        'deliverylines_id',
        'address_status',
        'profile_pic',
        'home_img',
        'otp',
        'remember_token',
        'status',
        'door_no',     // New field
        'floor_no',    // New field
        'street',      // New field
        'land_mark',   // New field
        'flat_no',
        'city',
        'temp_deliverylines_id',
        'edited_by',
        'edited_at',
        'type',
        'loc_status'
    ];
}
