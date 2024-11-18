<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customerpayment extends Model
{
    use HasFactory;

    protected $table = "customer_payments_history";

    protected $fillable = [
         'customers_id',
         'amount',
         'order_id',
         'transaction_id',
         'payment_status',
         'notes',

        ];
}
