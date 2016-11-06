<?php

namespace App\Model\OrderModel;

use App\Model\DeliveryModel\MilkManDeliveryPlan;
use Illuminate\Database\Eloquent\Model;
use App\Model\OrderModel\OrderType;
use App\Model\ProductModel\Product;
use App\Model\DeliveryModel\DeliveryType;

class OrderProduct extends Model
{
    const ORDER_PRODUCT_ORDERTYPE_YUEDAN = 1;
    const ORDER_PRODUCT_ORDERTYPE_JIDAN = 2;
    const ORDER_PRODUCT_ORDERTYPE_BANNIANDAN = 3;

    protected $table = 'orderproducts';

    protected $fillable = [
        'order_id',
        'product_id',
        'count_per_day',
        'order_type',
        'delivery_type',
        'custom_order_dates',
        'total_count',
        'total_amount',
        'product_price',
        'avg',
        'start_at'
    ];

    protected $appends = [
        'product_name',
        'order_type_name',
        'delivery_type_name',
        'finished_count',
        'remain_count',
        'remain_amount',
        'custom_order_dates_on_this_month',
        'last_deliver_plan',
        'finished_money_amount',
    ];

    public $timestamps = false;

    public function getFinishedMoneyAmountAttribute(){
        //$this->total_amount;
        $mdps = MilkManDeliveryPlan::where('order_id', $this->order_id)
            ->where('order_product_id', $this->id)
            ->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
            ->get();
        $finished_amount = $finished_count = 0;

        if($mdps)
        {
            foreach($mdps as $mdp)
            {
                $finished_count += $mdp->delivered_count;
                $finished_amount += $finished_count*$mdp->product_price;
            }
        }


        return $finished_amount;
    }


    public function getLastDeliverPlanAttribute(){
        return MilkManDeliveryPlan::where('order_product_id', $this->id)->orderBy('deliver_at', 'desc')->get()->first();
    }

    public function product(){
        return $this->belongsTo('App\Model\ProductModel\Product');
    }

    public function order()
    {
        return $this->belongsTo('App\Model\OrderModel\Order');
    }

    
    public function getProductNameAttribute()
    {
        $product = Product::find($this->product_id);
        if($product)
            return $product->name;
        else
            return "";
    }

    public function getOrderTypeNameAttribute()
    {
        $order_type = OrderType::find($this->order_type);
        if($order_type)
            return $order_type->name;
        else
            return "";
    }

    public function getDeliveryTypeNameAttribute()
    {
        $dt = DeliveryType::find($this->delivery_type);
        if($dt)
        {
            return $dt->name;
        } else
            return "";
    }

    public function getFinishedCountAttribute()
    {
        $order_plans = MilkManDeliveryPlan::where('order_product_id', $this->id)->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)->get();
        $done = 0;
        foreach($order_plans as $order_plan)
        {
            $done += $order_plan->delivered_count;
        }
        return $done;
    }

    public function getRemainCountAttribute()
    {
        $total_count = $this->total_count;
        $finished_count = $this->finished_count;
        $remain_count = $total_count-$finished_count;
        return $remain_count;
    }

    public function getRemainAmountAttribute()
    {
        return $this->remain_count * $this->product_price;
    }

    public function getCustomOrderDatesOnThisMonthAttribute()
    {

        if(isset($this->custom_order_dates))
        {
            $ct = $this->custom_order_dates;

            if($this->delivery_type == DeliveryType::DELIVERY_TYPE_MONTH)
            {
                //return month dates
                $result = $this->get_month_days_from_order_info($ct);
            } else {
                //return week dates
                $result = $this->get_week_days_from_order_info($ct);
            }
            return $result;
        }
    }

    function get_week_days_from_order_info($string)
    {
        /* get weekday string from int:int
         * data: "1:4,2:5,3:1,4:2"
         * result: "2016-09-28:5,2016-09-27:4,2016-09-29:1,2016-09-30:2"
         * 09-26: monday = 1, sunday = 7, by date('N', strtotime(string))
        */

        $result="";
        $string = rtrim($string, ',');
        $estring = explode(',', $string);
        $ecstring = array();
        for ($i = 0; $i < count($estring); $i++) {
            $day_count = $estring[$i];
            $day_count_array = explode(':', $day_count);
            $day = trim($day_count_array[0]);
            $count = trim($day_count_array[1]);

            $date = "";
            switch($day)
            {
                case 1:
                    $date = date('Y-m-d', strtotime('monday'));
                    break;
                case 2:
                    $date = date('Y-m-d', strtotime('tuesday'));
                    break;
                case 3:
                    $date = date('Y-m-d', strtotime('wednesday'));
                    break;
                case 4:
                    $date = date('Y-m-d', strtotime('thursday'));
                    break;
                case 5:
                    $date = date('Y-m-d', strtotime('friday'));
                    break;
                case 6:
                    $date = date('Y-m-d', strtotime('saturday'));
                    break;
                case 7:
                    $date = date('Y-m-d', strtotime('sunday'));
                    break;

            }


            $ecstring[$date] = $count;
        }

        ksort($ecstring);

        foreach ($ecstring as $x => $y) {
            $result .= $x . ':' . $y . ',';
        }
        $result = rtrim($result, ',');
        return $result;

    }

    function get_month_days_from_order_info($string)
    {
        /* get weekday string from int:int
         * data: "13:1,15:1,23:3,27:4,28:5"
         * result:"2016-09-28:5,2016-09-27:4,2016-09-13:1,2016-09-15:2,2016-09-23:3"
         * 09-26: monday = 1, sunday = 7, by date('N', strtotime(string))
        */

        $result="";
        $string = rtrim($string, ',');
        $estring = explode(',', $string);
        $ecstring = array();
        for ($i = 0; $i < count($estring); $i++) {
            $day_count = $estring[$i];
            $day_count_array = explode(':', $day_count);
            $day = trim($day_count_array[0]);
            $count = trim($day_count_array[1]);

            $d = mktime(0, 0, 0, date('m'), $day, date('Y'));
            $date = date('Y-m-d', $d);

            $ecstring[$date] = $count;
        }

        ksort($ecstring);

        foreach ($ecstring as $x => $y) {
            $result .= $x . ':' . $y . ',';
        }
        $result = rtrim($result, ',');
        return $result;

    }



}
