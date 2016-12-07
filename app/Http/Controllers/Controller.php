<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Auth;
use App\Model\UserModel\User;

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

    protected function getCurrentUser(){
        $guard_user_id = session('guard_user_id');

        if($guard_user_id) {
            $user = User::find($guard_user_id);

            return $user;
        }

        return null;
    }
}
