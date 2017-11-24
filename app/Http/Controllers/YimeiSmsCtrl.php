<?php

namespace App\Http\Controllers;

use App\Model\DeliveryModel\DeliveryStation;
use Illuminate\Http\Request;
use App\Model\UserModel\Page;
use DB;
use App\Model\SystemModel\YimeiSms;

require_once app_path() . "/Lib/ChuanglanSmsHelper/ChuanglanSmsApi.php";

class YimeiSmsCtrl extends Controller
{
    public function showYimei(Request $request){
        $child = 'xinxijiekou';
        $parent = 'xitong';
        $current_page = 'xinxijiekou';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();

        $yimeiurl = YimeiSms::where('name', 'sms_yimei_url')->first();
        $yimeiurlserial = YimeiSms::where('name', 'sms_yimei_serial')->first();
        $yimeiurlpassword = YimeiSms::where('name', 'sms_yimei_password')->first();

        return view('zongpingtai.xitong.xinxijiekou', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'yimeiurlserial' => $yimeiurlserial->value,
            'yimeiurlpassword' => $yimeiurlpassword->value,
            'yimeiurl' => $yimeiurl->value,
            'mass' => ''
        ]);
    }

    public function showPost(Request $request){
        $yimeiurl = YimeiSms::where('name', 'sms_yimei_url')->first();
        $yimeiurlserial = YimeiSms::where('name', 'sms_yimei_serial')->first();
        $yimeiurlpassword = YimeiSms::where('name', 'sms_yimei_password')->first();

        if ($yimeiurl->value != $request['yimeiurl'])
            DB::update("update yimeisms set value = '".$request['yimeiurl']."' where name = 'sms_yimei_url'");

        if ($yimeiurlserial->value != $request['yimeiurlserial'])
            DB::update("update yimeisms set value ='".$request['yimeiurlserial']."' where name = 'sms_yimei_serial'");

        if ($yimeiurlpassword->value != $request['yimeiurlpassword'])
            DB::update("update yimeisms set value = '".$request['yimeiurlpassword']."' where name = 'sms_yimei_password'");

        $child = 'xinxijiekou';
        $parent = 'xitong';
        $current_page = 'xinxijiekou';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();

        $yimeiurl = YimeiSms::where('name', 'sms_yimei_url')->first();
        $yimeiurlserial = YimeiSms::where('name', 'sms_yimei_serial')->first();
        $yimeiurlpassword = YimeiSms::where('name', 'sms_yimei_password')->first();

        return view('zongpingtai.xitong.xinxijiekou', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'yimeiurlserial' => $yimeiurlserial->value,
            'yimeiurlpassword' => $yimeiurlpassword->value,
            'yimeiurl' => $yimeiurl->value,
            'mass' => '修改成功'
        ]);
    }

    /**
     * 发送短信
     * @param $phone
     * @param $msg
     */
    public function sendSMS($phone, $msg)
    {
        $yimeiurlserial = YimeiSms::where('name', 'sms_yimei_serial')->first();
        $yimeiurlpassword = YimeiSms::where('name', 'sms_yimei_password')->first();

        $message = "您的验证码是:".$msg;

        $clapi = new \ChuanglanSmsApi($yimeiurlserial->value, $yimeiurlpassword->value);
        $clapi->sendSMS($phone, $message,'true');
    }
}