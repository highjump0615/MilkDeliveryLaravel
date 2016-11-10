<?php

namespace App\Model\WechatModel;

use App\Model\DeliveryModel\DeliveryType;
use App\Model\OrderModel\OrderType;
use Illuminate\Database\Eloquent\Model;


class WechatOrderProduct extends Model
{
    protected $table = 'wxorderproducts';
    protected $fillable = [
        'wxuser_id',
        'factory_id',
        'product_id',
        'order_type',
        'delivery_type',
        'total_count',
        'product_price',
        'count_per_day',
        'custom_date',
        'start_at',
        'total_amount',
        'group_id',
    ];

    protected $appends = [
        'avg',
    ];

    public function product(){
        return $this->belongsTo('App\Model\ProductModel\Product');
    }

    public function getAvgAttribute()
    {
        if($this->order_type == OrderType::ORDER_TYPE_MONTH)
        {
            return round( ($this->total_count/30), 1);
        } else if($this->order_type == OrderType::ORDER_TYPE_SEASON)
        {
            return round( ($this->total_count/90), 1);
        } else {
            return round( ($this->total_count/180), 1);
        }
    }

}
