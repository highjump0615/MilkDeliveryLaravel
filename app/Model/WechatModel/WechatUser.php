<?php

namespace App\Model\WechatModel;

use Illuminate\Database\Eloquent\Model;
use App\Model\WechatModel\WechatOrderProduct;

class WechatUser extends Model
{
    protected $table = 'wxusers';
    protected $fillable = [
        'name',
        'openid',
        'customer_id',
        'device_token',
        'last_session',
        'last_used_ip',
        'image_url',
    ];
    
    public $timestamps = false;

    protected $appends = [
        'is_loggedin',
    ];

    public function getIsLoggedinAttribute()
    {
        if($this->customer_id)
            return true;
        else
            return false;
    }

    public function order_start_at($group_id)
    {
        $wops = WechatOrderProduct::where('wxuser_id', $this->id)->where('group_id', $group_id)->get()->all();

        $first = true;

        $start_at = "";

        foreach($wops as $wop)
        {
            if($first)
            {
                $start_at = $wop->start_at;
                $first = false;
            }else {
                if(strtotime($start_at) > strtotime($wop->start_at))
                {
                    $start_at = $wop->start_at;
                }
            }
        }

        return $start_at;
    }
}
