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
use App\Model\BasicModel\CityData;
use App\Model\BasicModel\Customer;
use App\Model\BasicModel\DistrictData;
use App\Model\BasicModel\PaymentType;
use App\Model\BasicModel\ProvinceData;
use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\DSDeliveryArea;
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
    private $mnFactoryId = 1;
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

        set_time_limit(0);

        $nType = $request->input('type');

        if ($request->hasFile('upload')){

            $file = $request->file('upload');

            Excel::load($file, function ($reader) use ($nType) {

                echo "Started import ...<br>";

                // 订单数据导入
                if ($nType == 0) {
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
                            } else {
                                // 失败就终止
                                break;
                            }
                        }
                    }
                }
                // 客户数据导入
                else if ($nType == 1) {
                    $sheet = $reader->getSheet(0);
                    $this->importCustomer($sheet);
                }
                // 地址库数据导入
                else if ($nType == 2) {
                    $sheet = $reader->getSheet(0);

                    echo "verifying address data ...<br>";
                    if ($this->importAddress($sheet)) {
                        // 导入
                        echo "importing address data ... <br>";
                        $this->importAddress($sheet, false);
                    }
                }
            });
        }

        return "<br><br><br>Import Done";
    }

    /**
     * 画面上显示结果
     * @param $cell
     * @param $show
     */
    private function logData($cell, $show) {
        if (!$show) {
            return;
        }

        echo $cell->getFormattedValue();
        echo ', ';
    }

    /**
     * 导入订单信息
     * @param $sheet
     * @param $checkData boolean 是否只校验数据
     * @return boolean
     */
    private function importOrder($sheet, $checkData = true) {

        $bResult = true;

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

        // 显示log
        if ($checkData) {
            echo '---------- Order ------------<br>';
            echo '<br>';
        }

        $nRowIndex = -1;
        foreach ($sheet->getRowIterator() as $row) {
            usleep(10000);

            $nRowIndex++;

            // 跳过第一行
            if ($nRowIndex == 0) {
                continue;
            }

            $nIndex = 0;
            foreach ($row->getCellIterator() as $cell) {
                // 显示log
                $this->logData($cell, $checkData);

                // 获取订单内容
                switch ($nIndex) {
                    // 序号
                    case 0:
                        $nSeq = $cell->getValue();
                        break;

                    // 收货人
                    case 1:
                        $strName = $cell->getValue();
                        break;

                    // 电话
                    case 2:
                        $strPhone = strval($cell->getValue());
                        break;

                    // 订单性质
                    case 3:
                        $strProperty = $cell->getValue();
                        break;

                    // 商品名
                    case 4:
                        $strProduct = $cell->getValue();
                        break;

                    // 起送日期
                    case 5:
                        $strDateStart = $cell->getFormattedValue();
                        break;

                    // 订购数量
                    case 6:
                        $nCount = intval($cell->getValue());
                        break;

                    // 订单类型
                    case 7:
                        $strType = $cell->getValue();
                        break;

                    // 配送规则
                    case 8:
                        $strDeliveryType = $cell->getValue();
                        break;

                    // 销售价
                    case 9:
                        $dPrice = doubleval($cell->getValue());
                        break;

                    // 地址：省
                    case 12:
                        $strProvince = $cell->getValue();
                        break;

                    // 地址：市
                    case 13:
                        $strCity = $cell->getValue();
                        break;

                    // 地址：区
                    case 14:
                        $strDistrict = $cell->getValue();
                        break;

                    // 地址：街道
                    case 15:
                        $strStreet = $cell->getValue();
                        break;

                    // 地址：小区
                    case 16:
                        $strVillage = $cell->getValue();
                        break;

                    // 地址：详细地址
                    case 17:
                        $strAddress = $cell->getValue();
                        break;

                    // 配送站
                    case 18:
                        $strStation = $cell->getValue();
                        break;

                    // 录入时间
                    case 19:
                        $strDateInput = substr($cell->getValue(), 0, strlen("yyyy-mm-dd"));
                        break;

                    // 票据号
                    case 21:
                        $strReceipt = $cell->getValue();
                        break;

                    // 备注
                    case 24:
                        $strComment = $cell->getValue();
                        break;

                    // 征订人
                    case 26:
                        $strChecker = $cell->getValue();
                        break;
                }

                $nIndex++;
            }

            // 换行
            if ($checkData) {
                echo '<br>';
            }

            //
            // 验证地址
            //
            $village = null;

            foreach ($villages as $v) {
                $street = $v->parent;
                $district = $street->parent;
                $city = $district->parent;
                $province = $city->parent;

                if (!strcmp($strProvince, $province->name) &&
                    // 考虑到"北京"和"北京市"的情况
                    !strncmp($strCity, $city->name, strlen($strCity)) &&
                    !strcmp($strDistrict, $district->name) &&
                    !strcmp($strStreet, $street->name) &&
                    !strcmp($strVillage, $v->name)) {

                    $village = $v;
                    break;
                }
            }

            // 找不到小区信息, 失败
            if (!$village) {
                echo '找不到地址信息: ' . $strProvince . ' ' . $strCity . ' ' . $strDistrict . ' ' . $strStreet . ' ' . $strVillage;
                $bResult = false;
                break;
            }

            //
            // 配送员
            //
            $strFullVillageAddr = $village->getFullName();
            $strFullAddr = $strFullVillageAddr . ' ' . $strAddress;

            $station = null;
            $milkman = $orderCtrl->get_station_milkman_with_address_from_factory($this->mnFactoryId, $strFullVillageAddr, $station);

            if ($milkman == OrderCtrl::NOT_EXIST_DELIVERY_AREA) {
                echo '该地区没有覆盖可配送的范围，导入失败: ' . $strFullVillageAddr;
                $bResult = false;
                break;
            }
            else if ($milkman == OrderCtrl::NOT_EXIST_STATION) {
                echo '没有奶站，导入失败: ' . $strFullVillageAddr;
                $bResult = false;
                break;
            }
            else if ($milkman == OrderCtrl::NOT_EXIST_MILKMAN) {
                echo '奶站没有配送员，导入失败';
                $bResult = false;
                break;
            }

            //
            // 设置客户信息
            //
            $customer = $orderCtrl->getCustomer($strPhone, $strFullAddr, $this->mnFactoryId);

            $customer->station_id = $milkman[0];
            $customer->milkman_id = $milkman[1];

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
            $order->deliveryarea_id = $milkman[2];
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

            //
            // 创建一个MilkmanDeliveryPlan, 之后自动生成
            //
            $orderCtrl->addMilkmanDeliveryPlan($customer->milkman_id,
                $station->id,
                $op->start_at,
                $op,
                MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED,
                $op->total_count);

            echo 'Added order: ' . $nSeq . '<br>';
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

    /**
     * 导入客户信息；账户余额
     * @param $sheet
     */
    private function importCustomer($sheet) {

        $aryData = $sheet->toArray();

        for ($i = 1; $i < count($aryData); $i++) {
            $row = $aryData[$i];

            echo implode(', ', $row);

            // 手机号
            $strPhone = strval($row[1]);

            // 账户余额
            $dBalance = doubleval($row[2]);

            // 获取客户信息
            $customer = Customer::where('factory_id', $this->mnFactoryId)->where('phone', $strPhone)->first();
            if (!$customer) {
                echo "  找不到客户信息！";
                continue;
            }

            // 保存账户余额
            $customer->remain_amount = $dBalance;
            $customer->save();

            echo "<br>";
        }
    }

    /**
     * 导入地址库
     * @param $sheet
     * @return bool
     */
    private function importAddress($sheet, $checkData = true) {
        $bResult = true;

        $aryData = $sheet->toArray();

        for ($i = 1; $i < count($aryData); $i++) {
            $row = $aryData[$i];

            if ($checkData) {
                echo implode(', ', $row);
                echo "<br>";
            }

            // 小区
            $strVillage = $row[0];

            // 省
            $strProvince = $row[2];

            // 市
            $strCity = $row[3];

            // 区
            $strDistrict = $row[4];

            // 街道
            $strStreet = $row[5];

            // 只校验数据
            if ($checkData) {
                //
                // 校验省
                //
                $provinces = ProvinceData::where('name', 'LIKE', $strProvince . '%')->get();

                if (count($provinces) == 0) {
                    echo '!!! !!!!   找不到此省: ' . $strProvince;
                    $bResult = false;
                    break;
                }
                else if (count($provinces) > 1) {
                    echo "!!! !!!!   发现多个省: ";
                    foreach ($provinces as $pr) {
                        echo $pr->name . ", ";
                    }

                    $bResult = false;
                    break;
                }

                //
                // 校验市
                //
                $cities = CityData::where('name', 'LIKE', $strCity . '%')->where('provincecode', $provinces[0]->code)->get();

                if (count($cities) == 0) {
                    echo '!!! !!!!   找不到此市: ' . $strCity;
                    $bResult = false;
                    break;
                }
                else if (count($cities) > 1) {
                    echo "!!! !!!!   发现多个市: ";
                    foreach ($cities as $ct) {
                        echo $ct->name . ", ";
                    }

                    $bResult = false;
                    break;
                }

                //
                // 校验区
                //
                $districts = DistrictData::where('name', $strDistrict)->where('citycode', $cities[0]->code)->get();

                if (count($districts) == 0) {
                    echo '!!! !!!!   找不到此区: ' . $strDistrict;
                    $bResult = false;
                    break;
                }
                else if (count($districts) > 1) {
                    echo "!!! !!!!   发现多个区: ";
                    foreach ($districts as $dist) {
                        echo $dist->name . ", ";
                    }

                    $bResult = false;
                    break;
                }

                continue;
            }

            // 获取省
            $provinceData = ProvinceData::where('name', 'LIKE', $strProvince . '%')->first();
            $strProvince = $provinceData->name;

            $province = Address::where('name', $strProvince)
                ->where('level', Address::LEVEL_PROVINCE)
                ->where('is_active', Address::ADDRESS_ACTIVE)
                ->first();

            if (!$province) {
                $province = Address::create([
                    'name' => $strProvince,
                    'level' => Address::LEVEL_PROVINCE,
                    'parent_id' => 0,
                    'factory_id' => $this->mnFactoryId,
                ]);
            }

            // 获取市
            $cityData = CityData::where('name', 'LIKE', $strCity . '%')->where('provincecode', $provinceData->code)->first();
            $strCity = $cityData->name;

            $city = Address::where('name', $strCity)
                ->where('level', Address::LEVEL_CITY)
                ->where('parent_id', $province->id)
                ->where('is_active', Address::ADDRESS_ACTIVE)
                ->first();

            if (!$city) {
                $city = Address::create([
                    'name' => $strCity,
                    'level' => Address::LEVEL_CITY,
                    'parent_id' => $province->id,
                    'factory_id' => $this->mnFactoryId,
                ]);
            }

            $district = Address::where('name', $strDistrict)
                ->where('level', Address::LEVEL_DISTRICT)
                ->where('parent_id', $city->id)
                ->where('is_active', Address::ADDRESS_ACTIVE)
                ->first();

            if (!$district) {
                $district = Address::create([
                    'name' => $strDistrict,
                    'level' => Address::LEVEL_DISTRICT,
                    'parent_id' => $city->id,
                    'factory_id' => $this->mnFactoryId,
                ]);
            }

            // 获取街道
            $street = Address::where('name', $strStreet)
                ->where('level', Address::LEVEL_STREET)
                ->where('parent_id', $district->id)
                ->where('is_active', Address::ADDRESS_ACTIVE)
                ->first();

            if (!$street) {
                $street = Address::create([
                    'name' => $strStreet,
                    'level' => Address::LEVEL_STREET,
                    'parent_id' => $district->id,
                    'factory_id' => $this->mnFactoryId,
                ]);
            }

            // 获取小区
            $village = Address::where('name', $strVillage)
                ->where('level', Address::LEVEL_VILLAGE)
                ->where('parent_id', $street->id)
                ->where('is_active', Address::ADDRESS_ACTIVE)
                ->first();

            if (!$village) {
                $village = Address::create([
                    'name' => $strVillage,
                    'level' => Address::LEVEL_VILLAGE,
                    'parent_id' => $street->id,
                    'factory_id' => $this->mnFactoryId,
                ]);
            }
        }

        echo '<br>----------------------<br>';

        return $bResult;
    }

    /**
     * order里添加deliveryarea_id
     */
    public function updateOrder(Request $request) {
        $orders = Order::whereNull('deliveryarea_id')->get();

        foreach ($orders as $order) {

            echo $order->id . " => ";

            $deliveryArea = DSDeliveryArea::where('address', $order->main_address)
                ->first();

            if (!empty($deliveryArea)) {
                echo $deliveryArea->id . ", Station: " . $deliveryArea->station_id;

                $order->deliveryarea_id = $deliveryArea->id;
                $order->delivery_station_id= $deliveryArea->station_id;
                $order->save();
            }
            else {
                echo "Cannot get DSDevlieryArea object";
            }

            echo "<br />";
        }
    }
}