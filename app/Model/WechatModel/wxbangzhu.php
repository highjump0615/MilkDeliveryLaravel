<?php

namespace App\Model\WechatModel;

use Illuminate\Database\Eloquent\Model;

class wxbangzhu extends Model
{
    protected $table = 'wxbangzhu';
    protected $fillable = [
        'factory_id',
        'content'
    ];

    protected $appends = [
    ];

    public $timestamps = false;
}
