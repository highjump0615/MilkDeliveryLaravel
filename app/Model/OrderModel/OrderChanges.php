<?php

namespace App\Model\OrderModel;

use Illuminate\Database\Eloquent\Model;

class OrderChanges extends Model
{
    protected $table = 'orderchanges';

    protected $fillable = [
        'customer_id',
        'order_id',
        'order_product_id',
        'type',
        'original_value',
        'changed_value',
    ];

    public $appends = [
        'station',
    ];

    const ORDERCHANGES_TYPE_ADDRESS = 1;
    const ORDERCHANGES_TYPE_PHONE = 2;
    const ORDERCHANGES_TYPE_PRODUCT = 3;
    const ORDERCHANGES_TYPE_DELIVERY_RULE = 4;
    const ORDERCHANGES_TYPE_DELIVERY_COUNT = 5;

    public function getStationIdAttribute(){
        $order = Order::find($this->order_id);
        $station_id =  $order->station_id;

        return $station_id;
    }
}
