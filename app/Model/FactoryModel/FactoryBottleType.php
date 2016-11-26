<?php

namespace App\Model\FactoryModel;

use Illuminate\Database\Eloquent\Model;

class FactoryBottleType extends Model
{
    protected $table= 'mfbottletype';

    protected $fillable = [
        'name',
        'factory_id',
        'number',
        'is_deleted',
    ];

    public $timestamps = false;

    const FACTORY_BOTTLE_TYPE_PREFIX = "BOTTLE";

//    public function milkmanrefund(){
//        return $this->hasMany('App\Model\DeliveryModel\MilkmanBottleRefund');
//    }

}
