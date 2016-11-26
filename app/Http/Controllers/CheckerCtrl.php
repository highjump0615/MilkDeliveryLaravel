<?php

namespace App\Http\Controllers;

use App\Model\UserModel\Page;
use App\Model\OrderModel\OrderCheckers;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use App\Model\FactoryModel\Factory;
use App\Model\DeliveryModel\DeliveryStation;
use Illuminate\Http\Request;
use Auth;
use DateTime;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class CheckerCtrl extends Controller
{
    protected function createValidator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'phone' => 'required|max:255|unique:ordercheckers',
        ]);
    }

    protected function updateValidator(array $data, $userid)
    {
        return Validator::make($data, [
            'phone' => 'required|max:255|unique:ordercheckers,phone,'.$userid,
            'name' => 'required|max:255',
        ]);
    }



    public function showCheckerPage(Request $request, $station_id = null) {
        $child = 'zhengdingyuan';
        $parent = 'jichuxinxi';
        $current_page = 'zhengdingyuan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        $factory_id = Auth::guard('gongchang')->user()->factory_id;
        $factory = Factory::find($factory_id);

        $stations = DeliveryStation::where('factory_id',$factory_id)->where('is_deleted',0)->where('status',DeliveryStation::DELIVERY_STATION_STATUS_ACTIVE)->get();
        if($station_id == null) {
            $checkers = array();
            foreach($factory->ordercheckers as $c) {
                array_push($checkers, $c);
            }

            $stations = DeliveryStation::where('factory_id',$factory_id)->where('is_deleted',0)->where('status',DeliveryStation::DELIVERY_STATION_STATUS_ACTIVE)->get();
            foreach ($stations as $station) {
                $orderchecker_list = OrderCheckers::where('station_id', $station->id)->where('is_active', 1)->get();

                foreach($orderchecker_list as $c)
                {
                    array_push($checkers, $c);
                }
            }
        }else {
            $station = DeliveryStation::find($station_id);
            $checkers = $station->order_checkers;
        }

        return view('gongchang.jichuxinxi.zhengdingyuan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations' => $stations,
            'checkers' => $checkers,
        ]);
    }

    public function getChecker(Request $request, $id) {
        return OrderCheckers::find($id);
    }

    public function addChecker(Request $request) {
        $validator = $this->createValidator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }
        $name = $request->input('name');
        $phone = $request->input('phone');
        $station_id = $request->input('station');

        $factory_id = Auth::guard('gongchang')->user()->factory_id;

        $checker = new OrderCheckers;

        $checker->name = $name;
        $checker->phone = $phone;
        if($station_id == -1) {
            $checker->or_factory_id = $factory_id;
            $checker->station_id = null;
        } else {
            $checker->station_id = $station_id;
            $checker->or_factory_id = null;
        }
        $checker->is_active = 1;

        $checker->save();

        $checker->number = OrderCheckers::NUMBER_PREFIX.str_pad($checker->id, OrderCheckers::NUMBER_NUMBERS, '0', STR_PAD_LEFT);
        $checker->save();

        return Redirect::to('/gongchang/jichuxinxi/zhengdingyuan');
    }

    public function updateChecker(Request $request, $id) {
        $name = $request->input('name');
        $phone = $request->input('phone');
        $station_id = $request->input('station');
        $number = $request->input('number');

        $factory_id = Auth::guard('gongchang')->user()->factory_id;

        $checker = OrderCheckers::findOrFail($id);
        $checker->name = $name;
        $checker->phone = $phone;
        if($station_id == -1) {
            $checker->or_factory_id = $factory_id;
            $checker->station_id = null;
        } else {
            $checker->station_id = $station_id;
            $checker->or_factory_id = null;
        }
        $checker->number = $number;

        $checker->save();

        return $checker;
    }

    public function deleteChecker(Request $request, $id) {
        $checker = OrderCheckers::findOrFail($id);
        $checker->is_active = 0;

        $checker->save();

        return response()->json(['success'=>true]);
    }
}