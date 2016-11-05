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
        'bottle_name',
    ];

    public function getBottleNameAttribute(){
        return FactoryBottleType::find($this->bottle_type)->name;
    }

//    public function bottle(){
//        return $this->belongsTo('App\Model\FactoryModel\FactoryBottleType');
//    }
    //
}
