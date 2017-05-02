<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 16/12/5
 * Time: PM2:31
 */

namespace App\Http\Controllers;
namespace App\Http\Controllers;
use App\Model\BasicModel\Address;
use App\Model\BasicModel\PaymentType;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\MilkmanBottleRefund;
use App\Model\DeliveryModel\MilkManDeliveryPlan;
use App\Model\FactoryModel\Factory;
use App\Model\OrderModel\Order;
use App\Model\OrderModel\OrderProduct;
use App\Model\OrderModel\OrderProperty;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Model\UserModel\Page;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Excel;


class ImportCtrl extends Controller
{
    private $mnFactoryId = 7;
    private $mOrders = array();

    /**
     * 显示导入页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showImport(Request $request) {
        $child = 'shujuku';
        $parent = 'xitong';
        $current_page = 'import';
        $pages = Page::where('backend_type', '1')->where('parent_page', '0')->get();


        return view('zongpingtai.xitong.import', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
        ]);
    }

    /**
     * 处理上传的文件
     * @param Request $request
     * @return string
     */
    public function uploadFile(Request $request) {

        if ($request->hasFile('upload')){

            $file = $request->file('upload');

            Excel::load($file, function ($reader) {

                echo "Started import ...<br>";

                $nSheetCount = $reader->getSheetCount();

                // 遍历sheet
                for ($i = 0; $i < $nSheetCount; $i++) {
                    $sheet = $reader->getSheet($i);

                    // 通过column数量判断订单还是配送明细
                    $nColCount = \PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());

                    // 订单
                    if ($nColCount > 20) {
                        // 校验数据
                        echo "verifying order data ...<br>";
                        if ($this->importOrder($sheet)) {
                            // 导入
                            echo "importing order data ... <br>";
                            $this->importOrder($sheet, false);
                        }
                        else {
                            // 失败就终止
                            break;
                        }
                    }
                    // 配送明细
                    else {
                        // 校验数据
                        echo "verifying delivery data ...<br>";
                        if ($this->importDeliveryPlan($sheet)) {
                            // 导入
                            echo "importing delivery data ... <br>";
                            $this->importDeliveryPlan($sheet, false);
                        }
                    }
                }
            });
        }

