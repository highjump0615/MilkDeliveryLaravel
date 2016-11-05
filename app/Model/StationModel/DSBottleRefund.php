<?php

namespace App\Model\StationModel;

use App\Model\FactoryModel\FactoryBottleType;
use Illuminate\Database\Eloquent\Model;

class DSBottleRefund extends Model
{
    protected $table = "dsbottlerefunds";

    public $timestamps = false;

    protected $fillable =[
        'station_id',
        'bottle_type',
        'time',
        'init_store',
        'return_to_factory',
        'received',
        'station_damaged',
        'end_store',
    ];

    protected $appends = [
        'bottle_name',
    ];
    public function getBottleNameAttribute(){
        return FactoryBottleType::find($this->bottle_type)->name;
    }
    //
}
