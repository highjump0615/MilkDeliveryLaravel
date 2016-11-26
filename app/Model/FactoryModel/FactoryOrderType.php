<?php

namespace App\Model\FactoryModel;

use App\Model\OrderModel\Order;
use Illuminate\Database\Eloquent\Model;
use App\Model\OrderModel\OrderType;

class FactoryOrderType extends Model
{
    protected $table = 'mfordertype';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'factory_id',
        'order_type',
        'is_active',
        'name',
    ];

    protected $appends = [
        'order_count',
    ];


    public function getOrderCountAttribute()
    {
        return OrderType::find($this->order_type)->days;
    }

    public function getOrderTypeNameAttribute()
    {
        $orderType = OrderType::find($this->order_type);
        return $orderType->name;
    }

}
