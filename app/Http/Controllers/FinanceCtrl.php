<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 10/5/2016
 * Time: 4:58 PM
 */

namespace App\Http\Controllers;

use App\Model\BasicModel\PaymentType;
use App\Model\FinanceModel\DSBusinessCreditBalanceHistory;
use App\Model\FinanceModel\DSCalcBalanceHistory;
use App\Model\FinanceModel\DSDeliveryCreditBalanceHistory;
use App\Model\FinanceModel\DSTransaction;
use App\Model\FinanceModel\DSTransactionPay;
use App\Model\NotificationModel\DSNotification;

use App\Model\FinanceModel\StationsMoneyTransfer;

use App\Model\OrderModel\OrderType;
use Faker\Provider\at_AT\Payment;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Model\UserModel\Page;
use App\Model\UserModel\User;

use App\Model\OrderModel\Order;

use App\Model\DeliveryModel\DeliveryStation;
use App\Model\FactoryModel\Factory;

use App\Model\SystemModel\SysLog;

use File;
use Auth;
use DateTime;
use DateTimeZone;
use Excel;

class FinanceCtrl extends Controller
{
    //Feature
    public function getSumOfOrders($orders)
    {
        $sum = 0;
        if ($orders) {
            foreach ($orders as $order) {
                $sum += $order->total_amount;
            }
        }
        return $sum;
    }

    /*
     * GONGCHANG_FINANCE
     * */

