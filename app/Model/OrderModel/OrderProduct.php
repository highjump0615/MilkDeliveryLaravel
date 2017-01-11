<?php

namespace App\Model\OrderModel;

use App\Model\DeliveryModel\MilkManDeliveryPlan;
use Illuminate\Database\Eloquent\Model;
use App\Model\OrderModel\OrderType;
use App\Model\ProductModel\Product;
use App\Model\DeliveryModel\DeliveryType;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
    use SoftDeletes;

    protected $table = 'orderproducts';

    const ORDER_PRODUCT_ORDERTYPE_YUEDAN = 1;
    const ORDER_PRODUCT_ORDERTYPE_JIDAN = 2;
    const ORDER_PRODUCT_ORDERTYPE_BANNIANDAN = 3;

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
        'product_simple_name',
        'order_type_name',
        'delivery_type_name',
        'finished_count',
        'remain_count',
        'remain_amount',
        'last_deliver_plan',
        'finished_money_amount',
        'delivery_plans_sent_to_production_plan',
        'start_at_after_delivered'
    ];

    public function getStartAtAfterDeliveredAttribute()
    {
        //get deliverd date at last
        $last_delivered_plan = MilkManDeliveryPlan::where('order_id', $this->order_id)
            ->where('order_product_id', $this->id)
            ->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
            ->orderBy('deliver_at', 'desc')
            ->get()->first();
        if($last_delivered_plan) {
            $date = $last_delivered_plan->deliver_at;
            //get next deliver date
            $next_date = $this->getNextDeliverDate($date);
        } else {
            $next_date = $this->start_at;
        }
        return $next_date;
    }

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
        $finished_amount = 0;

        if($mdps)
        {
            foreach($mdps as $mdp)
            {
                $finished_amount += $mdp->delivered_count*$mdp->product_price;
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

    public function getProductSimpleNameAttribute()
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

        return (int)$nIndex;
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
    public function getDeliveryTypeCount($dateDeliver) {
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

        // 单日数量不能超过全部数量
        return min($nTypeCount, $this->total_count);
    }

    /**
     * 如果不是配送日期, 计算出最近的配送日期
     * @param $date
     * @return mixed
     */
    public function getClosestDeliverDate($date) {
        $dateDeliverNew = $date;

        if ($this->delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY ||
            $this->delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY) {
            return $dateDeliverNew;
        }

        // 获取年月日
        $aryDateTemp = explode('-', $date);

        // 计算本月天数
        $nMaxDay = date('t', strtotime($aryDateTemp[0] . '-' . $aryDateTemp[1] . '-01'));
        if ($this->delivery_type == DeliveryType::DELIVERY_TYPE_WEEK) {
            $nMaxDay = 7;
        }

        $aryDate = $this->getCustomDateIndexArray();

        // 到下个配送日的间隔
        $nIntervalDay = 0;

        // 当前索引
        $nIndex = $this->getCustomDateIndexFromDate($date);
        $nIntervalDay = $nMaxDay - $nIndex + $aryDate[0];

        // 获取下一个索引
        for ($i = 0; $i < count($aryDate); $i++) {
            // 超过最大范围，查看下一个索引
            if ($aryDate[$i] > $nMaxDay) {
                continue;
            }

            if ($aryDate[$i] >= $nIndex) {
                $nIntervalDay = $aryDate[$i] - $nIndex;
                break;
            }
        }

        // 算出配送日期
        $dateDeliverNew = date('Y-m-d', strtotime($date . "+" . $nIntervalDay . " days"));

        return $dateDeliverNew;
    }

    /**
     * 获取按周送、随心送索引数组
     * @return array
     */
    private function getCustomDateIndexArray() {
        $strCustom = rtrim($this->custom_order_dates, ',');
        $aryStrCustom = explode(',', $strCustom);

        // 规则日期需要重新排列
        $aryDate = array();
        foreach ($aryStrCustom as $strCustom) {
            array_push($aryDate, $this->getCustomDateIndex($strCustom));
        }
        sort($aryDate);

        return $aryDate;
    }

    /**
     * 计算下一个配送规则日期
     * @param $date
     * @return $date
     */
    public function getNextDeliverDate($date) {

        do {
            $bRestart = false;

            // 天天送
            if ($this->delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY) {
                $dateDeliverNew = date('Y-m-d', strtotime($date . "+1 days"));
            }
            // 隔日送
            else if ($this->delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY) {
                $dateDeliverNew = date('Y-m-d', strtotime($date . "+2 days"));
            }
            else {
                $dateDeliverNew = date('Y-m-d', strtotime($date . "+1 days"));
                $dateDeliverNew = $this->getClosestDeliverDate($dateDeliverNew);
            }

            // 如果算出来的日期属于暂停期间, 重新计算
            if($this->order)
            {
                if ($this->order->has_stopped) {
                    $dateStop = $this->order->stop_at;
                    $dateRestart = $this->order->order_stop_end_date;

                    if ($dateStop <= $dateDeliverNew && $dateDeliverNew <= $dateRestart) {
                        $bRestart = true;
                    }
                }
            }

            $date = $dateDeliverNew;

        } while ($bRestart);

        return $dateDeliverNew;
    }

    /**
     * 计算生产日期
     * @param $dateDeliver
     * @return string
     */
    public function getProductionDate($dateDeliver) {
        $nProductionPeriod = $this->product->production_period / 24;
        $nDateRes = date('Y-m-d',strtotime($dateDeliver . "-" . $nProductionPeriod . " days"));

        return $nDateRes;
    }

    /**
     * 出现了多余量 生成或删除配送计划
     * @param $planSrc - 导致这多余量的配送明细
     * @param $extra - 多余数量，正数或负数
     */
    public function processExtraCount($planSrc, $extra) {

        $nCountExtra = $extra;
        $lastDeliverPlan = null;

        while ($nCountExtra != 0) {

            if (!$lastDeliverPlan) {
                // 获取最后一条配送任务
                $lastDeliverPlan = MilkManDeliveryPlan::where('order_product_id', $this->id)
                    ->orderby('deliver_at', 'desc')
                    ->get()
                    ->first();
            }

            //
            // 多余量是正数，添加配送明细
            //
            if ($nCountExtra > 0) {

                // 获取最后那任务的配送规则数量
                $nNormalCount = $this->getDeliveryTypeCount($lastDeliverPlan->deliver_at);

                $nIncrease = min($nNormalCount - $lastDeliverPlan->changed_plan_count, $nCountExtra);

                // 如果最后那条是单日修改过，要新添加配送明细
                if ($lastDeliverPlan->changed_plan_count != $lastDeliverPlan->plan_count) {
                    $nIncrease = 0;
                }

                // 如果导致这次多余量的配送明细是最后的，也要新添加配送明细
                if ($planSrc) {
                    if ($lastDeliverPlan->id == $planSrc->id) {
                        $nIncrease = 0;
                    }
                }

                // 最后那条没有多余空间，要新创建一个配送任务
                if ($nIncrease == 0) {
                    $deliveryPlan = $lastDeliverPlan->replicate();

                    $deliveryPlan->determineStatus();
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

                    $deliveryPlan->setCount($lastDeliverPlan->changed_plan_count + $nIncrease);

                    $nCountExtra -= $nIncrease;
                }
            }
            //
            // 多余量是负数，删除配送明细
            //
            else {
                // 计算减少量
                $nDecrease = min($lastDeliverPlan->changed_plan_count, -$nCountExtra);
                $nCount = $lastDeliverPlan->changed_plan_count - $nDecrease;

                if ($nCount > 0) {
                    $lastDeliverPlan->setCount($nCount);
                    $deliveryPlan = $lastDeliverPlan;
                }
                else {
                    $lastDeliverPlan->forceDelete();
                    $deliveryPlan = null;
                }

                $nCountExtra += $nDecrease;
            }

            $lastDeliverPlan = $deliveryPlan;
        }
    }
}
