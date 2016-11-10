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

    /* 奶厂管理 / 基础信息管理 / 奶站管理 */
    public function showDeliveryStationPage(Request $request) {
        $child = 'naizhan';
        $parent = 'jichuxinxi';
        $current_page = 'naizhan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        $station_name = $request->input('station_name');
        $station_number = $request->input('station_number');
        $input_province = $request->input('province');
        $input_city = $request->input('city');
        $input_district = $request->input('district');
        if($station_name == null){
            $station_name = '';
        }
        if($station_number == null){
            $station_number = '';
        }

        $factory_id = Auth::guard('gongchang')->user()->factory_id;
        $factory = Factory::find($factory_id);
//        $deliveryStations = $factory->deliveryStations;
        $province = ProvinceData::all();
        if($input_province != null && $input_city != null){
            $deliveryStations = DeliveryStation::where('factory_id',$factory_id)->where('is_deleted',0)->where('name','LIKE','%'.$station_name.'%')->
            where('number','LIKE','%'.$station_number.'%')->where('address','LIKE', '%'.$input_province." ".$input_city." ".$input_district.'%')->get();
        }
        elseif ($input_province != null && $input_city == null){
            $deliveryStations = DeliveryStation::where('factory_id',$factory_id)->where('is_deleted',0)->where('name','LIKE','%'.$station_name.'%')->
            where('number','LIKE','%'.$station_number.'%')->where('address','LIKE','%'.$input_province.'%')->get();
        }
        elseif ($input_province == null){
            $deliveryStations = DeliveryStation::where('factory_id',$factory_id)->where('is_deleted',0)->where('name','LIKE','%'.$station_name.'%')->
            where('number','LIKE','%'.$station_number.'%')->get();
        }

        if($input_province == null){
            $input_province = "";
        }
        if($input_city == null){
            $input_city = "";
        }
        if($input_district == null){
            $input_district = "";
        }

        return view('gongchang.jichuxinxi.naizhan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'province' =>$province,            
            'stations' => $deliveryStations,
            'station_name' => $station_name,
            'station_number' => $station_number,
            'input_province' => $input_province,
            'input_city' => $input_city,
            'input_district' => $input_district,
        ]);
    }

    public function showDSDeliveryAreaChangePage($station_id) {
        $child = 'naizhan';
        $parent = 'jichuxinxi';
        $current_page = 'peisongfanwei-chakanbianji';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        $dsdeliveryarea = DSDeliveryArea::where('station_id',$station_id)->get();
        $area_address = array();
        if($dsdeliveryarea->first() != null){
            $i =0;
            foreach ($dsdeliveryarea as $da){
                $i++;
                if($da->address != null){
                    $addr = explode(" ",$da->address);
                    $area_address[$addr[3]][$i] = $addr[4];
                }
            }
        }
        $myAddress = DeliveryStation::find($station_id);
        $myAddr = explode(" ",$myAddress->address);
        $myDistrict = $myAddr[2];

        return view('gongchang.jichuxinxi.peisongfanwei-chakanbianji', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'station_id' =>$station_id,
            'address' => $area_address,
            'mydistrict' => $myDistrict,
        ]);
    }

    public function deleteDeliveryArea(Request $request){
        $station_id = $request->input('station_id');
        $street = $request->input('street');
        $deliveryareas = DSDeliveryArea::where('station_id',$station_id)->where('address','LIKE','%'." ".$street." "."%")->get();
        foreach ($deliveryareas as $da){
            $da->delete();
        }
        return Response::json(['status'=>"success"]);
    }

    public function insertDeliveryArea(Request $request){
        $station_id = $request->input('station_id');
        $street = $request->input('street');
        $xiaoqi = $request->input('xiaoqi');
        $station_addr = DeliveryStation::find($station_id)->address;
        $addr = explode(" ",$station_addr);
        foreach ($xiaoqi as $x){
            $current_addr = $addr[0]." ".$addr[1]." ".$addr[2]." ".$street." ".$x;
            $delivery_area = new DSDeliveryArea;
            $delivery_area->station_id = $station_id;
            $delivery_area->address = $current_addr;
            $delivery_area->save();
        }
        return Response::json(['street'=>$street]);
    }

    public function updateDeliveryArea(Request $request){
        $station_id = $request->input('station_id');
        $street = $request->input('street');
        $old_street = $request->input('old_street');
        $xiaoqi = $request->input('xiaoqi');
        $station_addr = DeliveryStation::find($station_id)->address;
        $addr = explode(" ",$station_addr);
        $deliveryareas = DSDeliveryArea::where('station_id',$station_id)->where('address','LIKE','%'." ".$old_street." "."%")->get();
        foreach ($deliveryareas as $da){
            $da->delete();
        }

        foreach ($xiaoqi as $x){
            $current_addr = $addr[0]." ".$addr[1]." ".$addr[2]." ".$street." ".$x;
            $new_delivery_area = new DSDeliveryArea;
            $new_delivery_area->station_id = $station_id;
            $new_delivery_area->address = $current_addr;
            $new_delivery_area->save();
        }
        return Response::json(['street'=>$street]);
    }
}
