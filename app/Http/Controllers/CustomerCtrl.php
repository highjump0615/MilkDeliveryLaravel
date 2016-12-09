<?php

namespace App\Http\Controllers;

use App\Model\BasicModel\Customer;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\MilkMan;
use App\Model\OrderModel\Order;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\UserModel\Page;
use App\Model\FactoryModel\Factory;
use App\Http\Controllers\Controller;

use App\Model\SystemModel\SysLog;
use App\Model\UserModel\User;

use Auth;
use DateTime;

class CustomerCtrl extends Controller
{
    public function isDeliveryStatus($customer_id){
        $order_check = Order::where('is_deleted','<>',1)->where('customer_id',$customer_id)->get();
        foreach ($order_check as $oc){
            if($oc->status == 3){
                return 1;
            }
        }
        return 0;
    }

    /**
     * 打开客户管理
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showZongpingtaiUserPage(Request $request){

        $child = 'kehuliebiao';
        $parent = 'kehu';
        $current_page = 'kehuliebiao';
        $pages = Page::where('backend_type','1')->where('parent_page', '0')->get();
        $factory = Factory::where('is_deleted','<>','1')->get();

        if($factory->first() != null){
            foreach ($factory as $fa){
                $fa['customer'] = 0;
                $fa['delivering_customer'] = 0;
                $delivery_stations = DeliveryStation::where('is_deleted','<>','1')->where('factory_id',$fa->id)->get();
                foreach ($delivery_stations as $ds){
                    $naizhan_customers = Customer::where('station_id',$ds->id)->where('is_deleted','<>','1')->get();
                    foreach ($naizhan_customers as $nc){
                        $fa['delivering_customer'] += $this->isDeliveryStatus($nc->id);
                    }
                    $fa['customer'] +=count($naizhan_customers);
                }
                $fa['naizhan_count'] = count($delivery_stations);
            }
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_ADMIN, '客户列表', SysLog::SYSLOG_OPERATION_VIEW);

        return view('zongpingtai.kehu.kehuliebiao',[
            'pages'=>$pages,
            'factory'=>$factory,
            'child'=>$child,
            'parent'=>$parent,
            'current_page'=>$current_page
        ]);
    }

    public function getOrderStatus($customer_id){
        $customer_orders = Order::where('customer_id',$customer_id)->get();
        $state = '';
        if(count($customer_orders)>1){
            $state = "多态";
        }
        elseif (count($customer_orders)==1){
            if ($customer_orders->first()->status == Order::ORDER_WAITING_STATUS ||
                $customer_orders->first()->status == Order::ORDER_NEW_WAITING_STATUS) {
                $state = "待审核";
            }
            elseif ($customer_orders->first()->status == Order::ORDER_PASSED_STATUS){
                $state = "未起奶";
            }
            elseif ($customer_orders->first()->status == Order::ORDER_ON_DELIVERY_STATUS){
                $state = "在配送";
            }
            elseif ($customer_orders->first()->status == Order::ORDER_STOPPED_STATUS){
                $state = "暂停";
            }
            elseif ($customer_orders->first()->status == Order::ORDER_NOT_PASSED_STATUS ||
                    $customer_orders->first()->status == Order::ORDER_NEW_NOT_PASSED_STATUS) {
                $state = "未通过";
            }
            elseif ($customer_orders->first()->status == Order::ORDER_CANCELLED_STATUS){
                $state = "退订";
            }
            elseif ($customer_orders->first()->status == Order::ORDER_FINISHED_STATUS){
                $state = "已完成";
            }
        }
        return $state;
    }

    public function showNaizhanUserPage(Request $request){
        $current_station_id = Auth::guard('naizhan')->user()->station_id;
        $child = 'kehudangan';
        $parent = 'kehu';
        $current_page = 'kehudangan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        $customers = Customer::where('is_deleted','<>',1)->where('station_id',$current_station_id)->get();
        foreach ($customers as $cu){
            $addr = explode(" ",$cu->address);
            $cu['area_addr'] = $addr[2];
            $cu['sector_addr'] = $addr[3];
            $cu['detail_addr'] = $addr[4].$addr[5];
            $cu['station_name'] = DeliveryStation::find($cu->station_id)->name;
            $cu['milkman_name'] = MilkMan::find($cu->milkman_id)->name.' '.MilkMan::find($cu->milkman_id)->phone;
            $customer_orders = Order::where('customer_id',$cu->id)->get();
            $cu['order_count'] = count($customer_orders);
            $cu['order_balance'] = 0;
            foreach ($customer_orders as $co){
                $cu['order_balance'] += $co->total_amount;
            }
            $cu['order_status'] = $this->getOrderStatus($cu->id);
        }

        $this->addSystemLog(User::USER_BACKEND_STATION, '客户列表', SysLog::SYSLOG_OPERATION_VIEW);

        return view('naizhan.kehu.kehudangan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'customers'=>$customers,
        ]);
    }

    public function showGongchangUserPage(Request $request){
        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;
        $child = 'kehu_child';
        $parent = 'kehu';
        $current_page = 'kehu';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        $customers = Customer::where('is_deleted','<>',1)->where('factory_id',$current_factory_id)->get();
        foreach ($customers as $cu){
            $addr = explode(" ",$cu->address);
            $cu['area_addr'] = $addr[0].$addr[1];
            $cu['sector_addr'] = $addr[2].$addr[3];
            $cu['detail_addr'] = $addr[4].$addr[5];
            $cu['station_name'] = DeliveryStation::find($cu->station_id)->name;
            $cu['milkman_name'] = MilkMan::find($cu->milkman_id)->name.' '.MilkMan::find($cu->milkman_id)->phone;
            $customer_orders = Order::where('customer_id',$cu->id)->get();
            $cu['order_count'] = count($customer_orders);
            $cu['order_balance'] = 0;
            foreach ($customer_orders as $co){
                $cu['order_balance'] += $co->total_amount;
            }
            $cu['order_status'] = $this->getOrderStatus($cu->id);
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '客户列表', SysLog::SYSLOG_OPERATION_VIEW);

        return view('gongchang.kehu.kehu', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'customers'=>$customers,
        ]);
    }
    //
}
