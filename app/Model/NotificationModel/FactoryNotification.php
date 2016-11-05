<?php

namespace App\Model\NotificationModel;

use Illuminate\Database\Eloquent\Model;
use DateTime;
use DateTimeZone;

class FactoryNotification extends Model
{
    const READ_STATUS = 1;
    const UNREAD_STATUS = 0;
    
    protected $table = "mfnotifications";

    public $timestamps = false;

    protected $fillable = [
        'factory_id',
        'title',
        'content',
        'created_at',
        'status',
        'read',
    ];

    public function sendToFactoryNotification($factory_id,$category,$title,$content){
        $current_datetime = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $current_datetime_str = $current_datetime->format('Y-m-d H:i:s');
        if($factory_id != null && $category != null){
            $new_alert = new $this;
            $new_alert->factory_id = $factory_id;
            $new_alert->category = $category;
            $new_alert->title = $title;
            $new_alert->content = $content;
            $new_alert->created_at = $current_datetime_str;
            $new_alert->save();
        }
    }
    //
}
