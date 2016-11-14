<?php

namespace App\Http\Controllers;

use App\Model\BasicModel\PaymentType;
use App\Model\DeliveryModel\DSProductionPlan;
use App\Model\DeliveryModel\MilkManDeliveryPlan;
use App\Model\FactoryModel\MilkCard;
use App\Model\FinanceModel\DSCalcBalanceHistory;
use App\Model\FinanceModel\DSDeliveryCreditBalanceHistory;
use Faker\Provider\at_AT\Payment;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Model\UserModel\Page;

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
use Symfony\Component\HttpFoundation\RequestStack;

use App\Model\BasicModel\Address;

class OrderCtrl extends Controller
{
    const NOT_EXIST_DELIVERY_AREA = 1;
    const NOT_EXIST_STATION = 2;
    const NOT_EXIST_MILKMAN = 3;
    const NOT_EXIST_PRICE = 4;


    function make_each_delivery_plan($milkman_id, $station_id, $order_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price)
    {
        //check exist of delivery plan that remains because of submitted to production plan before
        $sub_to_mmdp = MilkManDeliveryPlan::where('order_id', $order_id)
            ->where('order_product_id', $order_product_id)
            ->where('deliver_at', $deliver_at)->get()->first();

        if ($sub_to_mmdp) {
            $sub_to_mmdp->changed_plan_count = $changed_plan_count;
            $sub_to_mmdp->flag = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER_RULE_CHANGE;
            $sub_to_mmdp->save();

        } else {
            $sub_to_pp = DSProductionPlan::where('station_id', $station_id)
                ->where('produce_start_at', $produce_at)
//                ->where('product_id', $product_id)
                ->get()->first();

            if ($sub_to_pp) {
                $plan_count = 0;
            }

            $dp = new MilkManDeliveryPlan;

            $dp->milkman_id = $milkman_id;
            $dp->station_id = $station_id;
            $dp->order_id = $order_id;
            $dp->order_product_id = $order_product_id;
            $dp->produce_at = $produce_at;
            $dp->deliver_at = $deliver_at;
            $dp->status = $status;
            $dp->plan_count = $plan_count;
            $dp->product_price = $product_price;
            $dp->changed_plan_count = $changed_plan_count;
            $dp->delivery_count = $delivery_count;
            $dp->delivered_count = $delivered_count;
            $dp->type = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER;
            $dp->flag = 0;

            $dp->save();
        }
    }

