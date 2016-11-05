<?php

namespace App\Model\NotificationModel;

use Illuminate\Database\Eloquent\Model;

class NotificationCategory extends Model
{
    const TYPE_FACTORY = 0;
    const TYPE_MILK_STATION = 1;

    protected $table = "notificationcategory";

    public $timestamps = false;

    protected $fillable = [
        'category_name',
    ];
    //
}