        return "<br><br><br>Import Done";
    }

    /**
     * 导入订单信息
     * @param $sheet
     * @param $checkData boolean 是否只校验数据
     * @return boolean
     */
    private function importOrder($sheet, $checkData = true) {

        $bResult = true;

        $aryData = $sheet->toArray();

        //
        // 加载基础信息
        //
        $factory = Factory::find($this->mnFactoryId);

        $orderCtrl = new OrderCtrl();

        $orderProperties = OrderProperty::all();
        $products = $factory->active_products;
        $orderTypes = $factory->factory_order_types;
        $deliveryTypes = $factory->order_delivery_types;
        $stations = $factory->active_stations;

        // 加载地址
        $villages = Address::where('factory_id', $this->mnFactoryId)->where('level', 5)->get();

        for ($i = 1; $i < count($aryData); $i++) {
            $row = $aryData[$i];

            // 显示log
            if ($checkData) {
                echo '---------- Order ------------<br>';
                echo implode(', ', $row);
                echo '<br>';
            }

            // 序号
            $nSeq = $row[0];

            // 收货人
            $strName = $row[1];

            // 电话
            $strPhone = strval($row[2]);

            // 订单性质
            $strProperty = $row[3];

            // 商品名
            $strProduct = $row[4];

            // 起送日期
            $strDateStart = $row[5];

            // 订购数量
            $nCount = intval($row[6]);

            // 订单类型
            $strType = $row[7];

            // 配送规则
            $strDeliveryType = $row[8];

            // 销售价
            $dPrice = doubleval($row[9]);

            // 地址
            $strProvince = $row[12];
            $strCity = $row[13];
            $strDistrict = $row[14];
            $strStreet = $row[15];
            $strAddress = $row[16];

            // 配送站
            $strStation = $row[17];

            // 录入时间
            $strDateInput = substr($row[18], 0, strlen("yyyy-mm-dd"));

            // 票据号
            $strReceipt = $row[20];

            // 备注
            $strComment = $row[23];

            // 征订人
            $strChecker = strval($row[25]);

            //
            // 设置地址
            //
            $village = null;
            $strSubAddr = "";

            foreach ($villages as $v) {
                if (!strncmp($strAddress, $v->name, strlen($v->name))) {
                    $village = $v;
                    $strSubAddr = substr($strAddress, strlen($v->name));

                    break;
                }
            }

            // 找不到小区信息, 失败
            if (!$village) {
                echo '找不到小区信息: ' . $strSubAddr;
                $bResult = false;
                break;
            }

            //
            // 配送员
            //
            $strFullVillageAddr = $village->getFullName();
            $strFullAddr = $strFullVillageAddr . ' ' . $strSubAddr;

            $station = null;
            $milkman = $orderCtrl->get_station_milkman_with_address_from_factory($this->mnFactoryId, $strFullVillageAddr, $station);

            if ($milkman == OrderCtrl::NOT_EXIST_DELIVERY_AREA) {
                echo '该地区没有覆盖可配送的范围: ' . $strFullVillageAddr;
                $bResult = false;
                break;
            }
            else if ($milkman == OrderCtrl::NOT_EXIST_STATION) {
                echo '没有奶站: ' . $strFullVillageAddr;
                $bResult = false;
                break;
            }
            else if ($milkman == OrderCtrl::NOT_EXIST_MILKMAN) {
                echo '奶站没有配送员.';
                $bResult = false;
                break;
            }

            //
            // 设置客户信息
            //
            $customer = $orderCtrl->getCustomer($strPhone, $strFullAddr, $this->mnFactoryId);

            foreach ($milkman as $delivery_station_id => $milkman_id) {
                $customer->station_id = $delivery_station_id;
                $customer->milkman_id = $milkman_id;
            }

            $customer->name = $strName;

            // 新建的客户信息需要保存
            if (empty($customer->id)) {
                $customer->save();
            }

            //
            // 订单性质
            //
            $nProperty = 0;
            foreach ($orderProperties as $op) {
                if ($op->name == $strProperty) {
                    $nProperty = $op->id;
                }
            }

            // 找不到订单性质, 失败
            if ($nProperty == 0) {
                echo '找不到订单性质, 失败';
                $bResult = false;
                break;
            }

            //
            // 奶站
            //
            foreach ($stations as $s) {
                if ($s->name == $strStation) {
                    $station = $s;
                }
            }

            //
            // 征订员
            //
            $nCheckerId = 0;
            foreach ($station->all_order_checkers as $checker) {
                if ($checker->name == $strChecker) {
                    $nCheckerId = $checker->id;
                }
            }

            // 找不到征订员, 失败
            if ($nCheckerId == 0) {
                echo '找不到征订员, 失败: ' . $strChecker;
                $bResult = false;
                break;
            }

            //
            // 下单日期
            //
            $dateInput = \DateTime::createFromFormat('Y-m-d', $strDateInput);
            if ($dateInput == false) {
                echo '下单日期错误, 失败: ' . $strDateInput;
                $bResult = false;
                break;
            }
            $strDateInput = getStringFromDate($dateInput);

            //
            // 起送日期
            //
            $dateStart = \DateTime::createFromFormat('m-d-y', $strDateStart);
            if ($dateStart == false) {
                echo '起送日期错误, 失败: ' . $strDateStart;
                $bResult = false;
                break;
            }
            $strDateStart = getStringFromDate($dateStart);

            //
            // 奶品id
            //
            $nProductId = 0;
            foreach ($products as $product) {
                if ($product->name == $strProduct) {
                    $nProductId = $product->id;
                }
            }

            // 找不到奶品, 失败
            if ($nProductId == 0) {
                echo '找不到奶品, 失败: ' . $strProduct;
                $bResult = false;
                break;
            }

            //
            // 订单类型
            //
            $nOrderType = 0;
            foreach ($orderTypes as $ot) {
                if ($ot->order_type_name == $strType) {
                    $nOrderType = $ot->order_type;
                }
            }

            // 找不到订单类型, 失败
            if ($nOrderType == 0) {
                echo '找不到订单类型, 失败: ' . $strType;
                $bResult = false;
                break;
            }

            //
            // 配送规则
            //
            $nDeliveryType = 0;
            foreach ($deliveryTypes as $dt) {
                if ($dt->name == $strDeliveryType) {
                    $nDeliveryType = $dt->delivery_type;
                }
            }

            // 找不到配送规则, 失败
            if ($nDeliveryType == 0) {
                echo '找不到配送规则, 失败: ' . $strDeliveryType;
                $bResult = false;
                break;
            }

            if ($checkData) {
                // 只校验数据
                continue;
            }

            //
            // 添加Order
            //
            $dTotalAmount = $nCount * $dPrice;
            $order = new Order;

            $order->factory_id = $this->mnFactoryId;
            $order->ordered_at = $strDateInput;

            $order->order_by_milk_card = 0;
            $order->payment_type = PaymentType::PAYMENT_TYPE_MONEY_NORMAL;

            $order->comment = $strComment;
            $order->total_amount = $dTotalAmount;
            $order->remaining_amount = $dTotalAmount;
            $order->trans_check = 0;

            $order->customer_id = $customer->id;
            $order->phone = $strPhone;
            $order->address = $strFullAddr;
            $order->order_property_id = $nProperty;

            $order->station_id = $station->id;
            $order->delivery_station_id = $station->id;

            $order->receipt_number = $strReceipt;
            $order->order_checker_id = $nCheckerId;

            $order->status = Order::ORDER_ON_DELIVERY_STATUS;
            $order->start_at = $strDateStart;
            $order->delivery_time = 1;

            $order->created_at = $dateInput;

            $order->save();

            $order->mnSeq = $nSeq;
            $order->setOrderNumber();

            $this->mOrders[] = $order;

            //
            // 添加OrderProduct
            //
            $op = new OrderProduct;
            $op->order_id = $order->id;
            $op->product_id = $nProductId;
            $op->order_type = $nOrderType;
            $op->delivery_type = $nDeliveryType;

            $op->product_price = $dPrice;

            $op->total_count = $nCount;
            $op->total_amount = $dTotalAmount;
            $op->avg = 1;
            $op->start_at = $strDateStart;

            $op->count_per_day = 1;

            $op->save();
        }

        echo '<br>----------------------<br>';

        return $bResult;
    }

    /**
     * 导入配送明细信息
     * @param $sheet
     * @param bool $checkData
     * @return bool
     */
    private function importDeliveryPlan($sheet, $checkData = true) {
        $bResult = true;

        $aryData = $sheet->toArray();

        //
        // 加载基础信息
        //
        $order = null;
        $nMilkmanId = 0;

        $strDateDeliver = null;
        $nCountDeliver = 0;
        $nCountDeliverSent = 0;

        $nCountTotal = 0;
        $nCountTotalSent = 0;

        for ($i = 1; $i <= count($aryData); $i++) {
            // 最后一行
            if ($i == count($aryData)) {
                $strDate = $strDateDeliver;
                $nCount = 0;
                $nCountSent = 0;
            }
            else {

                $row = $aryData[$i];

                if ($checkData) {
                    // 显示log
                    echo implode(', ', $row);
                    echo '<br>';
                }

                if ($i == 1) {
                    // 订单信息
                    $nOrderSeq = intval($row[0]);

                    foreach ($this->mOrders as $o) {
                        if ($o->mnSeq == $nOrderSeq) {
                            $order = $o;
                        }
                    }

                    // 找不到订单信息, 失败
                    if (!$order) {
                        echo '找不到订单信息: ' . $nOrderSeq;
                        $bResult = false;
                        break;
                    }

                    $nMilkmanId = $order->milkman_id;
                }

                //
                // 配送日期
                //
                $strDate = substr($row[4], 0, strlen("yyyy-mm-dd"));

                // 数量
                $nCount = intval($row[5]);

                // 实际配送数量
                $nCountSent = intval($row[6]);
            }

            //
            // 配送日期, null是意味着用上面的日期
            //
            if (!empty($strDate)) {

                // 03-13-17
                $date = \DateTime::createFromFormat('m-d-y', $strDate);
                if ($date == false) {
                    // 2017-03-13
                    $date = getDateFromString($strDate);

                    if ($date == false) {
                        echo '配送日期错误, 失败: ' . $date;
                        $bResult = false;
                        break;
                    }
                }

                // 已有数据， 添加数据
                if (!$checkData && $strDateDeliver) {

                    foreach ($order->order_products as $op) {
                        $dp = new MilkManDeliveryPlan;

                        $dp->milkman_id = $nMilkmanId;
                        $dp->station_id = $order->station_id;
                        $dp->order_id = $order->id;
                        $dp->order_product_id = $op->id;
                        $dp->deliver_at = $strDateDeliver;
                        $dp->produce_at = $op->getProductionDate($dp->deliver_at);

                        if ($nCountDeliver == $nCountDeliverSent) {
                            $dp->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED;
                        }
                        else {
                            $dp->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED;
                        }

                        $dp->plan_count = $nCountDeliver;
                        $dp->changed_plan_count = $nCountDeliver;
                        $dp->delivery_count = $nCountDeliver;
                        $dp->delivered_count = $nCountDeliverSent;

                        $dp->product_price = $op->product_price;
                        $dp->type = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER;

                        $dp->save();
                    }

                    // 清空数量
                    $nCountDeliver = 0;
                    $nCountDeliverSent = 0;
                }

                $strDateDeliver = getStringFromDate($date);
            }

            // 累计一天的数量
            $nCountDeliver += $nCount;
            $nCountDeliverSent += $nCountSent;

            // 累计总数量
            $nCountTotal += $nCount;
            $nCountTotalSent += $nCountSent;
        }

        //
        // 更新订单状态
        //
        if (!$checkData && $order) {
            foreach ($order->order_products as $op) {
                $dRemain = ($nCountTotal - $nCountTotalSent) * $op->product_price;
                $order->remaining_amount = $dRemain;

                if ($dRemain == 0) {
                    $order->status = Order::ORDER_FINISHED_STATUS;
                }

                $order->save();

                break;
            }
        }

        echo '<br>----------------------<br>';

        return $bResult;
    }
}