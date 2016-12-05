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


class DatableCtrl extends Controller
{
    public function showDatable(Request $request) {
        $child = 'shujuku';
        $parent = 'xitong';
        $current_page = 'shujuku';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();
        $factory = DB::select(DB::raw('SHOW TABLE STATUS'));
        return view('zongpingtai.xitong.shujuku', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'factory' => $factory
        ]);
    }
}