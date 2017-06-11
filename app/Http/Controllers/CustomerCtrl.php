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
     * 打开总平台客户管理
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

    /**
     * 打开奶站客户信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showNaizhanUserPage(Request $request){
        $current_station_id = $this->getCurrentStationId();

        $child = 'kehudangan';
        $parent = 'kehu';
        $current_page = 'kehudangan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        $queryCustomer = Customer::with('station', 'milkman')
            ->where('is_deleted','<>',1)
            ->where('station_id',$current_station_id);

        $aryBaseData = $this->getCustomerList($queryCustomer, $request);

        $this->addSystemLog(User::USER_BACKEND_STATION, '客户列表', SysLog::SYSLOG_OPERATION_VIEW);

        return view('naizhan.kehu.kehudangan', array_merge($aryBaseData, [
            // 页面信息
            'pages'         => $pages,
            'child'         => $child,
            'parent'        => $parent,
            'current_page'  => $current_page,
        ]));
    }

    /**
     * 获取客户列表
     * @param $queryCustomer
     * @param Request $request
     * @return array
     */
    private function getCustomerList($queryCustomer, Request $request) {
        $retData = array();

        // 收件人
        $name = $request->input('name');
        if (!empty($name)) {
            // 筛选
            $queryCustomer->where('name', 'like', '%' . $name . '%');

            // 添加筛选参数
            $retData['name'] = $name;
        }

        // 手机号
        $phone = $request->input('phone');
        if (!empty($phone)) {
            // 筛选
            $queryCustomer->where('phone', 'like', '%' . $phone . '%');

            // 添加筛选参数
            $retData['phone'] = $phone;
        }

        // 区域
        $area = $request->input('area');
        if (!empty($phone)) {
            // 筛选
            $queryCustomer->where('address', 'like', '%' . $area . '%');

            // 添加筛选参数
            $retData['area'] = $area;
        }

        $retData['customers'] = $queryCustomer->paginate();

        foreach ($retData['customers'] as $cu){
            $queryOrder = Order::where('customer_id',$cu->id);

            $addr = explode(" ",$cu->address);
            $cu['area_addr'] = $addr[0].$addr[1];
            $cu['sector_addr'] = $addr[2].$addr[3];
            $cu['detail_addr'] = $addr[4].$addr[5];
            $cu['station_name'] = $cu->station->name;
            $cu['milkman_name'] = $cu->milkman->name;
            $cu['order_count'] = $queryOrder->count();
            $cu['order_balance'] = $queryOrder->sum('total_amount');
            $cu['order_status'] = $this->getOrderStatus($cu->id);
        }

        return $retData;
    }

    /**
     * 打开奶厂客户信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showGongchangUserPage(Request $request){
        $current_factory_id = $this->getCurrentFactoryId(true);

        $queryCustomer = Customer::with('station', 'milkman')
            ->where('is_deleted','<>',1)
            ->where('factory_id', $current_factory_id);

        $aryBaseData = $this->getCustomerList($queryCustomer, $request);

        $child = 'kehu_child';
        $parent = 'kehu';
        $current_page = 'kehu';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '客户列表', SysLog::SYSLOG_OPERATION_VIEW);

        return view('gongchang.kehu.kehu', array_merge($aryBaseData, [
            // 页面信息
            'pages'         => $pages,
            'child'         => $child,
            'parent'        => $parent,
            'current_page'  => $current_page,
        ]));
    }
    //
}
