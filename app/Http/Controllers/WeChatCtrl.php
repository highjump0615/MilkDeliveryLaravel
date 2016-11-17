<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Model\BasicModel\Address;
use App\Model\BasicModel\Customer;
use App\Model\BasicModel\PaymentType;
use App\Model\DeliveryModel\DeliveryType;
use App\Model\DeliveryModel\MilkManDeliveryPlan;
use App\Model\FactoryModel\Factory;
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
use App\Model\WechatModel\WechatUser;
use Auth;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;


class WeChatCtrl extends Controller
{
    //First page
    public function showIndexPage(Request $request)
    {

        //todo: get factory_id from phone owner's address or phone number
        $factory_id = 1;

        $factory = Factory::find($factory_id);
        if ($factory == null)
            abort(403);

        //save factory id in session
        $request->session()->put('factory_id', $factory_id);

        //todo
        $wechat_user_id =  WechatUser::all()->first()->id;
        session(['wechat_user_id'=>$wechat_user_id]);

        //add verified flag
        if(!session('verified'))
        {
            $request->session()->put('verified', 'no');
        }

        /*
         * Address in Session
         *
         * if this user is a new user, the address will be stored as the address from weixin api
         * currently, until weixin api enabled, use the default address with beijing
         *
         * if the user has his wechat user account
         *  find his primary address in wechat address list and save his primary address as session address
         *
         * if he has not primary address, if the this user has customer account,
         * save the customer addresss as session address
         *
         * */

        //get address from weixin api and save them
        $address = session('address');

//        if(!$address) {
//
//            //if wechat not exist, create new wechat user
//            $wechat_user = WechatUser::find($wechat_user_id);
//            if (!$wechat_user) {
//                $wechat_user = new WechatUser;
//                $wechat_user->save();
//                $wechat_user_id = $wechat_user->id;
//
//                session(['wechat_user_id'=>$wechat_user_id]);
//                session(['address' => '北京 北京市']);
//
//                $address = session('address');
//
//            } else {
//
//                //get this user's address and save in session
//                $primary_addr = WechatAddress::where('wxuser_id', $wechat_user_id)->where('primary', 1)->get()->first();
//                if ($primary_addr) {
//                    session(['address' => $primary_addr->address]);
//                } else {
//
//                    $primary_addr = WechatAddress::where('wxuser_id', $wechat_user_id)->get()->first();
//                    if ($primary_addr) {
//                        session(['address' => $primary_addr->address]);
//                    } else {
//                        //if customer exist for this user, save customer's address on session address
//                        $customer_id = $wechat_user->customer_id;
//                        if ($customer_id) {
//                            $customer = Customer::find($customer_id);
//                            if ($customer) {
//                                session(['address' => $customer->main_addr]);
//                            }
//                        }
//
//                    }
//                }
//
//                $address = session('address');
//            }
//        }

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
            ->take(4)
            ->get();

        $cartn = WechatCart::where('wxuser_id', $wechat_user_id)->get()->count();


        $result = array();
        //province list
        $provinces = Address::where('level', 1)->where('factory_id', $factory_id)
            ->where('parent_id', 0)->where('is_active', 1)->where('is_deleted', 0)->get();
        foreach($provinces as $province)
        {
            $cities = Address::where('level', 2)->where('factory_id', $factory_id)
                ->where('parent_id', $province->id)->where('is_active', 1)->where('is_deleted', 0)->get();
            foreach($cities as $city)
            {
                $result[$province->name][]= $city->name;
            }
        }

        if($address)
        {
            $addr = explode(' ', $address);
            $province_name = $addr[0];
            $city_name = $addr[1];

            return view('weixin.index', [
                'banners' => $banners,
                'promos' => $promos,
                'products' => $products,
                'address' => $address,
                'prov'=>$province_name,
                'city'=>$city_name,
                'cartn' => $cartn,
                'addr_list' => $result,
            ]);

        } else {
            return view('weixin.index', [
                'banners' => $banners,
                'promos' => $promos,
                'products' => $products,
                'address' => $address,
                'cartn' => $cartn,
                'addr_list' => $result,
            ]);
        }



    }

