<?php

namespace App\Http\Controllers;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\DSProductionPlan;
use App\Model\FactoryModel\Factory;
use App\Model\NotificationModel\BaseNotification;
use App\Model\NotificationModel\DSNotification;
use App\Model\NotificationModel\FactoryNotification;
use App\Model\NotificationModel\NotificationCategory;
use App\Model\UserModel\Page;
use App\Model\WechatModel\WechatReview;
use Auth;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class NotificationsAdmin extends Controller
{
    /**
     * 打开奶厂消息中心
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showGongchangZhongxin(Request $request){
        $current_factory_id = $this->getCurrentFactoryId(true);

        $child = 'zhongxin';
        $parent = 'xinxi';
        $current_page = 'zhongxin';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        $categories = FactoryNotification::getCategory();
        $mfnotification = FactoryNotification::where('factory_id',$current_factory_id)->orderby('created_at', 'desc')->get();

        foreach ($mfnotification as $dn){
            $dn['category_name'] = FactoryNotification::getCategoryName($dn->category);
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
        $notification->setRead(true);

        return Response::json(['id'=>$id,'unread'=>$this->getUnreadCountFactory()]);
    }

    public function changetoInactiveGongchang(Request $request){
        $id = $request->input('id');
        $notification = FactoryNotification::find($id);
        $notification->setRead(false);

        return Response::json(['id'=>$id,'unread'=>$this->getUnreadCountFactory()]);
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

    /**
     * 打开奶站消息中心
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showNaizhanZhongxin(Request $request){

        $current_station_id = $this->getCurrentStationId();

        $child = 'zhongxin';
        $parent = 'xiaoxi';
        $current_page = 'zhongxin';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        $categories = DSNotification::getCategory();
        $dsnotification = DSNotification::where('station_id',$current_station_id)->orderby('created_at','desc')->get();

        foreach ($dsnotification as $dn){
            $dn['category_name'] = DSNotification::getCategoryName($dn->category);
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
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        $dsnotifications = DSNotification::find($id);
        $dsnotifications->read = DSNotification::READ_STATUS;
        $dsnotifications->save();
        return view('naizhan.xiaoxi.zhongxin.xianqing', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'dsnotification'=>$dsnotifications,
        ]);
    }

    /**
     * 获取本奶厂的未读消息数量
     * @return int
     */
    public function getUnreadCountFactory() {
        $current_factory_id = $this->getCurrentFactoryId(true);

        $unreadCount = FactoryNotification::where('read', BaseNotification::UNREAD_STATUS)
            ->where('factory_id', $current_factory_id)
            ->count();

        return $unreadCount;
    }

    /**
     * 获取本奶站的未读消息数量
     * @return int
     */
    public function getUnreadCountStation() {
        $current_station_id = $this->getCurrentStationId();

        $unreadCount = DSNotification::where('read', BaseNotification::UNREAD_STATUS)
            ->where('station_id', $current_station_id)
            ->count();

        return $unreadCount;
    }

    public function changetoActive(Request $request){
        $id = $request->input('id');
        $notification = DSNotification::find($id);
        $notification->setRead(true);

        return Response::json(['id'=>$id,'unread'=>$this->getUnreadCountStation()]);
    }

    public function changetoInactive(Request $request){
        $id = $request->input('id');
        $notification = DSNotification::find($id);
        $notification->setRead(false);

        return Response::json(['id'=>$id,'unread'=>$this->getUnreadCountStation()]);
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
                    // 添加奶站通知
                    $notification = new DSNotification();
                    $notification->sendToStationNotification($ds->id,7,"提交生产计划","您今天还没有提交生产计划！");
                }
            }
        }
    }

    /**
     * 创建新的奶站通知
     */
    public function sendToStationNotification($station_id,$category,$title,$content) {
        if (!$station_id) {
            return;
        }

        if (!$category) {
            return;
        }

        $new_alert = new DSNotification();
        $new_alert->station_id = $station_id;
        $new_alert->category = $category;
        $new_alert->title = $title;
        $new_alert->content = $content;
        $new_alert->save();
    }

    /**
     * 创建新的奶厂通知
     */
    public function sendToFactoryNotification($factory_id, $category, $title, $content) {
        if (!$factory_id) {
            return;
        }

        if (!$category) {
            return;
        }

        $new_alert = new FactoryNotification();
        $new_alert->factory_id = $factory_id;
        $new_alert->category = $category;
        $new_alert->title = $title;
        $new_alert->content = $content;
        $new_alert->save();
    }

    /**
     * 创建新的微信通知通知
     */
    public function sendToWechatNotification($customer_id, $content) {
        if (!$customer_id) {
            return;
        }

        $new_alert = new WechatReview;
        $new_alert->customer_id = $customer_id;
        $new_alert->content = $content;
        $new_alert->save();
    }

}
