<?php

namespace App\Http\Controllers;

use App\Model\DeliveryModel\DSDeliveryArea;
use App\Model\DeliveryModel\MilkMan;
use App\Model\DeliveryModel\MilkManDeliveryArea;

use App\Model\UserModel\Page;
use App\Model\UserModel\User;
use App\Model\SystemModel\SysLog;

use App\Model\BasicModel\Address;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Auth;
use DateTime;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MilkManCtrl extends Controller
{
    /**
     * 打开配送员管理页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPeisongyuanRegister(Request $request){

        $current_station_id = $this->getCurrentStationId();

        $child = 'peisongyuan';
        $parent = 'naizhan';
        $current_page = 'peisongyuan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        $dsdeliveryarea  = DSDeliveryArea::where('station_id',$current_station_id)->get();

        $street = array();
        $i = 0;
        foreach ($dsdeliveryarea as $da){
            $flag = 0;
            $cur_addr = explode(" ",$da->address);
            if($i == 0){
                $street[$i] = $cur_addr[3];
                $i++;
            }
            for($j = 0; $j < $i; $j++){
                if($street[$j] == $cur_addr[3]){
                    $flag = 1;
                }
            }
            if($flag == 0){
                $street[$i] = $cur_addr[3];
                $i++;
            }
        }

        $milkmans = MilkMan::where('station_id',$current_station_id)->where('is_active',1)->get();
        foreach ($milkmans as $mm){
            $milkman_areas = MilkManDeliveryArea::where('milkman_id',$mm->id)->get();
            $m_street = array();
            $j = 0;
            foreach ($milkman_areas as $ma){
                $flag = 0;
                $addr = explode(" ",$ma->address);
                if($j == 0){
                    $m_street[$j] = $addr[3];
                    $mm['street'] = $addr[3];
                    $j++;
                }
                for($k = 0; $k < $j; $k++){
                    if($m_street[$k] == $addr[3]){
                        $flag = 1;
                    }
                }
                if($flag == 0){
                    $m_street[$j] = $addr[3];
                    $mm['street'] .= ', '.$addr[3];
                    $j++;
                }
            }
            $i = 0;
            foreach ($milkman_areas as $ma){
                $i++;
                $addr = explode(" ",$ma->address);

                if($i == 1){
                    $mm['xiaoqi'] = $addr[4];
                }
                else
                    $mm['xiaoqi'] .= ', '.$addr[4];
            }
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_STATION, '配送员管理', SysLog::SYSLOG_OPERATION_VIEW);

        return view('naizhan.naizhan.peisongyuan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,

            'deliveryarea'=> $dsdeliveryarea,
            'street' => $street,
            'milkmans'=>$milkmans,
        ]);
    }

    public function getXiaoqi(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        $streets = $request->input('street');
        $street_info = array();
        foreach ($streets as $s){
            $delivery_area = DSDeliveryArea::where('station_id',$current_station_id)->where('address','LIKE','%'.$s.'%')->get();
            foreach ($delivery_area as $i=>$da){
                $addr = explode(" ",$da->address);
                $xiaoqi = $addr[4];
                $street_info[$s][$i] = $xiaoqi;
            }
        }
        return $street_info;
    }

    public function savePeisongyuan(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        $name = $request->input('name');
        $number = $request->input('number');
        $phone = $request->input('phone');
        $street = $request->input('street');
        $xiaoqi = $request->input('xiaoqi');

        $milkman = new MilkMan;
        $milkman->name = $name;
        $milkman->phone = $phone;
        $milkman->station_id = $current_station_id;
        $milkman->number = $number;
        $milkman->save();
        $milkman_id = $milkman->id;
        $i =0;
        foreach ($xiaoqi as $x){
            $i++;
            $milkman_delivery_area = new MilkManDeliveryArea;
            $milkman_delivery_area->milkman_id = $milkman_id;
            $milkman_delivery_area->address = DSDeliveryArea::where('station_id',$current_station_id)->where('address','LIKE','%'.$x.'%')->get()->first()->address;
            $milkman_delivery_area->order = $i;
            $milkman_delivery_area->save();
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_STATION, '配送员管理', SysLog::SYSLOG_OPERATION_ADD);

        return $milkman_id;
    }

    /**
     * 修改配送员信息
     * @param Request $request
     * @return mixed
     */
    public function updatePeisongyuan(Request $request){
        $nId = $request->input('milkman_id');
        $strName = $request->input('name');
        $strNumber = $request->input('number');
        $strPhone = $request->input('phone');

        $milkman = MilkMan::find($nId);
        $milkman->name = $strName;
        $milkman->phone = $strPhone;
        $milkman->number = $strNumber;

        $milkman->save();

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_STATION, '配送员管理', SysLog::SYSLOG_OPERATION_EDIT);

        return redirect()->route('peisongyuan_page');
    }

    public function deletePeisongyuan($peisongyuan){
        $milkman_delivery_areas = MilkManDeliveryArea::where('milkman_id',$peisongyuan)->get();
        foreach ($milkman_delivery_areas as $mda){
            $mda->delete();
        }

        $deletePeisongyuan = MilkMan::destroy($peisongyuan);
        return Response::json($deletePeisongyuan);
    }

    public function showFanwei($milkman_id){
        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        $child = 'peisongyuan';
        $parent = 'naizhan';
        $current_page = 'fanwei-chakan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();


        $station = Milkman::find($milkman_id)->station;


        $delivery_areas = DSDeliveryArea::where('station_id',$station->id)->get();

        $available_address = array();
        if($delivery_areas->first() != null){
            foreach ($delivery_areas as $da){
                if($da->address != null){
                    $xiaoqu = Address::addressObjFromName($da->address, $station->factory->id);

                    if($xiaoqu) {
                        $available_address[$xiaoqu->parent_id][0] = $xiaoqu->street->name;
                        $available_address[$xiaoqu->parent_id][1][$xiaoqu->id] = $xiaoqu->name;
                    }
                }
            }
        }

        $milkman_delivery_area = MilkManDeliveryArea::where('milkman_id',$milkman_id)->orderby('order')->get();

        $area_address = array();
        
        if($milkman_delivery_area->first()){
            foreach ($milkman_delivery_area as $ma){
                $xiaoqu = Address::addressObjFromName($ma->address, $station->factory->id);
                if($xiaoqu) {
                    $area_address[$xiaoqu->parent_id][0] = $xiaoqu->street->name;
                    $area_address[$xiaoqu->parent_id][1][$xiaoqu->id] = $xiaoqu->name;
                }
            }
        }

        return view('naizhan.naizhan.peisongyuan.fanwei-chakan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'milkman_id'=>$milkman_id,
            'current_page' => $current_page,
            'area_address' => $area_address,
            'available_address' => $available_address,
        ]);
    }

    public function addDeliveryArea(Request $request) {
        $milkman_id = $request->input('milkman_id');
        $street_address_id = $request->input('street_id_to_add');

        $addr = Address::find($street_address_id);

        $street_addr = $addr->full_address_name;

        $da = MilkManDeliveryArea::where('milkman_id', $milkman_id)
            ->where('address', 'LIKE', $street_addr.' %')->get()->first();

        if(!$da) {
            $xiaoqus = $addr->getSubAddresses();
            foreach ($xiaoqus as $xiaoqu) {
                $da = new MilkManDeliveryArea();
                $da->milkman_id = $milkman_id;
                $da->address = $xiaoqu->full_address_name;
                $da->save();
            }
        }

        return redirect()->back();
    }

    public function deletePeisongyuanArea(Request $request){
        $milkman_id = $request->input('milkman_id');
        $street_id = $request->input('street_id');

        $street = Address::find($street_id);
        $district_name = $street->district->name;
        $city_name = $street->city->name;
        $province_name = $street->province->name;


        $milkman_areas = MilkManDeliveryArea::where('milkman_id',$milkman_id)
            ->where('address','LIKE',$province_name.' '.$city_name.' '.$district_name.' '.$street->name.'%')->get();
        foreach ($milkman_areas as $ma){
            $ma->delete();
        }

        return Response::json(['status'=>'success']);
    }

    public function modifyPeisongyuanArea(Request $request){
        $milkman_id = $request->input('milkman_id');
        $street_id = $request->input('street_id_to_change');

        $xiaoqus = $request->input('to');

        //Delete pre-exist delivery areas
        $street = Address::find($street_id);
        $street_name = $street->name;
        $district_name = $street->district->name;
        $city_name = $street->city->name;
        $province_name = $street->province->name;

        $milkman_areas = MilkManDeliveryArea::where('milkman_id',$milkman_id)
            ->where('address','LIKE',$province_name.' '.$city_name.' '.$district_name. ' '. $street_name. '%')->get();
        foreach ($milkman_areas as $ma){
            $ma->delete();
        }

        $i = 0;
        foreach ($xiaoqus as $xid){
            $i++;
            $this->make_delivery_area_for_xiaoqu($milkman_id, $xid, $i);
        }

        return response()->json(['status'=>'success']);
    }

    //Make new delivery area for xiaoqu with station
    private function make_delivery_area_for_xiaoqu($milkman_id, $xid, $order)
    {
        $xiaoqu = Address::find($xid);
        if(!$xiaoqu)
            return false;

        $address = $xiaoqu->full_address_name;

        $ma = new MilkmanDeliveryArea;

        $ma->milkman_id = $milkman_id;
        $ma->address = $address;
        $ma->order = $order;
        $ma->save();

    }

    public function sortPeisongyuanArea(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        $milkman = $request->input('milkman_id');
        $street = $request->input('street');
        $xiaoqi = $request->input('xiaoqi');
        $milkman_area = MilkManDeliveryArea::where('milkman_id',$milkman)->where('address','LIKE','%'.$street.'%')->get();
        foreach ($milkman_area as $ma){
            $ma->delete();
        }
        $i = 0;
        foreach ($xiaoqi as $x){
            $i++;
            $milkman_delivery_area = new MilkManDeliveryArea;
            $milkman_delivery_area->milkman_id = $milkman;
            $milkman_delivery_area->address = DSDeliveryArea::where('station_id',$current_station_id)->where('address','LIKE','%'.$street." ".$x.'%')->get()->first()->address;
            $milkman_delivery_area->order = $i;
            $milkman_delivery_area->save();
        }
        return Response::json(['street'=>$street]);
    }
    //
}