    function make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price)
    {
        //check exist of delivery plan that remains because of submitted to production plan before
        $sub_to_mmdp = MilkManDeliveryPlan::where('order_id', $order_id)
            ->where('order_product_id', $order_product_id)
            ->where('deliver_at', $deliver_at)->get()->first();

        if ($sub_to_mmdp) {
            $sub_to_mmdp->changed_plan_count = $changed_plan_count;
            $sub_to_mmdp->flag = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER_RULE_CHANGE;
            $sub_to_mmdp->save();

        } else {

            $sub_to_pp = DSProductionPlan::where('station_id', $station_id)
                ->where('produce_start_at', $produce_at)
//                ->where('product_id', $product_id)
                ->get()->first();

            if ($sub_to_pp) {
                $plan_count = 0;
            }

            $dp = new MilkManDeliveryPlan;
            $dp->milkman_id = $milkman_id;
            $dp->station_id = $station_id;
            $dp->order_id = $order_id;
            $dp->order_product_id = $order_product_id;
            $dp->produce_at = $produce_at;
            $dp->deliver_at = $deliver_at;
            $dp->status = $status;
            $dp->product_price = $product_price;
            $dp->plan_count = $plan_count;
            $dp->changed_plan_count = $changed_plan_count;
            $dp->delivery_count = $delivery_count;
            $dp->delivered_count = $delivered_count;
            $dp->type = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER;
            $dp->flag = 0;
            $dp->save();
        }
        return true;
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

    //Establish plan for changed order in xiugai
    function establish_new_plan($op, $factory_id, $station_id, $milkman_id)
    {
        //station id <- delivery station id
        $order_id = $op->order_id;
        $order_product_id = $op->id;
        $product_id = $op->product_id;
        $product_price = $op->product_price;

        //Order object
        $order = Order::find($order_id);

        //get product production_period and orderat-production_period
        $product = Product::find($product_id);
        $production_period = ($product->production_period) / 24;

        //get factory gap day before start delivery of new order
        $factory = Factory::find($factory_id);

        $status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING; //suggested

        $delivered_count = 0;

        //get total count and delivery type
        $total_count = $op->total_count;
        $delivery_type = $op->delivery_type;

        $deliver_at = $this->get_deliverable_date_for_op($op);

        $stop_at = $order->stop_at;
        $restart_at = $order->restart_at;

        $exist_stop = false;
        if (($stop_at && $restart_at) || ($stop_at != "" && $restart_at != "")) {
            $exist_stop = true;
        }

        $stop_checked = false;
        if ($exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
            $deliver_at = $restart_at;
            $stop_checked = true;
        }

        if ($delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY) {

            //every day send
            $plan_count = $op->count_per_day;
            $interval = 1;

            do {
                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                if ($total_count < $plan_count)
                    $plan_count = $total_count;

                $delivery_count = $plan_count;
                $changed_plan_count = $plan_count;

                $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                $total_count -= $plan_count;
                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                    $deliver_at = $restart_at;
                    $stop_checked = true;
                }

            } while ($total_count > 0);

        } else if ($delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY) {
            //each 2 days send
            $plan_count = $op->count_per_day;
            //changed plan count = plan acount
            $interval = 2;

            do {
                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                if ($total_count < $plan_count)
                    $plan_count = $total_count;

                $delivery_count = $plan_count;
                $changed_plan_count = $plan_count;


                $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                $total_count -= $plan_count;
                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                    $deliver_at = $restart_at;
                    $stop_checked = true;
                }

            } while ($total_count > 0);
        } else if ($delivery_type == DeliveryType::DELIVERY_TYPE_WEEK) {
            //week day
            $cod = $op->custom_order_dates;

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

                    $next_key = $this->get_next_key($custom, $key);

                    if ($next_key < $key) {
                        $interval = $next_key + 7 - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $delivery_count = $plan_count;
                    $changed_plan_count = $plan_count;

                    $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                    $total_count -= $plan_count;
                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

                    if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                        $deliver_at = $restart_at;
                        $stop_checked = true;
                    }
                } else {
                    //get avaiable key value > current_key
                    $old_key = $key;

                    $key = $this->getClosestKey($key, $custom);

                    //find the best closest $key

                    if ($key < $old_key)
                        $first_interval = $key + 7 - $old_key;
                    else
                        $first_interval = $key - $old_key;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $first_interval);

                    $plan_count = $custom[$key];

                    $next_key = $this->get_next_key($custom, $key);
                    if ($next_key < $key) {
                        $interval = $next_key + 7 - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $delivery_count = $plan_count;
                    $changed_plan_count = $plan_count;

                    $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                    $total_count -= $plan_count;
                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

                    if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                        $deliver_at = $restart_at;
                        $stop_checked = true;
                    }
                }
            } while ($total_count > 0);
        } else {
            //month day
            $cod = $op->custom_order_dates;
            $daynums = $this->days_in_month($deliver_at);

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
                $key = (new DateTime($deliver_at))->format('d');

                if (array_key_exists($key, $custom)) {
                    $plan_count = $custom[$key];

                    $next_key = $this->get_next_key($custom, $key);

                    if ($next_key < $key) {
                        $interval = $next_key + $daynums - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $delivery_count = $plan_count;
                    $changed_plan_count = $plan_count;

                    $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                    $total_count -= $plan_count;
                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

                    if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                        $deliver_at = $restart_at;
                        $stop_checked = true;
                    }
                    $daynums = $this->days_in_month($deliver_at);
                } else {
                    //get avaiable key value > current_key
                    $old_key = $key;

                    $key = $this->getClosestKey($key, $custom);

                    if ($key < $old_key) {
                        $first_interval = $key + $daynums - $old_key;
                    } else
                        $first_interval = $key - $old_key;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $first_interval);
                    $daynums = $this->days_in_month($deliver_at);

                    $plan_count = $custom[$key];

                    $next_key = $this->get_next_key($custom, $key);
                    if ($next_key < $key) {
                        $interval = $next_key + $daynums - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $delivery_count = $plan_count;
                    $changed_plan_count = $plan_count;

                    $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                    $total_count -= $plan_count;
                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

                    if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                        $deliver_at = $restart_at;
                        $stop_checked = true;
                    }

                    $daynums = $this->days_in_month($deliver_at);
                }
            } while ($total_count > 0);
        }

    }

    //Establish plan for changed order in xiugai based on money amount
    function establish_new_plan_with_money_amount($op, $factory_id, $delivery_station_id, $milkman_id, $total_amount)
    {
        //station id <- delivery station id
        $order_id = $op->order_id;
        $order_product_id = $op->id;
        $product_id = $op->product_id;

        //get product production_period and order at-production_period
        $product = Product::find($product_id);
        $production_period = ($product->production_period) / 24;

        $status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING; //suggested

        $delivery_type = $op->delivery_type;
        $delivered_count = 0;

        //Check for stop and restart at
        $order = Order::find($order_id);

        $stop_at = $order->stop_at;
        $restart_at = $order->restart_at;

        $exist_stop = false;
        if (($stop_at && $restart_at) || ($stop_at != "" && $restart_at != "") || ($stop_at != null && $restart_at != null)) {
            $exist_stop = true;
        }

        $total_count = $op->remain_count;

        //origin value
        $origin_total_count =$total_count;
        $origin_total_amount = $total_amount;


        $product_price = $op->product_price;

        $deliver_at = $this->get_deliverable_date_for_op($op);

        $stop_checked = false;
        if ($exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
            $deliver_at = $restart_at;
            $stop_checked = true;
        }

        if ($delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY) {

            //every day send
            $plan_count = $op->count_per_day;
            $product_price = $op->product_price;
            $interval = 1;

            do {
                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);

                if ($total_count < $plan_count)
                    $plan_count = $total_count;

                $plan_price = $plan_count * $product_price;

                if ($total_amount < $plan_price) {
                    $plan_count = floor($total_amount / $plan_price);
                }

                if ($plan_count == 0) {
                    return $total_amount;
                }

                $delivery_count = $plan_count;
                $changed_plan_count = $plan_count;

                $this->make_each_delivery_plan_for_changed_order($milkman_id, $delivery_station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);

                $total_count -= $plan_count;
                $total_amount -= $plan_count * $product_price;

                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

                if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                    $deliver_at = $restart_at;
                    $stop_checked = true;
                }

            } while ($total_amount >= $product_price && $total_count > 0);

        }
        else if ($delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY) {
            //each 2 days send
            $interval = 2;
            $product_price = $op->product_price;

            do {

                $plan_count = $op->count_per_day;

                if ($total_count < $plan_count)
                    $plan_count = $total_count;

                $plan_price = $plan_count * $product_price;

                if ($total_amount < $plan_price) {
                    $plan_count = round($total_amount / $product_price);
                }

                if ($plan_count == 0) {
                    return $total_amount;
                }

                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);

                $delivery_count = $plan_count;
                $changed_plan_count = $plan_count;


                $this->make_each_delivery_plan_for_changed_order($milkman_id, $delivery_station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);

                $total_count -= $plan_count;
                $total_amount -= $plan_count * $product_price;

                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

                if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                    $deliver_at = $restart_at;
                    $stop_checked = true;
                }

            } while ($total_amount >= $product_price && $total_count > 0);
        }
        else if ($delivery_type == DeliveryType::DELIVERY_TYPE_WEEK) {
            //week day
            $cod = $op->custom_order_dates;

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

                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $plan_price = $plan_count * $product_price;

                    if ($total_amount < $plan_price) {
                        $plan_count = round($total_amount / $product_price);
                    }

                    if ($plan_count == 0) {
                        return $total_amount;
                    }

                    $next_key = $this->get_next_key($custom, $key);

                    if ($next_key < $key) {
                        $interval = $next_key + 7 - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_amount < $plan_price)
                        $plan_count = round($total_amount / $product_price);

                    if ($plan_count == 0) {
                        return $total_amount;
                    }


                    $delivery_count = $plan_count;
                    $changed_plan_count = $plan_count;

                    $this->make_each_delivery_plan_for_changed_order($milkman_id, $delivery_station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);

                    $total_count -= $plan_count;
                    $total_amount -= $plan_count * $product_price;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

                    if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                        $deliver_at = $restart_at;
                        $stop_checked = true;
                    }
                } else {
                    //get avaiable key value > current_key
                    $old_key = $key;

                    $key = $this->getClosestKey($key, $custom);

                    //find the best closest $key

                    if ($key < $old_key)
                        $first_interval = $key + 7 - $old_key;
                    else
                        $first_interval = $key - $old_key;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $first_interval);

                    $plan_count = $custom[$key];

                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $plan_price = $plan_count * $product_price;

                    if ($total_amount < $plan_price) {
                        $plan_count = round($total_amount / $product_price);
                    }

                    if ($plan_count == 0) {
                        return $total_amount;
                    }

                    $next_key = $this->get_next_key($custom, $key);
                    if ($next_key < $key) {
                        $interval = $next_key + 7 - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_amount < $plan_price)
                        $plan_count = round($total_amount / $product_price);

                    if ($plan_count == 0) {
                        return $total_amount;
                    }


                    $delivery_count = $plan_count;
                    $changed_plan_count = $plan_count;

                    $this->make_each_delivery_plan_for_changed_order($milkman_id, $delivery_station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);

                    $total_count -= $plan_count;
                    $total_amount -= $plan_count * $product_price;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

                    if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                        $deliver_at = $restart_at;
                        $stop_checked = true;
                    }
                }
            } while ($total_amount >= $product_price && $total_count > 0);
        }
        else {
            //month day
            $cod = $op->custom_order_dates;
            $daynums = $this->days_in_month($deliver_at);

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
                $key = (new DateTime($deliver_at))->format('d');

                if (array_key_exists($key, $custom)) {
                    $plan_count = $custom[$key];

                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $plan_price = $plan_count * $product_price;

                    if ($total_amount < $plan_price) {
                        $plan_count = round($total_amount / $plan_price);
                    }

                    if ($plan_count == 0) {
                        return $total_amount;
                    }

                    $next_key = $this->get_next_key($custom, $key);

                    if ($next_key < $key) {
                        $interval = $next_key + $daynums - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);

                    $delivery_count = $plan_count;
                    $changed_plan_count = $plan_count;

                    $this->make_each_delivery_plan_for_changed_order($milkman_id, $delivery_station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);

                    $total_count -= $plan_count;
                    $total_amount -= $plan_count * $product_price;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

                    if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                        $deliver_at = $restart_at;
                        $stop_checked = true;
                    }

                    $daynums = $this->days_in_month($deliver_at);


                } else {
                    //get avaiable key value > current_key
                    $old_key = $key;

                    $key = $this->getClosestKey($key, $custom);

                    if ($key < $old_key) {
                        $first_interval = $key + $daynums - $old_key;
                    } else
                        $first_interval = $key - $old_key;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $first_interval);
                    $daynums = $this->days_in_month($deliver_at);

                    $plan_count = $custom[$key];

                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $plan_price = $plan_count * $product_price;

                    if ($total_amount < $plan_price) {
                        $plan_count = round($total_amount / $plan_price);
                    }

                    if ($plan_count == 0) {
                        return $total_amount;
                    }

                    $next_key = $this->get_next_key($custom, $key);
                    if ($next_key < $key) {
                        $interval = $next_key + $daynums - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);

                    if ($total_amount < $plan_price)
                        $plan_count = round($total_amount / $product_price);

                    if ($plan_count == 0) {
                        return $total_amount;
                    }


                    $delivery_count = $plan_count;
                    $changed_plan_count = $plan_count;

                    $this->make_each_delivery_plan_for_changed_order($milkman_id, $delivery_station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);

                    $total_count -= $plan_count;
                    $total_amount -= $plan_count * $product_price;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

                    if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                        $deliver_at = $restart_at;
                        $stop_checked = true;
                    }
                    $daynums = $this->days_in_month($deliver_at);
                }
            } while ($total_amount >= $product_price && $total_count > 0);
        }

        //add finished count and amount
        $op->total_count = $origin_total_count - $total_count+$op->finished_count;
        $op->total_amount = $origin_total_amount - $total_amount + $op->finished_money_amount;

        $op->save();

        //Change order product's count and amount
        return $total_amount;

    }


    //about this order product, find deliverable date after the finished plan, considering stop and start date
    public function get_deliverable_date_for_op($op)
    {
        $order_id = $op->order_id;
        $order = Order::find($order_id);

        //get last finished deliver date
        $last_finished_plan = MilkManDeliveryPlan::where('order_id', $order_id)->where('order_product_id', $op->id)
            ->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
            ->orderBy('deliver_at')->get()->first();

        if (!$last_finished_plan) {
            $date = $order->start_at;

            return $date;
        } else {

            $finished_last_deliver_date = $last_finished_plan->deliver_at;

            //get next deliver date
            $date = $this->get_next_delivery_day($op->delivery_type, $op, $finished_last_deliver_date);
            return $date;
        }

    }


    //This module can be used to change the plan for one day, cancel the production plan for one day
    public function change_delivery_plan($order_id, $plan_id, $diff)
    {
        $plan = MilkManDeliveryPlan::find($plan_id);

        $order = Order::find($order_id);

        $stop_at = $order->stop_at;
        $restart_at = $order->restart_at;

        $exist_stop = false;
        if (($stop_at && $restart_at) || ($stop_at != "" && $restart_at != "") || ($stop_at != null && $restart_at != null) ) {
            $exist_stop = true;
        }

        $origin = $plan->delivery_count;
        $changed = $origin + $diff;

        /*
         * first change the count
         *
         * Here, get all after count including current plan and if the count > changed then set, if not fail
         * */
        $rest_with_this = $this->get_rest_plans_count($order_id, $plan_id);

        if ($changed <= $rest_with_this) {
            //set current changed delivery plan
            $plan->changed_plan_count = $changed;
            $plan->delivery_count = $changed;
            $plan->save();

            //get each delivery plans from last delivery day and delete or create plans
            //enable to change the plan
            if ($diff > 0) {

                //decrease plans
                $count = 0;
                //check from last delivery plans
                $ldps = $order->getLastDeliveryPlans($plan_id);
                //decrease the plans
                foreach ($ldps as $ldp) {
                    if ($ldp->delivery_count > $diff) {
                        $ldp->delivery_count = $ldp->delivery_count - $diff;
                        $ldp->changed_plan_count = $ldp->delivery_count;
                        $ldp->save();
                        $count = 1;
                        break;
                    } else {
                        $diff = $diff - ($ldp->delivery_count);
                        $ldp->delete();
                        $count++;
                    }
                    if ($diff == 0)
                        break;
                }
            } else {
                //Increase Plans: create additional plans

                $diff = -$diff;//this is the total count that can be used to make new delivery plan

                //increase the plans
                //make new delivery plans
                $order_product_id = $plan->order_product_id;
                $op = OrderProduct::find($order_product_id);
                $product_price = $op->product_price;
                $product_id = $op->product_id;

                //get product production_period and order at-production_period
                $product = Product::find($product_id);
                $production_period = ($product->production_period) / 24;

                //get factory gap day before start delivery of new order


                //Even though this was on delivery, they should be sent to the check waiting status
                //$status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING; //suggested
                //set status of origin status
                $status = $plan->status;

                $milkman_id = $plan->milkman_id;
                $station_id = $plan->station_id;

                $delivered_count = 0;

                $delivery_type = $op->delivery_type;

                $last_deliver_plan = $op->last_deliver_plan;//last deliver plan's start at
                if ($last_deliver_plan) {
                    //get total count and delivery type
                    if ($last_deliver_plan->id == $plan_id) {
                        $total_count = $diff;
                        $deliver_at = $last_deliver_plan->deliver_at;

                        $deliver_at = $this->get_next_delivery_day($delivery_type, $op, $deliver_at);
                    } else {
                        $total_count = $diff + $last_deliver_plan->delivery_count;//$total_count = $diff+last_deliver's deliver count
                        $last_deliver_plan->delete();
                        $deliver_at = $last_deliver_plan->deliver_at;
                    }

                    $stop_checked = false;
                    if ($exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                        $deliver_at = $restart_at;
                        $stop_checked = true;
                    }

                    //check deliver_at exist in stop_at and restart_at

                    if ($delivery_type == 1) {

                        //every day send
                        $plan_count = $op->count_per_day;
                        //changed plan count = plan acount
                        $changed_plan_count = $plan_count;
                        $interval = 1;

                        do {
                            $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                            if ($total_count < $plan_count)
                                $plan_count = $total_count;

                            $changed_plan_count = $plan_count;
                            $delivery_count = $plan_count;

                            $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                            $total_count -= $plan_count;
                            $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                            if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                                $deliver_at = $restart_at;
                                $stop_checked = true;
                            }

                        } while ($total_count > 0);
                    } else if ($delivery_type == 2) {
                        //each 2 days send
                        $plan_count = $op->count_per_day;
                        //changed plan count = plan acount
                        $changed_plan_count = $plan_count;
                        $interval = 2;

                        do {
                            $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                            if ($total_count < $plan_count)
                                $plan_count = $total_count;
                            $changed_plan_count = $plan_count;
                            $delivery_count = $plan_count;

                            $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                            $total_count -= $plan_count;
                            $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                            if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                                $deliver_at = $restart_at;
                                $stop_checked = true;
                            }

                        } while ($total_count > 0);
                    } else if ($delivery_type == 3) {
                        //week day
                        $cod = $op->custom_order_dates;

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
                                $changed_plan_count = $plan_count;

                                $next_key = $this->get_next_key($custom, $key);

                                if ($next_key < $key) {
                                    $interval = $next_key + 7 - $key;
                                } else {
                                    $interval = $next_key - $key;
                                }

                                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                                if ($total_count < $plan_count)
                                    $plan_count = $total_count;

                                $changed_plan_count = $plan_count;
                                $delivery_count = $plan_count;

                                $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                                $total_count -= $plan_count;
                                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                                if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                                    $deliver_at = $restart_at;
                                    $stop_checked = true;
                                }
                            } else {
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
                                $changed_plan_count = $plan_count;

                                $next_key = $this->get_next_key($custom, $key);
                                if ($next_key < $key) {
                                    $interval = $next_key + 7 - $key;
                                } else {
                                    $interval = $next_key - $key;
                                }

                                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                                if ($total_count < $plan_count)
                                    $plan_count = $total_count;

                                $changed_plan_count = $plan_count;
                                $delivery_count = $plan_count;

                                $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                                $total_count -= $plan_count;
                                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                                if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                                    $deliver_at = $restart_at;
                                    $stop_checked = true;
                                }
                            }
                        } while ($total_count > 0);
                    } else {
                        //month day
                        $cod = $op->custom_order_dates;
                        $daynums = $this->days_in_month($deliver_at);

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
                            $key = (new DateTime($deliver_at))->format('d');

                            if (array_key_exists($key, $custom)) {
                                $plan_count = $custom[$key];
                                //changed plan count = plan acount
                                $changed_plan_count = $plan_count;

                                $next_key = $this->get_next_key($custom, $key);

                                if ($next_key < $key) {
                                    $interval = $next_key + $daynums - $key;
                                } else {
                                    $interval = $next_key - $key;
                                }

                                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                                if ($total_count < $plan_count)
                                    $plan_count = $total_count;

                                $changed_plan_count = $plan_count;
                                $delivery_count = $plan_count;

                                $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                                $total_count -= $plan_count;
                                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                                $daynums = $this->days_in_month($deliver_at);

                                if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                                    $deliver_at = $restart_at;
                                    $daynums = $this->days_in_month($deliver_at);
                                    $stop_checked = true;
                                }
                            } else {
                                //get avaiable key value > current_key
                                $old_key = $key;

                                $key = $this->getClosestKey($key, $custom);

                                if ($key < $old_key)
                                    $first_interval = $key + $daynums - $old_key;
                                else
                                    $first_interval = $key - $old_key;

                                $deliver_at = $this->get_deliver_at_day($deliver_at, $first_interval);
                                $daynums = $this->days_in_month($deliver_at);

                                $plan_count = $custom[$key];
                                //changed plan count = plan acount
                                $changed_plan_count = $plan_count;

                                $next_key = $this->get_next_key($custom, $key);
                                if ($next_key < $key) {
                                    $interval = $next_key + $daynums - $key;
                                } else {
                                    $interval = $next_key - $key;
                                }

                                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                                if ($total_count < $plan_count)
                                    $plan_count = $total_count;

                                $changed_plan_count = $plan_count;
                                $delivery_count = $plan_count;

                                $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                                $total_count -= $plan_count;
                                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                                $daynums = $this->days_in_month($deliver_at);

                                if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                                    $deliver_at = $restart_at;
                                    $daynums = $this->days_in_month($deliver_at);
                                    $stop_checked = true;
                                }
                            }
                        } while ($total_count > 0);
                    }

                } else {
                    $plan->changed_plan_count = $origin;
                    $plan->delivery_count = $origin;
                    $plan->save();
                    return ['status' => 'fail', 'message' => '同时改变了计划，发生错误.'];
                }

            }

            return ['status' => 'success', 'message' => '交付变更成功.'];
        } else {
            //impossible to change the plan
            return ['status' => 'fail', 'message' => '你不能改变这样的计划. 该计划的改变计数是在可能的数.'];
        }
    }


    //KIG: change delivery plan with plan_id, order_id, origin plan count, changed plan count
    public function change_delivery_plan_for_one_day($order_product_id, $diff, $deliver_at)
    {
        //get plan id from $deliver_at and $order_product_id

        $plan = MilkManDeliveryPlan::where('order_product_id', $order_product_id)->where('deliver_at', $deliver_at)->get()->first();
        $plan_id = $plan->id;
        $origin = $plan->delivery_count;
        $order_id = $plan->order_id;

        $order = Order::find($order_id);
        if (!$order) {
            return response()->json(['status' => 'fail', 'message' => '找不到订单.']);
        }

        $plan = MilkManDeliveryPlan::find($plan_id);

        $order = Order::find($order_id);

        $stop_at = $order->stop_at;
        $restart_at = $order->restart_at;

        $exist_stop = false;
        if (($stop_at && $restart_at) || ($stop_at != "" && $restart_at != "")) {
            $exist_stop = true;
        }

        $changed = $origin + $diff;

        /*
         * first change the count
         *
         * Here, get all after count including current plan and if the count > changed then set, if not fail
         * */
        $rest_with_this = $this->get_rest_plans_count($order_id, $plan_id);

        if ($changed <= $rest_with_this) {
            //set current changed delivery plan
            $plan->changed_plan_count = $changed;
            $plan->delivered_count = 0;
            $plan->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED;
            $plan->save();

            //get each delivery plans from last delivery day and delete or create plans
            //enable to change the plan
            if ($diff > 0) {

                //decrease plans
                $count = 0;
                //check from last delivery plans
                $ldps = $order->getLastDeliveryPlans($plan_id);
                //decrease the plans
                foreach ($ldps as $ldp) {
                    if ($ldp->delivery_count > $diff) {
                        $ldp->delivery_count = $ldp->delivery_count - $diff;
                        $ldp->changed_plan_count = $ldp->delivery_count;
                        $ldp->save();
                        $count = 1;
                        break;
                    } else {
                        $diff = $diff - ($ldp->delivery_count);
                        $ldp->delete();
                        $count++;
                    }
                    if ($diff == 0)
                        break;
                }
            } else {
                //Increase Plans: create additional plans

                $diff = -$diff;//this is the total count that can be used to make new delivery plan

                //increase the plans
                //make new delivery plans
                $order_product_id = $plan->order_product_id;
                $op = OrderProduct::find($order_product_id);
                $product_id = $op->product_id;

                //get product production_period and order at-production_period
                $product = Product::find($product_id);
                $production_period = ($product->production_period) / 24;

                $product_price = $op->product_price;

                //get factory gap day before start delivery of new order


                //Even though this was on delivery, they should be sent to the check waiting status
                //$status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING; //suggested
                //set status of origin status
                $status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED;

                $milkman_id = $plan->milkman_id;
                $station_id = $plan->station_id;

                $delivered_count = 0;

                $delivery_type = $op->delivery_type;

                $last_deliver_plan = $op->last_deliver_plan;//last deliver plan's start at
                if ($last_deliver_plan) {
                    //get total count and delivery type
                    if ($last_deliver_plan->id == $plan_id) {
                        $total_count = $diff;
                        $deliver_at = $last_deliver_plan->deliver_at;

                        $deliver_at = $this->get_next_delivery_day($delivery_type, $op, $deliver_at);
                    } else {
                        $total_count = $diff + $last_deliver_plan->delivery_count;//$total_count = $diff+last_deliver's deliver count
                        $last_deliver_plan->delete();
                        $deliver_at = $last_deliver_plan->deliver_at;
                    }

                    $stop_checked = false;
                    if ($exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                        $deliver_at = $restart_at;
                        $stop_checked = true;
                    }

                    //check deliver_at exist in stop_at and restart_at

                    if ($delivery_type == 1) {

                        //every day send
                        $plan_count = $op->count_per_day;
                        //changed plan count = plan acount
                        $changed_plan_count = $plan_count;
                        $interval = 1;

                        do {
                            $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                            if ($total_count < $plan_count)
                                $plan_count = $total_count;

                            $delivery_count = $plan_count;

                            $this->make_each_delivery_plan($milkman_id, $station_id, $order_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count,  $product_price);
                            $total_count -= $plan_count;
                            $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                            if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                                $deliver_at = $restart_at;
                                $stop_checked = true;
                            }

                        } while ($total_count > 0);
                    } else if ($delivery_type == 2) {
                        //each 2 days send
                        $plan_count = $op->count_per_day;
                        //changed plan count = plan acount
                        $changed_plan_count = $plan_count;
                        $interval = 2;

                        do {
                            $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                            if ($total_count < $plan_count)
                                $plan_count = $total_count;

                            $delivery_count = $plan_count;

                            $this->make_each_delivery_plan($milkman_id, $station_id, $order_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                            $total_count -= $plan_count;
                            $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                            if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                                $deliver_at = $restart_at;
                                $stop_checked = true;
                            }

                        } while ($total_count > 0);
                    } else if ($delivery_type == 3) {
                        //week day
                        $cod = $op->custom_order_dates;

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
                                $changed_plan_count = $plan_count;

                                $next_key = $this->get_next_key($custom, $key);

                                if ($next_key < $key) {
                                    $interval = $next_key + 7 - $key;
                                } else {
                                    $interval = $next_key - $key;
                                }

                                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                                if ($total_count < $plan_count)
                                    $plan_count = $total_count;

                                $delivery_count = $plan_count;

                                $this->make_each_delivery_plan($milkman_id, $station_id, $order_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                                $total_count -= $plan_count;
                                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                                if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                                    $deliver_at = $restart_at;
                                    $stop_checked = true;
                                }
                            } else {
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
                                $changed_plan_count = $plan_count;

                                $next_key = $this->get_next_key($custom, $key);
                                if ($next_key < $key) {
                                    $interval = $next_key + 7 - $key;
                                } else {
                                    $interval = $next_key - $key;
                                }

                                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                                if ($total_count < $plan_count)
                                    $plan_count = $total_count;

                                $delivery_count = $plan_count;

                                $this->make_each_delivery_plan($milkman_id, $station_id, $order_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count,  $product_price);
                                $total_count -= $plan_count;
                                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                                if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                                    $deliver_at = $restart_at;
                                    $stop_checked = true;
                                }
                            }
                        } while ($total_count > 0);
                    } else {
                        //month day
                        $cod = $op->custom_order_dates;
                        $daynums = $this->days_in_month($deliver_at);

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
                            $key = (new DateTime($deliver_at))->format('d');

                            if (array_key_exists($key, $custom)) {
                                $plan_count = $custom[$key];
                                //changed plan count = plan acount
                                $changed_plan_count = $plan_count;

                                $next_key = $this->get_next_key($custom, $key);

                                $month_days = cal_days_in_month(CAL_GREGORIAN, 10, 2016);

                                if ($next_key < $key) {
                                    $interval = $next_key + $daynums - $key;
                                } else {
                                    $interval = $next_key - $key;
                                }

                                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                                if ($total_count < $plan_count)
                                    $plan_count = $total_count;

                                $delivery_count = $plan_count;

                                $this->make_each_delivery_plan($milkman_id, $station_id, $order_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count,  $product_price);
                                $total_count -= $plan_count;
                                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                                $daynums = $this->days_in_month($deliver_at);

                                if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                                    $deliver_at = $restart_at;
                                    $daynums = $this->days_in_month($deliver_at);
                                    $stop_checked = true;
                                }
                            } else {
                                //get avaiable key value > current_key
                                $old_key = $key;

                                $key = $this->getClosestKey($key, $custom);
                                if ($key < $old_key)
                                    $first_interval = $key + 7 - $old_key;
                                else
                                    $first_interval = $key - $old_key;

                                $deliver_at = $this->get_deliver_at_day($deliver_at, $first_interval);
                                $daynums = $this->days_in_month($deliver_at);

                                $plan_count = $custom[$key];
                                //changed plan count = plan acount
                                $changed_plan_count = $plan_count;

                                $next_key = $this->get_next_key($custom, $key);
                                if ($next_key < $key) {
                                    $interval = $next_key + $daynums - $key;
                                } else {
                                    $interval = $next_key - $key;
                                }

                                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                                if ($total_count < $plan_count)
                                    $plan_count = $total_count;

                                $delivery_count = $plan_count;

                                $this->make_each_delivery_plan($milkman_id, $station_id, $order_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count,  $product_price);
                                $total_count -= $plan_count;
                                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                                $daynums = $this->days_in_month($deliver_at);

                                if (!$stop_checked && $exist_stop && (strtotime($deliver_at) >= $stop_at) && strtotime($deliver_at) < $restart_at) {
                                    $deliver_at = $restart_at;
                                    $daynums = $this->days_in_month($deliver_at);
                                    $stop_checked = true;
                                }
                            }
                        } while ($total_count > 0);
                    }

                } else {
                    $plan->changed_plan_count = $origin;
                    $plan->delivery_count = $origin;
                    $plan->save();
                    return false;
                }

            }

            return true;
        } else {
            //impossible to change the plan
            return false;
        }

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

            $notification = new DSNotification();
            $notification->sendToStationNotification($station_id, 7, "修改了单日", $customer_name . "用户修改了单日的交货计划改变！！");

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

            //get closet plan to today from order
