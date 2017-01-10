<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Model\UserModel\User;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 获取登陆的奶站id
     * @return mixed
     */
    protected function getCurrentStationId() {
        $nId = Auth::guard('naizhan')->user()->station_id;
        return $nId;
    }

    /**
     * 获取登陆的奶厂id
     * @return mixed
     */
    protected function getCurrentFactoryId($factoryUser) {

        if ($factoryUser) {
            $nId = Auth::guard('gongchang')->user()->factory_id;
        }
        else {
            $nId = Auth::guard('naizhan')->user()->factory_id;
        }

        return $nId;
    }

    /**
     * 获取奶站和奶厂id
     * For API
     * @param $fid
     * @param $sid
     */
    public function getFactoryStationId(&$fid, &$sid) {
        $fuser = Auth::guard('gongchang')->user();
        if($fuser)
        {
            $fid = $fuser->factory_id;
        }

        $suser = Auth::guard('naizhan')->user();
        if($suser)
        {
            $sid = $suser->station_id;
            $fid = $suser->factory_id;
        }
    }

    /**
     * 获取当前用户
     * @return null
     */
    protected function getCurrentUser($type) {
        $user = null;

        if ($type == User::USER_BACKEND_ADMIN) {
            $user = Auth::guard('zongpingtai')->user();
        }
        else if ($type == User::USER_BACKEND_FACTORY) {
            $user = Auth::guard('gongchang')->user();
        }
        else if ($type == User::USER_BACKEND_STATION) {
            $user = Auth::guard('naizhan')->user();
        }
        else {
            $guard_user_id = session('guard_user_id');

            if ($guard_user_id) {
                $user = User::find($guard_user_id);
            }
        }

        return $user;
    }

    /**
     * 添加系统日志
     * @param $page
     * @param $operation
     */
    protected function addSystemLog($usertype, $page, $operation) {
        $sysMgrCtrl = new SysManagerCtrl();
        $sysMgrCtrl->addSystemLog($usertype, $page, $operation);
    }

}
