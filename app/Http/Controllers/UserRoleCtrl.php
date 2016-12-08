<?php

namespace App\Http\Controllers;

use App\Model\UserModel\UserPageAccess;
use Illuminate\Http\Request;
use App\Model\UserModel\UserRole;
use App\Model\UserModel\Page;
use App\Model\SystemModel\SysLog;
use Auth;
use Illuminate\Support\Facades\Response;

class UserRoleCtrl extends Controller
{

    /*
        called when add role on Yonghuguanli page in Gongchangguanli, Zongpingtai
    */
    /*Save values seted on permission table*/
    public function store(Request $request) {
        $pageId = $request->input('input');
//            dd($pageId);
        $roleId = $request->input('roleId');

        $role = UserRole::find($roleId);
        $pages = Page::where('backend_type', $role->backend_type)->get();

        foreach($pages as $p) {
            $key = 'input'.$p->id;
            if($request->has($key)) {
                $access = $request->input($key);
            } else {
                $access = false;
            }

            $ur = UserPageAccess::where('user_role_id', $roleId)->where('page_id', $p->id)->get()->first();
            if($access) {
                if($ur == null) {
                    $ur = new UserPageAccess;
                    $ur->page_id = $p->id;
                    $ur->user_role_id = $roleId;
                    $ur->save();
                }
            }else {
                if($ur) {
                    $ur->delete();
                }
            }
        }

        // 添加系统日志
        $this->addSystemLog($role->backend_type, '角色管理', SysLog::SYSLOG_OPERATION_EDIT);

        if($role->backend_type == 1){
            return redirect()->route('zongpingtai_juese',['role_id'=> $roleId]);
            }
        elseif ($role->backend_type == 2){
            return redirect()->route('gongchang_juese',['role_id'=> $roleId]);
        }
        elseif ($role->backend_type == 3){
            return redirect()->route('naizhan_juese',['role_id'=> $roleId]);
        }
//            return redirect()->back();
    }
    /*View Gongchabg Juese page*/
    public function viewPage(Request $request, $role_id=null) {
        $child = 'juese';
        $parent = 'xitong';
        $current_page = 'juese';
        $pages = Page::where('backend_type','2')->where('parent_page', '0')->get();
        if($role_id != ''){
            $access_pages = UserPageAccess::where('user_role_id',$role_id)->get();
        }
        else{
            $access_pages = UserPageAccess::where('user_role_id','1')->get();
            $role_id = 1;
        }
        $role_name = UserRole::where('backend_type','2')->get();
        return view('gongchang.xitong.juese', [
            'pages'=>$pages,
            'child'=>$child,
            'parent'=>$parent,
            'current_page'=>$current_page,
            'role_name'=>$role_name,
            'access_pages'=>$access_pages,
            'role_id'=>$role_id,
        ]);
    }

    public function viewZongpingtaiPage(Request $request,$role_id=null) {
        $child = 'juese';
        $parent = 'yonghu';
        $current_page = 'juese';
        $pages = Page::where('backend_type','1')->where('parent_page', '0')->get();
        if($role_id != ''){
            $access_pages = UserPageAccess::where('user_role_id',$role_id)->get();
        }
        else{
            $access_pages = UserPageAccess::where('user_role_id','100')->get();
            $role_id = 100;
        }
        $role_name = UserRole::where('backend_type','1')->get();
        return view('zongpingtai.yonghu.juese', [
            'pages'=>$pages,
            'child'=>$child,
            'parent'=>$parent,
            'current_page'=>$current_page,
            'role_name'=>$role_name,
            'access_pages'=>$access_pages,
            'role_id'=>$role_id,
        ]);
    }

    public function stationJuese(Request $request,$role_id=null){
        $child = 'juese';
        $parent = 'xitong';
        $current_page = 'juese';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        if($role_id != ''){
            $access_pages = UserPageAccess::where('user_role_id',$role_id)->get();
        }
        else{
            $access_pages = UserPageAccess::where('user_role_id','200')->get();
            $role_id = 200;
        }
        $role_name = UserRole::where('backend_type','3')->get();
        return view('naizhan.xitong.juese', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'access_pages'=>$access_pages,
            'role_name'=>$role_name,
            'role_id'=>$role_id,
        ]);
    }
    
    /*Add role_name using ajax*/
    public function addRole(Request $request) {
        $type = $request->input('backend_type');
        $name = $request->input('name');

        $ur = new UserRole;
        $ur->name = $name;
        $ur->backend_type = $type;

        if($type == UserRole::USERROLE_BACKEND_TYPE_GONGCHANG){
            $current_factory_id = $this->getCurrentFactoryId(true);
            $ur->factory_id = $current_factory_id;
        }
        elseif($type == UserRole::USERROLE_BACKEND_TYPE_NAIZHAN){
            $current_station_id = $this->getCurrentStationId();
            $current_factory_id = $this->getCurrentFactoryId(false);
            $ur->station_id = $current_station_id;
            $ur->factory_id = $current_factory_id;
        }

        $ur->save();

        // 添加系统日志
        $this->addSystemLog($type, '角色管理', SysLog::SYSLOG_OPERATION_ADD);

        return Response::json(['id'=>$ur->id,'name'=>$name]);
    }

    /*Delete role name using ajax*/
    public function deleteRole($role_id) {
        $role = UserRole::find($role_id);

        // 添加系统日志
        $this->addSystemLog($role->backend_type, '角色管理', SysLog::SYSLOG_OPERATION_REMOVE);

        $role = UserRole::destroy($role_id);

        return Response::json($role);
    }

    /*return Permission Table*/
    public function index($role_id=null) {
        if($role_id == null) {
            return UserRole::all();
        } else {
            return UserRole::find($role_id);
        }
    }
}
