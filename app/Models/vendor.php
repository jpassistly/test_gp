<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vendor extends Model
{
    use HasFactory;
    protected $table = 'vendors';

    protected $fillable = [
        'name',
        'mobile',
        'email',
        'type',
        'address',
        'status',
        'created_by',
        'updated_by'
        
    ];
}
