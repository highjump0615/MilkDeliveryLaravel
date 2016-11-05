<?php

namespace App\Model\FactoryModel;

use Illuminate\Database\Eloquent\Model;

class MFBox extends Model
{
    protected $table = "mfboxes";

    public $timestamps = false;

    protected $fillable =[
        'box_type',
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
