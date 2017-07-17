<?php

namespace App\Http\Controllers;

use App\Model\BasicModel\ProvinceData;
use Auth;
use App\Model\BasicModel\Bank;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\DSDeliveryArea;
use Illuminate\Http\Request;
use App\Model\UserModel\Page;
use App\Model\FactoryModel\Factory;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class DeliveryStationCtrl extends Controller
{
    public function showJibenziliao(Request $request){
        $current_factory_id = Auth::guard('naizhan')->user()->station_id;
        $child = 'jibenziliao';
        $parent = 'naizhan';
        $current_page = 'jibenziliao';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        $dsinfo = DeliveryStation::find($current_factory_id);
        $billing_bank = Bank::find($dsinfo->billing_bank_id);
        $freepay_bank = Bank::find($dsinfo->freepay_bank_id);
        $deliveryarea = DSDeliveryArea::where('station_id',$current_factory_id)->get()->groupBy(function($area){
            $addr = $area->address;
            $addrs = explode(" ", $addr);
            return $addrs[0].$addrs[1].$addrs[2].$addrs[3];
        });

        $dsinfo["billing_bank"] = $billing_bank;
        $dsinfo["freepay_bank"] = $freepay_bank;
        $dsinfo["deliveryarea"] = $deliveryarea;
        return view('naizhan.naizhan.jibenziliao',[
            'pages'=>$pages,
            'child'=>$child,
            'parent'=>$parent,
            'current_page'=>$current_page,
            'dsinfo'=>$dsinfo,
        ]);
    }
    //

    public function deleteDeliveryArea(Request $request){
        $station_id = $request->input('station_id');
        $street = $request->input('street');
        $deliveryareas = DSDeliveryArea::where('station_id',$station_id)->where('address','LIKE','%'." ".$street." "."%")->get();
        foreach ($deliveryareas as $da){
            $da->delete();
        }
        return Response::json(['status'=>"success"]);
    }
}
