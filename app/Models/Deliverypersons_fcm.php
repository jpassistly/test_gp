<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deliverypersons_fcm extends Model
{
    use HasFactory;

    protected $table = 'deliverypersons_fcm';

    protected $fillable = [
        'deliveryperson_id',
        'fcm_key',
        'created_at',
    ];
}
