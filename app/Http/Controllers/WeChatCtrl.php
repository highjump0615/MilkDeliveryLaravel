<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Weixin\WechatesCtrl;
use App\Http\Requests;
use App\Model\BasicModel\Address;
use App\Model\BasicModel\Customer;
use App\Model\BasicModel\PaymentType;
use App\Model\DeliveryModel\DeliveryType;
use App\Model\DeliveryModel\MilkManDeliveryPlan;
use App\Model\FactoryModel\Factory;
use App\Model\NotificationModel\FactoryNotification;
use App\Model\OrderModel\Order;
use App\Model\OrderModel\OrderCheckers;
use App\Model\OrderModel\OrderProduct;
use App\Model\OrderModel\OrderProperty;
use App\Model\OrderModel\OrderType;
use App\Model\ProductModel\Product;
use App\Model\ProductModel\ProductCategory;
use App\Model\ProductModel\ProductPrice;
use App\Model\ReviewModel\Review;
use App\Model\WechatModel\WechatAd;
use App\Model\WechatModel\WechatAddress;
use App\Model\WechatModel\WechatCart;
use App\Model\WechatModel\WechatOrderProduct;
use App\Model\WechatModel\WechatReview;
use App\Model\WechatModel\WechatUser;
use Auth;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;


class WeChatCtrl extends Controller
{

    //First page
    public function showIndexPage(Request $request)
    {
        //todo: get factory_id from phone owner's address or phone number

        if (!session('factory_id') && isset($_GET['state'])) {
            $factory_id = $_GET['state'];

            //save factory id in session
            $request->session()->put('factory_id', $factory_id);
        } else {
            $factory_id = session('factory_id');
        }

        $factory = Factory::find($factory_id);
        if ($factory == null)
            abort(403);

        if (!session('wechat_user_id') && isset($_GET['code'])) {
            $wechatObj = new WeChatesCtrl($factory->app_id, $factory->app_secret, $factory->app_encoding_key, $factory->app_token, $factory->name, $factory_id);
            $codees = $wechatObj->codes($_GET['code']);

            //save wechat user id
            $open_id = $codees['openid'];

            $wechat_user = WechatUser::where('openid', $open_id)->get()->first();
            if (!$wechat_user) {
                $wechat_user = new WechatUser;
                $wechat_user->openid = $open_id;
                $wechat_user->factory_id = $factory_id;
                $wechat_user->save();
            }
            $wechat_user_id = $wechat_user->id;

            session(['wechat_user_id' => $wechat_user_id]);

        } else {
            $wechat_user_id = session('wechat_user_id');
        }

//        $factory_id = 1;
//        $wechat_user_id = 113;
//        $factory = Factory::find($factory_id);
//
//        session(['wechat_user_id' => $wechat_user_id]);
//        session(['factory_id' => $factory_id]);
//        session(['address' => '北京 北京市']);
//        session(['loggedin' => true]);

        //get address from weixin api and save them
        $address = session('address');
        if ($address == "" || !$address) {
            $address = $factory->first_active_address;
            session(['address' => $address]);
        }

        $banners = WechatAd::where('factory_id', $factory_id)
            ->where('type', WechatAd::WECHAT_AD_TYPE_BANNER)
            ->orderBy('image_no')
            ->get();

        $promos = WechatAd::where('factory_id', $factory_id)
            ->where('type', WechatAd::WECHAT_AD_TYPE_PROMOTION)
            ->orderBy('image_no')
            ->get();

        $products = Product::where('factory_id', $factory_id)
            ->where('is_deleted', 0)
            ->where('status', Product::PRODUCT_STATUS_ACTIVE)
            ->orderBy('id', 'desc')
            ->orderBy('updated_at', 'desc')
            ->take(4)
            ->get();

        $product_list = [];
        foreach ($products as $product) {
            $pid = $product->id;
            $product_list[$pid][0] = $product;
            $product_list[$pid][1] = $this->get_retail_price_of_product($pid);
        }

        $cartn = WechatCart::where('wxuser_id', $wechat_user_id)->get()->count();


        $result = array();
        //province list
        $provinces = Address::where('level', 1)->where('factory_id', $factory_id)
            ->where('parent_id', 0)->where('is_active', 1)->where('is_deleted', 0)->get()->all();
        if(count($provinces)>0)
        {
            foreach ($provinces as $province) {
                $cities = Address::where('level', 2)->where('factory_id', $factory_id)
                    ->where('parent_id', $province->id)->where('is_active', 1)->where('is_deleted', 0)->get()->all();
                foreach ($cities as $city) {
                    $result[$province->name][] = $city->name;
                }
            }
        }

        if ($address) {
            $addr = explode(' ', $address);
            $province_name = $addr[0];
            $city_name = $addr[1];

            return view('weixin.index', [
                'banners' => $banners,
                'promos' => $promos,
                'products' => $product_list,
                'address' => $address,
                'prov' => $province_name,
                'city' => $city_name,
                'cartn' => $cartn,
                'addr_list' => $result,
            ]);

        } else {
            return view('weixin.index', [
                'banners' => $banners,
                'promos' => $promos,
                'products' => $product_list,
                'address' => $address,
                'cartn' => $cartn,
                'addr_list' => $result,
            ]);
        }


    }

    public function set_session_address(Request $request)
    {
        $province = $request->input('province');
        $city = $request->input('city');

        $addr = $province . " " . $city;
        session(['address' => $addr]);

        $this->reset_wechat_order_product_price();

        return response()->json(['status' => 'success']);
    }

    public function gerenzhongxin(Request $request)
    {

        if (!session('factory_id') && isset($_GET['state'])) {
            $factory_id = $_GET['state'];

            //save factory id in session
            $request->session()->put('factory_id', $factory_id);
        } else {
            $factory_id = session('factory_id');
        }

        $factory = Factory::find($factory_id);

        if ($factory == null)
            abort(403);

        if (!session('wechat_user_id') && isset($_GET['code'])) {
            $wechatObj = new WeChatesCtrl($factory->app_id, $factory->app_secret, $factory->app_encoding_key, $factory->app_token, $factory->name, $factory_id);
            $codees = $wechatObj->codes($_GET['code']);

            //save wechat user id
            $open_id = $codees['openid'];
            if (!$open_id) {
                abort(403);
            }

            $wechat_user = WechatUser::where('openid', $open_id)->get()->first();
            if (!$wechat_user) {
                $wechat_user = new WechatUser;
                $wechat_user->openid = $open_id;
                $wechat_user->factory_id = $factory_id;
                $wechat_user->save();
            }
            $wechat_user_id = $wechat_user->id;

            session(['wechat_user_id' => $wechat_user_id]);

        } else {
            $wechat_user_id = session('wechat_user_id');
        }

        $address = session('address');
        if ($address == "" || !$address) {
            $address = $factory->first_active_address;
            session(['address' => $address]);
        }

        $wechat_user = WechatUser::find($wechat_user_id);
        if ($wechat_user == null)
            abort(403);

        $carts = WechatCart::where('wxuser_id', $wechat_user_id)->get();
        $cartn = $carts->count();

        $customer_id = $wechat_user->customer_id;
        $customer = Customer::find($customer_id);

        if ($customer) {
            //get customer's order remain amount
            $remain_order_amount = $customer->remain_order_amount;
            //get customer's remaining bottle amount
            $remaining_bottle_count = $customer->remaining_bottle_count;

            //notification
            $unread_cnt = WechatReview::where('customer_id', $customer_id)
                ->where('status', WechatReview::UNREAD_STATUS)
                ->get()
                ->count();
        } else {
            $remain_order_amount = 0;
            $remaining_bottle_count = 0;
            $unread_cnt = 0;
        }

        $loggedin = $wechat_user->is_loggedin;

        return view('weixin.gerenzhongxin', [
            'user' => $wechat_user,
            'remain_amount' => $remain_order_amount,
            'remaining_bottle_count' => $remaining_bottle_count,
            'cartn' => $cartn,
            'loggedin' => $loggedin,
            'unread_cnt' => $unread_cnt,
        ]);
    }

    //Show delivery plans on full calendar for change delivery plan on one date
    public function dingdanrijihua(Request $request)
    {

        $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $today = $today_date->format('Y-m-d');

        $wxuser_id = session('wechat_user_id');
        $factory_id = session('factory_id');

        if (!$wxuser_id || !$factory_id) {
            abort(403);
        }

        //show all plans for order in passed, on_delivery, finished

        $wxuser = WechatUser::find($wxuser_id);
        $customer_id = $wxuser->customer_id;

        if (!$customer_id) {
            $plans = array();

        } else {
            $plans = array();

            //show all order including admin order
            $orders = Order::where('customer_id', $customer_id)
                ->where(function ($query) {
//                    $query->where('status', Order::ORDER_FINISHED_STATUS);
                    $query->where('status', Order::ORDER_ON_DELIVERY_STATUS);
                    $query->orwhere('status', Order::ORDER_PASSED_STATUS);
                    $query->orwhere('status', Order::ORDER_STOPPED_STATUS);
                })
                ->orderBy('id', 'desc')
                ->get()->all();

            foreach ($orders as $order) {
                $plans_order = $order->grouped_delivery_plans;
                foreach ($plans_order as $plan) {
                    array_push($plans, $plan);
                }
            }
        }

        usort($plans, array($this, "cmp"));

        $edit_min_date = "";
        if (count($plans) > 0) {
            foreach ($plans as $plan) {
                if ($plan->isEditAvailable()) {
                    $edit_min_date = $plan->deliver_at;
                    break;
                }
            }
        }

        if ($request->has('from')) {
            return view('weixin.dingdanrijihua', [
                'plans' => $plans,
                'today' => $today,
                'from' => 'geren',
                'edit_min_date' => $edit_min_date,
            ]);
        } else {
            return view('weixin.dingdanrijihua', [
                'plans' => $plans,
                'today' => $today,
                'edit_min_date' => $edit_min_date,
            ]);
        }

    }

    function cmp($a, $b)
    {
        return strcmp($a->deliver_at, $b->deliver_at);
    }

    /*
     * session('change_order_product'):
     * this info is for order change, has order_id and array for product info that is included in this order
     * one product info : array($product_id, $product_name, $photo_url, $product_count, $product_price, $product_amount, $delivery_type, $count_per, $custom_date);
     *
     *   0: product_id
     *   1: product_name
     *   2: photo_url
     *   3: product_count
     *   4: product_price
     *   5: product_amount
     *   6: delivery_type
     *   7: count_per
     *   8: custom_date
     * */

