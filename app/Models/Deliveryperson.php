<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class Deliveryperson extends Model
{
    use HasFactory;
    use HasApiTokens;

    protected $table = 'deliverypersons';

    protected $fillable = [
        'name',
        'mobile',
        'password',
        'phone',
        'aadhar_number',
        'status',
        'pic',
        'remember_token',
        'created_by',
        'updated_by',
    ];
}
