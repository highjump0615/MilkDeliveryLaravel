<?php

namespace App\Model\FinanceModel;

use Illuminate\Database\Eloquent\Model;

class DSDeliveryCreditBalanceHistory extends Model
{
    protected $table = 'dsdeliverycreditbalancehistory';
    public $timestamps = false;

    protected $fillable = [
        'station_id',
        'type',
        'amount',
        'time',
        'order_id',
        'receipt_number',
        'io_type',
        'comment',
    ];

    protected $appends = [
        'type_name',
        'io_name'
    ];

    const DSDCBH_IO_TYPE_IN = 1;

    const DSDCBH_TYPE_IN_MONEY = 1;//本站实收金额
    const DSDCBH_TYPE_IN_WECHAT = 2;//代理商转账
    const DSDCBH_TYPE_IN_CARD = 3;//奶卡转账
    const DSDCBH_TYPE_IN_FROM_OTHER_STATION = 4;//其他奶站订单转入

    const DSDCBH_TYPE_OUT_OTHER_STATION = 5;//转出其他奶站订单款


    public function getIoNameAttribute()
    {
        if($this->type <= $this::DSDCBH_TYPE_IN_FROM_OTHER_STATION)
            return "收款";
        else
            return "转出";
    }

    public function getTypeNameAttribute()
    {
        $type_name = "";
        switch ($this->type) {
            case $this::DSDCBH_TYPE_IN_MONEY:
                $type_name = "本站实收金额";
                break;
            case $this::DSDCBH_TYPE_IN_WECHAT:
                $type_name = "代理商转账";
                break;
            case $this::DSDCBH_TYPE_IN_CARD:
                $type_name = "奶卡转账";
                break;
            case $this::DSDCBH_TYPE_IN_FROM_OTHER_STATION:
                $type_name = "其他奶站订单转入";
                break;
            case $this::DSDCBH_TYPE_OUT_OTHER_STATION:
                $type_name = "转出其他奶站订单款";
                break;
            default:
                break;
        }
        return $type_name;
    }
}