    //show order product change page
    public function dingdanxiugai(Request $request)
    {
        $order_id = $request->input('order');
        $order = Order::find($order_id);
        if (!$order) {
            abort(403);
        }
        $comment = $order->comment;

        //whether this is from dingdanliebiao  or back or cancel of xiugai
        $start = false;
        if ($request->has('start'))
            $start = true;

        $wechat_user_id = session('wechat_user_id');
        if (!$wechat_user_id)
            abort(403);

        $cartn = WechatCart::where('wxuser_id', $wechat_user_id)->get()->count();

        //after_changed_amount
        $after_changed_amount = 0;

        //products to show : order_products and products_in_session
        //product info: photo_url, product_name, price, product_count, product_amount  -> order_product_id

        $show_products = [];

        if (!session('change_order_product')) {
            //make change_order_product data in session
            $cop = [];
            $show_products = [];
            foreach ($order->order_products as $op) {
                $product = $op->product;

                $product_id = $product->id;
                $photo_url = $product->photo_url1;
                $product_name = $product->name;
                $product_count = $op->remain_count;
                $product_price = round($op->product_price, 3);
                $product_amount = round($op->remain_amount, 3);
                $delivery_type = $op->delivery_type;
                $count_per = $op->count_per_day;
                $custom_date = $op->custom_order_dates;

                $start_at = $op->start_at_after_delivered;
                $order_type = $op->order_type;

                //add to after_changed_amount
                $after_changed_amount += $product_amount;

                $show_products[] = array($product_id, $product_name, $photo_url, $product_count, $product_price, $product_amount, $delivery_type, $count_per, $custom_date, $start_at, $order_type);

            }

            $cop[$order_id] = $show_products;
            session(['change_order_product' => $cop]);

        } else {

            //exist,  check whether data exist for this order
            $cop = session('change_order_product');

            if ($start) {
                //create data for this order
                $cop = session('change_order_product');
                $show_products = [];
                foreach ($order->order_products as $op) {
                    $product = $op->product;

                    $product_id = $product->id;
                    $photo_url = $product->photo_url1;
                    $product_name = $product->name;
                    $product_count = $op->remain_count;
                    $product_price = round($op->product_price, 3);
                    $product_amount = round($op->remain_amount, 3);
                    $delivery_type = $op->delivery_type;
                    $count_per = $op->count_per_day;
                    $custom_date = $op->custom_order_dates;

                    $start_at = $op->start_at_after_delivered;
                    $order_type = $op->order_type;

                    //add to after_changed_amount
                    $after_changed_amount += $product_amount;

                    $show_products[] = array($product_id, $product_name, $photo_url, $product_count, $product_price, $product_amount, $delivery_type, $count_per, $custom_date, $start_at, $order_type);
                }

                $cop[$order_id] = $show_products;
                session(['change_order_product' => $cop]);
            } else {

                if (array_key_exists($order_id, $cop)) {
                    $show_products = $cop[$order_id];

                    foreach ($show_products as $cop_one_product) {
                        //add to after_changed_amount
                        $after_changed_amount += $cop_one_product[5];
                    }
                } else {
                    //create data for this order
                    $cop = session('change_order_product');
                    $show_products = [];
                    foreach ($order->order_products as $op) {
                        $product = $op->product;

                        $product_id = $product->id;
                        $photo_url = $product->photo_url1;
                        $product_name = $product->name;
                        $product_count = $op->remain_count;
                        $product_price = round($op->product_price, 3);
                        $product_amount = round($op->remain_amount, 3);
                        $delivery_type = $op->delivery_type;
                        $count_per = $op->count_per_day;
                        $custom_date = $op->custom_order_dates;

                        $start_at = $op->start_at_after_delivered;
                        $order_type = $op->order_type;

                        //add to after_changed_amount
                        $after_changed_amount += $product_amount;

                        $show_products[] = array($product_id, $product_name, $photo_url, $product_count, $product_price, $product_amount, $delivery_type, $count_per, $custom_date, $start_at, $order_type);
                    }

                    $cop[$order_id] = $show_products;
                    session(['change_order_product' => $cop]);
                }
            }

        }

        //Show remaining amount of order and order products for change
        $order_remain_amount = $order->remaining_amount;

        //left_amount
        $left_amount = $order_remain_amount - $after_changed_amount;
        $left_amount = round($left_amount, 3);


        //show dynamic delivery plans from show_products
//        $delivery_plans = $order->grouped_delivery_plans;
        $delivery_plans = [];
        foreach ($show_products as $sp) {
            //create wechat order product temporally
            $iwop = new WechatOrderProduct;
            $iwop->total_count = $sp[3];
            $iwop->order_type = $sp[10];
            $iwop->delivery_type = $sp[6];
            $iwop->count_per_day = $sp[7];
            $iwop->custom_order_dates = $sp[8];
//            $iwop->product_name = $sp[1];
            $iwop->start_at = $sp[9];
            $iwop->product_id = $sp[0];

            //create plans from ideal wechat order product
            $iplans = $iwop->get_temp_plans();
            foreach ($iplans as $iplan) {
                $delivery_plans [] = $iplan;
            }

            $iwop->delete();
        }

        $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $today = $today_date->format('Y-m-d');

        return view('weixin.dingdanxiugai', [
            'order' => $order,
            'plans' => $delivery_plans,
            'comment' => $comment,
            'cartn' => $cartn,
            'show_products' => $show_products,
            'after_changed_amount' => $after_changed_amount,
            'order_remain_amount' => $order_remain_amount,
            'left_amount' => $left_amount,
            'today' => $today,
        ]);
    }


    //show order product change page
    public function naipinxiugai(Request $request)
    {
        $order_id = $request->input('order_id');
        $index = $request->input('index');
        //current left amount for this order
        $current_order_remain_amount = $request->input('left_amount');

        $exist = false;

        if (session('change_order_product')) {
            $cop = session('change_order_product');
            if (array_key_exists($order_id, $cop)) {
                $cop_order = $cop[$order_id];

                if (array_key_exists($index, $cop_order)) {

                    /*
                    *   0: product_id
                    *   1: product_name
                    *   2: photo_url
                    *   3: product_count
                    *   4: product_price
                    *   5: product_amount
                    *   6: delivery_type
                    *   7: count_per
                    *   8: custom_date
                    *   9: start_at
                    *   10: order_type
                    */

                    $cop_order_product = $cop_order[$index];

                    $product_id = $cop_order_product[0];
                    $product = Product::find($product_id);

                    //current product to show above
                    $current_product = $product;
                    $current_pid = $cop_order_product[0];
                    $current_product_amount = $cop_order_product[5];
                    $current_product_count = $cop_order_product[3];
                    $current_product_price = $cop_order_product[4];
                    $current_product_name = $product->name;
                    $current_product_photo_url = $product->photo_url1;
                    $current_delivery_type = $cop_order_product[6];
                    $current_count_per_day = $cop_order_product[7];
                    $current_custom_order_dates = $cop_order_product[8];

                    $current_start_at = $cop_order_product[9];
                    $current_order_type = $cop_order_product[10];


                    //address
                    $address = session('address');

                    if ($current_product_count < 90) {
                        $order_type = OrderType::ORDER_TYPE_MONTH;
                    } else if ($current_product_count >= 90) {
                        $order_type = OrderType::ORDER_TYPE_SEASON;
                    } else if ($current_product_count >= 180) {
                        $order_type = OrderType::ORDER_TYPE_HALF_YEAR;
                    }

                    //get all active product of this factory (product, price, count by remain amount)
                    $factory_id = session('factory_id');
                    $factory = Factory::find($factory_id);
                    $products = [];
                    if ($factory) {
                        $fproducts = $factory->active_products;
                        foreach ($fproducts as $fp) {
                            $pid = $fp->id;
                            $price = $this->get_product_price_by_order_type($order_type, $pid, $address);
                            $count = floor(($current_product_amount + $current_order_remain_amount) / $price);
                            $products[$pid] = array($fp->id, $fp->name, $fp->photo_url1, $price, $count);
                        }

                    } else {
                        abort(403);
                    }

                    $exist = true;
                }
            }
        }

        if (!$exist) {
            abort(403);
        }

        if ($request->has('type')) {
            $type = $request->input('type');

            return view('weixin.naipinxiugai', [
                'order_id' => $order_id,
                'current_order_remain_amount' => $current_order_remain_amount,
                'index' => $index,
                'products' => $products,
                'current_product' => $current_product,
                'current_product_id' => $current_pid,
                'current_product_amount' => $current_product_amount,
                'current_product_count' => $current_product_count,
                'current_product_price' => $current_product_price,
                'current_product_name' => $current_product_name,
                'current_product_photo_url' => $current_product_photo_url,
                'current_delivery_type' => $current_delivery_type,
                'current_count_per_day' => $current_count_per_day,
                'current_custom_order_dates' => $current_custom_order_dates,
                'type' => $type,
            ]);
        } else {
            return view('weixin.naipinxiugai', [
                'order_id' => $order_id,
                'current_order_remain_amount' => $current_order_remain_amount,
                'index' => $index,
                'products' => $products,
                'current_product' => $current_product,
                'current_product_id' => $current_pid,
                'current_product_amount' => $current_product_amount,
                'current_product_count' => $current_product_count,
                'current_product_price' => $current_product_price,
                'current_product_name' => $current_product_name,
                'current_product_photo_url' => $current_product_photo_url,
                'current_delivery_type' => $current_delivery_type,
                'current_count_per_day' => $current_count_per_day,
                'current_custom_order_dates' => $current_custom_order_dates,
            ]);
        }

    }

    //change order product temporally
    public function change_temp_order_product(Request $request)
    {
        $order_id = $request->input('order_id');
        $index = $request->input('index');
        $current_product_id = $request->input('current_product_id');
        $new_product_id = $request->input('new_product_id');
        $product = Product::find($new_product_id);
        $product_name = $product->name;
        $photo_url = $product->photo_url1;

        $delivery_type = $request->input('delivery_type');
        $count_per = $request->input('count_per');
        $custom_date = $request->input('custom_date');
        $product_count = $request->input('product_count');
        $product_price = $request->input('product_price');
        $product_amount = $request->input('product_amount');

        /*
        *   0: product_id
        *   1: product_name
        *   2: photo_url
        *   3: product_count
        *   4: product_price
        *   5: product_amount
        *   6: delivery_type
        *   7: count_per
        *   8: custom_date
        *   9: start_at
        *   10: order_type
        */

        $cop = session('change_order_product');
        $origin_pinfo = $cop[$order_id][$index];

        //save changed product info in session
        $changed_product_info = array($new_product_id, $product_name, $photo_url, $product_count, $product_price, $product_amount, $delivery_type, $count_per, $custom_date, $origin_pinfo[9], $origin_pinfo[10]);

        $cop[$order_id][$index] = $changed_product_info;
        session(['change_order_product' => $cop]);

        return response()->json(['status' => 'success']);

    }

    //remove product from order
    public function remove_product_from_order(Request $request)
    {
        $order_id = $request->input('order_id');
        $index = $request->input('index');
        $exist = false;

        if (session('change_order_product')) {
            $cop = session('change_order_product');
            if (array_key_exists($order_id, $cop)) {
                $cop_order = $cop[$order_id];

                if (array_key_exists($index, $cop_order)) {

                    unset($cop_order[$index]);
                    $cop_order = array_values($cop_order);
                    $cop[$order_id] = $cop_order;
                    session(['change_order_product' => $cop]);
                    $exist = true;
                }
            }
        }

        if (!$exist) {
            abort(403);
        }

        return response()->json(['status' => 'success']);
    }