    //G1: First page
    public function show_finance_page_in_gongchang()
    {
        $factory_id = $this->getCurrentFactoryId(true);
        $factory = Factory::find($factory_id);

        $stations = $factory->active_stations;

        // 计算财务信息
        foreach ($stations as $s) {
            $this->getSummary($s);
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '奶站台帐页面', SysLog::SYSLOG_OPERATION_VIEW);

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'taizhang';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.caiwu.taizhang', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations' => $stations,
            'is_station' => false
        ]);
    }

    //G1-1: Insert money received from first page
    public function insert_money_order_received_to_station(Request $request)
    {
        if ($request->ajax()) {
            //station_id, amount, trans_id, comment
            $station_id = $request->input('station_id');
            $amount = $request->input('amount');
            $receipt_number = $request->input('receipt_number');
            $comment = $request->input('comment');

            //Save DSCalc Balance History
            $dsbh = new DSCalcBalanceHistory;
            $dsbh->station_id = $station_id;
            $dsbh->type = DSCalcBalanceHistory::DSCBH_IN_MONEY_STATION;
            $dsbh->amount = $amount;
            $dsbh->receipt_number = $receipt_number;
            $dsbh->io_type = DSCalcBalanceHistory::DSCBH_TYPE_IN;
            $dsbh->comment = $comment;
            $dsbh->save();

            //Increase DS Delivery Credit Balance and Calc Balance
            $station = DeliveryStation::find($station_id);
            
            if ($station) {
                $station->calculation_balance += $amount;
                $station->delivery_credit_balance += $amount;
                $station->save();
            } else
                return response()->json(['status' => 'fail', 'message' => "奶站未发现"]);

            $notification = new NotificationsAdmin();
            $notification->sendToStationNotification($station_id,
                DSNotification::CATEGORY_TRANSACTION,
                "奶厂已给您的结算账户",
                "奶厂已给您的结算账户，转入".$amount."元。请您查收核对");

            return response()->json(['status' => 'success', 'station_id' => $station_id, 'amount' => $amount]);
        }
    }

    /**
     * 计算台账财务数据
     * @param $station
     */
    private function getSummary(&$station) {
        $nCount = 0;
        $nCost = 0;

        // 期初余额
        $station->getBottleCountBeforeThisTerm($nCount, $nCost);
        $station['fin_before_count'] = $nCount;
        $station['fin_before_cost'] = $nCost;

        // 本期订单金额增加
        $station->getBottleCountIncreasedThisTerm($nCount, $nCost);
        $station['fin_added_count'] = $nCount;
        $station['fin_added_cost'] = $nCost;

        // 本期完成订单余额
        $station->getBottleDoneThisTerm($nCount, $nCost);
        $station['fin_done_count'] = $nCount;
        $station['fin_done_cost'] = $nCost;

        // 期末金额
        $station['fin_after_count'] = $station['fin_before_count'] + $station['fin_added_count'] - $station['fin_done_count'];
        $station['fin_after_cost'] = $station['fin_before_cost'] + $station['fin_added_cost'] - $station['fin_done_cost'];
    }

    /**
     * 获取奶站订单金额统计所需的数据
     * @param $station_id
     * @return array
     */
    private function getOrderMoneyStatistics($station_id) {
        $station = DeliveryStation::find($station_id);

        //all orders in month
        $orders = $station->orders_in_month;

        //Money Orders
        //current station's orders
        $money_orders = $station->getMoneyOrdersInput();
        $money_orders_count = count($money_orders);
        $money_orders_sum = $this->getSumOfOrders($money_orders);

        //Really orders received money
        $money_orders_really_got_sum = $station->money_orders_really_got_sum;


        //The money : not received at the start of month
        //receivable amount
        $receivable_order_money = $station->receivable_order_money;

        //My Money Orders: to others
        $money_orders_of_others = $station->money_orders_of_others;
        $money_orders_of_others_count = count($money_orders_of_others);
        $money_orders_of_others_sum = $this->getSumOfOrders($money_orders_of_others);

        //My Money Orders of mine
        $money_orders_of_mine = $station->money_orders_of_mine;
        $money_orders_of_mine_count = count($money_orders_of_mine);
        $money_orders_of_mine_sum = $this->getSumOfOrders($money_orders_of_mine);


        //Wechat Orders
        //current station's orders
        $wechat_orders = $station->wechat_orders;
        $wechat_orders_count = count($wechat_orders);
        $wechat_orders_sum = $this->getSumOfOrders($wechat_orders);

        //Really orders received wechat
        $wechat_orders_really_got = $station->wechat_orders_really_got;
        $wechat_orders_really_got_count = count($wechat_orders_really_got);
        $wechat_orders_really_got_sum = $this->calcOrderTransferAmount($wechat_orders_really_got);


        //CARD ORDERS
        //current station's orders
        $card_orders = $station->card_orders;
        $card_orders_count = count($card_orders);
        $card_orders_sum = $this->getSumOfOrders($card_orders);

        //Really orders received card
        $card_orders_really_got = $station->card_orders_really_got;
        $card_orders_really_got_count = count($card_orders_really_got);
        $card_orders_really_got_sum = $this->getSumOfOrders($card_orders_really_got);


        //OTHER ORDERS
        //current station's orders
        $other_orders = $station->other_orders;
        $other_orders_count = count($other_orders);
        $other_orders_sum = $this->getSumOfOrders($other_orders);

        //Really orders received wechat
        $other_orders_really_got = $station->other_orders_really_got;
        $other_orders_really_got_count = count($other_orders_really_got);

        // 其他奶站订单转入实收金额
        $other_orders_really_got_sum = $this->calcOrderTransferAmount($other_orders_really_got);

        $calc_histories = DSCalcBalanceHistory::where('station_id', $station_id)
            ->whereMonth('created_at', '=', date('m'))
            ->whereYear('created_at', '=', date('Y'))
            ->where(function($query) {
                $query->where('type', DSCalcBalanceHistory::DSCBH_IN_MONEY_STATION);
                $query->orwhere('type', DSCalcBalanceHistory::DSCBH_IN_ORDER_OTHER_STATION);
                $query->orwhere('type', DSCalcBalanceHistory::DSCBH_IN_ORDER_CARD);
                $query->orwhere('type', DSCalcBalanceHistory::DSCBH_IN_ORDER_WECHAT);
                $query->orwhere('type', DSCalcBalanceHistory::DSCBH_IN_ORDER_OUT_OTHER);
            })
            ->orderby('created_at', 'desc')
            ->get();

        return array(
            'station' => $station,
            'orders' => $orders,

            //Money Orders
            'money_orders_count' => $money_orders_count,
            'money_orders_sum' => $money_orders_sum,
            'money_orders_really_got_sum' => $money_orders_really_got_sum,
            'money_orders_of_others_count' => $money_orders_of_others_count,
            'money_orders_of_others_sum' => $money_orders_of_others_sum,
            'money_orders_of_mine_count' => $money_orders_of_mine_count,
            'money_orders_of_mine_sum' => $money_orders_of_mine_sum,
            'receivable_order_money' => $receivable_order_money,

            //Wechat Orders
            'wechat_orders_count' => $wechat_orders_count,
            'wechat_orders_sum' => $wechat_orders_sum,
            'wechat_orders_really_got_count' => $wechat_orders_really_got_count,
            'wechat_orders_really_got_sum' => $wechat_orders_really_got_sum,

            //Card Orders
            'card_orders_count' => $card_orders_count,
            'card_orders_sum' => $card_orders_sum,
            'card_orders_really_got_count' => $card_orders_really_got_count,
            'card_orders_really_got_sum' => $card_orders_really_got_sum,


            //Received Orders From Others
            'other_orders_count' => $other_orders_count,
            'other_orders_sum' => $other_orders_sum,
            'other_orders_really_got_count' => $other_orders_really_got_count,
            'other_orders_really_got_sum' => $other_orders_really_got_sum,

            // Calculation Histories
            'calc_histories' => $calc_histories,
        );
    }

    //G2: Show Selected Station's Order Money Status
    public function show_station_order_money_in_gongchang($station_id)
    {
        $aryData = $this->getOrderMoneyStatistics($station_id);

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'naizhandingdanjinetongji';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        $aryPage = array(
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'is_station' => false
        );

        return view('gongchang.caiwu.taizhang.naizhandingdanjinetongji', array_merge($aryData, $aryPage));
    }

    /**
     * 计算实际转账金额
     * @param $orders
     * @return int
     */
    private function calcOrderTransferAmount($orders) {
        $nAmount = 0;

        foreach ($orders as $transOrder) {
            $transaction = $transOrder->transaction;

            // 订单没有账单信息，下一个
            if (!$transaction) {
                continue;
            }

            // 账单没有转账信息，下一个
            if (!$transaction->transactionPay) {
                continue;
            }

            $nAmount += $transaction->transactionPay->amount;
        }

        return $nAmount;
    }

    //G3: Show station calc account balance
    public function show_station_calc_account_balance_in_gongchang($station_id)
    {
        $child = 'naizhanzhanghuyue';
        $parent = 'caiwu';
        $current_page = 'naizhanzhanghuyue';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        $station = DeliveryStation::find($station_id);

        //show only current months history
        $calc_histories_out = $station->calc_histories_out;
        return view('gongchang.caiwu.taizhang.naizhanzhanghuyue', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'station' => $station,
            'calc_histories_out' => $calc_histories_out,
            'today' => getCurDateString(),
            'is_station' => false
        ]);
    }

    //G3-1: Create Station's New Calculation History about Distribution
    public function add_calc_history(Request $request)
    {
        if ($request->ajax()) {
            $time = $request->input('time');
            $amount = $request->input('amount');
            $station_id = $request->input('station_id');
            $type = $request->input('type');
            $receipt_number = $request->input('receipt_number');
            $comment = $request->input('comment');

            $station = DeliveryStation::find($station_id);
            if (!$station)
                return response()->json(['status' => 'fail', 'message' => '找不到奶站']);

            $dscbh = new DSCalcBalanceHistory;
            $dscbh->amount = $amount;
            $dscbh->station_id = $station_id;
            $dscbh->type = $type;
            $dscbh->io_type = DSCalcBalanceHistory::DSCBH_TYPE_OUT;
            $dscbh->receipt_number = $receipt_number;
            $dscbh->comment = $comment;
            $dscbh->save();

            //descrease the calculation balance
            $station->calculation_balance -= $amount;
            $station->save();

            return response()->json(['status' => 'success']);
        }
    }

    //G4: Show Self Business account balance
    public function show_self_account_in_gongchang($station_id)
    {
        $station = DeliveryStation::find($station_id);
        $self_business_history = $station->self_business_history;

        $child = 'ziyingzhanghu';
        $parent = 'caiwu';
        $current_page = 'ziyingzhanghu';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.caiwu.taizhang.ziyingzhanghu', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'station' => $station,
            'self_business_history' => $self_business_history,
            'is_station' => false
        ]);
    }

    //G4-1: Add self business history
    public function add_self_business_history(Request $request)
    {
        if ($request->ajax()) {

            $station_id = $request->input('station_id');
            $io_type = $request->input('io_type');
            $type = $request->input('type');
            $amount = $request->input('amount');
            $receipt_number = $request->input('receipt_number');
            $comment = $request->input('comment');

            $station = DeliveryStation::find($station_id);
            if (!$station)
                return response()->json(['status' => 'fail', 'message' => '找不到奶站']);

            if ($io_type == DSBusinessCreditBalanceHistory::DSBCBH_IN) {
                $station->business_credit_balance += $amount;
            } else {
                //Limit option check
                $station->business_credit_balance -= $amount;
                if ($station->business_credit_balance <= ($station->init_business_credit_amount / 10)) {
                    return response()->json(['status' => 'fail', 'message' => '贷方余额是信用额度不足10％.']);
                }
            }

            $station->save();

            $dsbcbh = new DSBusinessCreditBalanceHistory;
            $dsbcbh->amount = $amount;
            $dsbcbh->station_id = $station_id;
            $dsbcbh->type = $type;
            $dsbcbh->io_type = $io_type;
            if ($io_type == DSBusinessCreditBalanceHistory::DSBCBH_IN)
                $dsbcbh->receipt_number = $receipt_number;
            $dsbcbh->comment = $comment;
            $dsbcbh->save();

            // 添加奶站通知
            $notification = new NotificationsAdmin();
            $notification->sendToStationNotification($station_id,
                DSNotification::CATEGORY_TRANSACTION,
                "奶厂已打入您的自由账户",
                "奶厂已打入您的自由账户".$amount."元。");

            return response()->json(['status' => 'success']);
        }
    }

    /*MONEY TRANSFER TO OTHER STATIONS*/

    //G5-1: Show transactions status
    public function show_transaction_between_other_station_in_gongchang()
    {
        $factory_id = $this->getCurrentFactoryId(true);
        $factory = Factory::find($factory_id);

        $other_orders_not_checked = $factory->get_other_orders_not_checked_for_transaction();
        
        $oo_total_money = $factory->get_other_orders_money_total();
        $oo_checked_money = $factory->get_other_orders_checked_money_total();
        $oo_unchecked_money = $factory->get_other_orders_unchecked_money_total();

        $stations = DeliveryStation::where('factory_id', $factory_id)->get();

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'qitanaizhanzhuanzhang';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.caiwu.taizhang.qitanaizhanzhuanzhang', [
            'pages'                         => $pages,
            'child'                         => $child,
            'parent'                        => $parent,
            'current_page'                  => $current_page,
            'other_orders_total_money'      => $oo_total_money,
            'other_orders_checked_money'    => $oo_checked_money,
            'other_orders_unchecked_money'  => $oo_unchecked_money,
            'stations'                      => $stations,
            'other_orders_nc'               => $other_orders_not_checked,
            'is_station'                    => false
        ]);
    }

    //G5-1-1: Create transactions for money order to others, set transaction id
    public function create_transactions_for_money_order($start_date, $end_date, $factory_id)
    {
        $factory = Factory::find($factory_id);

        if(!$start_date && !$end_date)
        {
            $orders = $factory->get_other_orders_not_checked_for_transaction();

        } else if(!$start_date && $end_date) {

            $orders = Order::where('factory_id', $factory_id)
                ->whereRaw('station_id != delivery_station_id')
                ->where('ordered_at', '<=', $end_date)
                ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
                ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                ->where('transaction_id', null)
                ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
                ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)
                ->get();

        } else if($start_date && !$end_date)
        {
            $orders = Order::where('factory_id', $factory_id)
                ->where('ordered_at', '>=', $start_date)
                ->whereRaw('station_id != delivery_station_id')
                ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
                ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                ->where('transaction_id', null)
                ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
                ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)
                ->get();
        }
        else{

            $orders = Order::where('factory_id', $factory_id)
                ->where('ordered_at', '>=', $start_date)
                ->whereRaw('station_id != delivery_station_id')
                ->where('ordered_at', '<=', $end_date)
                ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
                ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                ->where('transaction_id', null)
                ->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS)
                ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)
                ->get();
        }

        if (count($orders) == 0) {
            return null;
        }

        $res = array();

        foreach ($orders as $order) {
            $res[$order->station_id][$order->delivery_station_id][] = $order;
        }

        $transactions = [];

        foreach ($res as $station_from => $orders1) {
            foreach ($orders1 as $station_to => $orders2) {
                $total_amount = 0;
                $order_count = count($orders2);

                //
                // 计算账单订单范围
                //
                $order_from = "";
                $order_to = "";

                foreach ($orders2 as $o) {
                    if (!$order_from) {
                        $order_from = $o->ordered_at;
                        $order_to = $o->ordered_at;
                    }
                    else {
                        $of_date = date($order_from);
                        $ot_date = date($order_to);
                        $ff_date = date($o->ordered_at);

                        // 决定最小值
                        if ($of_date > $ff_date) {
                            $order_from = $o->ordered_at;
                        }
                        // 决定最大值
                        else if ($ot_date < $ff_date) {
                            $order_to = $o->ordered_at;
                        }
                    }

                    $total_amount += $o->total_amount;
                }

                $t = new DSTransaction;

                $t->station_id = $station_from;
                $t->delivery_station_id = $station_to;
                $t->payment_type = PaymentType::PAYMENT_TYPE_MONEY_NORMAL;
                $t->total_amount = $total_amount;
                $t->order_from = date('Y-m-d', strtotime($order_from));
                $t->order_to = date('Y-m-d', strtotime($order_to));
                $t->order_count = $order_count;
                $t->status = DSTransaction::DSTRANSACTION_CREATED;

                $t->save();

                $transactions[] = $t;

                foreach ($orders2 as $o) {
                    $o->transaction_id = $t->id;
                    $o->save();
                }
            }
        }
        return;
    }

    //G5-1-2: Show transaction list after creating transaction
    public function show_transaction_creation_page_for_other_money_in_gongchang(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        if(!$start)
            $start= date('Y-m-01');
        if(!$end)
            $end= getCurDateString();

        $factory_id = $this->getCurrentFactoryId(true);

        $stations = DeliveryStation::where('factory_id', $factory_id)->get();

        //create transaction for orders during start and end date
        $start_date = $start ? date('Y-m-d', strtotime($start)) : date('Y-m-01');
        $end_date = $end ? date('Y-m-d', strtotime($end)) : getCurDateString();

        $this->create_transactions_for_money_order($start_date, $end_date, $factory_id);

        //show not checked list
        //show not checked list
        $stations = DeliveryStation::where('factory_id', $factory_id)->get();

        $not_checked_transactions = array();

        foreach ($stations as $station) {
            $station_id = $station->id;
            $temps = DSTransaction::where('station_id', $station_id)
                ->where('status', DSTransaction::DSTRANSACTION_CREATED)
                ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
                ->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end)
                ->get();

            foreach ($temps as $not_checked_transaction) {
                array_push($not_checked_transactions, $not_checked_transaction);
            }
        }

        $res = array();
        $station_name_list = [];

        foreach ($not_checked_transactions as $trs) {
            $res[$trs->station_id][$trs->delivery_station_id][] = $trs;
            $station_name = $trs->station_name;
            $station_id = $trs->station_id;
            if (!in_array($station_name, $station_name_list))
                $station_name_list[$station_id] = $station_name;
        }

        /*$today_date = new DateTime("now",new DateTimeZone('Asia/Shanghai')); $today =$today_date->format('Y-m-d');
        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'zhuanzhangzhangchan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.caiwu.zhuanzhangzhangchan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations' => $stations,
            'today' => $today,
            'ncts' => $res,
            'station_name_list' => $station_name_list,
        ]);*/
        return redirect()->route('show_other_transaction_list');

    }

    //G5-2: Show Transaciton list for money order that has not been checked
    public function show_transaction_list_not_checked_for_other_money_in_gongchang()
    {
        $factory_id = $this->getCurrentFactoryId(true);

        //Get TransactionPays during first month to today
        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        //show not checked list
        $stations = DeliveryStation::where('factory_id', $factory_id)->get();

        $not_checked_transactions = array();

        foreach ($stations as $station) {
            $station_id = $station->id;
            $temps = DSTransaction::where('station_id', $station_id)
                ->whereRaw('station_id != delivery_station_id')
                ->where('status', DSTransaction::DSTRANSACTION_CREATED)
                ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
                ->whereDate('created_at', '>=', $first_m)
                ->whereDate('created_at', '<=', $last_m)
                ->get();

            foreach ($temps as $not_checked_transaction) {
                array_push($not_checked_transactions, $not_checked_transaction);
            }
        }

        $res = array();
        $station_name_list = [];

        foreach ($not_checked_transactions as $trs) {
            $res[$trs->station_id][$trs->delivery_station_id][] = $trs;
            $station_name = $trs->station_name;
            $station_id = $trs->station_id;
            if (!in_array($station_name, $station_name_list))
                $station_name_list[$station_id] = $station_name;
        }

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'zhuanzhangzhangchan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.caiwu.taizhang.zhuanzhangzhangchan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations' => $stations,
            'today' => getCurDateString(),
            'ncts' => $res,
            'station_name_list' => $station_name_list
        ]);
    }

    //G5-2-1: get trans data to print and insert
    public function get_trans_data_for_other_station(Request $request)
    {
        if ($request->ajax()) {
            //request: array
            $tids = $request->input('tids');

            $trs_info = [];
            $done = [];

            foreach ($tids as $tid) {
                //get station_name, delivery_station_name, order_count, total_amount, pre_remain
                if (in_array($tid, $done)) {
                    continue;
                } else {

                    array_push($done, $tid);
                    $trs = DSTransaction::find($tid);

                    $same_trs_count = 1;
                    $total_amount = $trs->total_amount;

                    $same_trs_ids = [];
                    array_push($same_trs_ids, $tid);

                    foreach ($tids as $stid) {
                        if ($tid == $stid)
                            continue;
                        $strs = DSTransaction::find($stid);

                        if (($trs->station_id == $strs->station_id) && ($trs->delivery_station_id == $strs->delivery_station_id)) {

                            //add total_amount, count
                            array_push($done, $stid);

                            if (!in_array($stid, $same_trs_ids)) {
                                array_push($same_trs_ids, $stid);
                            }

                            $total_amount += $strs->total_amount;
                            $same_trs_count++;
                        }

                    }

                    $pre_remain = $trs->pre_remain;

                    $total = $total_amount + $pre_remain;
                    $sid = $trs->station_id;
                    $did = $trs->delivery_station_id;
                    $trs_info[] = [
                        $trs->station_name,
                        $trs->delivery_station_name,
                        $same_trs_count,
                        $total_amount,
                        $trs->pre_remain,
                        $total,
                        $same_trs_ids,
                        $sid,
                        $did
                    ];
                }

            }

            return response()->json(['status' => 'success', 'trs' => $trs_info]);
        }
    }

    //G5-2-2: Complete transaction
    public function complete_trans_for_other_station(Request $request)
    {
        if ($request->ajax()) {

            $count = count($request->input('station_id'));

            for ($i = 0; $i < $count; $i++) {

                //INPUT: station_id, delivery_station_id, trs_ids:"44,46", total_amount, real_input, trans_number, comment
                $station_id = $request->input('station_id')[$i];
                $delivery_station_id = $request->input('delivery_station_id')[$i];
                $total_amount = $request->input('total_amount')[$i];
                $real_amount = $request->input('real_input')[$i];
                $receipt_number = $request->input('trans_number')[$i];//Bank Number
                $comment = $request->input('comment')[$i];
                $trs_ids = $request->input('trs_ids')[$i];

                $trsids = explode(',', $trs_ids);

                //Step1: Create Transaction Pay
                $dstrpay = new DSTransactionPay;
                $dstrpay->receipt_number = $receipt_number;
                $dstrpay->amount = $real_amount;
                $dstrpay->paid_at = date("Y-m-d H:i");
                $dstrpay->comment = $comment;
                $dstrpay->payment_type = PaymentType::PAYMENT_TYPE_MONEY_NORMAL;
                $dstrpay->save();

                $transaction_pay_id = $dstrpay->id;

                //Step2: Add history to StationMoneyTransfer
                $stmoneytrans = new StationsMoneyTransfer;
                $stmoneytrans->station1_id = $station_id;
                $stmoneytrans->station2_id = $delivery_station_id;
                $stmoneytrans->transaction_pay_id = $transaction_pay_id;
                $stmoneytrans->amount = $real_amount;
                $stmoneytrans->remaining = $total_amount - $real_amount;
                $stmoneytrans->payment_type = PaymentType::PAYMENT_TYPE_MONEY_NORMAL;
                $stmoneytrans->save();

                // 添加转出历史记录
                $dscalc_history_for_station1 = new DSCalcBalanceHistory;
                $dscalc_history_for_station1->station_id = $station_id;
                $dscalc_history_for_station1->type = DSCalcBalanceHistory::DSCBH_IN_ORDER_OUT_OTHER;
                $dscalc_history_for_station1->amount = $real_amount;
                $dscalc_history_for_station1->receipt_number = $receipt_number;//id from bank
                $dscalc_history_for_station1->io_type = DSCalcBalanceHistory::DSCBH_TYPE_IN;
                $dscalc_history_for_station1->comment = $comment;
                $dscalc_history_for_station1->save();

                // 添加转入历史记录
                $dscalc_history_for_station2 = new DSCalcBalanceHistory;
                $dscalc_history_for_station2->station_id = $delivery_station_id;
                $dscalc_history_for_station2->type = DSCalcBalanceHistory::DSCBH_IN_ORDER_OTHER_STATION;
                $dscalc_history_for_station2->amount = $real_amount;
                $dscalc_history_for_station2->receipt_number = $receipt_number;//id from bank
                $dscalc_history_for_station2->io_type = DSCalcBalanceHistory::DSCBH_TYPE_IN;
                $dscalc_history_for_station2->comment = $comment;
                $dscalc_history_for_station2->save();

                //Descrease the Station Calculation Amount
                $station1 = DeliveryStation::find($station_id);
                $station1->calculation_balance -= $real_amount;
                $station1->save();

                //Increase the Station Calculation Amount
                $station2 = DeliveryStation::find($delivery_station_id);
                $station2->calculation_balance += $real_amount;
                $station2->save();

                //Transaction Status Change
                foreach ($trsids as $trsid) {
                    $transaction = DSTransaction::find($trsid);
                    $transaction->transaction_pay_id = $transaction_pay_id;
                    $transaction->status = DSTransaction::DSTRANSACTION_COMPLETED;
                    $transaction->save();
                    $transaction_id = $transaction->id;

                    $orders = Order::where('transaction_id', $transaction_id)->get();
                    foreach ($orders as $order) {
                        if ($order) {
                            $order->trans_check = Order::ORDER_TRANS_CHECK_TRUE;
                            $order->save();
                        }
                    }

                }
            }

            return response()->json(['status' => 'success']);
        }
    }

    //G5-3: Show transaction and pay history of transactions to other station
    public function show_money_transaction_record_to_others_in_gongchang()
    {
        //Get TransactionPays during first month to today
        $first_m = date('Y-m-01');
        $last_m = date('Y-m-d');

        $stmoneytransfers = StationsMoneyTransfer::where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->whereDate('created_at', '>=', $first_m)
            ->whereDate('created_at', '<=', $last_m)
            ->get();

        //get transactions
        $trs_count = 0;
        $result = array();

        foreach ($stmoneytransfers as $stm) {
            $trspay_id = $stm->transaction_pay_id;
            $trs = DSTransaction::where('transaction_pay_id', $trspay_id)->get();
            $trs_count += count($trs);//get all count of transactions

            //make array according to transaction pay id
            $result[$trspay_id][0] = $stm;
            $result[$trspay_id][1] = $trs;//save transaction list
        }

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'zhuanzhangjilu';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.caiwu.taizhang.zhuanzhangjilu', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'result' => $result,
            'is_station' => false
        ]);
    }

    //G5-4: Show transaction detail of transaction
    public function show_transaction_detail_to_others_in_gongchang($tid)
    {
        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'zhangdanmingxi';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        $trans = DSTransaction::find($tid);

        $orders = Order::where('transaction_id', $tid)->get();

        return view('gongchang.caiwu.taizhang.zhangdanmingxi', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'trans' => $trans,
            'orders' => $orders,
            'is_station' => false
        ]);
    }

    /*CARD TRANSACTION*/

    //G6: Card Transaction First Page
    public function show_orders_for_card_transaction_in_gongchang()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;
        $stations = DeliveryStation::where('factory_id', $factory_id)->get();
        $factory = Factory::find($factory_id);

        $card_orders_not_checked = $factory->get_card_orders_not_checked_for_transaction();
        $card_orders_total_money = $factory->get_card_orders_money_total();
        $card_orders_checked_money = $factory->get_card_orders_checked_money_total();
        $card_orders_unchecked_money = $factory->get_card_orders_unchecked_money_total();

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'naikakuanzhuanzhang';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        return view('gongchang.caiwu.taizhang.naikakuanzhuanzhang', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations' => $stations,
            'card_orders_not_checked' => $card_orders_not_checked,
            'card_orders_total_money' => $card_orders_total_money,
            'card_orders_checked_money' => $card_orders_checked_money,
            'card_orders_unchecked_money' => $card_orders_unchecked_money,
        ]);
    }

    //G6-1: Create transactions for money order to others, set transaction id
    public function create_transactions_for_card_order($start_date, $end_date, $factory_id)
    {
        $factory = Factory::find($factory_id);

        if(!$start_date && !$end_date)
        {
            $orders = $factory->get_card_orders_not_checked_for_transaction();

        } else if(!$start_date && $end_date) {

            $orders = Order::where('factory_id', $factory_id)
                ->where('ordered_at', '<=', $end_date)->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
                ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                ->where('transaction_id', null)->get();
        } else if($start_date && !$end_date)
        {
            $orders = Order::where('factory_id', $factory_id)->where('ordered_at', '>=', $start_date)
                ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
                ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                ->where('transaction_id', null)->get();
        }
        else{
//                $start_date = date('Y-m-01');
//                $end_date = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

            $orders = Order::where('factory_id', $factory_id)->where('ordered_at', '>=', $start_date)
                ->where('ordered_at', '<=', $end_date)->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
                ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                ->where('transaction_id', null)->get();
        }

        $this->makeTransaction($orders, PaymentType::PAYMENT_TYPE_CARD);
    }

    //G7: show Transaction list for card order that has not been checked
    public function show_transaction_list_not_checked_for_card_in_gongchang()
    {
        $factory_id = $this->getCurrentFactoryId(true);

        $stations = DeliveryStation::where('factory_id', $factory_id)->get();

        //show not checked list
        //Get TransactionPays during first month to today
        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        $not_checked_transactions = array();
        foreach ($stations as $station) {
            $station_id = $station->id;

            $temps = DSTransaction::where('station_id', $station_id)
                ->where('status', DSTransaction::DSTRANSACTION_CREATED)
                ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
                ->whereDate('created_at', '>=', $first_m)
                ->whereDate('created_at', '<=', $last_m)
                ->get();

            foreach ($temps as $not_checked_transaction) {
                array_push($not_checked_transactions, $not_checked_transaction);
            }
        }

        $res = array();
        $station_name_list = [];

        foreach ($not_checked_transactions as $trs) {
            $res[$trs->delivery_station_id][] = $trs;
            $delivery_station_name = $trs->delivery_station_name;
            $delivery_station_id = $trs->delivery_station_id;
            if (!in_array($delivery_station_name, $station_name_list))
                $station_name_list[$delivery_station_id] = $delivery_station_name;
        }

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'naikazhuanzhangzhangchan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.caiwu.taizhang.naikazhuanzhangzhangchan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations' => $stations,
            'ncts' => $res,
            'today' => getCurDateString(),
            'station_name_list' => $station_name_list,
        ]);
    }

    //G7-1: Show transaction creation page
    public function show_transaction_creation_page_for_card_in_gongchang(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        if(!$start)
            $start= date('Y-m-01');
        if(!$end)
            $end= (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;


        //create transaction for orders during start and end date
        $start_date = $start ? date('Y-m-d', strtotime($start)) : date('Y-m-01');
        $end_date = $end ? date('Y-m-d', strtotime($end)) : (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $this->create_transactions_for_card_order($start_date, $end_date, $factory_id);

        //show not checked list
        $not_checked_transactions = DSTransaction::where('status', DSTransaction::DSTRANSACTION_CREATED)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)->get();

        $res = array();
        $station_name_list = [];

        foreach ($not_checked_transactions as $trs) {
            $res[$trs->delivery_station_id][] = $trs;
            $delivery_station_name = $trs->delivery_station_name;
            $delivery_station_id = $trs->delivery_station_id;
            if (!in_array($delivery_station_name, $station_name_list))
                $station_name_list[$delivery_station_id] = $delivery_station_name;
        }

        $today = getCurDateString();

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'naikazhuanzhangzhangchan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.caiwu.taizhang.naikazhuanzhangzhangchan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'ncts' => $res,
            'today' => $today,
            'station_name_list' => $station_name_list,
        ]);
    }

    //G8: Show transaction list made
    public function show_card_transaction_list_in_gongchang()
    {
        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'naikazhuanzhangzhangchan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        return view('gongchang.caiwu.naikazhuanzhangzhangchan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page
        ]);
    }
    //G8-1: get trans data to print and insert for card order
    public function get_trans_data_for_card(Request $request)
    {
        if ($request->ajax()) {
            //request: array
            $tids = $request->input('tids');

            $trs_info = [];
            $done = [];

            foreach ($tids as $tid) {
                //get station_name, delivery_station_name, order_count, total_amount, pre_remain
                if (in_array($tid, $done)) {
                    continue;
                } else {

                    array_push($done, $tid);
                    $trs = DSTransaction::find($tid);

                    $same_trs_count = 1;
                    $total_amount = $trs->total_amount;

                    $same_trs_ids = [];
                    array_push($same_trs_ids, $tid);

                    foreach ($tids as $stid) {
                        if ($tid == $stid)
                            continue;
                        $strs = DSTransaction::find($stid);

                        if ($trs->delivery_station_id == $strs->delivery_station_id) {

                            //add total_amount, count
                            array_push($done, $stid);

                            if (!in_array($stid, $same_trs_ids)) {
                                array_push($same_trs_ids, $stid);
                            }

                            $total_amount += $strs->total_amount;
                            $same_trs_count++;
                        }

                    }

                    //Card Order has no pre remain

                    $did = $trs->delivery_station_id;
                    $trs_info[] = [$trs->delivery_station_name, $same_trs_count, $total_amount, $same_trs_ids, $did];
                }

            }

            return response()->json(['status' => 'success', 'trs' => $trs_info]);
        }
    }
    //G8-2: Complete transaction for Card Orders
    public function complete_trans_for_card(Request $request)
    {
        if ($request->ajax()) {
            //data: delivery_station_id, same_trs_ids, total_amount, comment

            $count = count($request->input('delivery_station_id'));

            for ($i = 0; $i < $count; $i++) {
                $delivery_station_id = $request->input('delivery_station_id')[$i];
                $total_amount = $request->input('total_amount')[$i];
                $trs_ids = $request->input('same_trs_ids')[$i];
                $comment = $request->input('comment')[$i];

                $trsids = explode(',', $trs_ids);

                //Step1: Create Transaction Pay
                $dstrpay = new DSTransactionPay;
                $dstrpay->amount = $total_amount;
                $dstrpay->paid_at = date("Y-m-d H:i");
                $dstrpay->comment = $comment;
                $dstrpay->payment_type = PaymentType::PAYMENT_TYPE_CARD;
                $dstrpay->save();

                //Transaction Pay Id
                $transaction_pay_id = $dstrpay->id;

                //Step2: Add Calc History to Station2 about Card In to Station2
                $dscalc_history_for_station2_in = new DSCalcBalanceHistory;
                $dscalc_history_for_station2_in->station_id = $delivery_station_id;
                $dscalc_history_for_station2_in->type = DSCalcBalanceHistory::DSCBH_IN_ORDER_CARD;
                $dscalc_history_for_station2_in->amount = $total_amount;
                $dscalc_history_for_station2_in->io_type = DSCalcBalanceHistory::DSCBH_TYPE_IN;
                $dscalc_history_for_station2_in->comment = $comment;
                $dscalc_history_for_station2_in->save();

                //At the same time, as the card money goes to factory, out history need

                $dscalc_history_for_station2_out = new DSCalcBalanceHistory;
                $dscalc_history_for_station2_out->station_id = $delivery_station_id;
                $dscalc_history_for_station2_out->type = DSCalcBalanceHistory::DSCBH_OUT_MILK_CARD_ORDER_TRANSFER_FACTORY;
                $dscalc_history_for_station2_out->amount = $total_amount;
                $dscalc_history_for_station2_out->io_type = DSCalcBalanceHistory::DSCBH_TYPE_OUT;
                $dscalc_history_for_station2_out->comment = $comment;
                $dscalc_history_for_station2_out->save();

//                //Step 3: Increase the Station2 Calculation Amount
//                $station2 = DeliveryStation::find($delivery_station_id);
//                $station2->calculation_balance += $total_amount;
//                $station2->save();

                //Transaction Status Change

                foreach ($trsids as $trsid) {
                    $transaction = DSTransaction::find($trsid);
                    $transaction->transaction_pay_id = $transaction_pay_id;
                    $transaction->status = DSTransaction::DSTRANSACTION_COMPLETED;
                    $transaction->save();
                    $transaction_id = $transaction->id;

                    $orders = Order::where('transaction_id', $transaction_id)->get();
                    foreach ($orders as $order) {
                        if ($order) {
                            $order->trans_check = Order::ORDER_TRANS_CHECK_TRUE;
                            $order->save();
                        }
                    }

                }
            }

            return response()->json(['status' => 'success']);
        }
    }
    //G9: Show transaction list checked
    public function show_card_transaction_record_in_gongchang()
    {
        //Get TransactionPays during first month to today
        $first_m = date('Y-m-01 H:i');
        $last_m = date('Y-m-d H:i');

        $transaction_pays = DSTransactionPay::where('paid_at', '>=', $first_m)
            ->where('paid_at', '<=', $last_m)->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)->get();

        //get transactions
        $trs_count = 0;
        $result = array();

        foreach ($transaction_pays as $trp) {

            $trspay_id = $trp->id;
            $trs = DSTransaction::where('transaction_pay_id', $trspay_id)->get();
            $trs_count += count($trs);//get all count of transactions
            $trs_one = $trs->first();
            $delivery_station_name = $trs_one->delivery_station_name;

            //make array according to transaction pay id
            $result[$trspay_id] [0] = $trp;
            $result[$trspay_id] [1] = $trs;//save transaction list
            $result[$trspay_id] [2] = $delivery_station_name;
        }

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'naikazhuanzhangjilu';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.caiwu.taizhang.naikazhuanzhangjilu', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'transaction_pays' => $transaction_pays,
            'result' => $result,
            'transaction_pays' => $transaction_pays,
        ]);
    }
    //G10: Show detail of transaction
    public function show_card_transaction_detail_in_gongchang($trs_id)
    {
        $transaction = DSTransaction::find($trs_id);

        $orders = Order::where('transaction_id', $trs_id)->get();

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'naikazhangdanmingxi';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.caiwu.taizhang.naikazhangdanmingxi', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'trans' => $transaction,
            'orders' => $orders,
        ]);
    }



    /*
     * NAIZHAN FINANCE
    */

    //N1: Naizhan First page
    public function show_finance_page_in_naizhan()
    {
        $station_id = $this->getCurrentStationId();
        $station = DeliveryStation::find($station_id);

        $stations[0] = $station;
        // 计算财务信息
        foreach ($stations as $s) {
            $this->getSummary($s);
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_STATION, '奶站台帐页面', SysLog::SYSLOG_OPERATION_VIEW);

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'taizhang';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.caiwu.taizhang', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations' => $stations,
            'is_station' => true
        ]);
    }

    //N2: Show Station's Order Money Status
    public function show_station_order_money_in_naizhan()
    {
        $station_id = $this->getCurrentStationId();

        $aryData = $this->getOrderMoneyStatistics($station_id);

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'benzhandingdan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        $aryPage = array(
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'is_station' => true
        );

        return view('naizhan.caiwu.taizhang.benzhandingdan', array_merge($aryData, $aryPage));
    }

    //N3: Show Station's Calc Balance Status
    public function show_station_calc_account_balance_in_naizhan()
    {
        $station_id = Auth::guard('naizhan')->user()->station_id;
        $station = DeliveryStation::find($station_id);

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'zhanghuyue';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        //show only current months history
        $calc_histories_out = $station->calc_histories_out;
        return view('naizhan.caiwu.taizhang.zhanghuyue', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'station' => $station,
            'calc_histories_out' => $calc_histories_out,
            'today' => getCurDateString(),
            'is_station' => true
        ]);
    }
    //N4: Show Self Business account balance
    public function show_self_account_in_naizhan()
    {
        $station = DeliveryStation::find($this->getCurrentStationId());
        $self_business_history = $station->self_business_history;

        $child = 'ziyingzhanghujiru';
        $parent = 'caiwu';
        $current_page = 'ziyingzhanghujiru';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.caiwu.ziyingzhanghujiru', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'station' => $station,
            'self_business_history' => $self_business_history,
            'is_station' => true
        ]);
    }
    //N5: Show transaction between other stations

    /*Money Order Transaction*/
    //N6: show transaction status between this and other stations
    public function show_transaction_between_other_station_in_naizhan()
    {
        $station = DeliveryStation::find($this->getCurrentStationId());
        $factory = $station->factory;

        $other_orders_not_checked = $factory->get_other_orders_not_checked_for_transaction();

        $oo_total_money = $factory->get_other_orders_money_total();
        $oo_checked_money = $factory->get_other_orders_checked_money_total();
        $oo_unchecked_money = $factory->get_other_orders_unchecked_money_total();

        $stations[0] = $station;

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'xianjinzhuanzhangjiru';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.caiwu.taizhang.qitanaizhanzhuanzhang.xianjinzhuanzhangjiru', [
            'pages'                         => $pages,
            'child'                         => $child,
            'parent'                        => $parent,
            'current_page'                  => $current_page,

            'other_orders_total_money'      => $oo_total_money,
            'other_orders_checked_money'    => $oo_checked_money,
            'other_orders_unchecked_money'  => $oo_unchecked_money,
            'stations'                      => $stations,
            'other_orders_nc'               => $other_orders_not_checked,

            'is_station'                    => true
        ]);
