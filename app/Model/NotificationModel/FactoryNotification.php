<?php

namespace App\Model\NotificationModel;

use Illuminate\Database\Eloquent\Model;
use DateTime;
use DateTimeZone;

class FactoryNotification extends BaseNotification
{
    protected $table = "mfnotifications";

    protected $fillable = [
        'factory_id',
        'category',
        'title',
        'content',
        'status',
        'read'
    ];

    const CATEGORY_PRODUCE              = 200;

    /**
     * 获取分类列表
     * @return array
     */
    public static function getCategory() {
        $aryCategory = [
            [FactoryNotification::FIELD_CID=>FactoryNotification::CATEGORY_PRODUCE,       FactoryNotification::FIELD_CNAME=>"生产管理"]
        ];

        $aryRes = array_merge(parent::getCategory(), $aryCategory);

        return $aryRes;
    }

    /**
     * 获取分类名称
     * @param $value
     * @return string
     */
    public static function getCategoryName($value) {
        return parent::findCategoryName(FactoryNotification::getCategory(), $value);
    }
}
