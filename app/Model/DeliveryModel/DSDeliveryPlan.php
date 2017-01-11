<?php

namespace App\Model\DeliveryModel;

use Illuminate\Database\Eloquent\Model;
use App\Model\ProductModel\Product;
use Auth;

class DSDeliveryPlan extends Model
{
    protected $table = "dsdeliveryplan";

    public $timestamps = false;

    protected $fillable =[
        'station_id',
        'deliver_at',
        'product_id',
        'retail',
        'test_drink',
        'group_sale',
        'channel_sale',
        'remain',
        'created_at',
    ];

    protected $appends =[
        'product_name',
        'remain_final'
    ];

    public function getProductNameAttribute(){
        $product_id = $this->product_id;
        $product_name = Product::find($product_id);
        if($product_name == null)
            return null;
        else
            return $product_name->simple_name;
    }

    public function getRemainFinalAttribute(){
        return $this->remain - $this->retail - $this->test_drink - $this->group_sale - $this->channel_sale;
    }

    /**
     * 增加自营业务配送数量
     * @param $type
     * @param $count
     */
    public function increaseSelfDelivery($type, $count) {
        if ($type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_GROUP) {
            $this->group_sale += $count;
        }
        else if ($type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL) {
            $this->channel_sale += $count;
        }
        else if ($type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK) {
            $this->test_drink += $count;
        }
        else {
            $this->retail += $count;
        }

        $this->save();
    }

    /**
     * 今日该奶站该产品的配送列表是否已生成
     * @param $stationid
     * @param $productid
     * @return mixed
     */
    public static function getDeliveryPlanGenerated($stationid, $productid = 0, $deliverDate = null) {
        $date = $deliverDate;

        if (!$date) {
            $date = getCurDateString();
        }

        $deliveryPlan = DSDeliveryPlan::where('station_id', $stationid)->where('deliver_at', $date);

        if ($productid > 0) {
            $deliveryPlan->where('product_id', $productid);
        }

        return $deliveryPlan->count();
    }

}
