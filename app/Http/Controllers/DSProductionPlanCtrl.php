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
    /**
     * 奶厂是否已确认生产计划
     * @param $date
     * @return bool
     */
    private function isStationPlanAccepted($date) {
        $nStationId = $this->getCurrentStationId();
        $strDateStart = getNextDateString($date);

        $nCount = DSProductionPlan::where('station_id', $nStationId)
            ->where('produce_start_at', $strDateStart)
            ->wherebetween('status',[DSProductionPlan::DSPRODUCTION_PRODUCE_CANCEL,DSProductionPlan::DSPRODUCTION_PRODUCE_RECEIVED])
            ->count();

        return $nCount > 0 ? true : false;
    }

    /**
     * 打开计划管理页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showJihuaguanlinPage(Request $request){

        $current_station_id = $this->getCurrentStationId();
        $start_date = $end_date = $request->input('current_date');

        $produce_start_date = getNextDateString();

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

        //
        // 基于提交日期分组
        //
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

        //
        // 查看是否已经提交
        //
        $alert_message='';
        if ($this->isStationPlanAccepted(getCurDateString())) {
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

    /**
     * 打开提交计划页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public  function showTijiaojihuaPage(Request $request){

        $current_station_id = $this->getCurrentStationId();

        $current_station = DeliveryStation::find($current_station_id);
        $current_station_addr = $current_station->address;

        $child = 'jihuaguanli';
        $parent = 'shengchan';
        $current_page = 'tijiaojihua';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        $currentDateStr = $request->input('current_date');
        if (empty($currentDateStr)) {
            $currentDateStr = getCurDateString();
        }

        // 如果计划已接受，跳转到计划管理页面
        if ($this->isStationPlanAccepted($currentDateStr)){
            return redirect()->route('naizhan_shengchan_jihuaguanli')->with('alert_message','计划已经被牛奶厂接受。');
        }

        $currentDate_str = getNextDateString($currentDateStr);

        $is_sent = DSProductionPlan::where('station_id',$current_station_id)
            ->where('produce_start_at',$currentDate_str)
            ->count();

        // 奶品数组
        $product_list = $current_station->factory->products;
        foreach ($product_list as $pl) {
            // 获取奶品的价格
            $pl["current_price"] = $pl->getSettlePrice($current_station_addr);
            $pl["total_count"] = 0;
            $pl["total_money"] = 0;

            if ($is_sent) {
                $current_delivery_plans = DSProductionPlan::where('produce_start_at', $currentDate_str)
                    ->where('station_id', $current_station_id)
                    ->where('product_id', $pl->id)
                    ->first();

                $pl["ds_info"] = $current_delivery_plans;
            }

            // 计算出库日期
            $dateCurrent = getDateFromString($currentDateStr);
            $strOutDate = $dateCurrent->format('Y-m-d');

            $nDuration = $pl->production_period/24 + 1;

            $strOutDate = str_replace('-','/', $strOutDate);
            $dateOut = date('Y-m-d',strtotime($strOutDate."+".$nDuration."days"));

            $pl["out_date"] = $dateOut;
        }

        // 获取配送明细
        $plans = MilkManDeliveryPlan::with('orderProduct')
            ->where('station_id',$current_station_id)
            ->where(function($query) {
                $query->where('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED);
                $query->orwhere('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT);
            })
            ->where('produce_at',$currentDate_str)
            ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            ->get();

        foreach ($plans as $plan) {

            // 是否已获取
            for ($i = 0; $i < count($product_list); $i++) {
                if ($product_list[$i]->id == $plan->orderProduct->product_id) {
                    break;
                }
            }

            // 奶品数组里不存在，退出
            if ($i >= count($product_list)) {
                continue;
            }

            $product_list[$i]["total_count"] += $plan->plan_count;
            $product_list[$i]["total_money"] += $plan->plan_count * $plan->product_price;
        }

        return view('naizhan.shengchan.jihuaguanli.tijiaojihua',[
            // 菜单关联信息
            'pages'                     =>$pages,
            'child'                     =>$child,
            'parent'                    =>$parent,
            'current_page'              =>$current_page,

            // 页面内容
            'product_list'              =>$product_list,
            'current_station_status'    =>$current_station,
            'is_sent'                   =>$is_sent,
            'current_date'              =>$currentDateStr,
        ]);
    }

    /**
     * 添加奶站计划
     * @param Request $request
     * @param $addNotification bool 是否添加通知
     * @return double 自营业务余额
     */
    private function addStationPlan(Request $request, $addNotification = false) {

        $current_station_id = $this->getCurrentStationId();
        $produce_start_at = getNextDateString($request->input('date'));
        $dataPlan = $request->input('plan_data');

        $delivery_station = DeliveryStation::find($current_station_id);

        foreach ($dataPlan as $ti){
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

            $product_price = ProductPrice::priceTemplateFromAddress($product_id, $delivery_station->address);

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
        }

        if ($addNotification) {
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
        }

        return $delivery_station->business_credit_balance;
    }

    /**
     * 提交计划
     * @param Request $request
     * @return mixed
     */
    public function storeTijiaojihuaPlan(Request $request) {

        // 如果计划已接受，直接退出，不刷新点击确定会出现这种情况
        if ($this->isStationPlanAccepted($request->input('date'))){
            return Response::json(['message' => '计划已经被牛奶厂接受。'], 400);
        }

        $current_station_id = $this->getCurrentStationId();

        $produce_start_at = getNextDateString($request->input('date'));

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

        $business_balance = $this->addStationPlan($request, true);

        return Response::json(['business_balance'=>$business_balance]);
    }

    /**
     * 修改提交计划
     * @param Request $request
     * @return mixed
     */
    public function modifyTijiaojihuaPlan(Request $request) {

        // 如果计划已接受，直接退出，不刷新点击确定会出现这种情况
        if ($this->isStationPlanAccepted($request->input('date'))){
            return Response::json(['message' => '计划已经被牛奶厂接受。'], 400);
        }

        $current_station_id = $this->getCurrentStationId();

        $produce_start_at = getNextDateString($request->input('date'));
        $current_date_str = getCurDateString();

        // 删除已有的计划记录
        DSProductionPlan::where('produce_start_at',$produce_start_at)
            ->where('station_id',$current_station_id)
            ->delete();

        //
        // 返还自营账户金额
        //
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

        $business_balance = $this->addStationPlan($request);

        return Response::json(['business_balance'=>$business_balance]);
    }

    /**
     * 打开奶站计划审核页面
     * @param $request Request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPlanTableinFactory(Request $request){
        $current_factory_id = $this->getCurrentFactoryId(true);

        $child = 'naizhanjihuashenhe';
        $parent = 'shengchan';
        $current_page = 'naizhanjihuashenhe';
        $pages = Page::where('backend_type','2')->where('parent_page', '0')->get();

        // 获取时间段
        $strDate = $request->input('date');

        if (empty($strDate)) {
            $strDate = getCurDateString();
        }

        $strDateReal = getNextDateString($strDate);

        // 获取所有产品信息
        $products = Product::where('factory_id',$current_factory_id)
            ->where('is_deleted',0)
            ->get(['id','simple_name','production_period']);

        $plan_info = DSProductionPlan::whereHas('station', function($query) use ($current_factory_id) {
                $query->where('factory_id', $current_factory_id);
            })
            ->where('status','>=',DSProductionPlan::DSPRODUCTION_SENT_PLAN)
            ->where('produce_start_at', $strDateReal)
            ->get();

        // 只考虑提交过的订单
        $deliveryPlans = MilkManDeliveryPlan::whereHas('station', function($query) use ($current_factory_id) {
                $query->where('factory_id', $current_factory_id);
            })
            ->where('produce_at', $strDateReal)
            ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            ->where(function($query){
                $query->where('status','>=',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT);
                $query->orwhere('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL);
            });

        $changed_counts = $deliveryPlans->join('orderproducts', 'milkmandeliveryplan.order_product_id', '=', 'orderproducts.id')
            ->groupBy('orderproducts.product_id')
            ->selectRaw('orderproducts.product_id as pid, sum(milkmandeliveryplan.changed_plan_count) as plan_count')
            ->get();

        $planInfoProduct = $plan_info->groupBy('product_id');

        foreach($products as $p){

            $plan_count = 0;
            // 是否存在该奶品的奶站生产计划
            if (!empty($planInfoProduct[$p->id])) {
                foreach ($planInfoProduct[$p->id] as $pi) {
                    // 不考虑审核不通过的
                    if ($pi->status == DSProductionPlan::DSPRODUCTION_PRODUCE_CANCEL) {
                        continue;
                    }

                    $plan_count += $pi->subtotal_count;
                }
            }
            $p["plan_count"] = $plan_count;

            //
            // 查询已生产的状态和数量
            //
            $mfproductionplan = FactoryProductionPlan::where('factory_id',$current_factory_id)
                ->where('product_id',$p->id)
                ->where('start_at', $strDateReal)
                ->first();

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

            // 是否存在该奶品的配送明细
            foreach ($changed_counts as $cc) {
                // 只考虑本奶品
                if ($cc['pid'] != $p->id) {
                    continue;
                }

                $total_ordered_count = $cc['plan_count'];
            }

            $plan_ordered_count = 0;

            // 是否存在该奶品的奶站生产计划
            if (!empty($planInfoProduct[$p->id])) {
                foreach ($planInfoProduct[$p->id] as $pi) {
                    $plan_ordered_count += $pi->order_count;
                }
            }

            $p["change_order_amount"] = $total_ordered_count-$plan_ordered_count;
        }

        $stations = DeliveryStation::where('is_deleted',0)
            ->where('factory_id',$current_factory_id)
            ->get();

        $planInfoStation = $plan_info->groupBy('station_id');

        foreach($stations as $si) {
            $areas = explode(" ",$si->address);
            $si["area"] = $areas[0];

            //
            // 基于提交日期分组
            //
            $dsplanResult = array();
            $dsplanResult['count'] = 0;
            $dsplanResult['data'] = array();

            // 是否存在该奶站的奶站生产计划
            if (!empty($planInfoStation[$si->id])) {
                foreach ($planInfoStation[$si->id] as $po) {
                    $dateIndex = $po->submit_at;

                    if (isset($dsplanResult['data'][$dateIndex])) {
                        $dsplanResult['data'][$dateIndex][] = $po;
                    } else {
                        $dsplanResult['data'][$dateIndex] = array($po);
                    }

                    $dsplanResult['count']++;
                }
            }

            $si["station_plan"] = $dsplanResult;
            $si["plan_status"] = count($dsplanResult);
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '奶站计划审核', SysLog::SYSLOG_OPERATION_VIEW);

        return view('gongchang.shengchan.naizhanjihuashenhe',[
            'pages'                 =>$pages,
            'child'                 =>$child,
            'parent'                =>$parent,
            'current_page'          =>$current_page,

            'current_date'          =>$strDate,
            'getStations_info'      =>$stations,
            'products'              =>$products,
            'current_factory_id'    =>$current_factory_id,
        ]);
    }

    /**
     * 生产确认
     * @param Request $request
     * @return mixed
     */
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

        $current_date_str = getNextDateString($request->input('date'));

        if($station_id == null)
            return Response::json(['status'=>"没有奶站！"]);

        //
        // 更新状态
        //
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

    /**
     * 给待审核计划不通过
     * @param Request $request
     * @return mixed
     */
    public function cancelStationPlan(Request $request){
        $station_id = $request->input('station_id');

        $current_date_str = $request->input('date');
        $produce_date_str = getNextDateString($request->input('date'));

        if($station_id == null)
            return Response::json(['status'=>"没有奶站！"]);

        //
        // 更新状态
        //
        $dsproductionplans = DSProductionPlan::where('station_id',$station_id)
            ->where('produce_start_at',$produce_date_str)
            ->where('status',DSProductionPlan::DSPRODUCTION_PENDING_PLAN)
            ->get();

        foreach ($dsproductionplans as $dsp){
            $dsp->status = DSProductionPlan::DSPRODUCTION_PRODUCE_CANCEL;
            $dsp->save();
        }

        //
        // todo: 返还该计划的自营扣款，待处理
        //
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
     * 保存实际生产量
     * @param Request $request
     * @return mixed
     */
    public function saveProducedCount(Request $request) {

        // 在session保存日期
        $strDate = $request->session()->get('date');
        if (empty($strDate)) {
            return Response::json(null, 400);
        }

        // 解析上传的参数数组
        $table_info = json_decode($request->getContent(),true);

        foreach ($table_info as $ti){
            $product_id = $ti['id'];
            $real_count = $ti['count'];

            // 保存 & 更新数据库
            FactoryProductionPlan::where('product_id', $product_id)
                ->where('end_at', getPrevDateString($strDate))
                ->update(['real_count' => $real_count]);
        }

        return Response::json(['status' => 'success']);
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

        // 获取时间段
        $deliver_date_str = $request->input('date');
        if (empty($deliver_date_str)) {
            $deliver_date_str = getCurDateString();
        }

        // 在session保存日期
        $request->session()->put('date', $deliver_date_str);

        $current_date_str = getPrevDateString($deliver_date_str);

        $products = Product::where('factory_id',$current_factory_id)
            ->where('is_deleted',0)
            ->get(['id','simple_name']);

        $plan_info = DSProductionPlan::where('produce_end_at',$current_date_str)
            ->where('status','>=',DSProductionPlan::DSPRODUCTION_PASSED_PLAN)
            ->orderby('product_id')
            ->get();

        $planInfoProduct = $plan_info->groupBy('product_id');

        foreach ($products as $p) {

            $plan_count = 0;

            // 是否存在该奶品的奶站生产计划
            if (!empty($planInfoProduct[$p->id])) {
                foreach ($planInfoProduct[$p->id] as $pi) {
                    $plan_count += $pi->subtotal_count;
                }
            }
            $p["plan_count"] = $plan_count;

            $factory_plan = FactoryProductionPlan::where('factory_id',$current_factory_id)
                ->where('product_id',$p->id)
                ->where('end_at',$current_date_str)
                ->first(['count', 'real_count']);

            if($factory_plan != null){
                // 初始计划量和实际生产量是一样
                $p["produce_count"] = $p["real_count"] = $factory_plan->count;

                if ($factory_plan->real_count) {
                    $p["real_count"] = $factory_plan->real_count;
                }
            }
        }

        $stations = DeliveryStation::where('factory_id',$current_factory_id)
            ->where('is_deleted',0)
            ->get();

        $delivery_plans = MilkManDeliveryPlan::with('orderProduct')
            ->where('deliver_at', $deliver_date_str)
            ->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            ->where(function($query){
                $query->where('status','>=',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT);
                $query->orwhere('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL);
            })
            ->get()
            ->groupBy('station_id');

        $planInfoStation = $plan_info->groupBy('station_id');

        $nPageCount = 25;
        $nCurPageCount = 0;

        foreach ($stations as $si) {
            $areas = explode(" ",$si->address);
            $si["area"] = $areas[0];
            $station_plans = array();

            // 是否存在该奶站的奶站生产计划
            if (!empty($planInfoStation[$si->id])) {
                foreach ($planInfoStation[$si->id] as $sp) {

                    $product_id = $sp->product_id;

                    $total_changed = 0;

                    // 是否存在该奶品的配送明细
                    if (!empty($planInfoProduct[$p->id])) {
                        foreach ($delivery_plans[$si->id] as $dp) {
                            if ($dp->orderProduct->product_id == $product_id) {
                                //calc process
                                $total_changed += $dp->changed_plan_count;
                            }
                        }
                    }

                    $diff = $total_changed - $sp->order_count;
                    if ($diff > 0) {
                        $sp["diff"] = '+' . $diff;
                    } else
                        $sp["diff"] = $diff;

                    $station_plans[] = $sp;
                }
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

            'current_date'              =>$deliver_date_str,

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
            ->where('produce_end_at', getPrevDateString($request->input('date')))
            ->where('status',DSProductionPlan::DSPRODUCTION_PRODUCE_FINNISHED)
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
     * @param $stationId int 奶站id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showDayinchukuchanWithStation(Request $request, $stationId) {

        $current_factory_id = $this->getCurrentFactoryId(true);

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

        $current_date_str = getCurDateString();
        $produced_date = getPrevDateString();

        $input_date_str = $request->input('date');
        if($input_date_str != null){
            $date = str_replace('-','/',$input_date_str);
            $produced_date = date('Y-m-d',strtotime($date."-1 days"));
        }
        else{
            $input_date_str = $current_date_str;
        }

        if ($stationId > 0) {
            $station = DeliveryStation::find($stationId);
        }
        else {
            $station = DeliveryStation::where('factory_id', $current_factory_id)
                ->where('is_deleted', 0)
                ->where('status', Factory::FACTORY_STATUS_ACTIVE)
                ->where('address', 'LIKE', '%' . $address . '%')
                ->where('name', 'LIKE', '%' . $station_name . '%')
                ->where('number', 'LIKE', '%' . $station_number . '%')
                ->first(['id', 'name']);
        }

        $station['station_plan'] = DSProductionPlan::where('station_id', $station->id)
            ->where('produce_end_at', $produced_date)
            ->where('status','>=',DSProductionPlan::DSPRODUCTION_PRODUCE_FINNISHED)
            ->get();

        // 发货人、车牌号
        $strSenderName = '';
        $strCarNum = '';

//        $station['mfbottle_type'] = FactoryBottleType::where('is_deleted',0)->where('factory_id',$current_factory_id)->get();
        $aryBoxType = array();

        foreach ($station['station_plan'] as $dp) {
            $bExist = false;

            // 是否已在数组里
            foreach ($aryBoxType as $bx) {
                if ($bx['box']->id == $dp->product->box->id) {
                    $bExist = true;
                    break;
                }
            }

            // 添加到数组
            if (!$bExist) {
                $aryBoxType[] = array(
                    'box' => $dp->product->box,
                    'count' => $dp->box_count,
                );
            }

            $strSenderName = $dp->sender_name;
            $strCarNum = $dp->car_number;
        }

        $station['mfbox_type'] = $aryBoxType;

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

            'sender_name'       =>$strSenderName,
            'car_number'        =>$strCarNum,
//            'station_plan'=>$station_plan,
//            'mfbottle_type'=>$mfbottle_type,
        ]);
    }

    /**
     * 打开打印出库单
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showDayinchukuchan(Request $request){

        // 没有指定的奶站、搜索
        return $this->showDayinchukuchanWithStation($request, 0);
    }

    /**
     * 保存打印出库单内容
     * @param Request $request
     * @return mixed
     */
    public function saveOutStock(Request $request) {

        $nStationId = $request->input('station_id');
        $strSenderName = $request->input('sender_name');
        $strCarNum = $request->input('car_num');
        $aryBoxData = $request->input('box_data');

        // 如果没输入奶框数据，写成空数组，以便保证程序的正常运行
        if (empty($aryBoxData)) {
            $aryBoxData = array();
        }

        // 添加是否已保存的标记
        foreach ($aryBoxData as $index => $boxData) {
            $boxData['saved'] = false;
            $aryBoxData[$index] = $boxData;
        }

        // 查询奶站生产计划数据
        $dsplans = DSProductionPlan::where('station_id', $nStationId)
            ->where('produce_end_at', getPrevDateString())
            ->where('status', '>', DSProductionPlan::DSPRODUCTION_PRODUCE_FINNISHED)
            ->get();

        foreach ($dsplans as $dp) {
            // 保存发货人、车牌号、状态
            $dp->sender_name = $strSenderName;
            $dp->car_number = $strCarNum;

            // 保存瓶装
            foreach ($aryBoxData as $index => $boxData) {
                // 不考虑已保存的
                if ($boxData['saved']) {
                    continue;
                }

                if ($boxData['id'] == $dp->product->box->id) {
                    $dp->box_count = $boxData['count'];

                    $boxData['saved'] = true;
                    $aryBoxData[$index] = $boxData;
                }
            }

            $dp->save();
        }

        return Response::json(['status' => 'success']);
    }

    /**
     * 打开奶站签收计划
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showNaizhanQianshoujihua(Request $request){
        $current_station_id = $this->getCurrentStationId();
        $current_factory_id = $this->getCurrentFactoryId(false);

        $currentDateStr = $request->input('current_date');
        if (empty($currentDateStr)) {
            $currentDateStr = getCurDateString();
        }

        $current_date_str = getPrevDateString($currentDateStr);

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
                $received_count++;
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
            'current_date'  =>$currentDateStr,
        ]);
    }

    /**
     * 奶站签收 - 数量
     * @param Request $request
     * @return int
     */
    public function confirm_Plan_count(Request $request){

        $station_id = $this->getCurrentStationId();
        $product_id = $request->input('product_id');

        $confirm_count = $request->input('confirm_count');

        $current_date_str = getPrevDateString($request->input('date'));

        //
        // 更新奶站计划
        //
        $dsplan = DSProductionPlan::where('station_id',$station_id)
            ->where('produce_end_at',$current_date_str)
            ->where('product_id',$product_id)
            ->where('status',DSProductionPlan::DSPRODUCTION_PRODUCE_SENT)
            ->first();

        if ($dsplan != null){
            $dsplan->confirm_count = $confirm_count;
            $dsplan->status = DSProductionPlan::DSPRODUCTION_PRODUCE_RECEIVED;
            $dsplan->save();

            //
            // 添加/更新库存数据
            //

            // 获取今日以前最近的
            $dsdpLatest = DSDeliveryPlan::where('station_id', $station_id)
                ->where('product_id', $product_id)
                ->where('deliver_at', '<=', $request->input('date'))
                ->orderby('deliver_at', 'desc')
                ->first();

            if (empty($dsdpLatest) || $dsdpLatest->deliver_at != $request->input('date')) {
                // 添加/更新当日库存数据
                $dsdp = new DSDeliveryPlan;
                $dsdp->station_id = $station_id;
                $dsdp->deliver_at = $request->input('date');
                $dsdp->product_id = $product_id;

                if (!empty($dsdpLatest)) {
                    $dsdp->remain = $dsdpLatest->remain_final;
                }

                $dsdpLatest = $dsdp;
            }

            $dsdpLatest->remain += $confirm_count;
            $dsdpLatest->save();

            // 更新最新库存数据，有可能是今日以后或今日
            $dsdpLatestNew = DSDeliveryPlan::where('station_id', $station_id)
                ->where('product_id', $product_id)
                ->orderby('deliver_at', 'desc')
                ->first();

            if ($dsdpLatestNew->id != $dsdpLatest->id) {
                $dsdpLatestNew->remain += $confirm_count;
                $dsdpLatestNew->save();
            }
        }

        return count($dsplan);
    }

    /**
     * 奶站签收 - 返厂平框
     * @param Request $request
     * @return mixed
     */
    public function refund_BB(Request $request){

        $current_station_id = $this->getCurrentStationId();

        $types = $request->input('types');
        $object_type = $request->input('object_type');
        $return_to_factory = $request->input('return_to_factory');

        $current_date_str = $request->input('date');

        // 奶瓶
        if($types == 1){
            $bottle_refund = DSBottleRefund::where('time',$current_date_str)
                ->where('station_id',$current_station_id)
                ->where('bottle_type',$object_type)
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
