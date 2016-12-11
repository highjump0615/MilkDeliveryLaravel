<?php

namespace App\Model\FinanceModel;

use Illuminate\Database\Eloquent\Model;

class DSBusinessCreditBalanceHistory extends Model
{
    protected $table = 'dsbusinesscreditbalancehistory';

    protected $fillable = [
        'id',
        'station_id',
        'type',
        'io_type',
        'amount',
        'receipt_number',
        'time',
        'return_amount',
        'comment',
    ];

    protected $appends = [
        'type_name',
        'io_name',
    ];

    const DSBCBH_IN = 1; //收款
    const DSBCBH_OUT = 2; //扣款

    const DSBCBH_OUT_STATION_RETAIL_BUSINESS = 1; //站内零售业务
    const DSBCBH_OUT_GROUP_BUY_BUSINESS = 2; //团购业务\
    const DSBCBH_OUT_CHANNEL_SALES_OPERATIONS = 3; //渠道销售业务
    const DSBCBH_OUT_TRY_TO_DRINK_OR_GIFT = 4; //试饮或赠品
    const DSBCBH_OUT_RETURN = 5; // 自营账户调整

    

    public function getIoNameAttribute()
    {
        if ($this->io_type == $this::DSBCBH_OUT)
            return "扣款";
        else
            return "收款";
    }

    public function getTypeNameAttribute()
    {
        $type_name = "";

        switch ($this->type) {
            case $this::DSBCBH_OUT_STATION_RETAIL_BUSINESS:
                $type_name = "站内零售业务";
                break;

            case $this::DSBCBH_OUT_GROUP_BUY_BUSINESS:
                $type_name = "团购业务";
                break;

            case $this::DSBCBH_OUT_CHANNEL_SALES_OPERATIONS:
                $type_name = "渠道销售业务";
                break;

            case $this::DSBCBH_OUT_TRY_TO_DRINK_OR_GIFT:
                $type_name = "试饮或赠品";
                break;

            case $this::DSBCBH_OUT_RETURN:
                $type_name = "自营账户调整";
                break;

            default:
                break;
        }

        return $type_name;
    }
}
