<?php

namespace App\Model\WechatModel;

use Illuminate\Database\Eloquent\Model;

class Wxmenu extends Model
{
    protected $table = 'wxmenu';
    protected $fillable = [
        'wxmid',
        'factoryid',
        'mainindex',
        'displayorder',
        'type',
        'label',
        'name',
        'url',
        'keyword',
        'app',
    ];

    protected $appends = [
        
    ];

    public $timestamps = false;

    const WECHAT_AD_TYPE_BANNER = 1;
    const WECHAT_AD_TYPE_PROMOTION = 2;

}
