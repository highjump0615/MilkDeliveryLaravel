<?php

namespace App\Http\Controllers;

use App\Model\FactoryModel\FactoryBottleType;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\UserModel\Page;
use App\Http\Controllers\Controller;
use App\Model\DeliveryModel\DeliveryType;
use App\Model\DeliveryModel\FactoryDeliveryType;
use App\Model\FactoryModel\FactoryDeliveryTime;
use App\Model\FactoryModel\Factory;
use App\Model\FactoryModel\FactoryOrderType;
use App\Model\FactoryModel\FactoryBoxType;
use App\Model\OrderModel\OrderType;
use Auth;

class ProductSettingsCtrl extends Controller
{
    public function show_product_settings_page()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;

        $child = 'shangpin';
        $parent = 'jichuxinxi';
        $current_page = 'shangpincanshushezhi';
        $pages = Page::where('backend_type','2')->where('parent_page', '0')->get();


        //delivery type
        $fdts= FactoryDeliveryType::where('factory_id', $fuser->factory_id)->get();

        $deliveryTypes = DeliveryType::all();

        if($fdts->count() == 0)
        {
            $fdts = array();
            foreach($deliveryTypes as $dt)
            {
                $fdt = new FactoryDeliveryType;
                $fdt->delivery_type = $dt->id;
                $fdt->factory_id = $fuser->factory_id;
                $fdt->is_active = true;
                $fdt->save();
                array_push($fdts, $fdt);
            }
        }

        //delivery time
        $factory = Factory::findOrFail($factory_id);
        $dt = $factory->delivery_time;

        if($dt == null) {
            $dt = new FactoryDeliveryTime;
            $dt->factory_id = $factory_id;
            $dt->morning_start_at = "6:00";
            $dt->morning_end_at = "12:00";
            $dt->afternoon_start_at = "14:00";
            $dt->afternoon_end_at = "20:00";

            $dt->save();
        }

        //order type
        $order_types = OrderType::all();

        foreach($order_types as $ot) {
            $fot = FactoryOrderType::where('factory_id', $factory_id)->where('order_type', $ot->id)->get()->first();

            if($fot == null) {
                $ot['active'] = false;
            } else {
                $ot['active'] = true;
            }
        }
        //box type
        $box_types = $factory->active_box_types;

        //bottle type
        $bottle_types = $factory->bottle_types;
        $gap_day = $factory->gap_day;

