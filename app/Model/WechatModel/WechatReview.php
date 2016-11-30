<?php

namespace App\Model\WechatModel;

use Illuminate\Database\Eloquent\Model;

class WechatReview extends Model
{
    const WX_REVIRE_READ_STATUS = 1;
    const WX_REVIEW_UNREAD_STATUS = 0;
    
    protected $table = 'wxreview';
    protected $fillable = [
        'customer_id',
        'content',
        'status',
        'created_at',
    ];
    //
}
