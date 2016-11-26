<?php

namespace App\Model\FactoryModel;

use Illuminate\Database\Eloquent\Model;

class FactoryDeliveryTime extends Model
{
    protected $table = 'mfdeliverytime';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'factory_id',
        'morning_start_at',
        'morning_end_at',
        'afternoon_start_at',
        'afternoon_end_at',
    ];
}
