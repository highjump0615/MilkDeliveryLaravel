<?php

namespace App\Http\Controllers;

use App\Model\BasicModel\Customer;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\DeliveryType;
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
    private function getCurrentStationId() {
        return Auth::guard('naizhan')->user()->station_id;
    }

    /**
     * 显示配送管理页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPeisongguanli(Request $request){

        $current_station_id = $this->getCurrentStationId();

        $child = 'peisongguanli';
        $parent = 'shengchan';
        $current_page = 'peisongguanli';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        $deliver_date_str = getCurDateString();
        $currentDate_str = getPrevDateString();

        // 只有考虑已签收的计划
        $DSProduction_plans = DSProductionPlan::where('station_id',$current_station_id)
            ->where('produce_end_at',$currentDate_str)
            ->where('status', DSProductionPlan::DSPRODUCTION_PRODUCE_RECEIVED)
            ->orderby('product_id')
            ->get();

        $is_distributed = 0;

        $changed_counts = MilkManDeliveryPlan::where('station_id',$current_station_id)
            ->where('deliver_at',$deliver_date_str)
            ->wherebetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED])
            ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            ->get();

//        // 查询已配送完的配送订单
//        $deliver_finished_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)
//            ->where('deliver_at',$deliver_date_str)
//            ->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
//            ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
//            ->get();

        // DSProductionPlan有可能不包括当天配送的所有数据、因为有配送变化量等，于是手工设置
        $planResult = array();

        // 第一，根据配送订单查询数据
        foreach($changed_counts as $cc) {

            $planProduct = null;

            $index = $cc->order_product->product->id;
            if (array_key_exists($index, $planResult)) {
                $planProduct = $planResult[$index];
            }

            if (!$planProduct) {
                // 初始化
                foreach($DSProduction_plans as $dp){
                    if($index == $dp->product_id){
                        $planProduct = $dp;
                        break;
                    }
                }

                // 这产品不在DSProductionPlan里面，即没提交过来的
                if (!$planProduct) {
                    $planProduct = new DSProductionPlan;
                    $planProduct->product_id = $index;
                }

                $planProduct["changed_plan_count"] = 0;
            }

            $is_distributed = $this->calcPlanDataForProduct($planProduct, $cc->changed_plan_count);

            // 添加到主数组
            $planResult[$index] = $planProduct;
        }

        // 第二，根据提交数据查询数据
        foreach($DSProduction_plans as $dp){

            if (array_key_exists($dp->product_id, $planResult)) {
                // 这奶品以计算好了，不用再计算
                continue;
            }

            $is_distributed = $this->calcPlanDataForProduct($dp, 0);

            // 添加到主数组
            $planResult[$dp->product_id] = $dp;
        }

        // 计算配送计划调整数量
        foreach ($planResult as $planProduct) {
            $planProduct["changed_amount"] = $planProduct["changed_plan_count"] - $planProduct->order_count;
        }

        // 有数量变化的配送明细，排除数量为0的配送明细
        $changed_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)
            ->where('deliver_at',$deliver_date_str)
            ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            ->wherebetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED])
            ->get();

        return view('naizhan.shengchan.peisongguanli',[
            'pages'                 =>$pages,
            'child'                 =>$child,
            'parent'                =>$parent,
            'current_page'          =>$current_page,

            'dsproduction_plans'    =>$planResult,
            'is_distributed'        =>$is_distributed,
            'changed_plans'         =>$changed_plans,
        ]);
    }

    /**show
     * 配送管理页面计算一种奶品的参数
     * @param $planProduct
     * @param $planCount
     */
    private function calcPlanDataForProduct(&$planProduct, $planCount) {
        // 是否已调配
        $is_distributed = 0;

        $current_station_id = $this->getCurrentStationId();
        $deliver_date_str = getCurDateString();
        $currentDate_str = getPrevDateString();

        // 合计总数量
        $planProduct["changed_plan_count"] += $planCount;

        // 查看配送任务是否已经生成了
        $delivery_plans = DSDeliveryPlan::getDeliveryPlanGenerated($current_station_id, $planProduct->product_id);

        // 已调配
        if ($delivery_plans != null){
            $is_distributed = 1;
            $planProduct["dp_retail"] = $delivery_plans->retail;
            $planProduct["dp_test_drink"] = $delivery_plans->test_drink;
            $planProduct["dp_group_sale"] = $delivery_plans->group_sale;
            $planProduct["dp_channel_sale"] = $delivery_plans->channel_sale;
            $planProduct["dp_remain"] = $delivery_plans->remain_final;
        }

        // 获取昨日库存量
        $delivery_plans = DSDeliveryPlan::where('product_id',$planProduct->product_id)
            ->where('station_id', $current_station_id)
            ->where('deliver_at', $currentDate_str)
            ->get()
            ->first();

        if ($delivery_plans != null){
            $planProduct["dp_remain_before"] = $delivery_plans->remain_final;
        }

        // 查询已配送完的配送订单
        $deliver_finished_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)
            ->where('deliver_at',$deliver_date_str)
            ->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
            ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            ->get();

        // 配送业务实际配送数量
        $deliver_finished_count = 0;
        foreach($deliver_finished_plans as $dfp){
            if($dfp->order_product->product->id == $planProduct->product_id){
                $deliver_finished_count += $dfp->delivered_count;
            }
        }

        $planProduct["deliverd_count"] = $deliver_finished_count;

        return $is_distributed;
    }

    public function save_distribution(Request $request){

        $current_station_id = Auth::guard('naizhan')->user()->station_id;

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $currentDate_str = $currentDate->format('Y-m-d');

        $product_id = $request->input('product_id');
        $retail = $request->input('retail');
        $test_drink = $request->input('test_drink');
        $group_sale = $request->input('group_sale');
        $channel_sale = $request->input('channel_sale');
        $remain = $request->input('remain');

        $delivery_distribution = new DSDeliveryPlan;
        $delivery_distribution->station_id = $current_station_id;
        $delivery_distribution->deliver_at = $currentDate_str;
        $delivery_distribution->product_id = $product_id;
        $delivery_distribution->retail = $retail;
        $delivery_distribution->test_drink = $test_drink;
        $delivery_distribution->group_sale = $group_sale;
        $delivery_distribution->channel_sale = $channel_sale;
        $delivery_distribution->remain = $remain;
        $delivery_distribution->save();

        return count($delivery_distribution);
    }

    public function save_changed_distribution_deprecated(Request $request){

        $current_station_id = Auth::guard('naizhan')->user()->station_id;

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');

        $delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)
            ->where('deliver_at',$deliver_date_str)
            ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            ->where('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED)
            ->get();

        foreach($delivery_plans as $dp){
            if ($dp->plan_count == $dp->changed_plan_count){
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

        if ($rest_amount > 0) {
            $notification = new DSNotification();
            $notification->sendToStationNotification($current_station_id,7,"回报金钱","您本次订单计划多余扣除货款".$rest_amount."元已退回您的自营账户。");

            // 需要添加财务方面的记录
        }

        return Response::json();
    }

    /**
     * 生成配送列表
     * @param Request $request
     * @return mixed
     */
    public function save_changed_distribution(Request $request){

        $current_station_id = Auth::guard('naizhan')->user()->station_id;

        $strCurrentDate = getCurDateString();

        $delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)
            ->where('deliver_at',$strCurrentDate)
            ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            ->where('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED)
            ->get();

        //
        // delivery_count里填数量
        //
        foreach($delivery_plans as $dp){
            if ($dp->plan_count == $dp->changed_plan_count){
                $dp->delivery_count = $dp->plan_count;
                $dp->save();
            }
        }

        $table_info = json_decode($request->getContent(),true);
        foreach ($table_info as $ti){
            $changed_delivery = MilkManDeliveryPlan::find($ti['id']);
            $changed_delivery->delivery_count = $ti['delivery_count'];
            $changed_delivery->comment = $ti['comment'];
            $changed_delivery->save();
        }

        return Response::json();
    }

    /**
     * 打开配送列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPeisongliebiao(Request $request){

        $current_station_id = Auth::guard('naizhan')->user()->station_id;

        $deliver_date_str = getCurDateString();;

        $res = $this->getOrderDeliverList($current_station_id, $deliver_date_str);

        // 页面信息
        $child = 'peisongguanli';
        $parent = 'shengchan';
        $current_page = 'peisongliebiao';
        $pages = Page::where('backend_type',Page::NAIZHAN)->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.shengchan.peisongguanli.peisongliebiao',[
            'pages'             =>$pages,
            'child'             =>$child,
            'parent'            =>$parent,
            'current_page'      =>$current_page,
            'delivery_plans'    =>$res,
        ]);
    }

    /**
     * 查询配送列表
     * @param $station_id
     * @param $date
     * @return array
     */
    private function getOrderDeliverList($station_id, $date) {

        $delivery_plans = MilkManDeliveryPlan::where('station_id', $station_id)
            ->where('deliver_at', $date)
            ->where('type', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            ->whereBetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED])
            ->get()
            ->groupBy(function($sort){
                return $sort->order_id;
            });

        $aryRes = array();
        foreach ($delivery_plans as $r=>$by_order_id) {
            // 获取订单信息
            $orderData = Order::find($r);
            $products = array();
            $is_changed = 0;
            $delivery_type = 1;
            $box_install_count = 0;     // 奶箱安装数量
            $comment = '';

            foreach ($by_order_id as $dp) {
                $name = $dp->order_product->product->name;
                $count = $dp->delivery_count;
                $products[] = $name . '*' . $count;
                if ($dp->plan_count != $dp->changed_plan_count)
                    $is_changed = 1;
                $delivery_type = $dp->type;

                if ($dp->isBoxInstall()) {
                    $box_install_count++;
                }

                $comment = $dp->comment;
            }

            $orderData['product'] = implode(',', $products);
            if ($box_install_count > 0) {
                $orderData['product'] = $orderData['product'] . ', 奶箱*' . $box_install_count;
            }

            $orderData['changed'] = $is_changed;
            $orderData['delivery_type'] = $delivery_type;
            $orderData['comment_delivery'] = $comment;

            // 添加到主数组
            array_push($aryRes, $orderData);
        }

        return $aryRes;
    }

    public function showZiyingdingdan(Request $request){

        $current_station_id = Auth::guard('naizhan')->user()->station_id;

        // 配送日期
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');

        // 生产完成日期
        $currentDate->add(\DateInterval::createFromDateString('yesterday'));
        $production_date_str = $currentDate->format('Y-m-d');

        $delivery_plans = DSDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at',$deliver_date_str)->get();

        $milk_mans = MilkMan::where('station_id',$current_station_id)->get();

        if($milk_mans->first() == null){
            return redirect()->route('naizhan_peisongliebiao')->with('page_status','没有配送员!');
        }

        $milkman_delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)
            ->where('deliver_at',$deliver_date_str)
            ->where('type',"!=", MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            ->get()
            ->groupBy(function($sort){
                return $sort->order_id;
            });

        $res = array();
        foreach($milkman_delivery_plans as $o=>$dps_by_order) {
            $res[$o] = SelfOrder::find($o);
            $products = array();
            $is_changed = 0;
            $delivery_type = 1;
            $milk_man = '';
            $comment = '';

            foreach($dps_by_order as $dp) {
                $name = $dp->order_product->product->name;
                $count = $dp->delivery_count;
                $products[] = $name.'*'.$count;
                if($dp->plan_count != $dp->changed_plan_count)
                    $is_changed = 1;
                $delivery_type = $dp->type;
                $milk_man = $dp->milkman->name;
                $comment = $dp->comment;
            }
            $res[$o]['product'] = implode(',', $products);
            $res[$o]['changed'] = $is_changed;
            $res[$o]['delivery_type'] = $delivery_type;
            $res[$o]['milkman_name'] = $milk_man;
            $res[$o]['comment_delivery'] = $comment;
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

        // 页面信息
        $child = 'peisongguanli';
        $parent = 'shengchan';
        $current_page = 'ziyingdingdan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.shengchan.peisongguanli.ziyingdingdan',[
            'pages'                     =>$pages,
            'child'                     =>$child,
            'parent'                    =>$parent,
            'current_page'              =>$current_page,

            'delivery_plans'            =>$delivery_plans,
            'streets'                   =>$street,
            'milk_man'                  =>$milk_mans,
            'current_district'          =>$show_district,
            'addr_district'             =>$addr_district,
            'province'                  =>$province,
            'milkman_delivery_plans'    =>$res,
        ]);
    }

    public function showZiyingdingdan_deprecated(Request $request){

        $current_station_id = Auth::guard('naizhan')->user()->station_id;

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
            $milkman_delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)
                ->where('deliver_at',$deliver_date_str)
                ->where('type',"!=", 1)
                ->get();

            foreach($milkman_delivery_plans as $md) {
                if($md->order_product->product->id == $dp->product_id){
                    $milkman_planed_count += $md->delivery_count;
                }
            }

            $dp['rest_amount'] = $dp->test_drink + $dp->group_sale + $dp->channel_sale - $milkman_planed_count;
            $total_remain_product_count += $dp['rest_amount'];
        }

