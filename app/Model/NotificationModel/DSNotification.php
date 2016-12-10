<?php

namespace App\Model\NotificationModel;

use Illuminate\Database\Eloquent\Model;
use DateTime;
use DateTimeZone;

class DSNotification extends Model
{
    const READ_STATUS = 1;
    const UNREAD_STATUS = 0;

    const NOTIFICATION_PRODUCE = 1;
    
    protected $table = "dsnotifications";

    protected $fillable = [
        'station_id',
        'factory_id',
        'category',
        'title',
        'content',
        'status',
        'read',
    ];
    //
}
