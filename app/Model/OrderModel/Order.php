<?php

namespace App\Model\OrderModel;

use App\Model\DeliveryModel\DSDeliveryPlan;
use App\Model\DeliveryModel\MilkMan;
use App\Model\DeliveryModel\MilkManDeliveryArea;
use App\Model\DeliveryModel\MilkManDeliveryPlan;
use Illuminate\Database\Eloquent\Model;

use App\Model\BasicModel\Customer;
use App\Model\BasicModel\PaymentType;
use App\Model\OrderModel\OrderProperty;
use App\Model\OrderModel\OrderCheckers;
use App\Model\DeliveryModel\DeliveryStation;

use App\Model\BasicModel\Address;
use DateTime;
use DateTimeZone;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'customer_id',
        'phone',
        'address',
        'order_property_id',
        'station_id',
        'receipt_number',
        'receipt_path',
        'order_checker_id',
        'milk_box_install',
        'total_amount',
        'remaining_amount',
        'order_by_milk_card',
        'milk_card_id',
        'milk_card_code',
        'trans_check',
        'payment_type',
        'status',
//        'activated_at',
        'ordered_at',
        'stop_at',
        'restart_at',
        'start_at',
        'comment',
        'delivery_time',
        'flat_enter_mode_id',
        'previous_order_id',
        'delivery_on_other_station',
        'delivery_station_id',
        'transaction_id',
        'number',
        'factory_id',
        'is_deleted',
    ];

    protected $appends = [
        'customer_name',
        'payment_type_name',
        'order_property_name',
        'order_checker',
        'order_checker_name',
        'station_name',
        'delivery_station_name',
        'milkman_name',
        'milkman_id',
        'milkman',
        'addr_id',
        'addresses',
        'milk_box_install_label',
        'province_id',
        'city_id',
        'city_name',
        'district_id',
        'district_name',
        'street_id',
        'xiaoqu_id',
        'all_order_types',
//        'delivery_plans',
        'grouped_delivery_plans',
        'unfinished_delivery_plans',
        'delivery_plans_sent_to_production_plan',
        'waiting_passed_delivery_plans',
        'order_start_date',
        'order_end_date',
        'status_name',
        'customer',
        'has_stopped',
        'remain_order_money',
        'sub_address',
        'main_address',
        'total_count',
        'grouped_plans_per_product',
        'order_stop_end_date',
        'first_delivery_plans',
    ];
    
    const ORDER_TRANS_CHECK_TRUE = 1;
    const ORDER_TRANS_CHECK_FALSE = 0;
    
    const ORDER_NEW_WAITING_STATUS          = 1;    // 新订单待审核
    const ORDER_PASSED_STATUS               = 2;    // 未启奶
    const ORDER_ON_DELIVERY_STATUS          = 3;    // 在配送
    const ORDER_FINISHED_STATUS             = 7;    // 已完成
    const ORDER_NEW_NOT_PASSED_STATUS       = 5;    // 新订单未通过
    const ORDER_STOPPED_STATUS              = 4;    // 暂停
    const ORDER_CANCELLED_STATUS            = 6;    // 退订

    const ORDER_WAITING_STATUS              = 8;    // 订单待审核
    const ORDER_NOT_PASSED_STATUS           = 9;    // 订单未通过

    const ORDER_FLAT_ENTER_MODE_CALL_DEFAULT = 2;
    const ORDER_FLAT_ENTER_MODE_PASSWORD_DEFAULT = 1;

    // 配送时间
    const ORDER_DELIVERY_TIME_MORNING             = 1;
    const ORDER_DELIVERY_TIME_AFTERNOON           = 2;

    // 收件人地址信息
    private $mStrProvince;
    private $mStrCity;
    private $mStrDistrict;
    private $mStrStreet;
    private $mStrVillage;
    private $mStrHouseNumber;

    /**
     * 解析订单收货地址，以空格分隔的
     */
    public function resolveAddress() {
        $aryAddr = explode(' ', $this->address, 6);

        $this->mStrProvince = $aryAddr[0];
        $this->mStrCity = $aryAddr[1];
        $this->mStrDistrict = $aryAddr[2];
        $this->mStrStreet = $aryAddr[3];
        $this->mStrVillage = $aryAddr[4];
        $this->mStrHouseNumber = $aryAddr[5];
    }

    //
    // get
    //
    public function getAddrProvince() {
        return $this->mStrProvince;
    }

    public function getAddrCity() {
        return $this->mStrCity;
    }

    public function getAddrDistrict() {
        return $this->mStrDistrict;
    }

    public function getAddrStreet() {
        return $this->mStrStreet;
    }

    public function getAddrVillage() {
        return $this->mStrVillage;
    }

    public function getAddrHouseNumber() {
        return $this->mStrHouseNumber;
    }

    public function getFirstDeliveryPlansAttribute()
    {
        $plan1=MilkManDeliveryPlan::where('order_id', $this->id)->orderBy('deliver_at')->get()->first();
        if($plan1)
        {
            $first_deliver_at = $plan1->deliver_at;

            $plans = MilkManDeliveryPlan::where('order_id', $this->id)->where('deliver_at', $first_deliver_at)->get();
            return $plans;
        } else
            return null;
    }

    public function getTotalCountAttribute()
    {
        $total = 0;
        $order_products  = $this->order_products;
        foreach($order_products as $op)
        {
            $total+=$op->total_count;
        }
        return $total;
    }


    public function getSubAddressAttribute()
    {
        $sub_addr = str_replace($this->main_address, '', $this->address);
        return $sub_addr;
    }

    public function getMainAddressAttribute()
    {
        $main_addr = "";
        $addr = $this->address;
        $addr_list = explode(' ', $addr);
        $count = count($addr_list);
        if($count>=5)
            $main_addr = $addr_list[0].' '.$addr_list[1].' '.$addr_list[2].' '.$addr_list[3].' '.$addr_list[4];
        else
            $main_addr = $addr;

        return $main_addr;
    }

    public function getRemainOrderMoneyAttribute()
    {
        $order_products = $this->order_products;

        $finished_total = 0;

        if($order_products)
        {
            foreach($order_products as $op)
            {
                $finished_total += $op->finished_money_amount;
            }
        }

        $remain = $this->total_amount - $finished_total;
        return $remain;
    }

    /**
     * 重写获取status字段
     * @return int
     */
    public function getStatusAttribute(){
        $nStatus = $this->attributes['status'];

        // 假装设置STOPPED状态
        if ($this->isStopped()) {
            $nStatus = Order::ORDER_STOPPED_STATUS;
        }

        return $nStatus;
    }

    public function setStatusAttribute($value){
        $this->attributes['status'] = $value;
        $this->attributes['status_changed_at'] = getCurDateString();
    }

    /**
     * 是否暂停订单
     * @return bool
     */
    public function getHasStoppedAttribute()
    {
        // 只考虑没过期的
        $dateCurrent = date(getCurDateString());
        $dateRestart = date(getCurDateString());

        if ($dateCurrent > $dateRestart) {
            return false;
        }

        if($this->stop_at && $this->restart_at) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getStatusNameAttribute()
    {
        $status_name="待审核";

        switch($this->status)
        {
            case $this::ORDER_NEW_WAITING_STATUS:
            case $this::ORDER_WAITING_STATUS:
                $status_name="待审核";
                break;
            case $this::ORDER_ON_DELIVERY_STATUS:
                $status_name="在配送";
                break;
            case $this::ORDER_PASSED_STATUS:
                $status_name="未起奶";
                break;
            case $this::ORDER_STOPPED_STATUS:
                $status_name="暂停";
                break;
            case $this::ORDER_FINISHED_STATUS:
                $status_name="已完成";
                break;
            case $this::ORDER_NEW_NOT_PASSED_STATUS:
            case $this::ORDER_NOT_PASSED_STATUS:
                $status_name="未通过";
                break;
            case $this::ORDER_CANCELLED_STATUS:
                $status_name="退订";
                break;
            default:
                break;
        }

        // 暂停，这不是根据状态字段判断出来的
        if ($this->isStopped()) {
            $status_name="暂停";
        }

        return $status_name;
    }

    public function getOrderStartDateAttribute()
    {
        //get delivery date of last delivery plan
        $dp = MilkManDeliveryPlan::where('order_id', $this->id)->orderBy('deliver_at', 'asc')->get()->first();
        if($dp)
        {
            return $dp->deliver_at;
        } else
            return "";

    }

    public function getOrderEndDateAttribute()
    {
        //get delivery date of last delivery plan
        $last_dp = MilkManDeliveryPlan::where('order_id', $this->id)->orderBy('deliver_at', 'desc')->get()->first();
        if($last_dp)
        {
            return $last_dp->deliver_at;
        } else
            return "";

    }

    public function getOrderStopEndDateAttribute()
    {
        if($this->restart_at)
        {
            $day = date('Y-m-d', strtotime($this->restart_at.' - 1 day'));
            return $day;
        }
    }

    /**
     * 是否正在暂停状态
     * @return bool
     */
    public function isStopped() {

        if (!$this->stop_at || !$this->restart_at) {
            return false;
        }

        $dateCurrent = date(getCurDateString());
        $dateStop = date($this->stop_at);
        $dateRestart = date($this->restart_at);

        if ($dateStop <= $dateCurrent && $dateCurrent < $dateRestart) {
            return true;
        }

        return false;
    }

    /**
     * 获取暂停订单
     * @return QueryBuiler
     */
    public static function queryStopped() {
        return Order::where('stop_at', '<=', getCurDateString())->where('restart_at', '>', getCurDateString());
    }

    public function getDeliveryPlansSentToProductionPlanAttribute()
    {
        //delivery_plans_sent_to_production_plan
        $dps = MilkManDeliveryPlan::where('order_id', $this->id)
                ->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT)->get();
        return $dps;
    }

    public function getWaitingPassedDeliveryPlansAttribute()
    {
        $dps = MilkManDeliveryPlan::where('order_id', $this->id)
            ->where(function($query){
                $query->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED);
                $query->orWhere('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING);
            })->get();

        return $dps;
    }

    public function getUnfinishedDeliveryPlansAttribute()
    {
        $dps = MilkManDeliveryPlan::where('order_id', $this->id)
            ->wherebetween('status', [MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING, MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT])
            ->get();

        return $dps;
    }

    public function getLastDeliveryPlans($plan_id)
    {
        $ldps = MilkManDeliveryPlan::where('order_id', $this->id)
            ->where('status', '!=',  MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
            ->where('id', '>', $plan_id)
            ->orderBy('deliver_at', 'desc')
            ->get();

        return $ldps;
    }

    public function getGroupedDeliveryPlansAttribute()
    {
        //order_id, station_id
        $dps = MilkManDeliveryPlan::where('order_id', $this->id)->where('status', '!=', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL)->orderBy('deliver_at')->get();
        return $dps;
    }

    function array_sort($array, $on, $order=SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }


    public function getGroupedPlansPerProductAttribute()
    {
        $result_group=[];

        //get order products
        $order_products = $this->order_products_all;

        foreach ($order_products as $op)
        {
            $order_product_id = $op->id;
            $remain_count = $op->total_count;

            // 配送明细只针对订单的配送
            $op_dps = MilkManDeliveryPlan::where('order_id', $this->id)
                ->where('order_product_id', $order_product_id)
                ->where(function($query) {
                    $query->where('type', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER);
                    $query->orwhere('type', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_MILKBOXINSTALL);
                })
                // 不显示订单修改导致取消的明细
                ->where(function($query) {
                    $query->where('status', '<>', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL);
                    $query->orwhere('cancel_reason', '<>', MilkManDeliveryPlan::DP_CANCEL_CHANGEORDER);
                })
                ->orderBy('deliver_at')
                ->get();

            foreach($op_dps as $opdp)
            {
                if($opdp->status == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
                    $count = $opdp->delivered_count;
                else
                    $count = $opdp->changed_plan_count;

                $remain_count -= $count;

                $result_group[] = [
                    'time'          =>$opdp->deliver_at,
                    'plan_id'       =>$opdp->id,
                    'product_name'  =>$opdp->product_simple_name,
                    'count'         => $count,
                    'remain'        =>$remain_count,
                    'status'        =>$opdp->status,
                    'can_edit'      =>$opdp->isEditAvailable(),
                    'status_name'   =>$opdp->status_name,
                ];
            }
        }

        $new_array = $this->array_sort($result_group, 'time', SORT_ASC);

        return $new_array;
//        return $result_group;
    }


    public function getProvinceIdAttribute()
    {
//       $sa =explode(' ', $this->address);
        $sa = explode(' ', $this->address);
        $province = $sa[0];
        if(!$province)
            return 0;
        $province_m = Address::where('name', $province)->get()->first();
        if($province_m)
            return $province_m->id;
        else
            return 0;
    }

    public function getCityIdAttribute()
    {
        $province_id = $this->province_id;

        $sa = explode(' ', $this->address);
        $city = $sa[1];

        if(!$city)
            return 0;

        $city_m = Address::where('name', $city)->where('parent_id', $province_id)->get()->first();

        if($city_m)
            return $city_m->id;
        else
            return 0;
    }

    public function getCityNameAttribute()
    {
        $city_id =$this->city_id;
        $city = Address::find($city_id);
        if($city)
            return $city->name;
        else
            return "";
    }

    public function getDistrictIdAttribute()
    {
        $city_id = $this->city_id;

        $sa = multiexplode(' ', $this->address);
        $district = $sa[2];
        if(!$district)
            return 0;

        $district_m = Address::where('name', $district)->where('parent_id', $city_id)->get()->first();
        if($district_m)
            return $district_m->id;
        else
            return 0;
    }

    public function getDistrictNameAttribute()
    {
        $district_id =$this->district_id;
        $district = Address::find($district_id);
        if($district)
            return $district->name;
        else
            return "";
    }

    public function getStreetIdAttribute()
    {
        $district_id = $this->district_id;

        $sa = multiexplode(' ', $this->address);
        if(array_key_exists(3, $sa))
            $street = $sa[3];
        else
            $street = "";

        if(!$street)
            return 0;

        $street_m = Address::where('name', $street)->where('parent_id', $district_id)->get()->first();

        if($street_m)
            return $street_m->id;
        else
            return 0;
    }

    public function getXiaoquIdAttribute()
    {
        $parent_id = $this->street_id;

        $sa = multiexplode(' ', $this->address);

        if(array_key_exists(4, $sa))
            $xq = $sa[4];
        else
            $xq = "";


        if(!$xq)
            return 0;

        $xq_m = Address::where('name', $xq)->where('parent_id', $parent_id)->get()->first();
        if($xq_m)
            return $xq_m->id;
        else
            return 0;
    }



    public function getAddressesAttribute()
    {
        if($this->address)
        {
            return $this->address;
        }
        else if($this->customer_id)
        {
            $customer = Customer::find($this->customer_id);

            if($customer)
                return $customer->address;
            else
                return "";
        }
        else
            return "";
    }

    public function getCustomerAttribute()
    {
        if ($this->customer_id) {
            $customer = Customer::find($this->customer_id);
            return $customer;
        }
    }

    public function getCustomerNameAttribute()
    {
        if($this->customer_id)
        {
            $customer = Customer::find($this->customer_id);
            
            if($customer)
                return $customer->name;
            else
                return "";
        }
        else
            return "";
    }

    
    public function getPaymentTypeNameAttribute()
    {
        if($this->payment_type)
        {
            $paymenttype = PaymentType::find($this->payment_type);
            if($paymenttype)
                return $paymenttype->name;
            else
                return "";
        }
        else
            return "";
    }

    public function order_products()
    {
        return $this->hasMany('App\Model\OrderModel\OrderProduct');
    }

    public function order_products_all()
    {
        return $this->hasMany('App\Model\OrderModel\OrderProduct')->withTrashed()->orderby('id', 'desc');
    }

    public function getOrderPropertyNameAttribute()
    {
        if($this->order_property_id)
        {
            $order_property = OrderProperty::find($this->order_property_id);
            if($order_property)
                return $order_property->name;
            else
                return "";
        }
        else
            return "";
    }

    /**
     * 获取征订员信息
     * @return OrderChecker
     */
    public function getOrderCheckerAttribute()
    {
        $order_checker = null;

        if($this->order_checker_id)
        {
            $order_checker = OrderCheckers::find($this->order_checker_id);
        }

        return $order_checker;
    }

    /**
     * 获取征订员名称
     * @return string
     */
    public function getOrderCheckerNameAttribute()
    {
        if ($this->order_checker)
            return $this->order_checker->name;
        else
            return "";
    }

    public function getDeliveryStationNameAttribute()
    {
     if($this->delivery_station_id)
        {
            $delivery_station = DeliveryStation::find($this->delivery_station_id);
            if($delivery_station)
                return $delivery_station->name;
            else
                return "";
        }
        else
            return "";   
    }

    public function getStationNameAttribute()
    {
        if($this->station_id)
        {
            $station = DeliveryStation::find($this->station_id);
            if($station)
                return $station->name;
            else
                return "";
        }
        else
            return "";
    }

    public function getAddrIdAttribute()
    {
        $addr_id = $this->xiaoqu_id;
        return $addr_id;
    }

    /**
     * 获取配送员id
     * @return int|null
     */
    public function getMilkmanIdAttribute()
    {
        return $this->milkman->id;
    }

    /**
     * 获取配送员
     * @return
     */
    public function getMilkmanAttribute()
    {
        //
        // 抽取地址信息
        //
        $addr_to_xiaoqu = "";

        $addr_list = multiexplode(' ', $this->address);
        if (count($addr_list) >= 5)
        {
            for($i = 0; $i < 5; $i++)
            {
                $addr_to_xiaoqu .=$addr_list[$i]." ";
            }
        }
        $addr_to_xiaoqu = trim($addr_to_xiaoqu);

        if (empty($addr_to_xiaoqu)) {
            return null;
        }

        // 获取配送员
        return $this->deliveryStation->get_milkman_of_address($addr_to_xiaoqu);
    }

    /**
     * 获取配送员名称
     * @return mixed
     */
    public function getMilkmanNameAttribute()
    {
        return $this->milkman->name;
    }

    public function getAllOrderTypesAttribute()
    {
        //return "季单*1 月单*1";
        $order_products = $this->order_products;

        $ota= "";
        $season_opc = $month_opc = $half_opc = 0;

        if($order_products)
        {
            foreach($order_products as $op)
            {
                $ot = $op->order_type;

                if($ot == OrderType::ORDER_TYPE_MONTH)
                {
                    $month_opc++;

                } else if ($ot == OrderType::ORDER_TYPE_SEASON)
                {
                    $season_opc++;

                } else {

                    $half_opc++;
                }
            }

            if($half_opc>0)
            {
                $ota = OrderType::ORDER_TYPE_HALF_YEAR_NAME."*".$half_opc;
            }

            if($season_opc>0)
            {
                if($ota)
                {
                    $ota .= ", ".OrderType::ORDER_TYPE_SEASON_NAME."*".$season_opc;
                } else{
                    $ota .= OrderType::ORDER_TYPE_SEASON_NAME."*".$season_opc;
                }
            }

            if($month_opc>0)
            {
                if($ota)
                {
                    $ota .= ", ".OrderType::ORDER_TYPE_MONTH_NAME."*".$month_opc;
                } else{
                    $ota .= OrderType::ORDER_TYPE_MONTH_NAME."*".$month_opc;
                }
            }

            return $ota;

        } else
        {
            return "";
        }

    }

    /**
     * 获取录入奶站
     * @return DeliveryStation
     */
    public function station(){
        return $this->belongsTo('App\Model\DeliveryModel\DeliveryStation');
    }

    /**
     * 获取配送奶站
     * @return DeliveryStation
     */
    public function deliveryStation(){
        return $this->belongsTo('App\Model\DeliveryModel\DeliveryStation', 'delivery_station_id', 'id');
    }

    public function customer(){
        return $this->belongsTo('App\Model\BasicModel\Customer');
    }

    /**
     * 获取账单
     * @return DSTransaction
     */
    public function transaction() {
        return $this->belongsTo('App\Model\FinanceModel\DSTransaction', 'transaction_id', 'id');
    }

    /**
     * 获取奶卡
     * @return MilkCard
     */
    public function milkcard(){
        return $this->belongsTo('App\Model\FactoryModel\MilkCard', 'milk_card_id', 'number');
    }

    public function getMilkBoxInstallLabelAttribute()
    {
        if($this->milk_box_install)
            return "是";
        else
            return "不";
    }

    function get_week_delivery_info($string)
    {
        /*convert weekday string to int:int
         * data: "2016-09-28:5,2016-09-27:4,2016-09-29:1,2016-09-30:2"
         * 09-26: monday = 0
         * result: "1:4, 2:5, 3:1, 4:2"
        */
        $result = "";
        $estring = explode(',', $string);
        $ecstring = array();
        for ($i = 0; $i < count($estring); $i++) {
            $date_count = $estring[$i];
            $date_count_array = explode(':', $date_count);
            $date = trim($date_count_array[0]);

            $day = date('N', strtotime($date));

            $count = trim($date_count_array[1]);
            $ecstring[$day] = $count;
        }

        ksort($ecstring);

        foreach ($ecstring as $x => $y) {
            $result .= $x . ':' . $y . ',';
        }
        $result = rtrim($result, ',');
        return $result;
    }

    function get_month_delivery_info($string)
    {
        /*convert weekday string to int:int
         * data: "2016-09-28:5,2016-09-27:4,2016-09-13:1,2016-09-15:2,2016-09-23:3"
         * result: "13:1,15:1,23:3,27:4,28:5"
        */
        $result = "";

        $estring = explode(',', $string);
        $ecstring = array();
        for ($i = 0; $i < count($estring); $i++) {
            $date_count = $estring[$i];
            $date_count_array = explode(':', $date_count);
            $date = trim($date_count_array[0]);
            $day = explode('-', $date)[2];
            $count = trim($date_count_array[1]);
            $ecstring[$day] = $count;
        }

        ksort($ecstring);

        foreach ($ecstring as $x => $y) {
            $result .= $x . ':' . $y . ',';
        }
        $result = rtrim($result, ',');
        return $result;
    }

    /**
     * 订单是否有效状态
     */
    public function isAvailable() {
        $result = false;

        if ($this->status == Order::ORDER_ON_DELIVERY_STATUS ||
            $this->status == Order::ORDER_PASSED_STATUS ||
            $this->status == Order::ORDER_STOPPED_STATUS) {

            $result = true;
        }

        return $result;
    }

    /**
     * 新订单还没审核通过，包括待审核
     */
    public function isNewPassed() {
        $result = false;

        if ($this->status != Order::ORDER_NEW_NOT_PASSED_STATUS &&
            $this->status != Order::ORDER_NEW_WAITING_STATUS) {

            $result = true;
        }

        return $result;
    }
}
