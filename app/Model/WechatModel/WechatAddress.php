<?php

namespace App\Model\WechatModel;

use Illuminate\Database\Eloquent\Model;

class WechatAddress extends Model
{
    protected $table = 'wxaddress';
    protected $fillable = [
        'wxuser_id',
        'name',
        'phone',
        'address',
        'sub_address',
        'primary',
    ];

    protected $appends = [

    ];

    public $timestamps = false;
}