    public  function set_session_address(Request $request)
    {
        $province = $request->input('province');
        $city = $request->input('city');

        $addr = $province." ".$city;

        session(['address'=>$addr]);

        return response()->json(['status'=>'success']);
    }

    public function gerenzhongxin(Request $request)
    {
        $wechat_user_id = session('wechat_user_id');

        $carts = WechatCart::where('wxuser_id', $wechat_user_id)->get();
        $cartn = $carts->count();

        //TODO:
        $me = WechatUser::all()->first();

        return view('weixin.gerenzhongxin', [
            'user' => $me,
            'cartn'=> $cartn,
        ]);
    }

    //Show delivery plans on full calendar for change delivery plan on one date
    public function dingdanrijihua(Request $request)
    {

        $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $today = $today_date->format('Y-m-d');

        $wxuser_id = session('wechat_user_id');
        $factory_id = session('factory_id');

        //show all plans for order in passed, on_delivery, finished

        $wxuser = WechatUser::find($wxuser_id);
        $customer_id = $wxuser->customer_id;

        if (!$customer_id) {
            $plans = array();
        } else {
            $plans = array();

            $orders = Order::where('customer_id', $customer_id)->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                ->where(function ($query) {
//                    $query->where('status', Order::ORDER_FINISHED_STATUS);
                    $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
//                    $query->orwhere('status', Order::ORDER_PASSED_STATUS);
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

        if($request->has('from'))
        {
            return view('weixin.dingdanrijihua', [
                'plans' => $plans,
                'today' => $today,
                'from' => 'geren',
            ]);
        } else {
            return view('weixin.dingdanrijihua', [
                'plans' => $plans,
                'today' => $today,
            ]);
        }

    }


    //show order product change page
    public function dingdanxiugai(Request $request)
    {
        $order_product_id = $request->input('order-item');

        $order_product = OrderProduct::find($order_product_id);

        $factory_id = session('factory_id');
        $factory = Factory::find($factory_id);

        if ($factory) {
            $products = $factory->active_products;
            $factory_order_types = $factory->factory_order_types;
        } else {
            abort(403);
        }

        return view('weixin.dingdanxiugai', [
            'order_product' => $order_product,
            'products' => $products,
            'factory_order_types' => $factory_order_types,
        ]);
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
        $total_count = floor($total_amount/$product_price);

        if($order_type == OrderType::ORDER_TYPE_MONTH)
            $avg = round($total_count/30, 1);
        else if($order_type == OrderType::ORDER_TYPE_SEASON)
            $avg = round($total_count/90, 1);
        else
            $avg = round($total_count/180, 1);

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

        return response()->json(['status'=>'success']);
    }


    //show change delivery plan on one date
    public function danrixiugai(Request $request)
    {
        $date = $request->input('date');
        $date_time = new DateTime($date);
        $date = $date_time->format('Y-m-d');

        //get plans for the date
        $wechat_user_id = session('wechat_user_id');
        $wechat_user = WechatUser::find($wechat_user_id);
        $customer_id = $wechat_user->customer_id;
        $customer = Customer::find($customer_id);

        $plans = $customer->get_wechat_plans_for_date($date);
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

        foreach ($plans_data as $plan_data) {
            $plan_id = $plan_data[0];
            $origin = $plan_data[1];
            $change = $plan_data[2];
            $count++;

            $plan = MilkManDeliveryPlan::find($plan_id);

            $order_id = $plan->order_id;
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
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'fail', 'messages' => $messages]);
        }
    }

    public function dingdanliebiao(Request $request)
    {
        $type = $request->input('type');

        $wechat_user_id = session('wechat_user_id');
        if(!$wechat_user_id)
            $wechat_user_id = 10;
        $wechat_user = WechatUser::find($wechat_user_id);

        if(!$wechat_user)
            abort(403);

        $customer_id = $wechat_user->customer_id;

        $orders = Order::where('is_deleted', 0)->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
            ->where('customer_id', $customer_id)->orderBy('ordered_at', 'desc')->get();

        if ($type == 'waiting') {
            $orders = Order::where('is_deleted', 0)
                ->where('status', Order::ORDER_WAITING_STATUS)
                ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                ->where('customer_id', $customer_id)
                ->orderBy('ordered_at', 'desc')
                ->get();

        } else if ($type == 'finished') {
            $orders = Order::where('is_deleted', 0)
                ->where('status', Order::ORDER_FINISHED_STATUS)
                ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                ->where('customer_id', $customer_id)
                ->orderBy('ordered_at', 'desc')
                ->get();
        } else if ($type == 'stopped') {
            $orders = Order::where('is_deleted', 0)
                ->where('status', Order::ORDER_STOPPED_STATUS)
                ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                ->where('customer_id', $customer_id)
                ->orderBy('ordered_at', 'desc')
                ->get();
        } else if ($type == 'on_delivery') {
            $orders = Order::where('is_deleted', 0)
                ->where(function ($query) {
                    $query->where('status', Order::ORDER_PASSED_STATUS);
                    $query->orWhere('status', Order::ORDER_ON_DELIVERY_STATUS);
                })
                ->where('customer_id', $customer_id)
                ->where('payment_type', PaymentType::PAYMENT_TYPE_WECHAT)
                ->orderBy('ordered_at', 'desc')
                ->get();
        }
        if($type)
        {
            return view('weixin.dingdanliebiao', [
                'set_type'=>true,
                'orders' => $orders,
            ]);
        } else {
            return view('weixin.dingdanliebiao', [
                'orders' => $orders,
            ]);
        }

    }

    public function dingdanxiangqing(Request $request)
    {
        $order_id = $request->input('order');
        $order = Order::find($order_id);
        $comment = $order->comment;

        if ($order) {
            $delivery_plans = $order->grouped_delivery_plans;
        }

        return view('weixin.dingdanxiangqing', [
            'order' => $order,
            'plans' => $delivery_plans,
            'comment' => $comment,
        ]);
    }


    public function toushu(Request $request)
    {
        $phone1 = "12235236243";
        $phone2 = "32123412312";
        return view('weixin.toushu', [
            "phone1" => $phone1,
            "phone2" => $phone2,
        ]);
    }

    /* 商品列表 */
    public function shangpinliebiao(Request $request)
    {
        //TODO:
        $factory_id = session('factory_id');
        $wechat_user_id = session('wechat_user_id');

        $factory = Factory::find($factory_id);

        if (!$factory) {
            abort(403);
        }

        $products = $factory->active_products;

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

        if($request->has('search_product'))
        {
            $search_products = [];
            $search_pname = $request->input('search_product');

            foreach($products as $product)
            {
                if(contains($product->name, $search_pname))
                {
                    $search_products[] = $product;
                }
            }

            return view('weixin.shangpinliebiao', [
                'categories' => $categories,
                'products' => $search_products,
                'category' => $category_id,
                'cartn' => $cartn,
            ]);

        } else {

            return view('weixin.shangpinliebiao', [
                'categories' => $categories,
                'products' => $products,
                'category' => $category_id,
                'cartn' => $cartn,
            ]);

        }
    }

    public function wodepingjia($order_id = null)
    {
        if ($order_id == null) {
            $order_id = session('pingjia_order');
            if ($order_id != null) {
                $pingjia = Review::where('order_id', $order_id)->get()->first();
                if ($pingjia == '') {
                    $marks = 0;
                    $content = '';
                } else {
                    $marks = $pingjia->mark;
                    $content = $pingjia->content;
                }
                return view('weixin.wodepingjia', [
                    'marks' => $marks,
                    'content' => $content,
                ]);
            } else {
                return view('weixin.wodepingjia', [
                    'marks' => 0,
                    'content' => '',
                ]);
            }
        } else {
            $pingjia = Review::where('order_id', $order_id)->get()->first();
            if ($pingjia == '') {
                $marks = 0;
                $content = '';
            } else {
                $marks = $pingjia->mark;
                $content = $pingjia->content;
            }
            return view('weixin.wodepingjia', [
                'marks' => $marks,
                'content' => $content,
            ]);
        }
    }

    public function dingdanpingjia(Request $request)
    {
        $order_id = $request->input('order');

        $order = Order::find($order_id);
        $review = Review::where('order_id', $order_id)->get()->first();
        if ($review != '') {
            return redirect()->route('wodepingjia', ['pingjia_order' => $order_id]);
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
        $cartn = WechatCart::where('wxuser_id', $wechat_user_id)->get()->count();

        return view('weixin.zhifuchenggong', [
            'order_id' => $order_id,
            'cartn' => $cartn,
        ]);
    }

    public function zhifushibai(Request $request)
    {
        return view('weixin.zhifushibai', [

        ]);
    }

    public function xinxizhongxin(Request $request)
    {
        return view('weixin.xinxizhongxin', [

        ]);
    }

    public function dizhiliebiao(Request $request)
    {
        $wxuser_id = session('wechat_user_id');
        $addrs = WechatAddress::where('wxuser_id', $wxuser_id)->get();

        return view('weixin.dizhiliebiao', [
            'address_list' => $addrs,
        ]);
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

        return redirect()->route('dizhiliebiao');
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

        return redirect()->route('dizhiliebiao');
    }

    public function selectAddress(Request $request)
    {
        $wxuser_id = session('wechat_user_id');
        $address_id = $request->input('address');
        $group_id = session('group_id');

        WechatAddress::where('wxuser_id', $wxuser_id)->update(['primary' => 0]);

        $address = WechatAddress::find($address_id);
        if ($address) {
            $address->primary = true;
            $address->save();
        }

        return redirect()->route('querendingdan');
    }


    //add one product in cart
    public function tianjiadingdan(Request $request)
    {

        $factory_id = session('factory_id');
        $factory = Factory::find($factory_id);

        $product_id = $request->input("product");
        $product = Product::find($product_id);

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
        ]);
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

        $product_price_template = ProductPrice::priceTemplateFromAddress($product_id, $address);

        if(!$product_price_template)
        {
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

        $total_amount = $total_count * $product_price;

        $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
        $gap_day = intval($factory->gap_day);
        $start_at = $today_date->modify("+" . $gap_day . " days");
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
            $wcop->custom_date = $custom_date;

        $wcop->total_amount = $total_amount;
        $wcop->start_at = $start_at;
        $wcop->group_id = $group_id;
        $wcop->save();

        //save group id for this direct order
        session(['group_id'=>$group_id]);

        $verified= session('verified');

        if($verified == "no")
        {
            //this user needs to be phone number verified
            return response()->json(['status'=>'fail', 'redirect_path'=>'phone_verify']);
        }

        return response()->json(['status'=>'success']);
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

            $product_price_template = ProductPrice::priceTemplateFromAddress($product_id, $address);

            if(!$product_price_template){
                $product_price_template = ProductPrice::where('product_id', $product_id)->get()->first();
            }

            if ($order_type == OrderType::ORDER_TYPE_MONTH) {
                $product_price = $product_price_template->month_price;
            } else if ($order_type == OrderType::ORDER_TYPE_SEASON) {
                $product_price = $product_price_template->season_price;
            } else {
                $product_price = $product_price_template->half_year_price;
            }

            $total_amount = $total_count * $product_price;

            $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
            $gap_day = intval($factory->gap_day);
            $start_at = $today_date->modify("+" . $gap_day . " days");
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
                $wcop->custom_date = $custom_date;

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
        //todo
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

        return response()->json(['status'=>'success']);
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
        session(['group_id'=>$group_id]);

        //here check verified
        $verified = session('verified');
        if($verified == "no")
        {
            return response()->json(['status'=>'fail', 'redirect_path'=>'phone_verify']);
        }

        return response()->json(['status'=>'success']);

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

        $customer_id = $wxuser->customer_id;
        if($customer_id)
        {
            $customer = Customer::find($customer_id);
            if($customer)
            {
                $address = $customer->address;
            }
        } else {
            $address = session('address');
        }

        $group_id = session('group_id');

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
        if($wop->delivery_type  == DeliveryType::DELIVERY_TYPE_EVERY_DAY || $wop->delivery_type  == DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY )
        {
            $count_per_day = $wop->count_per_day;
            $order_day_num = round($total_count/$count_per_day);
        } else {
            $ord_ctrl = new OrderCtrl;
            $order_day_num = $ord_ctrl->get_number_of_days_for_wechat_product($wop->id);
        }

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
            'order_day_num'=>$order_day_num,
        ]);
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
            $pp = ProductPrice::priceTemplateFromAddress($product_id, $address);

