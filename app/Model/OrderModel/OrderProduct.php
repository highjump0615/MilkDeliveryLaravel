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
        'start_at',
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
        'delivery_plans_sent_to_production_plan',
    ];

    public $timestamps = false;

    public function getDeliveryPlansSentToProductionPlanAttribute()
    {
        //delivery_plans_sent_to_production_plan
        $dps = MilkManDeliveryPlan::where('order_product_id', $this->id)
            ->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT)->get();
        return $dps;
    }

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
            return $product->simple_name;
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

    /**
     * 获取配送规则前缀（星期或日子）
     * @param $strCustomDate 3:5
     * @return int 3
     */
    private function getCustomDateIndex($strCustomDate) {
        $day_count_array = explode(':', $strCustomDate);
        $day = trim($day_count_array[0]);

        return (int)$day;
    }

    /**
     * 获取配送规则后缀（数量）
     * @param $strCustomDate 3:5
     * @return int 5
     */
    private function getCustomDateCount($strCustomDate) {
        $day_count_array = explode(':', $strCustomDate);
        $count = trim($day_count_array[1]);

        return (int)$count;
    }

    /**
     * 通过日期获取索引
     * @param $date
     * @return false|int|string
     */
    private function getCustomDateIndexFromDate($date) {
        $nIndex = 0;

        if ($this->delivery_type == DeliveryType::DELIVERY_TYPE_WEEK) {
            $nIndex = date('w', strtotime($date));
        }
        else {
            $aryDate = explode('-', $date);
            $nIndex = $aryDate[2];
        }

        return $nIndex;
    }

    /**
     * 要不要考虑每次数量
     * @return bool
     */
    public function isDayCountAvailable() {
        return ($this->delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY || $this->delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY);
    }

    /**
     * 获取配送规则数量
     * @param $dateDeliver
     * @return int|mixed
     */
    private function getDeliveryTypeCount($dateDeliver) {
        $nTypeCount = 0;

        // 天天送、隔日送直接返回每次数量
        if ($this->isDayCountAvailable()) {
            $nTypeCount = $this->count_per_day;
        }
        // 按周送、随心送需要查询具体规则内容
        else {
            $strCustom = rtrim($this->custom_order_dates, ',');
            $aryStrCustom = explode(',', $strCustom);

            $nIndex = $this->getCustomDateIndexFromDate($dateDeliver);

            foreach ($aryStrCustom as $strCustom) {
                if ($this->getCustomDateIndex($strCustom) == $nIndex) {
                    $nTypeCount = $this->getCustomDateCount($strCustom);
                    break;
                }
            }
        }

        return $nTypeCount;
    }

    /**
     * 计算下个月同一天的配送日期，日子超过该月的日数，就返回最后日子
     * @param $date
     * @param $dateOfMonth
     * @return Datetime
     */
    private function getNextMonthDeliverDate($date, $dateOfMonth) {
        // 获取年月日
        $aryDate = explode('-', $date);

        // 计算本月天数
        $nMaxDay = date('t', strtotime($aryDate[0] . '-' . $aryDate[1] . '-01'));

        // 计算年月日
        $nYear = (int)$aryDate[0];
        $nMonth = (int)$aryDate[1];

        // 如果当前日期是12月
        if ($nMonth + 1 > 12) {
            $nYear++;
            $nMonth = 1;
        }
        else {
            $nMonth++;
        }

        $nDay = min($dateOfMonth, $nMaxDay);

        // 返回新的日期
        return date('Y-m-d', strtotime($nYear . '-' . $nMonth . '-' . $nDay));
    }

    /**
     * 计算下一个配送规则日期
     * @param $date
     * @return $date
     */
    private function getNextDeliverDate($date) {
        // 到下个配送日的间隔
        $nIntervalDay = 0;

        // 天天送
        if ($this->delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY) {
            $nIntervalDay = 1;
        }
        // 隔日送
        else if ($this->delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY) {
            $nIntervalDay = 2;
        }
        else {
            $strCustom = rtrim($this->custom_order_dates, ',');
            $aryStrCustom = explode(',', $strCustom);

            // 规则日期需要重新排列
            $aryDate = array();
            foreach ($aryStrCustom as $strCustom) {
                array_push($aryDate, $this->getCustomDateIndex($strCustom));
            }
            sort($aryDate);

            // 当前索引
            $nIndex = $this->getCustomDateIndexFromDate($date);

            for ($i = 0; $i < count($aryDate); $i++) {
                if ($aryDate[$i] == $nIndex) {
                    if ($i == count($aryDate) - 1) {
                        // 按周送
                        if ($this->delivery_type == DeliveryType::DELIVERY_TYPE_WEEK) {
                            $nIntervalDay = 7 - $aryDate[$i] + $aryDate[0];
                        }
                        // 随心选
                        else if ($this->delivery_type == DeliveryType::DELIVERY_TYPE_MONTH) {
                            // 直接计算下个月的配送日期
                            $dateDeliverNew = $this->getNextMonthDeliverDate($date, $aryDate[0]);
                        }
                    }
                    else {
                        // 按周送
                        if ($this->delivery_type == DeliveryType::DELIVERY_TYPE_WEEK) {
                            $nIntervalDay = $aryDate[$i+1] - $aryDate[$i];
                        }
                        // 随心选
                        else if ($this->delivery_type == DeliveryType::DELIVERY_TYPE_MONTH) {
                            // 直接计算下个月的配送日期
                            $dateDeliverNew = $this->getNextMonthDeliverDate($date, $aryDate[$i+1]);
                        }
                    }

                    break;
                }
            }
        }

        if ($nIntervalDay > 0) {
            $dateDeliverNew = date('Y-m-d', strtotime($date . "+" . $nIntervalDay . " days"));
        }

        return $dateDeliverNew;
    }

    /**
     * 计算生产日期
     * @param $dateDeliver
     * @return string
     */
    private function getProductionDate($dateDeliver) {
        $nProductionPeriod = $this->product->production_period / 24;;
        $nDateRes = date('Y-m-d',strtotime($dateDeliver . "-" . $nProductionPeriod . " days"));

        return $nDateRes;
    }

    /**
     * 出现了多余量 生成或删除配送计划
     * @param $extra - 多余数量，正数或负数
     */
    public function processExtraCount($extra) {

        $nCountExtra = $extra;

        // 获取最后一条配送任务
        $lastDeliverPlan = MilkManDeliveryPlan::where('order_product_id',$this->id)
            ->orderby('deliver_at','desc')
            ->get()
            ->first();

        while ($nCountExtra > 0) {
            // 获取最后那任务的配送规则数量
            $nNormalCount = $this->getDeliveryTypeCount($lastDeliverPlan->deliver_at);

            $nIncrease = min($nNormalCount - $lastDeliverPlan->changed_plan_count, $nCountExtra);

            // 如果最后那条是单日修改过，要新添加配送任务
            if ($lastDeliverPlan->changed_plan_count != $lastDeliverPlan->plan_count) {
                $nIncrease = 0;
            }

            // 最后那条没有多余空间，要新创建一个配送任务
            if ($nIncrease == 0) {
                $deliveryPlan = $lastDeliverPlan->replicate();

                $deliveryPlan->status = $deliveryPlan->determineStatus();
                $deliveryPlan->delivered_count = 0;

                $deliveryPlan->deliver_at = $this->getNextDeliverDate($lastDeliverPlan->deliver_at);
                $deliveryPlan->produce_at = $this->getProductionDate($deliveryPlan->deliver_at);

                // 获取下一个配送规则数量
                $nNormalCount = $this->getDeliveryTypeCount($deliveryPlan->deliver_at);

                $deliveryPlan->setCount(min($nNormalCount, $nCountExtra));
                $nCountExtra -= $deliveryPlan->changed_plan_count;
            }
            else {
                $deliveryPlan = $lastDeliverPlan;

                $deliveryPlan->setCount($lastDeliverPlan->changed_plan_count + $nCountExtra);

                $nCountExtra -= $nCountExtra;
            }

            $lastDeliverPlan = $deliveryPlan;
        }
    }
}
