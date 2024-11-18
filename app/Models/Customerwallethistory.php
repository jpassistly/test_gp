<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customerwallethistory extends Model
{
    use HasFactory;

    protected $table = "customer_wallet_history";

    protected $fillable = [
        'customer_wallet_id',
        'debit_credit_status',
        'amount',
        'remarks',
        'payment_history_id',
        'customer_id',
        'notes',
        ];

}
