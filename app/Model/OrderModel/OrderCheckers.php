<?php

namespace App\Model\OrderModel;

use App\Model\DeliveryModel\DeliveryStation;
use App\Model\FactoryModel\Factory;
use Illuminate\Database\Eloquent\Model;

class OrderCheckers extends Model
{
    protected $table = 'ordercheckers';

    protected $fillable = [
        'name',
        'number',
        'phone',
        'station_id',
        'or_factory_id',
        'is_active',
    ];

    public $appends = [
        'danwei_name',
    ];

    public $timestamps = false;

    const NUMBER_PREFIX = 'ZDY';
    const NUMBER_NUMBERS = 4;

    public function factory()
    {
    	return $this->belongsTo('App\Model\FactoryModel\Factory', 'or_factory_id');
    }

    public function station(){
        return $this->belongsTo('App\Model\DeliveryModel\DeliveryStation');
    }

    public function getDanweiNameAttribute(){
        if($this->or_factory_id) {
            return "奶厂";
        } else {
            $s = DeliveryStation::find($this->station_id);
            if ($s) {
                return $s->name;
            } else {
                return "";
            }
        }
    }
}
