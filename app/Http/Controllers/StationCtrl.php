<?php

namespace App\Http\Controllers;

use App\Model\BasicModel\ProvinceData;
use Auth;
use App\Model\BasicModel\Bank;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\DSDeliveryArea;
use Illuminate\Http\Request;
use App\Model\UserModel\Page;
use App\Model\UserModel\User;
use App\Model\SystemModel\SysLog;

use App\Http\Controllers\Controller;


class StationCtrl extends Controller
{
    /**
     * 打开基本资料页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showJibenziliao(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        
        $child = 'jibenziliao';
        $parent = 'naizhan';
        $current_page = 'jibenziliao';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        $dsinfo = DeliveryStation::find($current_station_id);
        $billing_bank = Bank::find($dsinfo->billing_bank_id);
        $freepay_bank = Bank::find($dsinfo->freepay_bank_id);

        $deliveryarea = DSDeliveryArea::where('station_id',$current_station_id)->get()->groupBy(function($area){
            $addr = $area->address;
            $addrs = explode(" ", $addr);
            return $addrs[0].$addrs[1].$addrs[2].$addrs[3];
        });

        $dsinfo["billing_bank"] = $billing_bank;
        $dsinfo["freepay_bank"] = $freepay_bank;
        $dsinfo["deliveryarea"] = $deliveryarea;

        $provinces = ProvinceData::all();

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_STATION, '奶站基本资料', SysLog::SYSLOG_OPERATION_VIEW);

        return view('naizhan.naizhan.jibenziliao',[
            'pages'=>$pages,
            'child'=>$child,
            'parent'=>$parent,
            'current_page'=>$current_page,
            'dsinfo'=>$dsinfo,
            'province'=>$provinces,
        ]);
    }
}
