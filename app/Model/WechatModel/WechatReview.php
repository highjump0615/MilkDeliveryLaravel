<?php

namespace App\Model\WechatModel;

use App\Model\NotificationModel\BaseNotification;
use Illuminate\Database\Eloquent\Model;

class WechatReview extends BaseNotification
{
    protected $table = 'wxreview';

    protected $fillable = [
        'customer_id',
        'content',
        'status',
        'read'
    ];
    //
}
