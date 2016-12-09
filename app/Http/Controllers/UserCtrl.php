<?php

namespace App\Http\Controllers;

use App\Model\SystemModel\SysLog;
use App\Model\DeliveryModel\DeliveryStation;
use Illuminate\Http\Request;
use App\Model\UserModel\Page;
use App\Model\UserModel\UserRole;
use App\Http\Requests;
use App\Model\UserModel\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class UserCtrl extends Controller
{
    public function viewPage(Request $request)
    {
        $current_factory_id = Auth::guard('gongchang')->User()->factory_id;
        $child = 'yonghu';
        $parent = 'xitong';
        $current_page = 'yonghu';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        $role_name = UserRole::where('backend_type', '2')->where('factory_id',$current_factory_id)->get();
        $userinfo = User::where('backend_type','2')->where('factory_id',$current_factory_id)->get();
        return view('gongchang.xitong.yonghu', [
            'pages' => $pages,
            'userinfo' => $userinfo,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'role_name' => $role_name,
            'current_factory_id'=>$current_factory_id
        ]);
    }

    public function changeStatus(Request $request){
        $current_id = $request->input('id');
        $status = $request->input('status');
        $user = User::find($current_id);
        $user->status = $status;
        $user->save();
        return Response::json($user);
    }

    public function getPage($user_id=null)
    {
        if($user_id == null){
            return Response::json(['status'=>'failed']);
        }
        else{
            $userinfo = User::find($user_id);
            return Response::json($userinfo);
        }
    }

    public function getNaizhanPage($user_id=null)
    {
        if($user_id == null){
            return Response::json(['status'=>'failed']);
        }
        else{
            $userinfo = User::find($user_id);
            return Response::json($userinfo);
        }
    }

    /**
     * 添加奶厂用户
     * @param Request $request
     * @return mixed
     */
    public function addAccount(Request $request)
    {
        $is_exist = User::where('name',$request->input('name'))->get()->first();
        if(empty($is_exist)){
            $user = new User;
            $user->name = $request->input('name');
            $user->password = bcrypt($request->input('password'));
            $user->factory_id = $request->input('factory_id');
            $user->nick_name = $request->input('nick_name');
            $user->user_role_id = $request->input('user_role_id');
            $user->status = $request->input('status');
            $user->backend_type = $request->input('backend_type');
            $user->description = $request->input('description');
            $user->last_used_ip = $request->ip();
            $user->save();

            // 添加系统日志
            $this->addSystemLog(User::USER_BACKEND_FACTORY, '用户管理', SysLog::SYSLOG_OPERATION_ADD);

            return Response::json($user);
        }
        else{
            return Response::json(['is_exist'=>1]);
        }
    }

    /*Delete & Update user-info using ajax*/
    public function updateAccount(Request $request, $user_id)
    {
        $user = User::find($user_id);
        $user->name = $request->input('name');
        if($request->input('password')!=null){
            $user->password = bcrypt($request->input('password'));
        }
        $user->nick_name = $request->input('nick_name');
        $user->status = $request->input('status');
        $user->user_role_id = $request->input('user_role_id');

        $user->save();

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '用户管理', SysLog::SYSLOG_OPERATION_EDIT);

        return Response::json($user);
    }

    public function removeAccount($user_id)
    {
        $removeAccount = User::destroy($user_id);

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '用户管理', SysLog::SYSLOG_OPERATION_REMOVE);

        return Response::json($removeAccount);
    }

    public function viewZongpingGuanliyuan(Request $request)
    {
        $child = 'guanliyuanzhongxin';
        $parent = 'yonghu';
        $current_page = 'guanliyuanzhongxin';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();
        $role_name = UserRole::where('backend_type', '1')->get();
        $userinfo = User::where('backend_type','1')->get();
        return view('zongpingtai.yonghu.guanliyuanzhongxin', [
            'pages' => $pages,
            'userinfo' => $userinfo,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'role_name' => $role_name
        ]);
    }

    public function  getZongpingGuanliyuan($user_id){
        $userinfo = User::find($user_id);
        return Response::json($userinfo);
    }

    public function addZongpingGuanliyuan(Request $request){
        $name = $request->input('name');
        $is_exist = User::where('name',$name)->get()->first();

        if(empty($is_exist)){
            $user = new User;
            $user->name = $request->input('name');
            $user->password = bcrypt($request->input('password'));
            $user->factory_id = 0;
            $user->user_role_id = $request->input('user_role_id');
            $user->status = $request->input('status');
            $user->backend_type = $request->input('backend_type');
            $user->last_used_ip = $request->ip();
            $user->description = $request->input('description');
            $user->save();

            // 添加系统日志
            $this->addSystemLog(User::USER_BACKEND_ADMIN, '管理员中心', SysLog::SYSLOG_OPERATION_ADD);

            return Response::json($user);
        }
        else{
            return Response::json(['is_exist'=>1]);
        }
    }

    public function updateZongpingGuanliyuan(Request $request, $user_id){
        $user = User::find($user_id);
        $user->name = $request->input('name');
        if($request->input('password') != null){
            $user->password = bcrypt($request->input('password'));
        }
        $user->status = $request->input('status');
        $user->user_role_id = $request->input('user_role_id');
        $user->description = $request->input('description');

        $user->save();

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_ADMIN, '管理员中心', SysLog::SYSLOG_OPERATION_EDIT);

        return Response::json($user);
    }

    public  function  changeStatusZongpingGuanliyuan(Request $request, $user_id){
        $user = User::find($user_id);
        $user->status = $request->input('status');
        $user->save();
        return Response::json($user);
    }

    public  function deleteZongpingGuanliyuan($user_id){
        $deleteZongpingGuanliyuan = User::destroy($user_id);

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_ADMIN, '管理员中心', SysLog::SYSLOG_OPERATION_REMOVE);

        return Response::json($deleteZongpingGuanliyuan);
    }

    /**
     * 打开奶站用户管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function stationJuese(Request $request){
        $current_station_id = $this->getCurrentStationId();

        $child = 'yonghu';
        $parent = 'xitong';
        $current_page = 'yonghu';
        $pages = Page::where('backend_type', '3')->where('parent_page', '0')->orderby('order_no')->get();

        $role_name = UserRole::where('backend_type', '3')->where('station_id',$current_station_id)->get();
        $userinfo = User::where('backend_type','3')->where('station_id',$current_station_id)->get();

        return view('naizhan.xitong.yonghu', [
            'pages'                 => $pages,
            'userinfo'              => $userinfo,
            'child'                 => $child,
            'parent'                => $parent,
            'current_page'          => $current_page,
            'role_name'             => $role_name,
            'current_station_id'    => $current_station_id,
        ]);
    }

    public function  getNaizhanGuanliyuan($user_id){
        $userinfo = User::find($user_id);
        return Response::json($userinfo);
    }

    public function addNaizhanGuanliyuan(Request $request){
        $current_station_id = $this->getCurrentStationId();
        $current_factory_id = $this->getCurrentFactoryId(false);

        $name = $request->input('name');
        $is_exist = User::where('name',$name)->get()->first();

        if(empty($is_exist)){
            $user = new User;
            $user->name = $request->input('name');
            $user->password = bcrypt($request->input('password'));
            $user->factory_id = $current_factory_id;
            $user->station_id = $current_station_id;
            $user->user_role_id = $request->input('user_role_id');
            $user->status = $request->input('status');
            $user->backend_type = $request->input('backend_type');
            $user->description = $request->input('description');
            $user->last_used_ip = $request->ip();
            $user->save();

            // 添加系统日志
            $this->addSystemLog(User::USER_BACKEND_STATION, '用户管理', SysLog::SYSLOG_OPERATION_ADD);

            return Response::json($user);
        }
        else{
            return Response::json(['is_exist'=>1]);
        }
    }

    public function updateNaizhanGuanliyuan(Request $request, $user_id){
        $user = User::find($user_id);
        $user->name = $request->input('name');
        if($request->input('password') != null){
            $user->password = bcrypt($request->input('password'));
        }
        $user->status = $request->input('status');
        $user->user_role_id = $request->input('user_role_id');

        $user->save();

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_STATION, '用户管理', SysLog::SYSLOG_OPERATION_EDIT);

        return Response::json($user);
    }

    public function changeStatusNaizhanGuanliyuan(Request $request){
        $user = User::find($request->input('id'));
        $user->status = $request->input('status');
        $user->save();
        return Response::json($user);
    }

    public  function deleteNaizhanGuanliyuan($user_id){
        $deleteZongpingGuanliyuan = User::destroy($user_id);

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_STATION, '用户管理', SysLog::SYSLOG_OPERATION_ADD);

        return Response::json($deleteZongpingGuanliyuan);
    }
}