<?php

namespace App\Http\Controllers;

use App\Model\BasicModel\PaymentType;
use App\Model\DeliveryModel\DSDeliveryPlan;
use App\Model\DeliveryModel\DSProductionPlan;
use App\Model\DeliveryModel\MilkManDeliveryPlan;
use App\Model\FactoryModel\MilkCard;
use App\Model\FinanceModel\DSCalcBalanceHistory;
use App\Model\FinanceModel\DSDeliveryCreditBalanceHistory;
use App\Model\WechatModel\WechatOrderProduct;
use Faker\Provider\at_AT\Payment;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\UserModel\Page;
use App\Model\UserModel\User;
use App\Model\SystemModel\SysLog;


use App\Model\OrderModel\Order;
use App\Model\OrderModel\OrderProduct;
use App\Model\OrderModel\OrderChanges;
use App\Model\OrderModel\OrderCheckers;
use App\Model\OrderModel\OrderTransaction;
use App\Model\OrderModel\OrderType;
use App\Model\OrderModel\OrderProperty;


use App\Model\BasicModel\ProvinceData;
use App\Model\BasicModel\CityData;
use App\Model\BasicModel\DistrictData;
use App\Model\BasicModel\Customer;
use App\Model\NotificationModel\DSNotification;
use File;
use Auth;
use DateTime;
use DateTimeZone;
use Excel;

use App\Model\DeliveryModel\DSDeliveryArea;
use App\Model\DeliveryModel\MilkManDeliveryArea;
use App\Model\DeliveryModel\MilkMan;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\DeliveryType;

use App\Model\FactoryModel\Factory;
use App\Model\FactoryModel\FactoryOrderType;

use App\Model\ProductModel\Product;
use App\Model\ProductModel\ProductPrice;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\RequestStack;

use App\Model\BasicModel\Address;

class OrderCtrl extends Controller
{
    const NOT_EXIST_DELIVERY_AREA = 1;
    const NOT_EXIST_STATION = 2;
    const NOT_EXIST_MILKMAN = 3;
    const NOT_EXIST_PRICE = 4;

    const ERR_SUCESS = 0;
    const ERR_CREDIT_BALANCE_NOT_ENOUGH = 1;

    //get only when wechat  product's delivery type ==3 or 4.
    function get_number_of_days_for_wechat_product($wop_id)
    {
        $wop = WechatOrderProduct::find($wop_id);

        $total_count = $wop->total_count;

        $deliver_at = $wop->start_at;

        $total_order_day_count = 0;

        // 按周送
        if($wop->delivery_type == DeliveryType::DELIVERY_TYPE_WEEK){
            //get order day counts of week

            //week day
            $cod = $wop->custom_order_dates;

            $cod = explode(',', $cod);
            $custom = [];
            foreach ($cod as $code) {
                $code = explode(':', $code);
                $key = $code[0];
                $value = $code[1];
                $custom[$key] = $value;
            }

            //custom week days
            do {
                //get key from day
                $key = date('N', strtotime($deliver_at));

                if (array_key_exists($key, $custom)) {
                    $plan_count = $custom[$key];
                    //changed plan count = plan acount

                    $next_key = $this->get_next_key($custom, $key);

                    if ($next_key < $key) {
                        $interval = $next_key + 7 - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $total_count -= $plan_count;

                    $total_order_day_count++;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                }
                else {
                    //get avaiable key value > current_key
                    $old_key = $key;

                    $key = $this->getClosestKey($key, $custom);
                    if ($key < $old_key)
                        $first_interval = $key + 7 - $old_key;
                    else
                        $first_interval = $key - $old_key;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $first_interval);

                    $plan_count = $custom[$key];
                    //changed plan count = plan acount

                    $next_key = $this->get_next_key($custom, $key);
                    if ($next_key < $key) {
                        $interval = $next_key + 7 - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $total_count -= $plan_count;
                    $total_order_day_count++;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                }
            }while($total_count > 0);

        }
        // 随心送
        else {
            //get order day counts of month
            $cod = $wop->custom_order_dates;

            $cod = explode(',', $cod);

            $total_order_day_count = count($cod);
        }

        return $total_order_day_count;
    }

    function getClosestKey($ckey, $array)
    {
        $closest = null;
        foreach ($array as $key => $value) {
            if ($key >= $ckey) {
                $closest = $key;
                break;
            }
        }
        if (!$closest) {
            foreach ($array as $key => $value) {
                if ($key <= $ckey) {
                    $closest = $key;
                    break;
                }
            }
        }
        if (!$closest)
            $closest = current(array_keys($array));

        return $closest;
    }

    //This module can be used to change the plan for one day, cancel the production plan for one day
    public function change_delivery_plan($order_id, $plan_id, $diff)
    {
        if ($diff == 0) {
            return ['status' => 'fail'];
        }

        $plan = MilkManDeliveryPlan::find($plan_id);

        $origin = $plan->changed_plan_count;
        $changed = $origin + $diff;

        /*
         * first change the count
         *
         * Here, get all after count including current plan and if the count > changed then set, if not fail
         * */
        $rest_with_this = $this->get_rest_plans_count($order_id, $plan_id);

        if ($changed <= $rest_with_this) {

            // 更新数量
            $plan->setCount($changed);

            // 处理多余量
            $plan->order_product->processExtraCount($plan, -$diff);

            return ['status' => 'success', 'message' => '交付变更成功.'];
        }

        //impossible to change the plan
        return ['status' => 'fail', 'message' => '你不能改变这样的计划. 该计划的改变计数是在可能的数.'];
    }

    //change delivery plan in xiugai page
    public
    function change_delivery_plan_for_one_day_in_xiangqing_and_xiugai(Request $request)
    {
        if ($request->ajax()) {

            $order_id = $request->input('order_id');
            $plan_id = $request->input('plan_id');
            $origin = $request->input('origin');
            $changed = $request->input('changed');

            $order = Order::find($order_id);
            if (!$order) {
                return response()->json(['status' => 'fail', 'message' => '找不到订单.']);
            }
            $diff = $changed - $origin;

            $result = $this->change_delivery_plan($order_id, $plan_id, $diff);

            $station_id = Order::find($order_id)->station_id;
            $customer_name = Customer::find(Order::find($order_id)->customer_id)->name;

            $notification = new NotificationsAdmin();
            $notification->sendToStationNotification($station_id,
                DSNotification::CATEGORY_CHANGE_ORDER,
                "修改了单日",
                $customer_name . "用户修改了单日的订单数量。");

            return response()->json(['status' => $result['status'], 'message' => $result['message']]);
        }
    }

    //Postpone order for one day
    public function postpone_order(Request $request)
    {
        if ($request->ajax()) {

            $order_id = $request->input('order_id');
            $order = Order::find($order_id);
            if (!$order) {
                return response()->json(['status' => 'fail', 'message' => '找不到订单.']);
            }

            // 获取今日或下个配送日期
            $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
            $today = $today_date->format('Y-m-d');

            $plans = MilkManDeliveryPlan::where('order_id', $order_id)->where('deliver_at', $today)->first();

            foreach ($plans as $plan) {
                // 已生成配送列表
                if (DSDeliveryPlan::getDeliveryPlanGenerated($order->delivery_station_id, $plan->order_product_id)) {
                    $plans = null;
                    break;
                }
            }

            // 没有今日的配送任务或今日配送列表已生成，于是暂停下一个配送任务
            if (!$plans) {
                $plans = MilkManDeliveryPlan::where('order_id', $order_id)->where('deliver_at', '>', $today)->first();
            }

            foreach ($plans as $plan) {
                $plan_id = $plan->id;
                $origin = $plan->changed_plan_count;
                $changed = 0;
                $diff = $changed - $origin;

                $this->change_delivery_plan($order_id, $plan_id, $diff);

                $plan->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL;
                $plan->cancel_reason = MilkManDeliveryPlan::DP_CANCEL_POSTPONE;
                $plan->save();
            }

            return response()->json(['status' => 'success']);
        }
    }

    /**
     * 开启暂停订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public
    function restart_dingdan(Request $request)
    {
        if ($request->ajax()) {

            $order_id = $request->input('order_id');
            $start_at = $request->input('start_at');

            //set order status as passed
            $order = Order::find($order_id);
            if (!$order)
                return response()->json(['status' => 'fail', 'message' => '找不到订单']);

            // 开启订单，相当于从今天到开启日期
            $result = $this->pauseOrder(getCurDateString(), $start_at, $order, true);

            return $result;
        }
    }



    //stop order for some period on dingdanxiugai
    /*
     * Module: Stop Order for some period
     *
     * This module can be used to stop order and change delivery formula
     * 1. delete all delivery plans of passed and waiting
     * 2. for the delivery plans that has submitted to production plan
     * change the changed_plan_count = 0;
     * 3. insert new record after the stop_end_date
     *
    */
    public
    function stop_order_for_some_period(Request $request)
    {
        if ($request->ajax()) {
            $start_date = $request->input('start');
            $end_date = $request->input('end');
            $order_id = $request->input('order_id');
            $order = Order::find($order_id);

            $result = $this->pauseOrder($start_date, $end_date, $order, false);

            return $result;
        }
    }

    /**
     * 暂停订单
     * @param $start_date
     * @param $end_date
     * @param $order_id
     * @param $forRestart bool 是否包括结束那天
     * @return \Illuminate\Http\JsonResponse
     */
    private function pauseOrder($start_date, $end_date, $order, $forRestart) {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);

        if ($start > $end) {
            return response()->json(['status' => 'fail']);
        }

        $order_products = $order->order_products;

        // 暂停时间设置
        if ($forRestart) {
            $order->restart_at = $end_date;
        }
        else {
            $order->stop_at = $start_date;
            $order->restart_at = getNextDateString($end_date);
        }
        $order->save();

        foreach ($order_products as $op) {
            // 获取该奶品的最后配送明细
            $dpLast = $op->getLastDeliveryPlan();
            if (empty($dpLast)) {
                return response()->json(['status' => 'fail']);
            }

            $dateLast = new DateTime($dpLast->deliver_at);

            // 如果暂停开始日期超出最后日期，忽略掉
            if ($start > $dateLast) {
                continue;
            }

            //
            // 获取数量
            //

            // 当天开启的就直接
            $qb = MilkManDeliveryPlan::where('order_product_id', $op->id)
                ->where('deliver_at', '>=', $start);

            // 计算多余量
            $nCountExtra = $qb->sum('changed_plan_count');

            // 删除暂停范围的配送明细
            $qb->delete();

            // 调整配送明细
            $op->processExtraCount(null, $nCountExtra);
        }

        return response()->json(['status' => 'success', 'order_status' => $order->status, 'stop_start' => $start_date, 'stop_end' => $end_date]);
    }

