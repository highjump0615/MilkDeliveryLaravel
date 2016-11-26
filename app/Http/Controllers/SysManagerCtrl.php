<?php

namespace App\Http\Controllers;

use App\Model\SystemModel\SysLog;
use Faker\Provider\cs_CZ\DateTime;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Model\UserModel\Page;

class SysManagerCtrl extends Controller
{
    private $mnPageCount = 15;

    /**
     * 添加系统日志
     * @param $user
     * @param $page
     * @param $operation
     */
    public function addSystemLog($user, $page, $operation) {
        $systemLog = new SysLog();

        $systemLog->user_id = $user->id;
        $systemLog->ipaddress = $user->last_used_ip;
        $systemLog->page = $page;
        $systemLog->operation = $operation;

        $systemLog->save();
    }

    /**
     * 打开系统日志页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSystemLog(Request $request) {

        // 页面信息lll
        $child = 'chakanrizhi';
        $parent = 'xitong';
        $current_page = 'chakanrizhi';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();

        // 获取参数
        $username = $request->input('username');
        $date = $request->input('date');
        $page = $request->input('page');

        // 默认是页面1
        if (empty($page)) {
            $page = 1;
        }

        $queryLog = null;
        $getField = null;

        $offset = ((int)$page - 1) * $this->mnPageCount;

        // 筛选
        $strDateQuery = 'created_at';
        if (!empty($username)) {
            $queryLog = SysLog::leftJoin('users', 'users.id', '=', 'systemlog.user_id')->where('users.name', 'like', '%' . $username . '%');
            $strDateQuery = 'systemlog.created_at';
            $getField = ['*', $strDateQuery];
        }
        if (!empty($date)) {
            $queryLog = SysLog::where($strDateQuery, 'like', $date . '%');
        }

        // 查询数据
        if ($queryLog) {
            $count = $queryLog->count();
            $aryLog = $queryLog->limit($this->mnPageCount)->offset($offset)->get($getField);
        }
        else {
            $count = SysLog::count();
            $aryLog = SysLog::limit($this->mnPageCount)->offset($offset)->get($getField);
        }

        return view('zongpingtai.xitong.chakanrizhi', [
            // 页面信息
            'pages'             => $pages,
            'child'             => $child,
            'parent'            => $parent,
            'current_page'      => $current_page,

            // 参数
            'username'          => $username,
            'date'              => $date,
            'page'              => $page,

            // 数据
            'count'             => $count,
            'logdata'           => $aryLog,
            'total_page'        => ceil((float)$count / (float)$this->mnPageCount)
        ]);
    }
}
