<?php

namespace App\Model\NotificationModel;

use Illuminate\Database\Eloquent\Model;
use DateTime;
use DateTimeZone;

class DSNotification extends BaseNotification
{
    const NOTIFICATION_PRODUCE = 1;
    
    protected $table = "dsnotifications";

    protected $fillable = [
        'station_id',
        'category',
        'title',
        'content',
        'status',
        'read'
    ];
}