            if ($order_type == OrderType::ORDER_TYPE_MONTH) {
                $product_price = $pp->month_price;
            } else if ($order_type == OrderType::ORDER_TYPE_SEASON) {
                $product_price = $pp->season_price;
            } else {
                //half year price
                $product_price = $pp->half_year_price;
            }

            $total_amount = $total_count * $product_price;

            $today_date = new DateTime("now", new DateTimeZone('Asia/Shanghai'));
            $gap_day = intval($factory->gap_day);
            $start_at = $today_date->modify("+" . $gap_day . " days");
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
                $wcop->custom_date = $custom_date;

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

        $orderctrl = new OrderCtrl();

        $customer_id = $wechat_user->customer_id;
        if (!$customer_id) {
            //get wechat primary addresss
            $primary_address_obj = WechatAddress::where('wxuser_id', $wxuser_id)->where('primary', 1)->get()->first();

            $primary_address = $primary_address_obj->address;

            //get station and milkman from factory and primary_address
            $station_milkman = $orderctrl->get_station_milkman_with_address_from_factory($factory_id, $primary_address);

            if ($station_milkman == OrderCtrl::NOT_EXIST_DELIVERY_AREA) {
                return response()->json(['status' => 'fail', 'message' => '客户并不住在可以递送区域.']);
            } else if ($station_milkman == OrderCtrl::NOT_EXIST_STATION) {
                return response()->json(['status' => 'fail', 'message' => '没有奶站.']);
            } else if ($station_milkman == OrderCtrl::NOT_EXIST_MILKMAN) {
                return response()->json(['status' => 'fail', 'message' => '没有递送人.']);
            }

            foreach($station_milkman as $delivery_station_id => $milkman_id)
            {
                //make new customer and change product price
                $customer = new Customer;
                $customer->phone = $primary_address_obj->phone;
                $customer->name = $primary_address_obj->name;
                $customer->address = $primary_address_obj->address .' '. $primary_address_obj->sub_address;
                $customer->station_id = $delivery_station_id;
                $customer->factory_id = $factory_id;
                $customer->milkman_id = $milkman_id;
                break;
            }
            $customer->save();

            $customer_id = $customer->id;
            $wechat_user->customer_id = $customer_id;
            $wechat_user->save();


        } else {
            $customer = Customer::find($customer_id);
        }

