<?php

namespace App\Model\FinanceModel;

use Illuminate\Database\Eloquent\Model;
use App\Model\DeliveryModel\DeliveryStation;

class StationsMoneyTransfer extends Model
{
    protected $table = 'stationsmoneytransfer';

    protected $fillable = [
        'id',
        'station1_id',
        'station2_id',
        'transaction_pay_id',
        'amount',
        'remaining',
        'payment_type',
    ];

    protected $appends = [
        'station_name',
        'delivery_station_name',
    ];


    public function getStationNameAttribute()
    {
        $s = DeliveryStation::find($this->station1_id);
        if ($s)
            return $s->name;
        else
            return "";
    }

    public function getDeliveryStationNameAttribute()
    {
        $s = DeliveryStation::find($this->station2_id);
        if ($s)
            return $s->name;
        else
            return "";
    }
}
