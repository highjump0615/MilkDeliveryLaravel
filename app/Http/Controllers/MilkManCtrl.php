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

        // 获取奶站的配送范围
        $dsdeliveryarea  = DSDeliveryArea::where('station_id',$current_station_id)->get();

        $streets = array();

        // 遍历所有配送范围、提取街道信息
        foreach ($dsdeliveryarea as $da){

            // 获取该小区的街道
            $street = $da->village->parent;

            // 判断是否重复
            $bExist = false;
            foreach ($streets as $st) {
                if ($street->id == $st->id) {
                    $bExist = true;
                    break;
                }
            }

            // 已添加，跳过
            if ($bExist) {
                continue;
            }

            // 添加到街道数组
            $streets[] = $street;
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
            'streets' => $streets,
            'milkmans'=>$milkmans,
        ]);
    }

    /**
     * 奶站配送范围的小区信息
     * @param Request $request
     * @return array
     */
    public function getXiaoqi(Request $request){
        $nStationId = $this->getCurrentStationId();
        $nStreetId = $request->input('street_id');

        // 获取该奶站的配送范围（小区）
        $deliveryAreas = DSDeliveryArea::where('station_id', $nStationId)->get();

        $deliveryAreasResult = array();
        foreach ($deliveryAreas as $da){
            // 如果该范围小区不属于目标街道，跳过
            if ($da->village->parent->id != $nStreetId) {
                continue;
            }

            $deliveryAreasResult[] = [$da->id, $da->village->name];
        }

        return response()->json([
            'status' => 'success',
            'deliveryArea' => $deliveryAreasResult,
            'streetId' => $nStreetId,
        ]);
    }

    /**
     * 添加配送员信息
     * @param Request $request
     * @return mixed
     */
    public function savePeisongyuan(Request $request){
        $current_station_id = $this->getCurrentStationId();

        $name = $request->input('name');
        $number = $request->input('number');
        $phone = $request->input('phone');
        $xiaoqi = $request->input('xiaoqi');

        // 添加配送员
        $milkman = new MilkMan;
        $milkman->name = $name;
        $milkman->phone = $phone;
        $milkman->station_id = $current_station_id;
        $milkman->number = $number;
        $milkman->save();

        $milkman_id = $milkman->id;

        // 获取奶站配送范围
        $deliveryAreas = DSDeliveryArea::whereIn('id', $xiaoqi)->get(['id', 'address']);

        // 添加配送员配送范围
        $i = 0;
        foreach ($xiaoqi as $x){
            $i++;
            $milkman_delivery_area = new MilkManDeliveryArea;
            $milkman_delivery_area->milkman_id = $milkman_id;

            // 获取该小区的全名
            foreach ($deliveryAreas as $da) {
                if ($da->id == $x) {
                    $milkman_delivery_area->address = $da->address;
                    break;
                }
            }

            $milkman_delivery_area->deliveryarea_id = $x;
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
        $strNumber = $request->input('idnumber');
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

    /**
     * 显示配送范围页面
     * @param $milkman_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showFanwei($milkman_id){

        $child = 'peisongyuan';
        $parent = 'naizhan';
        $current_page = 'fanwei-chakan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        // 获取改配送员的所属奶站
        $station = Milkman::find($milkman_id)->station;

        //
        // 获取该奶站的配送范围
        //
        $delivery_areas = $station->delivery_area;

        $available_address = array();
        foreach ($delivery_areas as $da){
            $xiaoqu = $da->village;

            if($xiaoqu) {
                $available_address[$xiaoqu->parent_id][0] = $xiaoqu->street->name;
                $available_address[$xiaoqu->parent_id][1][$da->id] = $xiaoqu->name;
            }
        }

        //
        // 获取该配送员的配送范围
        //
        $milkman_delivery_area = MilkManDeliveryArea::where('milkman_id',$milkman_id)->orderby('order')->get();

        $area_address = array();
        foreach ($milkman_delivery_area as $ma){
            $deliveryArea = $ma->deliveryarea;
            $xiaoqu = $deliveryArea->village;

            if($xiaoqu) {
                $area_address[$xiaoqu->parent_id][0] = $xiaoqu->street->name;
                $area_address[$xiaoqu->parent_id][1][$deliveryArea->id] = $xiaoqu->name;
            }
        }

        return view('naizhan.naizhan.peisongyuan.fanwei-chakan', [
            // 页面信息
            'pages'             => $pages,
            'child'             => $child,
            'parent'            => $parent,
            'current_page'      => $current_page,

            // 数据
            'milkman_id'        => $milkman_id,
            'area_address'      => $area_address,
            'available_address' => $available_address,
        ]);
    }

    public function addDeliveryArea(Request $request) {
        $milkman_id = $request->input('milkman_id');
        $street_address_id = $request->input('street_id_to_add');

        $addr = Address::find($street_address_id);

        $street_addr = $addr->full_address_name;

        $da = MilkManDeliveryArea::where('milkman_id', $milkman_id)
            ->where('address', 'LIKE', $street_addr.' %')
            ->first();

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

    /**
     * 修改配送员配送范围
     * @param Request $request
     * @return mixed
     */
    public function modifyPeisongyuanArea(Request $request){
        $milkman_id = $request->input('milkman_id');
        $street_id = $request->input('street_id_to_change');

        // DSDeliveryArea id 数组
        $xiaoqus = $request->input('to');

        //Delete pre-exist delivery areas
        $street = Address::find($street_id);
        $street_name = $street->name;
        $district_name = $street->district->name;
        $city_name = $street->city->name;
        $province_name = $street->province->name;

        MilkManDeliveryArea::where('milkman_id',$milkman_id)
            ->where('address','LIKE',$province_name.' '.$city_name.' '.$district_name. ' '. $street_name. '%')
            ->delete();

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
        $deliveryArea = DSDeliveryArea::find($xid);
        if (!$deliveryArea)
            return false;

        $ma = new MilkmanDeliveryArea;

        $ma->milkman_id = $milkman_id;
        $ma->address = $deliveryArea->village->getFullName();
        $ma->deliveryarea_id = $deliveryArea->id;
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
            $milkman_delivery_area->address = DSDeliveryArea::where('station_id',$current_station_id)
                ->where('address','LIKE','%'.$street." ".$x.'%')
                ->first()
                ->address;
            $milkman_delivery_area->order = $i;
            $milkman_delivery_area->save();
        }
        return Response::json(['street'=>$street]);
    }
    //
}
