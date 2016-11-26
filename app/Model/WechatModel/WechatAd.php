<?php

namespace App\Model\WechatModel;

use Illuminate\Database\Eloquent\Model;

class WechatAd extends Model
{
    protected $table = 'wechatads';
    protected $fillable = [
        'factory_id',
        'image_url',
        'product_id',
        'type',
        'image_no',
    ];

    protected $appends = [
        
    ];

    public $timestamps = false;

    const WECHAT_AD_TYPE_BANNER = 1;
    const WECHAT_AD_TYPE_PROMOTION = 2;

}
