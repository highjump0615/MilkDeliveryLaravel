<?php

namespace App\Model\FactoryModel;

use App\Model\BasicModel\ProvinceData;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\DSDeliveryArea;
use App\Model\OrderModel\OrderCheckers;
use App\Model\ProductModel\Product;
use Illuminate\Database\Eloquent\Model;
use App\Model\OrderModel\Order;
use App\Model\BasicModel\PaymentType;
use App\Model\FactoryModel\FactoryDeliveryType;
use App\Model\BasicModel\Address;

class Factory extends Model
{
    const FACTORY_STATUS_ACTIVE  = 1;
    const FACTORY_STATUS_INACTIVE  = 0;
    
    protected $table = 'factory';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'number',
        'contact',
        'phone',
        'status',
        'last_used_ip',
        'started_at',
        'end_at',
        'logo_url',
        'pubilc_name',
        'public_id',
        'wechat_id',
        'app_secret',
        'qrcode',
        'wechat_type',
        'status',
        'is_deleted',
        'factory_id',
        'factory_password',
        'gap_day',
        'service_phone',
        'return_phone'
    ];

    protected $appends = [
        'factory_order_types',
        'ordercheckers',
        'order_delivery_types',
        'active_stations',
        'active_products',
        'factory_provinces',
        'active_box_types',
        'first_active_address',
    ];


    public function getFactoryProvincesAttribute()
    {
        $provinces = Address::where('level', 1)->where('factory_id', $this->id)->get();
        return $provinces;
    }

    public function getFirstActiveAddressAttribute()
    {
        $provinces = Address::where('level', 1)->where('factory_id', $this->id)->get();
        $result = "";
        foreach($provinces as $province)
        {
            $city = Address::where('level',2)->where('factory_id', $this->id)->where('parent_id', $province->id)->get()->first();
            if($city)
            {
                $result = $province->name.' '.$city->name;
                break;
            }
        }

        if($result)
        {
            return $result;
        }
    }
   

    public function getActiveStationsAttribute()
    {
        $stations = DeliveryStation::where('factory_id', $this->id)->where('is_deleted', 0)->where('status', DeliveryStation::DELIVERY_STATION_STATUS_ACTIVE)->get();
        return $stations;
    }

    public function deliveryTypes()
    {
        return $this->belongsToMany('App\Model\DeliveryModel\DeliveryType', 'mfdeliverytype', 'factory_id', 'delivery_type');
    }

    public function delivery_time()
    {
        return $this->hasOne('App\Model\FactoryModel\FactoryDeliveryTime');
    }

    public function box_types()
    {
        return $this->hasMany('App\Model\FactoryModel\FactoryBoxType');
    }

    public function getActiveBoxTypesAttribute()
    {
        return FactoryBoxType::where('factory_id', $this->id)->where('is_deleted', 0)->get();
   }

    public function bottle_types()
    {
        return $this->hasMany('App\Model\FactoryModel\FactoryBottleType');
    }

    public function products()
    {
        return $this->hasMany('App\Model\ProductModel\Product');
    }

    public function getActiveProductsAttribute()
    {
        $products = Product::where('factory_id', $this->id)->where('is_deleted', 0)->where('status', Product::PRODUCT_STATUS_ACTIVE)->get();

        return $products;
    }

    public function getFactoryOrderTypesAttribute()
    {
        //return $this->hasMany('App\Model\FactoryModel\FactoryOrderType');
        return FactoryOrderType::where('factory_id', $this->id)->where('is_active', 1)->get();
    }

    public function getOrderDeliveryTypesAttribute()
    {
        return FactoryDeliveryType::where('factory_id', $this->id)->where('is_active', FactoryDeliveryType::FACTORY_DELIVERY_TYPE_ACTIVE)->get()->all();
    }

    public function deliveryStations()
    {
        return $this->hasMany('App\Model\DeliveryModel\DeliveryStation');
    }

    //TOTO: Show only factory's ordercheckers
    public function getOrdercheckersAttribute()
    {
        $ordercheckers = OrderCheckers::where('or_factory_id', $this->id)
            ->where('is_active', 1)->get();
        return $ordercheckers;
    }

    //get other money orders
    public function get_other_orders_not_checked()
    {
        $first_m = date('Y-m-01');
        $last_m = date('Y-m-d');

        $orders = Order::where('factory_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->whereRaw('station_id != delivery_station_id')
            ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
            ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)->get();
        return $orders;
    }


    //get other money orders for transaction
    public function get_other_orders_not_checked_for_transaction()
    {
        $orders = Order::where('factory_id', $this->id)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->whereRaw('station_id != delivery_station_id')
            ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
            ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)
            ->get();

        return $orders;
    }

    //get total money orders to send others
    public function get_other_orders_money_total()
    {
       $first_m = date('Y-m-01');
        $last_m = date('Y-m-d');

        $orders = Order::where('factory_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->whereRaw('station_id != delivery_station_id')
            ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
            ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)->get();

        $total = 0;
        foreach ($orders as $order) {
            $total += $order->total_amount;
        }
        return $total;
    }

    public function get_other_orders_checked_money_total()
    {
        $first_m = date('Y-m-01');
        $last_m = date('Y-m-d');

        $orders = Order::where('factory_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            -> whereRaw('station_id != delivery_station_id')
            ->where('trans_check', Order::ORDER_TRANS_CHECK_TRUE)
            ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
            ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)
            ->get();

        $total = 0;
        foreach ($orders as $order) {
            $total += $order->total_amount;
        }
        return $total;
    }

    public function get_other_orders_unchecked_money_total()
    {

        $first_m = date('Y-m-01');
        $last_m = date('Y-m-d');

        $orders = Order::where('factory_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->whereRaw('station_id != delivery_station_id')
            ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
            ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)
            ->get();

        $total = 0;
        foreach ($orders as $order) {
            $total += $order->total_amount;
        }
        return $total;
    }


    //get card orders of others
    public function get_card_orders()
    {
        $first_m = date('Y-m-01');
        $last_m = date('Y-m-d');

        $orders = Order::where('factory_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
            ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)
            ->get();

        return $orders;
    }

    //get card orders not checked
    public function get_card_orders_not_checked()
    {
        $first_m = date('Y-m-01');
        $last_m = date('Y-m-d');

        $orders = Order::where('factory_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
            ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)
            ->get();

        return $orders;
    }


    //get card orders not checked for transaction
    public function get_card_orders_not_checked_for_transaction()
    {
        $orders = Order::where('factory_id', $this->id)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
            ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)
            ->get();

        return $orders;
    }


    //get total money orders to send others
    public function get_card_orders_money_total()
    {
        $first_m = date('Y-m-01');
        $last_m = date('Y-m-d');

        $orders = Order::where('factory_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
            ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)->get();

        $total = 0;
        foreach ($orders as $order) {
            $total += $order->total_amount;
        }

        return $total;
    }

    public function get_card_orders_checked_money_total()
    {
        $first_m = date('Y-m-01');
        $last_m = date('Y-m-d');

        $orders = Order::where('factory_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->where('trans_check', Order::ORDER_TRANS_CHECK_TRUE)
            ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
            ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)->get();

        $total = 0;
        foreach ($orders as $order) {
            $total += $order->total_amount;
        }
        return $total;
    }

    public function get_card_orders_unchecked_money_total()
    {
        $first_m = date('Y-m-01');
        $last_m = date('Y-m-d');

        $orders = Order::where('factory_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
            ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)->get();

        $total = 0;
        foreach ($orders as $order) {
            $total += $order->total_amount;
        }
        return $total;
    }
}
