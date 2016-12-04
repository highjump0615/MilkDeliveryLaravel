<?php

namespace App\Model\FinanceModel;

use Illuminate\Database\Eloquent\Model;

class DSCalcBalanceHistory extends Model
{
    protected $table = 'dscalcbalancehistory';

    protected $fillable = [
        'station_id',
        'type',
        'amount',
        'receipt_number',
        'io_type',
        'comment',
    ];

    protected $appends = [
        'type_name',
        'io_name',
        'type_name_for_delivery',
    ];

    const DSCBH_TYPE_IN =1;
    const DSCBH_TYPE_OUT =2;

    //OUT
    const DSCBH_OUT_TRANSFER_MILK_FACTORY = 1; //划转奶厂奶款
    const DSCBH_OUT_SETTLEMENT_DELIVERY_COST =2; //结算配送费用
    const DSCBH_OUT_SETTLEMENT_ROBATE_ROYALTY = 3; //结算返利或提成费
    const DSCBH_OUT_OTHER_USES = 4; //其他用途划转
    const DSCBH_OUT_MILK_CARD_ORDER_TRANSFER_FACTORY = 5; //奶卡订单抵顶划转公司奶款

    //IN
    const DSCBH_IN_MONEY_STATION = 6; //本站实收现金订单款
    const DSCBH_IN_ORDER_WECHAT = 7; //收到代理商订单款
    const DSCBH_IN_ORDER_CARD = 8; //收到转入奶卡订单款
    const DSCBH_IN_ORDER_OTHER_STATION = 9; //收到其他奶站转入订单款
    const DSCBH_IN_ORDER_OUT_OTHER = 10; //转出由其他奶站配送订单款

    public function getIoNameAttribute()
    {
        if($this->io_type == $this::DSCBH_TYPE_IN)
        {
            if($this->type == $this::DSCBH_IN_ORDER_OUT_OTHER)
                return "转出";
            else
                return "收款";
        }
        else
            return "转出";
    }

    public function getTypeNameForDeliveryAttribute()
    {
        $type_name = "";
        switch ($this->type) {
            case $this::DSCBH_IN_MONEY_STATION:
                $type_name = "本站实收金额";
                break;
            case $this::DSCBH_IN_ORDER_WECHAT:
                $type_name = "代理商转账";
                break;
            case $this::DSCBH_IN_ORDER_CARD:
                $type_name = "奶卡转账";
                break;
            case $this::DSCBH_IN_ORDER_OTHER_STATION:
                $type_name = "其他奶站订单转入";
                break;
            case $this::DSCBH_IN_ORDER_OUT_OTHER:
                $type_name = "转出其他奶站订单款";
                break;
            default:
                break;
        }
        return $type_name;
    }

    public function getTypeNameAttribute()
    {
        $type_name = "";
        switch($this->type)
        {
            case $this::DSCBH_OUT_TRANSFER_MILK_FACTORY:
                $type_name = "划转奶厂奶款";
                break;
            case $this::DSCBH_OUT_SETTLEMENT_DELIVERY_COST:
                $type_name = "结算配送费用";
                break;
            case $this::DSCBH_OUT_SETTLEMENT_ROBATE_ROYALTY:
                $type_name = "结算返利或提成费";
                break;
            case $this::DSCBH_OUT_OTHER_USES:
                $type_name = "其他用途划转";
                break;
            case $this::DSCBH_OUT_MILK_CARD_ORDER_TRANSFER_FACTORY:
                $type_name = "奶卡订单抵顶划转公司奶款";
                break;
            case $this::DSCBH_IN_MONEY_STATION:
                $type_name = "本站实收现金订单款";
                break;
            case $this::DSCBH_IN_ORDER_WECHAT:
                $type_name = "收到代理商订单款";
                break;
            case $this::DSCBH_IN_ORDER_CARD:
                $type_name = "收到转入奶卡订单款";
                break;
            case $this::DSCBH_IN_ORDER_OTHER_STATION:
                $type_name = "收到其他奶站转入订单款";
                break;
            case $this::DSCBH_IN_ORDER_OUT_OTHER:
                $type_name = "转出由其他奶站配送订单款";
                break;
            default:
                break;
        }
        return $type_name;
    }

}
