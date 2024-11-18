<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventerytable extends Model
{
    use HasFactory;
    protected $table = "inventerytables";
    protected $fillable = [
        'id',
        'bid',
        'name',
        'address',
        'city',
        'state',
        'area',
        'pincode',
        'lat',
        'lon',
        'status',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'del'
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $maxBid = self::max('bid');
            $model->bid = $maxBid ? $maxBid + 1 : 1;
        });
    }
}
