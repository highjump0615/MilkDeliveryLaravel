<?php

namespace App\Model\NotificationModel;

use Illuminate\Database\Eloquent\Model;

class BaseNotification extends Model
{
    const READ_STATUS = 1;
    const UNREAD_STATUS = 0;

    const CATEGORY_CHANGE_ORDER         = 1;
    const CATEGORY_ACCOUNT              = 100;
    const CATEGORY_TRANSACTION          = 300;

    const FIELD_CID         = "id";
    const FIELD_CNAME       = "name";

    /**
     * 设置阅读状态
     * @param $read
     */
    public function setRead($read) {
        if ($read) {
            $this->read = BaseNotification::READ_STATUS;
        }
        else {
            $this->read = BaseNotification::UNREAD_STATUS;
        }

        $this->save();
    }

    /**
     * 获取分类列表
     * @return array
     */
    public static function getCategory() {
        return [
            [BaseNotification::FIELD_CID=>BaseNotification::CATEGORY_CHANGE_ORDER, BaseNotification::FIELD_CNAME=>"订单修改"],
            [BaseNotification::FIELD_CID=>BaseNotification::CATEGORY_ACCOUNT,      BaseNotification::FIELD_CNAME=>"账户通知"],
            [BaseNotification::FIELD_CID=>BaseNotification::CATEGORY_TRANSACTION,  BaseNotification::FIELD_CNAME=>"财务通知"]
        ];
    }

    /**
     * 查找分类名称
     * @param $aryCategory
     * @param $value
     * @return string
     */
    protected static function findCategoryName($aryCategory, $value) {
        $strName = "";

        foreach ($aryCategory as $cg) {
            if ($cg[BaseNotification::FIELD_CID] == $value) {
                $strName = $cg[BaseNotification::FIELD_CNAME];
            }
        }

        return $strName;
    }

    /**
     * 获取分类名称
     * @param $value
     * @return string
     */
    public static function getCategoryName($value) {
        return BaseNotification::findCategoryName(BaseNotification::getCategory(), $value);
    }
}
