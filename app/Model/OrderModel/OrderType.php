<?php

namespace App\Model\OrderModel;

use Illuminate\Database\Eloquent\Model;

class OrderType extends Model
{
    protected $table = 'ordertype';

    protected $fillable = [
        'name',
        'days',
    ];

    public $timestamps = false;

    const ORDER_TYPE_MONTH = 1;
    const ORDER_TYPE_SEASON = 2;
    const ORDER_TYPE_HALF_YEAR = 3;

    const ORDER_TYPE_MONTH_NAME="月单";
    const ORDER_TYPE_SEASON_NAME="季单";
    const ORDER_TYPE_HALF_YEAR_NAME="半年单";
}

