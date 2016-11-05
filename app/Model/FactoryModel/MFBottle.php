<?php

namespace App\Model\FactoryModel;

use Illuminate\Database\Eloquent\Model;

class MFBottle extends Model
{
    protected $table = "mfbottles";

    public $timestamps = false;

    protected $fillable =[
        'bottle_type',
        'time',
        'init_store_count',
        'station_refunds_count',
        'etc_refunds_count',
        'production_count',
        'store_damage_count',
        'final_count',
    ];
    //
}
