<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\WechatModel\WechatAd;
use Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Model\ProductModel\Product;
use App\Model\FactoryModel\Factory;
use App\Model\ProductModel\ProductCategory;

class WeChatCtrl extends Controller
{
    public function showIndexPage(Request $request) {
        $factory_id = 1;
        $factory = Factory::find($factory_id);

        if($factory == null)
            abort(403);

        $banners = WechatAd::where('factory_id', $factory_id)
            ->where('type', WechatAd::WECHAT_AD_TYPE_BANNER)
            ->orderBy('image_no')
            ->get();

        $promos = WechatAd::where('factory_id', $factory_id)
            ->where('type', WechatAd::WECHAT_AD_TYPE_PROMOTION)
            ->orderBy('image_no')
            ->get();

        $products = Product::where('factory_id', $factory_id)
            ->where('is_deleted', 0)
            ->where('status', Product::PRODUCT_STATUS_ACTIVE)
            ->orderBy('id', 'desc')
            ->take(4)
            ->get();

        return view('weixin.index', [
            'banners' => $banners,
            'promos' => $promos,
            'products' => $products,
        ]);
    }

    public function gerenzhongxin(Request $request){
        return view('weixin.gerenzhongxin', [

        ]);
    }
    
    public function dingdanrijihua(Request $request){
        return view('weixin.dingdanrijihua', [

        ]);
    }

    public function querendingdan(Request $request){
        return view('weixin.querendingdan', [

        ]);
    }

    public function dingdanxiugai(Request $request){
        return view('weixin.dingdanxiugai', [

        ]);
    }

    public function danrixiugai(Request $request){
        return view('weixin.danrixiugai', [

        ]);
    }

    public function dingdanliebiao(Request $request){
        return view('weixin.dingdanliebiao', [

        ]);
    }

    public function dingdanxiangqing(Request $request){
        return view('weixin.dingdanxiangqing', [

        ]);
    }

    public function xuedan(Request $request){
        return view('weixin.xuedan', [

        ]);
    }

    public function toushu(Request $request){
        return view('weixin.toushu', [

        ]);
    }

    /* 商品列表 */
    public function shangpinliebiao(Request $request){

        //TODO:
        $factory_id = 1;

        $factory = Factory::find($factory_id);

        if(!$factory) {
            abort(403);
        }

        $products = $factory->active_products;
        $categories = ProductCategory::where('factory_id', $factory_id)
            ->where('is_deleted', 0)
            ->get();

        $category_id = 0;



        if($request->has('category'))
            $category_id = $request->input('category');
        else
            if($categories->first()) {
                $category_id = $categories->first()->id;
            }

        return view('weixin.shangpinliebiao', [
            'categories' => $categories,
            'products' => $products,
            'category' => $category_id,
        ]);
    }

    public function wodepingjia(Request $request){
        return view('weixin.wodepingjia', [

        ]);
    }
    public function dingdanpingjia(Request $request){
        return view('weixin.dingdanpingjia', [

        ]);
    }
    public function zhifuchenggong(Request $request){
        return view('weixin.zhifuchenggong', [

        ]);
    }
    public function zhifushibai(Request $request){
        return view('weixin.zhifushibai', [

        ]);
    }

    public function xinxizhongxin(Request $request){
        return view('weixin.xinxizhongxin', [

        ]);
    }

    public function dizhiliebiao(Request $request){
        return view('weixin.dizhiliebiao', [

        ]);
    }

    public function dizhitianxie(Request $request){
        return view('weixin.dizhitianxie', [

        ]);
    }

    public function tianjiadingdan(Request $request){
        return view('weixin.tianjiadingdan', [

        ]);
    }

    public function gouwuche(Request $request){
        return view('weixin.gouwuche', [

        ]);
    }

}