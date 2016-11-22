<?php

namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;
use App\Model\OrderModel\OrderProduct;
use App\Model\OrderModel\Order;
use DateTime;
use DateTimeZone;

class MilkManDeliveryPlan extends Model
{
    public $timestamps = false;

    protected $table = "milkmandeliveryplan";

    protected $fillable =[
        'order_id',
        'station_id',
        'customer_id',
        'order_product_id',
        'time',
        'status',
        'product_price',
        'produce_at',
        'deliver_at',
        'plan_count',
        'changed_plan_count',
        'delivery_count',
        'delivered_count',
        'comment',
        'type',
        'flag',
        'milkman_id',
        'cancel_reason',
    ];

    protected $appends = [
        'plan_price',
        'station_id',
        'product_name',
        'status_name',
        'plan_product_image',
    ];

    const MILKMAN_DELIVERY_PLAN_TYPE_USER = 1;
    const MILKMAN_DELIVERY_PLAN_TYPE_GROUP = 2;
    const MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL = 3;
    const MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK = 4;
    const MILKMAN_DELIVERY_PLAN_TYPE_RETAIL = 6;
    const MILKMAN_DELIVERY_PLAN_TYPE_MILKBOXINSTALL = 5;

    const MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER = 1;
    const MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER_RULE_CHANGE = 2;

    const MILKMAN_DELIVERY_PLAN_STATUS_CANCEL = 0;
    const MILKMAN_DELIVERY_PLAN_STATUS_WAITING = 1;
    const MILKMAN_DELIVERY_PLAN_STATUS_PASSED = 2;
    const MILKMAN_DELIVERY_PLAN_STATUS_SENT = 3;
    const MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED = 4;


    public function getPlanProductImageAttribute()
    {
        $order_product = OrderProduct::find($this->order_product_id);
        if($order_product)
        {
            $product = $order_product->product;
            return $product->photo_url1;
        }
    }

    public function getStatusNameAttribute(){
        if($this->status == $this::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
            return "已配送";
        elseif($this->status == $this::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL)
            return $this->cancel_reason;
        else
            return "未配送";
    }

    public function getProductNameAttribute()
    {
        $op = OrderProduct::find($this->order_product_id);
        if($op)
            return $op->product_name;
        else
            return "";
    }

    public function getPlanPriceAttribute()
    {
        $plan_price = ($this->product_price) * ($this->changed_plan_count);
        return $plan_price;
    }

    public function getStationIdAttribute(){
        $station = Order::find($this->order_id);
        if($station == null){
            return null;
        }
        else{
            return $station->station_id;
        }
    }
    //

    public function order(){
        if($this->type == 1)
            return $this->belongsTo('App\Model\OrderModel\Order');
        else
            return $this->belongsTo('App\Model\OrderModel\SelfOrder');
    }

    public function order_product(){
        if($this->type == 1)
            return $this->belongsTo('App\Model\OrderModel\OrderProduct');
        else
            return $this->belongsTo('App\Model\OrderModel\SelfOrderProduct');
    }

    public function milkman(){
        return $this->belongsTo('App\Model\DeliveryModel\MilkMan');
    }

    public function getTypeDesc() {
        $strRes = '';

        if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER) {
            $strRes = '计划订单';
        }
        else if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_GROUP) {
            $strRes = '团购业务';
        }
        else if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK) {
            $strRes = '试饮赠品';
        }
        else if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL) {
            $strRes = '渠道业务';
        }
        else if ($this->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_RETAIL) {
            $strRes = '店内零售';
        }

        return $strRes;
    }

    /**
     * 是否奶箱安装
     * @return bool
     */
    public function isBoxInstall() {
        return ($this->flag && $this->order->milk_box_install);
    }

    /**
     * 设置配送数量
     * @param $value
     */
    public function setCount($value) {

        $this->changed_plan_count = $value;
        $this->delivery_count = $value;

        // 已提交生产计划才算是修改
        if ($this->status < MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT) {
            $this->plan_count = $value;
        }

        $this->save();
    }

    /**
     * 根据生产日期，决定配送明细的状态
     */
    public function determineStatus() {
        $nProductId = 0;
        $dateCurrent = new DateTime("now",new DateTimeZone('Asia/Shanghai'));

        // 这个明细已生成，直接用奶品id
        if ($this->order_product) {
            $nProductId = $this->order_product->product->id;
        }
        else {
            // 这个明细未生成，查询奶品id
            $product = OrderProduct::find($this->order_product_id);
            if ($product) {
                $nProductId = $product->product->id;
            }
        }

        // 计算提交日期
        $strDateProduce = str_replace('-','/', $this->produce_at);
        $dateSubmit = date('Y-m-d',strtotime($strDateProduce."-1 days"));
        $datetimeSubmit = DateTime::createFromFormat('Y-m-j', $dateSubmit);

        // 提交日期已过
        if ($dateCurrent > $datetimeSubmit) {
            $this->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT;
        }
        else {
            // 如果今天是提交日期，是查看是否已提交
            $count = DSProductionPlan::where('station_id', $this->station_id)
                ->where('produce_start_at', $this->produce_at)
                ->where('product_id', $nProductId)
                ->count();

            // 已提交，状态就设成已提交
            if ($count) {
                $this->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT;
            }
        }
    }

    /**
     * 把自己的flag转给下个配送明细
     */
    public function transferFlag() {
        // 获取下一个配送明细
        $deliverPlanNext = MilkManDeliveryPlan::where('order_product_id', $this->order_product_id)
            ->where('deliver_at', '>', $this->deliver_at)
            ->get()
            ->first();

        // 切换两个配送明细的flag
        $temp = $this->flag;
        $this->flag = $deliverPlanNext->flag;
        $deliverPlanNext->flag = $temp;

        $deliverPlanNext->save();
        $this->save();
    }
}
