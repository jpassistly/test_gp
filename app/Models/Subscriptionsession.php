<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriptionsession extends Model
{
    use HasFactory;
    
    protected $table = "subscription_session";
    
    protected $fillable = [
        'name',
        'status',
        'created_by',
        ];
 }