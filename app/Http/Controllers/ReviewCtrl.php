<?php

namespace App\Http\Controllers;

use App\Model\BasicModel\Customer;
use App\Model\ReviewModel\Review;
use App\Model\UserModel\Page;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

use App\Http\Requests;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewCtrl extends Controller
{
    public function showPingjiaPage(Request $request){
        $status = $request->input('status');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $child = 'pingjialiebiao';
        $parent = 'pingjia';
        $current_page = 'pingjialiebiao';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;
        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $endDate_str = $currentDate->format('Y-m-d');
        $endTime_str = $currentDate->format('Y-m-d H:i:s');
        $startDate_str = $currentDate->format('Y-m-01');
        $startTime_str = $currentDate->format('Y-m-01 00:00:00');
        if($start_date == ''){
            $start_date = $startDate_str;
            $start_time = $startTime_str;
        }else{
            $start_time = $start_date." 00:00:00";
        }
        if($end_date == ''){
            $end_date = $endDate_str;
            $end_time = $endTime_str;
        }else{
            $end_time = $end_date." 23:59:59";
        }
        $current_reviews = array();
        if($status == ''){
            $reviews = DB::select(DB::raw(
                "select rv.* from reviews rv, customer cur 
                where rv.customer_id = cur.id 
                and cur.factory_id = :factory_id 
                and rv.created_at BETWEEN :start_at and :end_at 
                order by rv.created_at DESC"),
                array('factory_id'=>$current_factory_id,
                    'start_at'=>$start_time,
                    'end_at'=>$end_time));
        }
        else{
            $reviews = DB::select(DB::raw("select rv.* from reviews rv, customer cur where rv.status = :status and rv.customer_id = cur.id and cur.factory_id = :factory_id and rv.created_at BETWEEN :start_at and :end_at order by rv.created_at DESC"),
                array('status'=>$status,'factory_id'=>$current_factory_id,'start_at'=>$start_time,'end_at'=>$end_time));
        }
        foreach ($reviews as $re){
            $current_reviews[$re->order_id]['review_id'] = $re->id;
            $current_reviews[$re->order_id]['customer_name'] = Customer::find($re->customer_id)->name;
            $current_reviews[$re->order_id]['marks'] = $re->mark;
            $current_reviews[$re->order_id]['substr'] = mb_substr($re->content,0,10)."...";
            $current_reviews[$re->order_id]['time'] = $re->created_at;
            $current_reviews[$re->order_id]['status_number'] = $re->status;
            if($re->status == Review::REVIEW_STATUS_WAITTING){
                $current_reviews[$re->order_id]['status'] = '待审核';
            }
            elseif ($re->status == Review::REVIEW_STATUS_ISOLATION){
                $current_reviews[$re->order_id]['status'] = '屏蔽';
            }
            elseif($re->status == Review::REVIEW_STATUS_PASSED){
                $current_reviews[$re->order_id]['status'] = '通过';
            }
        }
        return view('gongchang.pingjia.pingjialiebiao', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'reviews'=>$current_reviews,
            'status'=>$status,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
        ]);
    }

    public function deleteUserPingjia($review_id){
        $deletePingjia = Review::destroy($review_id);
        return Response::json($deletePingjia);
    }

    public function passUserPingjia(Request $request){
        $pingjia = Review::find($request->input('id'));
        $pingjia->status = Review::REVIEW_STATUS_PASSED;
        $pingjia->save();
        return Response::json($pingjia);
    }

    public function isolateUserPingjia(Request $request){
        $pingjia = Review::find($request->input('id'));
        $pingjia->status = Review::REVIEW_STATUS_ISOLATION;
        $pingjia->save();
        return Response::json($pingjia);
    }

    public function getCurrentInfo($review_id){
        $pingjia_info = Review::find($review_id);
        return Response::json($pingjia_info);
    }

    public function modifyUserPingjia(Request $request){
        $pingjia = Review::find($request->input('id'));
        $pingjia->mark = $request->input('mark');
        $pingjia->content = $request->input('content');
        $pingjia->save();
        return Response::json($pingjia);
    }

    public function showPingjialiebiaoPage($review_id){
        $child = 'pingjialiebiao';
        $parent = 'pingjia';
        $current_page = 'pingjiaxiangqing';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        $review = Review::find($review_id);
        return view('gongchang.pingjia.pingjialiebiao.pingjiaxiangqing', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'review'=>$review,
        ]);
    }
    //
}
