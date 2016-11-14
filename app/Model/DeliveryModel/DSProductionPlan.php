<?php

namespace App\Model\DeliveryModel;

use App\Model\ProductModel\Product;
use App\Model\ProductModel\ProductPrice;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class DSProductionPlan extends Model
{
    const DSPRODUCTION_SENT_PLAN = 1;
    const DSPRODUCTION_PENDING_PLAN = 2;
    const DSPRODUCTION_PRODUCE_CANCEL = 3;
    const DSPRODUCTION_PASSED_PLAN = 4;
    const DSPRODUCTION_PRODUCE_FINNISHED = 5;
    const DSPRODUCTION_PRODUCE_SENT = 6;
    const DSPRODUCTION_PRODUCE_RECEIVED = 7;

    protected $table = 'dsproductionplan';

    public $timestamps = false;

    protected $fillable = [
        'station_id',
        'product_id',
        'order_count',
        'produce_start_at',
        'produce_end_at',
        'created_at',
        'retail',
        'test_drink',
        'group_sale',
        'channel_sale',
        'subtotal_count',
        'subtotal_money',
        'actual_count',
        'conform_count',
        'status',
        'comment',
        'changed_plan_count',
    ];

    protected $appends = [
        'product_name',
        'submit_at',
        'receive_at'
    ];

    public function getProductNameAttribute(){
        $product_id = $this->product_id;
        $product_name = Product::find($product_id);
        if($product_name == null)
            return null;
        else
            return $product_name->name;
    }

    /**
     * 提交计划时间
     * @return string
     */
    public function getSubmitAtAttribute() {
        $date = str_replace('-','/', $this->produce_start_at);
        $submit_date = date('Y-m-d', strtotime($date . "-1 days"));

        return $submit_date;
    }

    /**
     * 收货时间
     * @return string
     */
    public function getReceiveAtAttribute() {
        $date = str_replace('-','/', $this->produce_end_at);
        $receive_date = date('Y-m-d', strtotime($date . "+1 days"));

        return $receive_date;
    }

    /**
     * 状态转换成文字
     * @return string
     */
    public function getStatusString() {
        $strStatus = '';

        switch ($this->status) {
            case DSProductionPlan::DSPRODUCTION_SENT_PLAN:
                $strStatus = '待生产';
                break;

            case DSProductionPlan::DSPRODUCTION_PENDING_PLAN:
                $strStatus = '待审核';
                break;

            case DSProductionPlan::DSPRODUCTION_PRODUCE_CANCEL:
                $strStatus = '生产取消';
                break;

            case DSProductionPlan::DSPRODUCTION_PASSED_PLAN:
                $strStatus = '待生产';
                break;

            case DSProductionPlan::DSPRODUCTION_PRODUCE_FINNISHED:
                $strStatus = '已生产';
                break;

            case DSProductionPlan::DSPRODUCTION_PRODUCE_SENT:
                $strStatus = '已发货';
                break;

            case DSProductionPlan::DSPRODUCTION_PRODUCE_RECEIVED:
                $strStatus = '已签收';
                break;
        }

        return $strStatus;
    }

}