//            $today_date = new DateTime("now",new DateTimeZone('Asia/Shanghai'));         $today =$today_date->format('Y-m-d');
            $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
            $today = $today_date->format('Y-m-d');

            $plans = MilkManDeliveryPlan::where('order_id', $order_id)->where('deliver_at', $today)->get();
            foreach ($plans as $plan) {
                $plan_id = $plan->id;
                $origin = $plan->changed_plan_count;
                $changed = 0;
                $diff = $changed - $origin;
                if ($diff == 0)
                    continue;
                $result = $this->change_delivery_plan($order_id, $plan_id, $diff);
            }

            $station_id = $order->station_id;
            $customer_name = Customer::find($order->customer_id)->name;

            $notification = new DSNotification();
            $notification->sendToStationNotification($station_id, 7, "修改了单日", $customer_name . "用户修改了单日的交货计划改变！！");

            return response()->json(['status' => 'success']);
        }

    }

    //restart stopped dingdan
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

            //Step1: First delete all delivery plans waiting or passed.
            $plans = MilkManDeliveryPlan::where('order_id', $order_id)
                ->where(function ($query) {
                    $query->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED);
                    $query->orWhere('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING);
                })->get();

            foreach ($plans as $plan) {
                $plan->delete();
            }

            //Step2: for the plans that has submitted to the production plans
            //change the changed_plan_count -> 0
            $plans = $order->delivery_plans_sent_to_production_plan;
            foreach ($plans as $plan) {
                $plan->changed_plan_count = 0;
                $plan->save();
            }

            //Step 3: Insert new record for stopped orders
            //These are plans after the stop end date

            $restart_date = $start_at;

            $order_products = $order->order_products;

            $result = true;
            foreach ($order_products as $op) {
                //make new delivery plans for each order product

                //Step a: get total counts of not delivered at
                //Total_count - finished_count
                $total = $op->total_count;

                $remain_for_stop_count = 0;
                $dps = MilkManDeliveryPlan::where('order_product_id', $op->id)->get();
                foreach ($dps as $dp) {
                    $remain_for_stop_count += $dp->changed_plan_count;
                }

                $todo = $total - $remain_for_stop_count;

                $rest = $this->make_new_delivery_plans($order_id, $op, $restart_date, $todo);
                if (!$rest) {
                    $result = false;
                    break;
                }
            }

            $order->restart_at = $start_at;
            $order->save();

            if ($result) {
                return response()->json(['status' => 'success', 'restart_at' => $start_at]);
            } else {
                return response()->json(['status' => 'fail', 'message' => '']);
            }

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
            $station_id = $order->station_id;

            if (strtotime($start_date) <= strtotime('today') && strtotime('today') <= strtotime($end_date)) {
                $order->status = Order::ORDER_STOPPED_STATUS;
                $order->save();
            }

            //Step1: First delete all delivery plans waiting or passed.
            $plans = MilkManDeliveryPlan::where('order_id', $order_id)
                ->where(function ($query) {
                    $query->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED);
                    $query->orWhere('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING);
                })->where('deliver_at', '>=', $start_date)->get();

            foreach ($plans as $plan) {
                $plan->delete();
            }

            //Step2: for the plans that has submitted to the production plans
            //change the changed_plan_count -> 0
            $plans = $order->delivery_plans_sent_to_production_plan;
            foreach ($plans as $plan) {
                $plan->changed_plan_count = 0;
                $plan->save();
            }

            //Step 3: Insert new record for stopped orders
            //These are plans after the stop end date

            $last = new DateTime($end_date);
            $last_date = $last->modify('+1 day');
            $restart_date = $last_date->format('Y-m-d');

            $order_products = $order->order_products;

            $result = true;
            foreach ($order_products as $op) {
                //make new delivery plans for each order product

                //Step a: get total counts of not delivered at
                //Total_count - finished_count
                $total = $op->total_count;

                $remain_for_stop_count = 0;
                $dps = MilkManDeliveryPlan::where('order_product_id', $op->id)->get();
                foreach ($dps as $dp) {
                    $remain_for_stop_count += $dp->changed_plan_count;
                }

                $todo = $total - $remain_for_stop_count;

                $rest = $this->make_new_delivery_plans($order_id, $op, $restart_date, $todo);
                if (!$rest) {
                    $result = false;
                    break;
                }
            }

            $order->stop_at = $start_date;
            $order->restart_at = $restart_date;

            $order->save();

            //Notification
            $customer_name = Customer::find(Order::find($order_id)->customer_id)->name;
            $notification = new DSNotification();
            $notification->sendToStationNotification($station_id, 7, "修改了单日", $customer_name . "用户修改了单日的订单暂停！！");

            if ($result)
                return response()->json(['status' => 'success', 'order_status' => $order->status, 'stop_start' => $start_date, 'stop_end' => $end_date]);
            else
                return response()->json(['status' => 'fail']);
        }
    }


    //make new delivery plan after restart_date
    function make_new_delivery_plans($order_id, $op, $restart_date, $todo)
    {
        //sd: start_date, ed: end_date, oid: order_id
        $order = Order::find($order_id);
        if (!$order)
            return false;

        $factory_id = $order->factory_id;
        $order_product_id = $op->id;
        $product_id = $op->product_id;
        $milkman_id = $order->milkman_id;
        $station_id = $order->delivery_station_id;

        //get product production_period and order_at production_period
        $product = Product::find($product_id);
        $production_period = ($product->production_period) / 24;

        $product_price = $op->product_price;

        //get factory gap day before start delivery of new order
        $factory = Factory::find($factory_id);
        $gap_day = $factory->gap_day;

        $status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING; //suggested

        $delivered_count = 0;

        /* based on delivery_type, factory_order_type
        * $op->order_type : total_count: 1 ->30,  2-> 90, 3-> 180
        * $op->delivery_type: 1 -- every day, 2- each twice day, 3- week, 4 - month
        * delivery_type
        *  1 => $deliver_at = $deliver_at + 1;
        *  2 => $deliver_at = $deliver_at + 2;
        *  3 => custom_order_dates (2:3,4:3,5:3,7:3)
        *  custom_order_dates: 1- Monday, 2-Tuesday, 3- wednesday, 4- Thurs, 5-Fri, 6-Saturday, 7- Sunday
        *  get current_day_of_week:
        *       $day = date('N', strtotime($delivery_date));
        *
        */

        //get total count and delivery type
        $total_count = $todo;
        $delivery_type = $op->delivery_type;

        $deliver_at = $restart_date;

        if ($delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY) {

            //every day send
            $plan_count = $op->count_per_day;
            $interval = 1;

            do {
                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                if ($total_count < $plan_count)
                    $plan_count = $total_count;

                $changed_plan_count = $plan_count;

                $delivery_count = $plan_count;

                $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                $total_count -= $plan_count;
                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

            } while ($total_count > 0);

        } else if ($delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY) {
            //each 2 days send
            $plan_count = $op->count_per_day;
            $interval = 2;

            do {
                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                if ($total_count < $plan_count)
                    $plan_count = $total_count;

                $changed_plan_count = $plan_count;
                $delivery_count = $plan_count;

                $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                $total_count -= $plan_count;
                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

            } while ($total_count > 0);
        } else if ($delivery_type == DeliveryType::DELIVERY_TYPE_WEEK) {
            //week day
            $cod = $op->custom_order_dates;

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

                    $next_key = $this->get_next_key($custom, $key);

                    if ($next_key < $key) {
                        $interval = $next_key + 7 - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_count < $plan_count)
                        $plan_count = $total_count;
                    $changed_plan_count = $plan_count;

                    $delivery_count = $plan_count;

                    $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                    $total_count -= $plan_count;
                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                } else {
                    //get avaiable key value > current_key
                    $old_key = $key;
                    $key = $this->getClosestKey($key, $custom);
                    if ($key < $old_key)
                        $first_interval = $key + 7 - $old_key;
                    else
                        $first_interval = $key - $old_key;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $first_interval);
                    $plan_count = $custom[$key];

                    $next_key = $this->get_next_key($custom, $key);
                    if ($next_key < $key) {
                        $interval = $next_key + 7 - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $changed_plan_count = $plan_count;

                    $delivery_count = $plan_count;

                    $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                    $total_count -= $plan_count;
                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                }
            } while ($total_count > 0);
        } else {
            //month day
            $cod = $op->custom_order_dates;
            $daynums = $this->days_in_month($deliver_at);
            $cod = rtrim($cod, ',');

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
                $key = (new DateTime($deliver_at))->format('d');

                if (array_key_exists($key, $custom)) {
                    $plan_count = $custom[$key];

                    $next_key = $this->get_next_key($custom, $key);

                    if ($next_key < $key) {
                        $interval = $next_key + $daynums - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_count < $plan_count)
                        $plan_count = $total_count;
                    $changed_plan_count = $plan_count;

                    $delivery_count = $plan_count;

                    $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                    $total_count -= $plan_count;
                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                    $daynums = $this->days_in_month($deliver_at);
                } else {
                    //get avaiable key value > current_key
                    $old_key = $key;

                    $key = $this->getClosestKey($key, $custom);

                    if ($key < $old_key)
                        $first_interval = $key + $daynums - $old_key;
                    else
                        $first_interval = $key - $old_key;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $first_interval);
                    $daynums = $this->days_in_month($deliver_at);

                    $plan_count = $custom[$key];

                    $next_key = $this->get_next_key($custom, $key);
                    if ($next_key < $key) {
                        $interval = $next_key + $daynums - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_count < $plan_count)
                        $plan_count = $total_count;
                    $changed_plan_count = $plan_count;

                    $delivery_count = $plan_count;

                    $this->make_each_delivery_plan_for_changed_order($milkman_id, $station_id, $order_id, $product_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count, $product_price);
                    $total_count -= $plan_count;
                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                    $daynums = $this->days_in_month($deliver_at);
                }
            } while ($total_count > 0);
        }
        return true;
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

        return view('naizhan.dingdan.xiugai', [
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

    private $factory;
    private $mFactoryId;

    private $order_property;
    private $province;
    private $products;
    private $factory_order_types;
    private $order_delivery_types;
    private $product_count_on_fot;
    private $delivery_stations;
    
    private $station;
    private $mStationId;

    /**
     * 初始化奶厂订单参数
     */
    private function initShowFactoryPage() {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;
        $this->factory = Factory::find($factory_id);

        $this->order_property = OrderProperty::all();

        $this->initBaseFromOrderInput();
    }

    /**
     * 初始化奶站订单参数
     */
    private function initShowStationPage() {

        $station_id = Auth::guard('naizhan')->user()->station_id;
        $this->station = DeliveryStation::find($station_id);

        $this->factory = Factory::find($this->station->factory_id);

        $this->initBaseFromOrderInput();
    }

    /**
     * 初始化订单录入的基础信息
     */
    private function initBaseFromOrderInput() {
        $this->order_property = OrderProperty::all();

        $this->products = $this->factory->active_products;
        $this->factory_order_types = $this->factory->factory_order_types;
        $this->order_delivery_types = $this->factory->order_delivery_types;
        $this->delivery_stations = $this->factory->active_stations;//get only active stations

        $this->province = Address::where('level', 1)->where('factory_id', $this->factory->id)
            ->where('parent_id', 0)->where('is_active', 1)->where('is_deleted', 0)->get();

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
        if ($order->order_checker->station) {
            $order_checkers = $order->order_checker->station->all_order_checkers;
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

    //get count of bottle to do
    public
    function get_rest_plans_count($oid, $pid)
    {
        $rest_with_this = 0;

        $order = Order::find($oid);
        if ($order) {
            $udps = $order->unfinished_delivery_plans;
            foreach ($udps as $udp) {
                if ($udp->id >= $pid) {
                    $rest_with_this += $udp->delivery_count;
                }
            }
        }

        return $rest_with_this;
    }

    //get next delivery day for order product from given deliver_at
    public
    function get_next_delivery_day($delivery_type, $op, $deliver_at)
    {

        //get next deliver at day
        if ($delivery_type == 1) {
            $deliver_at = $this->get_deliver_at_day($deliver_at, 1);


        } else if ($delivery_type == 2) {
            $deliver_at = $this->get_deliver_at_day($deliver_at, 2);
        } else if ($delivery_type == 3) {
            //week day
            $cod = $op->custom_order_dates;

            $cod = explode(',', $cod);
            $custom = [];
            foreach ($cod as $code) {
                $code = explode(':', $code);
                $key = $code[0];
                $value = $code[1];
                $custom[$key] = $value;
            }

            $key = date('N', strtotime($deliver_at));

            if (array_key_exists($key, $custom)) {

                $next_key = $this->get_next_key($custom, $key);

                if ($next_key < $key) {
                    $interval = $next_key + 7 - $key;
                } else {
                    $interval = $next_key - $key;
                }
                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
            } else {
                $old_key = $key;
                $key = $this->getClosestKey($key, $custom);
                if ($key < $old_key)
                    $first_interval = $key + 7 - $old_key;
                else
                    $first_interval = $key - $old_key;

                $deliver_at = $this->get_deliver_at_day($deliver_at, $first_interval);

                $next_key = $this->get_next_key($custom, $key);
                if ($next_key < $key) {
                    $interval = $next_key + 7 - $key;
                } else {
                    $interval = $next_key - $key;
                }
                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
            }

        } else {

            //month day
            $cod = $op->custom_order_dates;
            $daynums = $this->days_in_month($deliver_at);

            $cod = explode(',', $cod);
            $custom = [];
            foreach ($cod as $code) {
                $code = explode(':', $code);
                $key = $code[0];
                $value = $code[1];
                $custom[$key] = $value;
            }
            //custom week days

            //get key from day
            $key = (new DateTime($deliver_at))->format('d');

            if (array_key_exists($key, $custom)) {

                $next_key = $this->get_next_key($custom, $key);

                if ($next_key < $key) {
                    $interval = $next_key + $daynums - $key;
                } else {
                    $interval = $next_key - $key;
                }

                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                $daynums = $this->days_in_month($deliver_at);
            } else {
                //get avaiable key value > current_key
                $old_key = $key;
                $key = $this->getClosestKey($key, $custom);
                if ($key < $old_key)
                    $first_interval = $key + $daynums - $old_key;
                else
                    $first_interval = $key - $old_key;

                $deliver_at = $this->get_deliver_at_day($deliver_at, $first_interval);
                $daynums = $this->days_in_month($deliver_at);

                $next_key = $this->get_next_key($custom, $key);
                if ($next_key < $key) {
                    $interval = $next_key + $daynums - $key;
                } else {
                    $interval = $next_key - $key;
                }

                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                $daynums = $this->days_in_month($deliver_at);
            }
        }

        return $deliver_at;
    }


    function change_all_delivery_plans_price()
    {
        $dps = MilkManDeliveryPlan::all();
        foreach ($dps as $dp) {
            $op_id = $dp->order_product_id;
            if ($op_id) {
                $op = OrderProduct::find($op_id);
                if ($op) {
                    $dp->product_price = $op->product_price;
                    $dp->save();
                }
            }
        }


    }


    //change customer info in order xiugai page
    public
    function change_customer(Request $request)
    {
        //save changed customer info


        if ($request->ajax()) {

            
            $from_station = false;
            $fuser = Auth::guard('gongchang')->user();
            if (!$fuser) {
                $station_id = Auth::guard('naizhan')->user()->station_id;
                $station = DeliveryStation::find($station_id);
                $factory_id = $station->factory_id;
                $from_station = true;
            } else {
                $factory_id = $fuser->factory_id;
            }

            $factory = Factory::find($factory_id);

            $order_id = $request->input('order_id');
            $customer_id = $request->input('customer_id');

            $province = $request->input('c_province');
            $city = $request->input('c_city');
            $district = $request->input('c_district');
            $street = $request->input('c_street');
            $xiaoqu = $request->input('c_xiaoqu');
            $sub_addr = $request->input('c_sub_addr');

            $order = Order::find($order_id);
            $customer = Customer::find($customer_id);
            $customer_name = $customer->name;//for notification

            //get origin milkman and station
            $origin_delivery_station_id = $order->delivery_station_id;
            $origin_milkman_id = $customer->milkman_id;

            // Find the station and milkman that can delivery this customer's request
            $addr = $province . ' ' . $city . ' ' . $district . ' ' . $street . ' ' . $xiaoqu . ' ' . $sub_addr;
            $d_addr = $province . ' ' . $city . ' ' . $district . ' ' . $street . ' ' . $xiaoqu;

            $station_milkman = $this->get_station_milkman_with_address_from_factory($factory_id, $d_addr);

            if ($station_milkman == $this::NOT_EXIST_DELIVERY_AREA) {
                return response()->json(['status' => 'fail', 'message' => '客户并不住在可以递送区域.']);
            } else if ($station_milkman == $this::NOT_EXIST_STATION) {
                return response()->json(['status' => 'fail', 'message' => '没有奶站.']);
            } else if ($station_milkman == $this::NOT_EXIST_MILKMAN) {
                return response()->json(['status' => 'fail', 'message' => '没有递送人.']);
            }

            foreach ($station_milkman as $delivery_station_id => $milkman_id) {
                if ($origin_delivery_station_id == $delivery_station_id) {

                    if ($origin_milkman_id == $milkman_id) {

                        //Only need to save new address to customer and order
                        $customer->address = $addr;
                        $customer->save();

                        $order->address = $addr;
                        $order->save();

                    } else {
                        //Change delivery plan to new_milkman_id
                        //here: for the plans to do
                        $customer->address = $addr;
                        $customer->save();

                        $order->address = $addr;
                        $order->save();

                        $udps = $order->unfinished_delivery_plans;
                        foreach ($udps as $udp) {
                            $udp->milkman_id = $milkman_id;
                            $udp->save();
                        }
                    }

                    $milkman = MilkMan::find($milkman_id);
                    $milkman_name = $milkman->name;
                    $milkman_phone = $milkman->phone;

                    $customer_id = $customer->id;
                    $get_station = DeliveryStation::find($delivery_station_id);
                    $station_name = $get_station->name;

                    $notification = new DSNotification();
                    $notification->sendToStationNotification($delivery_station_id, 7, "修改了单日", $customer_name . "用户修改了单日的订单信息！！");

                    return response()->json(['status' => 'success', 'customer_id'=>$customer_id, 'milkman_id'=>$milkman_id,
                        'milkman_name' => $milkman_name, 'milkman_phone'=>$milkman_phone,
                        'station_id'=>$delivery_station_id, 'station_name'=>$station_name]);

                } else {

                    //change customer info
                    $customer->address = $addr;
                    $customer->station_id = $delivery_station_id;
                    $customer->milkman_id = $milkman_id;
                    $customer->save();

                    $customer_id = $customer->id;

                    //change order info
                    $order->address = $addr;
                    if($from_station)
                    {
                        $order->station_id = $station_id;
                    } else {
                        $order->station_id = $delivery_station_id;
                    }
                    $order->delivery_station_id = $delivery_station_id;
                    $order->save();

                    $amount = $this->get_not_finished_amount_order($order);
                    if ($amount == 0) {
                        return response()->json(['status' => 'fail']);
                    }

                    //Make new delivery plan
                    $order_products = $order->order_products;
                    foreach ($order_products as $op) {

                        $pid = $op->product_id;
                        $otype = $op->order_type;

                        $new_price = $this->get_product_price_by_pcd($pid, $otype, $province, $city, $district);

                        if ($new_price) {

                            $op->product_price = $new_price;
                            $op->save();

                            $amount = $this->establish_new_plan_with_money_amount($op, $factory_id, $delivery_station_id, $milkman_id, $amount);
                            if ($amount < $new_price) {
                                $customer->remain_amount += $amount;
                                $customer->save();
                                $order->total_amount -= $amount;
                                $order->save();
                                break;
                            }
                        } else {
                            return response()->json(['status' => 'fail']);
                        }

                    }
                    //As the order has changed, this should be daishenhe
                    $order->status = Order::ORDER_WAITING_STATUS;
                    $order->save();

                    $milkman = MilkMan::find($milkman_id);
                    $milkman_name = $milkman->name;
                    $milkman_phone = $milkman->phone;


                    //set flag on first order delivery plan
                    $plans = $order->first_delivery_plans;
                    if($plans)
                    {
                        foreach($plans as $plan)
                        {
                            $plan->flag = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER_RULE_CHANGE;
                            $plan->save();
                        }
                    }

                    $get_station = DeliveryStation::find($delivery_station_id);
                    $station_name= $get_station->name;
                    $notification = new DSNotification();
                    $notification->sendToStationNotification($delivery_station_id, 7, "修改了单日", $customer_name . "用户修改了单日的订单信息！！");

                    return response()->json([
                        'status' => 'success',
                        'customer_id'=>$customer_id,
                        'milkman_id'=>$milkman_id,
                        'milkman_name' => $milkman_name,
                        'milkman_phone'=>$milkman_phone,
                        'station_id'=>$delivery_station_id,
                        'station_name'=>$station_name
                    ]);
                }

            }
        }
    }

    //Change Order Products in order xiugai page
    public function change_order_info(Request $request)
    {
        if ($request->ajax()) {

            $fuser = Auth::guard('gongchang')->user();
            if (!$fuser) {
                $station_id = Auth::guard('naizhan')->user()->station_id;
                $station = DeliveryStation::find($station_id);
                $factory_id = $station->factory_id;
            } else {
                $factory_id = $fuser->factory_id;
            }

            $order_id = $request->input('order_id');

            $order = Order::find($order_id);

            $init_total_amount = $request->input('init_order_total');
            $current_total_amount = $request->input('current_order_total');

            //Step1-c: Get remaining total amount and delete order product
            $remain_amount = $order->remain_order_money;

            $customer_id = $request->input('customer_id');
            $receipt_number = $request->input('receipt_number');
            $receipt_path = $request->input('receipt_path');
            $milkman_id = $request->input('milkman_id');
            $station_id = $request->input('station_id');

            //check integrity

            $customer = Customer::find($customer_id);
            $customer_name = $customer->name;
            if (!$order)
                return response()->json(['status' => 'fail', 'message' => '该订单没有被发现']);
            if (!$customer)
                return response()->json(['status' => 'fail', 'message' => '该收货人没有被发现']);

            if ($order->customer_id != $customer_id) {
                $order->customer_id = $customer_id;
            }

            if ($order->receipt_number != $receipt_number) {
                $order->receipt_number = $receipt_number;
            }

            if ($order->receipt_path != $receipt_path) {
                $order->receipt_path = $receipt_path;
            }

            if ($customer->milkman_id != $milkman_id) {
                $order->milkman_id = $milkman_id;
            }

            if ($customer->station_id != $station_id) {
                $order->station_id = $station_id;
                $notification = new DSNotification();
                $notification->sendToStationNotification($station_id, 7, "修改了单日", $customer_name . "用户修改了单日的订单奶站！！");
            }

            $order->save();

            //delete origin order products
            $this->delete_all_order_products_and_delivery_plans_for_update_order($order);

            //Step2: Make new delivery plan with new product, new delivery plan
            //save order products
            $count = count($request->input('order_product_id'));

            //establish plan
            for ($i = 0; $i < $count; $i++) {
                $pid = $request->input('order_product_id')[$i];
                $otype = $request->input('factory_order_type')[$i];
                $total_count = $request->input('one_product_total_count')[$i];
                $one_amount = $request->input('one_p_amount')[$i];
                $product_price = $this->get_product_price_by_cid($pid, $otype, $customer_id);
                $delivery_type = $request->input('order_delivery_type')[$i];
                $avg = $request->input('avg')[$i];

                $op = new OrderProduct;
                $op->order_id = $order_id;
                $op->product_id = $pid;
                $op->order_type = $request->input('factory_order_type')[$i];
                $op->delivery_type = $delivery_type;
                $op->product_price = $product_price;
                $op->total_count = $total_count;
                $op->total_amount = $one_amount;
                $op->avg = $avg;

                if ($delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY || $delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY) {
                    $op->count_per_day = $request->input('order_product_count_per')[$i];
                } else {
                    $custom_dates = $request->input('delivery_dates')[$i];
//                    if ($delivery_type == DeliveryType::DELIVERY_TYPE_WEEK) {
//                        //Week Delivery
//                        $result = $this->get_week_delivery_info($custom_dates);
//
//                    } else {
//                        //Month Delivery
//                        $result = $this->get_month_delivery_info($custom_dates);
//                    }
                    $result = trim($custom_dates);
                    $op->custom_order_dates = $result;
                }

                $op->save();

                //establish plan
                $this->establish_new_plan($op, $factory_id, $station_id, $milkman_id);
            }

            //set flag on first order delivery plan
            $plans = $order->first_delivery_plans;
            if($plans)
            {
                foreach($plans as $plan)
                {
                    $plan->flag = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER_RULE_CHANGE;
                    $plan->save();
                }

            }

            $order->status = Order::ORDER_WAITING_STATUS;
            $order->save();

            $station_id = Order::find($order_id)->station_id;
            $customer_name = Customer::find(Order::find($order_id)->customer_id)->name;

            $notification = new DSNotification();
            $notification->sendToStationNotification($station_id, 7, "修改了单日", $customer_name . "用户修改了单日的订单奶品！！");
            return response()->json(['status' => 'success']);
        }
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


//Show stopped one order
    public
    function show_stopped_order_in_naizhan($order_id)
    {
        $station_id = Auth::guard('naizhan')->user()->station_id;
        $station = DeliveryStation::find($station_id);
        $factory_id = $station->factory_id;
        $factory = Factory::find($factory_id);

        $order = Order::find($order_id);
        $order_products = $order->order_products;
        $grouped_delivery_plans = $order->grouped_delivery_plans;

        $mine = 0;
        if ($order->station_id == $station_id or $order->delivery_station_id == $station_id) {
            $mine = 1;
        }

        $child = 'zantingliebiao';
        $parent = 'dingdan';
        $current_page = 'zanting';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.zantingliebiao.zanting', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'order' => $order,
            'order_products' => $order_products,
            'grouped_delivery_plans' => $grouped_delivery_plans,
            'mine' => $mine,
        ]);
    }


//Insert Xudan Order in Gongchang
    function insert_xudan_order_in_gongchang(Request $request)
    {
        if ($request->ajax()) {
            //factory id
            $fuser = Auth::guard('gongchang')->user();
            $factory_id = $fuser->factory_id;
            $factory = Factory::find($factory_id);

            //init
            $milk_card_id = null;
            $milk_card_code = null;

            //insert customer info
            $customer_id = $request->input('customer_id');
            $customer = Customer::find($customer_id);
            $phone = $customer->phone;
            $address = $customer->address;

            //whether this is new order or old order
            $order_property_id = OrderProperty::ORDER_PROPERTY_XUDAN_ORDER;

            //station info
            $station_id = $request->input('station');
            $order_checker_id = $request->input('order_checker');
            $receipt_number = $request->input('receipt_number');
            $receipt_path = $request->input('receipt_path');

            //order info
            $start_at = $request->input('start_at');
            $start_at = date_create($start_at);

            $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
            $today = $today_date->format('Y-m-d');
            $today = date_create($today);

            $diff = date_diff($start_at, $today);
            $diff = intval($diff->days);

            $gap_day = intval($factory->gap_day);
            if ($diff < $gap_day) {
                return response()->json(['status' => 'fail', 'message' => '实现' . $gap_day . '天后，订单开始日期.']);
            }

            $milk_box_install = $request->input('milk_box_install') == "on" ? 1 : 0;

            $order_by_milk_card = $request->input('milk_card_check') == "on" ? 1 : 0;
            if ($order_by_milk_card == 1) {
                $milk_card_id = $request->input('card_id');
                $milk_card_code = $request->input('card_code');
            }
            if ($order_by_milk_card == 0)
                $payment_type = PaymentType::PAYMENT_TYPE_MONEY_NORMAL;
            elseif ($order_by_milk_card == 1)
                $payment_type = PaymentType::PAYMENT_TYPE_CARD;//card
            else
                $payment_type = PaymentType::PAYMENT_TYPE_WECHAT;//wechat

            $delivery_time = $request->input('delivery_noon');

            $total_amount = $request->input('total_amount');

            //Card Check
            if ($order_by_milk_card == 1) {
                $milk_card = MilkCard::where('number', $milk_card_id)->where('password', $milk_card_code)->where('sale_status', MilkCard::MILKCARD_SALES_ON)->get()->first();
                if (!$milk_card) {
                    return response()->json(['status' => 'fail', 'message' => '奶卡卡号和验证码不正确.']);
                }
            }

            //check for 10% of delivery credit balance
            $station = DeliveryStation::find($station_id);
            if ((!$order_by_milk_card) && ($station->init_delivery_credit_amount + $station->delivery_credit_balance - $total_amount) < ($station->init_delivery_credit_amount / 10))
                return response()->json(['status' => 'fail', 'message' => '该站应保持高于其交割信用余额10％的货币.']);

            //other data
            $status = Order::ORDER_WAITING_STATUS;

            $trans_check = 0;
            $ordered_at = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');
            $delivery_station_id = $request->input('station');

            //flatenter mode: default 2 -> call
            $flat_enter_mode_id = Order::ORDER_FLAT_ENTER_MODE_CALL_DEFAULT;//by call

            $order = new Order;
            $order->factory_id = $factory_id;
            $order->customer_id = $customer_id;
            $order->phone = $phone;
            $order->address = $address;
            $order->order_property_id = $order_property_id;
            $order->station_id = $station_id;
            $order->receipt_number = $receipt_number;
            $order->receipt_path = $receipt_path;
            $order->order_checker_id = $order_checker_id;
            $order->milk_box_install = $milk_box_install;
            $order->total_amount = $total_amount;
            $order->remaining_amount = $total_amount;
            $order->order_by_milk_card = $order_by_milk_card;
            if ($order_by_milk_card) {
                $order->milk_card_id = $milk_card_id;
                $order->milk_card_code = $milk_card_code;
            }

            $order->trans_check = $trans_check;
            $order->payment_type = $payment_type;
            $order->status = $status;
            $order->ordered_at = $ordered_at;
            $order->start_at = $start_at;
            $order->delivery_time = $delivery_time;
            $order->flat_enter_mode_id = $flat_enter_mode_id;
            $order->delivery_station_id = $delivery_station_id;

            $order->save();

            //If Card is used, then set active flag for usage of card
            if ($order_by_milk_card == 1) {
                $milk_card->pay_status = MilkCard::MILKCARD_PAY_STATUS_ACTIVE;
                $milk_card->save();
            }

            $order_id = $order->id;
            $order->number = $this->order_number($factory_id, $station_id, $customer_id, $order_id);
            //order's unique number: format (F_fid_S_sid_C_cid_O_orderid)
            $order->save();

            //save order products
            $count = count($request->input('order_product_id'));

            $milkman_id = $order->milkman_id;


            if (!$milkman_id) {
                $order->delete();
                return response()->json(['status' => 'fail', 'message' => '找不到合适的配送员']);
            }

            for ($i = 0; $i < $count; $i++) {
                $pid = $request->input('order_product_id')[$i];
                $otype = $request->input('factory_order_type')[$i];
                $total_count = $request->input('one_product_total_count')[$i];
                $one_amount = $request->input('one_p_amount')[$i];
                $product_price = $this->get_product_price_by_cid($pid, $otype, $customer_id);
                $delivery_type = $request->input('order_delivery_type')[$i];
                $avg = $request->input('avg')[$i];

                $op = new OrderProduct;
                $op->order_id = $order_id;
                $op->product_id = $pid;
                $op->order_type = $request->input('factory_order_type')[$i];
                $op->delivery_type = $delivery_type;
                $op->product_price = $product_price;
                $op->total_count = $total_count;
                $op->total_amount = $one_amount;
                $op->avg = $avg;

                if ($delivery_type == "1" || $delivery_type == "2") {
                    $op->count_per_day = $request->input('order_product_count_per')[$i];
                } else {
                    $custom_dates = $request->input('delivery_dates')[$i];
//                    if ($delivery_type == "3") {
//                        //Week Delivery
//                        $result = $this->get_week_delivery_info($custom_dates);
//
//                    } else {
//                        //Month Delivery
//                        $result = $this->get_month_delivery_info($custom_dates);
//                    }
                    $result = trim($custom_dates);
                    $op->custom_order_dates = $result;
                }

                $op->save();

                //establish plan
                $this->establish_plan($op, $factory_id, $station_id, $milkman_id);
            }

            //set flag on first order delivery plan
            $plans = $order->first_delivery_plans;
            if($plans)
            {
                foreach($plans as $plan)
                {
                    $plan->flag = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER;
                    $plan->save();
                }

            }

            $remaining_amount = $request->input('remaining');
            $acceptable_amount = $request->input('acceptable_amount');

            $customer = Customer::find($customer_id);

            if ($acceptable_amount < 0) {
                $customer->remain_amount = -$acceptable_amount;

            } else {

                $customer->remain_amount = 0;
            }
            $customer->save();

            //Caiwu Related

            //When order save, decrease the delivery credit balance and change milkcard status if this is card  order.

            if (!$order_by_milk_card) {
                $station->delivery_credit_balance = $station->delivery_credit_balance - $total_amount;
                $station->save();

                //add calc history if this is the money order
                $dsdelivery_history = new DSDeliveryCreditBalanceHistory;
                $dsdelivery_history->station_id = $station_id;
                if ($station_id == $delivery_station_id)
                    $dsdelivery_history->type = DSDeliveryCreditBalanceHistory::DSDCBH_TYPE_IN_MONEY;
                else
                    $dsdelivery_history->type = DSDeliveryCreditBalanceHistory::DSDCBH_TYPE_OUT_OTHER_STATION;

                $dsdelivery_history->io_type = DSDeliveryCreditBalanceHistory::DSDCBH_IO_TYPE_IN;

                if ($acceptable_amount > 0)
                    $dsdelivery_history->amount = $acceptable_amount;
                else
                    $dsdelivery_history->amount = $total_amount;

                $dsdelivery_history->time = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');
                $dsdelivery_history->save();
            }

            return response()->json(['status' => 'success', 'order_id' => $order_id]);
        }
    }

//Insert Xudan Order in Naizhan
    function insert_xudan_order_in_naizhan(Request $request)
    {
        if ($request->ajax()) {

            //factory id and station id
            $station_id = Auth::guard('naizhan')->user()->station_id;
            $station = DeliveryStation::find($station_id);
            $factory_id = $station->factory_id;
            $factory = Factory::find($factory_id);

            //insert customer info
            $customer_id = $request->input('customer_id');
            $customer = Customer::find($customer_id);
            $phone = $customer->phone;
            $address = $customer->address;

            //whether this is new order or old order
            $order_property_id = OrderProperty::ORDER_PROPERTY_XUDAN_ORDER;

            //station info
            $order_checker_id = $request->input('order_checker');
            $receipt_number = $request->input('receipt_number');
            $receipt_path = $request->input('receipt_path');

            //order info
            $started_at = $request->input('started_at');
            $started_at = date_create($started_at);

            $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
            $today = $today_date->format('Y-m-d');
            $today = date_create($today);

            $diff = date_diff($started_at, $today);
            $diff = intval($diff->days);

            $gap_day = intval($factory->gap_day);
            if ($diff < $gap_day) {
                return response()->json(['status' => 'fail', 'message' => '实现' . $gap_day . '天后，订单开始日期.']);
            }

            $milk_box_install = $request->input('milk_box_install') == "on" ? 1 : 0;

            $order_by_milk_card = 0;

            if ($order_by_milk_card == 0)
                $payment_type = PaymentType::PAYMENT_TYPE_MONEY_NORMAL;
            else
                $payment_type = PaymentType::PAYMENT_TYPE_WECHAT;//wechat

            $delivery_time = $request->input('delivery_noon');

            $total_amount = $request->input('total_amount');

            $delivery_station_id = $request->input('station');

            //check for 10% of delivery credit balance
            $station = DeliveryStation::find($delivery_station_id);
            if ((!$order_by_milk_card) && ($station->init_delivery_credit_amount + $station->delivery_credit_balance - $total_amount) < ($station->init_delivery_credit_amount / 10))
                return response()->json(['status' => 'fail', 'message' => '该站应保持高于其交割信用余额10％的货币.']);

            //other data
            $status = Order::ORDER_WAITING_STATUS;
            $trans_check = 0;
            $ordered_at = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');

            //flatenter mode: default 2 -> call
            $flat_enter_mode_id = Order::ORDER_FLAT_ENTER_MODE_CALL_DEFAULT;//by call

            $order = new Order;
            $order->factory_id = $factory_id;
            $order->customer_id = $customer_id;
            $order->phone = $phone;
            $order->address = $address;
            $order->order_property_id = $order_property_id;
            $order->station_id = $station_id;
            $order->receipt_number = $receipt_number;
            $order->receipt_path = $receipt_path;
            $order->order_checker_id = $order_checker_id;
            $order->milk_box_install = $milk_box_install;
            $order->total_amount = $total_amount;
            $order->remaining_amount = $total_amount;
            $order->order_by_milk_card = $order_by_milk_card;
            $order->trans_check = $trans_check;
            $order->payment_type = $payment_type;
            $order->status = $status;
            $order->ordered_at = $ordered_at;
            $order->start_at = $started_at;
            $order->delivery_time = $delivery_time;
            $order->flat_enter_mode_id = $flat_enter_mode_id;
            $order->delivery_station_id = $delivery_station_id;

            $order->save();

            $order_id = $order->id;
            $order->number = $this->order_number($factory_id, $station_id, $customer_id, $order_id);
            //order's unique number: format (F_fid_S_sid_C_cid_O_orderid)
            $order->save();

            //save order products
            $count = count($request->input('order_product_id'));

            $milkman_id = $order->milkman_id;


            if (!$milkman_id) {
                $order->delete();
                return response()->json(['status' => 'fail', 'message' => '找不到合适的配送员']);
            }

            for ($i = 0; $i < $count; $i++) {
                $pid = $request->input('order_product_id')[$i];
                $otype = $request->input('factory_order_type')[$i];
                $total_count = $request->input('one_product_total_count')[$i];
                $one_amount = $request->input('one_p_amount')[$i];
                $product_price = $this->get_product_price_by_cid($pid, $otype, $customer_id);
                $delivery_type = $request->input('order_delivery_type')[$i];
                $avg = $request->input('avg')[$i];

                $op = new OrderProduct;
                $op->order_id = $order_id;
                $op->product_id = $pid;
                $op->order_type = $request->input('factory_order_type')[$i];
                $op->delivery_type = $delivery_type;
                $op->product_price = $product_price;
                $op->total_count = $total_count;
                $op->total_amount = $one_amount;
                $op->avg = $avg;

                if ($delivery_type == "1" || $delivery_type == "2") {
                    $op->count_per_day = $request->input('order_product_count_per')[$i];
                } else {
                    $custom_dates = $request->input('delivery_dates')[$i];
//                    if ($delivery_type == "3") {
//                        //Week Delivery
//                        $result = $this->get_week_delivery_info($custom_dates);
//
//                    } else {
//                        //Month Delivery
//                        $result = $this->get_month_delivery_info($custom_dates);
//                    }
                    $result = trim($custom_dates);
                    $op->custom_order_dates = $result;
                }

                $op->save();

                //establish plan
                $this->establish_plan($op, $factory_id, $station_id, $milkman_id);
            }


            //set flag on first order delivery plan
            $plans = $order->first_delivery_plans;
            if($plans)
            {
                foreach($plans as $plan)
                {
                    $plan->flag = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER;
                    $plan->save();
                }

            }

            $remaining_amount = $request->input('remaining');
            $acceptable_amount = $request->input('acceptable_amount');

            $customer = Customer::find($customer_id);

            if ($acceptable_amount < 0) {
                $customer->remain_amount = -$acceptable_amount;

            } else {

                $customer->remain_amount = 0;
            }
            $customer->save();

            //Caiwu Related

            //When order save, decrease the delivery credit balance and change milkcard status if this is card  order.

            if (!$order_by_milk_card) {
                $station->delivery_credit_balance = $station->delivery_credit_balance - $total_amount;
                $station->save();

                //add calc history if this is the money order
                $dsdelivery_history = new DSDeliveryCreditBalanceHistory;
                $dsdelivery_history->station_id = $station_id;
                if ($station_id == $delivery_station_id)
                    $dsdelivery_history->type = DSDeliveryCreditBalanceHistory::DSDCBH_TYPE_IN_MONEY;
                else
                    $dsdelivery_history->type = DSDeliveryCreditBalanceHistory::DSDCBH_TYPE_OUT_OTHER_STATION;

                $dsdelivery_history->io_type = DSDeliveryCreditBalanceHistory::DSDCBH_IO_TYPE_IN;

                if ($acceptable_amount > 0)
                    $dsdelivery_history->amount = $acceptable_amount;
                else
                    $dsdelivery_history->amount = $total_amount;

                $dsdelivery_history->time = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');
                $dsdelivery_history->save();
            }


            return response()->json(['status' => 'success', 'order_id' => $order_id]);
        }
    }

    /**
     * 录入/修改订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function insert_order(Request $request) {

        $order = null;
        $factory_id = $station_id = 0;

        if ($this->factory) {
            $factory_id = $this->factory->id;
        }
        if ($this->station) {
            $station_id = $this->station->id;
        }

        // 获取今日日期
        $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $today = $today_date->format('Y-m-d');
        $today = date_create($today);

        // 录入订单还是修改订单
        $order_id = $request->input('order_id');
        if ($order_id) {
            $order = Order::find($order_id);
        }

        //init
        $milk_card_id = null;
        $milk_card_code = null;

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
        $delivery_station_id = $request->input('station');

        // 如果是奶厂订单，配送奶站就是录入奶站
        if ($station_id == 0) {
            $station_id = $delivery_station_id;
        }

        if (!$milkman_id) {
            return response()->json(['status' => 'fail', 'message' => '找不到合适的配送员']);
        }

        $order_checker_id = $request->input('order_checker');
        $receipt_number = $request->input('receipt_number');
        $receipt_path = $request->input('receipt_path');

        //Order amount for remaingng, acceptable
        $remaining_amount = $request->input('remaining');
        $acceptable_amount = $request->input('acceptable_amount');
        $total_amount = $request->input('total_amount');

        $delivery_time = $request->input('delivery_noon');

        //order info
        $order_start_at = $request->input('order_start_at');
        $order_start_at = date_create($order_start_at);

        $ordered_at = $today;

//            $diff = date_diff($order_start_at, $today);
//            $diff = intval($diff->days);
//
//            $gap_day = intval($factory->gap_day);
////            if ($diff < $gap_day) {
////                return response()->json(['status' => 'fail', 'message' => '实现' . $gap_day . '天后，订单开始日期.']);
////            }

        $milk_box_install = $request->input('milk_box_install') == "on" ? 1 : 0;
        $payment_type = PaymentType::PAYMENT_TYPE_MONEY_NORMAL;

        // 奶卡订单录入
        $order_by_milk_card = $request->input('milk_card_check') == "on" ? 1 : 0;

        if ($order_by_milk_card == 1) {

            $payment_type = PaymentType::PAYMENT_TYPE_CARD;

            // 奶卡只在录入订单时处理
            if (!$order) {
                $milk_card_id = $request->input('card_id');
                $milk_card_code = $request->input('card_code');

                $milk_card = MilkCard::where('number', $milk_card_id)
                    ->where('password', $milk_card_code)
                    ->where('sale_status', MilkCard::MILKCARD_SALES_ON)
                    ->where('pay_status', MilkCard::MILKCARD_PAY_STATUS_INACTIVE)
                    ->get()
                    ->first();

                if (!$milk_card) {
                    return response()->json(['status' => 'fail', 'message' => '奶卡卡号和验证码不正确.']);
                }

                //balance check
                if ($milk_card->balance < $total_amount) {
                    return response()->json(['status' => 'fail', 'message' => '卡钱比订单钱少.']);
                }
                else {
                    $milk_card->pay_status = MilkCard::MILKCARD_PAY_STATUS_ACTIVE;
                    $milk_card->save();

                    $remain_from_card = $milk_card->balance - $total_amount;
                }
            }
        }

        //check for 10% of delivery credit balance
        $station = DeliveryStation::find($station_id);

        if (!$order_by_milk_card) {
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

                    return response()->json(['status' => 'fail', 'message' => '该站应保持高于其交割信用余额10％的货币.']);
                }
            }
        }

        // 状态, 默认是是待审核新订单
        $status = Order::ORDER_NEW_WAITING_STATUS;

        // 新订单未通过和新订单录入，就待审核
        if ($order && $order->isNewPassed()) {
            $status = Order::ORDER_WAITING_STATUS;
        }

        //other data
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
            if ($order_by_milk_card) {
                $order->milk_card_id = $milk_card_id;
                $order->milk_card_code = $milk_card_code;
            }

            $order->total_amount = $total_amount;

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
            $order->remaining_amount = $total_amount;
        }

        $order->customer_id = $customer_id;
        $order->phone = $phone;
        $order->address = $address;
        $order->order_property_id = $order_property_id;
        $order->station_id = $station_id;
        $order->receipt_number = $receipt_number;
        $order->receipt_path = $receipt_path;
        $order->order_checker_id = $order_checker_id;
        $order->milk_box_install = $milk_box_install;
        $order->trans_check = $trans_check;
        $order->payment_type = $payment_type;
        $order->status = $status;
        $order->start_at = $order_start_at;
        $order->delivery_time = $delivery_time;
        $order->flat_enter_mode_id = $flat_enter_mode_id;
        $order->delivery_station_id = $delivery_station_id;

        $order->save();

        // 订单修改要删除以前的配送明细和奶品信息
        if ($order_id) {
            $this->delete_all_order_products_and_delivery_plans_for_update_order($order);
        }
        // 新订单生成订单编号
        else {
            $order->number = $this->order_number($factory_id, $station_id, $customer_id, $order->id);
            //order's unique number: format (F_fid_S_sid_C_cid_O_orderid)
            $order->save();
        }

        //save order products
        $count = count($request->input('order_product_id'));

        for ($i = 0; $i < $count; $i++) {
            $pid = $request->input('order_product_id')[$i];
            $otype = $request->input('factory_order_type')[$i];
            $total_count = $request->input('one_product_total_count')[$i];
            $one_amount = $request->input('one_p_amount')[$i];
            $product_price = $this->get_product_price_by_cid($pid, $otype, $customer_id);
            $delivery_type = $request->input('order_delivery_type')[$i];
            $avg = $request->input('avg')[$i];
            $product_start_at = $request->input('start_at')[$i];

            $op = new OrderProduct;
            $op->order_id = $order->id;
            $op->product_id = $pid;
            $op->order_type = $request->input('factory_order_type')[$i];
            $op->delivery_type = $delivery_type;
            $op->product_price = $product_price;
            $op->total_count = $total_count;
            $op->total_amount = $one_amount;
            $op->avg = $avg;
            $op->start_at = $product_start_at;

            $op->count_per_day = $request->input('order_product_count_per')[$i];

            if ($delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY || $delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY) {   // 天天送、隔日送
//                    $op->count_per_day = $request->input('order_product_count_per')[$i];
            }
            else {
                $custom_dates = $request->input('delivery_dates')[$i];
//                    if ($delivery_type == "3") {
//                        //Week Delivery
//                        $result = $this->get_week_delivery_info($custom_dates);
//
//                    } else {
//                        //Month Delivery
//                        $result = $this->get_month_delivery_info($custom_dates);
//                    }
                $result = rtrim($custom_dates, ',');
                $op->custom_order_dates = $result;
            }

            $op->save();

            //establish plan
            $this->establish_plan($op, $factory_id, $delivery_station_id, $milkman_id);
        }

        //set flag on first order delivery plan
        $plans = $order->first_delivery_plans;
        if($plans)
        {
            foreach($plans as $plan)
            {
                $plan->flag = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER;
                $plan->save();
            }
        }

        // save customer
        $customer = Customer::find($customer_id);
        $customer->station_id = $delivery_station_id;
        $customer->milkman_id = $milkman_id;

//            if ($acceptable_amount < 0) {
//                $customer->remain_amount = -$acceptable_amount;
//            }
//            else {
//                $customer->remain_amount = 0;
//            }
        $customer->save();

        //Caiwu Related

        //When order save, decrease the delivery credit balance and change milkcard status if this is card  order.
        if ($status = Order::ORDER_NEW_WAITING_STATUS && !$order_by_milk_card) {

            $station->delivery_credit_balance = $station->delivery_credit_balance - $total_amount;
            $station->save();

//                //add calc history if this is the money order
//                $dsdelivery_history = new DSDeliveryCreditBalanceHistory;
//                $dsdelivery_history->station_id = $station_id;
//                if ($station_id == $delivery_station_id)
//                    $dsdelivery_history->type = DSDeliveryCreditBalanceHistory::DSDCBH_TYPE_IN_MONEY;
//                else
//                    $dsdelivery_history->type = DSDeliveryCreditBalanceHistory::DSDCBH_TYPE_OUT_OTHER_STATION;
//
//                $dsdelivery_history->io_type = DSDeliveryCreditBalanceHistory::DSDCBH_IO_TYPE_IN;
//
//                if ($acceptable_amount > 0)
//                    $dsdelivery_history->amount = $acceptable_amount;
//                else
//                    $dsdelivery_history->amount = $total_amount;
//
//                $dsdelivery_history->time = $today;
//                $dsdelivery_history->save();
        }
        else {
            //add card amount remained to customer account
//                $customer->remain_amount += $remain_from_card;
//                $customer->save();
        }

        return response()->json(['status' => 'success', 'order_id' => $order_id]);
    }

    //Insert Order In gongchang
    function insert_order_in_gongchang(Request $request)
    {
        if ($request->ajax()) {

            $fuser = Auth::guard('gongchang')->user();
            if ($fuser) {
                $factory_id = $fuser->factory_id;
                $this->factory = Factory::find($factory_id);

                return $this->insert_order($request);
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

                return $this->insert_order($request);
            }
        }
    }

    //show detial of every order, especially after saved order
    function show_detail_order_in_gongchang($order_id)
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;
        $factory = Factory::find($factory_id);

        //check this order is current factory's order
        $order = Order::find($order_id);
        $order_products = $order->order_products;

        $grouped_plans_per_product = $order->grouped_plans_per_product;

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
    function show_detail_order_in_naizhan($order_id)
    {
        $this->initStationIdFromUser();

        $station = DeliveryStation::find($this->mStationId);

        //check this order is current factory's order
        $order = Order::find($order_id);
        $order_products = $order->order_products;

        $grouped_plans_per_product = $order->grouped_plans_per_product;

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
            'gap_day'                   => $this->factory->gap_day,
            
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
     * 添加客户信息
     * @return
     */
    private function insert_customer_for_order(Request $request) {

        //get all stations in this factory
        $stations = $this->factory->active_stations;
        $station_ids = [];
        foreach ($stations as $station) {
            $station_ids[] = $station->id;
        }

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
        $station_milkman = $this->get_station_milkman_with_address_from_factory($this->factory->id, $d_addr);

        if ($station_milkman == $this::NOT_EXIST_DELIVERY_AREA) {
            return response()->json(['status' => 'fail', 'message' => '客户并不住在可以递送区域.']);
        }
        else if ($station_milkman == $this::NOT_EXIST_STATION) {
            return response()->json(['status' => 'fail', 'message' => '没有奶站.']);
        }
        else if ($station_milkman == $this::NOT_EXIST_MILKMAN) {
            return response()->json(['status' => 'fail', 'message' => '没有递送人.']);
        }

        $ext_customer = Customer::where('phone', $phone)->where('factory_id', $this->factory->id)->get()->first();

        if ($ext_customer) {
            //there is already the customer
            $address = $ext_customer->address;
            if ($address == $addr && $ext_customer->is_deleted == 0) {
                $id = $ext_customer->id;
                if($ext_customer->has_not_order)
                    $remain_amount = $ext_customer->remain_amount;
                else
                    $remain_amount=0;

                $station_id = $ext_customer->station_id;
                $milkman_id = $ext_customer->milkman_id;
                $station = DeliveryStation::find($station_id);
                $station_name = $station->name;
                $ext_customer->is_deleted = 0;
                $ext_customer->name = $name;
                $ext_customer->save();

                return response()->json([
                    'status'        => 'success',
                    'customer_id'   => $id,
                    'station_name'  => $station_name,
                    'station_id'    => $station_id,
                    'milkman_id'    => $milkman_id,
                    'remain_amount' => $remain_amount
                ]);
            }
            else {
                //this customer has changed the address, so change ths station
                $ext_customer->milkman_id = null;
                $ext_customer->address = $addr;
                $ext_customer->is_deleted = 1;
                $ext_customer->name = $name;
                $ext_customer->save();
            }
        }

        foreach ($station_milkman as $delivery_station_id => $milkman_id) {
            $station = DeliveryStation::find($delivery_station_id);
            $station_name = $station->name;

            if ($ext_customer) {
                $ext_customer->station_id = $delivery_station_id;
                $ext_customer->milkman_id = $milkman_id;
                $ext_customer->is_deleted = 0;
                $ext_customer->save();

                $id = $ext_customer->id;
                if($ext_customer->has_not_order)
                    $remain_amount = $ext_customer->remain_amount;
                else
                    $remain_amount=0;

                return response()->json([
                    'status'        => 'success',
                    'customer_id'   => $id,
                    'station_name'  => $station_name,
                    'station_id'    => $delivery_station_id,
                    'milkman_id'    => $milkman_id,
                    'remain_amount' => $remain_amount
                ]);

            }
            else {
                $customer = new Customer;

                $customer->name = $name;
                $customer->phone = $phone;
                $customer->address = $addr;
                $customer->factory_id = $this->factory->id;
                $customer->remain_amount = 0;
                $customer->created_at = (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d');
                $customer->station_id = $delivery_station_id;
                $customer->milkman_id = $milkman_id;
                $customer->is_deleted = 0;
                $customer->save();

                $id = $customer->id;
                $remain_amount = 0;

                return response()->json([
                    'status'        => 'success',
                    'customer_id'   => $id,
                    'station_name'  => $station_name,
                    'station_id'    => $delivery_station_id,
                    'milkman_id'    => $milkman_id,
                    'remain_amount' => $remain_amount
                ]);
            }
        }
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

    //show not passed dingdan in gongchang
    public
    function show_not_passed_dingdan_in_gongchang()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;
        $factory = Factory::find($factory_id);
        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        $orders = Order::where('is_deleted', "0")
            ->where('factory_id', $factory_id)
            ->where(function($query) {
                $query->where('status', Order::ORDER_NOT_PASSED_STATUS);
                $query->orwhere('status', Order::ORDER_NEW_NOT_PASSED_STATUS);
            })
            ->orderBy('id', 'desc')
            ->get();

        $child = 'weitongguodingdan';
        $parent = 'dingdan';
        $current_page = 'weitongguodingdan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.weitongguodingdan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $orders,
            'factory' => $factory,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
        ]);
    }

    //show not passed dingdan in naizhan
    public
    function show_not_passed_dingdan_in_naizhan()
    {
        $this->initStationIdFromUser();

        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        $orders = Order::where('is_deleted', "0")
            ->where('delivery_station_id', $this->mStationId)
            ->where(function ($query) {
                $query->where('status', Order::ORDER_NOT_PASSED_STATUS);
                $query->where('status', Order::ORDER_PASSED_STATUS);
            })
            ->orderBy('id', 'desc')
			->get();

        $child = 'weitongguo';
        $parent = 'dingdan';
        $current_page = 'weitongguon';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.weitongguo', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $orders,
            'factory' => $this->factory,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
        ]);
    }


