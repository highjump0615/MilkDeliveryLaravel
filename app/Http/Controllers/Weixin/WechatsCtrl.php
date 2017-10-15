<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/9
 * Time: 15:05
 */

namespace App\Http\Controllers\Weixin; 
use App\Http\Controllers\Controller;
use App\Http\Controllers\Weixin\WechatesCtrl;
use App\Model\FactoryModel\Factory;
use Illuminate\Http\Request;

class WeChatsCtrl extends Controller
{   
    
    public function index(Request $request)
    {
		if(isset($_GET['typesid'])){
			$typesid = $_GET['typesid'];
			$factory = Factory::find($typesid);
			$wechatObj = WechatesCtrl::withFactory($factory);
			if (!isset($_GET['echostr'])) {
					$wechatObj->responseMsg($request);
			}else{
				$wechatObj->valid();
			}
		}
    }
}
