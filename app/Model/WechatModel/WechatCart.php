<?php

namespace App\Model\WechatModel;

use Illuminate\Database\Eloquent\Model;



class WechatCart extends Model
{
    protected $table = 'wxcarts';
    protected $fillable = [
        'wxuser_id',
        'wxorder_product_id',
    ];

    protected $appends = [
    ];


    public function order_item(){
        return $this->belongsTo('App\Model\WechatModel\WechatOrderProduct', 'wxorder_product_id');
    }

}
