<?php

namespace App\Model\FinanceModel;

use Illuminate\Database\Eloquent\Model;
use App\Model\DeliveryModel\DeliveryStation;

class DSTransactionPay extends Model
{
    protected $table = 'dstransactionpay';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'receipt_number',
        'amount',
        'paid_at',
        'comment',
    ];

    protected $appends = [
        'delivery_station_name',
        'payer',
        'delivery_station_id',
    ];

    public function getDeliveryStationNameAttribute()
    {
        $trs = DSTransaction::where('transaction_pay_id', $this->id)->get()->first();
        if($trs)
            return $trs->delivery_station_name;
    }

    public function getDeliveryStationIDAttribute()
    {
        $trs = DSTransaction::where('transaction_pay_id', $this->id)->get()->first();
        if($trs)
            return $trs->delivery_station_id;
    }

    public function getPayerAttribute()
    {
        $trs = DSTransaction::where('transaction_pay_id', $this->id)->get()->first();
        $station_id =$trs->station_id;
        $station = DeliveryStation::find($station_id);
        return $station->boss;
    }

}