    //add product to order
    public function add_product_to_order_for_xiugai(Request $request)
    {
        $order_id = $request->input('order_id');
        $product_id = $request->input('product_id');
        $order_type = $request->input('order_type');
        $total_count = $request->input('total_count');

        $product = Product::find($product_id);
        if (!$product) {
            return response()->json(['status' => 'fail']);
        }

        $product_name = $product->name;
        $photo_url = $product->photo_url1;

        $delivery_type = $request->input('delivery_type');

        $count_per = $custom_date = null;
        if ($delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY || $delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY) {
            $count_per = $request->input('count_per');
        } else {
            $custom_date = $request->input('custom_date');
            $custom_date = rtrim($custom_date, ',');
        }

        $address = session('address');
        $product_price = $this->get_product_price_by_order_type($order_type, $product_id, $address);

        $total_amount = $total_count * $product_price;

        $start_at = $request->input('start_at');
        $start_at = new DateTime($start_at);
        $start_at = $start_at->format('Y-m-d');

        /*if ($order_type == OrderType::ORDER_TYPE_MONTH)
            $avg = round($total_count / 30, 1);
        else if ($order_type == OrderType::ORDER_TYPE_SEASON)
            $avg = round($total_count / 90, 1);
        else
            $avg = round($total_count / 180, 1);
        */
        $order_type = $request->input('order_type');

        $add_product_info = array($product_id, $product_name, $photo_url, $total_count, $product_price, $total_amount, $delivery_type, $count_per, $custom_date, $start_at, $order_type);

        if (session('change_order_product')) {
            $cop = session('change_order_product');
            if (array_key_exists($order_id, $cop)) {
                $cop_order = $cop[$order_id];
                array_push($cop_order, $add_product_info);
                $cop[$order_id] = $cop_order;
                session(['change_order_product' => $cop]);
            }
        }

        return response()->json(['status' => 'success']);

    }

    //cancel change order
    public function cancel_change_order(Request $request)
    {
        $order_id = $request->input('order_id');
        if (session('change_order_product')) {
            $cop = session('change_order_product');
            if (array_key_exists($order_id, $cop)) {
                unset($cop[$order_id]);
                $cop = array_values($cop);
                session(['change_order_product' => $cop]);
            }
        }
        return response()->json(['status' => 'success']);
    }

