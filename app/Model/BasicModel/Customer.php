<?php

namespace App\Model\BasicModel;

use App\Model\DeliveryModel\DeliveryStation;
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
    ];

    protected $appends =[
        'province',
        'city',
        'district',
        'street',
        'xiaoqu',
        'sub_addr',
        'has_milkbox',
        'remain_order_amount',
        'remaining_bottle_count',
    ];


    public function getRemainOrderAmountAttribute()
    {
        $total_remain_order_amount = $this->Order()
            ->where(function ($query) {
                $query->where('status', Order::ORDER_PASSED_STATUS);
                $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
            })
            ->sum('remaining_amount');

        return getEmptyValue($total_remain_order_amount);
    }

    public function getRemainingBottleCountAttribute()
    {
        $total_remain_count = MilkManDeliveryPlan::wherebetween('status', [MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING, MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT])
            ->whereHas('orderDelivery', function($queryOrder) {
                $queryOrder->where('customer_id', $this->id);
                $queryOrder->where(function ($query) {
                    $query->where('status', Order::ORDER_PASSED_STATUS);
                    $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
                });
            })
            ->sum('delivery_count');

        return getEmptyValue($total_remain_count);
    }

    public function getHasMilkboxAttribute()
    {
        if ($this->Order()->count() > 0)
        {
            return true;
        }

        return false;
    }

    /**
     * 获取此客户的所有订单
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Order()
    {
        return $this->hasMany('App\Model\OrderModel\Order');
    }

    /**
     * 获取配送员
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function milkman() {
        return $this->belongsTo('App\Model\DeliveryModel\MilkMan');
    }

    /**
     * 获取奶站
     * @return DeliveryStation
     */
    public function station(){
        return $this->belongsTo('App\Model\DeliveryModel\DeliveryStation');
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
        $plans = MilkManDeliveryPlan::where('deliver_at', $date)
            ->where('status', '!=', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL)
            ->whereHas('orderDelivery', function($queryOrder) {
                $queryOrder->where('customer_id', $this->id);
                $queryOrder->where(function ($query) {
                    $query->where('status', Order::ORDER_PASSED_STATUS);
                    $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
                });
                $queryOrder->orderBy('id', 'desc');
            })
            ->get();

        return empty($plans) ? array() : $plans;
    }

}