//show stopped dingdan in gongchang
    public
    function show_stopped_dingdan_in_gongchang()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;
        $factory = Factory::find($factory_id);
        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        $orders = Order::where('is_deleted', "0")
            ->where('factory_id', $factory_id)
            ->where('status', Order::ORDER_STOPPED_STATUS)
            ->orderBy('id', 'desc')->get();

        $child = 'zantingdingdan';
        $parent = 'dingdan';
        $current_page = 'zantingdingdan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.zantingdingdan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $orders,
            'factory' => $factory,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
        ]);
    }


//show stopped dingdan in gongchang
    public
    function show_stopped_dingdan_list_in_naizhan()
    {
        $this->initStationIdFromUser();

        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        $orders = Order::where('is_deleted', "0")
            ->where('delivery_station_id', $this->mStationId)
            ->where('status', Order::ORDER_STOPPED_STATUS)
            ->orderBy('id', 'desc')
            ->get();

        $child = 'zantingliebiao';
        $parent = 'dingdan';
        $current_page = 'zantingliebiao';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.zantingliebiao', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $orders,
            'factory' => $this->factory,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
        ]);
    }

//Show On Delivery Orders in gongchang
    public
    function show_on_delivery_dingdan_in_gongchang()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;
        $factory = Factory::find($factory_id);

        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        $orders = Order::where('is_deleted', "0")
            ->where('factory_id', $factory_id)
            ->where('status', Order::ORDER_ON_DELIVERY_STATUS)
            ->orderBy('id', 'desc')->get();

        $child = 'zaipeisongdingdan';
        $parent = 'dingdan';
        $current_page = 'zaipeisongdingdan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.zaipeisongdingdan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $orders,
            'factory' => $factory,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
        ]);
    }

    //Show on Delivery Orders in Naizhan
    public
    function show_on_delivery_dingdan_in_naizhan()
    {
        $this->initStationIdFromUser();

        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        $orders = Order::where('is_deleted', "0")->where('delivery_station_id', $this->mStationId)
            ->where('status', Order::ORDER_ON_DELIVERY_STATUS)
            ->orderBy('id', 'desc')
            ->get();

        $child = 'zaipeisong';
        $parent = 'dingdan';
        $current_page = 'zaipeisong';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.zaipeisong', [
            'pages'             => $pages,
            'child'             => $child,
            'parent'            => $parent,
            'current_page'      => $current_page,
            'orders'            => $orders,
            'factory'           => $this->factory,
            'order_properties'  => $order_properties,
            'payment_types'     => $payment_types,
        ]);
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