    //pubic function change order product
    public function change_order_product(Request $request)
    {
        $opid = $request->input('order_product_id');
        $order_product = OrderProduct::find($opid);
        $order_id = $order_product->order_id;
        $order = Order::find($order_id);

        $pid = $request->input('product_id');
        $delivery_type = $request->input('delivery_type');

        $count_per = $custom_date = null;
        if ($delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY || $delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY) {
            $count_per = $request->input('count_per');
        } else {
            $custom_date = $request->input('custom_date');
            $custom_date = rtrim($custom_date, ',');
        }

        //delete current order product and delivery plans for update
        MilkManDeliveryPlan::where('order_product_id', $opid)->where(function ($query) {
            $query->where('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED);
            $query->orWhere('status', MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING);
        })->delete();


        $order_ctrl = new OrderCtrl;

        $plans = $order_product->delivery_plans_sent_to_production_plan;
        foreach ($plans as $plan) {
            $plan->changed_plan_count = 0;
            $plan->save();
        }

        //old data for new product
        $factory_id = $order->factory_id;
        $station_id = $order->station_id;
        $milkman_id = $order->milkman_id;
        $order_type = $order_product->order_type;
        $customer_id = $order->customer_id;
        $product_price = $order_ctrl->get_product_price_by_cid($pid, $order_type, $customer_id);


        $total_amount = $order_product->remain_amount;
        $total_count = floor($total_amount / $product_price);

        if ($order_type == OrderType::ORDER_TYPE_MONTH)
            $avg = round($total_count / 30, 1);
        else if ($order_type == OrderType::ORDER_TYPE_SEASON)
            $avg = round($total_count / 90, 1);
        else
            $avg = round($total_count / 180, 1);

        //update order product and delivery plan
        $order_product->product_id = $pid;
        $order_product->delivery_type = $delivery_type;
        $order_product->product_price = $product_price;
        $order_product->total_count = $total_count;
        $order_product->total_amount = $total_amount;
        $order_product->avg = $avg;
        $order_product->count_per_day = $count_per;
        $order_product->custom_order_dates = $custom_date;
        $order_product->save();

        $order_ctrl->establish_new_plan_with_money_amount($order_product, $factory_id, $station_id, $milkman_id, $total_amount);

        //order changed status as waiting
        $order->status = Order::ORDER_WAITING_STATUS;
        $order->save();

        $notification = new NotificationsAdmin;
        $notification->sendToWechatNotification($customer_id, "您的订单修改已提交，请耐心等待，客户将尽快核对您的订单信息！");

        return response()->json(['status' => 'success']);
    }

    /*
     * change order
     * apply Saved session data for changed order to database
    */
    public function change_order(Request $request)
    {
        $order_id = $request->input('order_id');
        $order = Order::find($order_id);
        if (!$order) {
            return resoponse()->json(['status' => 'fail', 'message' => '没有订单']);
        }

        /*
        *   0: product_id
        *   1: product_name
        *   2: photo_url
        *   3: product_count
        *   4: product_price
        *   5: product_amount
        *   6: delivery_type
        *   7: count_per
        *   8: custom_date
        *   9: start_at
        *   10: order_type
        */

        if (session('change_order_product')) {
            $cop = session('change_order_product');
            if (array_key_exists($order_id, $cop)) {

                $factory_id = $order->factory_id;
                $delivery_station_id = $order->delivery_station_id;
                $milkman_id = $order->milkman_id;

                $order_total_amount = $order->total_amount;
                $order_remaining_amount = $order->remaining_amount;
                $previous_done_amount = $order_total_amount - $order_remaining_amount;

                $order_ctrl = new OrderCtrl;

                $cop_orders = $cop[$order_id];

                //first remove all order products and delivery plans except of delivered
                // from order in db
                $order_ctrl->delete_all_order_products_and_delivery_plans_for_update_order($order);

                $changed_product_total_amount = 0;

                foreach ($cop_orders as $index => $cop_order) {

                    $pid = $cop_order[0];
                    $order_type = $cop_order[10];
                    $total_count = $cop_order[3];
                    $one_amount = $cop_order[5];
                    $product_price = $cop_order[4];
                    $delivery_type = $cop_order[6];

                    $changed_product_total_amount += $one_amount;

                    if ($order_type == OrderType::ORDER_TYPE_MONTH)
                        $avg = round($total_count / 30, 1);
                    else if ($order_type == OrderType::ORDER_TYPE_SEASON)
                        $avg = round($total_count / 90, 1);
                    else
                        $avg = round($total_count / 180, 1);

                    $product_start_at = $cop_order[9];

                    $op = new OrderProduct;
                    $op->order_id = $order_id;
                    $op->product_id = $pid;
                    $op->order_type = $order_type;
                    $op->delivery_type = $delivery_type;
                    $op->product_price = $product_price;
                    $op->total_count = $total_count;
                    $op->total_amount = $one_amount;
                    $op->avg = $avg;
                    $op->start_at = $product_start_at;

                    $op->count_per_day = $cop_order[7];

                    if ($delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY || $delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY) {   // 天天送、隔日送
//                    $op->count_per_day = $request->input('order_product_count_per')[$i];
                    } else {
                        $custom_dates = $cop_order[8];
                        $result = rtrim($custom_dates, ',');
                        $op->custom_order_dates = $result;
                    }

                    $op->save();

                    //establish plan
                    $order_ctrl->establish_plan($op, $factory_id, $delivery_station_id, $milkman_id);
                }

                //set flag on first order delivery plan
                $plans = $order->first_delivery_plans;
                if ($plans) {
                    foreach ($plans as $plan) {
                        $plan->flag = MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_FLAG_FIRST_ON_ORDER;
                        $plan->save();
                    }
                }

                $order->remaining_amount = $changed_product_total_amount;
                $order->status = Order::ORDER_WAITING_STATUS;
                $order->save();

                //notification
                $customer = $order->customer;
                $customer_name = $customer->name;

                //notification to factory and wechat
                $notification = new NotificationsAdmin;
                $notification->sendToFactoryNotification($factory_id, FactoryNotification::CATEGORY_CHANGE_ORDER, "微信下单成功", $customer_name . "修改了订单, 请管理员尽快审核");
                $notification->sendToWechatNotification($customer->id, '您的订单修改已提交，请耐心等待，客户将尽快核对您的订单信息！');

                return response()->json(['status' => 'success']);
            }
        } else {
            return resoponse()->json(['status' => 'fail']);
        }


    }

    //show change delivery plan on one date
    public function danrixiugai(Request $request)
    {
        $date = $request->input('date');
        $date_time = new DateTime($date);
        $date = $date_time->format('Y-m-d');

        //get plans for the date
        $wechat_user_id = session('wechat_user_id');
        if (!$wechat_user_id)
            abort(403);

        $wechat_user = WechatUser::find($wechat_user_id);
        $customer_id = $wechat_user->customer_id;
        $customer = Customer::find($customer_id);

        $plans = $customer->get_plans_for_date($date);
        $total_date = 0;

        foreach ($plans as $plan) {
            $total_date += ($plan->product_price) * ($plan->changed_plan_count);
        }

        return view('weixin.danrixiugai', [
            'date' => $date,
            'plans' => $plans,
            'total_amount_on_date' => $total_date,
        ]);
    }

    //change delivery plans on one date
    public function change_delivery_plan_for_one_date(Request $request)
    {
        $plans_data = $request->input('plans_data');

        $order_ctrl = new OrderCtrl();

        $count = 0;
        $success_count = 0;

        $messages = [];

        $customer_id = "";
        foreach ($plans_data as $plan_data) {
            $plan_id = $plan_data[0];
            $origin = $plan_data[1];
            $change = $plan_data[2];
            $count++;

            $plan = MilkManDeliveryPlan::find($plan_id);

            $order_id = $plan->order_id;

            $order = Order::find($order_id);
            $customer_id = $order->customer_id;
            $diff = $change - $origin;
            $result = $order_ctrl->change_delivery_plan($order_id, $plan_id, $diff);
            if ($result['status'] == "success") {
                $success_count++;
            } else {
                $message = $result['message'];
                $messages[$count] = $message;
            }
        }

        if ($count == $success_count) {
            //notification to factory and wechat
            $notification = new NotificationsAdmin;
            $notification->sendToWechatNotification($customer_id, '单日修改成功，配送结果以实际配送为准');
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'fail', 'messages' => $messages]);
        }
    }

    public function dingdanliebiao(Request $request)
    {
        $wechat_user_id = session('wechat_user_id');
        if (!$wechat_user_id)
            abort(403);

        $wechat_user = WechatUser::find($wechat_user_id);

        if (!$wechat_user->is_loggedin) {
            return redirect()->route('dengji');
        }

        $type = $request->input('type');
        $customer_id = $wechat_user->customer_id;

        if ($customer_id) {
            $customer = Customer::find($customer_id);
            $cartn = WechatCart::where('wxuser_id', $wechat_user_id)->get()->count();

            $type = $request->input('type');

            $orders = Order::where('is_deleted', 0)
                ->where('phone', $customer->phone);

            switch ($type) {
                case 'waiting':
                    $orders = $orders->where(function ($query) {
                        $query->where('status', Order::ORDER_NEW_WAITING_STATUS);
                        $query->orWhere('status', Order::ORDER_WAITING_STATUS);
                    });
                    break;
                case 'finished':
                    $orders = $orders->where('status', Order::ORDER_FINISHED_STATUS);
                    break;
                case 'on_delivery':
                    $orders = $orders->where(function ($query) {
                        $query->where('status', Order::ORDER_PASSED_STATUS);
                        $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
                        $query->orWhere('status', Order::ORDER_STOPPED_STATUS);
                    });
                    break;
                default:
                    break;
            }

            $orders = $orders->orderBy('created_at', 'desc')->get();
            return view('weixin.dingdanliebiao', [
                'type' => $type,
                'orders' => $orders,
                'cartn' => $cartn,
            ]);

        } else {
            $orders = [];
            return view('weixin.dingdanliebiao', [
                'type' => $type,
                'orders' => $orders,
                'cartn' => 0,
            ]);
        }
    }

    public function dingdanxiangqing(Request $request)
    {
        $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $today = $today_date->format('Y-m-d');

        $order_id = $request->input('order');
        $order = Order::find($order_id);
        $comment = $order->comment;

        $wechat_user_id = session('wechat_user_id');
        $cartn = WechatCart::where('wxuser_id', $wechat_user_id)->get()->count();

        if ($order) {
            $delivery_plans = $order->grouped_delivery_plans;
            return view('weixin.dingdanxiangqing', [
                'order' => $order,
                'plans' => $delivery_plans,
                'comment' => $comment,
                'cartn' => $cartn,
                'today' => $today,
            ]);
        } else {
            abort(403);
        }
    }


    public function toushu(Request $request)
    {
        $factory_id = session('factory_id');
        $factory = Factory::find($factory_id);

        $phone1 = $factory->service_phone;
        $phone2 = $factory->return_phone;

        $wechat_user_id = session('wechat_user_id');
        $cartn = WechatCart::where('wxuser_id', $wechat_user_id)->get()->count();

        return view('weixin.toushu', [
            "phone1" => $phone1,
            "phone2" => $phone2,
            'cartn' => $cartn,
        ]);
    }


    /* 商品列表 */
    public function shangpinliebiao(Request $request)
    {

//        if (!session('factory_id') && isset($_GET['state'])) {
//            $factory_id = $_GET['state'];
//
//            //save factory id in session
//            $request->session()->put('factory_id', $factory_id);
//
//            $factory = Factory::find($factory_id);
//            $wechatObj = new WeChatesCtrl($factory->app_id, $factory->app_secret, $factory->app_encoding_key, $factory->app_token, $factory->name, $factory_id);
//            $codees = $wechatObj->codes($_GET['code']);
//
//            //save wechat user id
//            $open_id = $codees['openid'];
//
//            $wechat_user = WechatUser::where('openid', $open_id)->get()->first();
//            if (!$wechat_user) {
//                $wechat_user = new WechatUser;
//                $wechat_user->openid = $open_id;
//                $wechat_user->factory_id = $factory_id;
//                $wechat_user->save();
//            }
//            $wechat_user_id = $wechat_user->id;
//
//            session(['wechat_user_id' => $wechat_user_id]);
//
//        } else {
//
//            $factory_id = session('factory_id');
//            $factory = Factory::find($factory_id);
//            $wechat_user_id = session('wechat_user_id');
//        }

        if (!session('factory_id') && isset($_GET['state'])) {
            $factory_id = $_GET['state'];

            //save factory id in session
            $request->session()->put('factory_id', $factory_id);
        } else {
            $factory_id = session('factory_id');
        }

        $factory = Factory::find($factory_id);
        if ($factory == null)
            abort(403);

        if (!session('wechat_user_id') && isset($_GET['code'])) {
            $wechatObj = new WeChatesCtrl($factory->app_id, $factory->app_secret, $factory->app_encoding_key, $factory->app_token, $factory->name, $factory_id);
            $codees = $wechatObj->codes($_GET['code']);

            //save wechat user id
            $open_id = $codees['openid'];

            $wechat_user = WechatUser::where('openid', $open_id)->get()->first();
            if (!$wechat_user) {
                $wechat_user = new WechatUser;
                $wechat_user->openid = $open_id;
                $wechat_user->factory_id = $factory_id;
                $wechat_user->save();
            }
            $wechat_user_id = $wechat_user->id;

            session(['wechat_user_id' => $wechat_user_id]);

        } else {
            $wechat_user_id = session('wechat_user_id');
        }

        $address = session('address');
        if ($address == "" || !$address) {
            $address = $factory->first_active_address;
            session(['address' => $address]);
        }
        $product_list = [];

        $products = $factory->active_products;
        foreach ($products as $product) {
            $pid = $product->id;
            $product_list[$pid][0] = $product;
            $product_list[$pid][1] = $this->get_retail_price_of_product($pid);
        }

        $categories = ProductCategory::where('factory_id', $factory_id)
            ->where('is_deleted', 0)
            ->get();

        $category_id = 0;

        if ($request->has('category'))
            $category_id = $request->input('category');
        else {
            if ($categories->first()) {
                $category_id = $categories->first()->id;
            }
        }


        $cartn = WechatCart::where('wxuser_id', $wechat_user_id)->get()->count();

        if ($request->has('search_product')) {
            $search_pname = $request->input('search_product');

            $search_product_list = [];
            foreach ($products as $product) {
                if (strpos($product->name, $search_pname) !== false) {
                    $pid = $product->id;
                    $search_product_list[$pid][0] = $product;
                    $search_product_list[$pid][1] = $this->get_retail_price_of_product($pid);
                }
            }

            return view('weixin.shangpinliebiao', [
                'categories' => $categories,
                'products' => $search_product_list,
                'category' => $category_id,
                'cartn' => $cartn,
            ]);

        } else if ($request->has('order_id')) {

            if ($request->has('type')) {
                $type = $request->input('type');
                $order_id = $request->input('order_id');
                //from dingdanxiugai
                return view('weixin.shangpinliebiao', [
                    'categories' => $categories,
                    'products' => $product_list,
                    'category' => $category_id,
                    'cartn' => $cartn,
                    'order_id' => $order_id,
                    'type' => $type,
                ]);


            } else {

                $order_id = $request->input('order_id');
                //from dingdanxiugai
                return view('weixin.shangpinliebiao', [
                    'categories' => $categories,
                    'products' => $product_list,
                    'category' => $category_id,
                    'cartn' => $cartn,
                    'order_id' => $order_id,
                ]);
            }


        } else {

            return view('weixin.shangpinliebiao', [
                'categories' => $categories,
                'products' => $product_list,
                'category' => $category_id,
                'cartn' => $cartn,
            ]);

        }
    }

    //get retail price of product based on address
    public function get_retail_price_of_product($product_id)
    {
        $address = session('address');
        if ($address) {
            $pp = ProductPrice::priceTemplateFromAddress($product_id, $address);
        } else {

            $pp = ProductPrice::where('product_id', $product_id)->get()->first();
        }

        if ($pp) {
            return $pp->retail_price;
        }

    }

    public function wodepingjia(Request $request)
    {
        $wechat_user_id = session('wechat_user_id');
        $cartn = WechatCart::where('wxuser_id', $wechat_user_id)->get()->count();

        if ($request->has('order_id')) {

            $order_id = $request->input('order_id');
            $review = Review::where('order_id', $order_id)->get()->first();
            $reviews = [];
            $reviews[] = $review;
            return view('weixin.wodepingjia', [
                'reviews' => $reviews,
                'cartn' => $cartn,
            ]);

        } else {
            //show all review from this customer
            $wechat_user = WechatUser::find($wechat_user_id);
            $customer_id = $wechat_user->customer_id;
            $reviews = Review::where('customer_id', $customer_id)->get()->all();
            return view('weixin.wodepingjia', [
                'reviews' => $reviews,
                'cartn' => $cartn,
            ]);
        }
    }

    public function dingdanpingjia(Request $request)
    {
        $order_id = $request->input('order');

        $order = Order::find($order_id);
        $review = Review::where('order_id', $order_id)->get()->first();
        if ($review != '') {
            return redirect()->route('wodepingjia');
        }

        if ($order == null) {
            abort(403);
        }

        return view('weixin.dingdanpingjia', [
            'order' => $order,

        ]);
    }

    public function zhifuchenggong(Request $request)
    {
        $order_id = $request->input('order');
        $wechat_user_id = session('wechat_user_id');
        if (!$wechat_user_id)
            abort(403);
        $wechat_user = WechatUser::find($wechat_user_id);
        $cartn = WechatCart::where('wxuser_id', $wechat_user_id)->get()->count();

        $group_id = session('group_id');
        //delete cart and order item
        $this->remove_cart_by_group($group_id);

        //check whether this user has been loggedin
        //check logged in user's phone number ?= order = phone number

        $check = 'nov';
        if ($wechat_user->is_loggedin) {
            //the user has loggedin
            $customer_id = $wechat_user->customer_id;
            $customer = Customer::find($customer_id);
            $order = Order::find($order_id);

            if ($customer->phone == $order->phone) {
                $check = "cpop";
            } else {
                $check = 'op';
            }
        }


        return view('weixin.zhifuchenggong', [
            'order_id' => $order_id,
            'cartn' => $cartn,
            'check' => $check,
        ]);
    }

    public function zhifushibai(Request $request)
    {
        if ($request->has('order')) {

            $order_id = $request->input('order');

            $customer_id = Order::find($order_id);

            $notification = new NotificationsAdmin;
            $notification->sendToWechatNotification($customer_id, '抱歉，您的订单未及时付款，订单已经取消');

            $orderctrl = new OrderCtrl();
            $orderctrl->delete_order($order_id);

        }

        return view('weixin.zhifushibai', [
        ]);
    }

    public function xinxizhongxin(Request $request)
    {
        $wechat_user_id = session('wechat_user_id');
        $customer_id = WechatUser::find($wechat_user_id)->customer_id;
        $reviews = WechatReview::where('customer_id', $customer_id)->orderby('created_at', 'desc')->get();
        $cartn = WechatCart::where('wxuser_id', $wechat_user_id)->get()->count();

        $wxreviews = WechatReview::where('customer_id', $customer_id)->where('status', WechatReview::UNREAD_STATUS)->get()->all();
        foreach ($wxreviews as $wxreview) {
            $wxreview->status = WechatReview::READ_STATUS;
            $wxreview->save();
        }

        return view('weixin.xinxizhongxin', [
            'reviews' => $reviews,
            'cartn' => $cartn,
        ]);
    }

    public function dizhiliebiao(Request $request)
    {
        $wxuser_id = session('wechat_user_id');
        $addrs = WechatAddress::where('wxuser_id', $wxuser_id)->get();

        if ($request->has('order') and $request->has('type')) {
            $order = $request->input('order');
            $type = $request->input('type');

            if ($request->has('message')) {
                $message = $request->input('message');
                return view('weixin.dizhiliebiao', [
                    'address_list' => $addrs,
                    'order' => $order,
                    'type' => $type,
                    'message' => $message,
                ]);
            } else {
                return view('weixin.dizhiliebiao', [
                    'address_list' => $addrs,
                    'order' => $order,
                    'type' => $type,
                ]);
            }
        } else {
            if ($request->has('message')) {
                $message = $request->input('message');
                return view('weixin.dizhiliebiao', [
                    'address_list' => $addrs,
                    'message' => $message,
                ]);
            } else {
                return view('weixin.dizhiliebiao', [
                    'address_list' => $addrs,
                ]);
            }

        }

    }

    public function dizhitianxie(Request $request)
    {
        $factory_id = session('factory_id');
        $wxuser_id = session('wechat_user_id');

        $address = null;
        $address_id = null;

        if ($request->has('address')) {
            $address_id = $request->input('address');
            $address = WechatAddress::find($address_id);
        }

        if ($address) {
            $c_name = $address->name;
            $c_phone = $address->phone;
            $c_address = $address->address;
            $c_sub_address = $address->sub_address;
            $c_primary = $address->primary;

        } else {
            $c_name = $c_phone = $c_address = $c_sub_address = '';
            $c_primary = 1;
        }

        $addr = Address::where('factory_id', $factory_id)
            ->where('is_deleted', 0)
            ->where('is_active', 1)
            ->get();

        $ret = array();

        foreach ($addr as $a) {
            if ($a->level == 1) {
                $ret[86][$a->id] = $a->name;
            } else {
                $ret[$a->parent_id][$a->id] = $a->name;
            }
        }

        $c_address = str_replace(' ', '/', $c_address);

        if ($request->has('order') and $request->has('type')) {
            $order = $request->input('order');
            $type = $request->input('type');
            return view('weixin.dizhitianxie', [
                'wxuser_id' => $wxuser_id,
                'address_id' => $address_id,
                'address_list' => $ret,
                'name' => $c_name,
                'phone' => $c_phone,
                'address' => $c_address,
                'sub_address' => $c_sub_address,
                'primary' => $c_primary,
                'order' => $order,
                'type' => $type,
            ]);
        } else {
            return view('weixin.dizhitianxie', [
                'wxuser_id' => $wxuser_id,
                'address_id' => $address_id,
                'address_list' => $ret,
                'name' => $c_name,
                'phone' => $c_phone,
                'address' => $c_address,
                'sub_address' => $c_sub_address,
                'primary' => $c_primary,
            ]);
        }

    }

    public function addOrUpdateAddress(Request $request)
    {
        $wxuser_id = $request->input('wxuser_id');
        $name = $request->input('name');
        $phone = $request->input('phone');
        $address = $request->input('address');
        $sub_address = $request->input('sub_address');
        $primary = $request->input('primary');

        $address = str_replace('/', ' ', $address);

        if ($primary) {
            WechatAddress::where('wxuser_id', $wxuser_id)->update(['primary' => 0]);
        }


        if ($request->has('address_id')) {
            $address_id = $request->input('address_id');
            $addr = WechatAddress::find($address_id);
        } else {
            $addr = new WechatAddress();
        }

        $addr->name = $name;
        $addr->phone = $phone;
        $addr->address = $address;
        $addr->sub_address = $sub_address;
        $addr->wxuser_id = $wxuser_id;

        if ($primary) {
            WechatAddress::where('wxuser_id', $wxuser_id)->update(['primary' => 0]);
            $addr->primary = 1;
        }

        $addr->save();
        if ($request->has('order') && $request->has('type')) {
            $order = $request->input('order');
            $type = $request->input('type');
            return redirect()->route('dizhiliebiao', ['order' => $order, 'type' => $type]);
        } else {
            return redirect()->route('dizhiliebiao');
        }

    }

    public function deleteAddress(Request $request)
    {
        $wxuser_id = session('wechat_user_id');
        $address_id = $request->input('address');

        $address = WechatAddress::find($address_id);
        if ($address) {
            if ($address->primary == true) {
                $address->delete();

                $address = WechatAddress::where('wxuser_id', $wxuser_id)->get()->first();

                if ($address) {
                    $address->primary = true;
                    $address->save();
                }
            } else {
                $address->delete();
            }
        }

        if ($request->has('order') && $request->has('type')) {
            $order = $request->input('order');
            $type = $request->input('type');
            return redirect()->route('dizhiliebiao', ['order' => $order, 'type' => $type]);
        } else {
            return redirect()->route('dizhiliebiao');
        }
    }


    public function reset_wechat_order_product_price()
    {
        $wxuser_id = session('wechat_user_id');
        $new_address = session('address');

        $wops = WechatOrderProduct::where('wxuser_id', $wxuser_id)->get()->all();

        foreach ($wops as $wop) {
            $order_type = $wop->order_type;
            $product_id = $wop->product_id;
            $wop->product_price = $this->get_product_price_by_order_type($order_type, $product_id, $new_address);
            $wop->total_amount = $wop->product_price*$wop->total_count;
            $wop->save();
        }
    }

    public function selectAddress(Request $request)
    {
        $wxuser_id = session('wechat_user_id');
        $address_id = $request->input('address');
        $group_id = session('group_id');

        WechatAddress::where('wxuser_id', $wxuser_id)->update(['primary' => 0]);

        //check wheter selected address and session_address
        $session_addr = session('address');

        $address = WechatAddress::find($address_id);
        if ($address) {
            $sel_addr = $address->address;
            if (strpos($sel_addr, $session_addr) === false) {

                if ($request->has('order') and $request->has('type')) {
                    $order = $request->input('order');
                    $type = $request->input('type');
                    return redirect()->route('dizhiliebiao', ['message' => '该地址不在所选区域，可在首页更改区域.', 'order' => $order, 'type' => $type]);
                } else {
                    return redirect()->route('dizhiliebiao', ['message' => '该地址不在所选区域，可在首页更改区域.']);
                }
            }

            $address->primary = true;
            $address->save();

            //as the address changed, we should reset the wechat product's prices
//            $this->reset_wechat_order_product_price();
        }

        if ($request->has('order') and $request->has('type')) {
            $order = $request->input('order');
            $type = $request->input('type');
            return redirect()->route('show_xuedan', ['order' => $order, 'type' => $type]);
        } else {
            return redirect()->route('querendingdan');
        }

    }


    //add one product in cart
    public function tianjiadingdan(Request $request)
    {

        $factory_id = session('factory_id');
        $factory = Factory::find($factory_id);

        $product_id = $request->input("product");
        $product = Product::find($product_id);

        if ($request->has('previous')) {
            $previous = $request->input('previous');
        } else {
            $previous = "none";
        }

        //Product image
        $dest_dir = url('/img/product/logo/');

        $dest_dir = str_replace('\\', '/', $dest_dir);

        $dest_dir .= '/';

        if ($product->photo_url1)
            $file1_path = $dest_dir . ($product->photo_url1);
        else
            $file1_path = "";

        if ($product->photo_url2)
            $file2_path = $dest_dir . ($product->photo_url2);
        else
            $file2_path = "";

        if ($product->photo_url3)
            $file3_path = $dest_dir . ($product->photo_url3);
        else
            $file3_path = "";

        if ($product->photo_url4)
            $file4_path = $dest_dir . ($product->photo_url4);
        else
            $file4_path = "";

        //product price with id and session addresss
        $address = session('address');

        $pp = ProductPrice::priceTemplateFromAddress($product_id, $address);


        if ($pp) {
            $month_price = $pp->month_price;
            $season_price = $pp->season_price;
            $half_year_price = $pp->half_year_price;
        } else {
            $pp = ProductPrice::where('product_id', $product_id)->get()->first();
            $month_price = $pp->month_price;
            $season_price = $pp->season_price;
            $half_year_price = $pp->half_year_price;
        }


        //gap day
        $gap_day = $factory->gap_day;
        $factory_order_types = $factory->factory_order_types;

        //show reviews
        $reviews = array();

        $all_review = Review::where('status', Review::REVIEW_STATUS_PASSED)->get()->all();

        if ($all_review) {
            foreach ($all_review as $review) {
                $order_id = $review->order_id;
                $order = Order::find($order_id);
                foreach ($order->order_products as $op) {
                    if ($op->product_id == $product_id) {
                        array_push($reviews, $review);
                        break;
                    }
                }
            }
        }

        $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $today = $today_date->format('Y-m-d');

        if ($request->has('order_id')) {
            $order_id = $request->input('order_id');

            if ($request->has('type')) {
                $type = $request->input('type');
                return view('weixin.tianjiadingdan', [
                    "product" => $product,
                    'file1' => $file1_path,
                    'file2' => $file2_path,
                    'file3' => $file3_path,
                    'file4' => $file4_path,
                    'month_price' => $month_price,
                    'season_price' => $season_price,
                    'half_year_price' => $half_year_price,
                    'gap_day' => $gap_day,
                    'factory_order_types' => $factory_order_types,
                    'reviews' => $reviews,
                    'today' => $today,
                    'previous' => $previous,
                    'order_id' => $order_id,
                    'type' => $type,
                ]);

            } else {
                return view('weixin.tianjiadingdan', [
                    "product" => $product,
                    'file1' => $file1_path,
                    'file2' => $file2_path,
                    'file3' => $file3_path,
                    'file4' => $file4_path,
                    'month_price' => $month_price,
                    'season_price' => $season_price,
                    'half_year_price' => $half_year_price,
                    'gap_day' => $gap_day,
                    'factory_order_types' => $factory_order_types,
                    'reviews' => $reviews,
                    'today' => $today,
                    'previous' => $previous,
                    'order_id' => $order_id,
                ]);

            }

        } else {
            return view('weixin.tianjiadingdan', [
                "product" => $product,
                'file1' => $file1_path,
                'file2' => $file2_path,
                'file3' => $file3_path,
                'file4' => $file4_path,
                'month_price' => $month_price,
                'season_price' => $season_price,
                'half_year_price' => $half_year_price,
                'gap_day' => $gap_day,
                'factory_order_types' => $factory_order_types,
                'reviews' => $reviews,
                'today' => $today,
                'previous' => $previous,
            ]);
        }

    }

    /*
     * Make wechat order product directly from the selected product
     */
    public function make_order_directly(Request $request)
    {
        $product_id = $request->input('product_id');
        $order_type = $request->input('order_type');
        $total_count = $request->input('total_count');

        $delivery_type = $request->input('delivery_type');

        $count_per = $custom_date = null;
        if ($delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY || $delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY) {
            $count_per = $request->input('count_per');
        } else {
            $custom_date = $request->input('custom_date');
            $custom_date = rtrim($custom_date, ',');
        }

        $factory_id = session('factory_id');
        $factory = Factory::find($factory_id);

        $wxuser_id = session('wechat_user_id');

        $wxuser = WechatUser::find($wxuser_id);

        $customer_id = $wxuser->customer_id;

        $address = session('address');

        $product_price = $this->get_product_price_by_order_type($order_type, $product_id, $address);

        $total_amount = $total_count * $product_price;

//        $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
//        $gap_day = intval($factory->gap_day);
//        $start_at = $today_date->modify("+" . $gap_day . " days");
//        $start_at = $start_at->format('Y-m-d');

        $start_at = $request->input('start_at');
        $start_at = new DateTime($start_at);
        $start_at = $start_at->format('Y-m-d');

        $group_id = $this->get_new_group_id();

        //add wechat order products
        $wcop = new WechatOrderProduct;
        $wcop->wxuser_id = $wxuser_id;
        $wcop->factory_id = $factory_id;
        $wcop->product_id = $product_id;
        $wcop->order_type = $order_type;
        $wcop->delivery_type = $delivery_type;
        $wcop->total_count = $total_count;
        $wcop->product_price = $product_price;

        if ($count_per)
            $wcop->count_per_day = $count_per;
        else
            $wcop->custom_order_dates = $custom_date;

        $wcop->total_amount = $total_amount;
        $wcop->start_at = $start_at;
        $wcop->group_id = $group_id;
        $wcop->save();

        //save group id for this direct order
        session(['group_id' => $group_id]);
        return response()->json(['status' => 'success']);
    }

    /*
     * insert order item from product details
     * Default: Month/1/10
    */
    public function insert_order_item_to_cart(Request $request)
    {
        if ($request->ajax()) {

            $product_id = $request->input('product_id');
            $order_type = $request->input('order_type');
            $total_count = $request->input('total_count');

            $delivery_type = $request->input('delivery_type');

            $count_per = $custom_date = null;
            if ($delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY || $delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY) {
                $count_per = $request->input('count_per');
            } else {
                $custom_date = $request->input('custom_date');
                $custom_date = rtrim($custom_date, ',');
            }

            $wxuser_id = session('wechat_user_id');
            $factory_id = session('factory_id');

            $factory = Factory::find($factory_id);

            $address = session('address');

            $product_price = $this->get_product_price_by_order_type($order_type, $product_id, $address);

            $total_amount = $total_count * $product_price;

//            $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
//            $gap_day = intval($factory->gap_day);
//            $start_at = $today_date->modify("+" . $gap_day . " days");
//            $start_at = $start_at->format('Y-m-d');

            $start_at = $request->input('start_at');
            $start_at = new DateTime($start_at);
            $start_at = $start_at->format('Y-m-d');

            //add wechat order products
            $wcop = new WechatOrderProduct;
            $wcop->wxuser_id = $wxuser_id;
            $wcop->factory_id = $factory_id;
            $wcop->product_id = $product_id;
            $wcop->order_type = $order_type;
            $wcop->delivery_type = $delivery_type;
            $wcop->total_count = $total_count;
            $wcop->product_price = $product_price;

            if ($count_per)
                $wcop->count_per_day = $count_per;
            else
                $wcop->custom_order_dates = $custom_date;

            $wcop->total_amount = $total_amount;
            $wcop->start_at = $start_at;
            $wcop->save();

            $wxorder_product_id = $wcop->id;

            //add to wxcart
            $wcc = new WechatCart;
            $wcc->wxuser_id = $wxuser_id;
            $wcc->wxorder_product_id = $wxorder_product_id;
            $wcc->save();

            return response()->json(['status' => 'success']);
        }
    }

    //show product list in cart
    public function gouwuche(Request $request)
    {
        if (!session('factory_id') && isset($_GET['state'])) {
            $factory_id = $_GET['state'];

            //save factory id in session
            $request->session()->put('factory_id', $factory_id);

            $factory = Factory::find($factory_id);
            $wechatObj = new WeChatesCtrl($factory->app_id, $factory->app_secret, $factory->app_encoding_key, $factory->app_token, $factory->name, $factory_id);
            $codees = $wechatObj->codes($_GET['code']);

            //save wechat user id
            $open_id = $codees['openid'];

            $wechat_user = WechatUser::where('openid', $open_id)->get()->first();
            if (!$wechat_user) {
                $wechat_user = new WechatUser;
                $wechat_user->openid = $open_id;
                $wechat_user->factory_id = $factory_id;
                $wechat_user->save();
            }
            $wechat_user_id = $wechat_user->id;

            session(['wechat_user_id' => $wechat_user_id]);

        } else {

            $factory_id = session('factory_id');
            $factory = Factory::find($factory_id);
            $wechat_user_id = session('wechat_user_id');
        }


        $wechat_user_id = session('wechat_user_id');

        $carts = WechatCart::where('wxuser_id', $wechat_user_id)->get();
        $cartn = $carts->count();

        $total_count = 0;
        $total_amount = 0;

        $cart_ids = "";
        foreach ($carts as $c) {
            $total_count += $c->order_item->total_count;
            $total_amount += $c->order_item->total_count * $c->order_item->product_price;

            if ($cart_ids)
                $cart_ids .= "," . $c->id;
            else
                $cart_ids = $c->id;
        }

        return view('weixin.gouwuche', [
            'carts' => $carts,
            'cartn' => $cartn,
            'total_count' => $total_count,
            'total_amount' => $total_amount,
            'cart_ids' => $cart_ids,
        ]);
    }

    //delete product from cart
    public function delete_cart(Request $request)
    {

        $cart_id = $request->input('cart_id');

        $cart = WechatCart::find($cart_id);

        if ($cart) {
            $order_item = $cart->order_item;
            $cart->delete();
            $order_item->delete();
        }

        return redirect()->route('gouwuche');
    }

    //delete selected products from cart
    public function delete_selected_wop(Request $request)
    {
        $cart_ids = $request->input('cart_ids');

        $cart_ids = explode(',', $cart_ids);

        foreach ($cart_ids as $cid) {
            $cart = WechatCart::find($cid);
            if ($cart) {
                $wop = $cart->order_item;
                $cart->delete();
                $wop->delete();
            }
        }

        return response()->json(['status' => 'success']);
    }

    //make wop group with cart_ids
    public function make_wop_group(Request $request)
    {
        $cart_ids = $request->input('cart_ids');

        $cart_ids = explode(',', $cart_ids);

        $group_id = $this->get_new_group_id();
        foreach ($cart_ids as $cid) {
            $cart = WechatCart::find($cid);
            $wop = $cart->order_item;
            if ($wop) {
                $wop->group_id = $group_id;
                $wop->save();
            }
        }

        //store this group id for cart to session
        session(['group_id' => $group_id]);

        return response()->json(['status' => 'success']);

    }

    public function get_new_group_id()
    {
        $result = 0;

        $wops = WechatOrderProduct::all();
        foreach ($wops as $wop) {
            if ($wop->group_id > $result) {
                $result = $wop->group_id;
            }
        }
        $result += 1;
        return $result;
    }

    //edit one product in cart
    public function bianjidingdan(Request $request)
    {
        $factory_id = session('factory_id');
        $factory = Factory::find($factory_id);

        $wechat_order_product_id = $request->input('wechat_opid');
        $wop = WechatOrderProduct::find($wechat_order_product_id);

        $wxuser_id = session('wechat_user_id');
        $wxuser = WechatUser::find($wxuser_id);

        $address = session('address');

        $product = $wop->product;
        $product_id = $product->id;

        //Product image
        $dest_dir = url('/img/product/logo/');

        $dest_dir = str_replace('\\', '/', $dest_dir);

        $dest_dir .= '/';

        if ($product->photo_url1)
            $file1_path = $dest_dir . ($product->photo_url1);
        else
            $file1_path = "";

        if ($product->photo_url2)
            $file2_path = $dest_dir . ($product->photo_url2);
        else
            $file2_path = "";

        if ($product->photo_url3)
            $file3_path = $dest_dir . ($product->photo_url3);
        else
            $file3_path = "";

        if ($product->photo_url4)
            $file4_path = $dest_dir . ($product->photo_url4);
        else
            $file4_path = "";

        $pp = ProductPrice::priceTemplateFromAddress($product_id, $address);

        if (!$pp) {
            $pp = ProductPrice::where('product_id', $product_id)->get()->first();
        }

        $month_price = $pp->month_price;
        $season_price = $pp->season_price;
        $half_year_price = $pp->half_year_price;

        //gap day
        $gap_day = $factory->gap_day;
        $factory_order_types = $factory->factory_order_types;

        //get num of order days
        $total_count = $wop->total_count;
        if ($wop->delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY || $wop->delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY) {
            $count_per_day = $wop->count_per_day;
            $order_day_num = round($total_count / $count_per_day);
        } else {
            $ord_ctrl = new OrderCtrl;
            $order_day_num = $ord_ctrl->get_number_of_days_for_wechat_product($wop->id);
        }

        //show reviews
        $reviews = array();

        $all_review = Review::where('status', Review::REVIEW_STATUS_PASSED)->get()->all();

        foreach ($all_review as $review) {
            $order_id = $review->order_id;
            $order = Order::find($order_id);
            foreach ($order->order_products as $op) {
                if ($op->product_id == $product_id) {
                    array_push($reviews, $review);
                    break;
                }
            }

        }

        $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $today = $today_date->format('Y-m-d');

        $from = $request->input('from');
        if ($from == "queren") {
            $group_id = session('group_id');

            if ($request->has('for')) {
                //from xuedan
                return view('weixin.bianjidingdan', [
                    "product" => $product,
                    'file1' => $file1_path,
                    'file2' => $file2_path,
                    'file3' => $file3_path,
                    'file4' => $file4_path,
                    'month_price' => $month_price,
                    'season_price' => $season_price,
                    'half_year_price' => $half_year_price,
                    'gap_day' => $gap_day,
                    'factory_order_types' => $factory_order_types,
                    'wop' => $wop,
                    'group_id' => $group_id,
                    'order_day_num' => $order_day_num,
                    'reviews' => $reviews,
                    'today' => $today,
                    'previous' => 'queren',
                    'for' => "xuedan",
                ]);
            } else {
                //from queren dingdan

                return view('weixin.bianjidingdan', [
                    "product" => $product,
                    'file1' => $file1_path,
                    'file2' => $file2_path,
                    'file3' => $file3_path,
                    'file4' => $file4_path,
                    'month_price' => $month_price,
                    'season_price' => $season_price,
                    'half_year_price' => $half_year_price,
                    'gap_day' => $gap_day,
                    'factory_order_types' => $factory_order_types,
                    'wop' => $wop,
                    'group_id' => $group_id,
                    'order_day_num' => $order_day_num,
                    'reviews' => $reviews,
                    'today' => $today,
                    'previous' => 'queren',
                ]);
            }


        } else if ($from == "gouwuche") {

            //from gouwuche
            return view('weixin.bianjidingdan', [
                "product" => $product,
                'file1' => $file1_path,
                'file2' => $file2_path,
                'file3' => $file3_path,
                'file4' => $file4_path,
                'month_price' => $month_price,
                'season_price' => $season_price,
                'half_year_price' => $half_year_price,
                'gap_day' => $gap_day,
                'factory_order_types' => $factory_order_types,
                'wop' => $wop,
                'order_day_num' => $order_day_num,
                'reviews' => $reviews,
                'today' => $today,
                'previous' => 'gouwuche',
            ]);
        }

    }

    //save changed item from bianjidingdan
    public function save_changed_order_item(Request $request)
    {
        if ($request->ajax()) {

            $wopid = $request->input('wechat_order_product_id');
            $product_id = $request->input('product_id');
            $order_type = $request->input('order_type');
            $total_count = $request->input('total_count');
            $delivery_type = $request->input('delivery_type');

            $count_per = $custom_date = null;
            if ($delivery_type == DeliveryType::DELIVERY_TYPE_EVERY_DAY || $delivery_type == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY) {
                $count_per = $request->input('count_per');
            } else {
                $custom_date = $request->input('custom_date');
                $custom_date = rtrim($custom_date, ',');
            }

            $wxuser_id = session('wechat_user_id');

            $factory_id = session('factory_id');
            $factory = Factory::find($factory_id);

            $address = session('address');

            $product_price = $this->get_product_price_by_order_type($order_type, $product_id, $address);

            $total_amount = $total_count * $product_price;

//            $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
//            $gap_day = intval($factory->gap_day);
//            $start_at = $today_date->modify("+" . $gap_day . " days");
            $start_at = $request->input('start_at');
            $start_at = new DateTime($start_at);
            $start_at = $start_at->format('Y-m-d');

            //add wechat order products
            $wcop = WechatOrderProduct::find($wopid);

            $wcop->order_type = $order_type;
            $wcop->delivery_type = $delivery_type;
            $wcop->total_count = $total_count;
            $wcop->product_price = $product_price;
            if ($count_per)
                $wcop->count_per_day = $count_per;
            else
                $wcop->custom_order_dates = $custom_date;

            $wcop->total_amount = $total_amount;
            $wcop->start_at = $start_at;
            $wcop->save();

            return response()->json(['status' => 'success']);
        }
    }

    //make order by group
    public function make_order_by_group(Request $request)
    {
        $factory_id = session('factory_id');
        $wxuser_id = session('wechat_user_id');
        $wechat_user = WechatUser::find($wxuser_id);

        $comment = $request->input('comment');
        $group_id = $request->input('group_id');

        $addr_obj_id = $request->input('addr_obj_id');
        $addr_obj = WechatAddress::find($addr_obj_id);

        if (!$addr_obj)
            return response()->json(['status' => 'err_stop', 'message' => '地址和电话号码不存在.']);

        //check session address and primary address
        $addr = session('address');
        $order_address = $addr_obj->address;

        if (strpos($order_address, $addr) === false) {
            return response()->json(['status' => 'err_stop', 'message' => '该地址不在所选区域，可在首页更改区域.']);
        }

        $customer = Customer::where('phone', $addr_obj->phone)->get()->first();

        $orderctrl = new OrderCtrl();
        if (!$customer) {
            //create new customer
            $station = null;
            //get station and milkman from factory and primary_address
            $station_milkman = $orderctrl->get_station_milkman_with_address_from_factory($factory_id, $order_address, $station);

            if ($station_milkman == OrderCtrl::NOT_EXIST_DELIVERY_AREA) {
                return response()->json(['status' => 'fail', 'message' => '该地区没有覆盖可配送的范围.']);
            } else if ($station_milkman == OrderCtrl::NOT_EXIST_STATION) {
                return response()->json(['status' => 'fail', 'message' => '没有奶站.']);
            } else if ($station_milkman == OrderCtrl::NOT_EXIST_MILKMAN) {
                return response()->json(['status' => 'fail', 'message' => '没有递送人.']);
            }

            $customer = null;
            foreach ($station_milkman as $delivery_station_id => $milkman_id) {
                //make new customer and change product price
                $customer = new Customer;
                $customer->phone = $addr_obj->phone;
                $customer->name = $addr_obj->name;
                $customer->address = $addr_obj->address . ' ' . $addr_obj->sub_address;
                $customer->station_id = $delivery_station_id;
                $customer->factory_id = $factory_id;
                $customer->milkman_id = $milkman_id;
                break;
            }
            if ($customer) {
                $customer->save();
            }
        }

        $customer_id = $customer->id;

        $wops = WechatOrderProduct::where('group_id', $group_id)->get()->all();
        $total_amount = 0;
        foreach ($wops as $wop) {
            $total_amount += $wop->total_amount;
        }

        $station_id = $customer->station_id;

        $order_checker = OrderCheckers::where('station_id', $station_id)->where('is_active', 1)->get()->first();

        $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $today = $today_date->format('Y-m-d');

        //start at: wechat order product's first deliver at
        $start_at = $wechat_user->order_start_at($group_id);

        //make order
        $order = new Order;
        $order->factory_id = $factory_id;
        $order->customer_id = $customer_id;
        $order->phone = $addr_obj->phone;
        $order->address = $addr_obj->address . ' ' . $addr_obj->sub_address;
        $order->order_property_id = OrderProperty::ORDER_PROPERTY_NEW_ORDER;
        $order->station_id = $station_id;
        $order->order_checker_id = $order_checker->id;
        $order->milk_box_install = ($customer->has_milkbox) ? 1 : 0;
        $order->total_amount = $total_amount;
        $order->remaining_amount = $total_amount;
        $order->order_by_milk_card = 0;
        $order->trans_check = 0;
        $order->payment_type = PaymentType::PAYMENT_TYPE_WECHAT;
        $order->status = Order::ORDER_NEW_WAITING_STATUS;
        $order->ordered_at = $today;
        $order->start_at = $start_at;
        $order->delivery_time = 1;//default
        $order->flat_enter_mode_id = 2;//default
        $order->delivery_station_id = $station_id;
        $order->comment = $comment;
        $order->save();

        $order_id = $order->id;
        $order->number = $orderctrl->order_number($factory_id, $station_id, $customer_id, $order_id);
        //order's unique number: format (F_fid_S_sid_C_cid_O_orderid)
        $order->save();

        //make order products
        $this->make_order_products_and_delivery_plan($order_id, $group_id, $orderctrl);


        //notification to factory and wechat
        $notification = new NotificationsAdmin;
        $notification->sendToFactoryNotification($factory_id, FactoryNotification::CATEGORY_CHANGE_ORDER, "微信下单成功", $customer->name . "已经下单, 请管理员尽快审核");
        $notification->sendToWechatNotification($customer_id, '您已经成功下单，我们会尽快安排客服核对您的订单信息');


        //if payment fails, delete order
        return response()->json(['status' => 'success', 'order_id' => $order_id]);
    }

    //make order based on crated wechat order products
    public function make_order_from_wopids(Request $request)
    {
        $factory_id = session('factory_id');
        $wxuser_id = session('wechat_user_id');
        $wechat_user = WechatUser::find($wxuser_id);

        $wopids = $request->input('wopids');
        $comment = $request->input('comment');

        $customer_id = $wechat_user->customer_id;
        if (!$customer_id) {
            return response()->json(['status' => 'fail', 'message' => '您应该创建您的用户帐户']);
        }
        $customer = Customer::find($customer_id);

        $total_amount = 0;
        $wopids = explode(',', $wopids);
        foreach ($wopids as $wopid) {
            $wop = WechatOrderProduct::find($wopid);
            $total_amount += $wop->total_amount;
        }

        $station_id = $customer->station_id;

        $order_checker = OrderCheckers::where('station_id', $station_id)->where('is_active', 1)->get()->first();

        $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $today = $today_date->format('Y-m-d');

        //start at: wechat order product's first deliver at
        $start_at = $wechat_user->order_start_at(group_id);

        //make order
        $order = new Order;
        $order->factory_id = $factory_id;
        $order->customer_id = $customer_id;
        $order->phone = $customer->phone;
        $order->address = $customer->address;
        $order->order_property_id = OrderProperty::ORDER_PROPERTY_XUDAN_ORDER;
        $order->station_id = $station_id;
        $order->order_checker_id = $order_checker->id;
        $order->milk_box_install = ($customer->has_milkbox) ? 1 : 0;
        $order->total_amount = $total_amount;
        $order->remaining_amount = $total_amount;
        $order->order_by_milk_card = 0;
        $order->trans_check = 0;
        $order->payment_type = PaymentType::PAYMENT_TYPE_WECHAT;
        $order->status = Order::ORDER_NEW_WAITING_STATUS;
        $order->ordered_at = $today;
        $order->start_at = $start_at;
        $order->delivery_time = 1;//default
        $order->flat_enter_mode_id = 2;//default
        $order->delivery_station_id = $station_id;
        $order->comment = $comment;
        $order->save();

        $orderctrl = new OrderCtrl();

        $order_id = $order->id;
        $order->number = $orderctrl->order_number($factory_id, $station_id, $customer_id, $order_id);
        //order's unique number: format (F_fid_S_sid_C_cid_O_orderid)
        $order->save();

        $order_id = $order->id;

        //make order products
        $this->make_order_products_and_delivery_plan($wxuser_id, $order_id, $orderctrl);

        //delete cart and order item
        $this->remove_wechat_order_products_from_wxuser($wopids);

        //notification to factory and wechat
        $notification = new NotificationsAdmin;
        $notification->sendToFactoryNotification($factory_id, FactoryNotification::CATEGORY_CHANGE_ORDER, "微信下单成功", $customer->name . "已经下单, 请管理员尽快审核");
        $notification->sendToWechatNotification($customer_id, '您已经成功下单，我们会尽快安排客服核对您的订单信息');


        return response()->json(['status' => 'success', 'order_id' => $order_id]);


    }


    //make order products for wxuser
    public function make_order_products_and_delivery_plan($order_id, $group_id, $orderctrl)
    {
        $order = Order::find($order_id);

        $delivery_station_id = $order->delivery_station_id;
        $milkman_id = $order->milkman_id;
        $factory_id = $order->factory_id;

        $wops = WechatOrderProduct::where('group_id', $group_id)->get()->all();

        foreach ($wops as $wop) {
            //wechat order product
            $op = new OrderProduct;
            $op->order_id = $order_id;
            $op->product_id = $wop->product_id;
            $op->order_type = $wop->order_type;
            $op->delivery_type = $wop->delivery_type;
            $op->product_price = $wop->product_price;
            $op->total_count = $wop->total_count;
            $op->total_amount = $wop->total_amount;
            $op->avg = $wop->avg;
            $op->count_per_day = $wop->count_per_day;
            $op->custom_order_dates = $wop->custom_order_dates;
            $op->start_at = $wop->start_at;
            $op->save();

            //$milkmanId, $stationId, $startAt, $orderProduct, $status, $count

            //establish plan
            $orderctrl->establish_plan($op, $factory_id, $delivery_station_id, $milkman_id);
        }
    }

    //remove cart and wechat order product from wxuser
    public function remove_cart_by_group($group_id)
    {
        $wops = WechatOrderProduct::where('group_id', $group_id)->get()->all();
        foreach ($wops as $wop) {
            $cart = WechatCart::where('wxorder_product_id', $wop->id)->get()->first();
            if ($cart)
                $cart->delete();
            $wop->delete();
        }

    }

    //remove wechat order products from wxuser related with xuedan
    public function remove_wechat_order_products_from_wxuser($wopids)
    {
        foreach ($wopids as $wopid) {
            $wop = WechatOrderProduct::find($wopid);
            $wop->delete();
        }
    }

    //Show Xuedan page
    public function show_xuedan(Request $request)
    {
        $wxuser_id = session('wechat_user_id');

        $order_id = $request->input('order');
        $order = Order::find($order_id);
        $factory_id = $order->factory_id;

        $order_products = $order->order_products;

        $wechat_order_products = array();

        $factory = Factory::find($factory_id);
        $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $today = $today_date->format('Y-m-d');
        $gap_day = intval($factory->gap_day);

        $start_at_new = $today_date->modify("+" . $gap_day . " days");
        $start_at_new = $start_at_new->format('Y-m-d');

        $wopids = "";

        $group_id = $this->get_new_group_id();

        //make new wechat order products based on order products
        $total_amount = 0;
        foreach ($order_products as $op) {

            //start_at decision
            $start_at = $op->start_at;
            if (strtotime($start_at) < strtotime($start_at_new)) {
                $start_at = $start_at_new;
            }
            $wcop = new WechatOrderProduct;
            $wcop->wxuser_id = $wxuser_id;
            $wcop->factory_id = $factory_id;
            $wcop->product_id = $op->product_id;
            $wcop->order_type = $op->order_type;
            $wcop->delivery_type = $op->delivery_type;
            $wcop->total_count = $op->total_count;
            $wcop->product_price = $op->product_price;
            $wcop->count_per_day = $op->count_per_day;
            $wcop->custom_order_dates = $op->custom_order_dates;
            $wcop->total_amount = $op->total_amount;
            $wcop->start_at = $start_at;
            $wcop->group_id = $group_id;
            $wcop->save();

            if (!$wopids)
                $wopids .= "," . $wcop->id;
            else
                $wopids .= $wcop->id;

            array_push($wechat_order_products, $wcop);

            $total_amount += $wcop->total_amount;
        }

        $wechat_user = WechatUser::find($wxuser_id);
        $customer_id = $wechat_user->customer_id;
        $customer = Customer::find($customer_id);

        /*
         * Find wechataddress same with order's info,
         * if Exist, make it as primary and return
         * if not, create new wechat address and make primary
         * */
        $addr_obj = WechatAddress::where('wxuser_id', $wxuser_id)
            ->where('address', $order->main_address)
            ->where('phone', $order->phone)
            ->where('name', $order->customer_name)
            ->where('sub_address', $order->sub_address)
            ->get()->first();

        if (!$request->has('from') and !$addr_obj) {
            //use origin order's address and phone, customer_name
            $addr_obj = new WechatAddress;
            $addr_obj->wxuser_id = $wxuser_id;
            $addr_obj->name = $order->customer_name;
            $addr_obj->phone = $order->phone;
            $addr_obj->address = $order->main_address;
            $addr_obj->sub_address = $order->sub_address;
            $addr_obj->primary = 0;
            $addr_obj->save();
        }


        //as this is xuedan, that means before, this has passed
        $passed = true;
        session(['group_id' => $group_id]);

        if (!$wechat_user)
            abort(403);
        $openid = $wechat_user->openid;
        $total_amount = round($total_amount, 3);

        //show delivery plan before real order
        $plans = [];
        if (count($wechat_order_products) > 0) {
            foreach ($wechat_order_products as $wechat_order_product) {
                $wop_plans = $wechat_order_product->get_temp_plans();
                foreach ($wop_plans as $wop_plan) {
                    $plans[] = $wop_plan;
                }
            }
        }

        $type = $request->input('type');
        return view('weixin.querendingdan', [
            'addr_obj' => $addr_obj,
            'customer' => $customer,
            'wechat_order_products' => $wechat_order_products,
            'group_id' => $group_id,
            'wxuser_id' => $wxuser_id,
            'passed' => $passed,
            'for' => 'xuedan',
            'openid' => $openid,
            'total_amount' => $total_amount,
            'plans' => $plans,
            'order' => $order_id,
            'type' => $type,
            'today' => $today,
        ]);
    }

    public function get_count_by_order_type($order_type)
    {
        if ($order_type == OrderType::ORDER_TYPE_MONTH)
            return 30;
        else if ($order_type == OrderType::ORDER_TYPE_SEASON)
            return 90;
        else if ($order_type == OrderType::ORDER_TYPE_HALF_YEAR)
            return 180;
    }

    //check current group total count condition
    public function check_total_count($group_id)
    {
        //get total bottle count of this group
        $total = $max = 0;
        $wechat_order_products = WechatOrderProduct::where('group_id', $group_id)->where('group_id', '!=', null)->get()->all();
        foreach ($wechat_order_products as $wop) {
            $total += $wop->total_count;
            $count_by_order_type = $this->get_count_by_order_type($wop->order_type);
            if ($max < $count_by_order_type) {
                $max = $count_by_order_type;
            }
        }

        if ($total < $max) {
            return false;
        } else {
            return true;
        }

    }

    //Confirm Wechat order products to be included in Order
    public function querendingdan(Request $request)
    {
        $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $today = $today_date->format('Y-m-d');

        $wechat_user_id = session('wechat_user_id');
        $wechat_user = WechatUser::find($wechat_user_id);

        if (!$wechat_user)
            abort(403);

        $primary_addr_obj = WechatAddress::where('wxuser_id', $wechat_user_id)->where('primary', 1)->get()->first();

        $group_id = session('group_id');
        if ($request->has('group_id')) {
            $group_id = $request->input('group_id');
            session(['group_id' => $group_id]);
        }

        $wechat_order_products = WechatOrderProduct::where('group_id', $group_id)->where('group_id', '!=', null)->get()->all();

        $openid = $wechat_user->openid;

        //set new product price on primary address for the wechat order product
        $total_amount = 0;
        if ($primary_addr_obj) {
            $primary_address = $primary_addr_obj->address;

            if ($wechat_order_products) {
                foreach ($wechat_order_products as $wop) {
                    $product_id = $wop->product_id;

                    $new_product_price_tp = ProductPrice::priceTemplateFromAddress($product_id, $primary_address);

                    if ($new_product_price_tp) {
                        if ($wop->order_type == OrderType::ORDER_TYPE_MONTH) {
                            $wop->product_price = $new_product_price_tp->month_price;
                        } else if ($wop->order_type == OrderType::ORDER_TYPE_SEASON) {
                            $wop->product_price = $new_product_price_tp->season_price;
                        } else if ($wop->order_type == OrderType::ORDER_TYPE_HALF_YEAR) {
                            $wop->product_price = $new_product_price_tp->half_year_price;
                        } else {
                            $wop->product_price = $new_product_price_tp->settle_price;
                        }

                        $wop->save();

                        $wop->total_amount = $wop->product_price * $wop->total_count;
                        $wop->save();

                        $total_amount += $wop->total_amount;
                    } else {

                        $wop->product_price = null;
                        $wop->total_amount = null;
                        $total_amount = null;

                        //there is not product price for this primary address
                        return view('weixin.querendingdan', [
                            'addr_obj' => $primary_addr_obj,
                            'wechat_order_products' => $wechat_order_products,
                            'group_id' => $group_id,
                            'wxuser_id' => $wechat_user_id,
                            'message' => '未定义地址的产品价格',
                            'total_amount' => $total_amount,
                            'openid' => $openid,
                        ]);
                    }

                }
            }
        }

        if ($this->check_total_count($group_id)) {
            $passed = true;
            $message = "";
        } else {
            $passed = false;
            $message = "订单数量总合得符合订单类型条件";
        }

        $total_amount = round($total_amount, 3);

        //show delivery plan before real order
        $plans = [];
        if (count($wechat_order_products) > 0) {
            foreach ($wechat_order_products as $wechat_order_product) {
                $wop_plans = $wechat_order_product->get_temp_plans();
                foreach ($wop_plans as $wop_plan) {
                    $plans[] = $wop_plan;
                }
            }
        }

        return view('weixin.querendingdan', [
            'addr_obj' => $primary_addr_obj,
            'wechat_order_products' => $wechat_order_products,
            'group_id' => $group_id,
            'wxuser_id' => $wechat_user_id,
            'passed' => $passed,
            'message' => $message,
            'total_amount' => $total_amount,
            'openid' => $openid,
            'plans' => $plans,
            'today' => $today,
        ]);

    }

    public function addPingjia(Request $request)
    {
        $order_id = $request->input('order_id');
        $marks = $request->input('marks');
        $content = $request->input('contents');
        $current_datetime = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $current_datetime_str = $current_datetime->format('Y-m-d H:i:s');

        $order = Order::find($order_id);
        $customer_id = $order->customer_id;

//        foreach($order->order_products as $op)
//        {
        $review = new Review;
        $review->mark = $marks;
        $review->content = $content;
        $review->order_id = $order_id;
        $review->customer_id = $customer_id;
//            $review->product_id = $op->product_id;
        $review->created_at = $current_datetime_str;
        $review->status = Review::REVIEW_STATUS_WAITTING;
        $review->save();
//        }

        return response()->json(['status' => 'success', 'order_id' => $order_id]);

    }

    //show check telephone number page
    public function dengji(Request $request)
    {
        if ($request->has('to')) {
            $to = $request->input('to');
            return view('weixin.dengji', [
                'to' => $to,
            ]);
        } else {
            return view('weixin.dengji', [
            ]);
        }

    }

    public function dengchu(Request $request)
    {
        session(['loggedin' => false]);
        //set wxuser's customer id as null
        $wxuser_id = session('wechat_user_id');
        $wxuser = WechatUser::find($wxuser_id);
        if ($wxuser) {
            $wxuser->customer_id = null;
            $wxuser->save();

        }

        return redirect()->route('weixin_qianye');
    }

    //send verify code to phone
    public function send_verify_code_to_phone(Request $request)
    {
        $phone = $request->input('phone_number');
        $code = $this->generate_verify_code();
        $wxuser_id = session('wechat_user_id');
        $wxuser = WechatUser::find($wxuser_id);
        //if customer not exist for this wxuser, fail
        $customer = Customer::where('phone', $phone)->get()->first();

        if ($customer) {
            $code = "11111";
            $wxuser->phone_verify_code = $code;
            $wxuser->save();

            // 发送验证码
//            $smsCtrl = new YimeiSmsCtrl();
//            $smsCtrl->sendSMS($phone, $code);

            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'fail', 'xid' => $wxuser_id, 'phone' => $phone]);
        }
    }

    public function generate_verify_code()
    {
        $code = rand(10000, 99999); // random 4 digit code
        return $code;
    }

    public function check_verify_code(Request $request)
    {
        $phone_number = $request->input('phone_number');
        $code = $request->input('code');

        $wxuser_id = session('wechat_user_id');
        $wxuser = WechatUser::find($wxuser_id);

        $customer = Customer::where('phone', $phone_number)->get()->first();

        if ($code != '' && $wxuser && $customer && $wxuser->phone_verify_code == $code) {

            $customer_id = $customer->id;
            $wxuser->customer_id = $customer_id;
            $wxuser->name = $customer->name;
            $wxuser->phone_verify_code = "";
            $wxuser->save();

            session(['loggedin' => true]);

            return response()->json(['status' => 'success']);
        } else
            return response()->json(['status' => 'fail']);
    }

    public function get_product_price_by_order_type($order_type, $product_id, $address)
    {
        $product_price_template = ProductPrice::priceTemplateFromAddress($product_id, $address);

        if (!$product_price_template) {
            //give temp product price
            $product_price_template = ProductPrice::where('product_id', $product_id)->get()->first();
        }

        if ($order_type == OrderType::ORDER_TYPE_MONTH) {
            $product_price = $product_price_template->month_price;
        } else if ($order_type == OrderType::ORDER_TYPE_SEASON) {
            $product_price = $product_price_template->season_price;
        } else {
            //half year price
            $product_price = $product_price_template->half_year_price;
        }

        return $product_price;
    }

    public function show_session()
    {
        var_dump(session('change_order_product'));
    }

}