    //Show order revise page in naizhan
    public function show_order_revise_in_naizhan($order_id)
    {
        $this->initShowStationPage();

        $order_checkers = $this->station->active_order_checkers;

        $order = Order::find($order_id);

        // 解析收货地址
        $order->resolveAddress();

        $customer = $order->customer;
        $milkman = $order->milkman;

        $order_products = $order->order_products;

        $child = 'quanbuluru';
        $parent = 'dingdan';
        $current_page = 'xiugai';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.dingdanluru.xiugai', [
            // 菜单关联信息
            'pages'                     => $pages,
            'child'                     => $child,
            'parent'                    => $parent,
            'current_page'              => $current_page,

            // 是否修改订单
            'is_edit'                   => true,

            // 录入订单基础信息
            'order_property'            => $this->order_property,
            'province'                  => $this->province,
            'order_checkers'            => $order_checkers,
            'products'                  => $this->products,
            'factory_order_types'       => $this->factory_order_types,
            'order_delivery_types'      => $this->order_delivery_types,
            'products_count_on_fot'     => $this->product_count_on_fot,
            'delivery_stations'         => $this->delivery_stations,
            'gap_day'                   => $this->factory->gap_day,
            'remain_amount'             => 0,

            // 初始化录入页面
            'order'                     => $order,
            'order_products'            => $order_products,
            'customer'                  => $customer,
            'milkman'                   => $milkman,

            // 奶站信息
            'station'                   => $this->station
        ]);
    }

    private $order_property;
    private $province;
    private $products;
    private $factory_order_types;
    private $order_delivery_types;
    private $product_count_on_fot;
    private $delivery_stations;

    private $factory;
    private $station;

    /**
     * @param mixed $factory
     */
    public function setFactory($factoryId)
    {
        $this->factory = Factory::find($factoryId);
    }

    /**
     * 初始化奶厂订单参数
     */
    private function initShowFactoryPage() {
        $this->setFactory($this->getCurrentFactoryId(true));

        $this->initBaseFromOrderInput();
    }

    /**
     * 初始化奶站订单参数
     */
    private function initShowStationPage() {

        $this->station = DeliveryStation::find($this->getCurrentStationId());
        $this->setFactory($this->station->factory_id);

        $this->initBaseFromOrderInput();
    }

    /**
     * 初始化订单录入的基础信息
     */
    public function initBaseFromOrderInput() {
        $this->order_property = OrderProperty::all();

        $this->products = $this->factory->active_products;
        $this->factory_order_types = $this->factory->factory_order_types;
        $this->order_delivery_types = $this->factory->order_delivery_types;
        $this->delivery_stations = $this->factory->active_stations;//get only active stations

        $this->province = Address::getProvinces($this->factory->id);

        $this->product_count_on_fot = [];
        foreach ($this->factory_order_types as $fot) {
            $pcof = ["fot" => ($fot->order_type), "pcfot" => ($fot->order_count)];
            array_push($this->product_count_on_fot, $pcof);
        }
    }

    /**
     * 查询初始化订单录入页面需要的征订员信息
     * @param $order
     * @return 征订员列表
     */
    private function initShowOrderChecker($order) {
        // 征订员信息，奶站或奶厂的征订员
        $order_checkers = null;
        if ($order->checker->station) {
            $order_checkers = $order->checker->station->all_order_checkers;
        }
        else {
            $order_checkers = $this->factory->ordercheckers;
        }

        return $order_checkers;
    }

    //show xiugai order page in gongchang
    public
    function show_order_revise_in_gongchang($order_id)
    {
        $this->initShowFactoryPage();

        $order = Order::find($order_id);

        $order_checkers = $this->initShowOrderChecker($order);

        $customer = $order->customer;
        $milkman = $order->milkman;

        $order_products = $order->order_products;

        // 解析收货地址
        $order->resolveAddress();

        $child = '';
        $parent = 'dingdan';
        $current_page = 'dingdanxiugai';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.dingdanluru.dingdanxiugai', [
            // 菜单关联信息
            'pages'                     => $pages,
            'child'                     => $child,
            'parent'                    => $parent,
            'current_page'              => $current_page,

            // 是否修改订单
            'is_edit'                   => true,

            // 录入订单基础信息
            'order_property'            => $this->order_property,
            'province'                  => $this->province,
            'order_checkers'            => $order_checkers,
            'products'                  => $this->products,
            'factory_order_types'       => $this->factory_order_types,
            'order_delivery_types'      => $this->order_delivery_types,
            'products_count_on_fot'     => $this->product_count_on_fot,
            'delivery_stations'         => $this->delivery_stations,
            'gap_day'                   => $this->factory->gap_day,
            'remain_amount'             => 0,

            // 初始化录入页面
            'order'                     => $order,
            'order_products'            => $order_products,
            'customer'                  => $customer,
            'milkman'                   => $milkman
        ]);
    }

    /**
     * 计算本配送明细后的数量的总和
     * @param $oid
     * @param $pid
     * @return mixed
     */
    public function get_rest_plans_count($oid, $pid)
    {
        $rest_with_this = MilkManDeliveryPlan::where('order_id', $oid)
            ->where('id', '>=', $pid)
            ->wherebetween('status', [MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING, MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT])
            ->sum('changed_plan_count');

        return $rest_with_this;
    }

    //Check the date in this week
    public
    function check_in_this_week($date_string)
    {
        $curdate = date('Y-m-d', strtotime($date_string));
        $mon = date('Y-m-d', strtotime("previous monday"));
        $sun = date('Y-m-d', strtotime("sunday"));

        if ($curdate <= $sun && $curdate >= $mon)
            return true;
        else
            return false;
    }

    //Check the date in this month
    public
    function check_in_this_month($date_string)
    {
        $curdate = date('Y-m-d', strtotime($date_string));
        $first = date('Y-m-01');
        $last = date('Y-m-t');
        if ($curdate <= $last && $curdate >= $first)
            return true;
        else
            return false;
    }

    /**
     * 录入/修改核心
     * @param $factory_id
     * @param $station_id
     * @param $order_id
     * @param $customer_id
     * @param $phone
     * @param $address
     * @param $order_property_id
     * @param $milkman_id
     * @param $delivery_station_id
     * @param $order_checker_id
     * @param $receipt_number
     * @param $receipt_path
     * @param $total_amount
     * @param $delivery_time
     * @param $order_start_at
     * @param $milk_box_install
     * @param $payment_type
     * @param $order_by_milk_card
     * @param $card_id
     * @param $product_ids
     * @param $order_types
     * @param $total_counts
     * @param $total_amounts
     * @param $product_prices
     * @param $delivery_types
     * @param $avgs
     * @param $product_start_ats
     * @param $delivery_dates
     * @return int
     */
    public function insert_order_core($factory_id,
                                      $station_id,
                                      $order_id,
                                      $customer_id,
                                      $wxuser_id,
                                      $phone,
                                      $address,
                                      $order_property_id,
                                      $milkman_id,
                                      $deliveryarea_id,
                                      $delivery_station_id,
                                      $order_checker_id,
                                      $receipt_number,
                                      $receipt_path,
                                      $total_amount,
                                      $delivery_time,
                                      $order_start_at,
                                      $milk_box_install,
                                      $payment_type,
                                      $order_by_milk_card,
                                      $card_id,
                                      $comment,
                                      // OrderProducts
                                      $product_ids,
                                      $order_types,
                                      $total_counts,
                                      $total_amounts,
                                      $product_prices,
                                      $delivery_types,
                                      $avgs,
                                      $product_start_ats,
                                      $delivery_dates,
                                      $count_per_days,
                                      // 结果Order
                                      &$orderRes) {
        $order = null;

        // 获取今日日期
        $today = getCurDateString();
        $ordered_at = date_create($today);

        // 如果是奶厂订单，配送奶站就是录入奶站
        if ($station_id == 0) {
            $station_id = $delivery_station_id;
        }

        if ($order_id) {
            $order = Order::find($order_id);
        }

        if (empty($station_id) && $order) {
            $station = $order->station;
        }
        else {
            //check for 10% of delivery credit balance
            $station = DeliveryStation::find($station_id);
        }

        if ($payment_type == PaymentType::PAYMENT_TYPE_MONEY_NORMAL) {
            // 以下情况下要查询配送信用余额
            // 1. 新订单
            // 2. 新订单未通过的情况
            // 3. 新订单待审核的情况

            $remain_cost = $station->init_delivery_credit_amount + $station->delivery_credit_balance - $total_amount;
            if ($order && $order->status == Order::ORDER_NEW_WAITING_STATUS) {
                $remain_cost += $order->total_amount;
            }

            if (!$order || ($order && !$order->isNewPassed())) {
                if ($remain_cost < ($station->init_delivery_credit_amount / 10)) {
                    return OrderCtrl::ERR_CREDIT_BALANCE_NOT_ENOUGH;
                }
            }
        }

        // 状态, 默认是是待审核新订单
        $status = Order::ORDER_NEW_WAITING_STATUS;

        // 新订单未通过和新订单录入，就待审核
        if ($order && $order->isNewPassed()) {
            $status = Order::ORDER_WAITING_STATUS;
        }

        // 该订单是否生成了账单
        $trans_check = 0;

        //flatenter mode: default 2 -> call
        $flat_enter_mode_id = Order::ORDER_FLAT_ENTER_MODE_CALL_DEFAULT;//by call

        if (!$order) {
            $order = new Order;

            //
            // 这些字段一旦订好了不能改
            //
            $order->factory_id = $factory_id;
            $order->ordered_at = $ordered_at;

            $order->order_by_milk_card = $order_by_milk_card;
            $order->payment_type = $payment_type;

            $order->comment = $comment;
            $order->total_amount = $total_amount;
            $order->trans_check = $trans_check;
        }
        else if (!$order->isNewPassed() && $order->payment_type == PaymentType::PAYMENT_TYPE_MONEY_NORMAL) {
            //
            // 对于没通过的现金订单，重新设置金额
            //
            if ($order->status == Order::ORDER_NEW_WAITING_STATUS) {
                // 退回信用余额，因为下面会再扣的
                $station->delivery_credit_balance += $order->total_amount;
            }

            $order->total_amount = $total_amount;
        }

        /* Remain Amount
         * when the customer's info is inputed, check the customer's remain amount.
         * if the customer is new, then the remain amount is 0 ,else remain amount has been shown on insert order page.
         * Order's remaining amount is the other concept with customer's amount.
         * at first, remaining amount = total_amount and whenever each order delivery plan was finished,
         * remaining_amount = remaining_amount - deliver plan amount.
         * when the order was finished completely, the %remt = 0;
         * if not, %remt is added to customer's remain amount.
         * when the order was inserted in naizhan or factory, they can get the amount of money : total_order_amount-customer's remaining_amount
         */
        $order->remaining_amount = $total_amount;

        // 客户id
        if (!empty($customer_id)) {
            $order->customer_id = $customer_id;
        }
        // 微信用户
        if (!empty($wxuser_id)) {
            $order->wxuser_id = $wxuser_id;
        }
        // 电话
        if (!empty($phone)) {
            $order->phone = $phone;
        }
        // 地址
        if (!empty($address)) {
            $order->address = $address;
        }
        // 订单性质
        if (!empty($order_property_id)) {
            $order->order_property_id = $order_property_id;
        }

        if (!empty($station_id)) {
            $order->station_id = $station_id;
        }

        // 配送地区id
        if (!empty($deliveryarea_id)) {
            $order->deliveryarea_id = $deliveryarea_id;
        }

        $order->receipt_number = $receipt_number;
        $order->receipt_path = $receipt_path;

        // 征订员
        if (!empty($order_checker_id)) {
            $order->order_checker_id = $order_checker_id;
        }

        // 奶箱安装
        if ($milk_box_install >= 0) {
            $order->milk_box_install = $milk_box_install;
        }

        // 状态
        $order->status = $status;

        // 订单开启时间
        if (!empty($order_start_at)) {
            $order->start_at = $order_start_at;
        }

        // 配送时间
        if (!empty($delivery_time)) {
            $order->delivery_time = $delivery_time;
        }
        $order->flat_enter_mode_id = $flat_enter_mode_id;

        if (!empty($delivery_station_id)) {
            $order->delivery_station_id = $delivery_station_id;
        }

        $order->save();

        // 配送员
        $nMilkmanId = $milkman_id;
        if (empty($nMilkmanId)) {
            $nMilkmanId = $order->milkman_id;
        }

        // 配送奶站
        $nDeliveryStationId = $delivery_station_id;
        if (empty($nDeliveryStationId)) {
            $nDeliveryStationId = $order->delivery_station_id;
        }

        //save order products
        $count = count($product_ids);

        // 订单修改要删除以前的配送明细和奶品信息
        if ($order_id) {
            // 奶品数据有了变化，删除重新添加
            if ($count > 0) {
                $this->delete_all_order_products_and_delivery_plans_for_update_order($order);
            }
            // 没有变化，奶站重新设置
            else {
                $order->milkmanDeliveryPlan()->update([
                    'station_id' => $order->delivery_station_id,
                ]);
            }
        }
        // 新订单生成订单编号
        else {
            $order->number = $this->order_number($factory_id, $station_id, $customer_id, $order->id);
            //order's unique number: format (F_fid_S_sid_C_cid_O_orderid)
            $order->save();

            // 奶卡只在录入订单时处理
            if ($payment_type == PaymentType::PAYMENT_TYPE_CARD) {
                $aryMilkCardId = explode(',', $card_id);

                MilkCard::whereIn('id', $aryMilkCardId)
                    ->update([
                        'order_id' => $order->id,
                        'pay_status' => MilkCard::MILKCARD_PAY_STATUS_ACTIVE,
                    ]);
            }
        }

        for ($i = 0; $i < $count; $i++) {
            //
            // 创建OderProduct
            //
            $op = new OrderProduct;
            $op->order_id = $order->id;
            $op->product_id = $product_ids[$i];
            $op->order_type = $order_types[$i];
            $op->delivery_type = $delivery_types[$i];

            if (empty($product_prices)) {
                $product_price = $this->get_product_price_by_cid($op->product_id, $op->order_type, $customer_id);
            }
            else {
                $product_price = $product_prices[$i];
            }
            $op->product_price = $product_price;

            $op->total_count = $total_counts[$i];
            $op->total_amount = $total_amounts[$i];
            $op->avg = $avgs[$i];
            $op->start_at = $product_start_ats[$i];

            $op->count_per_day = $count_per_days[$i];

            if ($op->delivery_type == DeliveryType::DELIVERY_TYPE_WEEK || $op->delivery_type == DeliveryType::DELIVERY_TYPE_MONTH) {   // 按周送、随心送

                $custom_dates = $delivery_dates[$i];
                $result = rtrim($custom_dates, ',');

                $op->custom_order_dates = $result;
            }

            $op->save();

            //
            // 创建一个MilkmanDeliveryPlan, 之后自动生成
            //
            $this->addMilkmanDeliveryPlan($nMilkmanId,
                $nDeliveryStationId,
                $op->start_at,
                $op,
                MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING,
                $op->total_count);
        }

        // save customer
        if (!empty($customer_id) && !empty($delivery_station_id) && empty($nMilkmanId)) {
            $customer = Customer::find($customer_id);
            $customer->station_id = $delivery_station_id;
            $customer->milkman_id = $nMilkmanId;
            $customer->save();
        }

        //
        //Caiwu Related
        //

        //When order save, decrease the delivery credit balance and change milkcard status if this is card  order.
        if ($status = Order::ORDER_NEW_WAITING_STATUS && $payment_type == PaymentType::PAYMENT_TYPE_MONEY_NORMAL) {
            $station->delivery_credit_balance = $station->delivery_credit_balance - $total_amount;
            $station->save();
        }

        $orderRes = $order;

        return OrderCtrl::ERR_SUCESS;
    }

    /**
     * 录入/修改订单
     * @param Request $request
     * @param $backend_type int 后台用户权限
     * @return \Illuminate\Http\JsonResponse
     */
    function insert_order(Request $request, $backend_type) {

        // 默认操作是添加
        $nOperation = SysLog::SYSLOG_OPERATION_ADD;

        $order = null;
        $factory_id = $station_id = 0;

        if ($this->factory) {
            $factory_id = $this->factory->id;
        }
        if ($this->station) {
            $station_id = $this->station->id;
        }

        // 录入订单还是修改订单
        $order_id = $request->input('order_id');
        if (!empty($order_id)) {
            // 操作是修改
            $nOperation = SysLog::SYSLOG_OPERATION_EDIT;
        }

        //insert customer info
        $customer_id = $request->input('customer_id');
        if (empty($customer_id) && $order) {
            $customer_id = $order->customer_id;
        }

        $phone = $request->input('phone');
        $address = $request->input('c_province') . ' ' . $request->input('c_city') . ' ' . $request->input('c_district') . ' ' . $request->input('c_street') . ' ' . $request->input('c_xiaoqu') . ' ' . $request->input('c_sub_addr');

        //whether this is new order or old order
        $order_property_id = $request->input('order_property');

        //station info
        $milkman_id = $request->input('milkman_id');
        $deliveryarea_id = $request->input('deliveryarea_id');
        $delivery_station_id = $request->input('station');

        if (!$milkman_id) {
            return response()->json(['status' => 'fail', 'message' => '找不到合适的配送员']);
        }

        $order_checker_id = $request->input('order_checker');
        $receipt_number = $request->input('receipt_number');
        $receipt_path = $request->input('receipt_path');

        // 票据图片
        if (empty($receipt_path)) {
            if ($request->hasFile('receipt_img')) {
                $strDestProductPath = public_path() . '/img/order/';
                if (!file_exists($strDestProductPath)) {
                    File::makeDirectory($strDestProductPath, 0777, true);
                }

                $file = $request->file('receipt_img');
                if ($file->isValid()) {
                    $ext = $file->getClientOriginalExtension();
                    $receipt_path = "o" . time() . uniqid() . '.' . $ext;
                    $file->move($strDestProductPath, $receipt_path);
                }
            }
        }

        //Order amount for remaingng, acceptable
        $total_amount = $request->input('total_amount');

        $delivery_time = $request->input('delivery_noon');

        //order info
        $order_start_at = $request->input('order_start_at');
        $order_start_at = date_create($order_start_at);

        $milk_box_install = $request->input('milk_box_install') == "on" ? 1 : 0;
        $payment_type = PaymentType::PAYMENT_TYPE_MONEY_NORMAL;

        // 奶卡订单录入
        $order_by_milk_card = $request->input('milk_card_check') == "on" ? 1 : 0;
        if ($order_by_milk_card == 1) {
            $payment_type = PaymentType::PAYMENT_TYPE_CARD;
        }

        // 查看票据号，判断是否微信订单
        if (empty($receipt_number)) {
            $payment_type = PaymentType::PAYMENT_TYPE_WECHAT;
        }

        $order = null;
        $nResult = $this->insert_order_core(
            $factory_id,
            $station_id,
            $order_id,
            $customer_id,
            null,
            $phone,
            $address,
            $order_property_id,
            $milkman_id,
            $deliveryarea_id,
            $delivery_station_id,
            $order_checker_id,
            $receipt_number,
            $receipt_path,
            $total_amount,
            $delivery_time,
            $order_start_at,
            $milk_box_install,
            $payment_type,
            $order_by_milk_card,
            $request->input('card_id'),
            null,   // 备注
            $request->input('order_product_id'),
            $request->input('factory_order_type'),
            $request->input('one_product_total_count'),
            $request->input('one_p_amount'),
            null,
            $request->input('order_delivery_type'),
            $request->input('avg'),
            $request->input('start_at'),
            $request->input('delivery_dates'),
            $request->input('order_product_count_per'),
            $order);

        if ($nResult == OrderCtrl::ERR_CREDIT_BALANCE_NOT_ENOUGH) {
            return response()->json(['status' => 'fail', 'message' => '该站应保持高于其交割信用余额10％的货币.']);
        }

        // 添加系统日志
        $this->addSystemLog($backend_type, '订单', $nOperation);

        return response()->json(['status' => 'success', 'order_id' => $order->id]);
    }

    /**
     * 创建一个MilkmanDeliveryPlan, 之后自动生成
     * @param $milkmanId
     * @param $stationId
     * @param $startAt
     * @param $orderProduct
     * @param $status
     * @param $count
     */
    public function addMilkmanDeliveryPlan($milkmanId, $stationId, $startAt, $orderProduct, $status, $count) {
        $dp = new MilkManDeliveryPlan;

//        $dp->milkman_id = $milkmanId;
        $dp->station_id = $stationId;
        $dp->order_id = $orderProduct->order_id;
        $dp->order_product_id = $orderProduct->id;
        $dp->deliver_at = $orderProduct->getClosestDeliverDate($startAt);
        $dp->produce_at = $orderProduct->getProductionDate($dp->deliver_at);

        $dp->status = $status;
        $dp->determineStatus();

        $dp->setCount($orderProduct->getDeliveryTypeCount($dp->deliver_at));
        $dp->product_price = $orderProduct->product_price;
        $dp->delivered_count = 0;
        $dp->type = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER;

        $dp->save();

        // 自动生成配送明细
        $orderProduct->processExtraCount($dp, $count - $dp->changed_plan_count);

        // 说明这是第一条配送明细
        if ($count == $orderProduct->total_count) {
            // 设置配送第一天的标志
            $dp->flag = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER;
            $dp->save();
        }
    }

    //Insert Order In gongchang
    function insert_order_in_gongchang(Request $request)
    {
        if ($request->ajax()) {

            $fuser = Auth::guard('gongchang')->user();
            if ($fuser) {
                $factory_id = $fuser->factory_id;
                $this->factory = Factory::find($factory_id);

                return $this->insert_order($request, User::USER_BACKEND_FACTORY);
            }
        }
    }

    //Insert Order In naizhan
    function insert_order_in_naizhan(Request $request)
    {
        if ($request->ajax()) {

            $suser = Auth::guard('naizhan')->user();
            if ($suser) {
                $station_id = $suser->station_id;
                $this->station = DeliveryStation::find($station_id);

                $factory_id = $this->station->factory_id;
                $this->factory = Factory::find($factory_id);

                return $this->insert_order($request, User::USER_BACKEND_STATION);
            }
        }
    }

    /**
     * 获取配送明细
     * @return array
     */
    public function getOrderDeliveryPlan(Request $request, $orderId)
    {
        $result_group=[];

        // 配送明细只针对订单的配送
        $op_dps = MilkManDeliveryPlan::where('order_id', $orderId)
            ->where('type', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)
            // 不显示订单修改导致取消的明细
            ->where(function($query) {
                $query->where('status', '<>', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL);
                $query->orwhere('cancel_reason', '<>', MilkManDeliveryPlan::DP_CANCEL_CHANGEORDER);
            })
            ->orderBy('deliver_at')
            ->paginate();

        $remainCounts = [];

        foreach($op_dps as &$opdp)
        {
            // 查看剩余数量
            if (!isset($remainCounts[strval($opdp->order_product_id)])) {
                // 获取剩余数量
                $nCountDelivered = MilkManDeliveryPlan::where('order_product_id', $opdp->order_product_id)
                    ->where('deliver_at', '<', $opdp->deliver_at)
                    ->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
                    ->sum('delivered_count');

                $nCountNotDelivered = MilkManDeliveryPlan::where('order_product_id', $opdp->order_product_id)
                    ->where('deliver_at', '<', $opdp->deliver_at)
                    ->where('status', '!=', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
                    ->sum('changed_plan_count');

                $remainCounts[strval($opdp->order_product_id)] = $opdp->orderProduct->total_count - ($nCountDelivered + $nCountNotDelivered);
            }

            if ($opdp->status == MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
                $count = $opdp->delivered_count;
            else
                $count = $opdp->changed_plan_count;

            $remainCounts[strval($opdp->order_product_id)] -= $count;

            $opdp['count'] = $count;
            $opdp['remain'] = $remainCounts[strval($opdp->order_product_id)];
        }

        return $op_dps;
    }

    //show detial of every order, especially after saved order
    function show_detail_order_in_gongchang(Request $request, $order_id)
    {
        $factory_id = $this->getCurrentFactoryId(true);
        $factory = Factory::find($factory_id);

        //check this order is current factory's order
        $order = Order::find($order_id);
        $order_products = $order->order_products;

        $grouped_plans_per_product = $this->getOrderDeliveryPlan($request, $order_id);

        $child = 'dingdanluru';
        $parent = 'dingdan';
        $current_page = 'xiangqing';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.dingdanluru.xiangqing', [
            // 菜单关联信息
            'pages'                     => $pages,
            'child'                     => $child,
            'parent'                    => $parent,
            'current_page'              => $current_page,

            // 订单内容
            'order'                     => $order,
            'order_products'            => $order_products,
            'grouped_plans_per_product' => $grouped_plans_per_product,
            'gap_day'                   => $factory->gap_day
        ]);
    }

    //show detail of order in naizhan
    function show_detail_order_in_naizhan(Request $request, $order_id)
    {
        $station = DeliveryStation::find($this->getCurrentStationId());
        $factory = Factory::find($this->getCurrentFactoryId(false));

        //check this order is current factory's order
        $order = Order::find($order_id);
        $order_products = $order->order_products;

        $grouped_plans_per_product = $this->getOrderDeliveryPlan($request, $order_id);

        $child = 'dingdan';
        $parent = 'dingdan';
        $current_page = 'xiangqing';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.dingdanluru.detail', [
            // 菜单关联信息
            'pages'                     => $pages,
            'child'                     => $child,
            'parent'                    => $parent,
            'current_page'              => $current_page,

            // 订单内容
            'order'                     => $order,
            'order_products'            => $order_products,
            'grouped_plans_per_product' => $grouped_plans_per_product,
            'gap_day'                   => $factory->gap_day,

            // 奶站信息
            'station'                   => $station
        ]);
    }

