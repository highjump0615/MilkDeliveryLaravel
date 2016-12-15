<?php

namespace App\Http\Controllers;

use App\Model\BasicModel\Address;
use App\Model\BasicModel\ProvinceData;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\FactoryModel\Factory;
use App\Model\FinanceModel\DSCalcBalanceHistory;
use App\Model\OrderModel\OrderChanges;
use App\Model\OrderModel\OrderProduct;
use App\Model\OrderModel\OrderType;
use App\Model\ProductModel\Product;
use App\Model\OrderModel\Order;

use App\Model\UserModel\Page;
use App\Model\UserModel\User;
use App\Model\SystemModel\SysLog;

use App\Model\BasicModel\Customer;
use App\Model\OrderModel\OrderProperty;
use Illuminate\Http\Request;
use Auth;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class FactoryStatistics extends Controller
{
    /* 奶厂 / 到期订单统计 */
    public function showDaoqidingdantongji(Request $request){
        $child = 'daoqidingdantongji';
        $parent = 'tongjifenxi';
        $current_page = 'daoqidingdantongji';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $currentDate_str = $currentDate->format('Y-m-d');
        $currentMonth_str = $currentDate->format('Y-m-01');

        $station_name = $request->input('station_name');
        $station_number = $request->input('station_number');

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        if($start_date == null){
            $start_date = $currentMonth_str;
        }
        if($end_date == null){
            $end_date = $currentDate_str;
        }

        $input_province = $request->input('province');
        $input_city = $request->input('city');
        $input_district = $request->input('district');
        if($station_name == null){
            $station_name = '';
        }
        if($station_number == null){
            $station_number = '';
        }

        $factory_id = Auth::guard('gongchang')->user()->factory_id;
        $province = ProvinceData::all();
        $ended_orders = array();

        if($input_province != null && $input_city != null){
            $deliverystations = DeliveryStation::where('factory_id',$current_factory_id)->where('name','LIKE','%'.$station_name.'%')->
                where('number','LIKE','%'.$station_number.'%')->get(['id']);
            foreach ($deliverystations as $key=>$ds){
                $orders = Order::where('station_id',$ds->id)->where('status',Order::ORDER_FINISHED_STATUS)->wherebetween('ordered_at',[$start_date,$end_date])->
                where('address','LIKE', '%'.$input_province." ".$input_city." ".$input_district.'%')->where('is_deleted',0)->get();
                foreach ($orders as $or){
                    $orderproducts = OrderProduct::where('order_id',$or->id);
                    $yuedan = 0;
                    $jidan =0;
                    $banniandan = 0;
                    foreach ($orderproducts as $op){
                        if($op->order_type = OrderType::ORDER_TYPE_MONTH){
                            $yuedan = 1;
                        }
                        if($op->order_type == OrderType::ORDER_TYPE_SEASON){
                            $jidan = 1;
                        }
                        if($op->order_type == OrderType::ORDER_TYPE_HALF_YEAR){
                            $banniandan =1;
                        }
                    }

                    $order_type_status = [];
                    $chk = 0;
                    if($yuedan == 1){
                        if($chk == 0) {
                            $order_type_status = $order_type_status.'月单';
                            $chk++;
                        }
                    }
                    if($jidan == 1){
                        if($chk == 0)
                            $order_type_status =$order_type_status.'季单';
                        else
                            $order_type_status =$order_type_status.'/季单';
                    }
                    if($banniandan == 1){
                        if($chk == 1)
                            $order_type_status =$order_type_status.'半年单';
                        else
                            $order_type_status =$order_type_status.'/半年单';
                    }
                    $or['order_type'] = $order_type_status;

                }
                $ended_orders[$key] = $orders;
            }
        }
        elseif ($input_province != null && $input_city == null){
            $deliverystations = DeliveryStation::where('factory_id',$current_factory_id)->where('name','LIKE','%'.$station_name.'%')->
            where('number','LIKE','%'.$station_number.'%')->get(['id']);
            foreach ($deliverystations as $key=>$ds){
                $orders = Order::where('station_id',$ds->id)->where('status',Order::ORDER_FINISHED_STATUS)->wherebetween('ordered_at',[$start_date,$end_date])->
                where('address','LIKE', '%'.$input_province.'%')->where('is_deleted',0)->get();
                foreach ($orders as $or){
                    $orderproducts = OrderProduct::where('order_id',$or->id);
                    $yuedan = 0;
                    $jidan =0;
                    $banniandan = 0;
                    foreach ($orderproducts as $op){
                        if($op->order_type = OrderType::ORDER_TYPE_MONTH){
                            $yuedan = 1;
                        }
                        if($op->order_type == OrderType::ORDER_TYPE_SEASON){
                            $jidan = 1;
                        }
                        if($op->order_type == OrderType::ORDER_TYPE_HALF_YEAR){
                            $banniandan =1;
                        }
                    }

                    $order_type_status = '';
                    $chk = 0;
                    if($yuedan == 1){
                        if($chk == 0) {
                            $order_type_status = $order_type_status.'月单';
                            $chk++;
                        }
                    }
                    if($jidan == 1){
                        if($chk == 0)
                            $order_type_status =$order_type_status.'季单';
                        else
                            $order_type_status =$order_type_status.'/季单';
                    }
                    if($banniandan == 1){
                        if($chk == 1)
                            $order_type_status =$order_type_status.'半年单';
                        else
                            $order_type_status =$order_type_status.'/半年单';
                    }
                    $or['order_type'] = $order_type_status;

                }
                $ended_orders[$key] = $orders;
            }
        }
        elseif ($input_province == null){
            $deliverystations = DeliveryStation::where('factory_id',$current_factory_id)->where('name','LIKE','%'.$station_name.'%')->
            where('number','LIKE','%'.$station_number.'%')->get(['id']);
            foreach ($deliverystations as $key=>$ds){
                $orders = Order::where('station_id',$ds->id)->where('status',Order::ORDER_FINISHED_STATUS)->wherebetween('ordered_at',[$start_date,$end_date])->where('is_deleted',0)->get();
                $abc = 0;
                foreach ($orders as $or){
                    $abc++;
                    $orderproducts = OrderProduct::where('order_id',$or->id);
                    $yuedan = 0;
                    $jidan =0;
                    $banniandan = 0;
                    foreach ($orderproducts as $op){
                        if($op->order_type = OrderType::ORDER_TYPE_MONTH){
                               $yuedan = 1;
                        }
                        if($op->order_type == OrderType::ORDER_TYPE_SEASON){
                            $jidan = 1;
                        }
                        if($op->order_type == OrderType::ORDER_TYPE_HALF_YEAR){
                            $banniandan =1;
                        }
                    }

                    $order_type_status = '';
                    $chk = 0;
                    if($yuedan == 1){
                        if($chk == 0) {
                            $order_type_status = $order_type_status.'月单';
                            $chk++;
                        }
                    }
                    if($jidan == 1){
                        if($chk == 0)
                            $order_type_status =$order_type_status.'季单';
                        else
                            $order_type_status =$order_type_status.'/季单';
                    }
                    if($banniandan == 1){
                        if($chk == 1)
                            $order_type_status =$order_type_status.'半年单';
                        else
                            $order_type_status =$order_type_status.'/半年单';
                    }
                    $or['order_type'] = $order_type_status;
                }
                $ended_orders[$key] = $orders;
            }
        }

        if($input_province == null){
            $input_province = "";
        }
        if($input_city == null){
            $input_city = "";
        }
        if($input_district == null){
            $input_district = "";
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '到期订单统计', SysLog::SYSLOG_OPERATION_VIEW);

        return view('gongchang.tongjifenxi.daoqidingdantongji', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'province' =>$province,
            'order_info' => $ended_orders,
            'station_name' => $station_name,
            'station_number' => $station_number,
            'input_province' => $input_province,
            'input_city' => $input_city,
            'input_district' => $input_district,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    /* 奶厂 / 奶品配送统计 */
    public function showNaipinpeisongtongji(Request $request){
        $station_name = $request->input('station_name');
        $area_name = $request->input('area_name');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;
        $child = 'naipinpeisongtongji';
        $parent = 'tongjifenxi';
        $current_page = 'naipinpeisongtongji';
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $currentDate_str = $currentDate->format('Y-m-d');
        $currentYear_str = $currentDate->format('Y-01-01');
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        if($start_date == null){
            $start_date = $currentYear_str;
        }
        if($end_date == null){
            $end_date = $currentDate_str;
        }
        if($station_name == null)
            $station_name = "";

        $count = 0;
        $address = Address::where('level',1)->where('factory_id',$current_factory_id)->where('is_deleted',0)->get();

        $stations = DeliveryStation::where('factory_id',$current_factory_id)->where('name','LIKE','%'.$station_name.'%')->where('address','LIKE',$area_name.'%')->where('is_deleted',0)->get();
        foreach ($stations as $st){
                $product_info = Product::where('factory_id',$current_factory_id)->where('is_deleted',0)->get();

                foreach ($product_info as $pi){
                    $order_info = DB::select(DB::raw("select sum(p.delivered_count) as total_count, o.payment_type from milkmandeliveryplan p, orderproducts op, orders o
                    where p.type=1 and p.station_id=:station_id and op.id=p.order_product_id  and o.id = p.order_id and op.product_id=:product_id and
                     deliver_at between '$start_date' and '$end_date' group by o.payment_type"),array('station_id'=>$st->id,'product_id'=>$pi->id));
                    foreach ($order_info as $ci){
                        if($ci->payment_type == 1){
                            $pi['xianjin'] = $ci->total_count;
                        }
                        elseif($ci->payment_type == 2){
                            $pi['card'] = $ci->total_count;
                        }
                        elseif($ci->payment_type == 3){
                            $pi['weixin'] = $ci->total_count;
                        }
                    }

                    $delivery_info = DB::select(DB::raw("select sum(retail) as retail_count,sum(test_drink) as test_count,
                    sum(group_sale) as group_count,sum(channel_sale) as channel_count from dsdeliveryplan
                    where station_id=:station_id and product_id=:product_id and deliver_at between '$start_date' and '$end_date'"),
                        array('station_id'=>$st->id,'product_id'=>$pi->id));
                    foreach ($delivery_info as $di){
                        $pi['retail'] = $di->retail_count;
                        $pi['test_drink'] = $di->test_count;
                        $pi['group_sale'] = $di->group_count;
                        $pi['channel_sale'] = $di->channel_count;
                    }

                }

                $st['product'] = $product_info;

                $count = count($st['product']);
                $addr = explode(" ",$st->address);
                if(count($addr)>0)
                    $st['province'] = $addr[0];
                else
                    $st['province'] = '';
                if(count($addr)>2)
                    $st['district'] = $addr[2];
                else
                    $st['district'] = '';

        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '奶品配送统计', SysLog::SYSLOG_OPERATION_VIEW);

        return view('gongchang.tongjifenxi.naipinpeisongtongji', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations' => $stations,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'station_name'=>$station_name,
            'area_name'=>$area_name,
            'count'=> $count,
            'address'=>$address,
        ]);
    }


    /* 奶厂 / 客户行为统计 */
    public function showKehuxingweitongji(Request $request){
        $child = 'kehuxingweitongji';
        $parent = 'tongjifenxi';
        $current_page = 'kehuxingweitongji';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        $addr = Address::where('level',1)->get();
        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;

        $province = ProvinceData::all();
        $date_start = new DateTime('first day of this month',new DateTimeZone('Asia/Shanghai'));
        $date_start = $date_start->format('Y-m-d');
        $date_end = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $date_end = $date_end->format('Y-m-d');

        $station_name = $request->input('station_name');
        $station_number = $request->input('station_number');
        $addrs = $request->input('province')." ".$request->input('city');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $filtered_stations = DeliveryStation::where('factory_id',$current_factory_id)->where('name','LIKE','%'.$station_name.'%')->where('number','LIKE','%'.$station_number.'%')->where('address','LIKE','%'.$addrs.'%')->get();

        if($start_date == ''){
            $start_date = $date_start;
        }
        if($end_date == ''){
            $end_date = $date_end;
        }

        $stations = array(); //result station array

        $user = Auth::guard('gongchang')->user();
        $all_stations = DeliveryStation::where('factory_id', $user->factory_id)->where('is_deleted', 0)->get();


        foreach($all_stations as $s) {
            $stations[$s->id]['province_name'] = $s->province_name;
            $stations[$s->id]['city_name'] = $s->city_name;
            $stations[$s->id]['name'] = $s->name;
        }
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
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
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
        $result = array();
        foreach ($filtered_stations as $fs){
            foreach ($stations as $station_id=>$station_info){
                if($fs->id == $station_id){
                    $result[$station_id] = $station_info;
                }
            }
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '客户行为统计', SysLog::SYSLOG_OPERATION_VIEW);

        return view('gongchang.tongjifenxi.kehuxingweitongji', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'address'=>$addr,
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
        $child = 'kehudingdanxiugui';
        $parent = 'tongjifenxi';
        $current_page = 'kehudingdanxiugui';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;

        $province = ProvinceData::all();
        $date_start = new DateTime('first day of this month',new DateTimeZone('Asia/Shanghai'));
        $date_start = $date_start->format('Y-m-d');
        $date_end = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $date_end = $date_end->format('Y-m-d');

        $station_name = $request->input('station_name');
        $station_number = $request->input('station_number');
        $addrs = $request->input('province')." ".$request->input('city');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $filtered_stations = DeliveryStation::where('factory_id',$current_factory_id)->where('name','LIKE','%'.$station_name.'%')->where('number','LIKE','%'.$station_number.'%')->where('address','LIKE','%'.$addrs.'%')->get();

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
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '客户订单修改统计', SysLog::SYSLOG_OPERATION_VIEW);

        return view('gongchang.tongjifenxi.kehudingdanxiugui', [
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

    /* 奶厂 / 订单剩余量统计 */
    public function showDingdanshengyuliangtongji(Request $request){
        $child = 'dingdanshengyuliangtongji';
        $parent = 'tongjifenxi';
        $current_page = 'dingdanshengyuliangtongji';
        $station_name = $request->input('station_name');
        $area_name = $request->input('area_name');
        $end_date = $request->input('end_date');
        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $currentDate_str = $currentDate->format('Y-m-d');
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        if($end_date == null){
            $end_date = $currentDate_str;
        }
        if($station_name == null)
            $station_name = "";

        $count = 0;
        $address = Address::where('level',1)->where('factory_id',$current_factory_id)->where('is_deleted',0)->get();

        $stations = DeliveryStation::where('factory_id',$current_factory_id)->where('name','LIKE','%'.$station_name.'%')->where('address','LIKE',$area_name.'%')->where('is_deleted',0)->get();
        foreach ($stations as $st){
            $product_info = Product::where('factory_id',$current_factory_id)->where('is_deleted',0)->get();
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
                where op.order_id = o.id and o.station_id = :station_id and op.product_id = :product_id and o.ordered_at <= :end_date group by op.order_type"),
                    array('station_id'=>$st->id,'product_id'=>$pi->id,'end_date'=>$end_date));

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
                and mdp.station_id = :station_id and mdp.deliver_at <= :end_date group by op.order_type"),
                    array('station_id'=>$st->id,'product_id'=>$pi->id,'end_date'=>$end_date));
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
            $addr = explode(" ",$st->address);
            if(count($addr)>0)
                $st['province'] = $addr[0];
            else
                $st['province'] = '';
            if(count($addr)>2){
                $st['city'] = $addr[1];
                $st['district'] = $addr[2];
            }
            else{
                $st['city'] = '';
                $st['district'] = '';
            }

        }
        return view('gongchang.tongjifenxi.dingdanshengyuliangtongji', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations' => $stations,
            'end_date'=>$end_date,
            'station_name'=>$station_name,
            'area_name'=>$area_name,
            'count'=> $count,
            'address'=>$address,
        ]);
    }

    /* 奶厂 / 订单类型统计 */
    public function showDingdanleixingtongji(Request $request){
        $child = 'dingdanleixingtongji';
        $parent = 'tongjifenxi';
        $current_page = 'dingdanleixingtongji';
        $station_name = $request->input('station_name');
        $area_name = $request->input('area_name');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $currentDate_str = $currentDate->format('Y-m-d');
        $startDate_str = $currentDate->format('Y-01-01');
        if($start_date == null){
            $start_date = $startDate_str;
        }
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        if($end_date == null){
            $end_date = $currentDate_str;
        }
        if($station_name == null)
            $station_name = "";

        $count = 0;
        $address = Address::where('level',1)->where('factory_id',$current_factory_id)->where('is_deleted',0)->get();

        $stations = DeliveryStation::where('factory_id',$current_factory_id)->where('name','LIKE','%'.$station_name.'%')->where('address','LIKE',$area_name.'%')->where('is_deleted',0)->get();
        foreach ($stations as $st) {
            $product_info = Product::where('factory_id', $current_factory_id)->where('is_deleted', 0)->get();
            $st['yuedan_xin_total'] = 0;
            $st['jidan_xin_total'] = 0;
            $st['banniandan_xin_total'] = 0;
            $st['yuedan_xu_total'] = 0;
            $st['jidan_xu_total'] = 0;
            $st['banniandan_xu_total'] = 0;
            $st['yuedan_xin_amount'] = 0;
            $st['jidan_xin_amount'] = 0;
            $st['banniandan_xin_amount'] = 0;
            $st['yuedan_xu_amount'] = 0;
            $st['jidan_xu_amount'] = 0;
            $st['banniandan_xu_amount'] = 0;
            foreach ($product_info as $pi) {
                $pi['yuedan_xin'] = 0;
                $pi['jidan_xin'] = 0;
                $pi['banniandan_xin'] = 0;
                $pi['yuedan_xu'] = 0;
                $pi['jidan_xu'] = 0;
                $pi['banniandan_xu'] = 0;

                $xindan_info = DB::select(DB::raw("select sum(op.total_count) as type_total_count, sum(op.total_amount) as type_total_amount, op.order_type 
                from orderproducts op, orders o
                where op.order_id = o.id and op.product_id = :product_id and o.station_id = :station_id and o.order_property_id = 1 and o.ordered_at between '$start_date' and '$end_date' 
                group by op.order_type"), array('station_id' => $st->id, 'product_id' => $pi->id));
                foreach ($xindan_info as $ci) {
                    if ($ci->order_type == 1) {
                        $pi['yuedan_xin'] = $ci->type_total_count;
                        $st['yuedan_xin_total'] += $ci->type_total_count;
                        $st['yuedan_xin_amount'] +=$ci->type_total_amount;
                    } elseif ($ci->order_type == 2) {
                        $pi['jidan_xin'] = $ci->type_total_count;
                        $st['jidan_xin_total'] += $ci->type_total_count;
                        $st['jidan_xin_amount'] +=$ci->type_total_amount;
                    } elseif ($ci->order_type == 3) {
                        $pi['banniandan_xin'] = $ci->type_total_count;
                        $st['banniandan_xin_total'] += $ci->type_total_count;
                        $st['banniandan_xin_amount'] +=$ci->type_total_amount;
                    }
                }

                $xudan_info = DB::select(DB::raw("select sum(op.total_count) as type_total_count,sum(op.total_amount) as type_total_amount, op.order_type 
                from orderproducts op, orders o
                where op.order_id = o.id and op.product_id = :product_id and o.station_id = :station_id and o.order_property_id = 2 and o.ordered_at between '$start_date' and '$end_date' 
                group by op.order_type"), array('station_id' => $st->id, 'product_id' => $pi->id));
                foreach ($xudan_info as $xi) {
                    if ($xi->order_type == 1) {
                        $pi['yuedan_xu'] = $xi->type_total_count;
                        $st['yuedan_xu_total'] += $xi->type_total_count;
                        $st['yuedan_xu_amount'] += $xi->type_total_amount;
                    } elseif ($xi->order_type == 2) {
                        $pi['jidan_xu'] = $xi->type_total_count;
                        $st['jidan_xu_total'] += $xi->type_total_count;
                        $st['jidan_xu_amount'] += $xi->type_total_amount;
                    } elseif ($xi->order_type == 3) {
                        $pi['banniandan_xu'] = $xi->type_total_count;
                        $st['banniandan_xu_total'] += $xi->type_total_count;
                        $st['banniandan_xu_amount'] += $xi->type_total_amount;
                    }
                }
            }

            $st['product'] = $product_info;

            $count = count($st['product']);
            $addr = explode(" ", $st->address);
            if (count($addr) > 0)
                $st['province'] = $addr[0];
            else
                $st['province'] = '';
            if (count($addr) > 2){
                $st['city'] = $addr[1];
                $st['district'] = $addr[2];
            }
            else{
                $st['city'] = '';
                $st['district'] = '';
            }
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '订单剩余量统计', SysLog::SYSLOG_OPERATION_VIEW);

        return view('gongchang.tongjifenxi.dingdanleixingtongji', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations' => $stations,
            'start_date' => $start_date,
            'end_date'=>$end_date,
            'station_name'=>$station_name,
            'area_name'=>$area_name,
            'count'=> $count,
            'address'=>$address,
        ]);
    }
    //
}
