<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
//...
    protected $table = 'areas';

    protected $fillable = [
        'name',
        'status',
        'pincode',
        'created_by',
        'updated_by',
        'del',
    ];
}
