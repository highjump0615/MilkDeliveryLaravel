<?php

namespace App\Model\WechatModel;

use Illuminate\Database\Eloquent\Model;

class WechatActivity extends Model
{
    protected $table = 'wxactivity';
    protected $fillable = [
        'factory_id',
        'content'
    ];

    protected $appends = [
    ];

    public $timestamps = false;
}