//show xudan liebiao page
    public
    function show_xudan_dingdan_liebiao_in_gongchang()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;
        $factory = Factory::find($factory_id);

        $orders = Order::where('is_deleted', "0")->where('factory_id', $factory_id)
            ->where(function ($query) {
                $query->where('status', Order::ORDER_FINISHED_STATUS);
                $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
            })
            ->orderBy('id', 'desc')
            ->get();

        //find total amount according to payment type
        //payment type: wechat=3, money=1, card=2

        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();


        $child = 'xudanliebiao';
        $parent = 'dingdan';
        $current_page = 'xudanliebiao';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.xudanliebiao', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $orders,
            'factory' => $factory,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
        ]);
    }

    //show xudan liebiao  in Naizhan
    public
    function show_xudan_dingdan_liebiao_in_naizhan()
    {
        $this->initStationIdFromUser();

        $orders = Order::where('is_deleted', "0")
            ->where(function($query) {
                $query->where('status', Order::ORDER_FINISHED_STATUS);
                $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
            })
            ->where('delivery_station_id', $this->mStationId)
            ->orderBy('id', 'desc')
            ->get();

        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        $child = 'xudanliebiao';
        $parent = 'dingdan';
        $current_page = 'xudanliebiao';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.xudanliebiao', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $orders,
            'factory' => $this->factory,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
        ]);
    }

