<?php

namespace App\Model\WechatModel;

use Illuminate\Database\Eloquent\Model;

class WechatMenu extends Model
{
    protected $table = 'wechatmenu';
    
    protected $fillable = [
        'factory_id',
        'menu_no',
        'submenu_no',
        'type',
        'name',
        'page',
    ];

    protected $appends = [

    ];


}