        $customer_id =$customer->id;

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
        $start_at = $wechat_user->order_start_at;

        //make order
        $order = new Order;
        $order->factory_id = $factory_id;
        $order->customer_id = $customer_id;
        $order->phone = $customer->phone;
        $order->address = $customer->address;
        $order->order_property_id = OrderProperty::ORDER_PROPERTY_NEW_ORDER;
        $order->station_id = $station_id;
        $order->order_checker_id = $order_checker->id;
        $order->milk_box_install = ($customer->has_milkbox) ? 1 : 0;
        $order->total_amount = $total_amount;
        $order->remaining_amount = $total_amount;
        $order->order_by_milk_card = 0;
        $order->trans_check = 0;
        $order->payment_type = PaymentType::PAYMENT_TYPE_WECHAT;
        $order->status = Order::ORDER_WAITING_STATUS;
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

        //delete cart and order item
        $this->remove_cart_by_group($group_id);

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
        $start_at = $wechat_user->order_start_at;

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
        $order->status = Order::ORDER_WAITING_STATUS;
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
        $this->remove_wechat_order_products_from_wxuser($wxuser_id, $wopids);

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
            $op->custom_order_dates = $wop->custom_date;
            $op->start_at = $wop->start_at;
            $op->save();

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
        $gap_day = intval($factory->gap_day);

