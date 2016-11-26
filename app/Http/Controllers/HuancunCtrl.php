<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\UserModel\Page;

class HuancunCtrl extends Controller
{
    
    /**
     * 打开系统日志页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showSystemHuan(Request $request){
        $child = 'gengxinhuankun';
        $parent = 'xitong';
        $current_page = 'gengxinhuankun';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.xitong.gengxinhuankun', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'mass'=>''
        ]);
        return view('zongpingtai.xitong.gengxinhuankun');
    }
    
    public function delDirAndFile($path, $delDir = false) 
    {
        $handle = opendir($path);
        if ($handle) {
            while (false !== ( $item = readdir($handle) )) {
                if ($item != "." && $item != "..")
                    is_dir("$path/$item") ? $this->delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
            }
            closedir($handle);
            if ($delDir)
                return rmdir($path);
        }else {
            if (file_exists($path)) {
                return unlink($path);
            } else {
                return FALSE;
            }
        }
    }
    
    
    public function showPost(Request $request){
        $directory = '../storage/framework';
        if($request['option1'] == 'data'){
            $this->delDirAndFile($directory.'/cache');
            $this->delDirAndFile($directory.'/sessions');
             $child = 'gengxinhuankun';
            $parent = 'xitong';
            $current_page = 'gengxinhuankun';
            $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();
            return view('zongpingtai.xitong.gengxinhuankun', [
                'pages' => $pages,
                'child' => $child,
                'parent' => $parent,
                'current_page' => $current_page,
                'mass'=>'更新成功'
            ]);
            return view('zongpingtai.xitong.gengxinhuankun');
        }
       
    }
}