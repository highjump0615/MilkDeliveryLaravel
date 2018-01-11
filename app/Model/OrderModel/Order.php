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
use League\Flysystem\Exception;

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
        'station_name',
        'delivery_station_name',
        'milkman_name',
        'milkman_id',
        'milkman',
        'addresses',
        'milk_box_install_label',
        'province_id',
        'street_id',
        'xiaoqu_id',
        'all_order_types',
//        'delivery_plans',
        'order_end_date',
        'status_name',
        'has_stopped',
        'remain_order_money',
        'sub_address',
        'main_address',
        'total_count',
        'order_stop_end_date',
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

    // 序号，只在导入时使用
    public $mnSeq;

    /**
     * 解析订单收货地址，以空格分隔的
     */
    public function resolveAddress() {
        $aryAddr = explode(' ', $this->address, 6);

        $this->mStrProvince = $aryAddr[0];
        $this->mStrCity = $aryAddr[1];
        $this->mStrDistrict = $aryAddr[2];
        $this->mStrStreet = null;
        $this->mStrVillage = null;

        // 是否没选好
        if (strcmp($aryAddr[3], "其他")) {
            $this->mStrStreet = $aryAddr[3];
            $this->mStrVillage = $aryAddr[4];
            $this->mStrHouseNumber = !empty($aryAddr[5]) ? $aryAddr[5] : "";
        }
        else {
            // 编制其他地址
            $nPreLen = 0;
            for ($i = 0; $i < 4; $i++) {
                $nPreLen += strlen($aryAddr[$i]) + 1;
            }
            $this->mStrHouseNumber = substr($this->address, $nPreLen - 1);
        }
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

    public function getTotalCountAttribute()
    {
        return getEmptyValue($this->order_products()->sum('total_count'));
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

    /**
     * 获取小区和具体地址
     * @return string
     */
    public function getAddressSmall($level) {
        $main_addr = "";
        $addr_list = explode(" ", $this->address);

        for ($i = $level - 1; $i < count($addr_list); $i++) {
            if ($i >= $level) {
                $main_addr .= " ";
            }

            if (!empty($addr_list[$i])) {
                $main_addr .= $addr_list[$i];
            }
        }

        return $main_addr;
    }

    /**
     * 计算订单余额
     * @return mixed
     */
    public function getRemainOrderMoneyAttribute()
    {
        $dFinished = 0;

        $plans = MilkManDeliveryPlan::where('order_id', $this->id)
            ->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
            ->selectRaw('sum(delivered_count * product_price) as cost')
            ->first();

        if (!empty($plans->cost)) {
            $dFinished = $plans->cost;
        }

        $remain = $this->total_amount - $dFinished;

        return $remain;
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
        $dateRestart = date($this->restart_at);

        if ($dateCurrent >= $dateRestart) {
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

    /**
     * 订单配送结束日期
     * @return string
     */
    public function getOrderEndDateAttribute()
    {
        $dp = $this->milkmanDeliveryPlan()->orderBy('deliver_at', 'desc')->first();
        return ($dp) ? $dp->deliver_at : "";
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

    public function getUnfinishedDeliveryPlanQuery()
    {
        $dps = MilkManDeliveryPlan::where('order_id', $this->id)
            ->wherebetween('status', [MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING, MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT]);

        return $dps;
    }

    public function getGroupedDeliveryPlans()
    {
        //order_id, station_id
        $dps = MilkManDeliveryPlan::where('order_id', $this->id)
            ->where('status', '!=', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL)
            ->orderBy('deliver_at')
            ->get();

        return $dps;
    }

    public function getProvinceIdAttribute()
    {
//       $sa =explode(' ', $this->address);
        $sa = explode(' ', $this->address);
        $province = $sa[0];
        if(!$province)
            return 0;
        $province_m = Address::where('name', $province)->first();
        if($province_m)
            return $province_m->id;
        else
            return 0;
    }

    public function getCityId()
    {
        $province_id = $this->province_id;

        $sa = explode(' ', $this->address);
        $city = $sa[1];

        if(!$city)
            return 0;

        $city_m = Address::where('name', $city)->where('parent_id', $province_id)->first();

        if($city_m)
            return $city_m->id;
        else
            return 0;
    }

    public function getCityName()
    {
        $city_id = $this->getCityId();
        $city = Address::find($city_id);
        if($city)
            return $city->name;
        else
            return "";
    }

    public function getDistrictId()
    {
        $city_id = $this->getCityId();

        $sa = multiexplode(' ', $this->address);
        $district = $sa[2];
        if(!$district)
            return 0;

        $district_m = Address::where('name', $district)->where('parent_id', $city_id)->first();
        if($district_m)
            return $district_m->id;
        else
            return 0;
    }

    public function getDistrictName()
    {
        $district_id =$this->getDistrictId();
        $district = Address::find($district_id);
        if($district)
            return $district->name;
        else
            return "";
    }

    /**
     * 获取配送地址街道id
     * @return int
     */
    public function getStreetIdAttribute()
    {
        $nId = 0;

        if (!empty($this->deliveryArea)) {
            $nId = $this->deliveryArea->village->parent->id;
        }

        return $nId;
    }

    /**
     * 获取配送地址小区id
     * @return int
     */
    public function getXiaoquIdAttribute()
    {
        $nId = 0;

        if (!empty($this->deliveryArea)) {
            $nId = $this->deliveryArea->village->id;
        }

        return $nId;
    }

    /**
     * 获取订单地址
     * @return mixed|string
     */
    public function getAddressesAttribute()
    {
        return $this->getAddressSmall(Address::LEVEL_STREET);
    }

    /**
     * 获取收件人名称
     * @return mixed
     */
    public function getCustomerNameAttribute()
    {
        return $this->customer ? $this->customer->name : "";
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

    /**
     * 获取订单奶品信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function order_products()
    {
        return $this->hasMany('App\Model\OrderModel\OrderProduct');
    }

    public function order_products_all()
    {
        return $this->hasMany('App\Model\OrderModel\OrderProduct')->withTrashed()->orderby('id', 'desc');
    }

    /**
     * 获取配送明细
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function milkmanDeliveryPlan() {
        return $this->hasMany('App\Model\DeliveryModel\MilkManDeliveryPlan', 'order_id');
    }

    /**
     * 获取订单性质名称
     * @return mixed
     */
    public function getOrderPropertyName()
    {
        return $this->property->name;
    }

    /**
     * 获取征订员信息
     * @return OrderChecker
     */
    public function checker()
    {
        return $this->belongsTo('App\Model\OrderModel\OrderCheckers', 'order_checker_id');
    }

    /**
     * 获取DSDeliveryArea
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryArea() {
        return $this->belongsTo('App\Model\DeliveryModel\DSDeliveryArea', 'deliveryarea_id');
    }

    /**
     * 获取征订员名称
     * @return string
     */
    public function getCheckerName()
    {
        return $this->checker->name;
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

    /**
     * 获取配送员id
     * @return int|null
     */
    public function getMilkmanIdAttribute()
    {
        $milkman = $this->milkman;
        $nId = 0;

        if (!empty($milkman)) {
            $nId = $milkman->id;
        }

        return $nId;
    }

    /**
     * 获取配送员
     * @return
     */
    public function getMilkmanAttribute()
    {
        $milkman = null;

        $milkmanArea = MilkManDeliveryArea::where('deliveryarea_id', $this->deliveryarea_id)
            ->first();

        if (!empty($milkmanArea)) {
            $milkman = $milkmanArea->milkman;
        }

        return $milkman;
    }

    /**
     * 获取配送员名称
     * @return mixed
     */
    public function getMilkmanNameAttribute()
    {
        $strName = null;
        $milkman = $this->milkman;

        if (!empty($milkman)) {
            $strName = $milkman->name;
        }

        return $strName;
    }

    public function getAllOrderTypesAttribute()
    {
        $typeCounts = $this->order_products()
            ->groupBy('order_type')
            ->selectRaw('order_type, count(*) as count')
            ->get();

        $ota= "";

        foreach ($typeCounts as $typeCount) {
            if (!empty($ota)) {
                $ota .= ", ";
            }

            // 添加类型和数量
            $ota .= $typeCount->order_type_name . "*" . $typeCount->count;
        }

        return $ota;
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

    /**
     * 获取收件人
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(){
        return $this->belongsTo('App\Model\BasicModel\Customer');
    }

    /**
     * 获取订单性质
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property() {
        return $this->belongsTo('App\Model\OrderModel\OrderProperty', 'order_property_id');
    }

    /**
     * 获取账单
     * @return DSTransaction
     */
    public function transaction() {
        return $this->belongsTo('App\Model\FinanceModel\DSTransaction', 'transaction_id', 'id');
    }

    /**
     * 获取奶卡总金额
     * @return
     */
    public function getMilkcardValue() {
        $nValue = $this->hasMany('App\Model\FactoryModel\MilkCard', 'order_id')->sum('balance');
        return $nValue;
    }

    public function getMilkBoxInstallLabelAttribute()
    {
        if($this->milk_box_install)
            return "是";
        else
            return "不";
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

    /**
     * 设置订单编号
     * @param $needSave boolean
     */
    public function setOrderNumber($needSave = true)
    {
        $this->number = 'F' . $this->factory_id . 'S' . $this->station_id . 'C' . $this->customer_id . 'O' . $this->id;

        if ($needSave) {
            $this->save();
        }
    }

    /**
     * 获取配送员对该地址的配送顺序
     * @return int
     */
    public function getDeliverAddressOrder() {
        $nOrder = 9999;

        if (!empty($this->deliveryArea)) {
            if (!empty($this->deliveryArea->milkmanDeliveryArea)) {
                $nOrder = $this->deliveryArea->milkmanDeliveryArea->order;
            }
        }

        return $nOrder;
    }

    /**
     * 获取配送时间描述
     * @return string
     */
    public function getDeliveryTimeDesc() {
        if ($this->delivery_time == Order::ORDER_DELIVERY_TIME_MORNING) {
            return '上午';
        }

        return '下午';
    }

    /**
     * 获取暂停订单范围起始日期
     * @return mixed|string
     */
    public function getPauseStartAvailableDate() {
        $strDate = getCurDateString();

        if (!empty($this->deliveryStation)) {
            $strDate = max($strDate, $this->deliveryStation->getChangeStartDate());
        }

        $strDate = max($strDate, $this->getStartAtDate());

        return $strDate;
    }

    /**
     * 获取订单起送日期
     * @return mixed
     */
    public function getStartAtDate() {
        return $this->order_products->min('start_at');
    }
}
