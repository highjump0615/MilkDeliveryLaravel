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

    public $timestamps = false;

    protected $fillable = [
        'station_id',
        'factory_id',
        'category',
        'title',
        'content',
        'created_at',
        'status',
        'read',
    ];

    public function sendToStationNotification($station_id,$category,$title,$content){
        $current_datetime = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $current_datetime_str = $current_datetime->format('Y-m-d H:i:s');
        if($station_id != null && $category != null){
            $new_alert = new $this;
            $new_alert->station_id = $station_id;
            $new_alert->category = $category;
            $new_alert->title = $title;
            $new_alert->content = $content;
            $new_alert->created_at = $current_datetime_str;
            $new_alert->save();
        }
    }
    //
}
