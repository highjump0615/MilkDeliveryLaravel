<?php

namespace App\Model\OrderModel;

use Illuminate\Database\Eloquent\Model;

class WechatOrder extends Model
{
    protected $table = 'wxorders';

    protected $fillable = [
        'trade_no',
        'wxuser_id',
        'factory_id',
        'group_id',
        'address_id',
        'comment',
    ];

    protected $appends = [
    ];
    
}
