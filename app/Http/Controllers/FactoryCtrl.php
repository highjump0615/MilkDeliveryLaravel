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
use App\Http\Controllers\Weixin\WechatesCtrl;
use App\Model\WechatModel\WechatMenu;
use App\Model\WechatModel\WechatAd;

use App\Model\SystemModel\SysLog;


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

    /**
     * 添加用户
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTianjia(Request $request)
    {
        $this->saveFactory(null, $request);

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_ADMIN, '用户管理', SysLog::SYSLOG_OPERATION_ADD);

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

    private function saveFactory($factory, $request) {

        //
        // 获取参数
        //
        $name               = $request->input('name');
        $number             = $request->input('number');
        $contact            = $request->input('contact');
        $phonenumber        = $request->input('phonenumber');
        $status             = $request->input('status');

        $end_at             = $request->input('end_at');
        $factory_id         = $request->input('factory_id');
        $factory_password   = $request->input('factory_password');
        $public_name        = $request->input('public_name');
        $public_id          = $request->input('public_id');
        $wechat_id          = $request->input('wechat_id');
        $app_id             = $request->input('app_id');
        $app_secret         = $request->input('app_secret');
        $app_url            = $request->input('app_url');
        $app_token          = $request->input('app_token');
        $app_encoding_key   = $request->input('app_encoding_key');
        $app_mchid          = $request->input('app_mchid');
        $app_paysignkey     = $request->input('app_paysignkey');
        $wechat_type        = $request->input('wechat_type');
        $qrcode             = $request->input('qrcode');

        if (!$factory) {
            $factory = new Factory;
            $factory_user = new User;
        }
        else {
            $factory_user = User::where('factory_id', $factory->id)->where('user_role_id',1)->get()->first();
        }

        $factory->name              = $name;
        $factory->number            = $number;
        $factory->contact           = $contact;
        $factory->phone             = $phonenumber;
        $factory->status            = $status;

        $factory->end_at            = $end_at;
        $factory->factory_id        = $factory_id;
        $factory->factory_password  = bcrypt($factory_password);
        $factory->public_name       = $public_name;
        $factory->public_id         = $public_id;
        $factory->wechat_id         = $wechat_id;
        $factory->app_id            = $app_id;
        $factory->app_secret        = $app_secret;
        $factory->app_url           = $app_url;
        $factory->app_token         = $app_token;
        $factory->app_encoding_key  = $app_encoding_key;
        $factory->app_paysignkey    = $app_paysignkey;
        $factory->app_mchid         = $app_mchid;
        $factory->wechat_type       = $wechat_type;
        $factory->qrcode            = $qrcode;
        $factory->is_deleted        = 0;

        if ($request->hasFile('logo')) {
            $file = Input::file('logo');
            $name = rand(1, 9999) . '-' . $file->getClientOriginalName();
            $file->move(public_path() . '/uploads/images/logo', $name);
            $path = '/uploads/images/logo/' . $name;
            $factory->logo_url = $path;
        }
        $factory->save();

        if(!empty($app_id) && !empty($app_secret) && !empty($app_encoding_key)  && !empty($app_token) && !empty($name) && !empty($user_id)){
            $wechatObj = new WeChatesCtrl($app_id, $app_secret, $app_encoding_key, $app_token, $name, $user_id);
            $wechatObj->createMenu();
        }

        $current_factory_id = $factory->id;

        $factory_user->name = $factory_id;
        $factory_user->password = bcrypt($factory_password);
        $factory_user->status = Factory::FACTORY_STATUS_ACTIVE ;
        $factory_user->factory_id = $current_factory_id;
        $factory_user->user_role_id = UserRole::USERROLE_GONGCHANG_TOTAL_ADMIN;
        $factory_user->backend_type = UserRole::USERROLE_BACKEND_TYPE_GONGCHANG;
        $factory_user->save();
    }

    /**
     * 修改保存
     * @param Request $request
     * @param $user_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTianjia(Request $request,$user_id)
    {
        $fa = Factory::find($user_id);

        $this->saveFactory($fa, $request);

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_ADMIN, '用户管理', SysLog::SYSLOG_OPERATION_EDIT);
            
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

    /**
     * 更新微信公众号首页图片
     * @param Request $request
     * @param $type
     * @param $factory_id
     */
    private function updatePictures(Request $request, $type, $factory_id) {

        for($i=1; $i<=4; $i++) {

            $strIdBanner = 'product_banner_'.$i;
            $strImgBanner = 'img_banner_url_'.$i;
            $strFileName = 'banner';

            if ($type == WechatAd::WECHAT_AD_TYPE_PROMOTION) {
                $strIdBanner = 'product_promo_'.$i;
                $strImgBanner = 'img_promo_url_'.$i;
                $strFileName = 'promo';
            }

            //
            // 保存广告图片
            //
            $product_id = $request->input($strIdBanner);
            if (empty($product_id)) {
                continue;
            }

            $ad = WechatAd::where('factory_id', $factory_id)
                ->where('type', $type)
                ->where('image_no', $i)
                ->get()
                ->first();

            // 没有图片，删除
            $strImg = $request->input($strImgBanner);
            if (empty($strImg)) {
                if ($ad) {
                    $ad->delete();
                }

                continue;
            }

            // 图片没有变化，下一个
            if (!$request->hasFile($strFileName . $i)) {
                continue;
            }

            if ($ad == null) {
                $ad = new WechatAd();

                $ad->factory_id = $factory_id;
                $ad->image_no = $i;
                $ad->type = $type;
            }

            $ad->product_id = $product_id;

            $fileName = 'ad_' . $strFileName . $factory_id . '_' . $i . '.' . $request->file($strFileName . $i)->getClientOriginalExtension();
            $request->file($strFileName . $i)->move(base_path() . '/public/img/ads/', $fileName);

            $url = '/img/ads/' . $fileName;
            $ad->image_url = $url;

            $ad->save();
        }
    }

    /**
     * 更新微信公众号方面的内容
     * @param Request $request
     * @param $factory_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePublicAccountSetting(Request $request, $factory_id)
    {
        $factory = Factory::find($factory_id);

        $this->updatePictures($request, WechatAd::WECHAT_AD_TYPE_BANNER, $factory_id);
        $this->updatePictures($request, WechatAd::WECHAT_AD_TYPE_PROMOTION, $factory_id);

        // 客服电话、推定电话
        $factory->service_phone = $request->input('service_phone');
        $factory->return_phone = $request->input('return_phone');
        $factory->save();

        return redirect()->route('yonghu_page');
    }

    /**
     * 删除广告图片
     * @param Request $request
     * @return mixed
     */
    public function delete_banner(Request $request) {
        $id = $request->input('banner_id');
        $factory_id = $request->input('factory_id');
        $type = $request->input('type');

        $ad = WechatAd::where('factory_id', $factory_id)
            ->where('type', $type)
            ->where('image_no', $id)
            ->get()
            ->first();

        if($ad) {
            $ad->delete();

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status'=>'failure', 'msg'=>'没有图片']);
    }
}