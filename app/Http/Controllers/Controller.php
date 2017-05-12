<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Weixin\WechatesCtrl;
use App\Model\WechatModel\WechatUser;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Model\UserModel\User;
use Auth;
use App;

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

    //
    // 微信版helper函数
    //

    /**
     * 获取奶厂id
     * @return int
     */
    protected function getCurrentFactoryIdW(Request $request) {

        // 本地环境下返回测试值
        if (App::environment('local')) {
            return 1;
        }

        $nId = session('factory_id');

        if (empty($nId)) {
            if (isset($_GET['state'])) {
                $nId = $_GET['state'];

                //save factory id in session
                $request->session()->put('factory_id', $nId);
            }
        }

        // 获取不到奶厂id, 终止
        if (empty($nId)) {
            abort(403);
        }

        return $nId;
    }

    /**
     * 获取当前用户
     * @param $factory
     * @return int|mixed
     */
    protected function getCurrentUserIdW($factory = null) {

        // 本地环境下返回测试值
        if (App::environment('local')) {
            return 127;
        }

        $nUserId = session('wechat_user_id');

        if (empty($nUserId) && !empty($factory)) {
            if (isset($_GET['code'])) {
                $wechatObj = new WeChatesCtrl($factory->app_id, $factory->app_secret, $factory->app_encoding_key, $factory->app_token, $factory->name, $factory->id);
                $codees = $wechatObj->codes($_GET['code']);

                //save wechat user id
                $open_id = $codees['openid'];

                $wechat_user = WechatUser::where('openid', $open_id)->first();
                if (!$wechat_user) {
                    $wechat_user = new WechatUser;
                    $wechat_user->openid = $open_id;
                    $wechat_user->factory_id = $factory->id;
                    $wechat_user->save();
                }
                $nUserId = $wechat_user->id;

                session(['wechat_user_id' => $nUserId]);
            }
        }

        // 获取不到奶厂id, 终止
        if (empty($nUserId)) {
            abort(403);
        }

        return $nUserId;
    }
}
