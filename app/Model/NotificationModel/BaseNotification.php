<?php

namespace App\Model\NotificationModel;

use Illuminate\Database\Eloquent\Model;

class BaseNotification extends Model
{
    const READ_STATUS = 1;
    const UNREAD_STATUS = 0;

    public function setRead($read) {
        if ($read) {
            $this->read = BaseNotification::READ_STATUS;
        }
        else {
            $this->read = BaseNotification::UNREAD_STATUS;
        }

        $this->save();
    }

}