//show passsed dindgan in gongchang/weiqidingdan
    public
    function show_passed_dingdan_in_gongchang()
    {

        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;
        $factory = Factory::find($factory_id);

        $orders = Order::where('is_deleted', "0")
            ->where('factory_id', $factory_id)
            ->where('status', Order::ORDER_PASSED_STATUS)
            ->orderBy('id', 'desc')->get();

        //find total amount according to payment type
        //payment type: wechat=3, money=1, card=2

        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        $child = 'weiqinaidingdan';
        $parent = 'dingdan';
        $current_page = 'weiqinaidingdan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.weiqinaidingdan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $orders,
            'factory' => $factory,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
        ]);
    }

//show passsed dindgan in naizhan/weiqidingdan
    public
    function show_passed_dingdan_in_naizhan()
    {
        $station = Auth::guard('naizhan')->user();
        $station_id = $station->id;
        $factory_id = $station->factory_id;
        $factory = Factory::find($factory_id);

        $orders = Order::where('is_deleted', "0")->where('delivery_station_id', $station_id)
            ->where('status', Order::ORDER_PASSED_STATUS)->get();

        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        $child = 'weiqinaidingdan';
        $parent = 'dingdan';
        $current_page = 'weiqinaidingdan';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.weiqinaidingdan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $orders,
            'factory' => $factory,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
        ]);
    }

