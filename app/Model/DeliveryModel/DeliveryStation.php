<?php

namespace App\Model\DeliveryModel;

use App\Model\BasicModel\PaymentType;
use App\Model\FinanceModel\DSBusinessCreditBalanceHistory;
use App\Model\FinanceModel\DSDeliveryCreditBalanceHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Model\UserModel\User;
use App\Model\OrderModel\Order;
use App\Model\FinanceModel\DSCalcBalanceHistory;
use App\Model\OrderModel\OrderCheckers;
use DateTime;
use DateTimeZone;

class DeliveryStation extends Authenticatable
{
    protected $table = 'deliverystations';
    protected $fillable = [
        'name',
        'address',
        'boss',
        'phone',
        'number',
        'image_url',
        'factory_id',
        'station_type',
        'payment_calc_type',
        'billing_account_name',
        'billing_account_card_no',
        'freepay_account_name',
        'freepay_account_card_no',
        'init_delivery_credit_amount',
        'init_guarantee_amount',
        'guarantee_receipt_path',
        'calculation_balance',
        'delivery_credit_balance',
        'init_business_credit_amount',
        'business_credit_balance',
        'last_used_ip',
        'last_session',
        'userkind',
        'status',
        'is_deleted',
    ];

    public $timestamps = false;

    protected $appends = [
        'total_count',
        'received_order_money',
        'receivable_order_money',
        'orders_in_month',

        'payment_calc_type_str',

        //Money
        'money_orders_really_got',
        'money_orders_really_got_sum',
        'money_orders_of_others',
        'money_orders_of_mine',

        //Wechat
        'wechat_orders',
        'wechat_orders_really_got',
        'today_wechat_orders',
        'today_wechat_order_count',
        'today_wechat_order_amount',

        //Card
        'card_orders',
        'card_orders_really_got',

        //other
        'other_orders',
        'other_orders_really_got',

        //Calculation History
        'calc_histories',
        'calc_out_total',
        'calc_in_total',
        'calc_histories_out',

        //Term Init Amount
        'term_start_amount',
        'term_start_order_count',

        //Self Business Hitory
        'self_business_history',
        'business_term_start_amount',
        'business_in',
        'business_out',

        //get all stations checkers
        'all_order_checkers',
        //get active checkers of station
        'active_order_checkers',

        //province name, city_name
        'province_name',
        'city_name',
        'district_name',
        'sub_address',

        //Station type Name
        'type_name',

        //get milkman of this stations
        'milkmans',
    ];

    const DELIVERY_STATION_TYPE_STATION_NORMAL = 1;
    const DELIVERY_STATION_TYPE_WECHAT = 2;
    const DELIVERY_STATION_TYPE_CHANNEL = 3;

    const DELIVERY_STATION_STATUS_ACTIVE = 1;
    const DELIVERY_STATION_STATUS_INACTIVE = 0;

    /**
     * 期初余额数量
     * @param $count 数量
     * @param $cost 金额
     */
    public function getBottleCountBeforeThisTerm(&$count, &$cost) {
        $count = 0;
        $cost = 0;
        $aryOrderId = array();

        // 本期日期范围
        $last_m = date('Y-m-01');

        // 订单
        $aryOrder = Order::where('delivery_station_id', $this->id)
            ->where('ordered_at', '<', $last_m)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->get();

        // 获取订单id
        foreach ($aryOrder as $o) {
            array_push($aryOrderId, $o->id);
        }

        // 查询配送明细，条件为前期完成的相反
        $plans = MilkManDeliveryPlan::whereIn('order_id', $aryOrderId)
            ->where(function ($query) use ($last_m) {
                $query->where('deliver_at', '>=', $last_m);
                $query->where('status', '<>', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED);
            })
            ->get();

        foreach ($plans as $p) {
            $count += $p->changed_plan_count;
            $cost += $p->changed_plan_count * $p->product_price;
        }
    }

