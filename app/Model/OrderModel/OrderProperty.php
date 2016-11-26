<?php

namespace App\Model\OrderModel;

use Illuminate\Database\Eloquent\Model;

class OrderProperty extends Model
{
    protected $table = 'orderproperty';

    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    const ORDER_PROPERTY_NEW_ORDER = 1;
    const ORDER_PROPERTY_XUDAN_ORDER = 2;
}
