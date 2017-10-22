<?php

namespace App\Http\Controllers;

use App\Model\BasicModel\ProvinceData;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\FactoryModel\Factory;
use App\Model\FinanceModel\DSBusinessCreditBalanceHistory;
use App\Model\FinanceModel\DSCalcBalanceHistory;
use App\Model\BasicModel\Customer;
use App\Model\OrderModel\Order;
use App\Model\OrderModel\OrderProperty;
use App\Model\OrderModel\OrderChanges;
use App\Model\FinanceModel\DSDeliveryCreditBalanceHistory;
use App\Model\ProductModel\Product;
use App\Model\UserModel\Page;
use Illuminate\Http\Request;
use DateTime;
use DateTimeZone;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Model\SystemModel\SysLog;
use App\Model\UserModel\User;


class TotalStatisticsCtrl extends Controller
{
    /**
     * 打开奶品配送统计
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function naipinpeisong(Request $request){

        $child = 'naipinpeisong';
        $parent = 'tongji';
        $current_page = 'naipinpeisong';

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $factory_name = $request->input('factory_name');
        $factory_number = $request->input('factory_number');

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $currentMonth_str = $currentDate->format('Y-m-01');
        $currentDate_str = $currentDate->format('Y-m-d');
        if($start_date == null){
            $start_date = $currentMonth_str;
        }
        if($end_date == null){
            $end_date = $currentDate_str;
        }
        if($factory_name == null){
            $factory_name = '';
        }
        if($factory_number == null){
            $factory_number = '';
        }

        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();

        $factories = Factory::where('status',Factory::FACTORY_STATUS_ACTIVE)->where('name','LIKE','%'.$factory_name.'%')->where('name','LIKE','%'.$factory_number.'%')->get();
        foreach ($factories as $fa){
            $stations = DeliveryStation::where('factory_id',$fa->id)->where('is_deleted',0)->where('status',Factory::FACTORY_STATUS_ACTIVE)->get();
            $fa['order_total'] = 0;
            $fa['retail'] = 0;
            $fa['gift'] = 0;
            $fa['group'] = 0;
            $fa['channel'] = 0;
            foreach ($stations as $st){

                $dscalcbal = DSCalcBalanceHistory::where('station_id',$st->id)
                    ->where('io_type',DSCalcBalanceHistory::DSCBH_TYPE_OUT)
                    ->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<=', $end_date)
                    ->get();

                foreach ($dscalcbal as $dc){
                    $fa['order_total'] += $dc->amount;
                }

                $businesscalcbal = DSBusinessCreditBalanceHistory::where('station_id',$st->id)
                    ->where('io_type',DSBusinessCreditBalanceHistory::DSBCBH_OUT)
                    ->whereDate('created_at', '>=', $start_date)
                    ->whereDate('created_at', '<=', $end_date)
                    ->get();

                foreach ($businesscalcbal as $bcb){
                    if($bcb->type == DSBusinessCreditBalanceHistory::DSBCBH_OUT_STATION_RETAIL_BUSINESS){
                        $fa['retail'] += $bcb->amount - $bcb->return_amount;
                    }
                    if($bcb->type == DSBusinessCreditBalanceHistory::DSBCBH_OUT_TRY_TO_DRINK_OR_GIFT){
                        $fa['gift'] += $bcb->amount - $bcb->return_amount;
                    }
                    if($bcb->type == DSBusinessCreditBalanceHistory::DSBCBH_OUT_GROUP_BUY_BUSINESS){
                        $fa['group'] += $bcb->amount - $bcb->return_amount;
                    }
                    if($bcb->type == DSBusinessCreditBalanceHistory::DSBCBH_OUT_CHANNEL_SALES_OPERATIONS){
                        $fa['channel'] += $bcb->amount - $bcb->return_amount;
                    }
                }
            }
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_ADMIN, '奶品配送统计', SysLog::SYSLOG_OPERATION_VIEW);

        return view('zongpingtai.tongji.naipinpeisong', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'factory_name' => $factory_name,
            'factory_number' =>$factory_number,
            'start_date' =>$start_date,
            'end_date'=>$end_date,
            'factories_bal' =>$factories,
        ]);
    }

    /**
     * 打开订单类型统计
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dingdanleixing(Request $request){
        $child = 'dingdanleixing';
        $parent = 'tongji';
        $current_page = 'dingdanleixing';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();

        $factory_name = $request->input('factory_name');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $currentMonth_str = $currentDate->format('Y-m-01');
        $currentDate_str = $currentDate->format('Y-m-d');
        if($start_date == null){
            $start_date = $currentMonth_str;
        }
        if($end_date == null){
            $end_date = $currentDate_str;
        }
        if($factory_name == null)
            $factory_name = "";
        $factories = Factory::where('name','LIKE','%'.$factory_name.'%')->where('is_deleted',0)->where('status',Factory::FACTORY_STATUS_ACTIVE)->get();
        foreach ($factories as $st) {
            $product_info = Product::where('factory_id',$st->id)->where('is_deleted',0)->get();
            $st['t_yuedan'] = 0;
            $st['t_jidan'] = 0;
            $st['t_banniandan'] = 0;
            $st['r_yuedan'] = 0;
            $st['r_jidan'] = 0;
            $st['r_banniandan'] = 0;
            $st['t_yuedan_amount'] = 0;
            $st['t_jidan_amount'] = 0;
            $st['t_banniandan_amount'] = 0;
            $st['r_yuedan_amount'] = 0;
            $st['r_jidan_amount'] = 0;
            $st['r_banniandan_amount'] = 0;
            foreach ($product_info as $pi){
                $order_info = DB::select(DB::raw("select sum(op.total_count) as type_total_count, sum(op.total_amount) as type_total_amount, sum(op.avg) as type_avg_amount,op.order_type 
                from orderproducts op, orders o
                where op.order_id = o.id  and op.product_id = :product_id and o.ordered_at BETWEEN '$start_date' and '$end_date' group by op.order_type"),
                    array('product_id'=>$pi->id));

                $pi['t_yuedan'] = 0;
                $pi['t_jidan'] = 0;
                $pi['t_banniandan'] = 0;
                foreach ($order_info as $ci){
                    if($ci->order_type == 1){
                        $pi['t_yuedan'] = $ci->type_total_count;
                        $st['t_yuedan'] += $ci->type_total_count;
                        $st['t_yuedan_amount'] += $ci->type_total_amount;
                    }
                    elseif($ci->order_type == 2){
                        $pi['t_jidan'] = $ci->type_total_count;
                        $st['t_jidan'] += $ci->type_total_count;
                        $st['t_jidan_amount'] += $ci->type_total_amount;
                    }
                    elseif($ci->order_type == 3){
                        $pi['t_banniandan'] = $ci->type_total_count;
                        $st['t_banniandan'] += $ci->type_total_count;
                        $st['t_banniandan_amount'] += $ci->type_total_amount;
                    }
                }

                $delivery_info = DB::select(DB::raw("select sum(mdp.delivered_count) as current_delivered_count, sum(mdp.delivered_count * op.product_price) as delivered_amount ,op.order_type
                from milkmandeliveryplan mdp, orderproducts op where mdp.order_product_id = op.id and op.product_id = :product_id
                and mdp.deliver_at BETWEEN  '$start_date' and '$end_date' group by op.order_type"),
                    array('product_id'=>$pi->id));
                foreach ($delivery_info as $di){
                    if($di->order_type == 1){
                        $pi['r_yuedan'] = $di->current_delivered_count;
                        $st['r_yuedan'] += $di->current_delivered_count;
                        $st['r_delivered_yuedan_amount'] += $di->delivered_amount;

                    }
                    elseif($di->order_type == 2){
                        $pi['r_jidan'] = $di->current_delivered_count;
                        $st['r_jidan'] += $di->current_delivered_count;
                        $st['r_delivered_jidan_amount'] += $di->delivered_amount;
                    }
                    elseif($di->order_type == 3){
                        $pi['r_banniandan'] = $di->current_delivered_count;
                        $st['r_banniandan'] +=$di->current_delivered_count;
                        $st['r_delivered_banniandan_amount'] += $di->delivered_amount;
                    }
                }
            }

            $st['product'] = $product_info;
            $count = count($st['product']);
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_ADMIN, '订单类型统计', SysLog::SYSLOG_OPERATION_VIEW);

        return view('zongpingtai.tongji.dingdanleixing', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'start_date' => $start_date,
            'end_date' =>$end_date,
            'factory_name' => $factory_name,
            'factories' => $factories,
        ]);
    }
    //

    /* 奶厂 / 客户行为统计 */
    public function showKehuxingweitongji(Request $request){
        $child = 'kehuxingwei';
        $parent = 'tongji';
        $current_page = 'kehuxingwei';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();

        $province = ProvinceData::all();
        $date_start = new DateTime('first day of this month',new DateTimeZone('Asia/Shanghai'));
        $date_start = $date_start->format('Y-m-d');
        $date_end = getCurDateString();

        $station_name = $request->input('station_name');
        $station_number = $request->input('station_number');
        $addr = $request->input('province')." ".$request->input('city');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $filtered_stations = DeliveryStation::where('name','LIKE','%'.$station_name.'%')->where('number','LIKE','%'.$station_number.'%')->where('address','LIKE','%'.$addr.'%')->get();

        if($start_date == ''){
            $start_date = $date_start;
        }
        if($end_date == ''){
            $end_date = $date_end;
        }

        $stations = array(); //result station array
        /* 1. 新增客户数 newly registered customers */
        $new_customers = Customer::where('is_deleted', 0)
            ->whereBetween('created_at', array($start_date, $end_date))
            ->groupBy('station_id')
            ->selectRaw('station_id, count(*) as count')
            ->get();

        foreach($new_customers as $c) {
            $stations[$c->station_id]['new_customers'] = $c->count;
        }

        /* 2. 订单金额 */
        $new_orders = Order::where('is_deleted', 0)
//            ->where('order_property_id', OrderProperty::ORDER_PROPERTY_NEW_ORDER)
            ->whereBetween('ordered_at', array($start_date, $end_date))
            ->groupBy('station_id')
            ->selectRaw('station_id, sum(total_amount) as sum, count(*) as count')
            ->get();

        foreach($new_orders as $o) {
            $stations[$o->station_id]['new_order_price'] = $o->sum;
            $stations[$o->station_id]['new_order_count'] = $o->count;
        }


        /* 3. 期间到期客户数 */

        $finished_orders = Order::where('is_deleted', 0)
            ->where('status', Order::ORDER_FINISHED_STATUS)
            ->whereBetween('status_changed_at', array($start_date, $end_date))
            ->groupBy('station_id')
            ->selectRaw('station_id, count(*) as count')
            ->get();

        foreach($finished_orders as $o) {
            $stations[$o->station_id]['finished_orders'] = $o->count;
        }


        /* 4. 本期到期续单客户数  */

        $res = DB::select(DB::raw(
            'select station_id, count(*) as count from customer
            where
            id in (select customer_id from orders where ordered_at between :start_date1 and :end_date1 and (status <> :status_finish1 and status <> :status_cancel1) and is_deleted=0)
            AND
            id in (select customer_id from orders where status_changed_at between :start_date2 and :end_date2 and (status = :status_finish2 OR status = :status_cancel2) and is_deleted=0)
            group by station_id;'), array(
            'start_date1' => $start_date,
            'end_date1' => $end_date,
            'start_date2' => $start_date,
            'end_date2' => $end_date,
            'status_finish1' => ORDER::ORDER_FINISHED_STATUS,
            'status_cancel1' => ORDER::ORDER_CANCELLED_STATUS,
            'status_finish2' => ORDER::ORDER_FINISHED_STATUS,
            'status_cancel2' => ORDER::ORDER_CANCELLED_STATUS
        ));

        foreach($res as $r) {
            $stations[$r->station_id]['xuedan_after_another'] = $r->count;
        }


        /* 5. 前期到期续单客户数  */

        $res = DB::select(DB::raw(
            'select station_id, count(*) as count from customer
            where
            id in (select customer_id from orders where ordered_at < :start_date1 and (status <> :status_finish1 and status <> :status_cancel1) and is_deleted=0)
            AND
            id in (select customer_id from orders where status_changed_at between :start_date2 and :end_date2 and (status = :status_finish2 OR status = :status_cancel2) and is_deleted=0)
            group by station_id;'), array(
            'start_date1' => $start_date,
            'start_date2' => $start_date,
            'end_date2' => $end_date,
            'status_finish1' => ORDER::ORDER_FINISHED_STATUS,
            'status_cancel1' => ORDER::ORDER_CANCELLED_STATUS,
            'status_finish2' => ORDER::ORDER_FINISHED_STATUS,
            'status_cancel2' => ORDER::ORDER_CANCELLED_STATUS
        ));

        foreach($res as $r) {
            $stations[$r->station_id]['xuedan_after_finished_prev'] = $r->count;
        }

        /* 6. 续单金额 */

        $xudans = Order::where('is_deleted', 0)
            ->where('order_property_id', OrderProperty::ORDER_PROPERTY_XUDAN_ORDER)
            ->whereBetween('ordered_at', array($start_date, $end_date))
            ->groupBy('station_id')
            ->selectRaw('station_id, count(*) as count, sum(total_amount) as sum')
            ->get();

        foreach($xudans as $o) {
            $stations[$o->station_id]['xudan_orders'] = $o->count;
            $stations[$o->station_id]['xudan_price'] = $o->sum;
        }

        /* 7. 续单率 */

        foreach($xudans as $o) {
            $stations[$o->station_id]['xudan_ratio'] = round($stations[$o->station_id]['xudan_orders'] / $stations[$o->station_id]['new_order_count'] * 100, 1);
        }

        /* 8. 退单客户数 */ /* 9. 退款金额*/

        $ended_orders = Order::where('is_deleted', 0)
            ->where('status', Order::ORDER_CANCELLED_STATUS)
            ->whereBetween('status_changed_at', array($start_date, $end_date))
            ->groupBy('station_id')
            ->selectRaw('station_id, count(*) as count, sum(remaining_amount)')
            ->get();

        foreach($ended_orders as $o) {
            $stations[$o->station_id]['canceled_orders'] = $o->count;
            $stations[$o->station_id]['canceled_orders_amount'] = $o->sum;
        }

        /* 10. 订单金额合计 */
        foreach($stations as $station_id=>$station_info) {
            $total = $canceled = 0;
            if(isset($station_info['new_order_price']))
                $total = $station_info['new_order_price'];

            if(isset($station_info['canceled_orders_amount']))
                $canceled = $station_info['canceled_orders_amount'];
            $real = $total - $canceled;

            $stations[$station_id]['new_order_amount_real'] = $real;
        }
        /* 11. 划转公司奶款金额 */
        $res = DSCalcBalanceHistory::where('type', DSCalcBalanceHistory::DSCBH_OUT_TRANSFER_MILK_FACTORY)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->groupBy('station_id')
            ->selectRaw('station_id, count(*) as count, sum(amount)')
            ->get();

        foreach($res as $o) {
            $stations[$o->station_id]['trans_to_factory_amount'] = $o->sum;
        }

        /* 12. 支付返利提成金额 */

        $res = DSCalcBalanceHistory::where(function($query) {
            $query->where('type', DSCalcBalanceHistory::DSCBH_OUT_SETTLEMENT_DELIVERY_COST);
            $query->orWhere('type', DSCalcBalanceHistory::DSCBH_OUT_SETTLEMENT_ROBATE_ROYALTY);
        })
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->groupBy('station_id')
            ->selectRaw('station_id, count(*) as count, sum(amount)')
            ->get();

        foreach($res as $o) {
            $stations[$o->station_id]['trans_for_delivery_cost_amount'] = $o->sum;
        }
        /* 13. 其他划转金额 */
        $res = DSCalcBalanceHistory::where('type', DSCalcBalanceHistory::DSCBH_OUT_OTHER_USES)
            ->whereBetween('time', array($start_date, $end_date))
            ->groupBy('station_id')
            ->selectRaw('station_id, count(*) as count, sum(amount)')
            ->get();

        foreach($res as $o) {
            $stations[$o->station_id]['trans_to_other_amount'] = $o->sum;
        }

        // ----

        /* 14. 在配送客户数 */
        $res = Order::where('is_deleted', 0)
            ->where('status', Order::ORDER_ON_DELIVERY_STATUS)
            ->groupBy('station_id')
            ->selectRaw('station_id, count(*) as count, sum(total_amount) as sum')
            ->get();

        foreach($res as $o) {
            $stations[$o->station_id]['delivery_orders'] = $o->count;
            $stations[$o->station_id]['delivery_orders_amount'] = $o->sum;
        }

        /* 15. 剩余订单金额 */
        $res = DB::select(DB::raw(
            'select m.station_id, sum(m.delivered_count*op.product_price) as sum 
            from milkmandeliveryplan m, orderproducts op, orders o
            where m.order_product_id=op.id and m.order_id = o.id and o.status=:status
            group by m.station_id;'), array(
            'status' => ORDER::ORDER_ON_DELIVERY_STATUS
        ));

        foreach($res as $r) {
            $total = $stations[$o->station_id]['delivery_orders_amount'];
            $stations[$r->station_id]['delivery_orders_remaining_amount'] = $total - $r->sum;
        }

        /* 16. 暂停客户数 *//* 17. 剩余订单金额 */
        $res = Order::queryStopped()
            ->where('is_deleted', 0)
            ->groupBy('station_id')
            ->selectRaw('station_id, count(*) as count, sum(total_amount) as sum')
            ->get();

        foreach($res as $o) {
            $stations[$o->station_id]['stopped_orders'] = $o->count;
            $stations[$o->station_id]['stopped_orders_amount'] = $o->sum;
        }

        /* 18. 终止/退款客户数 */

        $res = Order::where(function($query) {
            $query->where('status', Order::ORDER_CANCELLED_STATUS);
            $query->orWhere('status', Order::ORDER_FINISHED_STATUS);
        })
            ->where('is_deleted', 0)
            ->groupBy('station_id')
            ->selectRaw('station_id, count(*) as count, sum(total_amount) as sum')
            ->get();

        foreach($res as $o) {
            $stations[$o->station_id]['ended_orders'] = $o->count;
            $stations[$o->station_id]['ended_orders_amount'] = $o->sum;
        }

        /* 19. 期末订单金额结余 */
        $res = Order::where('is_deleted', 0)
            ->groupBy('station_id')
            ->selectRaw('station_id, count(*) as count, sum(total_amount) as sum')
            ->get();

        foreach($res as $o) {
            $stations[$o->station_id]['total_orders_amount'] = $o->sum;
        }

        $res = DB::select(DB::raw(
            'select m.station_id as station_id, sum(m.delivered_count*op.product_price) as sum 
            from milkmandeliveryplan m, orderproducts op 
            where m.order_product_id=op.id group by m.station_id;'), array(
        ));

        foreach($res as $r) {
            $stations[$r->station_id]['total_orders_delivered_amount'] = $r->sum;
            $stations[$r->station_id]['total_orders_remaining_amount'] = $stations[$o->station_id]['total_orders_amount'] - $r->sum;
        }

        foreach($stations as $station_id=>$station_info) {

            $station = DeliveryStation::findOrFail($station_id);
            $stations[$station_id]['province_name'] = $station->province_name;
            $stations[$station_id]['city_name'] = $station->city_name;
            $stations[$station_id]['name'] = $station->name;
        }

        $result = array();
        foreach ($filtered_stations as $fs){
            foreach ($stations as $station_id=>$station_info){
                if($fs->id == $station_id){
                    $result[$station_id] = $station_info;
                }
            }
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_ADMIN, '客户行为分析', SysLog::SYSLOG_OPERATION_VIEW);

        return view('zongpingtai.tongji.kehuxingwei', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations'=>$result,
            'province'=>$province,
            'current_station_name'=>$station_name,
            'current_station_number'=>$station_number,
            'current_province'=>$request->input('province'),
            'current_city'=>$request->input('city'),
            'currrent_start_date'=>$start_date,
            'current_end_date'=>$end_date,
        ]);

    }

    /* 奶厂 / 客户订单修改统计 */
    public function showKehudingdanxiugui(Request $request){
        $child = 'kehudingdanxiugai';
        $parent = 'tongji';
        $current_page = 'kehudingdanxiugai';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();


        $province = ProvinceData::all();
        $date_start = new DateTime('first day of this month',new DateTimeZone('Asia/Shanghai'));
        $date_start = $date_start->format('Y-m-d');
        $date_end = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $date_end = $date_end->format('Y-m-d');

        $station_name = $request->input('station_name');
        $station_number = $request->input('station_number');
        $addr = $request->input('province')." ".$request->input('city');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $filtered_stations = DeliveryStation::where('name','LIKE','%'.$station_name.'%')->where('number','LIKE','%'.$station_number.'%')->where('address','LIKE','%'.$addr.'%')->get();

        if($start_date == ''){
            $start_date = $date_start;
        }
        if($end_date == ''){
            $end_date = $date_end;
        }

        $res = DB::select(DB::raw(
            'select o.station_id as station_id, count(*) as count 
            from orderchanges oc, orders o 
            where oc.order_id=o.id  and oc.type=:type and oc.created_at between :start_date and :end_date;'), array(
            'type' => OrderChanges::ORDERCHANGES_TYPE_ADDRESS,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ));

        foreach($res as $r) {
            $stations[$r->station_id]['address'] = $r->count;
        }

        $res = DB::select(DB::raw(
            'select o.station_id as station_id, count(*) as count 
            from orderchanges oc, orders o 
            where oc.order_id=o.id  and oc.type=:type and oc.created_at between :start_date and :end_date;'), array(
            'type' => OrderChanges::ORDERCHANGES_TYPE_PHONE,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ));

        foreach($res as $r) {
            $stations[$r->station_id]['phone'] = $r->count;
        }


        $res = Order::queryStopped()
            ->where('is_deleted', 0)
            ->whereBetween('status_changed_at', array($start_date, $end_date))
            ->selectRaw('station_id, count(*) as count')
            ->get();

        foreach($res as $o) {
            $stations[$o->station_id]['stopped'] = $o->count;
        }


        //increased

        $res = DB::select(DB::raw(
            'select o.station_id as station_id, count(*) as count 
            from orderchanges oc, orders o 
            where oc.order_id=o.id  and oc.type=:type
             and oc.original_value < oc.changed_value
            and oc.created_at between :start_date and :end_date;'), array(
            'type' => OrderChanges::ORDERCHANGES_TYPE_DELIVERY_COUNT,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ));

        foreach($res as $r) {
            $stations[$r->station_id]['increased'] = $r->count;
        }

        //decreased
        $res = DB::select(DB::raw(
            'select o.station_id as station_id, count(*) as count 
            from orderchanges oc, orders o 
            where oc.order_id=o.id  and oc.type=:type
             and oc.original_value > oc.changed_value
            and oc.created_at between :start_date and :end_date;'), array(
            'type' => OrderChanges::ORDERCHANGES_TYPE_DELIVERY_COUNT,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ));

        foreach($res as $r) {
            $stations[$r->station_id]['decreased'] = $r->count;
        }

        //pure milk <-> yogurt
        $res = DB::select(DB::raw(
            'select o.station_id, count(*) as count from orderchanges oc, orders o 
            where oc.order_id=o.id  and oc.type=:type
            and ((select p.property from products p where p.id=oc.original_value) = :product1 
            and (select p.property from products p where p.id=oc.changed_value) = :product2)
            and oc.created_at between :start_date and :end_date
            group by station_id;'), array(
            'type' => OrderChanges::ORDERCHANGES_TYPE_PRODUCT,
            'product1' => Product::PRODUCT_PROPERTY_PURE_MILK,
            'product2' => Product::PRODUCT_PROPERTY_YOGURT,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ));

        foreach($res as $r) {
            $stations[$r->station_id]['milk_yogurt'] = $r->count;
        }

        //yogurt -> milk

        $res = DB::select(DB::raw(
            'select o.station_id, count(*) as count from orderchanges oc, orders o 
            where oc.order_id=o.id  and oc.type=:type
            and ((select p.property from products p where p.id=oc.original_value) = :product1 
            and (select p.property from products p where p.id=oc.changed_value) = :product2)
            and oc.created_at between :start_date and :end_date
            group by station_id;'), array(
            'type' => OrderChanges::ORDERCHANGES_TYPE_PRODUCT,
            'product1' => Product::PRODUCT_PROPERTY_YOGURT,
            'product2' => Product::PRODUCT_PROPERTY_PURE_MILK,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ));

        foreach($res as $r) {
            $stations[$r->station_id]['yogurt_milk'] = $r->count;
        }

        //yogurt->kouwei

        $res = DB::select(DB::raw(
            'select o.station_id, count(*) as count from orderchanges oc, orders o 
            where oc.order_id=o.id  and oc.type=:type
            and ((select p.property from products p where p.id=oc.original_value) = :product1 
            and (select p.property from products p where p.id=oc.changed_value) = :product2)
            and oc.created_at between :start_date and :end_date
            group by station_id;'), array(
            'type' => OrderChanges::ORDERCHANGES_TYPE_PRODUCT,
            'product1' => Product::PRODUCT_PROPERTY_YOGURT,
            'product2' => Product::PRODUCT_PROPERTY_KOUWEI,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ));

        foreach($res as $r) {
            $stations[$r->station_id]['yogurt_kouwei'] = $r->count;
        }

        //changed delivery rules
        $res = DB::select(DB::raw(
            'select o.station_id as station_id, count(*) as count 
            from orderchanges oc, orders o 
            where oc.order_id=o.id  and oc.type=:type and oc.created_at between :start_date and :end_date;'), array(
            'type' => OrderChanges::ORDERCHANGES_TYPE_DELIVERY_RULE,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ));

        foreach($res as $r) {
            $stations[$r->station_id]['rule'] = $r->count;
        }

        foreach($stations as $station_id=>$station_info) {
            $station = DeliveryStation::find($station_id);
            if($station)
                $stations[$station_id]['name'] = $station->name;
        }
        $result = array();
        foreach ($filtered_stations as $fs){
            foreach ($stations as $station_id=>$station_info){
                if($fs->id == $station_id){
                    $result[$station_id] = $station_info;
                }
            }
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_ADMIN, '客户订单修改统计', SysLog::SYSLOG_OPERATION_VIEW);

        return view('zongpingtai.tongji.kehudingdanxiugai', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations' => $result,
            'province'=>$province,
            'current_station_name'=>$station_name,
            'current_station_number'=>$station_number,
            'current_province'=>$request->input('province'),
            'current_city'=>$request->input('city'),
            'currrent_start_date'=>$start_date,
            'current_end_date'=>$end_date,
        ]);
    }
}