    /**
     * 获取本期订单信息
     * @return mixed
     */
    public function getOrdersThisTerm() {
        // 本期日期范围
        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        // 订单
        $aryOrder = Order::where('delivery_station_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->get();

        return $aryOrder;
    }

    /**
     * 本期订单金额增加
     * @param $count
     * @param $cost
     */
    public function getBottleCountIncreasedThisTerm(&$count, &$cost) {
        $count = 0;
        $cost = 0;
        $aryOrderId = array();

        // 获取订单id
        $aryOrder = $this->getOrdersThisTerm();
        foreach ($aryOrder as $o) {
            array_push($aryOrderId, $o->id);
        }

        $plans = MilkManDeliveryPlan::whereIn('order_id', $aryOrderId)->get();
        foreach ($plans as $p) {
            $count += $p->changed_plan_count;
            $cost += $p->changed_plan_count * $p->product_price;
        }
    }

    /**
     * 本期完成订单余额
     * @param $count
     * @param $cost
     */
    public function getBottleDoneThisTerm(&$count, &$cost) {
        $count = 0;
        $cost = 0;

        // 本期日期范围
        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        // 查询配送明细
        $plans = MilkManDeliveryPlan::where('station_id', $this->id)
            ->where('deliver_at', '>=', $first_m)
            ->where('deliver_at', '<=', $last_m)
            ->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
            ->get();

        foreach ($plans as $p) {
            $count += $p->changed_plan_count;
            $cost += $p->changed_plan_count * $p->product_price;
        }
    }

    public function getMilkmansAttribute()
    {
        $milkmans = MilkMan::where('station_id', $this->id)->where('is_active', 1)->get();

        return $milkmans;
    }

    //get milkman who can delivery the product to the address in this station
    //first milkman
    public function get_milkman_of_address($address)
    {
        $sid  = $this->id;

        $milkmans = MilkMan::where('station_id', $sid)->where('is_active', 1)->get();
        $result_milkman = null;

        foreach($milkmans as $milkman)
        {
            $milkman_id = $milkman->id;
            $areas = MilkManDeliveryArea::where('milkman_id', $milkman_id)
                ->where('address', 'like', $address)->get()->first();

            if($areas)
            {
                $result_milkman = $milkman;
                break;
            }

        }

        return $result_milkman;
    }

    public function getTypeNameAttribute()
    {
        if ($this->station_type == $this::DELIVERY_STATION_TYPE_STATION_NORMAL) {
            return "奶站";
        } else if ($this->station_type == $this::DELIVERY_STATION_TYPE_WECHAT) {
            return "代理商";
        } else {
            return "渠道";
        }
    }


    public function getPaymentCalcTypeStrAttribute(){
        $payment_calc_type_id = $this->payment_calc_type;

        $payment_calc_type = DSPaymentCalcType::find($payment_calc_type_id);

        return $payment_calc_type->name;
    }

    public function getProvinceNameAttribute()
    {
        if ($this->address) {
            $address = explode(' ', $this->address);
            if (count($address) >= 1)
                return $address[0];
            else
                return "";
        }
    }

    public function getCityNameAttribute()
    {
        if ($this->address) {
            $address = explode(' ', $this->address);
            if (count($address) >= 2)
                return $address[1];
            else
                return "";
        }
    }

    public function getDistrictNameAttribute()
    {
        if ($this->address) {
            $address = explode(' ', $this->address);
            if (count($address) >= 3)
                return $address[2];
            else
                return "";
        }
    }

    public function getSubAddressAttribute()
    {
        if ($this->address) {
            $address = explode(' ', $this->address);
            $count = count($address);
            if ($count >= 4) {
                $subaddr = "";
                for ($i = 0; $i< $count-3; $i++ )
                {
                    $addr = $address[$i+3];
                    $subaddr .=$addr." ";
                }
                $subaddr = trim($subaddr);
                return $subaddr;
            } else
                return "";
        }
    }

    public function getBusinessTermStartAmountAttribute()
    {
        $term_start = ($this->business_credit_balance) - ($this->business_in) + ($this->business_out);
        return round($term_start, 2);
    }

    public function getBusinessInAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        $bus_histories = DSBusinessCreditBalanceHistory::where('station_id', $this->id)
            ->whereDate('created_at', '>=', $first_m)
            ->whereDate('created_at', '<=', $last_m)
            ->where('io_type', DSBusinessCreditBalanceHistory::DSBCBH_IN)
            ->get();

        $total = 0;
        foreach ($bus_histories as $bus) {
            $total += $bus->amount;
        }
        return $total;
    }

    public function getBusinessOutAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        $bus_histories = DSBusinessCreditBalanceHistory::where('station_id', $this->id)
            ->whereDate('created_at', '>=', $first_m)
            ->whereDate('created_at', '<=', $last_m)
            ->where('io_type', DSBusinessCreditBalanceHistory::DSBCBH_OUT)
            ->get();