        return view('gongchang.jichuxinxi.shangpin.shangpincanshushezhi',[
            'pages'=>$pages,
            'child'=>$child,
            'parent'=>$parent,
            'current_page'=>$current_page,
            'fdts' => $fdts,
            'delivery_time' => $dt,
            'order_types' => $order_types,
            'box_types' => $box_types,
            'bottle_types' => $bottle_types,
            'gap_day'=>$gap_day,
        ]);
    }

    public function set_use_delivery_type(Request $request)
    {
        $fuser = Auth::guard('gongchang')->user();
        if($request->ajax())
        {
            $fdts_id = $request->input('fdts_id');
            $action = $request->input('action');

            $fdts = FactoryDeliveryType::where('factory_id', $fuser->factory_id)->where('delivery_type', $fdts_id)->get()->first();

            if($action == 'unuse')
            {
                $fdts->is_active = 0;
                $fdts->save();
            } else
            {
                $fdts->is_active = 1;
                $fdts->save();
            }
            return response()->json(['status'=>'success']);
        }
    }

    public function set_delivery_time(Request $request)
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory = Factory::findOrFail($fuser->factory_id);

        if($request->ajax())
        {
            $ms = $request->input('morning_start_at');
            $me = $request->input('morning_end_at');
            $as = $request->input('afternoon_start_at');
            $ae = $request->input('afternoon_end_at');

            $dt = FactoryDeliveryTime::where('factory_id', $fuser->factory_id)->get()->first();

            if($dt == null) {
                $dt = new FactoryDeliveryTime;
                $dt->factory_id = $factory->id;
            }

            $dt->morning_start_at = $ms;
            $dt->morning_end_at = $me;
            $dt->afternoon_start_at = $as;
            $dt->afternoon_end_at = $ae;
            $dt->save();

            return response()->json(['success'=>true]);
        }
    }

    public function add_order_type(Request $request)
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory = Factory::findOrFail($fuser->factory_id);

        if($request->ajax())
        {
            $order_type = $request->input('order_type');
            $fot = FactoryOrderType::where('factory_id', $factory->id)->where('order_type', $order_type)->get()->first();

            if($fot == null) {
                $fot = new FactoryOrderType;
                $fot->factory_id = $factory->id;
                $fot->order_type = $order_type;
                $fot->save();
            }

            $o = OrderType::find($order_type);

            return $o;
        }
    }

    public function delete_order_type(Request $request, $id)
    {
        $fuser = Auth::guard('gongchang')->user();

        if($request->ajax())
        {
            $fot = FactoryOrderType::where('factory_id', $fuser->factory_id)->where('order_type', $id)->get()->first();

            if($fot) {
                $fot->delete();
            }

            return response()->json(['success'=>true]);
        }
    }

    public function generate_bottle_type_number($fid)
    {
        //get bottle type last number
        //FactoryBottleType::FACTORY_BOTTLE_TYPE_PREFIX
        $bt = FactoryBottleType::where('factory_id', $fid)->orderBy('id', 'desc')->get()->first();
        if($bt)
        {
            $sub = intval($bt->id)+1;
            return "F".$fid."_".FactoryBottleType::FACTORY_BOTTLE_TYPE_PREFIX.$sub;
        } else
            return "F".$fid."_".FactoryBottleType::FACTORY_BOTTLE_TYPE_PREFIX."0";
    }

    public function add_bottle_type(Request $request)
    {
        $fuser = Auth::guard('gongchang')->user();
        $fid = $fuser->factory_id;

        if($request->ajax())
        {
            $name = $request->input('bottle_type_name');
            $number = $this->generate_bottle_type_number($fid);

            $bottle = new FactoryBottleType;
            $bottle->factory_id = $fuser->factory_id;
            $bottle->name = $name;
            $bottle->number = $number;
            $bottle->is_deleted = false;
            $bottle->save();

            return $bottle;
        }
    }

    public function delete_bottle_type(Request $request, $id)
    {
        $fuser = Auth::guard('gongchang')->user();

        if($request->ajax())
        {
            $b = FactoryBottleType::find($id);

            if($b) {
                $b->is_deleted = true;
                $b->save();
                return response()->json(['success'=>true]);
            } else {
                return response()->json(['success'=>false]);
            }


        }
    }

    public function generate_box_type_number($fid)
    {
        //get bottle type last number
        //FactoryBottleType::FACTORY_BOTTLE_TYPE_PREFIX
        $bt = FactoryBoxType::where('factory_id', $fid)->orderBy('id', 'desc')->get()->first();

        if($bt)
        {
            $sub = intval($bt->id)+1;
            return "F".$fid."_".FactoryBoxType::FACTORY_BOX_TYPE_PREFIX.$sub;
        } else
            return "F".$fid."_".FactoryBoxType::FACTORY_BOX_TYPE_PREFIX."0";
    }

    public function add_box_type(Request $request)
    {
        $fuser = Auth::guard('gongchang')->user();
        $fid = $fuser->factory_id;

        if($request->ajax())
        {
            $box_spec_name = $request->input('box_spec_name');
            $box_spec_number = $this->generate_box_type_number($fid);

            $b = new FactoryBoxType;
            $b->factory_id = $fuser->factory_id;
            $b->name = $box_spec_name;
            $b->number = $box_spec_number;
            $b->is_deleted = false;
            $b->save();

            return $b;
        }
    }

    public function delete_box_type(Request $request, $id)
    {
        $fuser = Auth::guard('gongchang')->user();

        if($request->ajax())
        {
            $b = FactoryBoxType::find($id);

            if($b) {
                $b->is_deleted = true;
                $b->save();
                return response()->json(['success'=>true]);
            } else {
                return response()->json(['success'=>false]);
            }


        }
    }


    public function set_gap_day(Request $request)
    {
        $fuser = Auth::guard('gongchang')->user();
        $fid = $fuser->factory_id;

        if($request->ajax())
        {
            $gap_day = $request->input('gap_day');

            if($gap_day) {

                $factory = Factory::find($fid);
                $factory->gap_day = $gap_day;
                $factory->save();
                return response()->json(['status'=>'success']);
            } else {
                return response()->json(['status'=>'fail']);
            }


        }
    }
}