//show insert dingdan page in gongchang
    public
    function show_insert_order_page_in_gongchang()
    {
        $this->initShowFactoryPage();

        $order_checkers = $this->factory->ordercheckers;

        $child = 'dingdanluru';
        $parent = 'dingdan';
        $current_page = 'dingdanluru';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.dingdanluru', [
            // 菜单关联信息
            'pages'                 => $pages,
            'child'                 => $child,
            'parent'                => $parent,
            'current_page'          => $current_page,

            // 是否修改订单
            'is_edit'               => false,

            // 录入订单基础信息
            'order_property'        => $this->order_property,
            'province'              => $this->province,
            'order_checkers'        => $order_checkers,
            'products'              => $this->products,
            'factory_order_types'   => $this->factory_order_types,
            'order_delivery_types'  => $this->order_delivery_types,
            'products_count_on_fot' => $this->product_count_on_fot,
            'delivery_stations'     => $this->delivery_stations,
            'gap_day'               => $this->factory->gap_day,
            'remain_amount'         => 0
        ]);
    }

    //show insert dingdan page in gongchang
    public
    function show_insert_order_page_in_naizhan()
    {
        $this->initShowStationPage();

        $order_checkers = $this->station->active_order_checkers;

        $child = 'dingdanluru';
        $parent = 'dingdan';
        $current_page = 'dingdanluru';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.dingdanluru', [
            // 菜单关联信息
            'pages'                 => $pages,
            'child'                 => $child,
            'parent'                => $parent,
            'current_page'          => $current_page,

            // 是否修改订单
            'is_edit'               => false,

            // 录入订单基础信息
            'order_property'        => $this->order_property,
            'province'              => $this->province,
            'order_checkers'        => $order_checkers,
            'products'              => $this->products,
            'factory_order_types'   => $this->factory_order_types,
            'order_delivery_types'  => $this->order_delivery_types,
            'products_count_on_fot' => $this->product_count_on_fot,
            'delivery_stations'     => $this->delivery_stations,
            'gap_day'               => $this->factory->gap_day,
            'remain_amount'         => 0,

            // 奶站信息
            'station'               => $this->station
        ]);
    }

    /**
     * 获取客户信息
     * @param $phone
     * @param $address
     * @param $factoryId
     * @return Customer
     */
    public function getCustomer($phone, $address, $factoryId) {
        // 查看已存在的客户信息
        $customer = Customer::where('phone', $phone)->where('factory_id', $factoryId)->first();

        if (!$customer) {
            $customer = new Customer;
        }

        $customer->address = $address;
        $customer->is_deleted = 0;
        $customer->factory_id = $factoryId;
        $customer->phone = $phone;

        return $customer;
    }

    /**
     * 添加客户信息
     * @return
     */
    private function insert_customer_for_order(Request $request) {

        $name = $request->input('customer');
        $phone = $request->input('phone');

        $province = $request->input('c_province');
        $city = $request->input('c_city');
        $district = $request->input('c_district');
        $street = $request->input('c_street');
        $xiaoqu = $request->input('c_xiaoqu');
        $sub_addr = $request->input('c_sub_addr');

        $addr = $province . ' ' . $city . ' ' . $district . ' ' . $street . ' ' . $xiaoqu . ' ' . $sub_addr;
        $d_addr = $province . ' ' . $city . ' ' . $district . ' ' . $street . ' ' . $xiaoqu;

        //select avaiable one station and milkman
        $station = null;
        $station_milkman = $this->get_station_milkman_with_address_from_factory($this->factory->id, $d_addr, $station);

        if ($station_milkman == $this::NOT_EXIST_DELIVERY_AREA) {
            return response()->json(['status' => 'fail', 'message' => '该地区没有覆盖可配送的范围']);
        }
        else if ($station_milkman == $this::NOT_EXIST_STATION) {
            return response()->json(['status' => 'fail', 'message' => '没有奶站.']);
        }
        else if ($station_milkman == $this::NOT_EXIST_MILKMAN) {
            return response()->json([
                'status'        => 'fail',
                'message'       => '奶站没有配送员.',
                'station_name'  => $station->name,
                'station_id'    => $station->id,
                'date_start'    => $station->getChangeStartDate()
            ]);
        }

        // 设置客户信息
        $customer = $this->getCustomer($phone, $addr, $this->factory->id);
        $customer->station_id = $station_milkman[0];
        $customer->milkman_id = $station_milkman[1];
        $customer->name = $name;

        $station = DeliveryStation::find($station_milkman[0]);

        // 新建的客户信息需要保存
        if (empty($customer->id)) {
            $customer->save();
        }

        return response()->json([
            'status'            => 'success',
            'customer_id'       => $customer->id,
            'station_name'      => $station->name,
            'station_id'        => $station->id,
            'milkman_id'        => $customer->milkman_id,
            'deliveryarea_id'   => $station_milkman[2],
            'remain_amount'     => $customer->remain_amount,
            'date_start'        => $station->getChangeStartDate()
        ]);
    }

    //add customer in gongchang
    public
    function insert_customer_for_order_in_gongchang(Request $request)
    {
        if ($request->ajax()) {
            $fuser = Auth::guard('gongchang')->user();

            $factory_id = $fuser->factory_id;
            $this->factory = Factory::find($factory_id);

            return $this->insert_customer_for_order($request);
        }
    }

    //add customer in naizhan
    public
    function insert_customer_for_order_in_naizhan(Request $request)
    {
        if ($request->ajax()) {

            $my_station_id = Auth::guard('naizhan')->user()->station_id;
            $my_station = DeliveryStation::find($my_station_id);
            $factory_id = $my_station->factory_id;
            $this->factory = Factory::find($factory_id);

            return $this->insert_customer_for_order($request);
        }
    }

    /**
     * 打开奶厂未通过订单列表页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_not_passed_dingdan_in_gongchang(Request $request)
    {
        $factory_id = $this->getCurrentFactoryId(true);
        $factory = Factory::find($factory_id);

        $queryOrder = Order::where('is_deleted', 0)
            ->where('factory_id', $factory_id)
            ->where(function($query) {
                $query->where('status', Order::ORDER_NOT_PASSED_STATUS);
                $query->orwhere('status', Order::ORDER_NEW_NOT_PASSED_STATUS);
            })
            ->orderBy('updated_at', 'desc');

        $aryBaseData = $this->getOrderList($queryOrder, $request);

        $child = 'weitongguodingdan';
        $parent = 'dingdan';
        $current_page = 'weitongguodingdan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.weitongguodingdan', array_merge($aryBaseData, [
            // 页面信息
            'pages'         => $pages,
            'child'         => $child,
            'parent'        => $parent,
            'current_page'  => $current_page,

            // 数据
            'factory'       => $factory,
        ]));
    }

    /**
     * 打开奶站未通过订单列表页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_not_passed_dingdan_in_naizhan(Request $request)
    {
        $factory_id = $this->getCurrentFactoryId(false);
        $station_id = $this->getCurrentStationId();
        $factory = Factory::find($factory_id);

        $queryOrder = Order::where('is_deleted', 0)
            ->where('delivery_station_id', $station_id)
            ->where(function($query) {
                $query->where('status', Order::ORDER_NOT_PASSED_STATUS);
                $query->orwhere('status', Order::ORDER_NEW_NOT_PASSED_STATUS);
            })
            ->orderBy('updated_at', 'desc');

        $aryBaseData = $this->getOrderList($queryOrder, $request);

        $child = 'weitongguo';
        $parent = 'dingdan';
        $current_page = 'weitongguon';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.weitongguo', array_merge($aryBaseData, [
            // 页面信息
            'pages'         => $pages,
            'child'         => $child,
            'parent'        => $parent,
            'current_page'  => $current_page,

            // 数据
            'factory'       => $factory,
        ]));
    }

    /**
     * 打开奶厂暂停订单列表页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_stopped_dingdan_in_gongchang(Request $request)
    {
        $factory_id = $this->getCurrentFactoryId(true);
        $factory = Factory::find($factory_id);

        $queryOrder = Order::queryStopped()
            ->where('is_deleted', 0)
            ->where('factory_id', $factory_id)
            ->orderBy('updated_at', 'desc');

        $aryBaseData = $this->getOrderList($queryOrder, $request);

        $child = 'zantingdingdan';
        $parent = 'dingdan';
        $current_page = 'zantingdingdan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.zantingdingdan', array_merge($aryBaseData, [
            // 页面信息
            'pages'         => $pages,
            'child'         => $child,
            'parent'        => $parent,
            'current_page'  => $current_page,

            // 数据
            'factory'       => $factory,
        ]));
    }

    /**
     * 打开奶站暂停订单列表页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_stopped_dingdan_list_in_naizhan(Request $request)
    {
        $factory_id = $this->getCurrentFactoryId(false);
        $factory = Factory::find($factory_id);

        $queryOrder = Order::queryStopped()
            ->where('is_deleted', "0")
            ->where('delivery_station_id', $this->getCurrentStationId())
            ->orderBy('updated_at', 'desc');

        $aryBaseData = $this->getOrderList($queryOrder, $request);

        $child = 'zantingliebiao';
        $parent = 'dingdan';
        $current_page = 'zantingliebiao';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.zantingliebiao', array_merge($aryBaseData, [
            // 页面信息
            'pages'         => $pages,
            'child'         => $child,
            'parent'        => $parent,
            'current_page'  => $current_page,

            // 数据
            'factory'       => $factory,
        ]));
    }

    /**
     * 打开奶厂在配送订单列表页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_on_delivery_dingdan_in_gongchang(Request $request)
    {
        $factory_id = $this->getCurrentFactoryId(true);
        $factory = Factory::find($factory_id);

        $queryOrder = Order::where('is_deleted', 0)
            ->where('factory_id', $factory_id)
            ->where('status', Order::ORDER_ON_DELIVERY_STATUS)
            ->orderBy('updated_at', 'desc');

        $aryBaseData = $this->getOrderList($queryOrder, $request);

        $child = 'zaipeisongdingdan';
        $parent = 'dingdan';
        $current_page = 'zaipeisongdingdan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.zaipeisongdingdan', array_merge($aryBaseData, [
            // 页面信息
            'pages'         => $pages,
            'child'         => $child,
            'parent'        => $parent,
            'current_page'  => $current_page,

            // 数据
            'factory'       => $factory,
        ]));
    }

    /**
     * 打开奶站在配送订单列表页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_on_delivery_dingdan_in_naizhan(Request $request)
    {
        $factory_id = $this->getCurrentFactoryId(false);
        $station_id = $this->getCurrentStationId();
        $factory = Factory::find($factory_id);

        $queryOrder = Order::where('is_deleted', 0)
            ->where('delivery_station_id', $station_id)
            ->where('status', Order::ORDER_ON_DELIVERY_STATUS)
            ->orderBy('updated_at', 'desc');

        $aryBaseData = $this->getOrderList($queryOrder, $request);

        $child = 'zaipeisong';
        $parent = 'dingdan';
        $current_page = 'zaipeisong';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.zaipeisong', array_merge($aryBaseData, [
            // 页面信息
            'pages'             => $pages,
            'child'             => $child,
            'parent'            => $parent,
            'current_page'      => $current_page,

            // 数据
            'factory'           => $factory,
        ]));
    }

    //show xudan dingdan in gongchang/xudan
    public
    function show_xudan_dingdan_in_gongchang($order_id)
    {
        $this->initShowFactoryPage();

        $order = Order::find($order_id);
        if (!$order) {
            return;
        }

        $order_checkers = $this->initShowOrderChecker($order);

        $customer = $order->customer;
        $milkman = $order->milkman;

        // 账户余额，只能把所有订单结束了之后才能用
        $remain_amount = 0;
        if ($customer->has_not_order) {
            $remain_amount = $customer->remain_amount;
        }

        $order_products = $order->order_products;

        // 解析收货地址
        $order->resolveAddress();

        $child = 'xudanliebiao';
        $parent = 'dingdan';
        $current_page = 'xudan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.dingdanluru.xudan', [
            // 菜单关联信息
            'pages'                     => $pages,
            'child'                     => $child,
            'parent'                    => $parent,
            'current_page'              => $current_page,

            // 是否修改订单
            'is_edit'                   => false,

            // 录入订单基础信息
            'order_property'            => $this->order_property,
            'province'                  => $this->province,
            'order_checkers'            => $order_checkers,
            'products'                  => $this->products,
            'factory_order_types'       => $this->factory_order_types,
            'order_delivery_types'      => $this->order_delivery_types,
            'products_count_on_fot'     => $this->product_count_on_fot,
            'delivery_stations'         => $this->delivery_stations,
            'gap_day'                   => $this->factory->gap_day,
            'remain_amount'             => $remain_amount,

            // 初始化录入页面
            'order'                     => $order,
            'order_products'            => $order_products,
            'customer'                  => $customer,
            'milkman'                   => $milkman
        ]);
    }

//Show Xudan Page for Naizhan Order
    public
    function show_xudan_dingdan_in_naizhan($order_id)
    {
        $this->initShowStationPage();

        $order_checkers = $this->station->active_order_checkers;

        $order = Order::find($order_id);

        // 解析收货地址
        $order->resolveAddress();

        $customer = $order->customer;
        $milkman = $order->milkman;

        // 账户余额，只能把所有订单结束了之后才能用
        $remain_amount = 0;
        if ($customer->has_not_order) {
            $remain_amount = $customer->remain_amount;
        }

        $order_products = $order->order_products;

        $child = 'quanbuluru';
        $parent = 'dingdan';
        $current_page = 'xiugai';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.xudanliebiao.luruxudan', [
            // 菜单关联信息
            'pages'                     => $pages,
            'child'                     => $child,
            'parent'                    => $parent,
            'current_page'              => $current_page,

            // 是否修改订单
            'is_edit'                   => false,

            // 录入订单基础信息
            'order_property'            => $this->order_property,
            'province'                  => $this->province,
            'order_checkers'            => $order_checkers,
            'products'                  => $this->products,
            'factory_order_types'       => $this->factory_order_types,
            'order_delivery_types'      => $this->order_delivery_types,
            'products_count_on_fot'     => $this->product_count_on_fot,
            'delivery_stations'         => $this->delivery_stations,
            'gap_day'                   => $this->factory->gap_day,
            'remain_amount'             => $remain_amount,

            // 初始化录入页面
            'order'                     => $order,
            'order_products'            => $order_products,
            'customer'                  => $customer,
            'milkman'                   => $milkman,

            // 奶站信息
            'station'                   => $this->station
        ]);
    }

    /**
     * 打开奶厂续单列表页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_xudan_dingdan_liebiao_in_gongchang(Request $request)
    {
        $factory_id = $this->getCurrentFactoryId(true);
        $factory = Factory::find($factory_id);

        $queryOrder = Order::where('is_deleted', 0)
            ->where('factory_id', $factory_id)
            ->where(function ($query) {
                $query->where('status', Order::ORDER_FINISHED_STATUS);
                $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
            })
            ->orderBy('updated_at', 'desc');

        $aryBaseData = $this->getOrderList($queryOrder, $request);

        $child = 'xudanliebiao';
        $parent = 'dingdan';
        $current_page = 'xudanliebiao';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.xudanliebiao', array_merge($aryBaseData, [
            // 页面信息
            'pages'         => $pages,
            'child'         => $child,
            'parent'        => $parent,
            'current_page'  => $current_page,

            // 数据
            'factory'       => $factory,
        ]));
    }

    /**
     * 打开奶站续单列表页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_xudan_dingdan_liebiao_in_naizhan(Request $request)
    {
        $factory_id = $this->getCurrentFactoryId(false);
        $station_id = $this->getCurrentStationId();
        $factory = Factory::find($factory_id);

        $queryOrder = Order::where('is_deleted', 0)
            ->where(function($query) {
                $query->where('status', Order::ORDER_FINISHED_STATUS);
                $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
            })
            ->where('delivery_station_id', $station_id)
            ->orderBy('updated_at', 'desc');

        $aryBaseData = $this->getOrderList($queryOrder, $request);

        $child = 'xudanliebiao';
        $parent = 'dingdan';
        $current_page = 'xudanliebiao';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.xudanliebiao', array_merge($aryBaseData, [
            // 页面信息
            'pages'         => $pages,
            'child'         => $child,
            'parent'        => $parent,
            'current_page'  => $current_page,

            // 数据
            'factory' => $factory,
        ]));
    }

    /**
     * 打开奶厂待审核订单列表页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_check_waiting_dingdan_in_gongchang(Request $request)
    {
        $factory_id = $this->getCurrentFactoryId(true);
        $factory = Factory::find($factory_id);

        $queryOrder = Order::where('is_deleted', 0)
            ->where(function($query) {
                $query->where('status', Order::ORDER_NEW_WAITING_STATUS);
                $query->orWhere('status', Order::ORDER_WAITING_STATUS);
            })
            ->where('factory_id', $factory_id)
            ->orderBy('updated_at', 'desc');

        $aryBaseData = $this->getOrderList($queryOrder, $request);

        $child = 'daishenhedingdan';
        $parent = 'dingdan';
        $current_page = 'daishenhedingdan';;
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.daishenhedingdan', array_merge($aryBaseData, [
            // 页面信息
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,

            // 数据
            'factory' => $factory,
        ]));
    }

    public function change_sub_address_in_gongchang(Request $request)
    {
        if ($request->ajax()) {
            $new_sub_addr = $request->input('new_sub_addr');
            $order_id = $request->input('order_id');

            $new_sub_addr = trim($new_sub_addr);

            $order = Order::find($order_id);

            if ($order) {
                $new_address = $order->main_address . ' ' . $new_sub_addr;
                $order->address = $new_address;
                $order->save();
                $customer = $order->customer;
                $customer->address = $new_address;
                $customer->save();

                //find orders who has the same address and change

//                $orders = Order::where('customer_id', $customer->id)->get();
//
//                foreach ($orders as $order) {
//                    $order->address = $new_address;
//                    $order->save();
//                }

                return response()->json(['status' => 'success']);
            } else
                return response()->json(['status' => 'fail']);
        }
    }

//pass waiting order in gongchang
    public
    function pass_waiting_dingdan_in_gongchang(Request $request)
    {
        if ($request->ajax()) {
            $order_id = $request->input('order_id_to_pass');

            //set order status as passed
            $order = Order::find($order_id);
            if (!$order)
                return response()->json(['status' => 'fail', 'message' => '找不到订单']);

            // 查看奶站是否匹配好
            if (empty($order->station_id) || empty($order->delivery_station_id)) {
                return response()->json(['status' => 'fail', 'message' => '奶站没匹配到，没法通过']);
            }

            //
            // 添加微信通知
            //
            $notification = new NotificationsAdmin;

            if ($order->payment_type == PaymentType::PAYMENT_TYPE_WECHAT) {
                // 新提交
                if ($order->status == Order::ORDER_NEW_WAITING_STATUS) {
                    $notification->sendToWechatNotification($order->customer->id, "您的订单后台已审核通过，订单已生效。");
                }
                // 订单修改
                else {
                    $notification->sendToWechatNotification($order->customer->id, "您的订单修改内容后台核对后已生效。我们会尽力安排您的订单，请耐心等待！");
                }
            }

            $customer_name = $order->customer->name;

            // 添加奶站通知
            $notification->sendToStationNotification($order->station_id,
                DSNotification::CATEGORY_ACCOUNT,
                "订单审核已经通过",
                $customer_name . "用户订单审核已经通过。");

            //set passed status for deliveryplans
            $udps = $order->getUnfinishedDeliveryPlanQuery()->get();
            foreach ($udps as $udp) {
                $udp->passCheck(true);
            }

            // 新订单通过把卡余额加到客户账户余额
            if ($order->status == Order::ORDER_NEW_WAITING_STATUS && $order->payment_type == PaymentType::PAYMENT_TYPE_CARD) {
                $remain_from_card = $order->getMilkcardValue() - $order->total_amount;

                $customer = $order->customer;
                $customer->remain_amount += $remain_from_card;
                $customer->save();
            }

            // 订单通过
            if ($order->status == Order::ORDER_NEW_WAITING_STATUS || $order->status == Order::ORDER_WAITING_STATUS) {
                $order->status = Order::ORDER_ON_DELIVERY_STATUS;
            }
            $order->save();

            // 添加系统日志
            $this->addSystemLog(User::USER_BACKEND_FACTORY, '订单', SysLog::SYSLOG_OPERATION_CHECK);

            return response()->json(['status' => 'success', 'message' => '订单通过成功.']);
        }
    }

    //pass waiting order in gongchang
    public
    function not_pass_waiting_dingdan_in_gongchang(Request $request)
    {
        if ($request->ajax()) {
            $order_id = $request->input('order_id_to_not_pass');

            //set order status as passed
            $order = Order::find($order_id);
            if (!$order)
                return response()->json(['status' => 'fail', 'message' => '找不到订单']);

            // 新订单不通过
            if ($order->status == Order::ORDER_NEW_WAITING_STATUS) {
                $order->status = Order::ORDER_NEW_NOT_PASSED_STATUS;

                // 如果是现金订单，需要返还配送信用余额
                if ($order->payment_type == PaymentType::PAYMENT_TYPE_MONEY_NORMAL) {
                    $station = $order->station;

                    $station->delivery_credit_balance += $order->total_amount;
                    $station->save();
                }
            }
            // 订单不通过
            else if ($order->status == Order::ORDER_WAITING_STATUS) {
                $order->status = Order::ORDER_NOT_PASSED_STATUS;
            }

            $order->save();

            $customer_name = $order->customer->name;

            // 添加奶站通知
            $notification = new NotificationsAdmin();
            $notification->sendToStationNotification($order->station_id,
                DSNotification::CATEGORY_ACCOUNT,
                "订单审核未通过",
                $customer_name . "用户订单审核未通过。");

            // 删除其订单的配送明细
            $order->getUnfinishedDeliveryPlanQuery()->delete();

            return response()->json(['status' => 'success', 'message' => '订单未通过成功.']);
        }
    }

    /**
     * 打开奶站待审核订单列表页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_check_waiting_dingdan_in_naizhan(Request $request)
    {
        $factory_id = $this->getCurrentFactoryId(false);
        $station_id = $this->getCurrentStationId();
        $factory = Factory::find($factory_id);

        // 只显示本站录入的订单
        $queryOrder = Order::where('is_deleted', 0)
            ->where('station_id', $station_id)
            ->where(function($query) {
                $query->where('status', Order::ORDER_NEW_WAITING_STATUS);
                $query->orWhere('status', Order::ORDER_WAITING_STATUS);
            })
            ->orderBy('updated_at', 'desc');

        $aryBaseData = $this->getOrderList($queryOrder, $request);

        $child = 'daishenhe';
        $parent = 'dingdan';
        $current_page = 'daishenhe';;
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.daishenhe', array_merge($aryBaseData, [
            // 页面信息
            'pages'         => $pages,
            'child'         => $child,
            'parent'        => $parent,
            'current_page'  => $current_page,

            // 数据
            'factory'       => $factory,
        ]));
    }

    /**
     * 获取订单列表
     * @param $queryOrder
     * @param Request $request
     * @return mixed
     */
    private function getOrderList($queryOrder, Request $request) {

        $retData = array();

        // 收件人
        $customer = $request->input('customer');
        if (!empty($customer)) {
            // 筛选
            $queryOrder->whereHas('customer', function($query) use ($customer) {
                $query->where('name', 'like', '%' . $customer . '%');
            });

            // 添加筛选参数
            $retData['customer'] = $customer;
        }

        // 电话
        $phone = $request->input('phone');
        if (!empty($phone)) {
            // 筛选
            $queryOrder->where('phone', 'like', '%' . $phone . '%');

            // 添加筛选参数
            $retData['phone'] = $phone;
        }

        // 奶站
        $station = $request->input('station');
        if (!empty($station)) {
            // 筛选
            $queryOrder->where('delivery_station_id', $station);

            // 添加筛选参数
            $retData['station'] = $station;
        }

        // 订单性质
        $property = $request->input('property');
        if (!empty($property)) {
            // 筛选
            $queryOrder->whereHas('property', function($query) use ($property) {
                $query->where('id', $property);
            });

            // 添加筛选参数
            $retData['property'] = $property;
        }

        // 订单编号
        $number = $request->input('number');
        if (!empty($number)) {
            // 筛选
            $queryOrder->where('number', 'like', '%' . $number . '%');

            // 添加筛选参数
            $retData['number'] = $number;
        }

        // 地址
        $address = $request->input('address');
        if (!empty($address)) {
            // 筛选
            $queryOrder->where('address', 'like', '%' . $address . '%');

            // 添加筛选参数
            $retData['address'] = $address;
        }

        // 票据号
        $receipt = $request->input('receipt');
        if (!empty($receipt)) {
            // 筛选
            $queryOrder->where('receipt_number', 'like', '%' . $receipt . '%');

            // 添加筛选参数
            $retData['receipt'] = $receipt;
        }

        // 征订员
        $checker = $request->input('checker');
        if (!empty($checker)) {
            // 筛选
            $queryOrder->whereHas('checker', function($query) use ($checker) {
                $query->where('name', 'like', '%' . $checker . '%');
            });

            // 添加筛选参数
            $retData['checker'] = $checker;
        }

        // 订单类型
        $type = $request->input('type');
        if (!empty($type)) {
            // 筛选
            $queryOrder->whereHas('order_products', function($query) use ($type) {
                $query->where('order_type', $type);
            });

            // 添加筛选参数
            $retData['type'] = $type;
        }

        // 支付类型
        $ptype = $request->input('ptype');
        if (!empty($ptype)) {
            // 筛选
            $queryOrder->where('payment_type', $ptype);

            // 添加筛选参数
            $retData['ptype'] = $ptype;
        }

        // 订单状态
        $status = $request->input('status');
        if (!empty($status)) {
            // 筛选
            $queryOrder->where('status', $status);

            // 添加筛选参数
            $retData['status'] = $status;
        }

        // 下单日期
        $start = $request->input('start');
        if (!empty($start)) {
            // 筛选
            $queryOrder->where('ordered_at', '>=', $start);

            // 添加筛选参数
            $retData['start'] = $start;
        }
        $end = $request->input('end');
        if (!empty($end)) {
            // 筛选
            $queryOrder->where('ordered_at', '<=', $end);

            // 添加筛选参数
            $retData['end'] = $end;
        }

        // 到期日期
        $endDate = $request->input('end_date');
        if (!empty($endDate)) {
        }

        // 保存到session
        $request->session()->put('query_order', $retData);

        $retData['orders'] = $queryOrder->paginate();

        // 订单性质
        $order_properties = OrderProperty::get();
        $retData['order_properties'] = $order_properties;

        // 支付类型
        $payment_types = PaymentType::get();
        $retData['payment_types'] = $payment_types;

        return $retData;
    }

    /**
     * 打开奶厂全部订单列表页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_all_dingdan_in_gongchang(Request $request)
    {
        $factory_id = $this->getCurrentFactoryId(true);
        $factory = Factory::find($factory_id);

        $queryOrder = Order::where('is_deleted', 0)
            ->where('factory_id', $factory_id)
            ->orderBy('created_at', 'desc');

        $aryBaseData = $this->getOrderList($queryOrder, $request);

        //find total amount according to payment type
        //payment type: wechat=3, money=1, card=2

        // 现金订单
        $money_amount = Order::where('is_deleted', 0)
            ->where('factory_id', $factory_id)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->sum('total_amount');
        $money_dcount = Order::where('is_deleted', 0)
            ->where('factory_id', $factory_id)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->count();

        // 奶卡订单
        $card_amount = Order::where('is_deleted', 0)
            ->where('factory_id', $factory_id)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->sum('total_amount');
        $card_dcount = Order::where('is_deleted', 0)
            ->where('factory_id', $factory_id)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->count();

        // 微信订单
        $wechat_amount = Order::where('is_deleted', 0)
            ->where('factory_id', $factory_id)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
            ->sum('total_amount');
        $wechat_dcount = Order::where('is_deleted', 0)
            ->where('factory_id', $factory_id)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
            ->count();

        // 没数据变量会变成null
        if (empty($money_amount))
            $money_amount = 0;
        if (empty($card_amount))
            $card_amount = 0;
        if (empty($wechat_amount))
            $wechat_amount = 0;

        $child = 'quanbudingdan-liebiao';
        $parent = 'dingdan';
        $current_page = 'quanbudingdan-liebiao';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.quanbudingdan-liebiao', array_merge($aryBaseData, [
            // 页面信息
            'pages'             => $pages,
            'child'             => $child,
            'parent'            => $parent,
            'current_page'      => $current_page,

            // 数据
            'money_amount'      => $money_amount,
            'money_dcount'      => $money_dcount,
            'card_amount'       => $card_amount,
            'card_dcount'       => $card_dcount,
            'wechat_amount'     => $wechat_amount,
            'wechat_dcount'     => $wechat_dcount,
            'factory'           => $factory,
        ]));
    }

    /**
     * 打开奶站全部订单列表页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show_all_dingdan_in_naizhan(Request $request)
    {
        $factory_id = $this->getCurrentFactoryId(false);
        $station_id = $this->getCurrentStationId();
        $factory = Factory::find($factory_id);

        $queryOrder = Order::where('is_deleted', 0)
            ->where('factory_id', $factory_id)
            ->where(function ($query) use ($station_id) {
                $query->where('station_id', $station_id);
                $query->orWhere('delivery_station_id', $station_id);
            })
            ->orderBy('created_at', 'desc');

        $aryBaseData = $this->getOrderList($queryOrder, $request);

        $child = 'quanbuluru';
        $parent = 'dingdan';
        $current_page = 'quanbuluru';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.quanbuluru', array_merge($aryBaseData, [
            // 页面信息
            'pages'             => $pages,
            'child'             => $child,
            'parent'            => $parent,
            'current_page'      => $current_page,

            // 数据
            'factory'           => $factory,
        ]));
    }

    public function show_order_of_this_week_in_gongchang()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;
        $factory = Factory::find($factory_id);

        $orders = Order::where('is_deleted', "0")
            ->orderBy('updated_at', 'desc')
            ->where('factory_id', $factory_id)
            ->get();//add time condition

        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        //find total amount according to payment type
        //payment type: wechat=3, money=1, card=2

        //get wechat amount
        $money_amount = $card_amount = $wechat_amount = 0;
        $money_dcount = $card_dcount = $wechat_dcount = 0;

        $week_orders = array();

        foreach ($orders as $order) {
            $order_date = $order->ordered_at;
            if ($order_date && $this->check_in_this_week($order_date)) {
                array_push($week_orders, $order);

                if ($order->payment_type == 3) {
                    $wechat_amount += $order->total_amount;
                    $wechat_dcount++;

                } else if ($order->payment_type == 2) {
                    $card_amount += $order->total_amount;
                    $card_dcount++;

                } else {

                    $money_amount += $order->total_amount;
                    $money_dcount++;
                }
            } else {
                continue;
            }
        }

        $child = 'quanbudingdan-liebiao';
        $parent = 'dingdan';
        $current_page = 'quanbudingdan-liebiao';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        return view('gongchang.dingdan.quanbudingdan-liebiao', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $week_orders,
            'money_amount' => $money_amount,
            'money_dcount' => $money_dcount,
            'card_amount' => $card_amount,
            'card_dcount' => $card_dcount,
            'wechat_amount' => $wechat_amount,
            'wechat_dcount' => $wechat_dcount,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
            'factory' => $factory,
        ]);

    }

    public function show_order_of_this_month_in_gongchang()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;
        $factory = Factory::find($factory_id);

        $orders = Order::where('is_deleted', "0")
            ->orderBy('updated_at', 'desc')
            ->where('factory_id', $factory_id)
            ->get();//add time condition

        //find total amount according to payment type
        //payment type: wechat=3, money=1, card=2
        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        //get wechat amount
        $money_amount = $card_amount = $wechat_amount = 0;
        $money_dcount = $card_dcount = $wechat_dcount = 0;

        $month_orders = array();

        foreach ($orders as $order) {
            $order_date = $order->ordered_at;
            if ($order_date && $this->check_in_this_month($order_date)) {
                array_push($month_orders, $order);

                if ($order->payment_type == 3) {
                    $wechat_amount += $order->total_amount;
                    $wechat_dcount++;

                } else if ($order->payment_type == 2) {
                    $card_amount += $order->total_amount;
                    $card_dcount++;

                } else {

                    $money_amount += $order->total_amount;
                    $money_dcount++;
                }
            } else {
                continue;
            }
        }

        $child = 'quanbudingdan-liebiao';
        $parent = 'dingdan';
        $current_page = 'quanbudingdan-liebiao';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        return view('gongchang.dingdan.quanbudingdan-liebiao', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $month_orders,
            'money_amount' => $money_amount,
            'money_dcount' => $money_dcount,
            'card_amount' => $card_amount,
            'card_dcount' => $card_dcount,
            'wechat_amount' => $wechat_amount,
            'wechat_dcount' => $wechat_dcount,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
            'factory' => $factory,
        ]);
    }


    function cancel_order(Request $request)
    {
        if ($request->ajax()) {
            $order_id = $request->input('order_id');

            //set status of order as stopped
            $order = Order::find($order_id);
            if ($order) {
                $remain_amount = $order->remain_order_money;

                $order->status = Order::ORDER_CANCELLED_STATUS;
//                $order->remaining_amount = $remain_amount;
                $order->remaining_amount = 0;
                $order->save();

                // 把订单余额加到配送信用余额和结算账户
                $order->station->calculation_balance += $remain_amount;
                $order->station->delivery_credit_balance += $remain_amount;
                $order->station->save();

                //add order's remain amount to customer account's remain amount
//                if ($remain_amount > 0) {
//                    $customer = Customer::find($order->customer_id);
//                    if ($customer) {
//                        $customer->remain_amount += $remain_amount;
//                        $customer->save();
//                    }
//                }

                //Delete Delivery Plans for cancelled order
                $order->getUnfinishedDeliveryPlanQuery()->forceDelete();

                return response()->json(['status' => 'success', 'message' => '退订成功.']);
            } else {
                return response()->json(['status' => 'fail', 'message' => '未找到订单.']);
            }
        }
    }

    //show waiting dingdan in detail in gongchang
    function show_detail_waiting_dingdan_in_gongchang(Request $request, $order_id)
    {
        $order = Order::find($order_id);
        $order_products = $order->order_products;

        // 解析收货地址
        $order->resolveAddress();

        $grouped_plans_per_product = $this->getOrderDeliveryPlan($request, $order_id);

        $child = 'daishenhedingdan';
        $parent = 'dingdan';
        $current_page = 'daishenhe-dingdanxiangqing';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.daishenhedingdan.daishenhe-dingdanxiangqing', [
            // 菜单关联信息
            'pages'                     => $pages,
            'child'                     => $child,
            'parent'                    => $parent,
            'current_page'              => $current_page,

            // 订单内容
            'order'                     => $order,
            'order_products'            => $order_products,
            'grouped_plans_per_product' => $grouped_plans_per_product
        ]);
    }

    public function order_number($fid, $sid, $cid, $order_id)
    {
        return 'F' . $fid . 'S' . $sid . 'C' . $cid . 'O' . $order_id;
    }

    //Get next key in array
    function get_next_key($array, $current_key)
    {
        $keys = array_keys($array);
        $count = count($keys);

        $nextkey = "";
        foreach (array_keys($keys) As $k) {
            $this_value = $keys[$k];


            if ($this_value == $current_key) {

                if ($k + 1 < $count) {
                    $nextkey = $keys[$k + 1];

                } else {
                    $nextkey = $keys[0];
                }

                break;
            }
        }
        return $nextkey;
    }

    //Get next deliver at day after interval day
    function get_deliver_at_day($deliver_at, $interval)
    {
        if ($interval == 1) {
            $interval = '+' . $interval . ' day';
        } else {
            $interval = '+' . $interval . ' days';
        }

        $deliver_at_day = new DateTime($deliver_at);
        $deliver_at_day->modify($interval);
        $result = $deliver_at_day->format('Y-m-d');
        return $result;
    }

    //Get Product Price with Customer id and address
    public function get_product_price_by_cid($pid, $otype, $cid, $date = null)
    {
        $addr = Customer::find($cid)->address;
        $price = $province = $city = $district = null;

        $addr_array = multiexplode(array('，', ' ', ','), $addr);
        $province = $addr_array[0];
        $city = $addr_array[1];
        $district = $addr_array[2];

        if ($province && $city && $district) {
            $price = $this->get_product_price_by_pcd($pid, $otype, $province, $city, $district, $date);
        }
        return $price;
    }

    /**
     * 根据地区获取价格
     * @param $pid
     * @param $otype
     * @param $province
     * @param $city
     * @param $district
     * @param null $date
     * @return null
     */
    function get_product_price_by_pcd($pid, $otype, $province, $city, $district, $date = null)
    {
        $addr = $province . " " . $city . " " . $district;
        $pp = ProductPrice::priceTemplateFromAddress($pid, $addr, $date);
//        $pp = ProductPrice::where('product_id', $pid)->where('sales_area', 'like', $province . '%' . $city . '%' . $district . '%')->get()->first();
        $price = null;
        if ($pp) {
            if ($otype == 1) {
                $price = $pp->month_price;
            } else if ($otype == 2) {
                $price = $pp->season_price;
            } else if ($otype == 3) {
                $price = $pp->half_year_price;
            }
        }
        return $price;
    }

    //get order price of product selected in product list
    function get_order_product_price(Request $request)
    {
        if ($request->ajax()) {

            $product_id = $request->input('product_id');
            $order_type = $request->input('order_type');
            $customer_id = $request->input('customer_id');

            $date = $request->input('created_at');

            $product_price = null;

            if (!$customer_id) {
                $province = $request->input('province');
                $city = $request->input('city');

                // 去掉空格
                $district = trim($request->input('district'));
                $district = str_replace('　', '', $district);

                $product_price = $this->get_product_price_by_pcd($product_id,
                    $order_type,
                    $province,
                    $city,
                    $district,
                    $date);

            } else {
                $product_price = $this->get_product_price_by_cid($product_id,
                    $order_type,
                    $customer_id,
                    $date);
            }

            if (!$product_price) {
                return response()->json(['status' => 'fail', 'message' => '没有产品价格']);
            }

            return response()->json(['status' => 'success', 'order_product_price' => $product_price]);
        }
    }

    //MODULE
    //Get Station and Milk Pair from Factory With address
    public function get_station_milkman_with_address_from_factory($factory_id, $address, &$station)
    {
        $factory = Factory::find($factory_id);

        $stations = $factory->active_stations;
        $station_ids = [];

        foreach ($stations as $fstation) {
            $station_ids[] = $fstation->id;
        }

        $delivery_area = DSDeliveryArea::where('address', $address)->first();

        if ($delivery_area == null) {
            //客户并不住在可以递送区域
            return OrderCtrl::NOT_EXIST_DELIVERY_AREA;
        }

        $result = [];

        $delivery_station_count = 0;

        $delivery_station_id = $delivery_area->station_id;

        $delivery_station = DeliveryStation::find($delivery_station_id);

        if ($delivery_station && in_array($delivery_station_id, $station_ids)) {

            $delivery_station_count++;

            // 保存奶站信息
            $station = $delivery_station;

            //get this station's milkman that supports this address
            $milkman = $delivery_station->get_milkman_of_address($address);

            if ($milkman) {
                $result[] = $delivery_station_id;
                $result[] = $milkman->id;
                $result[] = $delivery_area->id;
            }
        }

        if ($delivery_station_count == 0) {
            return OrderCtrl::NOT_EXIST_STATION;
        }

        if (count($result) == 0) {
            return OrderCtrl::NOT_EXIST_MILKMAN;
        }

        return $result;
    }

    public function delete_all_order_products_and_delivery_plans_for_update_order($order)
    {
        //delete waiting and passed delivery  plan
        MilkManDeliveryPlan::where('order_id', $order->id)
            ->where(function ($query) {
                $query->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED);
                $query->orWhere('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING);
            })
            ->forceDelete();

        MilkManDeliveryPlan::where('order_id', $order->id)
            ->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT)
            ->update([
                'changed_plan_count' => 0,
                'delivery_count' => 0,

                // 改成取消状态
                'status' => MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_CANCEL,
                'cancel_reason' => MilkManDeliveryPlan::DP_CANCEL_CHANGEORDER,
            ]);

        $order->order_products()->delete();
    }

    /**
     * 删除订单
     * @param $order_id
     */
    public function delete_order($order_id)
    {
        $order = Order::find($order_id);

        //first delete milkman delivery plan
        MilkManDeliveryPlan::where('order_id', $order_id)->forceDelete();

        if($order)
        {
            //delete order product
            $order->order_products()->forceDelete();

            //delete order
            $order->delete();
        }
    }

    /**
     * 导出订单列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportOrderList(Request $request) {
        $factory_id = $this->getCurrentFactoryId(true);

        $filepath = public_path() . "/exports/order" . time() . ".csv";

        $query = "select 
                '订单号', 
                '收货人', 
                '电话', 
                '地址', 
                '订单金额', 
                '支付', 
                '订单性质', 
                '征订员', 
                '奶站', 
                '下单时间' as time,
                '一级推荐人',
                '二级推荐人'
            union all
            select 
                o.number, 
                c.name, 
                o.phone, 
                o.address, 
                ifnull(o.total_amount, ''), 
                pt.name,
                op.name,
                oc.name,
                ifnull(s.name, ''),
                o.created_at,
                ifnull(w.openid, ''),
                ifnull(w2.openid, '')
            from orders o
            join customer c on c.id = o.customer_id
            join paymenttype pt on pt.id = o.payment_type
            join orderproducts opd on opd.order_id = o.id
            join orderproperty op on op.id = o.order_property_id
            join ordercheckers oc on oc.id = o.order_checker_id
            left join deliverystations s on s.id = o.delivery_station_id
            left join wxusers w on w.id = o.wxuser_id
            left join wxusers w2 on w2.id = w.parent
            where o.factory_id = " . $factory_id;

        // 从session获取筛选信息
        $queryData = $request->session()->get('query_order');

        // 收件人
        if (!empty($queryData['customer'])) {
            $query .= " and c.name like '%" . $queryData['customer'] . "%'";
        }
        // 电话
        if (!empty($queryData['phone'])) {
            $query .= " and o.phone like '%" . $queryData['phone'] . "%'";
        }
        // 奶站
        if (!empty($queryData['station'])) {
            $query .= " and s.id = " . $queryData['station'];
        }
        // 订单性质
        if (!empty($queryData['property'])) {
            $query .= " and o.order_property_id = " . $queryData['property'];
        }
        // 订单编号
        if (!empty($queryData['number'])) {
            $query .= " and o.number like '%" . $queryData['number'] . "%'";
        }
        // 地址
        if (!empty($queryData['address'])) {
            $query .= " and o.address like '%" . $queryData['address'] . "%'";
        }
        // 票据号
        if (!empty($queryData['receipt'])) {
            $query .= " and o.receipt_number like '%" . $queryData['receipt'] . "%'";
        }
        // 征订员
        if (!empty($queryData['checker'])) {
            $query .= " and oc.name like '%" . $queryData['checker'] . "%'";
        }
        // 订单类型
        if (!empty($queryData['type'])) {
            $query .= " and odp.order_type = " . $queryData['type'];
        }
        // 支付类型
        if (!empty($queryData['ptype'])) {
            $query .= " and o.payment_type = " . $queryData['ptype'];
        }
        // 订单状态
        if (!empty($queryData['status'])) {
            $query .= " and o.status = " . $queryData['status'];
        }
        // 下单日期
        if (!empty($queryData['start'])) {
            $query .= " and o.ordered_at >= '" . $queryData['start'] . "'";
        }
        if (!empty($queryData['end'])) {
            $query .= " and o.ordered_at <= '" . $queryData['end'] . "'";
        }

        $query .= " group by o.id 
            order by time desc
            into outfile '" . $filepath . "'
            fields terminated by ','
            escaped by '\"'
            enclosed by '\"'
            lines terminated by '\n';";

        DB::statement($query);

        return response()->download($filepath)->deleteFileAfterSend(true);
    }
}