//show waiting check dingdan in gongchang/daishenhe
    public
    function show_check_waiting_dingdan_in_gongchang()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;
        $factory = Factory::find($factory_id);
        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        $orders = Order::where('is_deleted', "0")
            ->where(function($query) {
                $query->where('status', Order::ORDER_NEW_WAITING_STATUS);
                $query->orWhere('status', Order::ORDER_WAITING_STATUS);
            })
            ->where('factory_id', $factory_id)
            ->orderBy('id', 'desc')
            ->get();//add time condition


        $child = 'daishenhedingdan';
        $parent = 'dingdan';
        $current_page = 'daishenhedingdan';;
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.dingdan.daishenhedingdan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $orders,
            'factory' => $factory,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
        ]);
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

                $orders = Order::where('customer_id', $customer->id)->get();

                foreach ($orders as $order) {
                    $order->address = $new_address;
                    $order->save();
                }

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

            // 订单通过
            if ($order->status == Order::ORDER_NEW_WAITING_STATUS || $order->status == Order::ORDER_WAITING_STATUS) {
                $order->status = Order::ORDER_ON_DELIVERY_STATUS;
            }
            $order->save();

            $customer_name = $order->customer->name;

            // 发送通知
            $notification = new DSNotification();
            $notification->sendToStationNotification($order->station_id, 7, "订单审核已经通过 -- ", $customer_name . "用户订单审核已经通过！！");

            //set passed status for deliveryplans
            $udps = $order->unfinished_delivery_plans;
            foreach ($udps as $udp) {
                $udp->status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED;
                $udp->save();
            }

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

            // 发送通知
            $notification = new DSNotification();
            $notification->sendToStationNotification($order->station_id, 7, "订单审核未通过", $customer_name . "用户订单审核未通过！！");

            // 删除其订单的配送明细
            $udps = $order->unfinished_delivery_plans;
            foreach ($udps as $udp) {
                $udp->delete();
            }

            return response()->json(['status' => 'success', 'message' => '订单未通过成功.']);
        }
    }


    //show waiting check dingdan in gongchang/daishenhe
    public
    function show_check_waiting_dingdan_in_naizhan()
    {
        $this->initStationIdFromUser();

        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        // 只显示本站录入的订单
        $orders = Order::where('is_deleted', "0")
            ->where('station_id', $this->mStationId)
            ->where(function($query) {
                $query->where('status', Order::ORDER_NEW_WAITING_STATUS);
                $query->orWhere('status', Order::ORDER_WAITING_STATUS);
            })
            ->orderBy('id', 'desc')
            ->get();

        $child = 'daishenhe';
        $parent = 'dingdan';
        $current_page = 'daishenhe';;
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.daishenhe', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $orders,
            'factory' => $this->factory,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
        ]);
    }

