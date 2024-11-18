<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customersubscription extends Model
{
    use HasFactory;

    protected $table = "customers_subscription";

    protected $fillable = [
        'subscription_customer_id',
        'subscription_products_id',
        'subscription_quantity',
        'subscription_total_qty',
        'date',
        'subscription_session_id',
        'delivery_status',
        'addon_status',
        'comments',
        'deliveryperson_id',
        'delivery_at',
        'rating',
        'pincode',
        'area',
        'from_date',
        'to_date',
        'delivery_lat_lon',
        'delivery_line_id',
        'allow_grace',
        'grace_period',
        'customer_track_id',
        'edited_product_id',
        'edited_at'

    ];
    
}
