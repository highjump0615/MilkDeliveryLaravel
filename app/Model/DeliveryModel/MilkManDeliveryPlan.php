<?php

namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;
use App\Model\OrderModel\OrderProduct;
use App\Model\OrderModel\Order;

class MilkManDeliveryPlan extends Model
{
    public $timestamps = false;

    protected $table = "milkmandeliveryplan";

    protected $fillable =[
        'order_id',
        'station_id',
        'customer_id',
        'order_product_id',
        'time',
        'status',
        'product_price',
        'produce_at',
        'deliver_at',
        'plan_count',
        'changed_plan_count',
        'delivery_count',
        'delivered_count',
        'comment',
        'type',
        'flag',
        'milkman_id',
        'cancel_reason',
    ];

    protected $appends = [
        'plan_price',
        'station_id',
        'product_name',
        'status_name',
        'plan_product_image',
    ];

    const MILKMAN_DELIVERY_PLAN_TYPE_USER = 1;
    const MILKMAN_DELIVERY_PLAN_TYPE_GROUP = 2;
    const MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL = 3;
    const MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK = 4;
    const MILKMAN_DELIVERY_PLAN_TYPE_RETAIL = 6;
    const MILKMAN_DELIVERY_PLAN_TYPE_MILKBOXINSTALL = 5;

    const MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER = 1;
    const MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER_RULE_CHANGE = 2;

    const MILKMAN_DELIVERY_PLAN_STATUS_CANCEL = 0;
    const MILKMAN_DELIVERY_PLAN_STATUS_WAITING = 1;
    const MILKMAN_DELIVERY_PLAN_STATUS_PASSED = 2;
    const MILKMAN_DELIVERY_PLAN_STATUS_SENT = 3;
    const MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED = 4;


    public function getPlanProductImageAttribute()
    {
        $order_product = OrderProduct::find($this->order_product_id);
        if($order_product)
        {
            $product = $order_product->product;
            return $product->photo_url1;
        }
    }


    public function getStatusNameAttribute(){
        if($this->status == $this::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
            return "已配送";
        elseif($this->status == $this::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL)
            return $this->cancel_reason;
        else
            return "未配送";
    }

    public function getProductNameAttribute()
    {
        $op = OrderProduct::find($this->order_product_id);
        if($op)
            return $op->product_name;
        else
            return "";
    }

    public function getPlanPriceAttribute()
    {
        $plan_price = ($this->product_price) * ($this->changed_plan_count);
        return $plan_price;
    }

    public function getStationIdAttribute(){
        $station = Order::find($this->order_id);
        if($station == null){
            return null;
        }
        else{
            return $station->station_id;
        }
    }
    //

    public function order(){
        if($this->type == 1)
            return $this->belongsTo('App\Model\OrderModel\Order');
        else
            return $this->belongsTo('App\Model\OrderModel\SelfOrder');
    }

    public function order_product(){
        if($this->type == 1)
            return $this->belongsTo('App\Model\OrderModel\OrderProduct');
        else
            return $this->belongsTo('App\Model\OrderModel\SelfOrderProduct');
    }

    public function milkman(){
        return $this->belongsTo('App\Model\DeliveryModel\MilkMan');
    }

    public function getTypeDesc() {
        $strRes = '';

        if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER) {
            $strRes = '计划订单';
        }
        else if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_GROUP) {
            $strRes = '团购业务';
        }
        else if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK) {
            $strRes = '试饮赠品';
        }
        else if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL) {
            $strRes = '渠道业务';
        }
        else if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_RETAIL) {
            $strRes = '店内零售';
        }

        return $strRes;
    }
}
