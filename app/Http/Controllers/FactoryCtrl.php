<?php

namespace App\Http\Controllers;

use App\Model\FactoryModel\Factory;
use App\Model\UserModel\User;
use App\Model\UserModel\UserRole;
use Illuminate\Http\Request;
use App\Model\UserModel\Page;
use App\Model\WechatModel\Wxmenu;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use App\Model\WechatModel\WechatMenu;
use App\Model\WechatModel\WechatAd;

class FactoryCtrl extends Controller
{
    public function viewUserPage(Request $request)
    {
        $child = 'yonghu';
        $parent = 'yonghu';
        $current_page = 'yonghu';
        $factory = Factory::where('is_deleted','<>', '1')->get();
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.yonghu.yonghu', [
            'factory' => $factory,
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page
        ]);
    }

    public function changeStatus(Request $request){
        $current_id = $request->input('id');
        $status = $request->input('status');
        $factory = Factory::find($current_id);
        $factory->status = $status;
        $factory->save();

        $users = User::where('factory_id',$current_id)->get();
        foreach ($users as $u){
            if($status == 0){
                $u->status = $status;
            }
            elseif ($status == 1){
                if($u->user_role_id == 1){
                    $u->status = $status;
                }
            }
            $u->save();
        }
        return Response::json($factory);
    }

    public function showTianjia(Request $request)
    {
        $child = 'yonghu';
        $parent = 'yonghu';
        $current_page = 'tianjia';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();
        $last_factory = Factory::where('status',1)->where('is_deleted',0)->orderby('id','desc')->first();

        if($last_factory)
            $factory_number = $last_factory->id+1;
        else
            $factory_number = 1;

        $created_number = "FAC".$factory_number;
        return view('zongpingtai.yonghu.yonghu.tianjia', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'factory_number'=>$created_number,
        ]);
    }

    public function storeTianjia(Request $request)
    {

        $name = $request->input('name');
        $number = $request->input('number');
        $contact = $request->input('contact');
        $phonenumber = $request->input('phonenumber');
        $status = $request->input('status');

        $end_at = $request->input('end_at');
        $factory_id = $request->input('factory_id');
        $factory_password = $request->input('factory_password');
        $public_name = $request->input('public_name');
        $public_id = $request->input('public_id');
        $wechat_id = $request->input('wechat_id');
        $app_id = $request->input('app_id');
        $app_secret = $request->input('app_secret');
        $wechat_type = $request->input('wechat_type');
        $qrcode = $request->input('qrcode');

        $fa = new Factory;
        $fa->name = $name;
        $fa->number = $number;
        $fa->contact = $contact;
        $fa->phone = $phonenumber;
        $fa->status = $status;

        $fa->end_at = $end_at;
        $fa->factory_id = $factory_id;
        $fa->factory_password = bcrypt($factory_password);
        $fa->public_name = $public_name;
        $fa->public_id = $public_id;
        $fa->wechat_id = $wechat_id;
        $fa->app_id = $app_id;
        $fa->app_secret = $app_secret;
        $fa->wechat_type = $wechat_type;
        $fa->qrcode = $qrcode;
        $fa->is_deleted = 0;

        if ($request->hasFile('logo')) {
            $file = Input::file('logo');
            $name = rand(1, 9999) . '-' . $file->getClientOriginalName();
            $file->move(public_path() . '/uploads/images/logo', $name);
            $path = '/uploads/images/logo/' . $name;
            $fa->logo_url = $path;
        }
        $fa->save();
        $current_factory_id = $fa->id;

        $factory_user = new User;
        $factory_user->name = $factory_id;
        $factory_user->password = bcrypt($factory_password);
        $factory_user->status = Factory::FACTORY_STATUS_ACTIVE ;
        $factory_user->factory_id = $current_factory_id;
        $factory_user->user_role_id = UserRole::USERROLE_GONGCHANG_TOTAL_ADMIN;
        $factory_user->backend_type = UserRole::USERROLE_BACLEND_TYPE_GONGCHANG;
        $factory_user->save();

        return redirect()->route('yonghu_page');
    }

    public function showTianjiaModify(Request $request, $user_id)
    {
        $child = 'yonghu';
        $parent = 'yonghu';
        $current_page = 'xiangqing';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();
        $factory = Factory::where('id',$user_id)->get()->first();
        return view('zongpingtai.yonghu.yonghu.xiangqing', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'factory' => $factory,
        ]);
    }

    public function updateTianjia(Request $request,$user_id)
    {
        $fa = Factory::find($user_id);

        $name = $request->input('name');
        $number = $request->input('number');
        $contact = $request->input('contact');
        $phonenumber = $request->input('phonenumber');
        $status = $request->input('status');

        $end_at            = $request->input('end_at');
        $factory_id        = $request->input('factory_id');
        $factory_password  = $request->input('factory_password');
        $public_name       = $request->input('public_name');
        $public_id         = $request->input('public_id');
        $wechat_id         = $request->input('wechat_id');
        $app_id            = $request->input('app_id');
        $app_secret        = $request->input('app_secret');
        $app_url           = $request->input('app_url');
        $app_token         = $request->input('app_token');
        $app_encoding_key  = $request->input('app_encoding_key');
        $app_mchid         = $request->input('app_mchid');
        $app_paysignkey    = $request->input('app_paysignkey');
        $wechat_type = $request->input('wechat_type');
        $qrcode = $request->input('qrcode');

        $fa->name              = $name;
        $fa->number            = $number;
        $fa->contact           = $contact;
        $fa->phone             = $phonenumber;
        $fa->status            = $status;
        $fa->end_at            = $end_at;
        $fa->factory_id        = $factory_id;
        $fa->factory_password  = bcrypt($factory_password);
        $fa->public_name       = $public_name;
        $fa->public_id         = $public_id;
        $fa->wechat_id         = $wechat_id;
        $fa->app_id            = $app_id;
        $fa->app_secret        = $app_secret;
        $fa->app_url           = $app_url;
        $fa->app_token         = $app_token;
        $fa->app_encoding_key  = $app_encoding_key;
        $fa->app_mchid         = $app_mchid;
        $fa->app_paysignkey    = $app_paysignkey;
        $fa->wechat_type       = $wechat_type;
        $fa->qrcode            = $qrcode;
        $fa->is_deleted        = 0;

        if ($request->hasFile('logo')) {
            $file = Input::file('logo');
            $name = rand(1, 9999) . '-' . $file->getClientOriginalName();
            $file->move(public_path() . '/uploads/images/logo', $name);
            $path = '/uploads/images/logo/' . $name;
            $fa->logo_url = $path;
        }
        $fa->save();

        $user = User::where('factory_id',$user_id)->where('user_role_id',1)->get()->first();
        $user->name = $factory_id;
        $user->password = bcrypt($factory_password);
        $user->save();

        return redirect()->route('yonghu_page');
    }

    public function showPublicAccountSettingPage(Request $request, $factory_id) {
        $child = 'gongzhonghaosheding';
        $parent = 'yonghu';
        $current_page = 'gongzhonghaosheding';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();

        $factory = Factory::find($factory_id);

        if(!$factory) {
            abort(403);
        }
        $wxmeun = Wxmenu::where('factoryid', $factory_id)->get();
        $products = $factory->active_products;
        
        //load ad images
        $banner_ads = array();
        $promo_ads = array();

        for($i= 1; $i<=4; $i++) {
            $banner_ads[$i] = WechatAd::where('factory_id', $factory_id)
                ->where('type', WechatAd::WECHAT_AD_TYPE_BANNER)
                ->where('image_no', $i)
                ->get()->first();

            $promo_ads[$i] = WechatAd::where('factory_id', $factory_id)
                ->where('type', WechatAd::WECHAT_AD_TYPE_PROMOTION)
                ->where('image_no', $i)
                ->get()->first();
        }

        return view('zongpingtai.yonghu.yonghu.gongzhonghaosheding', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'products' => $products,
            'banners' => $banner_ads,
            'promos' => $promo_ads,
            'wxmeun' => $wxmeun,
            'factory' => $factory,
            'factory_id' => $factory->id,
        ]);
    }

    public function updatePublicAccountSetting(Request $request, $factory_id)
    {
        $factory = Factory::find($factory_id);


        /* banner ads */

        for($i=1; $i<=4; $i++) {
            $product_id = $request->input('product_banner_'.$i);
            $ad = WechatAd::where('factory_id', $factory_id)
                ->where('type', WechatAd::WECHAT_AD_TYPE_BANNER)
                ->where('image_no', $i)->get()->first();

            if ($ad == null) {
                $ad = new WechatAd();

                $ad->factory_id = $factory_id;
                $ad->image_no = $i;
                $ad->type = WechatAd::WECHAT_AD_TYPE_BANNER;
            }

            if($ad->product_id!=-1)
                $ad->product_id = $product_id;
            else
                continue;

            if ($request->hasFile('banner'.$i)) {
                $fileName = 'ad_banner' . $factory_id . '_' . $i . '.' . $request->file('banner'.$i)->getClientOriginalExtension();
                $request->file('banner'.$i)->move(base_path() . '/public/img/ads/', $fileName);

                $url = '/img/ads/' . $fileName;
                $ad->image_url = $url;
            }

            $ad->save();

        }

        /* promo ads */
        for($i=1; $i<=4; $i++) {
            $product_id = $request->input('product_promo_'.$i);
            $ad = WechatAd::where('factory_id', $factory_id)
                ->where('type', WechatAd::WECHAT_AD_TYPE_PROMOTION)
                ->where('image_no', $i)->get()->first();

            if ($ad == null) {
                $ad = new WechatAd();

                $ad->factory_id = $factory_id;
                $ad->image_no = $i;
                $ad->type = WechatAd::WECHAT_AD_TYPE_PROMOTION;
            }

            if($ad->product_id!=-1)
                $ad->product_id = $product_id;
            else
                continue;



            if ($request->hasFile('promo'.$i)) {
                $fileName = 'ad_promo' . $factory_id . '_' . $i . '.' . $request->file('promo'.$i)->getClientOriginalExtension();
                $request->file('promo'.$i)->move(base_path() . '/public/img/ads/', $fileName);

                $url = '/img/ads/' . $fileName;
                $ad->image_url = $url;
            }

            $ad->save();
        }




        return redirect()->route('yonghu_page');
    }

    public function delete_banner(Request $request) {
        $id = $request->input('banner_id');
        $factory_id = $request->input('factory_id');

        $ad = WechatAd::where('factory_id', $factory_id)
            ->where('type', WechatAd::WECHAT_AD_TYPE_BANNER)
            ->where('image_no', $id)->get()->first();

        if($ad) {
            $ad->delete();

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status'=>'failure', 'msg'=>'没有照片']);
    }
}