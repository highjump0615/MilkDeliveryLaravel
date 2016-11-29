<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
            $nId = Auth::guard('factory')->user()->factory_id;
        }
        else {
            $nId = Auth::guard('naizhan')->user()->factory_id;
        }

        return $nId;
    }
}
