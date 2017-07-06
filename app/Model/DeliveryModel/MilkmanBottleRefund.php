<?php

namespace App\Model\DeliveryModel;

use App\Model\FactoryModel\FactoryBottleType;
use Illuminate\Database\Eloquent\Model;

class MilkmanBottleRefund extends Model
{
    protected $table = "dsmilkmanbottlerefunds";

    public $timestamps = false;

    protected $fillable =[
        'milkman_id',
        'bottle_type',
        'time',
        'count',
    ];

    protected $appends = [
    ];

    /**
     * 获取奶瓶
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bottleType() {
        return $this->belongsTo('App\Model\FactoryModel\FactoryBottleType', 'bottle_type');
    }

    public function getBottleName(){
        return $this->bottleType->name;
    }

    /**
     * 获取配送员
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function milkman(){
        return $this->belongsTo('App\Model\DeliveryModel\MilkMan');
    }

//    public function bottle(){
//        return $this->belongsTo('App\Model\FactoryModel\FactoryBottleType');
//    }
    //
}
