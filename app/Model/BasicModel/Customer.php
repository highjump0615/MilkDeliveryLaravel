<?php

namespace App\Model\BasicModel;

use App\Model\DeliveryModel\MilkManDeliveryPlan;
use Illuminate\Database\Eloquent\Model;
use App\Factory;
use App\Model\DeliveryModel\DSDeliveryArea;
use App\Model\OrderModel\Order;


class Customer extends Model
{
    public $table = 'customer';

    protected $fillable = [
        'name',
        'phone',
        'address',
        'station_id',
        'status',
        'milkman_id',
        'factory_id',
        'remain_amount',
        'remain_order_amount',
    ];

    protected $appends =[
        'province',
        'city',
        'district',
        'street',
        'xiaoqu',
        'sub_addr',
        'has_milkbox',
        'remaining_bottle_count',
    ];


    public function getRemainOrderAmountAttribute()
    {
        $total_remain_order_amount= 0;

        $orders = Order::where('customer_id', $this->id)
            ->where(function ($query) {
                $query->where('status', Order::ORDER_PASSED_STATUS);
                $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
                $query->orWhere('status', Order::ORDER_STOPPED_STATUS);
            })->get()->all();

        foreach($orders as $order)
        {
            $total_remain_order_amount+= $order->remaining_amount;
        }

        return $total_remain_order_amount;
    }

    public function getRemainingBottleCountAttribute()
    {
        $total_remain_count = 0;
        //get all orders from customer
        $orders = Order::where('customer_id', $this->id)
            ->where(function ($query) {
                $query->where('status', Order::ORDER_PASSED_STATUS);
                $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
                $query->orWhere('status', Order::ORDER_STOPPED_STATUS);
            })->get()->all();

        foreach($orders as $order)
        {
            $plans = $order->unfinished_delivery_plans;
            foreach($plans as $plan)
            {
                $total_remain_count+=$plan->delivery_count;
            }
        }

        return $total_remain_count;
    }

    public function getHasMilkboxAttribute()
    {
        $orders = $this->Order();
        if(count($orders) > 0)
        {
            return true;
        } else {
            return false;
        }
    }

    public function Order()
    {
        return $this->hasMany('App\Model\OrderModel\Order');
    }

    public function getProvinceAttribute()
    {
        $address = $this->address;
        if($address)
        {
            $addr_array = explode(' ', $address);
            if(count($addr_array) > 0)
                return $addr_array[0];
            else
                return "";
        } else
            return "";
    }

    public function getCityAttribute()
    {
        $address = $this->address;
        if($address)
        {
            $addr_array = explode(' ', $address);
            if(count($addr_array) > 1)
                return $addr_array[1];
            else
                return "";
        } else
            return "";
    }

    public function getDistrictAttribute()
    {
        $address = $this->address;
        if($address)
        {
            $addr_array = explode(' ', $address);
            if(count($addr_array) > 2)
                return $addr_array[2];
            else
                return "";
        } else
            return "";
    }

    public function getStreetAttribute()
    {
        $address = $this->address;
        if($address)
        {
            $addr_array = explode(' ', $address);
            if(count($addr_array) > 3)
                return $addr_array[3];
            else
                return "";
        } else
            return "";
    }

    public function getXiaoquAttribute()
    {
        $address = $this->address;
        if($address)
        {
            $addr_array = explode(' ', $address);
            if(count($addr_array) > 4)
                return $addr_array[4];
            else
                return "";
        } else
            return "";
    }

    public function getSubAddrAttribute()
    {
        $address = $this->address;
        if($address)
        {
            $addr_array = explode(' ', $address);
            if(count($addr_array) > 5)
            {
                $length = count($addr_array);
                if($length == 6)
                    return $addr_array[5];
                else{
                    $sub_addr="";
                    for($i=5; $i< $length; $i++)
                    {
                        $sub_addr .=$addr_array[$i];
                    }

                    return $sub_addr;
                }
            }
            else
                return "";
        } else
            return "";
    }

    public function getMainAddrAttribute()
    {
        $main_addr = str_replace($this->sub_addr, " ", $this->address);
        return $main_addr;
    }


    public function get_plans_for_date($date)
    {
        $customer_id = $this->id;
        /*
         * //show all delivery plan including admin
        $orders = Order::where('customer_id', $customer_id)->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
            ->where(function ($query) {
                $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
                $query->orwhere('status', Order::ORDER_PASSED_STATUS);
            })
            ->orderBy('id', 'desc')
            ->get()->all();*/

        $orders = Order::where('customer_id', $customer_id)
            ->where(function ($query) {
                $query->where('status', Order::ORDER_ON_DELIVERY_STATUS);
                $query->orwhere('status', Order::ORDER_PASSED_STATUS);
                $query->orWhere('status', Order::ORDER_STOPPED_STATUS);
            })
            ->orderBy('id', 'desc')
            ->get()->all();

        $plans = array();
        foreach($orders as $order)
        {
            $order_id = $order->id;
            $plans_o = MilkManDeliveryPlan::where('order_id', $order_id)->where('deliver_at', $date)->where('status', '!=', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL)->get()->all();
            if($plans_o)
            {
                foreach($plans_o as $plan)
                {
                    array_push($plans, $plan);
                }
            }
        }

        return $plans;
    }

}
