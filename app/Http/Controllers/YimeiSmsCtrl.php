<?php

namespace App\Http\Controllers;

use App\Model\DeliveryModel\DeliveryStation;
use Illuminate\Http\Request;
use App\Model\UserModel\Page;
use DB;
use App\Model\SystemModel\YimeiSms;

class YimeiSmsCtrl extends Controller
{
    public function showYimei(Request $request){
        $child = 'xinxijiekou';
        $parent = 'xitong';
        $current_page = 'xinxijiekou';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();

        $yimeiurl = YimeiSms::where('name', 'sms_yimei_url')->get()->first();
        $yimeiurlserial = YimeiSms::where('name', 'sms_yimei_serial')->get()->first();
        $yimeiurlpassword = YimeiSms::where('name', 'sms_yimei_password')->get()->first();

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
        $yimeiurl = YimeiSms::where('name', 'sms_yimei_url')->get()->first();
        $yimeiurlserial = YimeiSms::where('name', 'sms_yimei_serial')->get()->first();
        $yimeiurlpassword = YimeiSms::where('name', 'sms_yimei_password')->get()->first();

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

        $yimeiurl = YimeiSms::where('name', 'sms_yimei_url')->get()->first();
        $yimeiurlserial = YimeiSms::where('name', 'sms_yimei_serial')->get()->first();
        $yimeiurlpassword = YimeiSms::where('name', 'sms_yimei_password')->get()->first();

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

    public function sendSMS($phone, $msg)
    {
        $yimeiurl = YimeiSms::where('name', 'sms_yimei_url')->get()->first();
        $yimeiurlserial = YimeiSms::where('name', 'sms_yimei_serial')->get()->first();
        $yimeiurlpassword = YimeiSms::where('name', 'sms_yimei_password')->get()->first();

        $message = "您的验证码为:".$msg;

        $url = $yimeiurl->value."?cdkey=".$yimeiurlserial->value."&password=".$yimeiurlpassword->value."&phone=".$phone."&message=".$message;
        $this->sendGetRequest($url);
    }

    // 发送get请求
    private function sendGetRequest($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//不输出内容
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result =  curl_exec($ch);
        if (curl_errno($ch)) {return 'ERROR '.curl_error($ch);}
        curl_close($ch);

        return $result;
    }
}