//
//        $fuser = Auth::guard('gongchang')->user();
//        $factory_id = $fuser->factory_id;
//
//
//        $stations = DeliveryStation::where('factory_id', $factory_id)->where('id', '!=', $station_id)->get();
//
//        $other_orders_not_checked = $station->get_other_orders_not_checked();
//        $oo_total_money = $station->get_other_orders_money_total();
//        $oo_checked_money = $station->get_other_orders_checked_money_total();
//        $oo_unchecked_money = $station->get_other_orders_unchecked_money_total();
//
//
//        return view('naizhan.caiwu.taizhang.qitanaizhanzhuanzhang.xianjinzhuanzhangjiru', [
//            'pages' => $pages,
//            'child' => $child,
//            'parent' => $parent,
//            'current_page' => $current_page,
//            'other_orders_total_money' => $oo_total_money,
//            'other_orders_checked_money' => $oo_checked_money,
//            'other_orders_unchecked_money' => $oo_unchecked_money,
//            'stations' => $stations,
//            'other_orders_nc' => $other_orders_not_checked,
//        ]);
    }
    //N7: Show transaction list not checked for other's money order
    public function show_transaction_list_not_checked_in_naizhan()
    {
        $station_id = $this->getCurrentStationId();

        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        //show not checked list
        $not_checked_transactions = DSTransaction::where('station_id', $station_id)
            ->whereRaw('station_id != delivery_station_id')
            ->where('status', DSTransaction::DSTRANSACTION_CREATED)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->whereDate('created_at', '>=', $first_m)
            ->whereDate('created_at', '<=', $last_m)
            ->get();

        $res = array();

        foreach ($not_checked_transactions as $trs) {
            $res[$trs->delivery_station_id][] = $trs;
        }

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'zhuanzhangzhangdan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.caiwu.taizhang.qitanaizhanzhuanzhang.zhuanzhangzhangdan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'ncts' => $res,
        ]);
    }
    //N8: Show Transaction record completed for other's money order
    public function show_transaction_record_completed_for_other_money_in_naizhan()
    {
        $station_id = $this->getCurrentStationId();

        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        //Get Station Money Transfers
        $stmoneytransfers = StationsMoneyTransfer::where('payment_type', PaymentType::PAYMENT_TYPE_MONEY_NORMAL)
            ->whereDate('created_at', '>=', $first_m)
            ->whereDate('created_at', '<=', $last_m)
            ->where(function($query) use($station_id) {
                $query->where('station1_id', $station_id);
                $query->orWhere('station2_id', $station_id);
            })
            ->get();

        //get transactions
        $result = array();
        foreach ($stmoneytransfers as $stm) {
            $trspay_id = $stm->transaction_pay_id;
            $trs = DSTransaction::where('transaction_pay_id', $trspay_id)->get();

            //make array according to transaction pay id
            $result[$trspay_id] [0] = $stm;
            $result[$trspay_id] [1] = $trs;//save transaction list
        }

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'zhuanzhangjiru';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.caiwu.taizhang.qitanaizhanzhuanzhang.zhuanzhangjiru', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'result' => $result,
            'is_station' => true
        ]);

    }
    //N9: Show detail of transactions to others for money order
    public function show_transaction_detail_to_others_in_naizhan($tid)
    {
        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'zhangdanmingxi';
        $pages = Page::where('backend_type', '3')->where('parent_page', '0')->get();

        $trans = DSTransaction::find($tid);

        $orders = Order::where('transaction_id', $tid)->get();

        return view('naizhan.caiwu.taizhang.qitanaizhanzhuanzhang.zhangdanmingxi', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'trans' => $trans,
            'orders' => $orders,
            'is_station' => true
        ]);
    }

    /*Card Order*/
    //N10: Card Transaction First Page
    public function show_orders_for_card_transaction_in_naizhan()
    {
        $station_id = Auth::guard('naizhan')->user()->station_id;
        $station = DeliveryStation::find($station_id);

        //$card_orders_not_checked = $station->get_card_orders_not_checked();
        $card_orders_not_checked = $station->get_card_orders_not_checked_for_transaction();
        $co_total_money = $station->get_card_orders_money_total();
        $co_checked_money = $station->get_card_orders_checked_money_total();
        $co_unchecked_money = $station->get_card_orders_unchecked_money_total();

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'dingdanjiru';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();
        return view('naizhan.caiwu.taizhang.naikakuanzhuanzhang.dingdanjiru', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'card_orders_total_money' => $co_total_money,
            'card_orders_checked_money' => $co_checked_money,
            'card_orders_unchecked_money' => $co_unchecked_money,
            'card_orders_not_checked' => $card_orders_not_checked,
        ]);

    }
    //N11: Show transaction list not checked
    public function show_transaction_list_not_checked_for_card_in_naizhan()
    {
        $station_id = $this->getCurrentStationId();

        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        //show not checked list
        $not_checked_transactions = DSTransaction::where('station_id', $station_id)
            ->where('status', DSTransaction::DSTRANSACTION_CREATED)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->whereDate('created_at', '>=', $first_m)
            ->whereDate('created_at', '<=', $last_m)
            ->get();

        $res = array();

        foreach ($not_checked_transactions as $trs) {
            $res[$trs->delivery_station_id][] = $trs;
        }

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'zhuanzhangzhangdan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.caiwu.taizhang.naikakuanzhuanzhang.zhuanzhangzhangdan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'ncts' => $res,
        ]);
    }
    //N12: Show transaction list checked
    public function show_card_transaction_record_in_naizhan()
    {
        $station_id = Auth::guard('naizhan')->user()->station_id;

        $first_m = date('Y-m-01');
        $last_m = getCurDateString();

        $transactions = DSTransaction::where('payment_type', PaymentType::PAYMENT_TYPE_CARD)
            ->whereDate('created_at', '>=', $first_m)
            ->whereDate('created_at', '<=', $last_m)
            ->where('status', DSTransaction::DSTRANSACTION_COMPLETED)->where('delivery_station_id', $station_id)
            ->get();

        $result = [];
        foreach ($transactions as $trs) {
            $trs_pay_id = $trs->transaction_pay_id;
            $trs_pay = DSTransactionPay::find($trs_pay_id);
            $result[$trs_pay_id][0] = $trs_pay;
            $result[$trs_pay_id][1][] = $trs;
        }

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'zhuanzhangjiru';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.caiwu.taizhang.naikakuanzhuanzhang.zhuanzhangjiru', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'result' => $result,
        ]);
    }
    //N13: Show detail of card transaction
    public function show_card_transaction_detail_in_naizhan($trs_id)
    {
        $station_id = Auth::guard('naizhan')->user()->station_id;
        $station = DeliveryStation::find($station_id);
        $factory_id = $station->factory_id;
        $factory = Factory::find($factory_id);

        $transaction = DSTransaction::find($trs_id);
        $orders = Order::where('transaction_id', $trs_id)->where('payment_type', PaymentType::PAYMENT_TYPE_CARD)->get();

        $child = 'taizhang';
        $parent = 'caiwu';
        $current_page = 'zhangdanmingxi';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.caiwu.taizhang.naikakuanzhuanzhang.zhangdanmingxi', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,

            'trans' => $transaction,
            'orders' => $orders,
        ]);
    }


    /*ZONGPINGTAI FINANCE*/
    //Z1: ZONPINGTAI CAIWU First PAGE
    public function show_wechat_orders()
    {
        $zuser = Auth::guard('zongpingtai')->user();

//        $first_m = date('Y-m-01');
//        $last_m = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $factories = Factory::where('status', Factory::FACTORY_STATUS_ACTIVE)->where('is_deleted', 0)->get();

        $orders = Order::where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
            ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where(function($query) {
                $query->where('status', '!=', Order::ORDER_NEW_WAITING_STATUS);
                $query->orWhere('status', '!=', Order::ORDER_WAITING_STATUS);
            })
            ->where('status', '!=', Order::ORDER_CANCELLED_STATUS)
            ->orderBy('created_at')
            ->get();

        $child = 'zhangwujiesuan';
        $parent = 'caiwu';
        $current_page = 'zhangwu';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.caiwu.zhangwujiesuan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'factories' => $factories,
            'wechat_orders' => $orders,
        ]);
    }

    //Z1-1: Create Wechat Transaction
    public function create_transaction_for_wechat_deprecated(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');
        
        if(!$start)
            $start= date('Y-m-01');
        if(!$end)
            $end= (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $start_date = $start ? date('Y-m-d', strtotime($start)) : date('Y-m-01');
        $end_date = $end ? date('Y-m-d', strtotime($end)) : (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $factory_id = $request->input('factory_id');
        $station_id = $request->input('station_id');

        if ( $factory_id && ($station_id == "none"))
        {
            if(!$start && !$end)
            {
                $orders = Order::where('factory_id', $factory_id)
                    ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                    ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                    ->where('transaction_id', null)->get();

            } else if(!$start && $end)
            {
                $orders = Order::where('ordered_at', '<=', $end_date)
                    ->where('factory_id', $factory_id)
                    ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                    ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                    ->where('transaction_id', null)->get();

            } else if($start && !$end){

                $orders = Order::where('ordered_at', '>=', $start_date)
                    ->where('factory_id', $factory_id)
                    ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                    ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                    ->where('transaction_id', null)->get();
            }
            else {
                $orders = Order::where('ordered_at', '>=', $start_date)->where('ordered_at', '<=', $end_date)
                    ->where('factory_id', $factory_id)
                    ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                    ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                    ->where('transaction_id', null)->get();
            }
        }
        elseif ($factory_id && ($station_id != "none"))
        {
            if(!start && !$end)
            {
                $orders = Order::where('factory_id', $factory_id)
                    ->where('station_id', $station_id)
                    ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                    ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                    ->where('transaction_id', null)->get();

            } else if(!$start && $end)
            {
                $orders = Order::where('ordered_at', '<=', $end_date)
                    ->where('factory_id', $factory_id)
                    ->where('station_id', $station_id)
                    ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                    ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                    ->where('transaction_id', null)->get();

            }
            else if($start && !$end){
                $orders = Order::where('ordered_at', '>=', $start_date)
                    ->where('factory_id', $factory_id)
                    ->where('station_id', $station_id)
                    ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                    ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                    ->where('transaction_id', null)->get();
            }
            else {
                $orders = Order::where('ordered_at', '>=', $start_date)->where('ordered_at', '<=', $end_date)
                    ->where('factory_id', $factory_id)
                    ->where('station_id', $station_id)
                    ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                    ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
                    ->where('transaction_id', null)->get();

            }
        }
        else {
            return redirect()->route('show_wechat_orders');
        }

        if (count($orders) == 0) {
            return redirect()->route('show_wechat_orders');
        }

        $res = array();

        foreach ($orders as $order) {
            $res[$order->delivery_station_id][] = $order;
        }

        foreach ($res as $station_to => $orders1) {

            $total_amount = 0;
            $order_from = "";
            $order_to = "";

            $order_count = count($orders1);

            foreach ($orders1 as $o) {
                if (!$order_from)
                    $order_from = $o->start_at;
                else {
                    $of_date = date($order_from);
                    $ff_date = date($o->start_at);
                    if ($of_date > $ff_date)
                        $order_from = $o->start_at;
                }

                if (!$order_to)
                    $order_to = $o->order_end_date;
                else {
                    $of_date = date($order_to);
                    $ff_date = date($o->order_end_date);
                    if ($of_date < $ff_date)
                        $order_to = $o->order_end_date;
                }
                $total_amount += $o->total_amount;
            }

            $t = new DSTransaction;
            $t->station_id = $station_to;
            $t->delivery_station_id = $station_to;

            $t->payment_type = PaymentType::PAYMENT_TYPE_WECHAT;
            $t->total_amount = $total_amount;
            $t->order_from = date('Y-m-d', strtotime($order_from));
            $t->order_to = date('Y-m-d', strtotime($order_to));
            $t->order_count = $order_count;
            $t->status = DSTransaction::DSTRANSACTION_CREATED;

            $t->save();

            foreach ($orders1 as $o) {
                $o->transaction_id = $t->id;
                $o->save();
            }
        }

        return response()->json(['status'=>'success', 'factory_id'=>$factory_id]);
    }

    /**
     * 生成账单
     * @param $orders
     * @param $type
     */
    private function makeTransaction($orders, $type) {
        if (count($orders) == 0) {
            return;
        }

        $res = array();

        foreach ($orders as $order) {
            $res[$order->delivery_station_id][] = $order;
        }

        foreach ($res as $station_to => $orders1) {
            $total_amount = 0;
            $order_count = count($orders1);

            //
            // 计算账单订单范围
            //
            $order_from = "";
            $order_to = "";

            foreach ($orders1 as $o) {
                if (!$order_from) {
                    $order_from = $o->ordered_at;
                    $order_to = $o->ordered_at;
                }
                else {
                    $of_date = date($order_from);
                    $ot_date = date($order_to);
                    $ff_date = date($o->ordered_at);

                    // 决定最小值
                    if ($of_date > $ff_date) {
                        $order_from = $o->ordered_at;
                    }
                    // 决定最大值
                    else if ($ot_date < $ff_date) {
                        $order_to = $o->ordered_at;
                    }
                }

                $total_amount += $o->total_amount;
            }

            $t = new DSTransaction;
            $t->station_id = $station_to;
            $t->delivery_station_id = $station_to;

            $t->payment_type = $type;
            $t->total_amount = $total_amount;
            $t->order_from = date('Y-m-d', strtotime($order_from));
            $t->order_to = date('Y-m-d', strtotime($order_to));
            $t->order_count = $order_count;
            $t->status = DSTransaction::DSTRANSACTION_CREATED;

            $t->save();

            foreach ($orders1 as $o) {
                $o->transaction_id = $t->id;
                $o->save();
            }
        }
    }

    /**
     * 生成微信订单账单
     * @param $start_date
     * @param $end_date
     * @param $factory_id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function create_transaction_for_wechat($start_date, $end_date, $factory_id)
    {
        if ($factory_id <= 0) {
            return;
        }

        // 查询未生成账单的订单信息
        $queryOrder = Order::where('factory_id', $factory_id)
            ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
            ->where('trans_check', Order::ORDER_TRANS_CHECK_FALSE)
            ->where('transaction_id', null);

        if(!$start_date && $end_date) {
            $queryOrder->where('ordered_at', '<=', $end_date);
        }
        else if(!$start_date && $end_date) {
            $queryOrder->where('ordered_at', '>=', $start_date);
        }
        else if($start_date && $end_date) {
            $queryOrder->where('ordered_at', '>=', $start_date)->where('ordered_at', '<=', $end_date);
        }

        $this->makeTransaction($queryOrder->get(), PaymentType::PAYMENT_TYPE_WECHAT);

        return;
    }

    /**
     * 打开生成账单页面
     * @param Request $request
     */
    public function show_transaction_creation_page_for_wechat(Request $request) {

        // 获取日期范围
        $start = $request->input('start');
        $end = $request->input('end');

        if(!$start)
            $start= date('Y-m-01');
        if(!$end)
            $end= getCurDateString();

        $start_date = $start ? date('Y-m-d', strtotime($start)) : date('Y-m-01');
        $end_date = $end ? date('Y-m-d', strtotime($end)) : getCurDateString();

        $factory_id = $request->input('factory_id');

        $this->create_transaction_for_wechat($start_date, $end_date, $factory_id);

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_ADMIN, '财务管理', SysLog::SYSLOG_OPERATION_FINANCE);

        return $this->show_wechat_transaction_list_not_checked_in_zongpingtai($factory_id);
    }

    //Z2: Show transaction list not checked
    public function show_wechat_transaction_list_not_checked_in_zongpingtai($factory_id)
    {
        if($factory_id == "null")
            return redirect()->route('show_wechat_orders');

        //shwo transactions according to delivery_station_id
//        $start_date = date('Y-m-01');
//        $end_date = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

        $factory = Factory::find($factory_id);

        $stations = $factory->deliveryStations;

        if(!$stations || count($stations) == 0)
        {
            $today = getCurDateString();

            $child = 'zhangwujiesuan';
            $parent = 'caiwu';
            $current_page = 'zhangdanzhuanzhang';
            $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();

            return view('zongpingtai.caiwu.zhangwujiesuan.zhangdanzhuanzhang', [
                'pages' => $pages,
                'child' => $child,
                'parent' => $parent,
                'current_page' => $current_page,
                'today'=>$today,
            ]);
        }

        $not_checked_transactions = [];

        foreach($stations as $station)
        {
            $station_id = $station->id;
            $transactions = DSTransaction::where('status', DSTransaction::DSTRANSACTION_CREATED)
                ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                ->where('delivery_station_id', $station_id)
                ->get();

            foreach($transactions as $transaction)
            {
                if($transaction)
                    $not_checked_transactions[] =$transaction;
            }
        }

        $ncts= [];
        $station_name_list = [];
        foreach($not_checked_transactions as $nct) {

            $ncts[$nct->delivery_station_id][]=$nct;

            $delivery_station_id = $nct->delivery_station_id;
            $station_name = DeliveryStation::find($delivery_station_id)->name;
            if (!array_key_exists($delivery_station_id, $station_name_list))
            {
                $station_name_list[$delivery_station_id] = $station_name;
            }
        }

        $child = 'zhangwujiesuan';
        $parent = 'caiwu';
        $current_page = 'zhangdanzhuanzhang';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();

        return view('zongpingtai.caiwu.zhangwujiesuan.zhangdanzhuanzhang', [
            'pages'             => $pages,
            'child'             => $child,
            'parent'            => $parent,
            'current_page'      => $current_page,
            'ncts'              =>$ncts,
            'station_name_list' =>$station_name_list,
            'today'             =>getCurDateString(),
        ]);

    }
    //Z2-1: Get Transaction data to show on Insert Modal
    public function get_trans_data_for_wechat(Request $request)
    {
        if ($request->ajax()) {
            //request: array
            $tids = $request->input('tids');

            $trs_info = [];
            $done = [];

            foreach ($tids as $tid) {
                //get delivery_station_name, transaction_total_amount, pre_remain_wechat, total
                if (in_array($tid, $done)) {
                    continue;
                } else {

                    array_push($done, $tid);
                    $trs = DSTransaction::find($tid);

                    $total_amount = $trs->total_amount;

                    $same_trs_ids = [];
                    array_push($same_trs_ids, $tid);

                    foreach ($tids as $stid) {
                        if ($tid == $stid)
                            continue;
                        $strs = DSTransaction::find($stid);

                        if (($trs->station_id == $strs->station_id) && ($trs->delivery_station_id == $strs->delivery_station_id)) {

                            //add total_amount, count
                            array_push($done, $stid);

                            if (!in_array($stid, $same_trs_ids)) {
                                array_push($same_trs_ids, $stid);
                            }

                            $total_amount += $strs->total_amount;
                        }

                    }

                    $pre_remain = $trs->pre_remain_wechat;

                    $total = $total_amount + $pre_remain;
                    $did = $trs->delivery_station_id;
                    $trs_info[] = [$trs->delivery_station_name, $total_amount, $pre_remain, $total, $same_trs_ids, $did];
                }

            }

            return response()->json(['status' => 'success', 'trs' => $trs_info]);
        }
    }
    //Z2-2: Complete transaction
    public function complete_trans_for_wechat(Request $request)
    {
        if ($request->ajax()) {

            $nFactoryId = 0;
            $count = count($request->input('delivery_station_id'));

            for ($i = 0; $i < $count; $i++) {

                //INPUT: delivery_station_id, trs_ids:"44,46", total_amount, real_input, trans_number, comment
                $delivery_station_id = $request->input('delivery_station_id')[$i];
                $total_amount = $request->input('total_amount')[$i];
                $real_amount = $request->input('real_input')[$i];
                $receipt_number = $request->input('trans_number')[$i];//Bank Number
                $comment = $request->input('comment')[$i];
                $trs_ids = $request->input('trs_ids')[$i];

                $trsids = explode(',', $trs_ids);

                //Step1: Create Transaction Pay
                $dstrpay = new DSTransactionPay;
                $dstrpay->receipt_number = $receipt_number;
                $dstrpay->amount = $real_amount;
                $dstrpay->paid_at = date("Y-m-d H:i");
                $dstrpay->comment = $comment;
                $dstrpay->payment_type = PaymentType::PAYMENT_TYPE_WECHAT;
                $dstrpay->save();

                $transaction_pay_id = $dstrpay->id;

                //Step2: Add history to StationMoneyTransfer
                $stmoneytrans = new StationsMoneyTransfer;
                $stmoneytrans->station1_id = null;
                $stmoneytrans->station2_id = $delivery_station_id;
                $stmoneytrans->transaction_pay_id = $transaction_pay_id;
                $stmoneytrans->amount = $real_amount;
                $stmoneytrans->remaining = $total_amount - $real_amount;
                $stmoneytrans->payment_type = PaymentType::PAYMENT_TYPE_WECHAT;
                $stmoneytrans->save();

                //Step3: Add Calc History to Station1 about Money Out to Station2
                $dscalc_history_for_station1 = new DSCalcBalanceHistory;
                $dscalc_history_for_station1->station_id = $delivery_station_id;
                $dscalc_history_for_station1->type = DSCalcBalanceHistory::DSCBH_IN_ORDER_WECHAT;
                $dscalc_history_for_station1->amount = $real_amount;
                $dscalc_history_for_station1->receipt_number = $receipt_number;//id from bank
                $dscalc_history_for_station1->io_type = DSCalcBalanceHistory::DSCBH_TYPE_IN;
                $dscalc_history_for_station1->comment = $comment;
                $dscalc_history_for_station1->save();

                //Increase the Station Calculation Amount
                $station2 = DeliveryStation::find($delivery_station_id);
                $station2->calculation_balance += $real_amount;
                $station2->save();

                $nFactoryId = $station2->factory_id;

                //Transaction Status Change
                foreach ($trsids as $trsid) {
                    $transaction = DSTransaction::find($trsid);
                    $transaction->transaction_pay_id = $transaction_pay_id;
                    $transaction->status = DSTransaction::DSTRANSACTION_COMPLETED;
                    $transaction->save();

                    $orders = Order::where('transaction_id', $trsid)->get();
                    foreach ($orders as $order) {
                        if ($order) {
                            $order->trans_check = Order::ORDER_TRANS_CHECK_TRUE;
                            $order->save();
                        }
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'factory' => $nFactoryId
            ]);
        }
    }
    //Z3: Show transaction list checked
    public function show_wechat_transaction_list_checked_in_zongpingtai($factory_id)
    {
        if($factory_id == "null")
            return redirect()->route('show_wechat_orders');

        $zuser = Auth::guard('zongpingtai')->user();

        $factories = Factory::where('status', Factory::FACTORY_STATUS_ACTIVE)->get();

        $factory = Factory::find($factory_id);
        $stations = $factory->deliveryStations;

        if(!$stations || count($stations) == 0)
        {
            $child = 'zhangwujiesuan';
            $parent = 'caiwu';
            $current_page = 'lishizhuanzhangjiru';
            $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();
            return view('zongpingtai.caiwu.zhangwujiesuan.lishizhuanzhangjiru', [
                'pages' => $pages,
                'child' => $child,
                'parent' => $parent,
                'current_page' => $current_page,
                'factories'=>$factories,
            ]);
        }

        $checked_transactions = [];

        foreach($stations as $station)
        {
            $station_id = $station->id;
            $transactions = DSTransaction::where('status', DSTransaction::DSTRANSACTION_COMPLETED)
                ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                ->where('delivery_station_id', $station_id)
                ->get();
            foreach($transactions as $transaction)
            {
                if($transaction)
                    $checked_transactions[] =$transaction;
            }
        }

        $result = array();

        // get all transaction pay and station money transfers
        foreach($checked_transactions as $ct)
        {
            $tid = $ct->transaction_pay_id;
            $result[$tid][0][] = $ct;
            $result[$tid][1] = StationsMoneyTransfer::where('transaction_pay_id', $tid)->get()->first();
            $result[$tid][2] = DSTransactionPay::find($tid);
        }

        $child = 'zhangwujiesuan';
        $parent = 'caiwu';
        $current_page = 'lishizhuanzhangjiru';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();

        return view('zongpingtai.caiwu.zhangwujiesuan.lishizhuanzhangjiru', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'factory'=>$factory,
            'factories'=>$factories,
            'result'=>$result,
        ]);
    }
    //Z4: Show detail of transaction
    public function show_wechat_transaction_detail_in_zongpingtai($trs_id)
    {
        $zuser = Auth::guard('zongpingtai')->user();

        $trans = DSTransaction::find($trs_id);

        $orders = Order::where('transaction_id', $trs_id)->get();

        $child = 'zhangwujiesuan';
        $parent = 'caiwu';
        $current_page = 'zhangdanmingxi';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.caiwu.zhangwujiesuan.zhangdanmingxi', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'trans' => $trans,
            'orders' => $orders,
        ]);
    }
    //Z5: Show all factories and stations
    public function show_factories_stations()
    {
        $zuser = Auth::guard('zongpingtai')->user();

        $result = array();

        $factories = Factory::where('status', Factory::FACTORY_STATUS_ACTIVE)->where('is_deleted', 0)->get();
        foreach($factories as $factory)
        {
            $fid = $factory->id;
            $fimage = $factory->logo_url;
            $fname =$factory->name;

            $result[$fid][0] = $fid;
            $result[$fid][1] = $fname;
            $result[$fid][2] = $fimage;

            $stations = $factory->deliveryStations;
            foreach($stations as $station)
            {
                $sd = [$station->id, $station->name, $station->billing_account_name, $station->billing_account_card_no];
                $result[$fid][3][] = $sd;
            }

        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_ADMIN, '财务管理', SysLog::SYSLOG_OPERATION_VIEW);

        $child = 'zhanghuguanli';
        $parent = 'caiwu';
        $current_page = 'zhanghuguanli';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.caiwu.zhanghuguanli', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'fac_sta'=>$result,
        ]);
    }

    //Z6: Show one factory
    public function show_one_factory($fid){

        $zuser = Auth::guard('zongpingtai')->user();

        $factory = Factory::find($fid);

        $yesterday = date('Y-m-d', strtotime('yesterday'));
        $yesterday_orders =Order::where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
            ->where('ordered_at', $yesterday)
            ->where('factory_id', $fid)
            ->get();

        $yo_count = count($yesterday_orders);

        $total_orders_received_yesterday = $this->getSumOfOrders($yesterday_orders);

        $transferable_amount =0;

        $not_checked_orders =Order::where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
            ->where('factory_id', $fid)
            ->where('trans_check', 0)
            ->get();

        $transferable_amount = $this->getSumOfOrders($not_checked_orders);
        

        $factory_info=[$factory->logo_url, $factory->name, $yo_count, $total_orders_received_yesterday, $transferable_amount];

        $stations = $factory->deliveryStations;

        $today_total_wechat_count = 0;
        $today_total_wechat_amount = 0;

        foreach($stations as $station)
        {
            $today_total_wechat_count += $station->today_wechat_order_count;
            $today_total_wechat_amount += $station->today_wechat_order_amount;
        }

        $child = 'zhanghuguanli';
        $parent = 'caiwu';
        $current_page = 'zhanghugaikuang';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();

        return view('zongpingtai.caiwu.zhanghuguanli.zhanghugaikuang', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,

            'factory_info' => $factory_info,
            'stations' => $stations,
            'today_total_wechat_count'=>$today_total_wechat_count,
            'today_total_wechat_amount'=>$today_total_wechat_amount,
            'factory_id'=>$fid,
        ]);
    }
    //Z7: Show all transactions for one factory
    public function show_all_transactions_one_factory($fid)
    {
        $zuser = Auth::guard('zongpingtai')->user();

        $factory = Factory::find($fid);

        $stations = $factory->deliveryStations;
        $station_name_list  = [];
        $checked_transactions = [];
        foreach($stations as $station)
        {
            $station_name_list[] = [$station->id, $station->name];

            $station_id = $station->id;
            $transactions = DSTransaction::where('status', DSTransaction::DSTRANSACTION_COMPLETED)
                ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                ->where('delivery_station_id', $station_id)
                ->get();
            foreach($transactions as $transaction)
            {
                if($transaction)
                    $checked_transactions[] =$transaction;
            }
        }

        $trsps = [];
        foreach($checked_transactions as $ct)
        {
            $trp_id = $ct->transaction_pay_id;
            $trsps [$trp_id]  = DSTransactionPay::find($trp_id);
        }

        $child = 'zhanghuguanli';
        $parent = 'caiwu';
        $current_page = 'zhanghujiru';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.caiwu.zhanghuguanli.zhanghujiru', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'station_name_list' => $station_name_list,
            'trsps'=>$trsps,
        ]);
    }

    public function factory_to_station(Request $request)
    {
        if ($request->ajax()) {
            $fid = $request->input('factory_id');
            $factory = Factory::find($fid);
            if (!$factory) {
                return response()->json(['status' => 'fail', 'message' => '未找到工厂']);
            }
            $stations = $factory->deliveryStations;

            $result = [];
            foreach ($stations as $station) {
                $result[] = array($station->id, $station->name);
            }

            return response()->json(['status' => 'success', 'stations' => $result]);
        }
    }

}