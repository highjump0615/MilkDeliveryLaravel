<?php

namespace App\Model\WechatModel;

use App\Model\DeliveryModel\DeliveryType;
use App\Model\DeliveryModel\MilkManDeliveryPlan;
use App\Model\OrderModel\OrderProduct;
use App\Model\OrderModel\OrderType;
use Illuminate\Database\Eloquent\Model;

class TempDeliveryPlan
{
    protected $fillable = [
        'changed_plan_count',
        'product_name',
        'deliver_at',
    ];
};

class WechatOrderProduct extends OrderProduct
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
        'custom_order_dates',
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

    public function get_temp_plans()
    {
        $wp_plans = [];

        $total_count = $this->total_count;

        $dp = new TempDeliveryPlan;
        //Product Name
        $dp->product_name = $this->product_simple_name;

        //deliver at
        $startAt = $this->start_at;
        $deliver_at = $this->getClosestDeliverDate($startAt);

        //Changed Plan Count
        $first_count =$this->getDeliveryTypeCount($deliver_at);
        $dp->changed_plan_count = $first_count;
        $dp->deliver_at = $deliver_at;

        $wp_plans[] = $dp;

        $total_count -= $first_count;

        //get next delivery plans
        while ($total_count > 0)
        {
            $ndp = new TempDeliveryPlan();
            $ndp->product_name = $this->product_name;

            $deliver_at = $this->getNextDeliverDate($deliver_at);
            $ndp->deliver_at = $deliver_at;

            $new_count = $this->getDeliveryTypeCount($deliver_at);

            if($total_count - $new_count>0)
                $ndp->changed_plan_count= $new_count;
            else
                $ndp->changed_plan_count= min($total_count, $new_count);

            $total_count -= $new_count;

            $wp_plans[] = $ndp;
        }
        
        return $wp_plans;
    }
}
