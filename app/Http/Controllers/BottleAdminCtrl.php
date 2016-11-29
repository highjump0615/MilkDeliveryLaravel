<?php

namespace App\Http\Controllers;

use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\MilkMan;
use App\Model\DeliveryModel\MilkmanBottleRefund;
use App\Model\DeliveryModel\MilkManDeliveryPlan;
use App\Model\FactoryModel\FactoryBottleType;
use App\Model\FactoryModel\FactoryBoxType;
use App\Model\FactoryModel\MFBottle;
use App\Model\FactoryModel\MFBox;
use App\Model\StationModel\DSBottleRefund;
use App\Model\StationModel\DSBoxRefund;
use App\Model\UserModel\Page;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class BottleAdminCtrl extends Controller
{
    public function gongchangPingkuangShow(Request $request){
        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;

        $child = 'pingkuang_child';
        $parent = 'pingkuang';
        $current_page = 'pingkuang';

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $current_date_str = $currentDate->format('Y-m-d');
        $current_month_day = $currentDate->format('Y-m-01');

        $bottle_name = $request->input('bottle_name');
        if($bottle_name == null){
            $bottle_name = '';
        }
        $box_name = $request->input('box_name');
        if($box_name == null){
            $box_name = '';
        }
        $start_date = $request->input('start_date');
        if($start_date == null){
            $start_date = $current_month_day;
        }

        $end_date = $request->input('end_date');
        if($end_date == null){
            $end_date = $current_date_str;
        }

        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        $bottle_types = FactoryBottleType::where('factory_id',$current_factory_id)->where('is_deleted',0)->get();
        $box_types = FactoryBoxType::where('factory_id',$current_factory_id)->where('is_deleted',0)->get();

        $today_bottle_info = array();
        foreach ($bottle_types as $bt){
            $today_bottle_info[$bt->id]['name'] = $bt->name;

            if(MFBottle::where('factory_id',$current_factory_id)->where('bottle_type',$bt->id)->orderBy('time','desc')->first() != null)
                $today_bottle_info[$bt->id]['init_store_count'] = MFBottle::where('factory_id',$current_factory_id)->where('bottle_type',$bt->id)->orderBy('time','desc')->first()->final_count;
            else
                $today_bottle_info[$bt->id]['init_store_count'] = 0;

            $station_refunds_count = 0;
            $station_refund = DB::select(DB::raw("select sum(df.return_to_factory) as total from dsbottlerefunds df,deliverystations ds where ds.id = df.station_id and ds.factory_id = :factory_id and df.bottle_type = :bottle_type and df.time =:refund_time"),
                array('factory_id'=>$current_factory_id,'refund_time'=>$current_date_str,'bottle_type'=>$bt->id));
            foreach ($station_refund as $sr){
                $station_refunds_count += $sr->total;
            }
            $today_bottle_info[$bt->id]['station_refunds_count']= $station_refunds_count;
        }

        $today_box_info = array();
        foreach ($box_types as $bx){
            $today_box_info[$bx->id]['name'] = $bx->name;

            if (MFBox::where('factory_id',$current_factory_id)->where('box_type',$bx->id)->orderBy('time','desc')->first() != null)
                $today_box_info[$bx->id]['init_store_count'] = MFBox::where('factory_id',$current_factory_id)->where('box_type',$bx->id)->orderBy('time','desc')->first()->final_count;
            else
                $today_box_info[$bx->id]['init_store_count'] = 0;

            $station_refunds_count = 0;
            $station_refund = DB::select(DB::raw("select sum(df.return_to_factory) as total from dsboxrefunds df,deliverystations ds where ds.id = df.station_id and ds.factory_id = :factory_id and df.box_type = :box_type and
 df.time =:refund_time"),
                array('factory_id'=>$current_factory_id,'refund_time'=>$current_date_str,'box_type'=>$bx->id));
            foreach ($station_refund as $sr){
                $station_refunds_count += $sr->total;
            }
            $today_box_info[$bx->id]['station_refunds_count']= $station_refunds_count;
        }

        $bottles = array();
        $dsbottlerefunds = MFBottle::where('factory_id',$current_factory_id)->get();
        $i = 0;
        foreach ($dsbottlerefunds as $bottle){
            if($start_date <= $bottle->time && $end_date >= $bottle->time){
                $bottles[$i]['time'] = $bottle->time;
                $bottles[$i]['type'] = $this->getBottleType($bottle->bottle_type);
                $bottles[$i]['init_store_count'] = $bottle->init_store_count;
                $bottles[$i]['station_refunds_count'] = $bottle->station_refunds_count;
                $bottles[$i]['etc_refunds_count'] = $bottle->etc_refunds_count;
                $bottles[$i]['production_count'] = $bottle->production_count;
                $bottles[$i]['store_damaged_count'] = $bottle->store_damage_count;
                $bottles[$i]['final_count'] = $bottle->final_count;
                $i++;
            }
        }
        $boxes = array();
        $dsboxrefunds = MFBox::where('factory_id',$current_factory_id)->get();
        $j = 0;
        foreach ($dsboxrefunds as $box){
            if($start_date <= $box->time && $end_date >= $box->time){
                $boxes[$j]['time'] = $box->time;
                $boxes[$j]['type'] = $this->getBoxType($box->box_type);
                $boxes[$j]['init_store_count'] = $box->init_store_count;
                $boxes[$j]['station_refunds_count'] = $box->station_refunds_count;
                $boxes[$j]['etc_refunds_count'] = $box->etc_refunds_count;
                $boxes[$j]['production_count'] = $box->production_count;
                $boxes[$j]['store_damaged_count'] = $box->store_damage_count;
                $boxes[$j]['final_count'] = $box->final_count;
                $j++;
            }
        }

        $total_info = array();
        foreach ($bottles as $bt){
            $total_info[$bt['time']][$bt['type']]['init_store_count'] = $bt['init_store_count'];
            $total_info[$bt['time']][$bt['type']]['station_refunds_count'] = $bt['station_refunds_count'];
            $total_info[$bt['time']][$bt['type']]['etc_refunds_count'] = $bt['etc_refunds_count'];
            $total_info[$bt['time']][$bt['type']]['production_count'] = $bt['production_count'];
            $total_info[$bt['time']][$bt['type']]['store_damaged_count'] = $bt['store_damaged_count'];
            $total_info[$bt['time']][$bt['type']]['final_count'] = $bt['final_count'];
        }
        foreach ($boxes as $bx){
            $total_info[$bx['time']][$bx['type']]['init_store_count'] = $bx['init_store_count'];
            $total_info[$bx['time']][$bx['type']]['station_refunds_count'] = $bx['station_refunds_count'];
            $total_info[$bx['time']][$bx['type']]['etc_refunds_count'] = $bx['etc_refunds_count'];
            $total_info[$bx['time']][$bx['type']]['production_count'] = $bx['production_count'];
            $total_info[$bx['time']][$bx['type']]['store_damaged_count'] = $bx['store_damaged_count'];
            $total_info[$bx['time']][$bx['type']]['final_count'] = $bx['final_count'];
        }

        $today_status = 0;
        $todaybottlerefunds = MFBottle::where('factory_id',$current_factory_id)->where('time',$current_date_str)->get();
        if($todaybottlerefunds->first() != null){
            if($todaybottlerefunds->first()->final_count != null)
                $today_status = 1;
        }
        $todayboxrefunds = MFBox::where('factory_id',$current_factory_id)->where('time',$current_date_str)->get();
        if($todayboxrefunds->first() != null){
            if($todayboxrefunds->first()->final_count != null)
                $today_status = 1;
        }
//        return $refunds_info;
        return view('gongchang.pingkuang.pingkuang', [
            'pages'             => $pages,
            'child'             => $child,
            'parent'            => $parent,
            'current_page'      => $current_page,
            'start_date'        =>$start_date,
            'end_date'          =>$end_date,
            'bottle_name'       =>$bottle_name,
            'box_name'          =>$box_name,
            'today_bottle_info' =>$today_bottle_info,
            'today_box_info'    =>$today_box_info,
            'refund_info'       =>$total_info,
            'today_status'      =>$today_status,
        ]);
    }

    public function SaveGongchangPingkuang(Request $request){
        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $current_date_str = $currentDate->format('Y-m-d');
        $type = $request->input('type');
        $object_type = $request->input('object_type');
        $init_store_count = $request->input('init_store_count');
        $station_refunds_count = $request->input('station_refunds_count');
        $etc_refunds_count = $request->input('etc_refunds_count');
        $production_count = $request->input('production_count');
        $store_damage_count = $request->input('store_damage_count');
        $final_count = $request->input('final_count');
        $status = 0;
        if($type == 0){
            $new_bottle_refund = new MFBottle();
            $new_bottle_refund->factory_id = $current_factory_id;
            $new_bottle_refund->time = $current_date_str;
            $new_bottle_refund->bottle_type = $object_type;
            $new_bottle_refund->init_store_count = $init_store_count;
            $new_bottle_refund->station_refunds_count = $station_refunds_count;
            $new_bottle_refund->etc_refunds_count = $etc_refunds_count;
            $new_bottle_refund->production_count = $production_count;
            $new_bottle_refund->store_damage_count = $store_damage_count;
            $new_bottle_refund->final_count = $final_count;
            $new_bottle_refund->save();
            $status = 1;
        }
        elseif ($type == 1){
            $new_box_refund = new MFBox();
            $new_box_refund->factory_id = $current_factory_id;
            $new_box_refund->time = $current_date_str;
            $new_box_refund->box_type = $object_type;
            $new_box_refund->init_store_count = $init_store_count;
            $new_box_refund->station_refunds_count = $station_refunds_count;
            $new_box_refund->etc_refunds_count = $etc_refunds_count;
            $new_box_refund->production_count = $production_count;
            $new_box_refund->store_damage_count = $store_damage_count;
            $new_box_refund->final_count = $final_count;
            $new_box_refund->save();
            $status = 1;
        }
        return Response::json(['status'=>$status]);
    }

    public function getBottleType($bottle_id){
        return FactoryBottleType::find($bottle_id)->name;
    }

    public function getBoxType($box_id){
        return FactoryBoxType::find($box_id)->name;
    }

    /**
     * 配送员瓶框回收记录
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showNaizhanPeisonguanpingkuang(Request $request){

        $milkman_id = $request->input('milkman_id');

        $current_station_id = $this->getCurrentStationId();

        // 页面信息
        $child = 'peisongyuanpingkuang';
        $parent = 'pingkuang';
        $current_page = 'peisongyuanpingkuang';
        $pages = Page::where('backend_type', Page::NAIZHAN)->where('parent_page', '0')->orderby('order_no')->get();

        // 配送员
        if($milkman_id == ''){
            $milkman = MilkMan::where('station_id',$current_station_id)->get()->first();
            if($milkman != null){
                $milkman_id = $milkman->id;
            }
        }

        // 日期范围
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $current_date_str = $currentDate->format('Y-m-d');
        $first_day_of_current_month = $currentDate->format('Y-m-01');

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if ($start_date == ''){
            $start_date = $first_day_of_current_month;
        }
        if ($end_date == ''){
            $end_date = $current_date_str;
        }

        // 配送数量
        $milkmans = MilkMan::where('station_id',$current_station_id)->get();
        $milkman_delivered_counts = MilkManDeliveryPlan::where('milkman_id',$milkman_id)
            ->where('delivered_count','<>','')
            ->orderby('deliver_at','spec')
            ->get();

        $delivered_info = array();
        $i = 0;

        foreach ($milkman_delivered_counts as $mdc){
            if($start_date <= $mdc->deliver_at && $end_date >= $mdc->deliver_at){
                $delivered_info[$i]['deliver_at'] = $mdc->deliver_at;
                $delivered_info[$i]['bottle_type'] = $this->getBottleType($mdc->order_product->product->bottle_type);
                $delivered_info[$i]['product_count'] = $mdc->delivered_count;
                $i++;
            }
        }

//        foreach ($milkman_delivered_counts as $mdc){
//            if($start_date != ''){
//                if($end_date != ''){
//                    if($start_date <= $mdc->deliver_at && $end_date >= $mdc->deliver_at){
//                        $delivered_info[$i]['deliver_at'] = $mdc->deliver_at;
//                        $delivered_info[$i]['bottle_type'] = $this->getBottleType($mdc->order_product->product->bottle_type);
//                        $delivered_info[$i]['product_count'] = $mdc->delivered_count;
//                        $i++;
//                    }
//                }
//                else{
//                    if($start_date <= $mdc->deliver_at){
//                        $delivered_info[$i]['deliver_at'] = $mdc->deliver_at;
//                        $delivered_info[$i]['bottle_type'] = $this->getBottleType($mdc->order_product->product->bottle_type);
//                        $delivered_info[$i]['product_count'] = $mdc->delivered_count;
//                        $i++;
//                    }
//                }
//            }
//            else{
//                if($end_date != ''){
//                    if($end_date >= $mdc->deliver_at){
//                        $delivered_info[$i]['deliver_at'] = $mdc->deliver_at;
//                        $delivered_info[$i]['bottle_type'] = $this->getBottleType($mdc->order_product->product->bottle_type);
//                        $delivered_info[$i]['product_count'] = $mdc->delivered_count;
//                        $i++;
//                    }
//                }
//                else{
//                    $delivered_info[$i]['deliver_at'] = $mdc->deliver_at;
//                    $delivered_info[$i]['bottle_type'] = $this->getBottleType($mdc->order_product->product->bottle_type);
//                    $delivered_info[$i]['product_count'] = $mdc->delivered_count;
//                    $i++;
//                }
//            }
//        }

        // 回收量
        $refund_info = array();
        $j = 0;
        $milkmanbottlerefunds = MilkmanBottleRefund::where('milkman_id',$milkman_id)->orderby('time','bottle_type')->get();
        foreach ($milkmanbottlerefunds as $mb){
            if($start_date <= $mb->time && $end_date >= $mb->time){
                $refund_info[$j]['refund_date'] = $mb->time;
                $refund_info[$j]['bottle_type'] = $this->getBottleType($mb->bottle_type);
                $refund_info[$j]['refund_count'] = $mb->count;
                $j++;
            }
        }

        $total_info = array();
        foreach ($delivered_info as $di){
            $total_info[$di['deliver_at']][$di['bottle_type']]['delivered'] = $di['product_count'];
            $total_info[$di['deliver_at']][$di['bottle_type']]['refund'] = '';
        }
        foreach ($refund_info as $ri){
            $total_info[$ri['refund_date']][$ri['bottle_type']]['refund'] = $ri['refund_count'];
            $total_info[$ri['refund_date']][$ri['bottle_type']]['delivered'] = '';
        }

        return view('naizhan.pingkuang.peisongyuanpingkuang', [
            'pages'                 => $pages,
            'child'                 => $child,
            'parent'                => $parent,
            'current_page'          => $current_page,

            'milkmans'              =>$milkmans,
            'milkmanbottlerefunds'  =>$total_info,
            'start_date'            =>$start_date,
            'end_date'              =>$end_date,
            'milkman_id'            =>$milkman_id,
        ]);
    }

    /**
     * 打开瓶框收回记录
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showNaizhanPingkuangshouhui(Request $request){

        $current_station_id = $this->getCurrentStationId();
        $current_factory_id = $this->getCurrentFactoryId(false);

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $deliver_date_str = $currentDate->format('Y-m-d');

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        // 页面信息
        $child = 'pingkuangshouhui';
        $parent = 'pingkuang';
        $current_page = 'pingkuangshouhui';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        //
        // 计算日期范围
        //
        $current_date_str = $currentDate->format('Y-m-d');
        $first_day_of_current_month = $currentDate->format('Y-m-01');
        $currentDate->add(\DateInterval::createFromDateString('yesterday'));
        $produce_date_str = $currentDate->format("Y-m-d");

        if($start_date == ''){
            $start_date = $first_day_of_current_month;
        }
        if($end_date == ''){
            $end_date = $current_date_str;
        }

        //
        // 根据奶站地址信息、获取该奶站的产品信息
        //
        $station_addr = DeliveryStation::find($current_station_id)->address;
        $station_addr = explode(' ',$station_addr);
        $station_addr = $station_addr[0]." ".$station_addr[1]." ".$station_addr[2];

        $bottle_types = DB::select(DB::raw("select DISTINCT p.bottle_type from products p, productprice pp
                    where p.id = pp.product_id and p.factory_id = $current_factory_id and pp.sales_area LIKE '%$station_addr%'"));

        $box_types = DB::select(DB::raw("select DISTINCT p.basket_spec from products p, productprice pp
                    where p.id = pp.product_id and p.factory_id = $current_factory_id and pp.sales_area LIKE '%$station_addr%'"));

        //
        // 今日奶瓶状态
        //
        $today_bottle_info = array();
        foreach ($bottle_types as $bt){
            $today_bottle_info[$bt->bottle_type]['name'] = FactoryBottleType::find($bt->bottle_type)->name;

            //
            // 期初库存
            //
            if (DSBottleRefund::where('time',$current_date_str)
                    ->where('station_id',$current_station_id)
                    ->where('bottle_type',$bt->bottle_type)
                    ->get()
                    ->first() != null) {

                $today_bottle_info[$bt->bottle_type]['init_count'] = (int)DSBottleRefund::where('time',$current_date_str)
                        ->where('station_id',$current_station_id)
                        ->where('bottle_type',$bt->bottle_type)
                        ->get()
                        ->first()
                        ->init_store;
            }
            else{
                if (DSBottleRefund::where('station_id',$current_station_id)
                        ->where('bottle_type',$bt->bottle_type)
                        ->orderBy('time','desc')
                        ->first() != null) {

                    $today_bottle_info[$bt->bottle_type]['init_count'] = (int)DSBottleRefund::where('station_id', $current_station_id)
                        ->where('bottle_type', $bt->bottle_type)
                        ->orderBy('time', 'desc')
                        ->first()
                        ->end_store;
                }
                else {
                    $today_bottle_info[$bt->bottle_type]['init_count'] = 0;
                }
            }

            //
            // 配送员回收量
            //
            $milkman_refund_count = 0;

            $milkman_refund = DB::select(DB::raw(
                    "select sum(mf.count) as total from dsmilkmanbottlerefunds mf,milkman mm " .
                    "where mm.station_id = :station_id and mm.id = mf.milkman_id and mf.time =:refund_time and mf.bottle_type = :bottle_type"
                ),
                array('station_id'=>$current_station_id,'refund_time'=>$current_date_str,'bottle_type'=>$bt->bottle_type)
            );

            foreach ($milkman_refund as $mr){
                $milkman_refund_count += (int)$mr->total;
            }

            $today_bottle_info[$bt->bottle_type]['milkman_refund'] = $milkman_refund_count;

            //
            // 返厂数量
            //
            $current_return_to_factory = DSBottleRefund::where('time',$current_date_str)
                ->where('station_id',$current_station_id)
                ->where('bottle_type',$bt->bottle_type)
                ->get()
                ->first();

            if($current_return_to_factory != null){
                $today_bottle_info[$bt->bottle_type]['return_to_factory'] = (int)$current_return_to_factory->return_to_factory;
            }
            else{
                $today_bottle_info[$bt->bottle_type]['return_to_factory'] = 0;
            }

            //
            // 站内破损
            //
            $damaged = DSBottleRefund::where('time',$current_date_str)
                ->where('station_id',$current_station_id)
                ->where('bottle_type',$bt->bottle_type)
                ->get()
                ->first();

            if($damaged != null){
                $today_bottle_info[$bt->bottle_type]['damaged'] = (int)$damaged->station_damaged;
            }
            else{
                $today_bottle_info[$bt->bottle_type]['damaged'] = 0;
            }

            //
            // 收货数量
            //
            $today_received_count = 0;

            $today_received = DB::select(DB::raw(
                    "select sum(confirm_count) as total_received from dsproductionplan dp, products p " .
                    "where dp.station_id = :station_id and dp.status = 7 and dp.produce_end_at = :produce_end_date and dp.product_id = p.id and p.bottle_type = :bottle_type"
                ),
                array('station_id'=>$current_station_id,'produce_end_date'=>$produce_date_str,'bottle_type'=>$bt->bottle_type)
            );

            foreach ($today_received as $tr){
                $today_received_count += (int)$tr->total_received;
            }

            $today_bottle_info[$bt->bottle_type]['received'] = $today_received_count;
        }

        //
        // 今日奶框状态
        //
        $today_box_info = array();
        foreach ($box_types as $bx){
            $today_box_info[$bx->basket_spec]['name'] = FactoryBoxType::find($bx->basket_spec)->name;

            //
            // 期初库存
            //
            if (DSBoxRefund::where('time',$current_date_str)
                    ->where('station_id',$current_station_id)
                    ->where('box_type',$bx->basket_spec)
                    ->get()
                    ->first() != null) {

                $today_box_info[$bx->basket_spec]['init_count'] = (int)DSBoxRefund::where('time',$current_date_str)
                    ->where('station_id',$current_station_id)
                    ->where('box_type',$bx->basket_spec)
                    ->get()
                    ->first()->init_store;
            }
            else {
                if (DSBoxRefund::where('station_id',$current_station_id)
                        ->where('box_type',$bx->basket_spec)
                        ->orderBy('time','desc')
                        ->first() != null) {

                    $today_box_info[$bx->basket_spec]['init_count'] = (int)DSBoxRefund::where('station_id', $current_station_id)
                        ->where('box_type', $bx->basket_spec)
                        ->orderBy('time', 'desc')
                        ->first()->end_store;
                }
                else {
                    $today_box_info[$bx->basket_spec]['init_count'] = 0;
                }
            }

            //
            // 返厂数量
            //
            $current_return_to_factory = DSBoxRefund::where('time',$current_date_str)
                ->where('station_id',$current_station_id)
                ->where('box_type',$bx->basket_spec)
                ->get()
                ->first();

            if ($current_return_to_factory != null){
                $today_box_info[$bx->basket_spec]['return_to_factory'] = (int)$current_return_to_factory->return_to_factory;
            }
            else {
                $today_box_info[$bx->basket_spec]['return_to_factory'] = 0;
            }

            //
            // 站内破损
            //
            $damaged = DSBoxRefund::where('time',$current_date_str)
                ->where('station_id',$current_station_id)
                ->where('box_type',$bx->basket_spec)
                ->get()
                ->first();

            if($damaged != null){
                $today_box_info[$bx->basket_spec]['damaged'] = (int)$damaged->station_damaged;
            }
            else{
                $today_box_info[$bx->basket_spec]['damaged'] = 0;
            }
        }

        //
        // 今日状态
        //
        $today_status = 0;
        $todaybottlerefunds = DSBottleRefund::where('station_id',$current_station_id)->where('time',$deliver_date_str)->get();
        if($todaybottlerefunds->first() != null){
            if($todaybottlerefunds->first()->end_store != null)
                $today_status = 1;
        }

        $todayboxrefunds = DSBoxRefund::where('station_id',$current_station_id)->where('time',$deliver_date_str)->get();
        if($todayboxrefunds->first() != null){
            if($todayboxrefunds->first()->end_store != null)
                $today_status = 1;
        }

        //
        // 日期范围
        //
        $bottles = array();
        $dsbottlerefunds = DSBottleRefund::where('station_id',$current_station_id)->get();

        $i = 0;
        foreach ($dsbottlerefunds as $bottle){
            if($start_date <= $bottle->time && $end_date >= $bottle->time){

                $bottles[$i]['time'] = $bottle->time;
                $bottles[$i]['type'] = $this->getBottleType($bottle->bottle_type);

                // 今日的数据是直接使用上面的，没保存也能查看
                if ($bottle->time == $current_date_str) {
                    $bottles[$i]['init_store'] = $today_bottle_info[$bottle->bottle_type]['init_count'];
                    $bottles[$i]['milkman_return'] = $today_bottle_info[$bottle->bottle_type]['milkman_refund'];
                    $bottles[$i]['return_to_factory'] = $today_bottle_info[$bottle->bottle_type]['return_to_factory'];
                    $bottles[$i]['station_damaged'] = $today_bottle_info[$bottle->bottle_type]['damaged'];
                    $bottles[$i]['received'] = $today_bottle_info[$bottle->bottle_type]['received'];
                }
                else {
                    $bottles[$i]['init_store'] = $bottle->init_store;
                    $bottles[$i]['milkman_return'] = $bottle->milkman_return;
                    $bottles[$i]['return_to_factory'] = $bottle->return_to_factory;
                    $bottles[$i]['station_damaged'] = $bottle->station_damaged;
                    $bottles[$i]['received'] = $bottle->received;
                }

                // 期末库存
                $bottles[$i]['end_store'] = $bottles[$i]['init_store'] + $bottles[$i]['milkman_return'] - $bottles[$i]['return_to_factory'] - $bottles[$i]['station_damaged'];

                $i++;
            }
        }

        $boxes = array();
        $dsboxrefunds = DSBoxRefund::where('station_id',$current_station_id)->get();

        $j = 0;
        foreach ($dsboxrefunds as $box){
            if ($start_date <= $box->time && $end_date >= $box->time){

                $boxes[$j]['time'] = $box->time;
                $boxes[$j]['type'] = $this->getBoxType($box->box_type);
                $boxes[$j]['received'] = $box->received;

                // 今日的数据是直接使用上面的，没保存也能查看
                if ($box->time == $current_date_str) {
                    $boxes[$j]['init_store'] = $today_box_info[$box->box_type]['init_count'];
                    $boxes[$j]['return_to_factory'] = $today_box_info[$box->box_type]['return_to_factory'];
                    $boxes[$j]['station_damaged'] = $today_box_info[$box->box_type]['damaged'];
                }
                else {
                    $boxes[$j]['init_store'] = $box->init_store;
                    $boxes[$j]['return_to_factory'] = $box->return_to_factory;
                    $boxes[$j]['station_damaged'] = $box->station_damaged;
                }

                // 期末库存
                $boxes[$j]['end_store'] = $boxes[$j]['init_store'] - $boxes[$j]['return_to_factory'] - $boxes[$j]['station_damaged'];

                $j++;
            }
        }

        $total_info = array();
        foreach ($bottles as $bt){
            $total_info[$bt['time']][$bt['type']]['init_store'] = $bt['init_store'];
            $total_info[$bt['time']][$bt['type']]['milkman_return'] = $bt['milkman_return'];
            $total_info[$bt['time']][$bt['type']]['return_to_factory'] = $bt['return_to_factory'];
            $total_info[$bt['time']][$bt['type']]['station_damaged'] = $bt['station_damaged'];
            $total_info[$bt['time']][$bt['type']]['end_store'] = $bt['end_store'];
            $total_info[$bt['time']][$bt['type']]['received'] = $bt['received'];
        }
        foreach ($boxes as $bx){
            $total_info[$bx['time']][$bx['type']]['init_store'] = $bx['init_store'];
            $total_info[$bx['time']][$bx['type']]['milkman_return'] = '';
            $total_info[$bx['time']][$bx['type']]['return_to_factory'] = $bx['return_to_factory'];
            $total_info[$bx['time']][$bx['type']]['station_damaged'] = $bx['station_damaged'];
            $total_info[$bx['time']][$bx['type']]['end_store'] = $bx['end_store'];
            $total_info[$bx['time']][$bx['type']]['received'] = $bx['received'];
        }

        return view('naizhan.pingkuang.pingkuangshouhui', [
            'pages'                 => $pages,
            'child'                 => $child,
            'parent'                => $parent,
            'current_page'          => $current_page,

            'today_status'          =>$today_status,
            'todaybottlerefunds'    =>$today_bottle_info,
            'todayboxrefunds'       =>$today_box_info,
            'refund_info'           => $total_info,
            'start_date'            =>$start_date,
            'end_date'              =>$end_date,
        ]);
    }

    public function confirmTodaysBottle(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $current_date_str = $currentDate->format('Y-m-d');
        $type = $request->input('type');
        $bottle_type = $request->input('bottle_type');
        $init_store = $request->input('init_store');
        $milkman_refund = $request->input('milkman_refund');
        $station_damaged = $request->input('station_damaged');
        $end_store = $request->input('end_store');
        $recipient = $request->input('received');
        $status = 0;
        if($type == 0){
            $bottles = DSBottleRefund::where('station_id',$current_station_id)->where('time',$current_date_str)->where('bottle_type',$bottle_type)->get()->first();
            if($bottles != null){
                $bottles->init_store = $init_store;
                $bottles->milkman_return = $milkman_refund;
                $bottles->station_damaged = $station_damaged;
                $bottles->end_store = $end_store;
                $bottles->received = $recipient;
                $bottles->save();
            }
            else{
                $new_bottle_refund = new DSBottleRefund();
                $new_bottle_refund->station_id = $current_station_id;
                $new_bottle_refund->time = $current_date_str;
                $new_bottle_refund->bottle_type = $bottle_type;
                $new_bottle_refund->init_store = $init_store;
                $new_bottle_refund->milkman_return = $milkman_refund;
                $new_bottle_refund->station_damaged = $station_damaged;
                $new_bottle_refund->end_store = $end_store;
                $new_bottle_refund->received = $recipient;
                $new_bottle_refund->save();
            }
            $status = 1;
        }
        elseif ($type == 1){
            $boxes = DSBoxRefund::where('station_id',$current_station_id)->where('time',$current_date_str)->where('box_type',$bottle_type)->get()->first();
            if($boxes != null){
                $boxes->init_store = $init_store;
                $boxes->station_damaged = $station_damaged;
                $boxes->end_store = $end_store;
                $boxes->save();
            }
            else{
                $new_box_refund = new DSBoxRefund();
                $new_box_refund->station_id = $current_station_id;
                $new_box_refund->time = $current_date_str;
                $new_box_refund->box_type = $bottle_type;
                $new_box_refund->init_store = $init_store;
                $new_box_refund->station_damaged = $station_damaged;
                $new_box_refund->end_store = $end_store;
                $new_box_refund->save();
            }
            $status = 1;
        }
        return $status;
    }

    public function showNaizhanPingkuangtongji(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        $current_factory_id = Auth::guard('naizhan')->user()->factory_id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $current_year_str = $currentDate->format('Y');
        $child = 'pingkuangtongji';
        $parent = 'pingkuang';
        $current_page = 'pingkuangtongji';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        $bottles = array();

        $station_addr = DeliveryStation::find($current_station_id)->address;
        $station_addr = explode(' ',$station_addr);
        $station_addr = $station_addr[0]." ".$station_addr[1]." ".$station_addr[2];
        $bottletypes = DB::select(DB::raw("select DISTINCT p.bottle_type from products p, productprice pp
                    where p.id = pp.product_id and p.factory_id = $current_factory_id and pp.sales_area LIKE '%$station_addr%'"));


        $dsbottles = DSBottleRefund::where('station_id',$current_station_id)->get();

        for($i = 1; $i< 13; $i++){
            foreach ($bottletypes as $bt){
                $bottles[$i][$bt->bottle_type]['month']= '';
                $bottles[$i][$bt->bottle_type]['type'] = $this->getBottleType($bt->bottle_type);
                $bottles[$i][$bt->bottle_type]['init'] = 0;
                $bottles[$i][$bt->bottle_type]['milkman'] = 0;
                $bottles[$i][$bt->bottle_type]['from_factory'] = 0;
                $bottles[$i][$bt->bottle_type]['received'] = 0;
                $bottles[$i][$bt->bottle_type]['damaged'] = 0;
            }
        }
        foreach ($dsbottles as $bf){
            $time = $bf->time;
            $cur_date = explode("-",$time);
            if($cur_date[0] == $current_year_str){
                for($i = 1; $i< 13; $i++){
                    if(intval($cur_date[1]) == $i){
                        foreach ($bottletypes as $bt){
                            if($bt->bottle_type == $bf->bottle_type){
                                $bottles[$i][$bt->bottle_type]['month']=$i;
                                $bottles[$i][$bt->bottle_type]['init'] += $bf->init_store;
                                $bottles[$i][$bt->bottle_type]['milkman'] += $bf->milkman_return;
                                $bottles[$i][$bt->bottle_type]['from_factory'] += $bf->return_to_factory;
                                $bottles[$i][$bt->bottle_type]['received'] += $bf->received;
                                $bottles[$i][$bt->bottle_type]['damaged'] += $bf->station_damaged;
                            }
                        }
                    }
                }
            }
        }

        $boxes = array();
        $dsboxes = DSBoxRefund::where('station_id',$current_station_id)->get();
        $boxtypes = DB::select(DB::raw("select DISTINCT p.basket_spec from products p, productprice pp
                    where p.id = pp.product_id and p.factory_id = $current_factory_id and pp.sales_area LIKE '%$station_addr%'"));
        for($i = 1; $i< 13; $i++){
            foreach ($boxtypes as $bx){
                $boxes[$i][$bx->basket_spec]['month']= '';
                $boxes[$i][$bx->basket_spec]['type']= $this->getBoxType($bx->basket_spec);
                $boxes[$i][$bx->basket_spec]['init'] = 0;
                $boxes[$i][$bx->basket_spec]['milkman'] = 0;
                $boxes[$i][$bx->basket_spec]['from_factory'] = 0;
                $boxes[$i][$bx->basket_spec]['received'] = 0;
                $boxes[$i][$bx->basket_spec]['damaged'] = 0;
            }
        }
        foreach ($dsboxes as $xf){
            $time = $xf->time;
            $cur_date = explode("-",$time);
            if($cur_date[0] == $current_year_str){
                for($i = 1; $i< 13; $i++){
                    if(intval($cur_date[1]) == $i){
                        foreach ($boxtypes as $bx){
                            if($bx->basket_spec == $xf->box_type){
                                $boxes[$i][$bx->basket_spec]['month']=$i;
                                $boxes[$i][$bx->basket_spec]['init'] += $xf->init_store;
                                $boxes[$i][$bx->basket_spec]['milkman'] = '';
                                $boxes[$i][$bx->basket_spec]['from_factory'] += $xf->return_to_factory;
                                $boxes[$i][$bx->basket_spec]['received'] += $xf->received;
                                $boxes[$i][$bx->basket_spec]['damaged'] += $xf->satation_damaged;
                            }
                        }
                    }
                }
            }
        }

        $total_info = array();
        foreach ($bottles as $bt){
            foreach ($bt as $type){
                $total_info[$type['month']][$type['type']]['init'] = $type['init'];
                $total_info[$type['month']][$type['type']]['milkman'] = $type['milkman'];
                $total_info[$type['month']][$type['type']]['from_factory'] = $type['from_factory'];
                $total_info[$type['month']][$type['type']]['received'] = $type['received'];
                $total_info[$type['month']][$type['type']]['damaged'] = $type['damaged'];
            }
        }
        foreach ($boxes as $bx){
            foreach ($bx as $type){
                $total_info[$type['month']][$type['type']]['init'] = $type['init'];
                $total_info[$type['month']][$type['type']]['milkman'] = $type['milkman'];
                $total_info[$type['month']][$type['type']]['from_factory'] = $type['from_factory'];
                $total_info[$type['month']][$type['type']]['received'] = $type['received'];
                $total_info[$type['month']][$type['type']]['damaged'] = $type['damaged'];
            }
        }

        $month_counts = count($bottletypes)+count($boxtypes);
//        return $total_info;

        return view('naizhan.pingkuang.pingkuangtongji', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'dsrefundinfo'=>$total_info,
            'rows'=>$month_counts,
        ]);
    }
    //
}