        $total = 0;
        foreach ($bus_histories as $bus) {
            $total += $bus->amount;
        }
        return $total;

    }

    public function getSelfBusinessHistoryAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        $histories = DSBusinessCreditBalanceHistory::where('station_id', $this->id)
            ->whereDate('created_at', '>=', $first_m)
            ->whereDate('created_at', '<=', $last_m)
            ->orderby('created_at', 'desc')
            ->get();

        return $histories;
    }

    //get out history of calculation balance
    public function getCalcHistoriesOutAttribute()
    {
        $histories = DSCalcBalanceHistory::where('station_id', $this->id)
            ->whereMonth('created_at', '=', date('m'))
            ->whereYear('created_at', '=', date('Y'))
            ->where('io_type', DSCalcBalanceHistory::DSCBH_TYPE_OUT)
            ->orderby('created_at', 'desc')
            ->get();

        return $histories;
    }

    //At term start, the initial amount
    public function getTermStartAmountAttribute()
    {
        //Current Calc Balance + OUT - IN
        $term_init = $this->calculation_balance + $this->calc_out_total - $this->calc_in_total;
        return $term_init;
    }

    //At term start, the order count remained
    public function getTermStartOrderCountAttribute()
    {
        $first_m = date('Y-m-01');

        $count_orders_before = Order::where('station_id', $this->id)->where('ordered_at', '<', $first_m)->get()->count();

        $count_orders_done_before = Order::where('station_id', $this->id)->where('ordered_at', '<', $first_m)
            ->where('status', Order::ORDER_FINISHED_STATUS)->get()->count();

        $term_start_order_count = $count_orders_before-$count_orders_done_before;

        return $term_start_order_count;

    }

    //total money out during this term
    public function getCalcOutTotalAttribute()
    {
        $sum = 0;

        $histories = $this->calc_histories;
        foreach ($histories as $history) {
            if ($history->io_type == DSCalcBalanceHistory::DSCBH_TYPE_OUT)
                $sum += $history->amount;
        }
        return $sum;
    }

    //total money got during this term
    public function getCalcInTotalAttribute()
    {
        $sum = 0;

        $histories = $this->calc_histories;
        foreach ($histories as $history) {
            if ($history->io_type == DSCalcBalanceHistory::DSCBH_TYPE_IN)
                $sum += $history->amount;
        }
        return $sum;
    }

    //get calculation history
    public function getCalcHistoriesAttribute()
    {
        $histories = DSCalcBalanceHistory::where('station_id', $this->id)
            ->whereMonth('created_at', '=', date('m'))
            ->whereYear('created_at', '=', date('Y'))
            ->get();

        return $histories;
    }

    //Other Stations ORDERS
    //get other stations orders that has received wechat from station
    public function getOtherOrdersReallyGotAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');
        $orders = Order::where('station_id', '!=', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->where('ordered_at', '<=', $last_m)->where('trans_check', Order::ORDER_TRANS_CHECK_TRUE)
            ->where('delivery_station_id', '=', $this->id)->get();

        return $orders;
    }

    //get other stations orders
    public function getOtherOrdersAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');
        $orders = Order::where('station_id', '!=', $this->id)->where('ordered_at', '>=', $first_m)->where('ordered_at', '<=', $last_m)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->where('delivery_station_id', '=', $this->id)->get();
        return $orders;
    }

    //Card ORDERS
    //get card orders that has received wechat from station
    public function getCardOrdersReallyGotAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('delivery_station_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->where('ordered_at', '<=', $last_m)->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->where('trans_check', Order::ORDER_TRANS_CHECK_TRUE)
            ->get();

        return $orders;
    }

    //get card orders
    public function getCardOrdersAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('delivery_station_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->get();

        return $orders;
    }

    //WECHAT ORDERS
    //get wechat orders that has received wechat from station
    public function getWechatOrdersReallyGotAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        $orders = Order::where('delivery_station_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->where('trans_check', Order::ORDER_TRANS_CHECK_TRUE)
            ->get();

        return $orders;
    }

    //get wechat orders
    public function getWechatOrdersAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('delivery_station_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)->get();

        return $orders;
    }


    public function getTodayWechatOrdersAttribute()
    {
        $today = getCurDateString();

        $orders = Order::where('station_id', $this->id)
            ->where('ordered_at', $today)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->get();

        return $orders;
    }


    //wechat order count this month
    public function getTodayWechatOrderCountAttribute()
    {
        return count($this->today_wechat_orders);
    }

    public function getTodayWechatOrderAmountAttribute()
    {
        return $this->getSumOfOrders($this->today_wechat_orders);
    }


    //MONEY ORDERS
    //get money orders of mine
    public function getMoneyOrdersOfMineAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('station_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->where('delivery_station_id', '=', $this->id)->get();

        return $orders;
    }

    //get money orders of others
    public function getMoneyOrdersOfOthersAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('station_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->where('delivery_station_id', '!=', $this->id)->get();

        return $orders;
    }

    //get money orders that has received money from station
    public function getMoneyOrdersReallyGotAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('delivery_station_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->where('status', Order::ORDER_FINISHED_STATUS)
            ->get();

        return $orders;
    }

    //get money orders that has received money from station
    public function getMoneyOrdersReallyGotSumAttribute()
    {
        $res = DSCalcBalanceHistory::where('station_id', $this->id)
            ->where('type', DSCalcBalanceHistory::DSCBH_IN_MONEY_STATION)
            ->whereMonth('created_at', '=', date('m'))
            ->whereYear('created_at', '=', date('Y'))
            ->get()
            ->sum('amount');

        return $res;
    }

    /**
     * 计算期初没收款的金额
     * @return int
     */
    public function getMoneyNotReceivedStartOfMonth(){
        $first_m = date('Y-m-01');

        $orders = Order::where('station_id', $this->id)
            ->where('ordered_at', '<', $first_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->where(function($query){
                    $query->where('status', '<>', Order::ORDER_WAITING_STATUS);
                    $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                    $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                    $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
                })
            ->get();

        $order_total = $this->getSumOfOrders($orders);

        $calc_histories = DSCalcBalanceHistory::where('station_id', $this->id)
            ->where('time', '<', $first_m)
            ->where('io_type', DSCalcBalanceHistory::DSCBH_TYPE_IN)
            ->where('type', DSCalcBalanceHistory::DSCBH_IN_MONEY_STATION)
            ->get();

        $received_total = $this->getSumOfHistories($calc_histories);

        return $order_total - $received_total;
    }

    //get money orders
    public function getMoneyOrdersAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        $orders = Order::where('delivery_station_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->get();

        return $orders;
    }

    public function getMoneyOrdersInput()
    {
        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        $orders = Order::where('station_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->get();

        return $orders;
    }

    //get all orders of station in current month
    public function getOrdersInMonthAttribute()
    {
        //to current day

        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('station_id', $this->id)
            ->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->get();

        return $orders;
    }

    public function getTotalCountAttribute()
    {
        $totalCount = DeliveryStation::all()->count();
        return $totalCount;
    }

    /**
     * 获取配送范围
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function delivery_area()
    {
        return $this->hasMany('App\Model\DeliveryModel\DSDeliveryArea', 'station_id', 'id');
    }

    public function factory()
    {
        return $this->belongsTo('App\Model\FactoryModel\Factory');
    }

    /**
     * 获取配送范围根据街道分组
     * @return mixed
     */
    public function getDeliveryAreaGrouped() {
        $deliveryarea = DSDeliveryArea::where('station_id', $this->id)
            ->get()
            ->groupBy(function($area){
                $addr = $area->address;
                $addrs = explode(" ", $addr);
                return $addrs[0].$addrs[1].$addrs[2].$addrs[3];
            });

        return $deliveryarea;
    }

    /**
     * 获取配送范围街道小区信息
     * @return array
     */
    public function getDeliveryStreetVillage() {

        $aryStreet = array();

        foreach ($this->delivery_area as $area) {

            $addr = $area->address;
            $addrs = explode(" ", $addr);

            if (!isset($aryStreet[$addrs[3]])) {
                $aryStreet[$addrs[3]] = array();
            }

            array_push($aryStreet[$addrs[3]], $addrs[4]);
        }

        return $aryStreet;
    }

    /**
     * 获取超级管理员
     */
    public function getUser() {
        $userinfo = User::where('backend_type','3')
            ->where('user_role_id', '200')
            ->where('station_id', $this->id)
            ->get()->first();

        return $userinfo;
    }

    //get all order checkers in station
    public function getAllOrderCheckersAttribute()
    {
        $order_checkers = OrderCheckers::where('station_id', $this->id)->get();
        return $order_checkers;
    }

    //get active order checkers in station
    public function getActiveOrderCheckersAttribute()
    {
        $order_checkers = OrderCheckers::where('station_id', $this->id)->where('is_active', 1)->get();
        return $order_checkers;
    }

    //Insert money amount received really for money order
    //From first day of month, to today, the total amount of received order money by inserting
    public function getReceivedOrderMoneyAttribute()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        //From month first, to today, total amount of order money really received
        $received_order_money_histories = DSDeliveryCreditBalanceHistory::where('station_id', $this->id)->where('time', '>=', $first_m)
            ->where('time', '<=', $last_m)->where("type", DSDeliveryCreditBalanceHistory::DSDCBH_TYPE_IN_MONEY)->get();
        $total = 0;
        foreach ($received_order_money_histories as $romh) {
            $total += $romh->amount;
        }
        return $total;
    }

    public function getSumOfOrders($orders)
    {
        $sum = 0;
        if ($orders) {
            foreach ($orders as $order) {
                $sum += $order->total_amount;
            }
        }
        return $sum;
    }

    public function getSumOfHistories($histories)
    {
        $total = 0;
        foreach ($histories as $his) {
            $total += $his->amount;
        }
        return $total;
    }

    public function getReceivableOrderMoneyAttribute()
    {
        $money_orders = $this->getMoneyOrdersInput();
        $money_orders_sum = $this->getSumOfOrders($money_orders);

        $money_orders_really_got_sum = $this->money_orders_really_got_sum;

        $receivable = $money_orders_sum - $money_orders_really_got_sum + $this->getMoneyNotReceivedStartOfMonth();

        return $receivable;
    }

    //get other money orders
    public function get_other_orders_not_checked()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('factory_id', $this->factory_id)->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->where('station_id', $this->id)->whereRaw('station_id != delivery_station_id')
            ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })->get();
        return $orders;
    }

    public function get_other_orders_not_checked_for_transaction()
    {
        $orders = Order::where('factory_id', $this->factory_id)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->where('station_id', $this->id)->whereRaw('station_id != delivery_station_id')
            ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
            })->get();
        return $orders;
    }

    //get total money orders to send others
    public function get_other_orders_money_total()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('factory_id', $this->factory_id)->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->where('station_id', $this->id)->whereRaw('station_id != delivery_station_id')
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->get();

        $total = 0;
        foreach ($orders as $order) {
            $total += $order->total_amount;
        }
        return $total;
    }

    public function get_other_orders_checked_money_total()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('factory_id', $this->factory_id)->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->where('station_id', $this->id)->whereRaw('station_id != delivery_station_id')
            ->where('trans_check', Order::ORDER_TRANS_CHECK_TRUE)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
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
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('factory_id', $this->id)->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->where('station_id', $this->id)
            ->whereRaw('station_id != delivery_station_id')->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->get();

        $total = 0;
        foreach ($orders as $order) {
            $total += $order->total_amount;
        }
        return $total;
    }

    //get card money orders
    public function get_card_orders()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('factory_id', $this->factory_id)->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->where('station_id', $this->id)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->get();
        return $orders;
    }

    public function get_card_orders_not_checked()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('factory_id', $this->factory_id)->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->where('station_id', $this->id)
            ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->get();
        return $orders;
    }

    public function get_card_orders_not_checked_for_transaction()
    {
        $orders = Order::where('factory_id', $this->factory_id)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->where('station_id', $this->id)
            ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->get();
        return $orders;
    }


    //get total money orders to send others
    public function get_card_orders_money_total()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('factory_id', $this->factory_id)->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->where('station_id', $this->id)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })
            ->get();

        $total = 0;
        foreach ($orders as $order) {
            $total += $order->total_amount;
        }
        return $total;
    }

    public function get_card_orders_checked_money_total()
    {
        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('factory_id', $this->factory_id)->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->where('station_id', $this->id)
            ->where('trans_check', Order::ORDER_TRANS_CHECK_TRUE)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })->get();

        $total = 0;
        foreach ($orders as $order) {
            $total += $order->total_amount;
        }
        return $total;
    }

    public function get_card_orders_unchecked_money_total()
    {

        $first_m = date('Y-m-01');
        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $orders = Order::where('factory_id', $this->id)->where('ordered_at', '>=', $first_m)
            ->where('ordered_at', '<=', $last_m)->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->where('station_id', $this->id)
            ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where(function($query){
                $query->where('status', '<>', Order::ORDER_NEW_WAITING_STATUS);
                $query->where('status', '<>', Order::ORDER_NEW_NOT_PASSED_STATUS);
                $query->where('status', '<>', Order::ORDER_CANCELLED_STATUS);
            })->get();

        $total = 0;
        foreach ($orders as $order) {
            $total += $order->total_amount;
        }
        return $total;
    }

    public function getBottleCountOfOrders($orders)
    {
        $total= 0;
        foreach ($orders as $order)
        {
            $total +=$order->total_count;
        }
        return $total;
    }

    /**
     * 添加自营账户余额
     * @param $amount
     */
    public function addSelfOrderAccount($amount) {
        $this->business_credit_balance += $amount;
        $this->save();
    }

    public function getChangeStartDate() {
        // 正常是返回当天
        $dateStart = getCurDateString();

        // 已生成配送列表，返回第二天
        if (DSDeliveryPlan::getDeliveryPlanGenerated($this->id)) {
            $dateStart = getNextDateString();
        }

        return $dateStart;
    }
}
