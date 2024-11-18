<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    use HasFactory;
    protected $table = 'measurements';

    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by',
        'del',
    ];
}
