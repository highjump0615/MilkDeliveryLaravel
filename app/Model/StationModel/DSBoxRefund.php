<?php

namespace App\Model\StationModel;

use Illuminate\Database\Eloquent\Model;

class DSBoxRefund extends Model
{
    protected $table = "dsboxrefunds";

    public $timestamps = false;

    protected $fillable =[
        'station_id',
        'box_type',
        'time',
        'init_store',
        'milkman_return',
        'return_to_factory',
        'received',
        'station_damaged',
        'end_store',
    ];
    //
}
