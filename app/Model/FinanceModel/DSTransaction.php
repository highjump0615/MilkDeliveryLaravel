<?php

namespace App\Model\FinanceModel;

use App\Model\BasicModel\PaymentType;
use Illuminate\Database\Eloquent\Model;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\FinanceModel\StationsMoneyTransfer;

class DSTransaction extends Model
{
    protected $table = 'dstransactions';

    protected $fillable = [
        'id',
        'station_id',
        'delivery_station_id',
        'order_list',
        'payment_type',
        'total_amount',
        'order_from',
        'order_to',
        'order_count',
        'transaction_pay_id',
        'status',
    ];

    protected $appends = [
        'station_name',
        'delivery_station_name',
        'pre_remain',
        'pre_remain_wechat',
        'checked_time',
    ];

    const DSTRANSACTION_CREATED =0;
    const DSTRANSACTION_COMPLETED =1;

    public function transactionPay() {
        return $this->belongsTo('App\Model\FinanceModel\DSTransactionPay', 'transaction_pay_id', 'id');
    }

    public function getCheckedTimeAttribute()
    {
        if($this->status == $this::DSTRANSACTION_COMPLETED && $this->transaction_pay_id != null)
        {
            $transaction_pay = DSTransactionPay::find($this->transaction_pay_id);
            return $transaction_pay->paid_at;
        }
    }

    public function getStationNameAttribute()
    {
        $s = DeliveryStation::find($this->station_id);
        if ($s)
            return $s->name;
        else
            return "";
    }

    public function getDeliveryStationNameAttribute()
    {
        $s = DeliveryStation::find($this->delivery_station_id);
        if ($s)
            return $s->name;
        else
            return "";
    }

    //Get Remain Amount from Previous Transactions
    public function getPreRemainAttribute()
    {
        $remain = 0;

        $money_transfer = StationsMoneyTransfer::where('station1_id', $this->station_id)
            ->where('station2_id', $this->delivery_station_id)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->orderBy('created_at', 'desc')
            ->get()
            ->first();

        if($money_transfer)
            $remain = $money_transfer->remaining;

        return $remain;
    }

    public function getPreRemainWechatAttribute()
    {
        $remain = 0;
        $money_transfer = StationsMoneyTransfer::where('station2_id', $this->delivery_station_id)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
            ->orderBy('created_at', 'desc')
            ->get()
            ->first();

        if($money_transfer)
            $remain = $money_transfer->remaining;

        return $remain;
    }
}
