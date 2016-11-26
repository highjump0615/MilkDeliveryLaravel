<?php

namespace App\Model\OrderModel;

use Illuminate\Database\Eloquent\Model;

class OrderTransaction extends Model
{
    protected $table = 'ordertransaction';

    protected $fillable = [
        'transaction_id',
        'customer_id',
    ];

    public $timestamps = false;
}
