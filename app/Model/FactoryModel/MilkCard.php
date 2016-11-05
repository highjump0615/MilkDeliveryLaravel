<?php

namespace App\Model\FactoryModel;

use Illuminate\Database\Eloquent\Model;

class MilkCard extends Model
{
    protected $table = "milkcards";

    public $timestamps = false;

    protected $fillable = [
        'batch_number',
        'number',
        'product',
        'balance',
        'password',
        'sale_status',
        'pay_status',
        'recipent',
        'time',
    ];
    //

    const MILKCARD_SALES_ON = 1;
    const MILKCARD_SALES_OFF = 0;

    const MILKCARD_PAY_STATUS_ACTIVE = 1;
    const MILKCARD_PAY_STATUS_INACTIVE = 0;
}
