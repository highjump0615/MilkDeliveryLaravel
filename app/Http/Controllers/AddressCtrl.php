<?php

namespace App\Http\Controllers;

use App\Model\DeliveryModel\DeliveryStation;
use App\Model\SystemModel\SysLog;
use Illuminate\Http\Request;
use App\Model\BasicModel\Address;
use App\Model\UserModel\Page;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Model\BasicModel\ProvinceData;
use App\Model\BasicModel\CityData;
use App\Model\BasicModel\DistrictData;
use App\Model\UserModel\User;

use Auth;
use Excel;

class AddressCtrl extends Controller
{

    public function show()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;

        $streets = Address::where('level', 4)->where('factory_id', $factory_id)->where('is_deleted', 0)->get();

        //Combine address list that has the same province, city distirct

        $addresses = array();
        foreach ($streets as $street) {
            $key = $street->province->name . ' ' . $street->city->name . ' ' . $street->district->name;
            $addresses[$key][] = $street;
        }

        $child = 'dizhiku';
        $parent = 'jichuxinxi';
        $current_page = 'dizhiku';
        $pages = Page::where('backend_type', User::USER_BACKEND_FACTORY)->where('parent_page', '0')->get();

        $provinces = ProvinceData::all();

        return view('gongchang.jichuxinxi.dizhiku', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'address_list' => $streets,
            'provinces' => $provinces,
            'addresses' => $addresses,
        ]);

    }

    public function store(Request $request)
    {

        if ($request->ajax()) {
            //address parameter validation check

            //register new address
            $province = $request->input('province');
            $city = $request->input('city');
            $district = $request->input('district');
            $street = $request->input('street');
            $xiaoqudata = $request->input('xiaoqu');

            $pid = $this->storeIfNotExist($province, 0);
            $cid = $this->storeIfNotExist($city, $pid);
            $did = $this->storeIfNotExist($district, $cid);
            $sid = $this->storeIfNotExist($street, $did);

            $xiaoqus = array_map('trim', multiexplode(array('，', ' ', ','), $xiaoqudata));

            foreach ($xiaoqus as $xiaoqu) {
                $xiaoqu = trim($xiaoqu);
                if ($xiaoqu) {
                    $this->storeIfNotExist($xiaoqu, $sid);
                }
            }

            return response()->json(['status' => 'success']);
        }
    }

    public function storeIfNotExist($addressName, $parentId)
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;

        $parent = null;

        if ($parentId > 0)
            $parent = Address::find($parentId);

        $addr = Address::where('parent_id', $parentId)
            ->where('name', $addressName)->where('factory_id', $factory_id)->where('is_active', Address::ADDRESS_ACTIVE)->get()->first();

        if ($addr == null) {
            $addr = new Address;

            $addr->name = $addressName;
            $addr->parent_id = $parentId;
            $addr->factory_id = $factory_id;
            if ($parentId == 0) {
                $addr->level = 1;
            } else {
                $addr->level = $parent->level + 1;
            }

            $addr->save();
        }
        return $addr->id;
    }

    public function findMyChildren($myId)
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;

        //get my child list
        $children = Address::where('parent_id', $myId)->where('factory_id', $factory_id)->get();

        return $children;
    }

    public function findMyAcitveChildren()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;

        $myid = $this->id;
        $children = Address::where('parent_id', $myid)->where('factory_id', $factory_id)->where('is_active', Address::ADDRESS_ACTIVE)->get();

        return $children;
    }

    public function delete_address(Request $request)
    {
        if ($request->ajax()) {

            $fuser = Auth::guard('gongchang')->user();
            $factory_id = $fuser->factory_id;

            $province = $request->input('province');
            $city = $request->input('city');
            $district = $request->input('district');
            $street = $request->input('street');
            $xiaoqudata = $request->input('xiaoqu');

            //$xiaoqus = array_map('trim', explode(',', $xiaoqudata));

            //get street id
            //Delete one row means delete the street and all xiaoqu in the street

            //get province id
            $po = Address::where('name', $province)->where('level', 1)->where('factory_id', $factory_id)->get()->first();
            if (!$po) {
                return response()->json(['status' => 'fail', 'message' => '没有省']);
            }
            $pid = $po->id;

            //get city id
            $co = Address::where('name', $city)->where('level', 2)->where('parent_id', $pid)->where('factory_id', $factory_id)->get()->first();
            if (!$co) {
                return response()->json(['status' => 'fail', 'message' => '没有市']);
            }
            $cid = $co->id;

            //get district id
            $do = Address::where('name', $district)->where('level', 3)->where('parent_id', $cid)->where('factory_id', $factory_id)->get()->first();
            if (!$do) {
                return response()->json(['status' => 'fail', 'message' => '没有区']);
            }
            $did = $do->id;

            //get street id
            $so = Address::where('name', $street)->where('level', 4)->where('parent_id', $did)->where('factory_id', $factory_id)->get()->first();
            if (!$so) {
                return response()->json(['status' => 'fail', 'message' => '没有大街']);
            }
            $sid = $so->id;

            //get all xiaoqu data which has parent_id= street id
            $xiaoqus = Address::where('parent_id', $sid)->where('factory_id', $factory_id)->get();

            foreach ($xiaoqus as $xiaoqu) {
//                $xiaoqu->setDelete();
                $xiaoqu->delete();
            }

            //delete one which has no child
            $recurseId = $sid;

            while ($recurseId != -1) {
                $recurseId = $this->deleteHasNoChild($recurseId);

                if ($recurseId == 0)
                    break;
            }
            return response()->json(['status' => 'success', 'message' => 'Delete Success']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Delete Failed']);
        }
    }

    public function deleteHasNoChild($id)
    {
        //get current id's children
        $children = $this->findMyChildren($id);
        if (count($children) == 0) {
            $pid = Address::find($id)->parent_id;
            Address::find($id)->setDelete();
            return $pid;
        } else {
            return -1;
        }
    }

    //Enable-Disable Street and Xiaoqus
    public function setflag(Request $request)
    {
        if ($request->ajax()) {

            $fuser = Auth::guard('gongchang')->user();
            $factory_id = $fuser->factory_id;

            $province = $request->input('province');
            $city = $request->input('city');
            $district = $request->input('district');
            $street = $request->input('street');
            $xiaoqudata = $request->input('xiaoqu');

            $use = $request->input('use');

            //get province id
            $pid = Address::where('name', $province)->where('level', 1)->where('factory_id', $factory_id)->get()->first()->id;

            //get city id
            $cid = Address::where('name', $city)->where('level', 2)->where('factory_id', $factory_id)->where('parent_id', $pid)->get()->first()->id;

            //get district id
            $did = Address::where('name', $district)->where('level', 3)->where('factory_id', $factory_id)->where('parent_id', $cid)->get()->first()->id;

            //get street id
            $sid = Address::where('name', $street)->where('level', 4)->where('factory_id', $factory_id)->where('parent_id', $did)->get()->first()->id;


            if ($use == "1") {
                //make disable
                $addr = Address::where('id', $sid)->where('factory_id', $factory_id)->get()->first();
                if ($addr) {
                    $addr->setDisable();
                    return response()->json(['status' => 'success', 'action' => 'disabled', 'message' => 'disabled']);
                } else {
                    return response()->json(['status' => 'fail', 'message' => '找不到地址']);
                }


            } else {
                //Make enable

                $addr = Address::where('id', $sid)->where('factory_id', $factory_id)->get()->first();

                if ($addr) {
                    $addr->setEnable();
                    return response()->json(['status' => 'success', 'action' => 'enabled', 'message' => 'enabled']);
                } else {
                    return response()->json(['status' => 'fail', 'message' => '找不到地址']);
                }

            }

        } else {
            return response()->json(['status' => 'failed', 'message' => 'Operation Failed']);
        }
    }

    //Update Address
    public function update(Request $request)
    {

        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;

        //compare with data-origin and real value, update

        $street_changed = false;

        $province = trim($request->input('province'));
        $city = trim($request->input('city'));
        $district = trim($request->input('district'));

        $province = Address::where('parent_id', 0)->where('name', $province)->where('factory_id', $factory_id)->get()->first();
        $city = $province->getSubAddressesWithNameAttribute($city)->first();
        $district = $city->getSubAddressesWithNameAttribute($district)->first();

        $origin_street = trim($request->input('origin_street'));
        $new_street = trim($request->input('street'));

        $street = $district->changeSubAddressName($origin_street, $new_street);

        $origin_xiaoqu = trim($request->input('origin_xiaoqu'));
        $origin_xiaoqus = array_map('trim', explode(',', $origin_xiaoqu));

        $new_xiaoqu = trim($request->input('xiaoqu'));
        $new_xiaoqus = array_map('trim', explode(',', $new_xiaoqu));

        $delete_xiaoqu = array_diff($origin_xiaoqus, $new_xiaoqus);
        $add_xiaoqu = array_diff($new_xiaoqus, $origin_xiaoqus);

        foreach ($delete_xiaoqu as $do_xiaoqu) {
            if ($do_xiaoqu) {
                $d_xiaoqu = $street->getSubAddressesWithNameAttribute($do_xiaoqu)->first();
                $d_xiaoqu->delete();
            }
        }

        foreach ($add_xiaoqu as $no_xiaoqu) {
            $this->storeIfNotExist($no_xiaoqu, $street->id);
        }

        return response()->json(['status' => 'success']);
    }

    public function chinese_str($str)
    {
        return chr(255) . chr(254) . mb_convert_encoding($str, 'UTF-16LE', 'UTF-8');
    }

    public function export(Request $request)
    {

        if ($request->ajax()) {
            $fuser = Auth::guard('gongchang')->user();
            $factory_id = $fuser->factory_id;

            $streets = Address::where('level', 4)->where('factory_id', $factory_id)->get();

            $rows = array();
            foreach ($streets as $street) {
                $province_name = $street->province->name;
                $city_name = $street->city->name;
                $district_name = $street->district->name;
                $street_name = $street->name;
                $xiaoqu_name = $street->sub_addresses_str;
                array_push($rows, array($province_name, $city_name, $district_name, $street_name, $xiaoqu_name));
            }

            Excel::create('addresslist', function ($excel) use ($rows) {

                $excel->sheet('Sheet1', function ($sheet) use ($rows) {

                    //$sheet->appendRow(array('序号', '省', '市', '区', '街道', '小区'));
                    foreach ($rows as $row) {
                        $sheet->appendRow($row);
                    }
                });

            })->store('xls', 'exports');

            // 添加系统日志
            $sysMgrCtrl = new SysManagerCtrl();
            $sysMgrCtrl->addSystemLog($fuser, '地址库管理', SysLog::SYSLOG_OPERATION_EXPORT);

            return response()->json(['status' => 'success', 'path' => 'http://' . $request->server('HTTP_HOST') . '/milk/public/exports/addresslist.xls']);
        }

    }

    /*
     * Get all cities in Province
     * in: Province_Name
     * out: All City in Province from Area Table
     */
    public function all_province_to_city(Request $request)
    {
        if ($request->ajax()) {

            $province_name = $request->input('province');

            $province = ProvinceData::where('name', $province_name)->get()->first();

            $city = $province->city;

            return response()->json(['status' => 'success', 'city' => $city]);
        }

    }

    /*
    * Get all districts in City
    * in: Province_Name and City_Name
    * out: All City in Province from Area Table
    */
    public function all_city_to_district(Request $request)
    {
        if ($request->ajax()) {

            $province_name = $request->input('province');
            $city_name = $request->input('city');

            $province = ProvinceData::where('name', $province_name)->get()->first();
            $province_code = $province->code;

            $city = CityData::where('name', $city_name)->where('provincecode', $province_code)->get()->first();

            $district = $city->district;
            return response()->json(['status' => 'success', 'district' => $district]);
        }
    }



    // province -> city names
    public function active_province_to_city(Request $request)
    {
        if ($request->ajax()) {

            $fid = $sid = null;

            $fuser = Auth::guard('gongchang')->user();
            if($fuser)
            {
                $fid = $fuser->factory_id;
            }
            else
            {
                $station_id = Auth::guard('naizhan')->user()->station_id;
                $suser = DeliveryStation::find($station_id);

                if($suser)
                {
                    $sid = $suser->id;
                    $station = DeliveryStation::find($sid);
                    $fid = $station->factory_id;
                } else {
                    return response()->json(['status'=>'fail']);
                }
            }

            $province = $request->input('province');

            $province = $this->addressObjFromName($province, $fid);

            if (!$province)
                return response()->json(['status' => 'fail']);

            $cities = $province->getSubActiveAddresses();

            $city_names = array();
            foreach ($cities as $city) {
                $city_names[] = $city->name;
            }

            return response()->json(['status' => 'success', 'city' => $city_names]);
        }

    }

    // province, city -> distirct names
    public function active_city_to_district(Request $request)
    {
        if ($request->ajax()) {


            $fid = $sid = null;

            $fuser = Auth::guard('gongchang')->user();
            if($fuser)
            {
                $fid = $fuser->factory_id;
            }
            else
            {
                $station_id = Auth::guard('naizhan')->user()->station_id;
                $suser = DeliveryStation::find($station_id);

                if($suser)
                {
                    $sid = $suser->id;
                    $station = DeliveryStation::find($sid);
                    $fid = $station->factory_id;
                } else {
                    return response()->json(['status'=>'fail']);
                }
            }

            $province = $request->input('province');
            $city = $request->input('city');

            $address = $province.' '.$city;

            $city = $this->addressObjFromName($address, $fid);
            if (!$city)
                return response()->json(['status' => 'fail']);

            $districts = $city->getSubActiveAddresses();

            $district_names = array();
            foreach ($districts as $district) {
                $district_names[] = $district->name;
            }

            return response()->json(['status' => 'success', 'district' => $district_names]);
        }

    }

   // province, city, district -> [street id, street_name]
    public function active_district_to_street(Request $request)
    {
        if ($request->ajax()) {

            $fid = $sid = null;

            $fuser = Auth::guard('gongchang')->user();
            if($fuser)
            {
                $fid = $fuser->factory_id;
            }
            else
            {
                $station_id = Auth::guard('naizhan')->user()->station_id;
                $suser = DeliveryStation::find($station_id);

                if($suser)
                {
                    $sid = $suser->id;
                    $station = DeliveryStation::find($sid);
                    $fid = $station->factory_id;
                } else {
                    return response()->json(['status'=>'fail']);
                }
            }

            $province = $request->input('province');
            $city = $request->input('city');
            $district = $request->input('district');

            $address = $province.' '.$city.' '.$district;

            $district = $this->addressObjFromName($address, $fid);

            if (!$district)
                return response()->json(['status' => 'fail']);

            $streets = $district->getSubActiveAddresses();

            $street_array = array();
            foreach ($streets as $street) {
                $street_array[] = [$street->id, $street->name];
            }

            return response()->json(['status' => 'success', 'streets' => $street_array]);
        }
    }

    //street_id -> [xiaoqu_id, xiaoqu_name] + current_street
    public function active_street_to_xiaoqu(Request $request)
    {
        if ($request->ajax()) {

            $street_id = $request->input('street_id');

            $street = Address::find($street_id);
            $street_name = $street->name;

            if ($street) {
                $xiaoqus = $street->getSubActiveAddresses();

                if ($xiaoqus->count() == 0)
                    return response()->json(['status' => 'fail']);

                $xiaoqus_data = [];
                foreach ($xiaoqus as $xiaoqu) {
                    $xiaoqus_data[] = [$xiaoqu->id, $xiaoqu->name];
                }

                return response()->json(['status' => 'success', 'xiaoqus' => $xiaoqus_data, 'current_street' => $street_name]);

                //return response()->json(['status'=>'success', 'streets'=> $streets]);
            } else {
                return response()->json(['status' => 'fail']);
            }
        }
    }

      /*
     * Get all Xiaoqus from Street
     * in: street_id and street_name
     * out: [[xiaoqu_id, xiaoqu_name]]
     *      + current_street
     * */
    public function exist_street_to_xiaoqu(Request $request)
    {
        $fid = $sid = null;

        $fuser = Auth::guard('gongchang')->user();
        if($fuser)
        {
            $fid = $fuser->factory_id;
        }
        else
        {
            $station_id = Auth::guard('naizhan')->user()->station_id;
            $suser = DeliveryStation::find($station_id);

            if($suser)
            {
                $sid = $suser->id;
                $station = DeliveryStation::find($sid);
                $fid = $station->factory_id;
            } else {
                return response()->json(['status'=>'fail']);
            }
        }

        if ($request->ajax()) {

            $street_name = $request->input('street_name');
            $street_id = $request->input('street_id');

            $street = Address::find($street_id);

            if ($street) {
                $xiaoqus = Address::where('parent_id', $street_id)
                    ->where('factory_id', $fid)
                    ->where('level', 5)
                    ->get();

                if ($xiaoqus->count() == 0)
                    return response()->json(['status' => 'fail']);

                $xiaoqus_data = [];
                foreach ($xiaoqus as $xiaoqu) {
                    $xiaoqus_data[] = [$xiaoqu->id, $xiaoqu->name];
                }

                return response()->json(['status' => 'success', 'xiaoqus' => $xiaoqus_data, 'current_street' => $street_name]);

                //return response()->json(['status'=>'success', 'streets'=> $streets]);
            } else {
                return response()->json(['status' => 'fail']);
            }
        }
    }


    public function addressObjFromName($address, $factory_id) {
        if($address == null || $address == "")
            return null;

        $addr = explode(" ",$address);

        $level = count($addr);

        if($level < 1)
            return null;

        $province_name = $addr[0];

        $province = Address::where('name', $province_name)->where('level', 1)
            ->where('factory_id', $factory_id)
            ->where('is_active', Address::ADDRESS_ACTIVE)
            ->where('is_deleted', 0)->get()->first();

        if($province == null)
            return null;

        if($level < 2)
            return $province;

        $city_name = $addr[1];

        $city = Address::where('name', $city_name)->where('level', 2)
            ->where('parent_id', $province->id)
            ->where('factory_id', $factory_id)
            ->where('is_active', Address::ADDRESS_ACTIVE)
            ->where('is_deleted', 0)->get()->first();


        if($city == null)
            return null;

        if($level < 3)
            return $city;

        $district_name = $addr[2];

        $district = Address::where('name', $district_name)->where('level', 3)
            ->where('parent_id', $city->id)
            ->where('factory_id', $factory_id)
            ->where('is_active', Address::ADDRESS_ACTIVE)
            ->where('is_deleted', 0)->get()->first();


        if($district == null)
            return null;

        if($level < 4)
            return $district;

        $street_name = $addr[3];

        $street = Address::where('name', $street_name)->where('level', 4)
            ->where('parent_id', $district->id)
            ->where('factory_id', $factory_id)
            ->where('is_active', Address::ADDRESS_ACTIVE)
            ->where('is_deleted', 0)->get()->first();

        if($street == null)
            return null;

        if($level < 5)
            return $street;

        $xiaoqi_name = $addr[4];

        $xiaoqi = Address::where('name', $xiaoqi_name)->where('level', 5)
            ->where('parent_id', $street->id)
            ->where('factory_id', $factory_id)
            ->where('is_active', Address::ADDRESS_ACTIVE)
            ->where('is_deleted', 0)->get()->first();

        return $xiaoqi;
    }


}