        $start_at_new = $today_date->modify("+" . $gap_day . " days");
        $start_at_new = $start_at_new->format('Y-m-d');

        $wopids = "";

        $group_id = $this->get_new_group_id();

        //make new wechat order products based on order products
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
            $wcop->custom_date = $op->custom_order_dates;
            $wcop->total_amount = $op->total_amount;
            $wcop->start_at = $start_at;
            $wcop->group_id = $group_id;
            $wcop->save();

            if (!$wopids)
                $wopids .= "," . $wcop->id;
            else
                $wopids .= $wcop->id;

            array_push($wechat_order_products, $wcop);
        }

        $wechat_user = WechatUser::find($wxuser_id);
        $customer_id = $wechat_user->customer_id;
        $customer = Customer::find($customer_id);

        $primary_addr_obj = WechatAddress::where('wxuser_id', $wxuser_id)->where('primary',1)->get()->first();

        return view('weixin.querendingdan', [
            'primary_addr_obj'=>$primary_addr_obj,
            'customer' => $customer,
            'wechat_order_products' => $wechat_order_products,
            'group_id' => $group_id,
            'wxuser_id' => $wxuser_id,
        ]);
    }

    public function get_count_by_order_type($order_type){
        if($order_type == OrderType::ORDER_TYPE_MONTH)
            return 30;
        else if($order_type == OrderType::ORDER_TYPE_SEASON)
            return 90;
        else if($order_type == OrderType::ORDER_TYPE_HALF_YEAR)
            return 180;
    }

    //check current group total count condition
    public function check_total_count($group_id)
    {
        //get total bottle count of this group
        $total = $max = 0;
        $wechat_order_products = WechatOrderProduct::where('group_id', $group_id)->where('group_id', '!=', null)->get()->all();
        foreach ($wechat_order_products as $wop)
        {
            $total += $wop->total_count;
            $count_by_order_type = $this->get_count_by_order_type($wop->order_type);
            if($max<$count_by_order_type)
            {
                $max = $count_by_order_type;
            }
        }

        if($total < $max)
        {
            return false;
        } else {
            return true;
        }

    }

    //Confirm Wechat order products to be included in Order
    public function querendingdan(Request $request)
    {
        $wechat_user_id = session('wechat_user_id');

        $primary_addr_obj = WechatAddress::where('wxuser_id', $wechat_user_id)->where('primary',1)->get()->first();

        $group_id = session('group_id');

        $wechat_order_products = WechatOrderProduct::where('group_id', $group_id)->where('group_id', '!=', null)->get()->all();

        //set new product price on primary address for the wechat order product
        if($primary_addr_obj)
        {
            $primary_address = $primary_addr_obj->address;

            if($wechat_order_products)
            {
                foreach($wechat_order_products as $wop)
                {
                    $product_id = $wop->product_id;

                    $new_product_price_tp = ProductPrice::priceTemplateFromAddress($product_id, $primary_address);

                    if($new_product_price_tp)
                    {
                        if($wop->order_type == OrderType::ORDER_TYPE_MONTH) {
                            $wop->product_price = $new_product_price_tp->month_price;
                        } else if($wop->order_type == OrderType::ORDER_TYPE_SEASON)
                        {
                            $wop->product_price = $new_product_price_tp->season_price;
                        } else if ($wop->order_type == OrderType::ORDER_TYPE_HALF_YEAR){
                            $wop->product_price = $new_product_price_tp->half_year_price;
                        } else {
                            $wop->product_price = $new_product_price_tp->settle_price;
                        }

                        $wop->save();

                        $wop->total_amount = $wop->product_price * $wop->total_count;
                        $wop->save();
                    } else {

                        $wop->product_price = null;
                        $wop->total_amount = null;

                        //there is not product price for this primary address
                        return view('weixin.querendingdan', [
                            'primary_addr_obj'=>$primary_addr_obj,
                            'wechat_order_products' => $wechat_order_products,
                            'group_id' => $group_id,
                            'wxuser_id' => $wechat_user_id,
                            'message' => '未定义地址的产品价格',
                        ]);
                    }

                }
            }


            //set primary address as session address
            session(['address'=>$primary_address]);
        }else {
            //if this user has customer account and wechat address has not info about this user, make the wechat address automatically
            $wxuser = WechatUser::find($wechat_user_id);
            if($wxuser)
            {
                $customer_id = $wxuser->customer_id;
                if($customer_id)
                {
                    $customer = Customer::find($customer_id);
                    if($customer)
                    {
                        //check wechat address
                        $wx_address_list =WechatAddress::where('wxuser_id', $wechat_user_id)->get();
                        if(count($wx_address_list) == 0)
                        {
                            $new_wx_addr = new WechatAddress;
                            $new_wx_addr->wxuser_id = $wechat_user_id;
                            $new_wx_addr->name = $customer->name;
                            $new_wx_addr->phone = $customer->phone;
                            $new_wx_addr->address = $customer->main_addr;
                            $new_wx_addr->sub_address = $customer->sub_addr;
                            $new_wx_addr->primary = 1;
                            $new_wx_addr->save();
                            $primary_addr_obj = $new_wx_addr;
                        }
                    }
                }
            }

        }

        if($this->check_total_count($group_id))
        {
            $passed = true;
            $message ="";
        } else {
            $passed = false;
            $message="订单数量总合得符合订单类型条件";
        }

        return view('weixin.querendingdan', [
            'primary_addr_obj'=>$primary_addr_obj,
            'wechat_order_products' => $wechat_order_products,
            'group_id' => $group_id,
            'wxuser_id' => $wechat_user_id,
            'passed'=>$passed,
            'message'=>$message,
        ]);

    }

    public function addPingjia(Request $request)
    {
        $order_id = $request->input('order_id');
        $marks = $request->input('marks');
        $content = $request->input('contents');
        $current_datetime = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $current_datetime_str = $current_datetime->format('Y-m-d H:i:s');

        $order = Order::find($order_id);
        $customer_id = $order->customer_id;
        foreach($order->order_products as $op)
        {
            $review = new Review;
            $review->mark = $marks;
            $review->content = $content;
            $review->order_id = $order_id;
            $review->customer_id = $customer_id;
            $review->product_id = $op->product_id;
            $review->created_at = $current_datetime_str;
            $review->status = Review::REVIEW_STATUS_WAITTING;
            $review->save();
        }

        return response()->json(['status'=>'success', 'order_id'=>$order_id]);

    }

    //show check telephone number page
    public function dengji(Request $request)
    {
        return  view('weixin.dengji', [
        ]);
    }
    //send verify code to phone
    public function send_verify_code_to_phone(Request $request)
    {
        $phone = $request->input('phone_number');
        $code = $this->generate_verify_code();
        //todo: save verify code to this wxuser

        $wxuser_id = session('wechat_user_id');
        $wxuser = WechatUser::find($wxuser_id);
        $wxuser->phone_verify_code =$code;
        $wxuser->save();

        return response()->json(['status'=>'success']);

    }

    public function generate_verify_code(){
        $code = rand(10000, 99999); // random 4 digit code
        $code = 1111;
        return $code;
    }

    public function check_verify_code(Request $request)
    {
        $phone_number = $request->input('phone_number');
        $code = $request->input('code');

        $wxuser_id = session('wechat_user_id');
        $wxuser = WechatUser::find($wxuser_id);
        if($wxuser->phone_verify_code == $code)
        {
            session(['verified'=>'yes']);
            return response()->json(['status'=>'success']);
        }
        else
            return response()->json(['status'=>'fail']);
    }



}