<?php

namespace App\Http\Controllers;

use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\DSDeliveryPlan;
use App\Model\DeliveryModel\MilkMan;
use App\Model\DeliveryModel\MilkmanBottleRefund;
use App\Model\DeliveryModel\MilkManDeliveryPlan;
use App\Model\OrderModel\Order;
use App\Model\OrderModel\OrderProduct;
use App\Model\OrderModel\OrderType;
use App\Model\ProductModel\Product;
use App\Model\StationModel\DSBottleRefund;

use App\Model\UserModel\Page;
use App\Model\UserModel\User;
use App\Model\SystemModel\SysLog;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class DSStatistics extends Controller
{
    public function showDingdan(Request $request){
        $child = 'dingdan';
        $parent = 'tongji';
        $current_page = 'dingdan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $current_factory_id = Auth::guard('naizhan')->user()->factory_id;
        $current_station_id = Auth::guard('naizhan')->user()->station_id;

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $currentDate_str = $currentDate->format('Y-m-d');
        $startDate_str = $currentDate->format('Y-01-01');

        if($start_date == null){
            $start_date = $startDate_str;
        }
        if($end_date == null){
            $end_date = $currentDate_str;
        }
        $order_type = $request->input('order_type');
        if($order_type == null)
            $order_type = OrderType::ORDER_TYPE_MONTH;

        $ot = OrderType::find($order_type);
        $timming = $ot->days;

        $product_info = Product::where('factory_id',$current_factory_id)->where('is_deleted',0)->get();
        $t_type = 0;
        $t_type_amount = 0;
        $r_type = 0;
        $r_type_amount = 0;
        $s_type = 0;
        $s_type_amount = 0;
        $xin_property = 0;
        $xin_property_amount = 0;
        $xu_property = 0;
        $xu_property_amount = 0;
        foreach ($product_info as $pi){
            $order_info = DB::select(DB::raw("select sum(op.total_count) as type_total_count, sum(op.total_amount) as type_total_amount, sum(op.avg) as type_avg_amount,op.order_type 
                from orderproducts op, orders o
                where op.order_id = o.id and o.station_id = :station_id and op.product_id = :product_id and op.order_type=:order_type and o.ordered_at BETWEEN '$start_date' and '$end_date'"),
                array('station_id'=>$current_station_id,'product_id'=>$pi->id,'order_type'=>$order_type));

            $pi['t_type'] = 0;
            foreach ($order_info as $ci){
                $pi['t_type'] = $ci->type_total_count;
                $t_type += $ci->type_total_count;
                $t_type_amount += $ci->type_total_amount;
            }

            $from_first_order_info = DB::select(DB::raw("select sum(op.total_count) as type_total_count, sum(op.total_amount) as type_total_amount, sum(op.avg) as type_avg_amount,op.order_type 
                from orderproducts op, orders o
                where op.order_id = o.id and o.station_id = :station_id and op.product_id = :product_id and op.order_type=:order_type and o.ordered_at <= :end_date"),
                array('station_id'=>$current_station_id,'product_id'=>$pi->id,'order_type'=>$order_type, 'end_date'=>$end_date));

            $pi['s_type'] = 0;
            foreach ($from_first_order_info as $ci){
                $pi['s_type'] = $ci->type_total_count;
                $s_type += $ci->type_total_count;
                $s_type_amount += $ci->type_total_amount;
            }

            $delivery_info = DB::select(DB::raw("select sum(mdp.delivered_count) as current_delivered_count, sum(mdp.delivered_count * op.product_price) as delivered_amount ,op.order_type
                from milkmandeliveryplan mdp, orderproducts op where mdp.order_product_id = op.id and op.product_id = :product_id
                and mdp.station_id = :station_id and op.order_type=:order_type and mdp.deliver_at BETWEEN '$start_date' and '$end_date'"),
                array('station_id'=>$current_station_id,'product_id'=>$pi->id,'order_type'=>$order_type));
            $pi['r_type'] = 0;
            foreach ($delivery_info as $di){
                $pi['r_type'] = $di->current_delivered_count;
                $r_type += $di->current_delivered_count;
                $r_type_amount += $di->delivered_amount;
            }

            $xindan_info = DB::select(DB::raw("select sum(op.total_count) as type_total_count, sum(op.total_amount) as type_total_amount, op.order_type 
                from orderproducts op, orders o
                where op.order_id = o.id and op.product_id = :product_id and o.station_id = :station_id and o.order_property_id = 1 and op.order_type=:order_type and o.ordered_at between '$start_date' and '$end_date' "),
                array('station_id' => $current_station_id, 'product_id' => $pi->id, 'order_type' =>$order_type));
            $pi['xin_property'] = 0;
            foreach ($xindan_info as $ci) {
                $pi['xin_property'] = $ci->type_total_count;
                $xin_property += $ci->type_total_count;
                $xin_property_amount +=$ci->type_total_amount;
            }

            $xudan_info = DB::select(DB::raw("select sum(op.total_count) as type_total_count,sum(op.total_amount) as type_total_amount, op.order_type 
                from orderproducts op, orders o
                where op.order_id = o.id and op.product_id = :product_id and o.station_id = :station_id and o.order_property_id = 2 and op.order_type = :order_type and o.ordered_at between '$start_date' and '$end_date' "),
                array('station_id' => $current_station_id, 'product_id' => $pi->id, 'order_type' => $order_type));
            $pi['xu_property'] = 0;
            foreach ($xudan_info as $xi) {
                $pi['xu_property'] = $xi->type_total_count;
                $xu_property += $xi->type_total_count;
                $xu_property_amount += $xi->type_total_amount;
            }
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_STATION, '订单统计', SysLog::SYSLOG_OPERATION_VIEW);

        return view('naizhan.tongji.dingdan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'order_type' =>$order_type,
            'start_date' =>$start_date,
            'end_date' =>$end_date,
            'product_info' =>$product_info,
            't_type' => $t_type,
            't_type_amount' => $t_type_amount,
            'r_type' => $r_type,
            'r_type_amount' => $r_type_amount,
            's_type' => $s_type,
            's_type_amount' => $s_type_amount,
            'xin_property' => $xin_property,
            'xin_property_amount' => $xin_property_amount,
            'xu_property' => $xu_property,
            'xu_property_amount' => $xu_property_amount,
            'timming' =>$timming,
        ]);
    }

    public function showDingdanshengyuliang(Request $request){
        $child = 'dingdanshenyuliang';
        $parent = 'tongji';
        $current_page = 'dingdanshenyuliang';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        $current_factory_id = Auth::guard('naizhan')->user()->factory_id;

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $currentDate_str = $currentDate->format('Y-m-d');
        $startDate_str = $currentDate->format('Y-01-01');
        if($start_date == null){
            $start_date = $startDate_str;
        }
        if($end_date == null){
            $end_date = $currentDate_str;
        }

        $product_info = Product::where('factory_id',$current_factory_id)->where('is_deleted',0)->get();
        $t_yuedan = 0;
        $t_jidan = 0;
        $t_banniandan = 0;
        $r_yuedan = 0;
        $r_jidan = 0;
        $r_banniandan = 0;
        $t_yuedan_amount = 0;
        $t_jidan_amount = 0;
        $t_banniandan_amount = 0;
        $r_yuedan_amount = 0;
        $r_jidan_amount = 0;
        $r_banniandan_amount = 0;
        $s_yuedan = 0;
        $s_jidan = 0;
        $s_banniandan = 0;
        $s_yuedan_amount = 0;
        $s_jidan_amount = 0;
        $s_banniandan_amount = 0;
        foreach ($product_info as $pi){
            $order_info = DB::select(DB::raw("select sum(op.total_count) as type_total_count, sum(op.total_amount) as type_total_amount, sum(op.avg) as type_avg_amount,op.order_type 
                from orderproducts op, orders o
                where op.order_id = o.id and o.station_id = :station_id and op.product_id = :product_id and o.ordered_at BETWEEN '$start_date' and '$end_date' group by op.order_type"),
                array('station_id'=>$current_station_id,'product_id'=>$pi->id));

            $pi['t_yuedan'] = 0;
            $pi['t_jidan'] = 0;
            $pi['t_banniandan'] = 0;
            foreach ($order_info as $ci){
                if($ci->order_type == 1){
                    $pi['t_yuedan'] = $ci->type_total_count;
                    $t_yuedan += $ci->type_total_count;
                    $t_yuedan_amount += $ci->type_total_amount;
                }
                elseif($ci->order_type == 2){
                    $pi['t_jidan'] = $ci->type_total_count;
                    $t_jidan += $ci->type_total_count;
                    $t_jidan_amount += $ci->type_total_amount;
                }
                elseif($ci->order_type == 3){
                    $pi['t_banniandan'] = $ci->type_total_count;
                    $t_banniandan += $ci->type_total_count;
                    $t_banniandan_amount += $ci->type_total_amount;
                }
            }

            $total_order_info = DB::select(DB::raw("select sum(op.total_count) as type_total_count, sum(op.total_amount) as type_total_amount, sum(op.avg) as type_avg_amount,op.order_type
                from orderproducts op, orders o
                where op.order_id = o.id and o.station_id = :station_id and op.product_id = :product_id and o.ordered_at <= :end_date group by op.order_type"),
                array('station_id'=>$current_station_id,'product_id'=>$pi->id,'end_date'=>$end_date));

            $pi['s_yuedan'] = 0;
            $pi['s_jidan'] = 0;
            $pi['s_banniandan'] = 0;
            foreach ($total_order_info as $si){
                if($si->order_type == 1){
                    $pi['s_yuedan'] = $si->type_total_count;
                    $s_yuedan += $si->type_total_count;
                    $s_yuedan_amount += $si->type_total_amount;
                }
                elseif($si->order_type == 2){
                    $pi['s_jidan'] = $si->type_total_count;
                    $s_jidan += $si->type_total_count;
                    $s_jidan_amount += $si->type_total_amount;
                }
                elseif($si->order_type == 3){
                    $pi['s_banniandan'] = $si->type_total_count;
                    $s_banniandan += $si->type_total_count;
                    $s_banniandan_amount += $si->type_total_amount;
                }
            }

            $delivery_info = DB::select(DB::raw("select sum(mdp.delivered_count) as current_delivered_count, sum(mdp.delivered_count * op.product_price) as delivered_amount ,op.order_type
                from milkmandeliveryplan mdp, orderproducts op where mdp.order_product_id = op.id and op.product_id = :product_id
                and mdp.station_id = :station_id and mdp.deliver_at <= :end_date group by op.order_type"),
                array('station_id'=>$current_station_id,'product_id'=>$pi->id,'end_date'=>$end_date));
            foreach ($delivery_info as $di){
                if($di->order_type == 1){
                    $pi['r_yuedan'] = $di->current_delivered_count;
                    $r_yuedan += $di->current_delivered_count;
                    $r_yuedan_amount += $di->delivered_amount;

                }
                elseif($di->order_type == 2){
                    $pi['r_jidan'] = $di->current_delivered_count;
                    $r_jidan += $di->current_delivered_count;
                    $r_jidan_amount += $di->delivered_amount;
                }
                elseif($di->order_type == 3){
                    $pi['r_banniandan'] = $di->current_delivered_count;
                    $r_banniandan +=$di->current_delivered_count;
                    $r_banniandan_amount += $di->delivered_amount;
                }
            }
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_STATION, '订单剩余量统计', SysLog::SYSLOG_OPERATION_VIEW);

        return view('naizhan.tongji.dingdanshenyuliang', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'product_info' => $product_info,
            't_yuedan' =>$t_yuedan,
            't_jidan' => $t_jidan,
            't_banniandan' => $t_banniandan,
            'r_yuedan' =>$r_yuedan,
            'r_jidan' => $r_jidan,
            'r_banniandan' =>$r_banniandan,
            't_yuedan_amount' => $t_yuedan_amount,
            't_jidan_amount' => $t_jidan_amount,
            't_banniandan_amount' => $t_banniandan_amount,
            'r_yuedan_amount' => $r_yuedan_amount,
            'r_jidan_amount' => $r_jidan_amount,
            'r_banniandan_amount' => $r_banniandan_amount,
            's_yuedan' =>$s_yuedan,
            's_jidan' => $s_jidan,
            's_banniandan' =>$s_banniandan,
            's_yuedan_amount' => $s_yuedan_amount,
            's_jidan_amount' => $s_jidan_amount,
            's_banniandan_amount' => $s_banniandan_amount,
            'start_date' =>$start_date,
            'end_date' =>$end_date,

        ]);
    }

    public function showNaipinpeisongri(Request $request){
        $child = 'naipinpeisongri';
        $parent = 'tongji';
        $current_page = 'naipinpeisongri';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $endDate_str = $currentDate->format('Y-m-d');
        $currentDate_str = $currentDate->format('Y-m-01');
        $start_date = $request->input('start_date');
        if($start_date == null){
            $start_date = $currentDate_str;
        }
        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        $current_factory_id = Auth::guard('naizhan')->user()->factory_id;
        $customer_delivers = array();
        $milkmandelivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at','>=',$start_date)->
        where('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)->where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)->get();
        $getByDates = $milkmandelivery_plans->groupby(function ($sort){return $sort->deliver_at;});
        foreach ($getByDates as $date=>$bydate){
            $customer_delivers_products = array();

            $products = Product::where('factory_id',$current_factory_id)->where('is_deleted',0)->get();
            foreach ($products as $p){
                $yuedan = 0;
                $jidan = 0;
                $baninadan = 0;
                foreach ($bydate as $bd){
                    if($p->id == $bd->order_product->product->id){
                        if($bd->order_type = OrderProduct::ORDER_PRODUCT_ORDERTYPE_YUEDAN){
                            $yuedan +=$bd->deliverd_count;
                        }
                        elseif($bd->order_type = OrderProduct::ORDER_PRODUCT_ORDERTYPE_JIDAN){
                            $jidan +=$bd->deliverd_count;
                        }
                        elseif($bd->order_type = OrderProduct::ORDER_PRODUCT_ORDERTYPE_BANNIANDAN){
                            $baninadan +=$bd->deliverd_count;
                        }
                    }
                }
                $customer_delivers_products['yuedan'][$p->id] = $yuedan;
                $customer_delivers_products['jidan'][$p->id] = $jidan;
                $customer_delivers_products['banniandan'][$p->id] = $baninadan;
                $customer_delivers_products['gift'][$p->id] = 0;
                $customer_delivers_products['channel'][$p->id] = 0;
            }
            $customer_delivers[$date]=$customer_delivers_products;
        }


        $station_delivers = array();
        $getFromDSDeliveryPlans = DSDeliveryPlan::where('station_id',$current_station_id)->where('deliver_at','>=',$start_date)->get()->groupby(function ($sort){return $sort->deliver_at;});
        foreach ($getFromDSDeliveryPlans as $date=>$gdp){
            $station_delivers_products = array();

            $products = Product::where('factory_id',$current_factory_id)->where('is_deleted',0)->get();
            foreach ($products as $p){
                $gift = 0;
                $channel = 0;
                foreach ($gdp as $g){
                    if($p->id == $g->product_id){
                        $gift += $g->retail + $g->test_drink;
                        $channel += $g->group_sale + $g->channel_sale;
                    }
                }
                $station_delivers_products['yuedan'][$p->id] = 0;
                $station_delivers_products['jidan'][$p->id] = 0;
                $station_delivers_products['banniandan'][$p->id] = 0;
                $station_delivers_products['gift'][$p->id] = $gift;
                $station_delivers_products['channel'][$p->id] = $channel;
            }
            $station_delivers[$date]=$station_delivers_products;
        }

        $result = array();
        foreach ($customer_delivers as $date=>$cd){
            foreach ($cd as $type=>$y){
                $result[$date][$type]=$y;
            }
        }
        foreach ($station_delivers as $date=>$sd){
            foreach ($sd as $type=>$y){
                $result[$date][$type]=$y;
            }
        }

        foreach ($result as $date=>$rs){
            $customer_orders = MilkManDeliveryPlan::where('station_id', $current_station_id)->where('deliver_at', $date)->
            where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)->
            where('type', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)->get()->groupby(function ($sort) {
                return $sort->order_id;
            })->count();

            $channel_orders = MilkManDeliveryPlan::where('station_id', $current_station_id)->where('deliver_at',$date)->
            where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)->wherebetween('type', [2, 3])->get()->groupby(function ($sort) {
                return $sort->order_id;
            })->count();

            $bottle_refunds = DSBottleRefund::where('station_id',$current_station_id)->where('time',$date)->get()->sum('milkman_return');

            $result[$date]['orders'] = $customer_orders + $channel_orders;
            $result[$date]['bottle_refunds'] = $bottle_refunds;
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_STATION, '奶品配送日统计', SysLog::SYSLOG_OPERATION_VIEW);

        $products_name = Product::where('factory_id',$current_factory_id)->where('is_deleted',0)->get(['id','name']);
        return view('naizhan.tongji.naipinpeisongri', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'products' =>$products_name,
            'result'=>$result,
        ]);
    }

    public function showPeisongyuanwei(Request $request){
        $child = 'peisongyuanwei';
        $parent = 'tongji';
        $current_page = 'peisongyuanwei';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        $current_station_id = Auth::guard('naizhan')->user()->station_id;

        $milkman_name = $request->input('milkman_name');
        if($milkman_name == null){
            $milkman_id ='';
        }
        $milkman_number = $request->input('milkman_number');
        if($milkman_number == null){
            $milkman_number = '';
        }
        
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $currentDate_str = $currentDate->format('Y-m-d');
        $startDate_str = $currentDate->format('Y-m-01');
        $start_date = $request->input('start_date');
        if($start_date == null){
            $start_date = $startDate_str;
        }
        $end_date = $request->input('end_date');
        if($end_date == null){
            $end_date = $currentDate_str;
        }

        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        $current_factory_id = Auth::guard('naizhan')->user()->factory_id;
        
        $milkman_delivers = array();

        $milkman_info = MilkMan::where('station_id', $current_station_id)
            ->where('name','LIKE','%'.$milkman_name.'%')
            ->where('number','LIKE','%'.$milkman_number.'%')
            ->get(['id']);

        foreach ($milkman_info as $key=>$mi) {
            $milkmandelivery_plans = MilkManDeliveryPlan::where('station_id', $current_station_id)->where('milkman_id', $mi->id)->wherebetween('deliver_at', [$start_date, $end_date])->
            where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)->get();

            $customer_delivers_products = array();
            $customer_delivers_products['milkman_name'] = MilkMan::find($mi->id)->name;
            $customer_orders = MilkManDeliveryPlan::where('station_id', $current_station_id)->wherebetween('deliver_at', [$start_date, $end_date])->
            where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)->where('milkman_id', $mi->id)->
            where('type', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)->get()->groupby(function ($sort) {
                return $sort->order_id;
            })->count();
            $channel_orders = MilkManDeliveryPlan::where('station_id', $current_station_id)->wherebetween('deliver_at', [$start_date, $end_date])->
            where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)->where('milkman_id', $mi->id)->
            wherebetween('type', [2, 3])->get()->groupby(function ($sort) {
                return $sort->order_id;
            })->count();
            $customer_delivers_products['orders_count'] = $customer_orders + $channel_orders;
            $customer_delivers_products['bottle_refund'] = MilkmanBottleRefund::where('milkman_id', $mi->id)->wherebetween('time', [$start_date, $end_date])->get()->sum('count');

            $products = Product::where('factory_id', $current_factory_id)->where('is_deleted', 0)->get();
            foreach ($products as $p) {
                $yuedan = 0;
                $jidan = 0;
                $baninadan = 0;
                $channel = 0;
                foreach ($milkmandelivery_plans as $bd) {
                    if ($bd->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER) {
                        if ($p->id == $bd->order_product->product->id) {
                            if ($bd->order_type = OrderProduct::ORDER_PRODUCT_ORDERTYPE_YUEDAN) {
                                $yuedan += $bd->deliverd_count;
                            } elseif ($bd->order_type = OrderProduct::ORDER_PRODUCT_ORDERTYPE_JIDAN) {
                                $jidan += $bd->deliverd_count;
                            } elseif ($bd->order_type = OrderProduct::ORDER_PRODUCT_ORDERTYPE_BANNIANDAN) {
                                $baninadan += $bd->deliverd_count;
                            }
                        }
                    } elseif ($bd->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_GROUP || $bd->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL) {
                        if ($p->id == $bd->order_product->product->id) {
                            $channel += $bd->delivered_count;
                        }
                    }
                }
                $customer_delivers_products['yuedan'][$p->id] = $yuedan;
                $customer_delivers_products['jidan'][$p->id] = $jidan;
                $customer_delivers_products['banniandan'][$p->id] = $baninadan;
                $customer_delivers_products['channel'][$p->id] = $channel;
            }
            $milkman_delivers[$key] = $customer_delivers_products;
        }
        
//        $milkmandelivery_plans = MilkManDeliveryPlan::where('station_id',$current_station_id)->wherebetween('deliver_at',[$start_date,$end_date])->
//        where('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)->get();
//        $getBymilkman = $milkmandelivery_plans->groupby(function ($sort){return $sort->milkman_id;});
//        foreach ($getBymilkman as $milkman=>$bymilkman){
//            $customer_delivers_products = array();
//            $customer_delivers_products['milkman_name'] = MilkMan::find($milkman)->name;
//            $customer_orders =  MilkManDeliveryPlan::where('station_id',$current_station_id)->wherebetween('deliver_at',[$start_date,$end_date])->
//            where('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)->where('milkman_id',$milkman)->
//            where('type',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)->get()->groupby(function ($sort){return $sort->order_id;})->count();
//            $channel_orders  =  MilkManDeliveryPlan::where('station_id',$current_station_id)->wherebetween('deliver_at',[$start_date,$end_date])->
//            where('status',MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)->where('milkman_id',$milkman)->
//            wherebetween('type',[2,3])->get()->groupby(function ($sort){return $sort->order_id;})->count();
//            $customer_delivers_products['orders_count'] = $customer_orders + $channel_orders;
//            $customer_delivers_products['bottle_refund'] = MilkmanBottleRefund::where('milkman_id',$milkman)->wherebetween('time',[$start_date,$end_date])->get()->sum('count');
//
//            $products = Product::where('factory_id',$current_factory_id)->where('is_deleted',0)->get();
//            foreach ($products as $p){
//                $yuedan = 0;
//                $jidan = 0;
//                $baninadan = 0;
//                $channel = 0;
//                foreach ($bymilkman as $bd){
//                    if($bd->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER){
//                        if($p->id == $bd->order_product->product->id){
//                            if($bd->order_type = OrderProduct::ORDER_PRODUCT_ORDERTYPE_YUEDAN){
//                                $yuedan +=$bd->deliverd_count;
//                            }
//                            elseif($bd->order_type = OrderProduct::ORDER_PRODUCT_ORDERTYPE_JIDAN){
//                                $jidan +=$bd->deliverd_count;
//                            }
//                            elseif($bd->order_type = OrderProduct::ORDER_PRODUCT_ORDERTYPE_BANNIANDAN){
//                                $baninadan +=$bd->deliverd_count;
//                            }
//                        }
//                    }
//                    elseif ($bd->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_GROUP || $bd->type == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL){
//                        if($p->id == $bd->order_product->product->id){
//                            $channel +=$bd->delivered_count;
//                        }
//                    }
//                }
//                $customer_delivers_products['yuedan'][$p->id] = $yuedan;
//                $customer_delivers_products['jidan'][$p->id] = $jidan;
//                $customer_delivers_products['banniandan'][$p->id] = $baninadan;
//                $customer_delivers_products['channel'][$p->id] = $channel;
//            }
//            $milkman_delivers[$milkman]=$customer_delivers_products;
//        }

        $products = Product::where('factory_id',$current_factory_id)->where('is_deleted',0)->get();

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_STATION, '配送员业务统计', SysLog::SYSLOG_OPERATION_VIEW);

        return view('naizhan.tongji.peisongyuanwei', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'products'=>$products,
            'milkman_delivers'=>$milkman_delivers,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'milkman_name'=>$milkman_name,
            'milkman_number'=>$milkman_number,
        ]);
    }
    //
}
