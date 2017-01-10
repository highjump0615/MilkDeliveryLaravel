<?php

namespace App\Http\Controllers;

use App\Model\DeliveryModel\DSDeliveryPlan;
use App\Model\FactoryModel\Factory;
use App\Model\FinanceModel\DSBusinessCreditBalanceHistory;
use App\Model\NotificationModel\DSNotification;
use App\Model\NotificationModel\FactoryNotification;
use App\Model\StationModel\DSBottleRefund;
use App\Model\StationModel\DSBoxRefund;
use App\Model\FactoryModel\FactoryBottleType;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\DSProductionPlan;
use App\Model\DeliveryModel\MilkManDeliveryPlan;
use App\Model\FactoryModel\FactoryBoxType;
use App\Model\OrderModel\Order;
use App\Model\ProductionModel\FactoryProductionPlan;
use App\Model\ProductModel\Product;
use App\Model\OrderModel\OrderProduct;

use App\Model\UserModel\Page;
use App\Model\UserModel\User;
use App\Model\SystemModel\SysLog;

use DateTime;
use DateTimeZone;
use Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Model\ProductModel\ProductPrice;
use App\Http\Requests;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class DSProductionPlanCtrl extends Controller
{
    public function showJihuaguanlinPage(Request $request){

        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        $start_date = $end_date = $request->input('current_date');

        $produce_Date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $produce_Date->add(\DateInterval::createFromDateString('tomorrow'));
        $produce_start_date = $produce_Date->format('Y-m-d');

        // 默认是今天倒数5天
        if ($start_date == '') {
            // 获取系统时间，第二天开始生产
            $end_date = $produce_start_date;

            $date = str_replace('-','/',$end_date);
            $start_date = date('Y-m-d',strtotime($date."-5 days"));
        }
        else {
            $date = str_replace('-','/',$start_date);
            $start_date = date('Y-m-d',strtotime($date."+1 days"));
            $end_date = $start_date;
        }

        $child = 'jihuaguanli';
        $parent = 'shengchan';
        $current_page = 'jihuaguanli';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        $dsplan = DSProductionPlan::where('station_id',$current_station_id)
            ->wherebetween('produce_start_at', [$start_date, $end_date])
            ->orderby('produce_start_at', 'desc')
            ->get();

        $dsplanResult = array();

        foreach ($dsplan as $dp) {
            $dateIndex = $dp->submit_at;

            if (isset($dsplanResult[$dateIndex])) {
                $dsplanResult[$dateIndex][] = $dp;
            }
            else {
                $dsplanResult[$dateIndex] = array($dp);
            }
        }

        // 是否已经提交
        $is_passed = count(DSProductionPlan::where('station_id',$current_station_id)
            ->where('produce_start_at', $produce_start_date)
            ->wherebetween('status',[DSProductionPlan::DSPRODUCTION_PRODUCE_CANCEL,DSProductionPlan::DSPRODUCTION_PRODUCE_FINNISHED])
            ->get());

        $alert_message='';
        if($is_passed > 0){
            $alert_message = '计划已经被牛奶厂接受。';
        }

        return view('naizhan.shengchan.jihuaguanli',[
            // 菜单关联信息
            'pages'         =>$pages,
            'child'         =>$child,
            'parent'        =>$parent,
            'current_page'  =>$current_page,

            // 计划信息
            'dsplan'        =>$dsplanResult,
            'alert_message' =>$alert_message,
            'current_date'  => $request->input('current_date'),
        ]);
    }

    public  function showTijiaojihuaPage(Request $request){

        $current_station_id = Auth::guard('naizhan')->user()->station_id;

        $current_station = DeliveryStation::find($current_station_id);
        $current_station_addr = $current_station->address;

        $child = 'jihuaguanli';
        $parent = 'shengchan';
        $current_page = 'tijiaojihua';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));

        $currentDate->add(\DateInterval::createFromDateString('tomorrow'));
        $currentDate_str = $currentDate->format('Y-m-d');

        $is_passed = count(DSProductionPlan::where('station_id',$current_station_id)
            ->where('produce_start_at',$currentDate_str)
            ->wherebetween('status',[DSProductionPlan::DSPRODUCTION_PRODUCE_CANCEL,DSProductionPlan::DSPRODUCTION_PRODUCE_FINNISHED])
            ->get());

        $is_sent = count(
            DSProductionPlan::where(function($query) use ($current_station_id, $currentDate_str) {
                $query->where('station_id',$current_station_id);
                $query->where('produce_start_at',$currentDate_str);
                $query->where('status',DSProductionPlan::DSPRODUCTION_SENT_PLAN);
            })->orWhere(function($query) use ($current_station_id, $currentDate_str) {
                $query->where('status',DSProductionPlan::DSPRODUCTION_PENDING_PLAN);
                $query->where('produce_start_at',$currentDate_str);
                $query->where('station_id',$current_station_id);
            })->get()
        );

        if($is_passed > 0){
            return redirect()->route('naizhan_shengchan_jihuaguanli')->with('alert_message','计划已经被牛奶厂接受。');
        }

        $product_list = Product::where('is_deleted','0')->get();

        foreach($product_list as $pl){
            $product_price = ProductPrice::priceTemplateFromAddress($pl->id, $current_station_addr);
            if($product_price == null)
                $pl["current_price"] = null;
            else
                $pl["current_price"] = $product_price->settle_price;

            $order_products = OrderProduct::where('product_id',$pl->id)->get();
            $total_count = 0;
            $total_money = 0;

            foreach($order_products as $op){
                $plan = MilkManDeliveryPlan::where('station_id',$current_station_id)
                    ->where(function($query) {
                        $query->where('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED);
                        $query->orwhere('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT);
                    })
                    ->where('produce_at',$currentDate_str)
                    ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
                    ->where('order_product_id',$op->id)
                    ->get()
                    ->first();

                if($plan == null){
                }
                else {
                    $total_count += $plan->plan_count;
                    $total_money += $plan->plan_count * $plan->product_price;
                }
            }
            $pl["total_count"] = $total_count;
            $pl["total_money"] = $total_money;

            if ($is_sent) {
                $current_delivery_plans = DSProductionPlan::where('produce_start_at', $currentDate_str)
                    ->where('station_id', $current_station_id)
                    ->where('product_id', $pl->id)
                    ->get()
                    ->first();

                $pl["ds_info"] = $current_delivery_plans;
            }

            // 计算出库日期
            $current_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
            $strOutDate = $current_date->format('Y-m-d');

            $nDuration = $pl->production_period/24 + 1;

            $strOutDate = str_replace('-','/', $strOutDate);
            $dateOut = date('Y-m-d',strtotime($strOutDate."+".$nDuration."days"));

            $pl["out_date"] = $dateOut;
        }

        return view('naizhan.shengchan.jihuaguanli.tijiaojihua',[
            'pages'                     =>$pages,
            'child'                     =>$child,
            'parent'                    =>$parent,
            'current_page'              =>$current_page,
            'product_list'              =>$product_list,
            'current_station_status'    =>$current_station,
            'is_sent'                   =>$is_sent,
        ]);
    }

    /**
     * 提交计划
     * @param Request $request
     * @return mixed
     */
    public function storeTijiaojihuaPlan(Request $request) {

        $current_station_id = $this->getCurrentStationId();

        $produce_start_at = getNextDateString();
        $current_date_str = getCurDateString();

        $milkmandeliveryplans = MilkManDeliveryPlan::where('station_id',$current_station_id)->where('produce_at',$produce_start_at)->get();
        foreach ($milkmandeliveryplans as $mp){
            if($mp->status == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED){
                $mp->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT;
                $checkOrders = Order::find($mp->order_id);
                if($checkOrders->status == Order::ORDER_PASSED_STATUS){
                    $checkOrders->status = Order::ORDER_ON_DELIVERY_STATUS;
                    $checkOrders->save();
                }
                $mp->save();
            }
        }

        $table_info = json_decode($request->getContent(),true);
        foreach ($table_info as $ti){
            $product_id = $ti['product_id'];
            $order_count = $ti['order_count'];
            $retail = $ti['retail'];
            $test_drink = $ti['test_drink'];
            $group_sale = $ti['group_sale'];
            $channel_sale = $ti['channel_sale'];
            $subtotal_count = $ti['subtotal_count'];
            $subtotal_money = $ti['subtotal_money'];
            $status = $ti['status'];

            $production_period = Product::find($product_id)->production_period;

            $product_price = ProductPrice::priceTemplateFromAddress($product_id, DeliveryStation::find($current_station_id)->address);

            if($product_price == null)
                $current_product_price = 0;
            else
                $current_product_price = $product_price->settle_price;

            if($production_period == 24){
                $current_date=$produce_start_at;
            }
            elseif ($production_period > 24){
                $produce_period = strval($production_period/24 - 1);
                $date = str_replace('-','/',$produce_start_at);
                $current_date = date('Y-m-d',strtotime($date."+".$produce_period."days"));
            }

            $dsproduction_plan = new DSProductionPlan;
            $dsproduction_plan->station_id = $current_station_id;
            $dsproduction_plan->product_id = $product_id;
            $dsproduction_plan->order_count = $order_count;
            $dsproduction_plan->retail = $retail;
            $dsproduction_plan->test_drink = $test_drink;
            $dsproduction_plan->group_sale = $group_sale;
            $dsproduction_plan->channel_sale = $channel_sale;
            $dsproduction_plan->subtotal_count = $subtotal_count;
            $dsproduction_plan->subtotal_money = $subtotal_money;
            $dsproduction_plan->status = $status;
            $dsproduction_plan->settle_product_price = $current_product_price;
            $dsproduction_plan->produce_start_at = $produce_start_at;
            $dsproduction_plan->produce_end_at = $current_date;
            $dsproduction_plan->save();
        }

        //
        // 添加账单历史
        //
        $delivery_station = DeliveryStation::find($current_station_id);

        $transaction_history_info = DB::select(DB::raw("select sum(retail * settle_product_price) as retail_amount, sum(test_drink * settle_product_price) as test_amount , 
sum(group_sale * settle_product_price) as group_amount,sum(channel_sale * settle_product_price) as channel_amount
                from dsproductionplan where produce_start_at = :produce_start_at and station_id = :station_id"),
            array('produce_start_at'=>$produce_start_at, 'station_id'=>$current_station_id));
        $sent_amount = 0;
        foreach ($transaction_history_info as $th){
            if($th->retail_amount !=null){
                $balancehistory = new DSBusinessCreditBalanceHistory;
                $balancehistory->station_id = $current_station_id;
                $balancehistory->type = DSBusinessCreditBalanceHistory::DSBCBH_OUT_STATION_RETAIL_BUSINESS;
                $balancehistory->io_type = DSBusinessCreditBalanceHistory::DSBCBH_OUT;
                $balancehistory->amount = $th->retail_amount;
                $balancehistory->return_amount = 0;
                $balancehistory->save();
                $sent_amount += $th->retail_amount;
            }
            if($th->test_amount !=null){
                $balancehistory = new DSBusinessCreditBalanceHistory;
                $balancehistory->station_id = $current_station_id;
                $balancehistory->type = DSBusinessCreditBalanceHistory::DSBCBH_OUT_TRY_TO_DRINK_OR_GIFT;
                $balancehistory->io_type = DSBusinessCreditBalanceHistory::DSBCBH_OUT;
                $balancehistory->amount = $th->test_amount;
                $balancehistory->return_amount = 0;
                $balancehistory->save();
                $sent_amount += $th->test_amount;
            }
            if($th->group_amount !=null){
                $balancehistory = new DSBusinessCreditBalanceHistory;
                $balancehistory->station_id = $current_station_id;
                $balancehistory->type = DSBusinessCreditBalanceHistory::DSBCBH_OUT_GROUP_BUY_BUSINESS;
                $balancehistory->io_type = DSBusinessCreditBalanceHistory::DSBCBH_OUT;
                $balancehistory->amount = $th->group_amount;
                $balancehistory->return_amount = 0;
                $balancehistory->save();
                $sent_amount += $th->group_amount;
            }
            if($th->channel_amount !=null){
                $balancehistory = new DSBusinessCreditBalanceHistory;
                $balancehistory->station_id = $current_station_id;
                $balancehistory->type = DSBusinessCreditBalanceHistory::DSBCBH_OUT_CHANNEL_SALES_OPERATIONS;
                $balancehistory->io_type = DSBusinessCreditBalanceHistory::DSBCBH_OUT;
                $balancehistory->amount = $th->channel_amount;
                $balancehistory->return_amount = 0;
                $balancehistory->save();
                $sent_amount += $th->channel_amount;
            }

            $delivery_station->business_credit_balance = $delivery_station->business_credit_balance - $th->retail_amount - $th->test_amount -$th->group_amount -$th->channel_amount;
            $delivery_station->save();
            $business_balance = $delivery_station->business_credit_balance;
        }

        // 添加奶站通知
        $notification = new NotificationsAdmin();
        $notification->sendToStationNotification($current_station_id,
            DSNotification::CATEGORY_TRANSACTION,
            "您本次提交的订单计划",
            "您本次提交的订单计划，已从自营账户中扣款".$sent_amount."元。");

        // 添加奶厂通知
        $notification->sendToFactoryNotification($delivery_station->factory_id,
            FactoryNotification::CATEGORY_PRODUCE,
            "奶站已提交了今天的生产计划",
            $delivery_station->name . "奶站已提交了今天的生产计划。");

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_STATION, '计划管理', SysLog::SYSLOG_OPERATION_SUBMIT_PLAN);

        return Response::json(['business_balance'=>$business_balance]);
    }

    public function modifyTijiaojihuaPlan(Request $request) {
        $current_station_id = Auth::guard('naizhan')->user()->station_id;

        $produce_start_at = getNextDateString();
        $current_date_str = getCurDateString();

        $current_dsdelivery_plans = DSProductionPlan::where('produce_start_at',$produce_start_at)->where('station_id',$current_station_id)->get();
        foreach ($current_dsdelivery_plans as $cd){
            $cd->delete();
        }
        $refund_money = DSBusinessCreditBalanceHistory::whereDate('created_at', '=', $current_date_str)
            ->where('station_id',$current_station_id)
            ->where('io_type',DSBusinessCreditBalanceHistory::DSBCBH_OUT)
            ->get();

        $refund_amount = 0;
        foreach ($refund_money as $rm){
            $refund_amount += $rm->amount;
            $rm->delete();
        }

        $delivery_transation = DeliveryStation::find($current_station_id);
        $delivery_transation->business_credit_balance = $delivery_transation->business_credit_balance + $refund_amount;
        $delivery_transation->save();

        $table_info = json_decode($request->getContent(),true);
        foreach ($table_info as $ti){
            $product_id = $ti['product_id'];
            $order_count = $ti['order_count'];
            $retail = $ti['retail'];
            $test_drink = $ti['test_drink'];
            $group_sale = $ti['group_sale'];
            $channel_sale = $ti['channel_sale'];
            $subtotal_count = $ti['subtotal_count'];
            $subtotal_money = $ti['subtotal_money'];
            $status = $ti['status'];

            $production_period = Product::find($product_id)->production_period;
            $product_price = ProductPrice::priceTemplateFromAddress($product_id, DeliveryStation::find($current_station_id)->address);

            if($product_price == null)
                $current_product_price = 0;
            else
                $current_product_price = $product_price->settle_price;

            if($production_period == 24){
                $current_date=$produce_start_at;
            }
            elseif ($production_period > 24){
                $produce_period = strval($production_period/24 - 1);
                $date = str_replace('-','/',$produce_start_at);
                $current_date = date('Y-m-d',strtotime($date."+".$produce_period."days"));
            }

            $dsproduction_plan = new DSProductionPlan;
            $dsproduction_plan->station_id = $current_station_id;
            $dsproduction_plan->product_id = $product_id;
            $dsproduction_plan->order_count = $order_count;
            $dsproduction_plan->retail = $retail;
            $dsproduction_plan->test_drink = $test_drink;
            $dsproduction_plan->group_sale = $group_sale;
            $dsproduction_plan->channel_sale = $channel_sale;
            $dsproduction_plan->subtotal_count = $subtotal_count;
            $dsproduction_plan->subtotal_money = $subtotal_money;
            $dsproduction_plan->status = $status;
            $dsproduction_plan->settle_product_price = $current_product_price;
            $dsproduction_plan->produce_start_at = $produce_start_at;
            $dsproduction_plan->produce_end_at = $current_date;
            $dsproduction_plan->save();
        }

        $transaction_history_info = DB::select(DB::raw("select sum(retail * settle_product_price) as retail_amount, sum(test_drink * settle_product_price) as test_amount , 
sum(group_sale * settle_product_price) as group_amount,sum(channel_sale * settle_product_price) as channel_amount
                from dsproductionplan where produce_start_at = :produce_start_at and station_id = :station_id"),
            array('produce_start_at'=>$produce_start_at, 'station_id'=>$current_station_id));
        foreach ($transaction_history_info as $th){
            if($th->retail_amount !=null){
                $balancehistory = new DSBusinessCreditBalanceHistory;
                $balancehistory->station_id = $current_station_id;
                $balancehistory->type = DSBusinessCreditBalanceHistory::DSBCBH_OUT_STATION_RETAIL_BUSINESS;
                $balancehistory->io_type = DSBusinessCreditBalanceHistory::DSBCBH_OUT;
                $balancehistory->amount = $th->retail_amount;
                $balancehistory->return_amount = 0;
                $balancehistory->save();
            }
            if($th->test_amount !=null){
                $balancehistory = new DSBusinessCreditBalanceHistory;
                $balancehistory->station_id = $current_station_id;
                $balancehistory->type = DSBusinessCreditBalanceHistory::DSBCBH_OUT_TRY_TO_DRINK_OR_GIFT;
                $balancehistory->io_type = DSBusinessCreditBalanceHistory::DSBCBH_OUT;
                $balancehistory->amount = $th->test_amount;
                $balancehistory->return_amount = 0;
                $balancehistory->save();
            }
            if($th->group_amount !=null){
                $balancehistory = new DSBusinessCreditBalanceHistory;
                $balancehistory->station_id = $current_station_id;
                $balancehistory->type = DSBusinessCreditBalanceHistory::DSBCBH_OUT_GROUP_BUY_BUSINESS;
                $balancehistory->io_type = DSBusinessCreditBalanceHistory::DSBCBH_OUT;
                $balancehistory->amount = $th->group_amount;
                $balancehistory->return_amount = 0;
                $balancehistory->save();
            }
            if($th->channel_amount !=null){
                $balancehistory = new DSBusinessCreditBalanceHistory;
                $balancehistory->station_id = $current_station_id;
                $balancehistory->type = DSBusinessCreditBalanceHistory::DSBCBH_OUT_CHANNEL_SALES_OPERATIONS;
                $balancehistory->io_type = DSBusinessCreditBalanceHistory::DSBCBH_OUT;
                $balancehistory->amount = $th->channel_amount;
                $balancehistory->return_amount = 0;
                $balancehistory->save();
            }

            $delivery_transation = DeliveryStation::find($current_station_id);
            $delivery_transation->business_credit_balance = $delivery_transation->business_credit_balance - $th->retail_amount - $th->test_amount -$th->group_amount -$th->channel_amount;
            $delivery_transation->save();
            $business_balance = $delivery_transation->business_credit_balance;
        }

        return Response::json(['business_balance'=>$business_balance]);
    }

    /**
     * 打开奶站计划审核页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPlanTableinFactory(){
        $current_factory_id = $this->getCurrentFactoryId(true);

        $child = 'naizhanjihuashenhe';
        $parent = 'shengchan';
        $current_page = 'naizhanjihuashenhe';
        $pages = Page::where('backend_type','2')->where('parent_page', '0')->get();

        $currentDate_str = getNextDateString();

        // 获取所有产品信息
        $products = Product::where('factory_id',$current_factory_id)
            ->where('is_deleted',0)
            ->get(['id','simple_name','production_period']);

        foreach($products as $p){
            $plan_info = DSProductionPlan::where('produce_start_at', $currentDate_str)
                ->where('status','<>',DSProductionPlan::DSPRODUCTION_PRODUCE_CANCEL)
                ->where('product_id',$p->id)
                ->get();

            $plan_count = 0;
            foreach($plan_info as $pi){
                $plan_count+=$pi->subtotal_count;
            }
            $p["plan_count"] = $plan_count;

            $mfproductionplan = FactoryProductionPlan::where('factory_id',$current_factory_id)
                ->where('product_id',$p->id)
                ->where('start_at', $currentDate_str)
                ->get()
                ->first();
//            $mfproductionplan = FactoryProductionPlan::where('factory_id',1)->where('product_id',$p->id)->where('time',$currentDate_str)->get()->first();

            if ($mfproductionplan != null){
                if($mfproductionplan->status == FactoryProductionPlan::FACTORY_PRODUCE_CANCELED){
                    $p["isfactory_ordered"] = FactoryProductionPlan::FACTORY_PRODUCE_CANCELED;
                }
                else{
                    $p["isfactory_ordered"] = FactoryProductionPlan::FACTORY_PRODUCE_PLAN_SENT;
                    $p["produce_count"] = $mfproductionplan->count;
                }
            }
            else{
                $p["isfactory_ordered"] = 0;
            }

            // 计算当天订单数量
            $total_ordered_count = 0;

            $order_product = OrderProduct::where('product_id',$p->id)->get(['id']);
            foreach($order_product as $op){
                // 只考虑提交过的订单
                $changed_counts = MilkManDeliveryPlan::where('produce_at', $currentDate_str)
                    ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
                    ->where('order_product_id',$op->id)
                    ->where(function($query){
                        $query->where('status','>=',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT);
                        $query->orwhere('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL);
                    })
                    ->get(['changed_plan_count']);

                if ($changed_counts != null){
                    foreach($changed_counts as $cc){
                        $total_ordered_count += $cc->changed_plan_count;
                    }
                }
            }

            $plan_ordered_count = 0;
            $plan_ordered = DSProductionPlan::where('product_id',$p->id)
                ->where('status','>=',DSProductionPlan::DSPRODUCTION_SENT_PLAN)
                ->where('produce_start_at', $currentDate_str)
                ->get();

            foreach($plan_ordered as $po){
                $plan_ordered_count += $po->order_count;
            }

            $p["change_order_amount"] = $total_ordered_count-$plan_ordered_count;
        }

        $stations = DeliveryStation::where('is_deleted',0)->where('factory_id',$current_factory_id)->get();

        foreach($stations as $si) {
            $areas = explode(" ",$si->address);
            $si["area"] = $areas[0];
            $station_plan = DSProductionPlan::where('station_id', $si->id)->where('produce_start_at', $currentDate_str)->get();
            $si["station_plan"] = $station_plan;
            $si["plan_status"] = count($station_plan);
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '奶站计划审核', SysLog::SYSLOG_OPERATION_VIEW);

        return view('gongchang.shengchan.naizhanjihuashenhe',[
            'pages'                 =>$pages,
            'child'                 =>$child,
            'parent'                =>$parent,
            'current_page'          =>$current_page,
            'getStations_info'      =>$stations,
            'products'              =>$products,
            'current_factory_id'    =>$current_factory_id,
        ]);
    }

    /*Save total amount of Produce Plan*/
    public function SaveforProduce(Request $request){
        $current_factory_id = $this->getCurrentFactoryId(true);

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $currentDate->add(\DateInterval::createFromDateString('tomorrow'));

        $currentDate_str = $currentDate->format('Y-m-d');
        $product_id = $request->input('product_id');
        $count = $request->input('count');
        $produce_period = $request->input('produce_period');

        if($produce_period == 1){
            $produce_end_date = $currentDate_str;
        }
        else{
            $produce_period = strval($produce_period);
            $produce_period =$produce_period-1;
            $date = str_replace('-','/',$currentDate_str);
            $produce_end_date = date('Y-m-d',strtotime($date."+".$produce_period."days"));
        }

        $dsproductionplans = DSProductionPlan::where('status',DSProductionPlan::DSPRODUCTION_SENT_PLAN)
            ->where('produce_start_at',$currentDate_str)
            ->where('product_id',$product_id)
            ->get();

        foreach ($dsproductionplans as $dp){
            $dp->status = DSProductionPlan::DSPRODUCTION_PRODUCE_FINNISHED;
            $dp->save();
        }
//        $notification = new DSNotification();
//        $notification->sendToStationNotification($ds->id,7,"生产计划接受","您发送的计划已被接受。");

        $mfproductionplans = new FactoryProductionPlan;
        $mfproductionplans->factory_id = $current_factory_id;
        $mfproductionplans->product_id = $product_id;
        $mfproductionplans->count = $count;
        $mfproductionplans->start_at = $currentDate_str;
        $mfproductionplans->end_at = $produce_end_date;
        $mfproductionplans->status = FactoryProductionPlan::FACTORY_PRODUCE_PLAN_SENT;
        $mfproductionplans->save();

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '奶站计划审核', SysLog::SYSLOG_OPERATION_PRODUCE_OK);
        
        return Response::json(['status'=>'success']);
    }

    /*Cancel produce*/
    public function StopforProduce(Request $request){

        $plan_status = null;

        $current_factory_id = $this->getCurrentFactoryId(true);

        $currentDate_str = getCurDateString();
        $produce_date = getNextDateString();

        $product_id = $request->input('product_id');
        $count = $request->input('count');
        $produce_period = $request->input('produce_period');

        if($produce_period == 1){
            $produce_end_date = $currentDate_str;
        }
        else{
            $produce_period = strval($produce_period);
            $produce_period = $produce_period-1;
            $date = str_replace('-','/',$currentDate_str);
            $produce_end_date = date('Y-m-d',strtotime($date."+".$produce_period."days"));
        }

        $notification = new NotificationsAdmin();
        $deliverystations = DeliveryStation::where('factory_id',$current_factory_id)->get(['id', 'business_credit_balance']);

        foreach ($deliverystations as $ds){
            $plan = DSProductionPlan::where('station_id',$ds->id)
                ->where('product_id',$product_id)
                ->where('produce_start_at',$produce_date)
                ->get();

            $refund_amount = 0;
            foreach($plan as $p){
                $p->status = DSProductionPlan::DSPRODUCTION_PRODUCE_CANCEL;
                $p->save();

                $refunds = DSBusinessCreditBalanceHistory::where('station_id', $p->station_id)
                    ->whereDate('created_at', '=', $currentDate_str)
                    ->where('io_type', DSBusinessCreditBalanceHistory::DSBCBH_OUT)
                    ->get();

                foreach ($refunds as $rf){
                    if($rf->type == DSBusinessCreditBalanceHistory::DSBCBH_OUT_STATION_RETAIL_BUSINESS){
//                        $rf->amount = $rf->amount - $p->retail * $p->settle_product_price;
                        $rf->return_amount = $p->retail * $p->settle_product_price;
                        $refund_amount += $p->retail * $p->settle_product_price;
                    }
                    elseif ($rf->type == DSBusinessCreditBalanceHistory::DSBCBH_OUT_TRY_TO_DRINK_OR_GIFT){
//                        $rf->amount = $rf->amount = $p->test_drink * $p->settle_product_price;
                        $rf->return_amount = $p->test_drink * $p->settle_product_price;
                        $refund_amount += $p->test_drink * $p->settle_product_price;
                    }
                    elseif ($rf->type == DSBusinessCreditBalanceHistory::DSBCBH_OUT_GROUP_BUY_BUSINESS){
//                        $rf->amount = $rf->amount = $p->group_sale * $p->settle_product_price;
                        $rf->return_amount = $p->group_sale * $p->settle_product_price;
                        $refund_amount += $p->group_sale * $p->settle_product_price;
                    }
                    elseif ($rf->type == DSBusinessCreditBalanceHistory::DSBCBH_OUT_CHANNEL_SALES_OPERATIONS){
//                        $rf->amount = $rf->amount = $p->channel_sale * $p->settle_product_price;
                        $rf->return_amount = $p->channel_sale * $p->settle_product_price;
                        $refund_amount += $p->channel_sale * $p->settle_product_price;
                    }
                    $rf->save();
                }
            }

            $ds->business_credit_balance = $ds->business_credit_balance + $refund_amount;
            $ds->save();

            $milk_type = Product::find($product_id)->name;

            // 添加奶站通知
            $notification->sendToStationNotification($ds->id,
                DSNotification::CATEGORY_ACCOUNT,
                "生产取消",
                $milk_type . " 生产取消。");
        }

        $order_product = OrderProduct::where('product_id',$product_id)->get(['id']);
        foreach($order_product as $op){
            $plan_status = MilkManDeliveryPlan::where('produce_at',$produce_date)
                ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
                ->where('order_product_id',$op->id)
                ->get();

            if($plan_status != null){
                foreach($plan_status as $ps){
                    $ps->delivered_count = 0;
                    $ps->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL;
                    $ps->cancel_reason = MilkManDeliveryPlan::DP_CANCEL_PRODUCE;
                    $ps->save();

                    $orders_change = new DSDeliveryPlanCtrl;
                    $orders_change->undelivered_process($ps);
                }
            }
        }

        $mfproductionplans = new FactoryProductionPlan;
        $mfproductionplans->factory_id = $current_factory_id;
        $mfproductionplans->product_id = $product_id;
        $mfproductionplans->count = $count;
        $mfproductionplans->start_at = $produce_date;
        $mfproductionplans->end_at = $produce_end_date;
        $mfproductionplans->status = FactoryProductionPlan::FACTORY_PRODUCE_CANCELED;
        $mfproductionplans->save();

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '奶站计划审核', SysLog::SYSLOG_OPERATION_PRODUCE_CANCEL);

        return Response::json(['status'=>'success']);
    }

    /**
     * 给待审核计划通过
     * @param Request $request
     * @return mixed
     */
    public function determineStationPlan(Request $request){
        $station_id = $request->input('station_id');

        $current_date_str = getNextDateString();

        if($station_id == null)
            return Response::json(['status'=>"没有奶站！"]);

        $dsproductionplans = DSProductionPlan::where('station_id',$station_id)
            ->where('produce_start_at',$current_date_str)
            ->where('status',DSProductionPlan::DSPRODUCTION_PENDING_PLAN)
            ->get();

        foreach ($dsproductionplans as $dsp){
            $dsp->status = DSProductionPlan::DSPRODUCTION_SENT_PLAN;
            $dsp->save();
        }

        return Response::json(['status'=>"successfully_called"]);
    }
    
    public function cancelStationPlan(Request $request){
        $station_id = $request->input('station_id');

        $current_date_str = getCurDateString();
        $produce_date_str = getNextDateString();

        if($station_id == null)
            return Response::json(['status'=>"没有奶站！"]);

        $dsproductionplans = DSProductionPlan::where('station_id',$station_id)->where('produce_start_at',$produce_date_str)->where('status',DSProductionPlan::DSPRODUCTION_PENDING_PLAN)->get();
        foreach ($dsproductionplans as $dsp){
            $dsp->status = DSProductionPlan::DSPRODUCTION_PRODUCE_CANCEL;
            $dsp->save();
        }

        $refund = DSBusinessCreditBalanceHistory::whereDate('created_at', '=', $current_date_str)
            ->where('station_id',$station_id)
            ->where('io_type', DSBusinessCreditBalanceHistory::DSBCBH_OUT)
            ->get();

        $refund_amount = 0;
        foreach ($refund as $rf){
            $refund_amount += $rf->amount;
            $rf->return_amount = $rf->amount;
        }
        $dstation = DeliveryStation::find($station_id);
        $dstation->business_credit_balance = $dstation->business_credit_balance + $refund_amount;
        $dstation->save();

        $milkmandeliveryplans = MilkManDeliveryPlan::where('station_id',$station_id)->where('produce_at',$produce_date_str)->get();
        foreach ($milkmandeliveryplans as $mp){
            $mp->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL;
            $mp->cancel_reason = MilkManDeliveryPlan::DP_CANCEL_PRODUCE;
            $mp->delivered_count = 0;
            $mp->save();

            $order_changes = new DSDeliveryPlanCtrl;
            $order_changes->undelivered_process($mp);
        }

        return Response::json(['status'=>"successfully_called!"]);
    }

    /**
     * 打开奶站配送管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showNaizhanpeisongPage(Request $request){
        $current_factory_id = $this->getCurrentFactoryId(true);

        $child = 'naizhanpeisong';
        $parent = 'shengchan';
        $current_page = 'naizhanpeisong';
        $pages = Page::where('backend_type','2')->where('parent_page', '0')->get();

        $deliver_date_str = getCurDateString();
        $current_date_str = getPrevDateString();

        $products = Product::where('factory_id',$current_factory_id)
            ->where('is_deleted',0)
            ->get(['id','simple_name']);

        foreach ($products as $p) {
            $plan_info = DSProductionPlan::where('produce_end_at',$current_date_str)
                ->where('status','>=',DSProductionPlan::DSPRODUCTION_PASSED_PLAN)
                ->where('product_id',$p->id)
                ->get();

            $plan_count = 0;
            foreach($plan_info as $pi){
                $plan_count += $pi->subtotal_count;
            }
            $p["plan_count"] = $plan_count;

            $factory_plan = FactoryProductionPlan::where('factory_id',$current_factory_id)
                ->where('product_id',$p->id)
                ->where('end_at',$current_date_str)
                ->get(['count'])
                ->first();

            if($factory_plan != null){
                $p["produce_count"] = $factory_plan->count;
            }
//            $total_ordered_count = 0;
//
//            $order_product = OrderProduct::where('product_id',$p->id)->get(['id']);
//            foreach($order_product as $op){
//                $changed_count = MilkManDeliveryPlan::where('deliver_at',$deliver_date_str)->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)->
//                where('order_product_id',$op->id)->where('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT)->get(['changed_plan_count']);
//                if($changed_count != null){
//                    foreach($changed_count as $cc){
//                        $total_ordered_count += $cc->changed_plan_count;
//                    }
//                }
//            }
//
//            $plan_ordered_count = 0;
//            $plan_ordered = DSProductionPlan::where('product_id',$p->id)->where('produce_end_at',$current_date_str)->where('status',DSProductionPlan::DSPRODUCTION_PASSED_PLAN)->get();
//            foreach($plan_ordered as $po){
//                $plan_ordered_count += $po->order_count;
//            }
//            $p["change_order_amount"] = $total_ordered_count-$plan_ordered_count;
        }

        $stations = DeliveryStation::where('factory_id',$current_factory_id)->where('is_deleted',0)->get();
        $nPageCount = 25;
        $nCurPageCount = 0;

        foreach ($stations as $si) {
            $areas = explode(" ",$si->address);
            $si["area"] = $areas[0];
            $station_plans = DSProductionPlan::where('station_id', $si->id)
                ->where('produce_end_at', $current_date_str)
                ->where('status','>=',DSProductionPlan::DSPRODUCTION_PASSED_PLAN)
                ->orderby('product_id')
                ->get();

            foreach($station_plans as $sp) {
                $product_id = $sp->product_id;

                $total_changed = 0;
                $delivery_plans = MilkManDeliveryPlan::where('deliver_at', $deliver_date_str)
                    ->where('station_id', $si->id)
                    ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
                    ->where(function($query){
                        $query->where('status','>=',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT);
                        $query->orwhere('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL);
                    })
                    ->get();

                foreach($delivery_plans as $dp) {
                    if($dp->order->deliveryStation->id == $si->id && $dp->order_product->product->id == $product_id) {
                        //calc process
                        $total_changed += $dp->changed_plan_count;
                    }
                }

                $diff = $total_changed - $sp->order_count;
                if($diff>0){
                    $sp["diff"] = '+'.$diff;
                }
                else
                    $sp["diff"] = $diff;
            }

            $si["station_plan"] = $station_plans;
            $si["plan_status"] = (count($station_plans) > 0);

            // 计算记录行数，超过制定的数量就
            if ($nCurPageCount + count($station_plans) <= $nPageCount) {
                $nCurPageCount += count($station_plans);
            }
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '奶站配送管理', SysLog::SYSLOG_OPERATION_VIEW);

        return view('gongchang.shengchan.naizhanpeisong',[
            // 页面信息
            'pages'                     =>$pages,
            'child'                     =>$child,
            'parent'                    =>$parent,
            'current_page'              =>$current_page,

            // 奶站信息
            'DeliveryStations_info'     =>$stations,

            // 生产结果信息
            'products'                  =>$products,

            // 一个页面的记录数量, 防止一个奶站的信息被分页表示
            'page_count'                =>$nCurPageCount
        ]);
    }

    /**
     * 发货确认
     * @param Request $request
     * @return mixed
     */
    public function updateNaizhanPlanTable(Request $request){

        $current_station_id = $request->input('station_id');
        $product_id = $request->input('product_id');
        $actual_count = $request->input('actual_count');
        $product_name = Product::find($product_id)->name;

        $dsplans = DSProductionPlan::where('station_id',$current_station_id)
            ->where('product_id',$product_id)
            ->where('produce_end_at', getPrevDateString())
            ->where('status',DSProductionPlan::DSPRODUCTION_PRODUCE_FINNISHED)
            ->get()
            ->first();

        if($dsplans != null) {
            // 清空Whitespace
            $actual_count = preg_replace('/\s+/', '', $actual_count);

            $dsplans->actual_count = $actual_count;
            $dsplans->status = DSProductionPlan::DSPRODUCTION_PRODUCE_SENT;
            $dsplans->save();
        }

        $notification = new NotificationsAdmin();
        $notification->sendToStationNotification($current_station_id,
            DSNotification::CATEGORY_ACCOUNT,
            "奶厂已发货",
            $product_name . ":" . $actual_count . " 奶厂已发货！");

        return Response::json($dsplans);
    }

    public function showNaizhanshouhuoquerenPage(Request $request){
        $current_factory_id = $this->getCurrentFactoryId(true);

        $child = 'naizhanpeisong';
        $parent = 'shengchan';
        $current_page = 'naizhanshouhuoqueren';

        $input_date_str = $request->input('date');
        $station_name = $request->input('station_name');
        if($station_name == null){
            $station_name = '';
        }
        $station_number = $request->input('station_number');
        if($station_number == null){
            $station_number = '';
        }
        $address = $request->input('address');
        if($address == null){
            $address = '';
        }

        $pages = Page::where('backend_type','2')->where('parent_page', '0')->get();
        $produced_date = getPrevDateString();

        if ($input_date_str != null){
            $date = str_replace('-','/',$input_date_str);
            $produced_date = date('Y-m-d',strtotime($date."-1 days"));
        }
        else{
            $input_date_str = getCurDateString();
        }

        $stations = DeliveryStation::where('factory_id',$current_factory_id)->where('is_deleted',0)->get();
//        $stations = DeliveryStation::where('is_delete',0)->where('factory_id',1)->get();
        foreach($stations as $si) {
            $areas = explode(" ",$si->address);
            $si["area"] = $areas[0];
            $station_plans = DSProductionPlan::where('station_id', $si->id)->where('produce_end_at', $produced_date)->
            where('status','>=',DSProductionPlan::DSPRODUCTION_PRODUCE_FINNISHED)->get();

            foreach($station_plans as $station_plan) {
                $product_id = $station_plan->product_id;
                $total_changed = 0;
                $delivery_plans = MilkManDeliveryPlan::where('deliver_at', $input_date_str)
                    ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
                    ->where('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT)
                    ->get();

                foreach($delivery_plans as $dp) {
                    if($dp->order->station->id == $si->id && $dp->order_product->product->id == $product_id) {
                        //calc process
                        $total_changed += $dp->changed_plan_count;
                    }
                }

                $diff = $total_changed - $station_plan->order_count;
                if($diff>0){
                    $station_plan["diff"] = '+'.$diff;
                }
                else
                    $station_plan["diff"] = $diff;
            }
            $si["station_plan"] = $station_plans;
            $si["plan_status"] = (count($station_plans) > 0);
        }

        return view('gongchang.shengchan.naizhanpeisong.naizhanshouhuoqueren',[
            'pages'=>$pages,
            'child'=>$child,
            'parent'=>$parent,
            'current_page'=>$current_page,
            'DSPlan_info'=>$stations,
            'current_date'=>$input_date_str,
            'station_name'=>$station_name,
            'station_number'=>$station_number,
            'address'=>$address,
        ]);
    }

    /**
     * 打开打印出库单
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showDayinchukuchan(Request $request){

        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;

        // 页面信息
        $child = 'naizhanpeisong';
        $parent = 'shengchan';
        $current_page = 'dayinchukuchan';
        $pages = Page::where('backend_type','2')->where('parent_page', '0')->get();

        $station_name = $request->input('station_name');
        if($station_name == null){
            $station_name = '';
        }
        $station_number = $request->input('station_number');
        if($station_number == null){
            $station_number = '';
        }
        $address = $request->input('address');
        if($address == null){
            $address = '';
        }

        $current_date = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $current_date_str = $current_date->format('Y-m-d');
        $current_date->add(\DateInterval::createFromDateString('yesterday'));
        $produced_date = $current_date->format('Y-m-d');

        $input_date_str = $request->input('date');
        if($input_date_str != null){
            $date = str_replace('-','/',$input_date_str);
            $produced_date = date('Y-m-d',strtotime($date."-1 days"));
        }
        else{
            $input_date_str = $current_date_str;
        }

        $station = DeliveryStation::where('factory_id',$current_factory_id)
            ->where('is_deleted',0)
            ->where('status',Factory::FACTORY_STATUS_ACTIVE)
            ->where('address','LIKE','%'.$address.'%')
            ->where('name','LIKE','%'.$station_name.'%')
            ->where('number','LIKE','%'.$station_number.'%')
            ->get(['id','name']);

        foreach ($station as $st){
            $st['station_plan'] = DSProductionPlan::where('station_id', $st->id)
                ->where('produce_end_at', $produced_date)
                ->where('status','>=',DSProductionPlan::DSPRODUCTION_PRODUCE_FINNISHED)
                ->get();

            $st['mfbottle_type'] = FactoryBottleType::where('is_deleted',0)->where('factory_id',$current_factory_id)->get();
            $st['mfbox_type'] = FactoryBoxType::where('is_deleted',0)->where('factory_id',$current_factory_id)->get();
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '打印出库单', SysLog::SYSLOG_OPERATION_VIEW);

        return view('gongchang.shengchan.naizhanpeisong.dayinchukuchan',[
            'pages'             =>$pages,
            'child'             =>$child,
            'parent'            =>$parent,

            'current_page'      =>$current_page,
            'current_date'      =>$input_date_str,

            'station'           =>$station,
            'station_name'      =>$station_name,
            'station_number'    =>$station_number,
            'address'           =>$address,
//            'station_plan'=>$station_plan,
//            'mfbottle_type'=>$mfbottle_type,
        ]);
    }

    /**
     * 打开奶站签收计划
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showNaizhanQianshoujihua(Request $request){
        $current_station_id = $this->getCurrentStationId();
        $current_factory_id = $this->getCurrentFactoryId(false);

        $current_date_str = getPrevDateString();

        $child = 'qianshoujihua';
        $parent = 'shengchan';
        $current_page = 'qianshoujihua';
        $pages = Page::where('backend_type',Page::NAIZHAN)->where('parent_page', '0')->orderby('order_no')->get();

        $dsplan = DSProductionPlan::where('station_id',$current_station_id)
            ->where('produce_end_at',$current_date_str)
            ->where('status','>=',DSProductionPlan::DSPRODUCTION_PRODUCE_FINNISHED)
            ->orderby('product_id')
            ->get();

        $refund_date_str = getCurDateString();

        $station_addr = DeliveryStation::find($current_station_id)->address;
        $station_addr = explode(' ',$station_addr);
        $station_addr = $station_addr[0]." ".$station_addr[1]." ".$station_addr[2];

        $bottle_types = DB::select(DB::raw("select DISTINCT p.bottle_type from products p, productprice pp
                    where p.id = pp.product_id and p.factory_id = $current_factory_id and pp.sales_area LIKE '%$station_addr%'"));

        $box_types = DB::select(DB::raw("select DISTINCT p.basket_spec from products p, productprice pp
                    where p.id = pp.product_id and p.factory_id = $current_factory_id and pp.sales_area LIKE '%$station_addr%'"));

        $bottle_refund = DSBottleRefund::where('station_id',$current_station_id)->where('time',$refund_date_str)->get();
        $box_refund = DSBoxRefund::where('station_id',$current_station_id)->where('time',$refund_date_str)->get();
        
        // 是否已签收
        $received_count = 0;
        foreach ($dsplan as $dp){
            if ($dp->status == DSProductionPlan::DSPRODUCTION_PRODUCE_RECEIVED) {
                $received_count += $dp->confirm_count;
            }
        }

        return view('naizhan.shengchan.qianshoujihua',[
            'pages'         =>$pages,
            'child'         =>$child,
            'parent'        =>$parent,
            'current_page'  =>$current_page,

            'dsplan'        =>$dsplan,
            'fbottle'       =>$bottle_types,
            'fbox'          =>$box_types,
            'bottle_refund' =>$bottle_refund,
            'box_refund'    =>$box_refund,
            'sent_status'   =>$received_count,
        ]);
    }

    public function confirm_Plan_count(Request $request){

        $station_id = $this->getCurrentStationId();
        $product_id = $request->input('product_id');

        $confirm_count = $request->input('confirm_count');

        $current_date_str = getPrevDateString();

        $dsplan = DSProductionPlan::where('station_id',$station_id)
            ->where('produce_end_at',$current_date_str)
            ->where('product_id',$product_id)
            ->where('status',DSProductionPlan::DSPRODUCTION_PRODUCE_SENT)
            ->get()
            ->first();

        if ($dsplan != null){
            $dsplan->confirm_count = $confirm_count;
            $dsplan->status = DSProductionPlan::DSPRODUCTION_PRODUCE_RECEIVED;
            $dsplan->save();
        }

        return count($dsplan);
    }

    public function refund_BB(Request $request){

        $current_station_id = $this->getCurrentStationId();

        $types = $request->input('types');
        $object_type = $request->input('object_type');
        $return_to_factory = $request->input('return_to_factory');

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $current_date_str = $currentDate->format('Y-m-d');

        // 奶瓶
        if($types == 1){
            $bottle_refund = DSBottleRefund::where('time',$current_date_str)
                ->where('station_id',$current_station_id)
                ->where('bottle_type',$object_type)
                ->get()
                ->first();

            if($bottle_refund != null){
                $bottle_refund->return_to_factory = $return_to_factory;
                $bottle_refund->end_store = $bottle_refund->end_store - $return_to_factory;
                $bottle_refund->save();
            }
            else{
                $dsbottle_refunds = new DSBottleRefund;
                $dsbottle_refunds->station_id = $current_station_id;
                $dsbottle_refunds->bottle_type = $object_type;
                $dsbottle_refunds->return_to_factory = $return_to_factory;
                $dsbottle_refunds->time = $current_date_str;
                $dsbottle_refunds->save();
            }
        }
        // 奶框
        elseif($types == 2){
            $box_refund = DSBoxRefund::where('time',$current_date_str)
                ->where('station_id',$current_station_id)
                ->where('box_type',$object_type)
                ->get()
                ->first();

            if($box_refund != null){
                $box_refund->return_to_factory = $return_to_factory;
                $box_refund->end_store = $box_refund->end_store - $return_to_factory;
                $box_refund->save();
            }
            else{
                $dsbox_refunds = new DSBoxRefund;
                $dsbox_refunds->station_id = $current_station_id;
                $dsbox_refunds->box_type = $object_type;
                $dsbox_refunds->return_to_factory = $return_to_factory;
                $dsbox_refunds->time = $current_date_str;
                $dsbox_refunds->save();
            }
        }
        return Response::json(['status'=>'passed']);
    }
}