//        if($total_remain_product_count == 0){
//            return redirect()->route('naizhan_peisongliebiao')->with('page_status','你已经添加自营配送任务!');
//        }

        $milk_mans = MilkMan::where('station_id',$current_station_id)->get();
//        if($delivery_plans->first() == null){
//            return redirect()->route('naizhan_peisongliebiao')->with('page_status','没有自营计划量!');
//        }

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

        return view('naizhan.shengchan.peisongguanli.ziyingdingdan',[
            'pages'                     =>$pages,
            'child'                     =>$child,
            'parent'                    =>$parent,
            'current_page'              =>$current_page,
            'delivery_plans'            =>$delivery_plans,
            'streets'                   =>$street,
            'milk_man'                  =>$milk_mans,
            'current_district'          =>$show_district,
            'addr_district'             =>$addr_district,
            'province'                  =>$province,
            'milkman_delivery_plans'    =>$res,
        ]);
    }

    public function getXiaoquName(Request $request){
        if ($request->ajax()) {
            $street_name = $request->input('street_name');
            $current_station_id = Auth::guard('naizhan')->user()->station_id;
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
        $current_station_id = Auth::guard('naizhan')->user()->station_id;

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
        $comment = $request->input('comment');

        $self_orders = new SelfOrder;
        $self_orders->station_id = $current_station_id;
        $self_orders->customer_name = $customer_name;
        $self_orders->deliver_at = $deliver_date_str;
        $self_orders->phone = $phone;
        $self_orders->address = $address;
        $self_orders->delivery_time = $deliver_time;
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
            $milkman_delivery_plans->comment = $comment;
            $milkman_delivery_plans->save();

            // 更新
            $deliveryPlan = DSDeliveryPlan::where('product_id', $product_id[$i])
                ->where('station_id', $current_station_id)
                ->where('deliver_at', $deliver_date_str)
                ->get()
                ->first();

            if ($deliveryPlan) {
                $deliveryPlan->increaseSelfDelivery($type, $product_count[$i]);
            }
        }

        return Response::json(['status'=>"success"]);
    }

    /**
     * 今日配送单 - 配送员统计
     * @param $milkman_id
     * @return array
     */
    public function MilkmanProductInfo($milkman_id){
        $current_station_id = Auth::guard('naizhan')->user()->station_id;

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');

        $milkman_delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)
            ->where('deliver_at',$deliver_date_str)
            ->wherebetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED])
            ->where('milkman_id',$milkman_id)
            ->get();

        // 配送员当日配送的奶品数组
        // 键是产品id
        $products = array();

        foreach ($milkman_delivery_plans as $mdp) {
            $prodData = null;

            $index = $mdp->order_product->product->id;
            if (array_key_exists($index, $products)) {
                $prodData = $products[$index];
            }

            if (!$prodData) {
                $prodData = array();

                // 初始化
                $prodData['name'] = $mdp->order_product->product->name;
                $prodData[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER] = 0;
                $prodData[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_GROUP] = 0;
                $prodData[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL] = 0;
                $prodData[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK] = 0;
                $prodData[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_RETAIL] = 0;
            }

            $prodData[$mdp->type] += $mdp->delivery_count;

            $products[$index] = $prodData;
        }

        return $products;
    }

    /**
     * 今日配送单的配送量统计
     * @param $milkman_id
     * @return array
     */
    public function jinrichangestatus($milkman_id){
        $current_station_id = Auth::guard('naizhan')->user()->station_id;

        $deliver_date_str = getCurDateString();

        $milkman_delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)
            ->where('deliver_at',$deliver_date_str)
            ->wherebetween('status', [MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT, MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED])
            ->where('milkman_id',$milkman_id)
            ->get();

        $status = array();
        $status['new_order_amount'] = 0;
        $status['new_changed_order_amount'] = 0;
        $status['milkbox_amount'] = 0;

        foreach ($milkman_delivery_plans as $k=>$mdp){
            // 只考虑订单配送
            if ($mdp->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER){
                if($mdp->flag == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER) {
                    // 第一次配送的数量合计
                    $status['new_order_amount'] += $mdp->delivery_count;
                }
                if($mdp->flag == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER_RULE_CHANGE) {
                    $status['new_changed_order_amount'] += $mdp->delivery_count;
                }

                if ($mdp->isBoxInstall()) {
                    $status['milkbox_amount']++;
                }
            }
//            elseif ($mdp->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_MILKBOXINSTALL){
//                $status['milkbox_amount']++;
//            }
        }

        return $status;
    }

    public function showJinripeisongdan(Request $request){

        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');

        $child = 'jinripeisongdan';
        $parent = 'shengchan';
        $current_page = 'jinripeisongdan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        // 配送任务根据配送员分组
        $milkman_delivery_plans_all = MilkManDeliveryPlan::where('station_id',$current_station_id)
            ->where('deliver_at',$deliver_date_str)
            ->wherebetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED])
            ->get();

        $milkman_info = array();

        $deliveryPlan = $milkman_delivery_plans_all->first();

        // 只有生成了配送列表之后才显示今日配送单
        if ($deliveryPlan && !DSDeliveryPlan::getDeliveryPlanGenerated($current_station_id, $deliveryPlan->order_product->product_id)) {
            return view('naizhan.shengchan.jinripeisongdan',[
                'pages'         =>$pages,
                'child'         =>$child,
                'parent'        =>$parent,
                'current_page'  =>$current_page,

                'milkman_info'  =>$milkman_info,
            ]);
        }

        $milkman_delivery_plans = $milkman_delivery_plans_all->groupBy(function ($sort) {
            return $sort->milkman_id;
        });

        foreach ($milkman_delivery_plans as $m => $dps_by_milkman) {
            $delivery_info = array();
            $comment = '';

            // 配送员的任务根据配送类型分组
            $order_by_types = $dps_by_milkman->groupBy(function ($sort) {
                return $sort->type;
            });

            foreach ($order_by_types as $o => $dbm) {

                // 订单配送
                if ($o == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER) {
                    // 配送任务根据订单分组
                    $regular_delivers = $dbm->groupBy(function ($sort) {
                        return $sort->order_id;
                    });

                    foreach ($regular_delivers as $r => $by_order_id) {
                        // 获取订单信息
                        $orderData = Order::find($r);

                        $products = array();
                        $is_changed = 0;
                        $delivery_type = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER;

                        $flag = 0;                  // 是否第一次配送
                        $box_install_count = 0;     // 奶箱安装数量

                        foreach ($by_order_id as $dp) {
                            $name = $dp->order_product->product->name;
                            $count = $dp->delivery_count;
                            $products[] = $name . '*' . $count;

                            if ($dp->plan_count != $dp->changed_plan_count)
                                $is_changed = 1;

                            $delivery_type = $dp->type;
                            $flag = $dp->flag;

                            if ($dp->isBoxInstall()) {
                                $box_install_count++;
                            }

                            $comment = $dp->comment;
                        }

                        $orderData['product'] = implode(',', $products);
                        if ($box_install_count > 0) {
                            $orderData['product'] = $orderData['product'] . ', 奶箱*' . $box_install_count;
                        }

                        $orderData['changed'] = $is_changed;
                        $orderData['delivery_type'] = $delivery_type;
                        $orderData['flag'] = $flag;
                        $orderData['comment_delivery'] = $comment;

                        // 添加到主数组
                        array_push($delivery_info, $orderData);
                    }
                } else {
                    $extra_delivers = $dbm->groupBy(function ($sort) {
                        return $sort->order_id;
                    });

                    foreach ($extra_delivers as $r => $by_order_id) {
                        // 获取自营项目信息
                        $orderData = SelfOrder::find($r);
                        $products = array();
                        $is_changed = 0;
                        $delivery_type = 1;

                        foreach ($by_order_id as $dp) {
                            $name = $dp->order_product->product->name;
                            $count = $dp->delivery_count;
                            $products[] = $name . '*' . $count;
                            $delivery_type = $dp->type;
                            $comment = $dp->comment;
                        }

                        $orderData['product'] = implode(',', $products);
                        $orderData['changed'] = $is_changed;
                        $orderData['delivery_type'] = $delivery_type;
                        $orderData['comment_delivery'] = $comment;

                        // 添加到主数组
                        array_push($delivery_info, $orderData);
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
            'pages'         =>$pages,
            'child'         =>$child,
            'parent'        =>$parent,
            'current_page'  =>$current_page,

            'milkman_info'  =>$milkman_info,
        ]);
    }


    public function undelivered_process($order_product_id, $delivered_count, $deliver_at)
    {
        $plan = MilkManDeliveryPlan::where('order_product_id', $order_product_id)
            ->where('deliver_at', $deliver_at)
            ->get()
            ->first();

        $delivery_count = $plan->delivery_count;

        $undelivered_count = $delivery_count - $delivered_count;

        $delivery_type = OrderProduct::find($order_product_id);
        $delivery_type->processExtraCount($undelivered_count);
    }


    /**
     * 打开配送反录页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPeisongfanru(Request $request){

        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        $current_factory_id = Auth::guard('naizhan')->user()->factory_id;

        $current_date_str = getCurDateString();

        // 配送日期，默认是今日
        $deliver_date_str = $request->input('current_date');
        if($deliver_date_str == ''){
            $deliver_date_str = getCurDateString();
        }

        // 页面信息
        $child = 'peisongfanru';
        $parent = 'shengchan';
        $current_page = 'peisongfanru';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        // 配送员列表
        $milkman = MilkMan::where('is_active',1)->where('station_id',$current_station_id)->get();

        // 配送员信息，默认是第一个
        $current_milkman = $request->input('milkman_id');
        if ($current_milkman == ''){
            $current_milkmans = MilkMan::where('is_active',1)->where('station_id',$current_station_id)->get()->first();
            if($current_milkmans != null){
                $current_milkman = $current_milkmans->id;
            }
            else{
                $current_milkman = '';
            }
        }

        // 是否已生成配送列表？
        $milkman_delivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)
            ->where('deliver_at', $deliver_date_str)
            ->where('type', '<>', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            ->wherebetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED])
            ->where('milkman_id',$current_milkman)
            ->get();

        $deliveryPlan = $milkman_delivery_plans->first();

        // 只有生成了配送列表之后才显示反录
        if ($deliveryPlan && !DSDeliveryPlan::getDeliveryPlanGenerated($current_station_id, $deliveryPlan->order_product->product_id)) {
            return view('naizhan.shengchan.peisongfanru',[
                'pages'                     =>$pages,
                'child'                     =>$child,
                'parent'                    =>$parent,
                'current_page'              =>$current_page,

                'delivery_info'             =>array(),
                'milkman'                   =>$milkman,
                'deliver_date'              =>$deliver_date_str,
                'current_date'              =>$current_date_str,
                'current_milkman'           =>$current_milkman,
                'bottle_types'              =>array(),
                'milkman_bottle_refunds'    =>array(),
                'is_todayrefund'            =>0,
            ]);
        }

        //
        // 查询瓶框数据
        //
        $station_addr = DeliveryStation::find($current_station_id)->address;
        $station_addr = explode(' ',$station_addr);
        $station_addr = $station_addr[0]." ".$station_addr[1]." ".$station_addr[2];

        $bottle_types = DB::select(DB::raw("select DISTINCT p.bottle_type from products p, productprice pp
                    where p.id = pp.product_id and p.factory_id = $current_factory_id and pp.sales_area LIKE '%$station_addr%'"));

        $milkman_bottle_refunds = MilkmanBottleRefund::where('milkman_id',$current_milkman)
            ->where('time',$deliver_date_str)
            ->get(['count','bottle_type']);

        $todays_milkman_bottle_refunds = MilkmanBottleRefund::where('milkman_id',$current_milkman)
            ->where('time',$current_date_str)
            ->get(['count','bottle_type']);

        $is_todayrefund = 0;
        foreach ($milkman_delivery_plans as $mdp){
            $is_todayrefund += $mdp->delivered_count;
        }

        foreach ($todays_milkman_bottle_refunds as $tbr){
            $is_todayrefund += $tbr->count;
        }

        //
        // 查询配送数据
        //
        $delivery_info = array();

        // 根据订单分类
        $mdp_by_order = $milkman_delivery_plans->groupBy(function ($sort){
            return $sort->order_id;
        });

        foreach ($mdp_by_order as $r=>$by_order_id){
            // 获取订单信息
            $orderData = SelfOrder::find($r);
            $products = array();
            $is_changed = 0;
            $delivery_type = 1;
            $milkboxinstall = 0;

            foreach($by_order_id as $pro=>$dp) {
                $name = $dp->order_product->product->name;
                $count = $dp->delivery_count;
                $products[$pro]['name'] = $name;
                $products[$pro]['count'] = $count;
                $products[$pro]['id'] = $dp->order_product->product->id;
                $products[$pro]['order_product_id'] = $dp->order_product_id;
                $products[$pro]['delivered_count'] = $dp->delivered_count;
                $products[$pro]['report'] = $dp->report;
                $products[$pro]['comment'] = $dp->comment;

                if($dp->plan_count != $dp->changed_plan_count)
                    $is_changed = 1;
                $delivery_type = $dp->type;

                $milk_man = $dp->milkman->name;

                if($dp->isBoxInstall())
                    $milkboxinstall = 1;
            }

            $orderData['product'] = $products;
            $orderData['changed'] = $is_changed;
            $orderData['delivery_type'] = $delivery_type;
            $orderData['milkman_name'] = $milk_man;
            $orderData['milkbox_install'] = $milkboxinstall;

            // 添加到主数组
            array_push($delivery_info, $orderData);
        }

        return view('naizhan.shengchan.peisongfanru',[
            'pages'                     =>$pages,
            'child'                     =>$child,
            'parent'                    =>$parent,
            'current_page'              =>$current_page,

            'delivery_info'             =>$delivery_info,
            'milkman'                   =>$milkman,
            'deliver_date'              =>$deliver_date_str,
            'current_date'              =>$current_date_str,
            'current_milkman'           =>$current_milkman,
            'bottle_types'              =>$bottle_types,
            'milkman_bottle_refunds'    =>$milkman_bottle_refunds,
            'is_todayrefund'            =>$is_todayrefund,
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

    /**
     * 保存反录
     * @param Request $request
     * @return mixed
     */
    public function confirmdeliveryPeisongfanru(Request $request){

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');

        $table_info = json_decode($request->getContent(),true);
        foreach ($table_info as $ti){
            $current_milkman_id = $ti['milkman_id'];
            $order_product_id = $ti['order_product_id'];
            $delivered_count = $ti['delivered_count'];
            $delivery_type = $ti['delivery_type'];
            // 这是反录情况内容，不是备注内容
            $report = $ti['report'];
            $order_id = $ti['order_id'];

            $delivered_count = preg_replace('/\s+/', '', $delivered_count);

            $milkmandeliverys = MilkManDeliveryPlan::where('deliver_at',$deliver_date_str)
                ->where('milkman_id',$current_milkman_id)
                ->where('type',$delivery_type)
                ->where('order_product_id',$order_product_id)
                ->wherebetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT])
                ->get()->first();

            $milkmandeliverys->delivered_count = $delivered_count;

            // 配送数量0是怎么处理，按照正常留长处理
//            if($delivered_count == 0){
//                $milkmandeliverys->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL;
//                $milkmandeliverys->cancel_reason = "配送取消";
//            }
//            else{
                $milkmandeliverys->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED;
//            }

            $milkmandeliverys->report = report;
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

                // 计算订单剩余金额
                $order = Order::find($order_id);
                $order->remaining_amount = $order->remaining_amount - $milkmandeliverys->delivered_count * $milkmandeliverys->product_price;
                $order->save();

                // 计算剩下数量
                $total_order_counts = count(
                    MilkManDeliveryPlan::where('order_id',$order_id)
                        ->whereBetween('status',[MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT])
                        ->get()
                );

                // 如果没有剩下的，订单变成已完成
                if($total_order_counts == 0){
                    $finished_order = Order::find($order_id);
                    $finished_order->status = Order::ORDER_FINISHED_STATUS;
                    $finished_order->save();

                    // 订单余额到客户账户里加回去
                    $customer = Customer::find($finished_order->customer_id);
                    $customer->remain_amount = $customer->remain_amount + $finished_order->remaining_amount;
                    $customer->save();
                }

                // 如果当天没有配送完，顺延处理
                if($milkmandeliverys->delivered_count != $milkmandeliverys->changed_plan_count){
                    $this->undelivered_process($milkmandeliverys->order_product_id,$milkmandeliverys->delivered_count,$milkmandeliverys->deliver_at);
                }
            }
        }
        return Response::json(['status'=>"success"]);
    }

    public function confirm(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->station_id;
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
