<?php

namespace App\Http\Controllers;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\DSProductionPlan;
use App\Model\FactoryModel\Factory;
use App\Model\NotificationModel\DSNotification;
use App\Model\NotificationModel\FactoryNotification;
use App\Model\NotificationModel\NotificationCategory;
use App\Model\UserModel\Page;
use Auth;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class NotificationsAdmin extends Controller
{
    public function showGongchangZhongxin(Request $request){
        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;
        $child = 'zhongxin';
        $parent = 'xinxi';
        $current_page = 'zhongxin';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        $categories = NotificationCategory::where('type',NotificationCategory::TYPE_FACTORY)->get();
        $mfnotification = FactoryNotification::where('factory_id',$current_factory_id)->orderby('created_at')->get();
        foreach ($mfnotification as $dn){
            $dn['category_name'] = NotificationCategory::find($dn->category)->category_name;
        }
        return view('gongchang.xinxi.zhongxin', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'mfnotification' => $mfnotification,
            'categories'=>$categories,
        ]);
    }

    public function changetoActiveGongchang(Request $request){
        $id = $request->input('id');
        $notification = FactoryNotification::find($id);
        $notification->read = FactoryNotification::READ_STATUS;
        $notification->save();
        $unreadCount = Count(FactoryNotification::where('read',0)->get());
        return Response::json(['id'=>$id,'unread'=>$unreadCount]);
    }

    public function changetoInactiveGongchang(Request $request){
        $id = $request->input('id');
        $notification = FactoryNotification::find($id);
        $notification->read = FactoryNotification::UNREAD_STATUS;
        $notification->save();
        $unreadCount = Count(FactoryNotification::where('read',0)->get());
        return Response::json(['id'=>$id,'unread'=>$unreadCount]);
    }

    public function showGongchangXiangxi($id){
        $child = 'xiangxi';
        $parent = 'xinxi';
        $current_page = 'xiangxi';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        $fac_notifications = FactoryNotification::find($id);
        $fac_notifications->read = FactoryNotification::READ_STATUS;
        $fac_notifications->save();
        return view('gongchang.xinxi.zhongxin.xiangxi', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'fac_notifications' => $fac_notifications,
        ]);
    }

    public function showNaizhanZhongxin(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->id;
        $child = 'zhongxin';
        $parent = 'xiaoxi';
        $current_page = 'zhongxin';
        $pages = Page::where('backend_type', '3')->where('parent_page', '0')->get();
        $categories = NotificationCategory::where('type',NotificationCategory::TYPE_MILK_STATION)->get();
        $dsnotification = DSNotification::where('station_id',$current_station_id)->orderby('created_at','desc')->get();
        foreach ($dsnotification as $dn){
            $dn['category_name'] = NotificationCategory::find($dn->category)->category_name;
        }
        return view('naizhan.xiaoxi.zhongxin', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'dsnotification' => $dsnotification,
            'categories'=>$categories,
        ]);
    }

    public function showNaizhanXiangxi($id){
        $child = 'zhongxin';
        $parent = 'xiaoxi';
        $current_page = 'xianqing';
        $pages = Page::where('backend_type', '3')->where('parent_page', '0')->get();
        $dsnotifications = DSNotification::find($id);
        $dsnotifications->read = DSNotification::READ_STATUS;
        $dsnotifications->save();
        return view('naizhan.xiaoxi.xianqing', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'dsnotification'=>$dsnotifications,
        ]);
    }

    public function changetoActive(Request $request){
        $id = $request->input('id');
        $notification = DSNotification::find($id);
        $notification->read = DSNotification::READ_STATUS;
        $notification->save();
        $unreadCount = Count(DSNotification::where('read',0)->get());
        return Response::json(['id'=>$id,'unread'=>$unreadCount]);
    }

    public function changetoInactive(Request $request){
        $id = $request->input('id');
        $notification = DSNotification::find($id);
        $notification->read = DSNotification::UNREAD_STATUS;
        $notification->save();
        $unreadCount = Count(DSNotification::where('read',0)->get());
        return Response::json(['id'=>$id,'unread'=>$unreadCount]);
    }

    public function sendHourlyRequstforPlan(){
        $produce_Date = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $produce_Date->add(\DateInterval::createFromDateString('tomorrow'));
        $producre_start_date = $produce_Date->format('Y-m-d');
        $factories = Factory::where('is_deleted',0)->get();
        foreach ($factories as $fa){
            $deliveryStations  = DeliveryStation::where('factory_id',$fa->id)->where('is_deleted',0)->where('status',1)->get();
            foreach ($deliveryStations as $ds){
                if(count(DSProductionPlan::where('station_id',$ds->id)->where('produce_start_at',$producre_start_date)->get()) == 0){
                    $notification = new DSNotification();
                    $notification->sendToStationNotification($ds->id,7,"发送生产计划","你没有发送产品计划。 请尽快发送今天的计划");
                }
            }
        }
    }
    //
}