//show all dingdan in one season by ordered_at
    public
    function show_all_dingdan_in_gongchang()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;
        $factory = Factory::find($factory_id);

        $orders = Order::where('is_deleted', "0")
            ->where('factory_id', $factory_id)
            ->orderBy('id', 'desc')->get();

        //find total amount according to payment type
        //payment type: wechat=3, money=1, card=2

        //get wechat amount
        $money_amount = $card_amount = $wechat_amount = 0;
        $money_dcount = $card_dcount = $wechat_dcount = 0;

        foreach ($orders as $order) {
            if ($order->payment_type == PaymentType::PAYMENT_TYPE_MONEY_NORMAL) {
                $money_amount += $order->total_amount;
                $money_dcount++;
            } else if ($order->payment_type == PaymentType::PAYMENT_TYPE_CARD) {
                $card_amount += $order->total_amount;
                $card_dcount++;
            } else {
                $wechat_amount += $order->total_amount;
                $wechat_dcount++;
            }
        }

        $child = 'quanbudingdan-liebiao';
        $parent = 'dingdan';
        $current_page = 'quanbudingdan-liebiao';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        return view('gongchang.dingdan.quanbudingdan-liebiao', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'orders' => $orders,
            'money_amount' => $money_amount,
            'money_dcount' => $money_dcount,
            'card_amount' => $card_amount,
            'card_dcount' => $card_dcount,
            'wechat_amount' => $wechat_amount,
            'wechat_dcount' => $wechat_dcount,
            'factory' => $factory,
            'order_properties' => $order_properties,
            'payment_types' => $payment_types,
        ]);
    }

    /**
     * 初始化基础信息
     */
    private function initStationIdFromUser() {
        $suser = Auth::guard('naizhan')->user();

        $this->mStationId = $suser->station_id;
        $this->mFactoryId = $suser->factory_id;

        $this->factory = Factory::find($this->mFactoryId);
    }

    //Show All dingdan in Naizhan : Only it's orders
    public
    function show_all_dingdan_in_naizhan()
    {
        $this->initStationIdFromUser();

        $orders = Order::where('is_deleted', "0")->where('factory_id', $this->mFactoryId)
            ->where(function ($query) {
                $query->where('station_id', $this->mStationId);
                $query->orWhere('delivery_station_id', $this->mStationId);
            })->orderBy('id', 'desc')->get();

        //find total amount according to payment type
        //payment type: wechat=3, money=1, card=2

        $order_properties = OrderProperty::get()->all();
        $payment_types = PaymentType::get()->all();

        $child = 'quanbuluru';
        $parent = 'dingdan';
        $current_page = 'quanbuluru';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.dingdan.quanbuluru', [
            'pages'             => $pages,
            'child'             => $child,
            'parent'            => $parent,
            'current_page'      => $current_page,
            'orders'            => $orders,
            'factory'           => $this->factory,
            'order_properties'  => $order_properties,
            'payment_types'     => $payment_types,
        ]);
    }


    //Miscellous Functions
    function convert_to_chinese($data)
    {
        //data == array(array)
        $new_data = [];

        foreach ($data as $data_row) {
            $new_data_row = [];
            foreach ($data_row as $data_cel) {
                $data_cel = mb_convert_encoding($data_cel, 'UTF-16E', 'UTF-8');
                array_push($new_data_row, $data_cel);
            }
            array_push($new_data, $new_data_row);
        }

        return $new_data;
    }

    public function show_order_of_this_week_in_gongchang()
    {
        $fuser = Auth::guard('gongchang')->user();
        $factory_id = $fuser->factory_id;
        $factory = Factory::find($factory_id);

        $orders = Order::where('is_deleted', "0")->orderBy('id', 'desc')->where('factory_id', $factory_id)->get();//add time condition
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

        $orders = Order::where('is_deleted', "0")->orderBy('id', 'desc')->where('factory_id', $factory_id)->get();//add time condition

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
                $udps = $order->unfinished_delivery_plans;
                foreach ($udps as $udp) {
                    $udp->delete();
                }
                return response()->json(['status' => 'success', 'message' => '退订成功.']);
            } else {
                return response()->json(['status' => 'fail', 'message' => '未找到订单.']);
            }
        }
    }

    //show waiting dingdan in detail in gongchang
    function show_detail_waiting_dingdan_in_gongchang($order_id)
    {
        $order = Order::find($order_id);
        $order_products = $order->order_products;

        // 解析收货地址
        $order->resolveAddress();

        $grouped_plans_per_product = $order->grouped_plans_per_product;

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
        return 'F' . $fid . '_S' . $sid . '_C' . $cid . '_O' . $order_id;
    }

    function order_number_in_naizhan($fid, $sid, $subscriber_id, $order_id)
    {
        return 'F' . $fid . '_S' . $sid . '_C' . $subscriber_id . '_O' . $order_id;
    }


    //get Produce date for plan from start_at and product period
    function get_produce_at_day($start_at, $production_period)
    {
        $interval = intval($production_period);
        $interval = '-' . $interval . ' days';
        $start_at_day = new DateTime($start_at);
        $start_at_day->modify($interval);
        $result = $start_at_day->format('Y-m-d');
        return $result;
    }

    //Establish delivery plans for new order
    function establish_plan($op, $factory_id, $station_id, $milkman_id)
    {
        //station id <- delivery station id

        $order_id = $op->order_id;
        $order_product_id = $op->id;
        $product_id = $op->product_id;
        $product_price = $op->product_price;

        //Order object
        $order = Order::find($order_id);

        //get product production_period and orderat-production_period
        $product = Product::find($product_id);
        $production_period = ($product->production_period) / 24;

        //get factory gap day before start delivery of new order
        $factory = Factory::find($factory_id);

        $status = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING; //suggested

        $delivered_count = 0;

        /* based on delivery_type, factory_order_type
        * $op->order_type : total_count: 1 ->30,  2-> 90, 3-> 180
        * $op->delivery_type: 1 -- every day, 2- each twice day, 3- week, 4 - month
        * delivery_type
        *  1 => $deliver_at = $deliver_at + 1;
        *  2 => $deliver_at = $deliver_at + 2;
        *  3 => custom_order_dates (2:3,4:3,5:3,7:3)
        *  custom_order_dates: 1- Monday, 2-Tuesday, 3- wednesday, 4- Thurs, 5-Fri, 6-Saturday, 7- Sunday
        *  get current_day_of_week:
        *       $day = date('N', strtotime($delivery_date));
        *
        */

        //get total count and delivery type
        $total_count = $op->total_count;
        $delivery_type = $op->delivery_type;

        $deliver_at = $op->start_at;

        if ($delivery_type == 1) {

            //every day send
            $plan_count = $op->count_per_day;
            //changed plan count = plan acount

            $interval = 1;

            do {
                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                if ($total_count < $plan_count)
                    $plan_count = $total_count;

                $delivery_count = $plan_count;
                $changed_plan_count = $plan_count;

                $this->make_each_delivery_plan($milkman_id, $station_id, $order_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count,  $product_price);

                $total_count -= $plan_count;
                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

            } while ($total_count > 0);

        } else if ($delivery_type == 2) {
            //each 2 days send
            $plan_count = $op->count_per_day;
            //changed plan count = plan acount
            $interval = 2;

            do {
                $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                if ($total_count < $plan_count)
                    $plan_count = $total_count;

                $delivery_count = $plan_count;
                $changed_plan_count = $plan_count;

                $this->make_each_delivery_plan($milkman_id, $station_id, $order_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count,  $product_price);
                $total_count -= $plan_count;
                $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);

            } while ($total_count > 0);
        } else if ($delivery_type == 3) {
            //week day
            $cod = $op->custom_order_dates;

            $cod = explode(',', $cod);
            $custom = [];
            foreach ($cod as $code) {
                $code = explode(':', $code);
                $key = $code[0];
                $value = $code[1];
                $custom[$key] = $value;
            }

            $daynums = $this->days_in_month($deliver_at);

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

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $delivery_count = $plan_count;
                    $changed_plan_count = $plan_count;

                    $this->make_each_delivery_plan($milkman_id, $station_id, $order_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count,  $product_price);
                    $total_count -= $plan_count;
                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                    $daynums = $this->days_in_month($deliver_at);
                } else {
                    //get avaiable key value > current_key
                    $old_key = $key;

                    $key = $this->getClosestKey($key, $custom);
                    if ($key < $old_key)
                        $first_interval = $key + 7 - $old_key;
                    else
                        $first_interval = $key - $old_key;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $first_interval);
                    $daynums = $this->days_in_month($deliver_at);

                    $plan_count = $custom[$key];
                    //changed plan count = plan acount

                    $next_key = $this->get_next_key($custom, $key);
                    if ($next_key < $key) {
                        $interval = $next_key + 7 - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $delivery_count = $plan_count;
                    $changed_plan_count = $plan_count;

                    $this->make_each_delivery_plan($milkman_id, $station_id, $order_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count,  $product_price);
                    $total_count -= $plan_count;
                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                    $daynums = $this->days_in_month($deliver_at);
                }
            } while ($total_count > 0);
        } else {
            //month day
            $cod = $op->custom_order_dates;
            $daynums = $this->days_in_month($deliver_at);

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
                $key = (new DateTime($deliver_at))->format('d');

                if (array_key_exists($key, $custom)) {
                    $plan_count = $custom[$key];
                    //changed plan count = plan acount

                    $next_key = $this->get_next_key($custom, $key);

                    $month_days = cal_days_in_month(CAL_GREGORIAN, 10, 2016);

                    if ($next_key < $key) {
                        $interval = $next_key + $daynums - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $delivery_count = $plan_count;
                    $changed_plan_count = $plan_count;

                    $this->make_each_delivery_plan($milkman_id, $station_id, $order_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count,  $product_price);
                    $total_count -= $plan_count;
                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                    $daynums = $this->days_in_month($deliver_at);
                }
                else {
                    //get avaiable key value > current_key
                    $old_key = $key;

                    $key = $this->getClosestKey($key, $custom);
                    if ($key < $old_key)
                        $first_interval = $key + $daynums - $old_key;
                    else
                        $first_interval = $key - $old_key;

                    $deliver_at = $this->get_deliver_at_day($deliver_at, $first_interval);
                    $daynums = $this->days_in_month($deliver_at);

                    $plan_count = $custom[$key];
                    //changed plan count = plan acount

                    $next_key = $this->get_next_key($custom, $key);
                    if ($next_key < $key) {
                        $interval = $next_key + $daynums - $key;
                    } else {
                        $interval = $next_key - $key;
                    }

                    $produce_at = $this->get_produce_at_day($deliver_at, $production_period);
                    if ($total_count < $plan_count)
                        $plan_count = $total_count;

                    $delivery_count = $plan_count;
                    $changed_plan_count = $plan_count;

                    $this->make_each_delivery_plan($milkman_id, $station_id, $order_id, $order_product_id, $produce_at, $deliver_at, $status, $plan_count, $changed_plan_count, $delivery_count, $delivered_count,  $product_price);
                    $total_count -= $plan_count;
                    $deliver_at = $this->get_deliver_at_day($deliver_at, $interval);
                    $daynums = $this->days_in_month($deliver_at);
                }
            } while ($total_count > 0);
        }
    }

    function days_in_month($dd)
    {
        $dt = new DateTIME($dd);
        $m = $dt->format('m');
        $y = $dt->format('Y');
        $numdays = cal_days_in_month(CAL_GREGORIAN, $m, $y);
        return $numdays;
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

    //get weeek delivery info from string
    function get_week_delivery_info($string)
    {
        /*convert weekday string to int:int
         * data: "2016-09-28:5,2016-09-27:4,2016-09-29:1,2016-09-30:2"
         * 09-26: monday = 0
         * result: "1:4, 2:5, 3:1, 4:2"
        */
        $result = "";
        $estring = explode(',', $string);
        $ecstring = array();
        for ($i = 0; $i < count($estring); $i++) {
            $date_count = $estring[$i];
            $date_count_array = explode(':', $date_count);
            $date = trim($date_count_array[0]);

            $day = date('N', strtotime($date));

            $count = trim($date_count_array[1]);
            $ecstring[$day] = $count;
        }

        ksort($ecstring);

        foreach ($ecstring as $x => $y) {
            $result .= $x . ':' . $y . ',';
        }
        $result = rtrim($result, ',');
        return $result;
    }

    //get month delivery info from string
    function get_month_delivery_info($string)
    {
        /*convert weekday string to int:int
         * data: "2016-09-28:5,2016-09-27:4,2016-09-13:1,2016-09-15:2,2016-09-23:3"
         * result: "13:1,15:1,23:3,27:4,28:5"
        */
        $result = "";

        $estring = explode(',', $string);
        $ecstring = array();
        for ($i = 0; $i < count($estring); $i++) {
            $date_count = $estring[$i];
            $date_count_array = explode(':', $date_count);
            $date = trim($date_count_array[0]);
            $day = explode('-', $date)[2];
            $count = trim($date_count_array[1]);
            $ecstring[$day] = $count;
        }

        ksort($ecstring);

        foreach ($ecstring as $x => $y) {
            $result .= $x . ':' . $y . ',';
        }
        $result = rtrim($result, ',');
        return $result;
    }

    //Get Product Price with Customer id and address
    public function get_product_price_by_cid($pid, $otype, $cid)
    {
        $addr = Customer::find($cid)->address;
        $price = $province = $city = $district = null;

        $addr_array = multiexplode(array('，', ' ', ','), $addr);
        $province = $addr_array[0];
        $city = $addr_array[1];
        $district = $addr_array[2];

        if ($province && $city && $district) {
            $price = $this->get_product_price_by_pcd($pid, $otype, $province, $city, $district);
        }
        return $price;
    }


    function get_product_price_by_pcd($pid, $otype, $province, $city, $district)
    {
        $addr = $province . " " . $city . " " . $district;
        $pp = ProductPrice::priceTemplateFromAddress($pid, $addr);
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

            $product_price = null;

            if (!$customer_id) {
                $province = $request->input('province');
                $city = $request->input('city');

                // 去掉空格
                $district = trim($request->input('district'));
                $district = str_replace('　', '', $district);

                $product_price = $this->get_product_price_by_pcd($product_id, $order_type, $province, $city, $district);

            } else {
                $product_price = $this->get_product_price_by_cid($product_id, $order_type, $customer_id);
            }

            if (!$product_price) {
                return response()->json(['status' => 'fail', 'message' => '没有产品价格']);
            }

            $product_count = $request->input('product_count');

            $one_order_product_total_price = $product_count * $product_price;

            return response()->json(['status' => 'success', 'order_product_price' => $one_order_product_total_price]);
        }
    }

    public function update_order_status()
    {
        $now = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $today = $now->format('Y-m-d');

        Order::where('status', Order::ORDER_STOPPED_STATUS)
            ->where('restart_at', $today)
            ->update([
                'status' => Order::ORDER_ON_DELIVERY_STATUS,
                'stop_at' => '',
                'restart_at' => '',
                'status_changed_at' => (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d H:i:s'),
            ]);

        Order::where('stop_at', $today)
            ->where(function ($query) {
                $query->where('status', Order::ORDER_PASSED_STATUS);
                $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
            })->update([
                'status' => Order::ORDER_STOPPED_STATUS,
                'status_changed_at' => (new DateTime("now", new DateTimeZone('Asia/Shanghai')))->format('Y-m-d H:i:s'),
            ]);

        return $today;
    }

    //MODULE
    //Get Station and Milk Pair from Factory With address
    public function get_station_milkman_with_address_from_factory($factory_id, $address)
    {

        $factory = Factory::find($factory_id);

        $stations = $factory->active_stations;
        $station_ids = [];

        foreach ($stations as $fstation) {
            $station_ids[] = $fstation->id;
        }


        $delivery_areas = DSDeliveryArea::where('address', 'like', $address . '%')->get();

        if (count($delivery_areas) == 0) {
            //客户并不住在可以递送区域
            return OrderCtrl::NOT_EXIST_DELIVERY_AREA;
        }

        $result = [];

        $delivery_station_count = 0;
        foreach ($delivery_areas as $delivery_area) {

            $delivery_station_id = $delivery_area->station_id;

            $delivery_station = DeliveryStation::find($delivery_station_id);

            if ($delivery_station && in_array($delivery_station_id, $station_ids)) {

                $delivery_station_count++;

                //get this station's milkman that supports this address
                $milkman = $delivery_station->get_milkman_of_address($address);

                if ($milkman) {
                    $milkman_id = $milkman->id;
                    $result[$delivery_station_id] = $milkman_id;
                }
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

    /*
     *Get not finished money amounf of this order
     * From Milkman Delivery plan
     * delete passed and waiting plans
     * remain finished
     * change the changed_plan_count for sent to production plan
    */
    public function get_not_finished_amount_order($order)
    {
        $order_id = $order->id;
        //delete all delivery plans
        $plans = MilkManDeliveryPlan::where('order_id', $order_id)
            ->where(function ($query) {
                $query->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED);
                $query->orWhere('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING);
            })->get();

        foreach ($plans as $plan) {
            $plan->delete();
        }

        //for the plans that has submitted to the production plans
        //change the changed_plan_count -> 0
        //get money amount that has been delivered
        $delivered_amount = 0;
        $plans = $order->delivery_plans_sent_to_production_plan;
        foreach ($plans as $plan) {
            $plan->changed_plan_count = 0;
            $plan->save();
        }

        //Money amount for new delivery plans
        $remain = $order->remain_order_money;
        return $remain;
    }

    public function delete_all_order_products_and_delivery_plans_for_update_order($order)
    {


        //delete waiting and passed delivery  plan
        MilkManDeliveryPlan::where('order_id', $order->id)->where(function ($query) {
            $query->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED);
            $query->orWhere('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING);
        })->delete();

        $plans = $order->delivery_plans_sent_to_production_plan;
        foreach ($plans as $plan) {
            $plan->changed_plan_count = 0;
            $plan->save();
        }

        $order_products = $order->order_products;
        foreach($order_products as $op)
        {
            $op->delete();
        }

        return;
    }

}

