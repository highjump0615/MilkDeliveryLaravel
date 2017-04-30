<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 16/12/5
 * Time: PM2:31
 */

namespace App\Http\Controllers;
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\UserModel\Page;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class ImportCtrl extends Controller
{
    /**
     * 显示导入页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showImport(Request $request) {
        $child = 'shujuku';
        $parent = 'xitong';
        $current_page = 'import';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();


        return view('zongpingtai.xitong.import', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
        ]);
    }

    /**
     * 处理上传的文件
     * @param Request $request
     * @return string
     */
    public function uploadFile(Request $request) {

        $factory_id = 7;

        if ($request->hasFile('upload')){

            $file = $request->file('upload');

            Excel::load($file, function ($reader) {

                $reader->dump();

//                $reader->each(function($sheet) {
//                });
            });
        }

        return "Started import ...";
    }
}