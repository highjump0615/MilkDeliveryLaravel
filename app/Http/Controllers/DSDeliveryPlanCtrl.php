<?php

namespace App\Http\Controllers;

use App\Model\BasicModel\Customer;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\DSDeliveryArea;
use App\Model\DeliveryModel\DSDeliveryPlan;
use App\Model\DeliveryModel\DSProductionPlan;
use App\Model\DeliveryModel\MilkMan;
use App\Model\DeliveryModel\MilkmanBottleRefund;
use App\Model\DeliveryModel\MilkManDeliveryPlan;
use App\Model\BasicModel\ProvinceData;
use App\Model\FactoryModel\FactoryBottleType;
use App\Model\NotificationModel\DSNotification;
use App\Model\OrderModel\OrderProduct;
use App\Model\OrderModel\SelfOrder;
use App\Model\OrderModel\SelfOrderProduct;
use App\Model\ProductModel\Product;
use App\Model\ProductModel\ProductPrice;
use Illuminate\Http\Request;
use App\Model\UserModel\Page;
use DateTime;
use DateTimeZone;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Model\OrderModel\Order;
use App\Http\Requests;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class DSDeliveryPlanCtrl extends Controller
{
    public function showPeisongguanli(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->id;
        $child = 'peisongguanli';
        $parent = 'shengchan';
        $current_page = 'peisongguanli';
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');
        $currentDate->add(\DateInterval::createFromDateString('yesterday'));
        $currentDate_str = $currentDate->format('Y-m-d');
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        $DSProduction_plans = DSProductionPlan::where('station_id',$current_station_id)->where('produce_end_at',$currentDate_str)->orderby('product_id')->get();
        $is_distributed = 0;
        foreach($DSProduction_plans as $dp){
            $changed_plan_count_per_product = 0;
            $changed_counts = MilkManDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->wherebetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT])->
            where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)->get();
            foreach($changed_counts as $cc){
                if($cc->order_product->product->id == $dp->product_id){
                    $changed_plan_count_per_product += $cc->changed_plan_count;
                }
            }
            $dp["changed_amount"] = $changed_plan_count_per_product - $dp->order_count;
            $delivery_plans = DSDeliveryPlan::where('product_id',$dp->product_id)->where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->get()->first();
            if($delivery_plans != null){
                $is_distributed = 1;
                $dp["dp_retail"]=$delivery_plans->retail - $dp->retail;
                $dp["dp_test_drink"]=$delivery_plans->test_drink - $dp->test_drink;
                $dp["dp_group_sale"]=$delivery_plans->group_sale - $dp->group_sale;
                $dp["dp_channel_sale"]=$delivery_plans->channel_sale - $dp->channel_sale;
            }
        }
        $changed_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)->get();
        return view('naizhan.shengchan.peisongguanli',[
            'pages'=>$pages,
            'child'=>$child,
            'parent'=>$parent,
            'current_page'=>$current_page,
            'dsproduction_plans'=>$DSProduction_plans,
            'is_distributed'=>$is_distributed,
            'changed_plans'=>$changed_plans,
        ]);
    }

    public function save_distribution(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $currentDate_str = $currentDate->format('Y-m-d');
        $product_id = $request->input('product_id');
        $retail = $request->input('retail');
        $test_drink = $request->input('test_drink');
        $group_sale = $request->input('group_sale');
        $channel_sale = $request->input('channel_sale');

        $delivery_distribution = new DSDeliveryPlan;
        $delivery_distribution->station_id = $current_station_id;
        $delivery_distribution->deliver_at = $currentDate_str;
        $delivery_distribution->product_id = $product_id;
        $delivery_distribution->retail = $retail;
        $delivery_distribution->test_drink = $test_drink;
        $delivery_distribution->group_sale = $group_sale;
        $delivery_distribution->channel_sale = $channel_sale;
        $delivery_distribution->save();

        return count($delivery_distribution);
    }

    public function save_changed_distribution(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');

        $delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)->where('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED)->get();
        foreach($delivery_plans as $dp){
            if($dp->plan_count == $dp->changed_plan_count){
                $dp->delivery_count = $dp->plan_count;
                $dp->save();
            }
        }

        $rest_amount = 0;
        $table_info = json_decode($request->getContent(),true);
        foreach ($table_info as $ti){
            $changed_delivery = MilkManDeliveryPlan::find($ti['id']);
            $changed_delivery->delivery_count = $ti['delivery_count'];
            $changed_delivery->comment = $ti['comment'];
            $changed_delivery->save();
            $rest_amount += ($changed_delivery->delivery_count - $changed_delivery->plan_count)*$changed_delivery->product_price;
        }
        $station = DeliveryStation::find($current_station_id);
        $station->business_credit_balance = $station->business_credit_balance + $rest_amount;
        $station->save();
        if($rest_amount > 0){
            $notification = new DSNotification();
            $notification->sendToStationNotification($current_station_id,7,"回报金钱","您本次订单计划多余扣除货款".$rest_amount."元已退回您的自营账户。");
        }

        return Response::json();
    }

    public function showPeisongliebiao(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');
        $child = 'peisongguanli';
        $parent = 'shengchan';
        $current_page = 'peisongliebiao';
        $pages = Page::where('backend_type',Page::NAIZHAN)->where('parent_page', '0')->get();

        $delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->
        whereBetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED])->get()->groupBy(function($sort){return $sort->type;});

        $res = array();
        foreach($delivery_plans as $o=>$dps_by_type) {
            if($o == 1){
                $regular_delivers = $dps_by_type->groupBy(function($sort){return $sort->order_id;});
                foreach ($regular_delivers as $r=>$by_order_id){
                    $res[$r] = Order::find($r);
                    $products = array();
                    $is_changed = 0;
                    $delivery_type = 1;
                    foreach($by_order_id as $dp) {
                        $name = $dp->order_product->product->name;
                        $count = $dp->delivery_count;
                        $products[] = $name.'*'.$count;
                        if($dp->plan_count != $dp->changed_plan_count)
                            $is_changed = 1;
                        $delivery_type = $dp->type;
                    }
                    $res[$r]['product'] = implode(',', $products);
                    $res[$r]['changed'] = $is_changed;
                    $res[$r]['delivery_type'] = $delivery_type;
                }
            }
            else{
                $regular_delivers = $dps_by_type->groupBy(function($sort){return $sort->order_id;});
                foreach ($regular_delivers as $r=>$by_order_id){
                    $res[$r] = SelfOrder::find($r);
                    $products = array();
                    $is_changed = 0;
                    $delivery_type = 1;
                    foreach($by_order_id as $dp) {
                        $name = $dp->order_product->product->name;
                        $count = $dp->delivery_count;
                        $products[] = $name.'*'.$count;
                        $delivery_type = $dp->type;
                    }
                    $res[$r]['product'] = implode(',', $products);
                    $res[$r]['changed'] = $is_changed;
                    $res[$r]['delivery_type'] = $delivery_type;
                }
            }

        }

        return view('naizhan.shengchan.peisongliebiao',[
            'pages'=>$pages,
            'child'=>$child,
            'parent'=>$parent,
            'current_page'=>$current_page,
            'delivery_plans'=>$res,
        ]);
    }

    public function showZiyingdingdan(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');
        $child = 'peisongguanli';
        $parent = 'shengchan';
        $current_page = 'ziyingdingdan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        $delivery_plans = DSDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->get();
        $total_remain_product_count = 0;
        foreach ($delivery_plans as $dp){
            $milkman_planed_count = 0;
            $milkman_delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->where('type',"!=", 1)->get();
            foreach($milkman_delivery_plans as $md) {
                if($md->order_product->product->id == $dp->product_id){
                    $milkman_planed_count += $md->delivery_count;
                }
            }
            $dp['rest_amount'] = $dp->test_drink + $dp->group_sale + $dp->channel_sale - $milkman_planed_count;
            $total_remain_product_count += $dp['rest_amount'];
        }

        if($total_remain_product_count == 0){
            return redirect()->route('naizhan_peisongliebiao')->with('page_status','你已经添加自营配送任务!');
        }
        $milk_mans = MilkMan::where('station_id',$current_station_id)->get();
        if($delivery_plans->first() == null){
            return redirect()->route('naizhan_peisongliebiao')->with('page_status','没有自营计划量!');
        }

        if($milk_mans->first() == null){
            return redirect()->route('naizhan_peisongliebiao')->with('page_status','没有配送员!');
        }
        $milkman_delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->where('type',"!=", 1)->get()->groupBy(function($sort){return $sort->order_id;});
        $res = array();
        foreach($milkman_delivery_plans as $o=>$dps_by_order) {
            $res[$o] = SelfOrder::find($o);
            $products = array();
            $is_changed = 0;
            $delivery_type = 1;
            $milk_man = '';
            foreach($dps_by_order as $dp) {
                $name = $dp->order_product->product->name;
                $count = $dp->delivery_count;
                $products[] = $name.'*'.$count;
                if($dp->plan_count != $dp->changed_plan_count)
                    $is_changed = 1;
                $delivery_type = $dp->type;
                $milk_man = $dp->milkman->name;
            }
            $res[$o]['product'] = implode(',', $products);
            $res[$o]['changed'] = $is_changed;
            $res[$o]['delivery_type'] = $delivery_type;
            $res[$o]['milkman_name'] = $milk_man;
        }
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

        $current_district = DeliveryStation::find($current_station_id)->address;
        $current_district = explode(" ",$current_district);
        $show_district = $current_district[0].$current_district[1].$current_district[2];
        $addr_district = $current_district[0].' '.$current_district[1].' '.$current_district[2];
        $province = ProvinceData::all();
        return view('naizhan.shengchan.ziyingdingdan',[
            'pages'=>$pages,
            'child'=>$child,
            'parent'=>$parent,
            'current_page'=>$current_page,
            'delivery_plans'=>$delivery_plans,
            'streets'=>$street,
            'milk_man'=>$milk_mans,
            'current_district'=>$show_district,
            'addr_district'=>$addr_district,
            'province'=>$province,
            'milkman_delivery_plans'=>$res,
        ]);
    }

    public function getXiaoquName(Request $request){
        if ($request->ajax()) {
            $street_name = $request->input('street_name');
            $current_station_id = Auth::guard('naizhan')->user()->id;
            $dsdeliveryarea  = DSDeliveryArea::where('station_id',$current_station_id)->get();

            $xiaoqu = array();
            $i = 0;
            foreach ($dsdeliveryarea as $da){
                $cur_addr = explode(" ",$da->address);
                if($cur_addr[3] == $street_name){
                    $xiaoqu[$i] = $cur_addr[4];
                    $i++;
                }
            }
            return response()->json(['status' => 'success', 'xiaoqus' => $xiaoqu]);
        }
    }

    public function saveZiyingdingdan(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');

        $customer_name = $request->input('customer_name');
        $address = $request->input('address');
        $type = $request->input('type');
        $phone = $request->input('phone');
        $milkman_id = $request->input('milkman_id');
        $deliver_time = $request->input('deliver_time');
        $product_id = $request->input('product_id');
        $product_count = $request->input('product_count');

        $self_orders = new SelfOrder;
        $self_orders->station_id = $current_station_id;
        $self_orders->customer_name = $customer_name;
        $self_orders->deliver_at = $deliver_date_str;
        $self_orders->phone = $phone;
        $self_orders->address = $address;
        $self_orders->deliver_at = $deliver_time;
        $self_orders->save();

        $order_id = $self_orders->id;

        for($i=0; $i < count($product_count)-1; $i++){
            $self_order_products = new SelfOrderProduct;
            $self_order_products->order_id = $order_id;
            $self_order_products->product_id = $product_id[$i];
            $self_order_products->count = $product_count[$i];
            $product_price[$i] = ProductPrice::priceTemplateFromAddress($product_id[$i], $address);
            $self_order_products->price = $product_price[$i];
            $self_order_products->save();

            $order_product_id = $self_order_products->id;

            $milkman_delivery_plans = new MilkManDeliveryPlan;
            $milkman_delivery_plans->milkman_id = $milkman_id;
            $milkman_delivery_plans->station_id = $current_station_id;
            $milkman_delivery_plans->order_id = $order_id;
            $milkman_delivery_plans->order_product_id = $order_product_id;
            $milkman_delivery_plans->deliver_at = $deliver_date_str;
            $milkman_delivery_plans->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT;
            $milkman_delivery_plans->delivery_count = $product_count[$i];
            $milkman_delivery_plans->type = $type;
            $milkman_delivery_plans->save();
        }
        return Response::json(['status'=>"success"]);
    }

    public function MilkmanProductInfo($milkman_id){
        $current_station_id = Auth::guard('naizhan')->user()->id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');
        $milkman_delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->
        wherebetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT])->where('milkman_id',$milkman_id)->get();
        $products = array();
        $i = 0;
        $flag = 0;
        foreach ($milkman_delivery_plans as $k=>$mdp){
            if($mdp->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER){
                $product_number = -1;
                if($i == 0){
                    if($flag == 0){
                        $products[$i]['name'] = $mdp->order_product->product->name;
                        $products[$i]['planed_count'] = $mdp->delivery_count;
                        $products[$i]['test_drink'] = 0;
                        $products[$i]['group_count'] = 0;
                        $products[$i]['channel_count'] = 0;
                        $flag = 1;
                    }
                    else{
                        if($products[$i]['name'] == $mdp->order_product->product->name){
                            $products[$i]['planed_count'] += $mdp->delivery_count;
                        }
                        else{
                            $i++;
                            $products[$i]['name'] = $mdp->order_product->product->name;
                            $products[$i]['planed_count'] = $mdp->delivery_count;
                            $products[$i]['test_drink'] = 0;
                            $products[$i]['group_count'] = 0;
                            $products[$i]['channel_count'] = 0;
                        }
                    }
                }
                else{
                    for($j=0; $j<=$i;$j++){
                        if($products[$j]['name']==$mdp->order_product->product->name){
                            $product_number = $j;
                        }
                    }
                    if($product_number == -1){
                        $i++;
                        $products[$i]['name'] = $mdp->order_product->product->name;
                        $products[$i]['planed_count'] = $mdp->delivery_count;
                        $products[$i]['test_drink'] = 0;
                        $products[$i]['group_count'] = 0;
                        $products[$i]['channel_count'] = 0;
                    }
                    else{
                        $products[$product_number]['planed_count'] = +$mdp->delivery_count;
                    }
                }
            }
            elseif ($mdp->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_GROUP){
                $product_number = -1;
                if($i == 0){
                    if($flag == 0){
                        $products[$i]['name'] = $mdp->order_product->product->name;
                        $products[$i]['group_count'] = $mdp->delivery_count;
                        $products[$i]['test_drink'] = 0;
                        $products[$i]['planed_count'] = 0;
                        $products[$i]['channel_count'] = 0;
                        $flag = 1;
                    }
                    else{
                        if($products[$i]['name'] == $mdp->order_product->product->name){
                            $products[$i]['group_count'] += $mdp->delivery_count;
                        }
                        else{
                            $i++;
                            $products[$i]['name'] = $mdp->order_product->product->name;
                            $products[$i]['group_count'] = $mdp->delivery_count;
                            $products[$i]['test_drink'] = 0;
                            $products[$i]['planed_count'] = 0;
                            $products[$i]['channel_count'] = 0;
                        }
                    }
                }
                else{
                    for($j=0; $j<=$i;$j++){
                        if($products[$j]['name']==$mdp->order_product->product->name){
                            $product_number = $j;
                        }
                    }
                    if($product_number == -1){
                        $i++;
                        $products[$i]['name'] = $mdp->order_product->product->name;
                        $products[$i]['group_count'] = $mdp->delivery_count;
                        $products[$i]['test_drink'] = 0;
                        $products[$i]['planed_count'] = 0;
                        $products[$i]['channel_count'] = 0;
                    }
                    else{
                        $products[$product_number]['group_count'] += $mdp->delivery_count;
                    }
                }
            }
            elseif ($mdp->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL){
                $product_number = -1;
                if($i == 0){
                    if($flag == 0){
                        $products[$i]['name'] = $mdp->order_product->product->name;
                        $products[$i]['channel_count'] = $mdp->delivery_count;
                        $products[$i]['test_drink'] = 0;
                        $products[$i]['group_count'] = 0;
                        $products[$i]['planed_count'] = 0;
                        $flag = 1;
                    }
                    else{
                        if($products[$i]['name'] == $mdp->order_product->product->name){
                            $products[$i]['channel_count'] += $mdp->delivery_count;
                        }
                        else{
                            $i++;
                            $products[$i]['name'] = $mdp->order_product->product->name;
                            $products[$i]['channel_count'] = $mdp->delivery_count;
                            $products[$i]['test_drink'] = 0;
                            $products[$i]['group_count'] = 0;
                            $products[$i]['planed_count'] = 0;
                        }
                    }
                }
                else{
                    for($j=0; $j<=$i;$j++){
                        if($products[$j]['name']==$mdp->order_product->product->name){
                            $product_number = $j;
                        }
                    }
                    if($product_number == -1){
                        $i++;
                        $products[$i]['name'] = $mdp->order_product->product->name;
                        $products[$i]['channel_count'] = $mdp->delivery_count;
                        $products[$i]['test_drink'] = 0;
                        $products[$i]['group_count'] = 0;
                        $products[$i]['planed_count'] = 0;
                    }
                    else{
                        $products[$product_number]['channel_count'] += $mdp->delivery_count;
                    }
                }
            }
            elseif ($mdp->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK){
                $product_number = -1;
                if($i == 0){
                    if($flag == 0){
                        $products[$i]['name'] = $mdp->order_product->product->name;
                        $products[$i]['test_drink'] = $mdp->delivery_count;
                        $products[$i]['planed_count'] = 0;
                        $products[$i]['group_count'] = 0;
                        $products[$i]['planed_count'] = 0;
                        $flag = 1;
                    }
                    else{
                        if($products[$i]['name'] == $mdp->order_product->product->name){
                            $products[$i]['test_drink'] += $mdp->delivery_count;
                        }
                        else{
                            $i++;
                            $products[$i]['name'] = $mdp->order_product->product->name;
                            $products[$i]['test_drink'] = $mdp->delivery_count;
                            $products[$i]['planed_count'] = 0;
                            $products[$i]['group_count'] = 0;
                            $products[$i]['planed_count'] = 0;
                        }
                    }
                }
                else{
                    for($j=0; $j<=$i;$j++){
                        if($products[$j]['name']==$mdp->order_product->product->name){
                            $product_number = $j;
                        }
                    }
                    if($product_number == -1){
                        $i++;
                        $products[$i]['name'] = $mdp->order_product->product->name;
                        $products[$i]['test_drink'] = $mdp->delivery_count;
                        $products[$i]['planed_count'] = 0;
                        $products[$i]['group_count'] = 0;
                        $products[$i]['planed_count'] = 0;
                    }
                    else{
                        $products[$product_number]['test_drink'] += $mdp->delivery_count;
                    }
                }
            }

        }
        return $products;
    }

    public function jinrichangestatus($milkman_id){
        $current_station_id = Auth::guard('naizhan')->user()->id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');
        $milkman_delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->
        where('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT)->where('milkman_id',$milkman_id)->get();
        $status = array();
        $status['new_order_amount'] = 0;
        $status['new_changed_order_amount'] = 0;
        $status['milkbox_amount'] = 0;
        foreach ($milkman_delivery_plans as $k=>$mdp){
            if($mdp->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER){
                if($mdp->flag == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER)
                    $status['new_order_amount'] += $mdp->delivery_count;
                if($mdp->flag == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER_RULE_CHANGE)
                    $status['new_changed_order_amount'] += $mdp->delivery_count;
            }
            elseif ($mdp->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_MILKBOXINSTALL){
                $status['milkbox_amount']++;
            }
        }
        return $status;
    }

    public function showJinripeisongdan(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');

        $child = 'jinripeisongdan';
        $parent = 'shengchan';
        $current_page = 'jinripeisongdan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        $milkman_delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->
        wherebetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED])->get()->groupBy(function($sort){return $sort->milkman_id;});


        $milkman_info = array();
        foreach($milkman_delivery_plans as $m=>$dps_by_milkman) {
            $delivery_info = array();

            $order_by_types = $dps_by_milkman->groupBy(function ($sort){return $sort->type;});
            foreach ($order_by_types as $o=>$dbm){
                if($o == 1){
                    $regular_delivers = $dbm->groupBy(function($sort){return $sort->order_id;});
                    foreach ($regular_delivers as $r=>$by_order_id){
                        $delivery_info[$r] = Order::find($r);
                        $products = array();
                        $is_changed = 0;
                        $delivery_type = 1;

                        foreach($by_order_id as $dp) {
                            $name = $dp->order_product->product->name;
                            $count = $dp->delivery_count;
                            $products[] = $name.'*'.$count;
                            if($dp->plan_count != $dp->changed_plan_count)
                                $is_changed = 1;
                            $delivery_type = $dp->type;
                        }
                        $delivery_info[$r]['product'] = implode(',', $products);
                        $delivery_info[$r]['changed'] = $is_changed;
                        $delivery_info[$r]['delivery_type'] = $delivery_type;
                    }
                }
                else{
                    $extra_delivers = $dbm->groupBy(function($sort){return $sort->order_id;});
                    foreach ($extra_delivers as $r=>$by_order_id){
                        $delivery_info[$r] = SelfOrder::find($r);
                        $products = array();
                        $is_changed = 0;
                        $delivery_type = 1;
                        foreach($by_order_id as $dp) {
                            $name = $dp->order_product->product->name;
                            $count = $dp->delivery_count;
                            $products[] = $name.'*'.$count;
                            $delivery_type = $dp->type;
                        }
                        $delivery_info[$r]['product'] = implode(',', $products);
                        $delivery_info[$r]['changed'] = $is_changed;
                        $delivery_info[$r]['delivery_type'] = $delivery_type;
                    }
                }
            }
            $milkman_info[$m]['delivery_info'] = $delivery_info;
            $milkman_info[$m]['milkman_name'] = MilkMan::find($m)->name;
            $milkman_info[$m]['milkman_number'] = MilkMan::find($m)->number;
            $milkman_info[$m]['milkman_products'] = $this->MilkmanProductInfo(MilkMan::find($m)->id);
            $milkman_info[$m]['milkman_changestatus'] = $this->jinrichangestatus(MilkMan::find($m)->id);
        }

        return view('naizhan.shengchan.jinripeisongdan',[
            'pages'=>$pages,
            'child'=>$child,
            'parent'=>$parent,
            'current_page'=>$current_page,
            'milkman_info'=>$milkman_info,
        ]);
    }


    public function undelivered_process($order_product_id, $delivered_count, $deliver_at)
    {
        $plan = MilkManDeliveryPlan::where('order_product_id', $order_product_id)->where('deliver_at', $deliver_at)->get()->first();
        $delivery_count = $plan->changed_plan_count;
        $order_id = $plan->order_id;

        $order = Order::find($order_id);
        if (!$order) {
            return response()->json(['status' => 'fail', 'message' => '找不到订单.']);
        }

        $undelivered_count = $delivery_count - $delivered_count;
        $delivery_type = OrderProduct::find($order_product_id);
        $last_delivery_date_info = MilkManDeliveryPlan::where('order_product_id',$order_product_id)->orderby('deliver_at','desc')->get()->first();
        if($last_delivery_date_info->status == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED){
            if($last_delivery_date_info->plan_count < $delivery_type->count_per_day){
                if($last_delivery_date_info->plan_count + $undelivered_count >= $delivery_type->count_per_day){
                    $undelivered_count = $undelivered_count-($delivery_type->count_per_day - $last_delivery_date_info->plan_count);
                    $last_delivery_date_info->plan_count = $delivery_type->count_per_day;
                    $last_delivery_date_info->changed_plan_count = $delivery_type->count_per_day;
                    $last_delivery_date_info->delivery_count = $delivery_type->count_per_day;
                    $last_delivery_date_info->save();
                }
                else{
                    $last_delivery_date_info->plan_count = $last_delivery_date_info->plan_count + $undelivered_count;
                    $last_delivery_date_info->changed_plan_count = $delivery_type->count_per_day;
                    $last_delivery_date_info->delivery_count = $delivery_type->count_per_day;
                    $last_delivery_date_info->save();
                    return;
                }
            }
            if($undelivered_count > 0){
                if($delivery_type->delivery_type == 1){
                    $p_date = str_replace('-','/',$last_delivery_date_info->produce_at);
                    $produce_date = date('Y-m-d',strtotime($p_date."+1 days"));
                    $d_date = str_replace('-','/',$last_delivery_date_info->deliver_at);
                    $deliver_date = date('Y-m-d',strtotime($d_date."+1 days"));
                }
                elseif($delivery_type->delivery_type == 2){
                    $p_date = str_replace('-','/',$last_delivery_date_info->produce_at);
                    $produce_date = date('Y-m-d',strtotime($p_date."+2 days"));
                    $d_date = str_replace('-','/',$last_delivery_date_info->deliver_at);
                    $deliver_date = date('Y-m-d',strtotime($d_date."+2 days"));
                }
                elseif($delivery_type->delivery_type == 3){
                    $delivered_dates = explode(',',$delivery_type->custom_order_dates);
                    $last_delivery_weekday = date('w', strtotime($last_delivery_date_info->deliver_at));
                    $i = 0;
                    foreach ($delivered_dates as $dd){
                        if($dd == $last_delivery_weekday){
                            if($i < count($delivered_dates)-1){
                                $deliver_circle = $delivered_dates[$i+1] - $delivered_dates[$i];
                                $deliver_date = date('Y-m-d',strtotime($last_delivery_date_info->deliver_at."+".$deliver_circle." days"));
                            }
                            else{
                                $deliver_circle = 7+$delivered_dates[0] - $delivered_dates[$i];
                                $deliver_date = date('Y-m-d',strtotime($last_delivery_date_info->deliver_at."+".$deliver_circle." days"));
                            }
                        }
                        $i++;
                    }
                    $production_period = Product::find(OrderProduct::find($order_product_id)->product_id)->production_period/24;
                    $produce_date = date('Y-m-d',strtotime($deliver_date."-".$production_period." days"));
                }
                else{
                    $last_deliver_day = explode('-',$last_delivery_date_info->deliver_at);
                    $delivered_dates = explode(',',$delivery_type->custom_order_dates);
                    $i =0;
                    foreach ($delivered_dates as $dd){
                        if(indval($dd) == intval($last_deliver_day[2])){
                            if($i < count($delivered_dates)){
                                $deliver_date = $last_deliver_day[0]."-".$last_deliver_day[1]."-".$delivered_dates[$i+1];
                            }
                            else{
                                $p_date = str_replace('-','/',$last_delivery_date_info->deliver_at);
                                $produce_month = date('Y-m-d',strtotime($p_date."+1 month"));
                                $date_val = explode('-',$produce_month);
                                $deliver_date = $date_val[0]."-".$date_val[1]."-".$delivered_dates[0];
                            }
                        }
                        $i++;
                    }
                    $production_period = Product::find(OrderProduct::find($order_product_id)->product_id)->production_period/24;
                    $produce_date = date('Y-m-d',strtotime($deliver_date."-".$production_period." days"));
                }
                $addplan = new MilkManDeliveryPlan;
                $addplan->milkman_id = $last_delivery_date_info->milkman_id;
                $addplan->station_id = $last_delivery_date_info->station_id;
                $addplan->order_id = $last_delivery_date_info->order_id;
                $addplan->order_product_id = $last_delivery_date_info->order_product_id;
                $addplan->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED;
                $addplan->type = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER;
                $addplan->plan_count = $undelivered_count;
                $addplan->changed_plan_count = $undelivered_count;
                $addplan->delivery_count = $undelivered_count;
                $addplan->produce_at = $produce_date;
                $addplan->deliver_at = $deliver_date;
                $addplan->save();
            }
            return;
        }
        elseif($last_delivery_date_info->status == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT){
            if($last_delivery_date_info->plan_count < $delivery_type->count_per_day){
                if($last_delivery_date_info->plan_count + $undelivered_count >= $delivery_type->count_per_day){
                    $undelivered_count = $undelivered_count-($delivery_type->count_per_day - $last_delivery_date_info->plan_count);
                    $last_delivery_date_info->changed_plan_count = $delivery_type->count_per_day;
                    $last_delivery_date_info->delivery_count = $delivery_type->count_per_day;
                    $last_delivery_date_info->save();
                }
                else{
                    $last_delivery_date_info->changed_plan_count = $last_delivery_date_info->plan_count + $undelivered_count;
                    $last_delivery_date_info->delivery_count = $last_delivery_date_info->plan_count + $undelivered_count;
                    $last_delivery_date_info->save();
                    return;
                }
            }
            if($undelivered_count > 0){
                if($delivery_type->delivery_type == 1){
                    $p_date = str_replace('-','/',$last_delivery_date_info->produce_at);
                    $produce_date = date('Y-m-d',strtotime($p_date."+1 days"));
                    $d_date = str_replace('-','/',$last_delivery_date_info->deliver_at);
                    $deliver_date = date('Y-m-d',strtotime($d_date."+1 days"));
                }
                elseif($delivery_type->delivery_type == 2){
                    $p_date = str_replace('-','/',$last_delivery_date_info->produce_at);
                    $produce_date = date('Y-m-d',strtotime($p_date."+2 days"));
                    $d_date = str_replace('-','/',$last_delivery_date_info->deliver_at);
                    $deliver_date = date('Y-m-d',strtotime($d_date."+2 days"));
                }
                elseif($delivery_type->delivery_type == 3){
                    $delivered_dates = explode(',',$delivery_type->custom_order_dates);
                    $last_delivery_weekday = date('w', strtotime($last_delivery_date_info->deliver_at));
                    $i = 0;
                    foreach ($delivered_dates as $dd){
                        if($dd == $last_delivery_weekday){
                            if($i < count($delivered_dates)-1){
                                $deliver_circle = $delivered_dates[$i+1] - $delivered_dates[$i];
                                $deliver_date = date('Y-m-d',strtotime($last_delivery_date_info->deliver_at."+".$deliver_circle." days"));
                            }
                            else{
                                $deliver_circle = 7+$delivered_dates[0] - $delivered_dates[$i];
                                $deliver_date = date('Y-m-d',strtotime($last_delivery_date_info->deliver_at."+".$deliver_circle." days"));
                            }
                        }
                        $i++;
                    }
                    $production_period = Product::find(OrderProduct::find($order_product_id)->product_id)->production_period/24;
                    $produce_date = date('Y-m-d',strtotime($deliver_date."-".$production_period." days"));
                }
                else{
                    $last_deliver_day = explode('-',$last_delivery_date_info->deliver_at);
                    $delivered_dates = explode(',',$delivery_type->custom_order_dates);
                    $i =0;
                    foreach ($delivered_dates as $dd){
                        if(indval($dd) == intval($last_deliver_day[2])){
                            if($i < count($delivered_dates)){
                                $deliver_date = $last_deliver_day[0]."-".$last_deliver_day[1]."-".$delivered_dates[$i+1];
                            }
                            else{
                                $p_date = str_replace('-','/',$last_delivery_date_info->deliver_at);
                                $produce_month = date('Y-m-d',strtotime($p_date."+1 month"));
                                $date_val = explode('-',$produce_month);
                                $deliver_date = $date_val[0]."-".$date_val[1]."-".$delivered_dates[0];
                            }
                        }
                        $i++;
                    }
                    $production_period = Product::find(OrderProduct::find($order_product_id)->product_id)->production_period/24;
                    $produce_date = date('Y-m-d',strtotime($deliver_date."-".$production_period." days"));
                }
                $addplan = new MilkManDeliveryPlan;
                $addplan->milkman_id = $last_delivery_date_info->milkman_id;
                $addplan->station_id = $last_delivery_date_info->station_id;
                $addplan->order_id = $last_delivery_date_info->order_id;
                $addplan->order_product_id = $last_delivery_date_info->order_product_id;
                $addplan->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED;
                $addplan->type = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER;
                $addplan->plan_count = $undelivered_count;
                $addplan->changed_plan_count = $undelivered_count;
                $addplan->delivery_count = $undelivered_count;
                $addplan->produce_at = $produce_date;
                $addplan->deliver_at = $deliver_date;
                $addplan->save();
            }
        }
        return;
    }


    public function showPeisongfanru(Request $request){

        $current_station_id = Auth::guard('naizhan')->user()->id;
        $current_factory_id = Auth::guard('naizhan')->user()->factory_id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $current_date_str = $currentDate->format('Y-m-d');
        $deliver_date_str = $request->input('current_date');
        if($deliver_date_str == ''){
            $deliver_date_str = $currentDate->format('Y-m-d');
        }
        $MilkboxSetupDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $MilkboxSetupDate->add(\DateInterval::createFromDateString('tomorrow'));
        $child = 'peisongfanru';
        $parent = 'shengchan';
        $current_page = 'peisongfanru';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        $milkman = MilkMan::where('is_active',1)->where('station_id',$current_station_id)->get();
        $current_milkman = $request->input('milkman_id');
        if($current_milkman == ''){
            $current_milkmans = MilkMan::where('is_active',1)->where('station_id',$current_station_id)->get()->first();
            if($current_milkmans != null){
                $current_milkman = $current_milkmans->id;
            }
            else{
                $current_milkman = '';
            }
        }
        $milkman_delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->
        wherebetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED])->where('milkman_id',$current_milkman)->get();
        $delivery_info = array();
        $order_by_types = $milkman_delivery_plans->groupBy(function ($sort){return $sort->type;});

        $station_addr = DeliveryStation::find($current_station_id)->address;
        $station_addr = explode(' ',$station_addr);
        $station_addr = $station_addr[0]." ".$station_addr[1]." ".$station_addr[2];

        $bottle_types = DB::select(DB::raw("select DISTINCT p.bottle_type from products p, productprice pp
                    where p.id = pp.product_id and p.factory_id = $current_factory_id and pp.sales_area LIKE '%$station_addr%'"));

        $milkman_bottle_refunds = MilkmanBottleRefund::where('milkman_id',$current_milkman)->where('time',$deliver_date_str)->get(['count','bottle_type']);
        $todays_milkman_bottle_refunds = MilkmanBottleRefund::where('milkman_id',$current_milkman)->where('time',$current_date_str)->get(['count','bottle_type']);
        $is_todayrefund = 0;
        foreach ($milkman_delivery_plans as $mdp){
            $is_todayrefund += $mdp->delivered_count;
        }
        
        foreach ($todays_milkman_bottle_refunds as $tbr){
            $is_todayrefund += $tbr->count;
        }
        foreach ($order_by_types as $o=>$dbm){
            if($o == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER){
                $regular_delivers = $dbm->groupBy(function($sort){return $sort->order_id;});
                foreach ($regular_delivers as $r=>$by_order_id){
                    $delivery_info[$r] = Order::find($r);
                    $products = array();
                    $is_changed = 0;
                    $delivery_type = 1;
                    foreach($by_order_id as $pro=>$dp) {
                        $name = $dp->order_product->product->name;
                        $count = $dp->delivery_count;
                        $products[$pro]['name'] = $name;
                        $products[$pro]['count'] = $count;
                        $products[$pro]['id'] = $dp->order_product->product->id;
                        $products[$pro]['order_product_id'] = $dp->order_product_id;
                        $products[$pro]['delivered_count'] = $dp->delivered_count;
                        $products[$pro]['comment'] = $dp->comment;
//                        $products[] = $name.'*'.$count;
                        if($dp->plan_count != $dp->changed_plan_count)
                            $is_changed = 1;
                        $delivery_type = $dp->type;
                        $milk_man = $dp->milkman->name;
                    }
//                    $delivery_info[$r]['product'] = implode(',', $products);
                    $delivery_info[$r]['product'] = $products;
                    $delivery_info[$r]['changed'] = $is_changed;
                    $delivery_info[$r]['delivery_type'] = $delivery_type;
                    $delivery_info[$r]['milkman_name'] = $milk_man;
                }
            }
            else{
                $extra_delivers = $dbm->groupBy(function($sort){return $sort->order_id;});
                foreach ($extra_delivers as $r=>$by_order_id){
                    $delivery_info[$r] = SelfOrder::find($r);
                    if($delivery_info[$r] != null){
                        $products = array();
                        $is_changed = 0;
                        $delivery_type = 2;
                        $milkboxinstall = 0;
                        foreach($by_order_id as $pro=>$dp) {
                            $name = $dp->order_product->product->name;
                            $count = $dp->delivery_count;
//                            $products[] = $name.'*'.$count;
                            $products[$pro]['name'] = $name;
                            $products[$pro]['id'] = $dp->order_product->product->id;
                            $products[$pro]['count'] = $count;
                            $products[$pro]['order_product_id'] = $dp->order_product_id;
                            $products[$pro]['delivered_count'] = $dp->delivered_count;
                            $products[$pro]['comment'] = $dp->comment;
                            $delivery_type = $dp->type;
                            $milk_man = $dp->milkman->name;
                            if($dp->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_MILKBOXINSTALL)
                                $milkboxinstall = 1;
                        }
                        $delivery_info[$r]['is_milkbox'] = 0;
//                        $delivery_info[$r]['product'] = implode(',', $products);
                        $delivery_info[$r]['product'] = $products;
                        $delivery_info[$r]['changed'] = $is_changed;
                        $delivery_info[$r]['delivery_type'] = $delivery_type;
                        $delivery_info[$r]['milkman_name'] = $milk_man;
                        $delivery_info[$r]['milkbox_install'] = $milkboxinstall;
                    }
                }
            }
        }

        return view('naizhan.shengchan.peisongfanru',[
            'pages'=>$pages,
            'child'=>$child,
            'parent'=>$parent,
            'current_page'=>$current_page,
            'delivery_info'=>$delivery_info,
            'milkman'=>$milkman,
            'deliver_date'=>$deliver_date_str,
            'current_date'=>$current_date_str,
            'current_milkman'=>$current_milkman,
            'bottle_types'=>$bottle_types,
            'milkman_bottle_refunds'=>$milkman_bottle_refunds,
            'is_todayrefund'=>$is_todayrefund,
        ]);
    }

    public function savebottleboxPeisongfanru(Request $request){
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');

        $current_milkman_id = $request->input('milkman_id');
        $bottle_type = $request->input('bottle_type');
        $count = $request->input('count');

        $bottle_refunds = new MilkmanBottleRefund;
        $bottle_refunds->milkman_id = $current_milkman_id;
        $bottle_refunds->bottle_type = $bottle_type;
        $bottle_refunds->time = $deliver_date_str;
        $bottle_refunds->count = $count;
        $bottle_refunds->save();
        return;
    }

    public function confirmdeliveryPeisongfanru(Request $request){
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');
        $current_station_id = Auth::guard('naizhan')->user()->id;

        $table_info = json_decode($request->getContent(),true);
        foreach ($table_info as $ti){
            $current_milkman_id = $ti['milkman_id'];
            $order_product_id = $ti['order_product_id'];
            $delivered_count = $ti['delivered_count'];
            $delivery_type = $ti['delivery_type'];
            $comment = $ti['comment'];
            $order_id = $ti['order_id'];

            $delivered_count = preg_replace('/\s+/', '', $delivered_count);
            $milkmandeliverys = MilkManDeliveryPlan::where('deliver_at',$deliver_date_str)->where('milkman_id',$current_milkman_id)->
            where('type',$delivery_type)->where('order_product_id',$order_product_id)->wherebetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT])->get()->first();
            $milkmandeliverys->delivered_count = $delivered_count;
            if($delivered_count == 0){
                $milkmandeliverys->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL;
                $milkmandeliverys->cancel_reasone = "配送取消";
            }else{
                $milkmandeliverys->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED;

            }
            $milkmandeliverys->comment = $comment;
            $milkmandeliverys->save();
            if($milkmandeliverys->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER){
//                $total_order_counts = 0;
//                $delivered_counts = 0;
//                $order_products = OrderProduct::where('order_id',$order_id)->get();
//                foreach ($order_products as $op){
//                    $total_order_counts += $op->total_count;
//                }
//                $delivered_products_count = MilkManDeliveryPlan::where('order_id',$order_id)->where('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)->get();
//                foreach ($delivered_products_count as $dpc){
//                    $delivered_counts += $dpc->delivered_count;
//                }
//                if($delivered_count == $total_order_counts){
//                    $finished_order = Order::find($order_id);
//                    $finished_order->status = Order::ORDER_FINISHED_STATUS;
//                    $finished_order->save();
//                }
                $order = Order::find($order_id);
                $order->remaining_amount = $order->remaining_amount - $milkmandeliverys->delivered_count * $milkmandeliverys->product_price;
                $order->save();
                $total_order_counts = count(MilkManDeliveryPlan::where('order_id',$order_id)->whereBetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT])->get());
                if($total_order_counts == 0){
                    $finished_order = Order::find($order_id);
                    $finished_order->status = Order::ORDER_FINISHED_STATUS;
                    $finished_order->save();
                    $customer = Customer::find($finished_order->customer_id);
                    $customer->remain_amount = $customer->remain_amount + $finished_order->remaining_amount;
                    $customer->save();
                }
                if($milkmandeliverys->delivered_count != $milkmandeliverys->changed_plan_count){
                    $this->undelivered_process($milkmandeliverys->order_product_id,$milkmandeliverys->delivered_count,$milkmandeliverys->deliver_at);
                }
            }
        }
        return Response::json(['status'=>"success"]);
    }

    public function confirm(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');

        $current_milkman_id = $request->input('milkman_id');
        $order_id = $request->input('order_id');
        $type = $request->input('delivery_type');
        $comment = $request->input('comment');
        $status = 'fail';
        $milkman_deliverys = MilkManDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->
        where('milkman_id',$current_milkman_id)->where('order_id',$order_id)->where('type',$type)->get();
        if($milkman_deliverys != null){
            foreach ($milkman_deliverys as $md){
                $md->comment = $comment;
                $md->flag = 1;
                $md->delivered_count = $md->delivery_count;
                $md->save();
                $changed_value = $md->delivered_count - $md->delivery_count;
                $change_order = new OrderCtrl;
                $change_order->change_delivery_plan_for_one_day($md->order_product_id,$changed_value,$deliver_date_str);
                $status = 'success';
            }
        }
        return Response::json(['status'=>$status]);
    }
    //
}
