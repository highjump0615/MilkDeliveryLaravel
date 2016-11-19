<?php

namespace App\Model\OrderModel;

use Illuminate\Database\Eloquent\Model;

class SelfOrder extends Model
{
    protected $table = "selforder";

    public $timestamps = false;

    protected $fillable = [
        'station_id',
        'customer_name',
        'deliver_at',
        'phone',
        'address',
        'delivery_time'
    ];
    //
}
