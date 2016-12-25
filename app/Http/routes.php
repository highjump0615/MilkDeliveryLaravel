<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::auth();

Route::get('/home', 'HomeController@index');


/*Common Features*/
//Export Table Data
Route::post('api/export', 'ExportCtrl@export');

Route::post('api/printlog', 'ExportCtrl@printLog');

//Get all area
Route::get('api/province_to_city', 'AddressCtrl@all_province_to_city');
Route::get('api/city_to_district', 'AddressCtrl@all_city_to_district');

//Get only active child address under the parent
Route::get('api/active_province_to_city', 'AddressCtrl@active_province_to_city');
Route::get('api/active_city_to_district', 'AddressCtrl@active_city_to_district');
Route::get('api/active_district_to_street', 'AddressCtrl@active_district_to_street');
Route::get('api/active_street_to_xiaoqu', 'AddressCtrl@active_street_to_xiaoqu');


//Get all station from factory
Route::post('api/factory_to_station', 'FinanceCtrl@factory_to_station');

//get Product Price
Route::get('api/order/get_order_product_price', 'OrderCtrl@get_order_product_price');

// 单日修改
Route::post('api/gongchang/dingdan/change_delivery_plan_for_one_day_in_xiangqing_and_xiugai', 'OrderCtrl@change_delivery_plan_for_one_day_in_xiangqing_and_xiugai');

// 暂停订单
Route::post('api/gongchang/dingdan/stop_order_in_gongchang', 'OrderCtrl@stop_order_for_some_period');

// 顺延订单
Route::post('api/gongchang/dingdan/postpone_order', 'OrderCtrl@postpone_order');

// 退订
Route::post('api/gongchang/dingdan/cancel_order', 'OrderCtrl@cancel_order');

// 开启订单
Route::post('api/gongchang/dingdan/restart_order', 'OrderCtrl@restart_dingdan');


//Test
Route::get('/test', 'WeChatCtrl@test');
Route::get('/apply', 'WeChatCtrl@apply');

/*Send hourly notification*/
Route::get('api/send_alert_to_deliverystation','NotificationsAdmin@sendHourlyRequstforPlan');

/*G-O-N-G-C-H-A-N-G*/
//Route::group(['middleware' => ['web']], function () {
Route::get('/gongchang', function () {
    return view('gongchang.auth.login');
});

Route::group(['middleware' => ['gongchang']], function () {

    //get all product names
    Route::get('api/get_exist_product_names', 'ProductCtrl@get_all_product_names');

    //Gongchang/Jichuxinxi/Shangpin Canshshezi
    Route::get('/gongchang/jichuxinxi/shangpin/shangpincanshushezhi', 'ProductSettingsCtrl@show_product_settings_page');
    Route::post('api/gongchang/jichuxinxi/shangpin/shangpincanshushezhi/set_use_delivery_type', 'ProductSettingsCtrl@set_use_delivery_type');
    Route::post('api/gongchang/jichuxinxi/shangpin/shangpincanshushezhi/delivery_time', 'ProductSettingsCtrl@set_delivery_time');

    Route::post('api/gongchang/jichuxinxi/shangpin/shangpincanshushezhi/order_type', 'ProductSettingsCtrl@add_order_type');
    Route::delete('api/gongchang/jichuxinxi/shangpin/shangpincanshushezhi/order_type/{id}', 'ProductSettingsCtrl@delete_order_type');

    Route::post('api/gongchang/jichuxinxi/shangpin/shangpincanshushezhi/bottle_type', 'ProductSettingsCtrl@add_bottle_type');
    Route::delete('api/gongchang/jichuxinxi/shangpin/shangpincanshushezhi/bottle_type/{id}', 'ProductSettingsCtrl@delete_bottle_type');

    Route::post('api/gongchang/jichuxinxi/shangpin/shangpincanshushezhi/box_type', 'ProductSettingsCtrl@add_box_type');
    Route::delete('api/gongchang/jichuxinxi/shangpin/shangpincanshushezhi/box_type/{id}', 'ProductSettingsCtrl@delete_box_type');

    Route::post('api/gongchang/jichuxinxi/shangpin/shangpincanshushezhi/set_gap_day', 'ProductSettingsCtrl@set_gap_day');

    Route::post('api/gongchang/dingdan/dingdanluru/insert_customer', 'OrderCtrl@insert_customer_for_order_in_gongchang');

    /*
     * DELVIERY STATION MANAGEMENT
     */
    //SHow Delivery Stations
    Route::get('/gongchang/xitong/naizhanzhanghao','StationManageCtrl@show_delivery_stations')->name('show_station');
    //Delete station
    Route::post('api/gongchang/xitong/naizhanzhanghao/delete_station','StationManageCtrl@delete_station');
    //Activate or Deactivate Station
    Route::post('api/gongchang/xitong/naizhanzhanghao/change_status_of_station','StationManageCtrl@change_status_of_station');

    //Show Station insert page
    Route::get('/gongchang/xitong/naizhanzhanghao/tianjianaizhanzhanghu', 'StationManageCtrl@show_insert_station_page');
    //get station admin name
    Route::post('api/gongchang/xitong/naizhanzhanghao/tianjianaizhanzhanghu/get_station_admin_name', 'StationManageCtrl@get_station_admin_name');
    Route::post('api/gongchang/xitong/tianjianaizhanzhanghu/insert_station', 'StationManageCtrl@insert_station');
    Route::post('api/gongchang/xitong/tianjianaizhanzhanghu/insert_station_image', 'StationManageCtrl@insert_station_image');

    Route::post('api/gongchang/xitong/tianjianaizhanzhanghu/update_station', 'StationManageCtrl@update_station');
    Route::get('/gongchang/xitong/naizhanzhanghao/tianjianaizhanzhanghu/zhanghuxiangqing-chakan/{station_id}', 'StationManageCtrl@show_detail_of_station_page');

    //Change naizhan
    Route::get('/gongchang/xitong/naizhanzhanghao/naizhanxiugai/{station_id}', 'StationManageCtrl@show_change_station_page');

    //GongChang/Jichuxinxi/Dizhiku
    Route::get('gongchang/jichuxinxi/dizhiku/', 'AddressCtrl@show')->name('address_show');
    Route::post('api/gongchang/jichuxinxi/dizhiku/store', 'AddressCtrl@store');
    Route::post('api/gongchang/jichuxinxi/dizhiku/update', 'AddressCtrl@update');
    Route::post('api/gongchang/jichuxinxi/dizhiku/delete_address', 'AddressCtrl@delete_address');
    Route::post('api/gongchang/jichuxinxi/dizhiku/setflag', 'AddressCtrl@setflag');

    //GongChang/Jichuxinxi/Shangpin
    Route::get('/gongchang/jichuxinxi/shangpin', 'ProductCtrl@show_product_list')->name('show_product_list');
    Route::post('api/gongchang/jichuxinxi/shangpin/add_category', 'ProductCtrl@add_category');
    Route::get('api/gongchang/jichuxinxi/shangpin/check_same_category', 'ProductCtrl@check_same_category');
    Route::post('api/gongchang/jichuxinxi/shangpin/update_category', 'ProductCtrl@update_category');
    Route::post('api/gongchang/jichuxinxi/shangpin/delete_product', 'ProductCtrl@delete_product');
    Route::post('api/gongchang/jichuxinxi/shangpin/disable_product', 'ProductCtrl@disable_product');

    //gongchang/Jichuxinxi/Shanpin/shanpinxiangqing
    Route::get('/gongchang/jichuxinxi/shangpin/shangpinxiangqing/{product_id}', array('as' => 'detail_product', 'uses' => 'ProductCtrl@show_detail_product'));
    Route::post('api/gongchang/jichuxinxi/shangpin/shangpinxiangqing/update_product', 'ProductCtrl@update_product');
    Route::post('api/gongchang/jichuxinxi/shangpin/shangpinxiangqing/update_product_price', 'ProductCtrl@update_product_price');
    Route::post('api/gongchang/jichuxinxi/shangpin/shangpinxiangqing/update_product_price_template_one', 'ProductCtrl@update_product_price_template_one');


    //Gongchang/Jichuxinxi/naipinluru
    Route::get('/gongchang/jichuxinxi/shangpin/naipinluru', 'ProductCtrl@show_insert_product')->name('show_insert_product');
    Route::post('api/gongchang/naipinluru/insert_product', 'ProductCtrl@insert_product');
    Route::post('api/gongchang/naipinluru/insert_product_price_template', 'ProductCtrl@insert_product_price_template');

    Route::get('/gongchang/xitong/yonghu', 'UserCtrl@viewPage');

    Route::get('/api/gongchang/xitong/yonghu/{user_id?}', 'UserCtrl@getPage');
    /*save and update user information*/
    Route::post('api/gongchang/xitong/yonghu', 'UserCtrl@addAccount');
    Route::put('api/gongchang/xitong/yonghu/{user_id}', 'UserCtrl@updateAccount');

    Route::post('api/gongchang/xitong/yonghu/changeStatus','UserCtrl@changeStatus');
    /*Delete user information*/
    Route::delete('api/gongchang/xitong/yonghu/{user_id}', 'UserCtrl@removeAccount');
    /*View Xitong/Juese Page*/
    Route::get('/gongchang/xitong/juese/{role_id?}', 'UserRoleCtrl@viewPage')->name('gongchang_juese');
    /*Add Role_name*/
    Route::post('api/gongchang/xitong/juese', 'UserRoleCtrl@addRole');
    /*Delete Role_name*/
    Route::delete('api/gongchang/xitong/juese/{role_id}', 'UserRoleCtrl@deleteRole');

    Route::get('api/gongchang/xitong/juese/{role_id?}', 'UserRoleCtrl@index');
    /*Save inupt value*/
    Route::post('api/gongchang/xitong/juese/store', 'UserRoleCtrl@store');

    //Gongchang/Dingdan/Dingdanluru
    Route::get('/gongchang/dingdan/dingdanluru', 'OrderCtrl@show_insert_order_page_in_gongchang');

    Route::post('api/gongchang/dingdan/dingdanluru/insert_order', 'OrderCtrl@insert_order_in_gongchang');
    Route::post('api/gongchang/dingdan/dingdanluru/verify_card', 'MilkCardCtrl@verify_card');


    //Gongchang/Dingdan/show detail dingdan
    Route::get('/gongchang/dingdan/dingdanluru/xiangqing/{order_id}', 'OrderCtrl@show_detail_order_in_gongchang');

    //Gongchang/Dingdan/quanbudingdan-liebiao
    Route::get('/gongchang/dingdan/quanbudingdan-liebiao', 'OrderCtrl@show_all_dingdan_in_gongchang');
    Route::get('/gongchang/dingdan/quanbudingdan-liebiao/week_show', 'OrderCtrl@show_order_of_this_week_in_gongchang');
    Route::get('/gongchang/dingdan/quanbudingdan-liebiao/month_show', 'OrderCtrl@show_order_of_this_month_in_gongchang');

    //Show Daishenhe Orders
    Route::get('/gongchang/dingdan/daishenhedingdan', 'OrderCtrl@show_check_waiting_dingdan_in_gongchang');
    Route::post('api/gongchang/daishenhedingdan/change_sub_addr', 'OrderCtrl@change_sub_address_in_gongchang');
    Route::get('api/gongchang/daishenhedingdan/pass_order', 'OrderCtrl@pass_waiting_dingdan_in_gongchang');
    Route::get('api/gongchang/daishenhedingdan/no_pass_order', 'OrderCtrl@not_pass_waiting_dingdan_in_gongchang');
    Route::get('/gongchang/dingdan/daishenhedingdan/daishenhe-dingdanxiangqing/{order_id}', 'OrderCtrl@show_detail_waiting_dingdan_in_gongchang');

    //Show Order Xiugai Page
    Route::get('/gongchang/dingdan/dingdanxiugai/{order_id}', 'OrderCtrl@show_order_revise_in_gongchang');
    Route::post('api/gongchang/dingdan/dingdanxiugai/stop_order_for_some_period', 'OrderCtrl@stop_order_for_some_period');

    Route::post('api/gongchang/dingdan/dingdanxiugai/change_order_info', 'OrderCtrl@change_order_info');


    //Show Passed Orders
    Route::get('/gongchang/dingdan/weiqinaidingdan', 'OrderCtrl@show_passed_dingdan_in_gongchang');

    //Show On Delivery Orders
    Route::get('/gongchang/dingdan/zaipeisongdingdan', 'OrderCtrl@show_on_delivery_dingdan_in_gongchang');

    //Show Xudan Order page
    Route::get('/gongchang/dingdan/xudanliebiao/xudan/{order_id}', 'OrderCtrl@show_xudan_dingdan_in_gongchang');

    //Show xudan orders liebiao
    Route::get('/gongchang/dingdan/xudanliebiao', 'OrderCtrl@show_xudan_dingdan_liebiao_in_gongchang');

    //Show stopped orders
    Route::get('/gongchang/dingdan/zantingdingdan', 'OrderCtrl@show_stopped_dingdan_in_gongchang');


    //Show not passed orders
    Route::get('/gongchang/dingdan/weitongguodingdan', 'OrderCtrl@show_not_passed_dingdan_in_gongchang');

    /*Show Naizhanpeisonggongchang/shengchan/naizhanpeisong page*/
    Route::get('/gongchang/shengchan/naizhanpeisong', 'DSProductionPlanCtrl@showNaizhanpeisongPage');
    /*update actual_count for real produce - naizhan*/
    Route::put('api/gongchang/shengchan/naizhanpeisong', 'DSProductionPlanCtrl@updateNaizhanPlanTable');
    /*Show Naizhanshouhuoqueren page*/
    Route::get('/gongchang/shengchan/naizhanpeisong/naizhanshouhuoqueren', 'DSProductionPlanCtrl@showNaizhanshouhuoquerenPage');
    /*show dayinchukuchan Page*/
    Route::get('/gongchang/shengchan/naizhanpeisong/dayinchukuchan', 'DSProductionPlanCtrl@showDayinchukuchan');
    /*Show milkstation Plan table*/
    Route::get('/gongchang/shengchan/naizhanjihuashenhe', 'DSProductionPlanCtrl@showPlanTableinFactory');
    /*Determine product counts*/
    Route::post('api/gongchang/shengchan/naizhanjihuashenhe/saveProduct', 'DSProductionPlanCtrl@SaveforProduce');
    /*Cancel Produce*/
    Route::post('api/gongchang/shengchan/naizhanjihuashenhe/cancelProduct', 'DSProductionPlanCtrl@StopforProduce');
    /*Production determine*/
    Route::post('api/gongchang/shengchan/naizhanjihuashenhe/determine_station_plan', 'DSProductionPlanCtrl@determineStationPlan');
    /*Production Plan cancel*/
    Route::post('api/gongchang/shengchan/naizhanjihuashenhe/cancel_station_plan', 'DSProductionPlanCtrl@cancelStationPlan');
    /*Gongchang Finance*/

    //Finance First page: stations account info page
    Route::get('/gongchang/caiwu/taizhang', 'FinanceCtrl@show_finance_page_in_gongchang');

    //Show selected station's order money
    Route::get('/gongchang/caiwu/taizhang/naizhandingdanjinetongji/{station_id}', 'FinanceCtrl@show_station_order_money_in_gongchang');
    //Insert Money Order received to station info
    Route::post('api/gongchang/caiwu/taizhang/insert_money_order_received', 'FinanceCtrl@insert_money_order_received_to_station');

    //show station account Calc balance
    Route::get('/gongchang/caiwu/naizhanzhanghuyue/{station_id}', 'FinanceCtrl@show_station_calc_account_balance_in_gongchang');
    Route::post('api/gongchang/caiwu/naizhanzhanghuyue/add_calc_history', 'FinanceCtrl@add_calc_history');

    //show self account balance
    Route::get('/gongchang/caiwu/ziyingzhanghu/{station_id}', 'FinanceCtrl@show_self_account_in_gongchang');
    Route::get('api/gongchang/caiwu/ziyingzhanghu/add_self_business_history', 'FinanceCtrl@add_self_business_history');

    //show delivery status between other stations
    Route::get('/gongchang/caiwu/taizhang/qitanaizhanzhuanzhang', 'FinanceCtrl@show_transaction_between_other_station_in_gongchang');

    //show created new transaction list for money order of other stations
    Route::post('/gongchang/caiwu/taizhang/qitanaizhanzhuanzhang/create_transaction', 'FinanceCtrl@show_transaction_creation_page_for_other_money_in_gongchang');
    Route::get('/gongchang/caiwu/taizhang/qitanaizhanzhuanzhang/zhuanzhangzhangchan', 'FinanceCtrl@show_transaction_list_not_checked_for_other_money_in_gongchang')->name('show_other_transaction_list');
    Route::post('api/gongchang/caiwu/taizhang/qitanaizhanzhuanzhang/zhuanzhangzhangchan/get_trans_data', 'FinanceCtrl@get_trans_data_for_other_station');
    Route::post('api/gongchang/caiwu/taizhang/qitanaizhanzhuanzhang/zhuanzhangzhangchan/complete_trans', 'FinanceCtrl@complete_trans_for_other_station');


    //show milk card delivery status
    Route::get('/gongchang/caiwu/taizhang/naikakuanzhuanzhang', 'FinanceCtrl@show_orders_for_card_transaction_in_gongchang');
    //show milk card delivery list
    Route::get('/gongchang/caiwu/taizhang/naikazhuanzhangzhangchan', 'FinanceCtrl@show_transaction_list_not_checked_for_card_in_gongchang');
    Route::post('/gongchang/caiwu/taizhang/naikakuanzhuanzhang/create_transaction', 'FinanceCtrl@show_transaction_creation_page_for_card_in_gongchang');

    Route::post('api/gongchang/caiwu/taizhang/naikazhuanzhangzhangchan/get_trans_data', 'FinanceCtrl@get_trans_data_for_card');
    Route::post('api/gongchang/caiwu/taizhang/naikazhuanzhangzhangchan/complete_trans', 'FinanceCtrl@complete_trans_for_card');

    //show delivery record to other stations
    Route::get('/gongchang/caiwu/taizhang/qitanaizhanzhuanzhang/zhuanzhangjilu', 'FinanceCtrl@show_money_transaction_record_to_others_in_gongchang');
    //show delivery bill detail
    Route::get('/gongchang/caiwu/taizhang/qitanaizhanzhuanzhang/zhangdanmingxi/{tid}', 'FinanceCtrl@show_transaction_detail_to_others_in_gongchang');

    //show milk card delivery record
    Route::get('/gongchang/caiwu/taizhang/naikazhuanzhangjilu', 'FinanceCtrl@show_card_transaction_record_in_gongchang');
    //show milk card delivery detail
    Route::get('/gongchang/caiwu/taizhang/naikazhangdanmingxi/{trs_id}', 'FinanceCtrl@show_card_transaction_detail_in_gongchang');


    /*show Customer admin page*/
    Route::get('/gongchang/kehu/kehu', 'CustomerCtrl@showGongchangUserPage');
    /*show naika gunali page*/
    Route::get('/gongchang/naika/naika', 'MilkCardCtrl@showNaikaPage');
    Route::post('/gongchang/naika/naika/import', 'MilkCardCtrl@importCard');
    /*get naika info*/
    Route::get('api/gongchang/get_naika_info', 'MilkCardCtrl@getNaikaInfo');
    /*save naika info*/
    Route::post('api/gongchang/naika/naika/register', 'MilkCardCtrl@registerNaikaInfo');
    /*show pingkuang guanli Page*/
    Route::get('/gongchang/pingkuang/pingkuang', 'BottleAdminCtrl@gongchangPingkuangShow');
    /*gongchang pingkuang save Page*/
    Route::post('api/gongchang/pingkuang/pingkuang/save', 'BottleAdminCtrl@SaveGongchangPingkuang');
    /*show zhongxin*/
    Route::get('/gongchang/xinxi/zhongxin', 'NotificationsAdmin@showGongchangZhongxin');
    /*change to active*/
    Route::post('api/gongchang/xinxi/zhongxin/changeActiveStatus', 'NotificationsAdmin@changetoActiveGongchang');
    /*change to inactive*/
    Route::post('api/gongchang/xinxi/zhongxin/changeInActiveStatus', 'NotificationsAdmin@changetoInactiveGongchang');
    /*show zhongxinxiangxi*/
    Route::get('/gongchang/xinxi/xiangxi/{fac_notification_id}', 'NotificationsAdmin@showGongchangXiangxi');
    /*show tongji-daoqidingdantongji Page*/
    Route::get('/gongchang/tongjifenxi/daoqidingdantongji', 'FactoryStatistics@showDaoqidingdantongji');
    /*show tongji-naipinpeisongtongji Page*/
    Route::get('/gongchang/tongjifenxi/naipinpeisongtongji', 'FactoryStatistics@showNaipinpeisongtongji');
    /*show tongji-Kehuxingweitongji Page*/
    Route::get('/gongchang/tongjifenxi/kehuxingweitongji', 'FactoryStatistics@showKehuxingweitongji');
    /*show tongji-kehudingdanxiugui*/
    Route::get('gongchang/tongjifenxi/kehudingdanxiugui', 'FactoryStatistics@showKehudingdanxiugui');
    /*show tongji-dingdanshengyuliangtongji*/
    Route::get('/gongchang/tongjifenxi/dingdanshengyuliangtongji', 'FactoryStatistics@showDingdanshengyuliangtongji');

    /*show tongji-dingdanleixingtongji Page*/
    Route::get('/gongchang/tongjifenxi/dingdanleixingtongji', 'FactoryStatistics@showDingdanleixingtongji');
    /*工厂管理 / 基础信息管理 / 配送员管理 */

    Route::get('/gongchang/jichuxinxi/zhengdingyuan', 'CheckerCtrl@showCheckerPage')->name('show_checkers');
    Route::post('gongchang/jichuxinxi/zhengdingyuan', 'CheckerCtrl@addChecker');
    Route::get('api/gongchang/jichuxinxi/zhengdingyuan/{id}', 'CheckerCtrl@getChecker');
    Route::post('api/gongchang/jichuxinxi/zhengdingyuan/{id}', 'CheckerCtrl@updateChecker');
    Route::delete('api/gongchang/jichuxinxi/zhengdingyuan/{id}', 'CheckerCtrl@deleteChecker');


    /*Station Delivery Area Management*/
    /* 工厂管理 / 基础信息管理 / 奶站管理 */
    //Show stations
    Route::get('/gongchang/jichuxinxi/naizhan', 'StationManageCtrl@show_station_list_for_delivery_area');
    //show station's delivery area
    Route::get('/gongchang/jichuxinxi/naizhan/peisongfanwei-chakanbianji/{station_id}', 'StationManageCtrl@show_delivery_area_of_station');
    /*insert area-street*/
    Route::post('api/gongchang/jichuxinxi/naizhan/peisongfanwei-chakanbianji/add_delivery_area', 'StationManageCtrl@add_delivery_area');

    /*change delivery area*/
    Route::post('api/gongchang/jichuxinxi/naizhan/peisongfanwei-chakanbianji/change_delivery_area', 'StationManageCtrl@change_delivery_area');
    /*delete area-street*/
    Route::post('api/gongchang/jichuxinxi/naizhan/peisongfanwei-chakanbianji/delete_delivery_area', 'StationManageCtrl@delete_delivery_area');
    /*show Pingjia Page*/
    Route::get('/gongchang/pingjia/pingjialiebiao','ReviewCtrl@showPingjiaPage');
    /*delete userpingjia*/
    Route::delete('api/gongchang/pingjia/pingjialiebiao/remove/{review_id}','ReviewCtrl@deleteUserPingjia');
    /*pass userpingjia*/
    Route::post('api/gongchang/pingjia/pingjialiebiao/pass','ReviewCtrl@passUserPingjia');
    /*isolate userpingjia*/
    Route::post('api/gongchang/pingjia/pingjialiebiao/isolate','ReviewCtrl@isolateUserPingjia');
    /*modify userpingjia*/
    Route::post('api/gongchang/pingjia/pingjialiebiao/modify','ReviewCtrl@modifyUserPingjia');
    /*isolate userpingjia*/
    Route::get('api/gongchang/pingjia/pingjialiebiao/current_info/{review_id}','ReviewCtrl@getCurrentInfo');
    /*show Pingjialiebiao Page*/
    Route::get('/gongchang/pingjia/pingjiaxiangqing/{review_id}','ReviewCtrl@showPingjialiebiaoPage');
});

Route::post('/gongchang/login', 'GongchangAuth\AuthController@login');
Route::get('/gongchang/logout', 'GongchangAuth\AuthController@logout');
Route::get('/gongchang/shouye', function (Request $request) {
    $child = '';
    $parent = 'shouye';
    $current_page = 'shouye';
    $pages = App\Model\UserModel\Page::where('backend_type', '2')->where('parent_page', '0')->get();
    return view('gongchang.shouye', [
        'pages' => $pages,
        'child' => $child,
        'parent' => $parent,
        'current_page' => $current_page

    ]);
});

/*N-A-I-Z-H-A-N*/
Route::get('/naizhan', function () {
    return view('naizhan.auth.login');
});

//user login
Route::get('/naizhan/login', function () {
    return view('naizhan.auth.login');
});
Route::post('/naizhan/login', 'NaizhanAuth\AuthController@login');
Route::get('/naizhan/logout', 'NaizhanAuth\AuthController@logout');

//order mgmt
Route::group(['middleware' => ['naizhan']], function () {
    /*show user admin page*/
    Route::get('/naizhan/xitong/yonghu','UserCtrl@stationJuese');
    /*get Pages*/
    Route::get('/api/naizhan/xitong/yonghu/{user_id?}', 'UserCtrl@getNaizhanPage');
    /*save and update user information*/
    Route::post('api/naizhan/xitong/yonghu', 'UserCtrl@addNaizhanGuanliyuan');
    /*update account*/
    Route::put('api/naizhan/xitong/yonghu/{user_id}', 'UserCtrl@updateNaizhanGuanliyuan');
    /*change Status*/
    Route::post('api/naizhan/xitong/yonghu/changeStatus','UserCtrl@changeStatusNaizhanGuanliyuan');
    /*Delete user information*/
    Route::delete('api/naizhan/xitong/yonghu/{user_id}', 'UserCtrl@deleteNaizhanGuanliyuan');
    /*View Xitong/Juese Page*/
    Route::get('/naizhan/xitong/juese/{role_id?}', 'UserRoleCtrl@stationJuese')->name('naizhan_juese');
    /*Add Role_name*/
    Route::post('api/naizhan/xitong/juese', 'UserRoleCtrl@addRole');
    /*Delete Role_name*/
    Route::delete('api/naizhan/xitong/juese/{role_id}', 'UserRoleCtrl@deleteRole');

    Route::get('api/naizhan/xitong/juese/{role_id?}', 'UserRoleCtrl@index');
    /*Save inupt value*/
    Route::post('api/naizhan/xitong/juese/store', 'UserRoleCtrl@store');
    /*Show DSProductPlanView Page*/
    Route::get('/naizhan/shengchan/jihuaguanli', 'DSProductionPlanCtrl@showJihuaguanlinPage')->name('naizhan_shengchan_jihuaguanli');
    /*Show ExportPlan Page*/
    Route::get('/naizhan/shengchan/tijiaojihua', 'DSProductionPlanCtrl@showTijiaojihuaPage');
    /*input plan*/
    Route::post('api/naizhan/shengchan/tijiaojihua/store', 'DSProductionPlanCtrl@storeTijiaojihuaPlan');
    /*modify plan*/
    Route::post('api/naizhan/shengchan/tijiaojihua/modify', 'DSProductionPlanCtrl@modifyTijiaojihuaPlan');
    /*Show Naizhan qianshoujihua Page*/
    Route::get('/naizhan/shengchan/qianshoujihua', 'DSProductionPlanCtrl@showNaizhanQianshoujihua');
    /*Confirm receipt product and bottle*/
    Route::put('api/naizhan/shengchan/qianshoujihua/confirm_product', 'DSProductionPlanCtrl@confirm_Plan_count');
    /*refund box & bottle from milk station*/
    Route::post('api/naizhan/shengchan/qianshoujihua/refund_bb', 'DSProductionPlanCtrl@refund_BB');
    /*show naizhan peisongguanli page*/
    Route::get('/naizhan/shengchan/peisongguanli', 'DSDeliveryPlanCtrl@showPeisongguanli');
    /*save distribution_plan*/
    Route::post('api/naizhan/shengchan/peisongguanli/save_distribution', 'DSDeliveryPlanCtrl@save_distribution');
    /*update distribution_plan*/
    Route::put('api/naizhan/shengchan/peisongguanli/save_changed_distribution', 'DSDeliveryPlanCtrl@save_changed_distribution');

    /*show Naizhan peisongliebiao Page*/
    Route::get('/naizhan/shengchan/peisongliebiao', 'DSDeliveryPlanCtrl@showPeisongliebiao')->name('naizhan_peisongliebiao');
    /*show Ziyingdingdan Page*/
    Route::get('/naizhan/shengchan/ziyingdingdan', 'DSDeliveryPlanCtrl@showZiyingdingdan');
    /*Save Ziyingdingdan*/
    Route::post('api/naizhan/shengchan/ziyingdingdan/save', 'DSDeliveryPlanCtrl@saveZiyingdingdan');
    /*Save Ziyingdingdan getXiaoqu*/
    Route::get('api/naizhan/shengchan/ziyingdingdan/getXiaoqu', 'DSDeliveryPlanCtrl@getXiaoquName');
    /*show Jinripeisongdan*/
    Route::get('/naizhan/shengchan/jinripeisongdan', 'DSDeliveryPlanCtrl@showJinripeisongdan');
    /*Show Peisongfanru Page*/
    Route::get('/naizhan/shengchan/peisongfanru', 'DSDeliveryPlanCtrl@showPeisongfanru');
    /*Save milkman_refund bottles*/
    Route::post('api/naizhan/shengchan/peisongfanru/bottleboxsave','DSDeliveryPlanCtrl@savebottleboxPeisongfanru');
    /*Confirm milkman_delivered_products*/
    Route::post('api/naizhan/shengchan/peisongfanru/confirmdelivery','DSDeliveryPlanCtrl@confirmdeliveryPeisongfanru');
    /*Add comment on Milkman delivery*/
    Route::post('api/naizhan/shengchan/peisongfanru/confirm', 'DSDeliveryPlanCtrl@confirm');
    /*Show customer admin Page*/
    Route::get('/naizhan/kehu/kehudangan', 'CustomerCtrl@showNaizhanUserPage');
    /*ping kuang admin- naizhanpeisongyuanpingkuang*/
    Route::get('/naizhan/pingkuang/peisongyuanpingkuang', 'BottleAdminCtrl@showNaizhanPeisonguanpingkuang');
    /*show naizhan pingkuangshouhui*/
    Route::get('/naizhan/pingkuang/pingkuangshouhui', 'BottleAdminCtrl@showNaizhanPingkuangshouhui');
    /*show naizhan pingkuangtoingji*/
    Route::get('/naizhan/pingkuang/pingkuangtongji', 'BottleAdminCtrl@showNaizhanPingkuangtongji');
    /*confirm today's bottle & box*/
    Route::post('api/naizhan/pingkuang/pingkuangshouhui/confirm', 'BottleAdminCtrl@confirmTodaysBottle');
    /*Show jibenziliao Page*/
    Route::get('/naizhan/naizhan/jibenziliao', 'StationCtrl@showJibenziliao');
    //update station info
    Route::post('api/naizhan/naizhan/jibenziliao/update_station_info', 'StationManageCtrl@update_station_info');
    /*Peisongyuan register Pagw*/
    Route::get('/naizhan/naizhan/peisongyuan', 'MilkManCtrl@showPeisongyuanRegister')->name('peisongyuan_page');;
    /*Peisongyuan fanwei-chakan*/
    Route::get('/naizhan/naizhan/fanwei-chakan/{peisongyuan}', 'MilkManCtrl@showFanwei');
    /*Get xiaoqi from street*/
    Route::get('api/naizhan/naizhan/peisongyuan/getXiaoqi', 'MilkManCtrl@getXiaoqi');
    /*Save peisongyuan info*/
    Route::post('api/naizhan/naizhan/peisongyuan/savePeisongyuan', 'MilkManCtrl@savePeisongyuan');
    Route::post('api/naizhan/naizhan/peisongyuan/updatePeisongyuan', 'MilkManCtrl@updatePeisongyuan');
    /*delete peisongyuan*/
    Route::delete('api/naizhan/naizhan/peisongyuan/deletePeisongyuan/{peisongyuan}', 'MilkManCtrl@deletePeisongyuan');
    /*delete peisongyuanArea*/
    Route::post('api/naizhan/naizhan/fanwei-chakan/deleteDeliveryArea', 'MilkManCtrl@deletePeisongyuanArea');
    /*modify PeisongyuanArea*/
    Route::post('api/naizhan/naizhan/fanwei-chakan/modifyPeisongyuanArea', 'MilkManCtrl@modifyPeisongyuanArea');
    /*add delivery street*/
    Route::post('naizhan/naizhan/fanwei-chakan/street', 'MilkManCtrl@addDeliveryArea');
    /*sort PeisongyuanArea*/
    Route::post('api/naizhan/naizhan/fanwei-chakan/sortPeisongyuanArea', 'MilkManCtrl@sortPeisongyuanArea');
    /*show xiaoxinzhongxin*/
    Route::get('/naizhan/xiaoxi/zhongxin', 'NotificationsAdmin@showNaizhanZhongxin');
    /*change to active*/
    Route::post('api/naizhan/xiaoxi/zhongxin/changeActiveStatus', 'NotificationsAdmin@changetoActive');
    /*change to inactive*/
    Route::post('api/naizhan/xiaoxi/zhongxin/changeInActiveStatus', 'NotificationsAdmin@changetoInactive');
    /*show xiaoxinxiangxi*/
    Route::get('/naizhan/xiaoxi/xianqing/{dsnotification_id}', 'NotificationsAdmin@showNaizhanXiangxi');
    /*show tongji-dingdan Page*/
    Route::get('/naizhan/tongji/dingdan', 'DSStatistics@showDingdan');
    /*show tongji-dingdanshengyuliang Page*/
    Route::get('/naizhan/tongji/dingdanshenyuliang', 'DSStatistics@showDingdanshengyuliang');
    /*show tongji-naipinpeisongri Page*/
    Route::get('/naizhan/tongji/naipinpeisongri', 'DSStatistics@showNaipinpeisongri');
    /*show tongji-Peisongyuanwei*/
    Route::get('/naizhan/tongji/peisongyuanwei', 'DSStatistics@showPeisongyuanwei');


    /*Naizhan ORDER*/
    Route::get('/naizhan/dingdan', 'OrderCtrl@show_all_dingdan_in_naizhan');
    //Show all dingdan in order first page
    Route::get('/naizhan/dingdan/quanbuluru', 'OrderCtrl@show_all_dingdan_in_naizhan');
    //Show orders that can be xudan
    Route::get('/naizhan/dingdan/xudanliebiao', 'OrderCtrl@show_xudan_dingdan_liebiao_in_naizhan');
    //Show check waiting dingdan
    Route::get('/naizhan/dingdan/daishenhe', 'OrderCtrl@show_check_waiting_dingdan_in_naizhan');
    //Show paseed dingdan
    Route::get('/naizhan/dingdan/weiqinaidingdan', 'OrderCtrl@show_passed_dingdan_in_naizhan');
    //Show On Delivery Dingdan
    Route::get('/naizhan/dingdan/zaipeisong', 'OrderCtrl@show_on_delivery_dingdan_in_naizhan');
    //Show Stopped Dingdan
    Route::get('/naizhan/dingdan/zantingliebiao', 'OrderCtrl@show_stopped_dingdan_list_in_naizhan');
    //Show Not Passed Dingdan
    Route::get('/naizhan/dingdan/weitongguo', 'OrderCtrl@show_not_passed_dingdan_in_naizhan');
    //show insert dingdan page
    Route::get('/naizhan/dingdan/dingdanluru', 'OrderCtrl@show_insert_order_page_in_naizhan');

    //Insert Customer for order
    Route::post('api/naizhan/dingdan/dingdanluru/insert_customer', 'OrderCtrl@insert_customer_for_order_in_naizhan');
    //Insert Order
    Route::post('api/naizhan/dingdan/dingdanluru/insert_order', 'OrderCtrl@insert_order_in_naizhan');
    //Go to Xudan Page
    Route::get('/naizhan/dingdan/luruxudan/{order_id}', 'OrderCtrl@show_xudan_dingdan_in_naizhan');

    //show detail order
    Route::get('/naizhan/dingdan/xiangqing/{order_id}', 'OrderCtrl@show_detail_order_in_naizhan');


    //Restart Stopped Dingdan
    Route::post('api/naizhan/dingdan/restart_order', 'OrderCtrl@restart_dingdan');
    //Cancel Order
    Route::post('api/naizhan/dingdan/cancel_order', 'OrderCtrl@cancel_order');
    //Stop Order
    Route::post('api/naizhan/dingdan/stop_order', 'OrderCtrl@stop_order_for_some_period');

    //postpone order
    Route::post('api/naizhan/dingdan/postpone_order', 'OrderCtrl@postpone_order');

    //Show Xiugai Page
    Route::get('naizhan/dingdan/xiugai/{order_id}', 'OrderCtrl@show_order_revise_in_naizhan');
    Route::post('api/naizhan/dingdan/dingdanxiugai/change_order_info', 'OrderCtrl@change_order_info');

    /*FINANCE MANAGEMENT CTRL*/
    //Finance First page: stations account info page
    Route::get('/naizhan/caiwu/taizhang', 'FinanceCtrl@show_finance_page_in_naizhan');
    //Show selected station's order money
    Route::get('/naizhan/caiwu/taizhang/benzhandingdan', 'FinanceCtrl@show_station_order_money_in_naizhan');
    //show station account Calc balance
    Route::get('naizhan/caiwu/taizhang/zhanghuyue', 'FinanceCtrl@show_station_calc_account_balance_in_naizhan');
    //show self account balance
    Route::get('/naizhan/caiwu/ziyingzhanghujiru', 'FinanceCtrl@show_self_account_in_naizhan');

    //show transaction status between other stations
    Route::get('/naizhan/caiwu/taizhang/qitanaizhanzhuanzhang/xianjinzhuanzhangjiru', 'FinanceCtrl@show_transaction_between_other_station_in_naizhan');
    //Show transaction list not checked
    Route::get('/naizhan/caiwu/taizhang/qitanaizhanzhuanzhang/zhuanzhangzhangdan', 'FinanceCtrl@show_transaction_list_not_checked_in_naizhan');

    //Show transaction record completed
    Route::get('/naizhan/caiwu/taizhang/qitanaizhanzhuanzhang/zhuanzhangjilu', 'FinanceCtrl@show_transaction_record_completed_for_other_money_in_naizhan');
    //Show detail transaction
    Route::get('/naizhan/caiwu/taizhang/qitanaizhanzhuanzhang/zhangdanmingxi/{tid}', 'FinanceCtrl@show_transaction_detail_to_others_in_naizhan');
    //show milk card delivery status
    Route::get('/naizhan/caiwu/taizhang/naikakuanzhuanzhang/dingdanjiru', 'FinanceCtrl@show_orders_for_card_transaction_in_naizhan');
    Route::get('/naizhan/caiwu/taizhang/naica', 'FinanceCtrl@show_orders_for_card_transaction_in_naizhan');

    //show milk card delivery list
    Route::get('/naizhan/caiwu/taizhang/naikakuanzhuanzhang/zhuanzhangzhangdan', 'FinanceCtrl@show_transaction_list_not_checked_for_card_in_naizhan');
    //show milk card delivery record
    Route::get('/naizhan/caiwu/taizhang/naikakuanzhuanzhang/zhuanzhangjiru', 'FinanceCtrl@show_card_transaction_record_in_naizhan');
    //show milk card delivery detail
    Route::get('/naizhan/caiwu/taizhang/naikakuanzhuanzhang/zhangdanmingxi/{trs_id}', 'FinanceCtrl@show_card_transaction_detail_in_naizhan');

    Route::get('/naizhan/shouye', function (Request $request) {
        $child = '';
        $parent = 'shouye';
        $current_page = 'shouye';
        $pages = App\Model\UserModel\Page::where('backend_type', '3')->where('parent_page', '0')->orderby('order_no')->get();
        return view('naizhan.shouye', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page

        ]);
    });

});

Route::get('/naizhan/dingdan/daoqi', function (Request $request) {
    $child = 'quanbuluru';
    $parent = 'dingdan';
    $current_page = 'daoqi';
    $pages = App\Model\UserModel\Page::where('backend_type', '3')->where('parent_page', '0')->orderby('order_no')->get();
    return view('naizhan.dingdan.dingdanluru.daoqi', [
        'pages' => $pages,
        'child' => $child,
        'parent' => $parent,
        'current_page' => $current_page
    ]);
});
//naizhan mgmt
Route::get('/naizhan/naizhan', 'StationCtrl@showJibenziliao');
//shenchan and peisong
Route::get('/naizhan/shengchan', function (Request $request) {
    $child = 'shengchan';
    $parent = 'shengchan';
    $current_page = 'jihuaguanli';
    $pages = App\Model\UserModel\Page::where('backend_type', '3')->where('parent_page', '0')->get();
    return view('naizhan.shengchan.jihuaguanli', [
        'pages' => $pages,
        'child' => $child,
        'parent' => $parent,
        'current_page' => $current_page
    ]);
});
Route::get('/naizhan/tongji', function (Request $request) {
    $child = '';
    $parent = 'tongji';
    $current_page = 'tongji';
    $pages = App\Model\UserModel\Page::where('backend_type', '3')->where('parent_page', '0')->get();
    return view('naizhan.tongji.dingdan', [
        'pages' => $pages,
        'child' => $child,
        'parent' => $parent,
        'current_page' => $current_page
    ]);
});
//Kehu Dangan
Route::get('/naizhan/kehu', function (Request $request) {
    $child = '';
    $parent = 'kehu';
    $current_page = 'kehu';
    $pages = App\Model\UserModel\Page::where('backend_type', '3')->where('parent_page', '0')->get();
    return view('naizhan.kehu.kehudangan', [
        'pages' => $pages,
        'child' => $child,
        'parent' => $parent,
        'current_page' => $current_page
    ]);
});
//pingkuang
Route::get('/naizhan/pingkuang', function (Request $request) {
    $child = '';
    $parent = 'pingkuang';
    $current_page = 'pingkuang';
    $pages = App\Model\UserModel\Page::where('backend_type', '3')->where('parent_page', '0')->get();
    return view('naizhan.pingkuang.peisongyuanpingkuang', [
        'pages' => $pages,
        'child' => $child,
        'parent' => $parent,
        'current_page' => $current_page
    ]);
});
//xiaoxi
Route::get('/naizhan/xiaoxi', function (Request $request) {
    $child = '';
    $parent = 'xiaoxi';
    $current_page = 'xiaoxi';
    $pages = App\Model\UserModel\Page::where('backend_type', '3')->where('parent_page', '0')->get();
    return view('naizhan.xiaoxi.xianqing', [
        'pages' => $pages,
        'child' => $child,
        'parent' => $parent,
        'current_page' => $current_page
    ]);
});


/*Z-O-N-G-P-I-N-G-T-A-I*/
//user login
Route::get('/zongpingtai', function () {
    return view('zongpingtai.auth.login');
});
Route::post('/zongpingtai/login', 'ZongpingtaiAuth\AuthController@login');
Route::get('/zongpingtai/logout', 'ZongpingtaiAuth\AuthController@logout');

Route::group(['middleware' => ['zongpingtai']], function () {

//    Route::get('/zongpingtai/logout', function(){
//        Auth::logout();
//        return view('zongpingtai.auth.login');
//    });

    Route::get('/zongpingtai/shouye', function (Request $request) {
        $child = '';
        $parent = '';
        $current_page = 'shouye';
        $pages = App\Model\UserModel\Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.shouye', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page
        ]);
    });

    Route::get('/zongpingtai/xitong', function (Request $request) {
        return Redirect::to('/zongpingtai/xitong/chakanrizhi');
    });

    Route::get('/zongpingtai/xitong/chakanrizhi', 'SysManagerCtrl@showSystemLog');

    Route::get('/zongpingtai/xitong/fujianshezhi', function (Request $request) {
        $child = 'fujianshezhi';
        $parent = 'xitong';
        $current_page = 'fujianshezhi';
        $pages = App\Model\UserModel\Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.xitong.fujianshezhi', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page
        ]);
    });

    Route::get('/zongpingtai/xitong/jianchagongju', function (Request $request) {
        $child = 'jianchagongju';
        $parent = 'xitong';
        $current_page = 'jianchagongju';
        $pages = App\Model\UserModel\Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.xitong.jianchagongju', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page
        ]);
    });
    Route::get('/zongpingtai/xitong/qitashezhi', function (Request $request) {
        $child = 'qitashezhi';
        $parent = 'xitong';
        $current_page = 'qitashezhi';
        $pages = App\Model\UserModel\Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.xitong.qitashezhi', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page
        ]);
    });
    Route::get('/zongpingtai/xitong/shujuku', function (Request $request) {
        $child = 'shujuku';
        $parent = 'xitong';
        $current_page = 'shujuku';
        $pages = App\Model\UserModel\Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.xitong.shujuku', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page
        ]);
    });

    // 更新缓存
    Route::get('/zongpingtai/xitong/gengxinhuankun', 'HuancunCtrl@showSystemHuan');
    Route::post('/zongpingtai/xitong/gengxinhuankun', 'HuancunCtrl@showPost');

    // 信息接口
    Route::get('/zongpingtai/xitong/xinxijiekou','YimeiSmsCtrl@showYimei');
    Route::post('/zongpingtai/xitong/xinxijiekou','YimeiSmsCtrl@showPost');

    // 数据库
    Route::get('/zongpingtai/xitong/shujuku','DatableCtrl@showDatable');

    Route::get('/zongpingtai/xitong/zhandiansheding', function (Request $request) {
        $child = 'zhandiansheding';
        $parent = 'xitong';
        $current_page = 'zhandiansheding';
        $pages = App\Model\UserModel\Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.xitong.zhandiansheding', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page
        ]);
    });

//yonghu page view

    Route::get('/zongpingtai/yonghu', 'UserCtrl@viewZongpingGuanliyuan');
    Route::get('/zongpingtai/yonghu/yonghu', 'FactoryCtrl@viewUserPage')->name('yonghu_page');
    /*change status*/
    Route::post('api/zongpingtai/yonghu/yonghu/changeStatus','FactoryCtrl@changeStatus');

    Route::get('/zongpingtai/yonghu/gongzhonghaosheding/{factory_id}', 'FactoryCtrl@showPublicAccountSettingPage');
    Route::post('/zongpingtai/yonghu/gongzhonghaosheding/{factory_id}', 'FactoryCtrl@updatePublicAccountSetting');
    Route::post('/api/zongpingtai/yonghu/gongzhonghaosheding/delete_banner', 'FactoryCtrl@delete_banner');

    /*view Guanliyuan page*/
    Route::get('/zongpingtai/yonghu/guanliyuanzhongxin', 'UserCtrl@viewZongpingGuanliyuan');
    /*get information from guanliyuan page*/
    Route::get('api/zongpingtai/yonghu/guanliyuanzhongxin/{guanliyuaninfo?}', 'UserCtrl@getZongpingGuanliyuan');
    /*add guanliyuan info*/
    Route::post('api/zongpingtai/yonghu/guanliyuanzhongxin', 'UserCtrl@addZongpingGuanliyuan');
    /*update guanliyuan info*/
    Route::put('api/zongpingtai/yonghu/guanliyuanzhongxin/{guanliyuaninfo}', 'UserCtrl@updateZongpingGuanliyuan');
    /*change guanliyuan status info*/
    Route::put('api/zongpingtai/yonghu/guanliyuanzhongxin/changeStatus/{guanliyuaninfo}', 'UserCtrl@changeStatusZongpingGuanliyuan');
    /*delete guanliyuan info*/
    Route::delete('api/zongpingtai/yonghu/guanliyuanzhongxin/{guanliyuaninfo?}', 'UserCtrl@deleteZongpingGuanliyuan');

    Route::get('/zongpingtai/yonghu/juese/{role_id?}', 'UserRoleCtrl@viewZongpingtaiPage')->name('zongpingtai_juese');
    /*Add Role_name*/
    Route::post('api/zongpingtai/yonghu/juese', 'UserRoleCtrl@addRole');
    /*Delete Role_name*/
    Route::delete('api/zongpingtai/yonghu/juese/{role_id}', 'UserRoleCtrl@deleteRole');

    Route::get('api/zongpingtai/yonghu/juese/{role_id?}', 'UserRoleCtrl@index');
    /*Save inupt value*/
    Route::post('api/zongpingtai/yonghu/juese/store', 'UserRoleCtrl@store');


    /*Save User register of Zongpingtai*/
    Route::post('api/zongpingtai/yonghu/tianjia', 'FactoryCtrl@storeTianjia');
    /*show yonghuguanli register page*/
    Route::get('/zongpingtai/yonghu/tianjia', 'FactoryCtrl@showTianjia');
    /*Modify pageShow*/
    Route::get('/zongpingtai/yonghu/xiangqing/{user_id}', 'FactoryCtrl@showTianjiaModify');
    /*Update User register of Zongpingtai*/
    Route::post('api/zongpingtai/yonghu/xiangqing/{user_id}', 'FactoryCtrl@updateTianjia');

    /*CAIWU*/
    //Show wechat orders list for transactions
    Route::get('/zongpingtai/caiwu/zhangwujiesuan', 'FinanceCtrl@show_wechat_orders')->name('show_wechat_orders');
    Route::post('api/zongpingtai/factory_to_station', 'FinanceCtrl@get_station_from_factory');

    Route::post('/zongpingtai/caiwu/create_transaction', 'FinanceCtrl@show_transaction_creation_page_for_wechat');

    //Show wechat transactions not checked
    Route::get('/zongpingtai/caiwu/zhangwujiesuan/zhangdanzhuanzhang/{factory_id}', 'FinanceCtrl@show_wechat_transaction_list_not_checked_in_zongpingtai');
    Route::post('api/zongpingtai/caiwu/zhangwujiesuan/zhangdanzhuanzhang/get_trans_data', 'FinanceCtrl@get_trans_data_for_wechat');
    Route::post('api/zongpingtai/caiwu/zhangwujiesuan/zhangdanzhuanzhang/complete_trans', 'FinanceCtrl@complete_trans_for_wechat');
    //Show transaction record completed
    Route::get('/zongpingtai/caiwu/zhangwujiesuan/lishizhuanzhangjiru/{factory_id}', 'FinanceCtrl@show_wechat_transaction_list_checked_in_zongpingtai');
    //show detail of wechat transaction
    Route::get('/zongpingtai/caiwu/zhangwujiesuan/zhangdanmingxi/{trs_id}', 'FinanceCtrl@show_wechat_transaction_detail_in_zongpingtai');

    //ZHANGHU - FACTORY
    //Show all stations in certain factory
    Route::get('/zongpingtai/caiwu/zhanghuguanli', 'FinanceCtrl@show_factories_stations');
    //Show one factory
    Route::get('/zongpingtai/caiwu/zhanghu/zhanghugaikuang/{fid}', 'FinanceCtrl@show_one_factory');
    Route::get('/zongpingtai/caiwu/zhanghu/zhanghujiru/{fid}', 'FinanceCtrl@show_all_transactions_one_factory');

//kehu
    Route::get('/zongpingtai/kehu/kehuliebiao', 'CustomerCtrl@showZongpingtaiUserPage');
//tongji

    Route::get('/zongpingtai/tongji', 'TotalStatisticsCtrl@naipinpeisong');

    Route::get('/zongpingtai/tongji/kehudingdanxiugai', 'TotalStatisticsCtrl@showKehudingdanxiugui');

    /*zongpingtai dingdanleixiang*/
    Route::get('/zongpingtai/tongji/dingdanleixing','TotalStatisticsCtrl@dingdanleixing');

    Route::get('/zongpingtai/tongji/kehuxingwei', 'TotalStatisticsCtrl@showKehuxingweitongji');

    Route::get('/zongpingtai/tongji/liuliang', function (Request $request) {
        $child = 'liuliang';
        $parent = 'tongji';
        $current_page = 'liuliang';
        $pages = App\Model\UserModel\Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.tongji.liuliang', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page
        ]);
    });

    /*Tongji naipinpeisong*/
    Route::get('/zongpingtai/tongji/naipinpeisong','TotalStatisticsCtrl@naipinpeisong');

    Route::get('/zongpingtai/tongji/xiangxitongjiliang', function (Request $request) {
        $child = 'liuliang';
        $parent = 'tongji';
        $current_page = 'xiangxitongjiliang';
        $pages = App\Model\UserModel\Page::where('backend_type', '1')->where('parent_page', '0')->get();
        return view('zongpingtai.tongji.liuliang.xiangxitongjiliang', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page
        ]);
    });

});

Route::get('/weixin/weixinservice', 'Weixin\WeChatsCtrl@index');
Route::post('/weixin/weixinservice', 'Weixin\WeChatsCtrl@index');
Route::get('/weixin/createMenus', 'Weixin\WeChatsCtrl@createMenus');
Route::post('/weixin/createMenus', 'Weixin\WeChatsCtrl@createMenus');


Route::group(['prefix'=>'/weixin'], function(){
    /* home page */
    Route::get('/qianye',  'WeChatCtrl@showIndexPage')->name('weixin_qianye');
    Route::post('api/set_session_address',  'WeChatCtrl@set_session_address');

    /* product list */
    Route::get('/shangpinliebiao', 'WeChatCtrl@shangpinliebiao');

    /* show order item */
    Route::get('/tianjiadingdan', 'WeChatCtrl@tianjiadingdan');
    // add order item to cart
    Route::post('api/insert_order_item_to_cart', 'WeChatCtrl@insert_order_item_to_cart');
    //make order directly
    Route::post('api/make_order_directly', 'WeChatCtrl@make_order_directly');
    //add product to order for xiugai
    Route::post('api/add_product_to_order_for_xiugai', 'WeChatCtrl@add_product_to_order_for_xiugai');

    /* shopping cart */
    Route::get('/gouwuche', 'WeChatCtrl@gouwuche')->name('gouwuche');
    Route::post('/gouwuche/delete_cart', 'WeChatCtrl@delete_cart');
    Route::post('/gouwuche/api/make_wop_group', 'WeChatCtrl@make_wop_group');
    Route::post('/gouwuche/api/delete_selected_wop', 'WeChatCtrl@delete_selected_wop');
    Route::post('/api/check_verified_before_checkout', 'WeChatCtrl@check_verified_before_checkout');

    /* confirm order before purchase */
    Route::get('/querendingdan', 'WeChatCtrl@querendingdan')->name('querendingdan');
    //make order from cart
    Route::post('/api/make_order_by_group', 'WeChatCtrl@make_order_by_group');

    //edit order product
    Route::get('/bianjidingdan', 'WeChatCtrl@bianjidingdan');
    Route::post('/bianjidingdan/save_changed_order_item', 'WeChatCtrl@save_changed_order_item');

    /* addresses */
    Route::get('/dizhiliebiao', 'WeChatCtrl@dizhiliebiao')->name('dizhiliebiao');
    Route::get('/dizhitianxie', 'WeChatCtrl@dizhitianxie');
    Route::post('/dizhitianxie', 'WeChatCtrl@addOrUpdateAddress');
    Route::post('/delete_address', 'WeChatCtrl@deleteAddress');
    Route::post('/select_address', 'WeChatCtrl@selectAddress');

    /* contact */
    Route::get('/toushu', 'WeChatCtrl@toushu');

    /* pay success */
    Route::get('/zhifuchenggong', 'WeChatCtrl@zhifuchenggong');
    /* pay failure */
    Route::get('/zhifushibai', 'WeChatCtrl@zhifushibai');

    /* profile */
    Route::get('/gerenzhongxin', 'WeChatCtrl@gerenzhongxin');

    /* notification */
    Route::get('/xinxizhongxin', 'WeChatCtrl@xinxizhongxin');

    /* view review */
    Route::get('/wodepingjia', 'WeChatCtrl@wodepingjia')->name('wodepingjia');

    /* write review */
    Route::get('/dingdanpingjia', 'WeChatCtrl@dingdanpingjia');
    /* add review */
    Route::post('/dingdanpingjia/addpingjia','WeChatCtrl@addPingjia');
    /* order schedule */
    Route::get('/dingdanrijihua',  'WeChatCtrl@dingdanrijihua');

    /* order list */
    Route::get('/dingdanliebiao', 'WeChatCtrl@dingdanliebiao');

    /* order detail */
    Route::get('/dingdanxiangqing', 'WeChatCtrl@dingdanxiangqing');

    /* xuedan */
    Route::get('/show_xuedan', 'WeChatCtrl@show_xuedan')->name('show_xuedan');
    //make xudan based on created wechat order products
    Route::post('/api/make_order_from_wopids', 'WeChatCtrl@make_order_from_wopids');

    /* change order */
    Route::get('/dingdanxiugai', 'WeChatCtrl@dingdanxiugai');
    Route::get('/naipinxiugai', 'WeChatCtrl@naipinxiugai');
    //change order product temporally based on session
    Route::post('api/change_temp_order_product', 'WeChatCtrl@change_temp_order_product');
    Route::post('api/remove_product_from_order', 'WeChatCtrl@remove_product_from_order');
    Route::post('api/cancel_change_order', 'WeChatCtrl@cancel_change_order');
    Route::post('api/change_order', 'WeChatCtrl@change_order');

    /* change order per day */
    Route::get('/danrixiugai', 'WeChatCtrl@danrixiugai');
    Route::post('api/change_delivery_plan_for_one_date', 'WeChatCtrl@change_delivery_plan_for_one_date');

    /*dengji*/
    Route::get('dengji', 'WeChatCtrl@dengji')->name('dengji');
    Route::get('dengchu', 'WeChatCtrl@dengchu');
    //send verify code to phone
    Route::post('/api/send_verify_code_to_phone', 'WeChatCtrl@send_verify_code_to_phone');
    Route::post('/api/check_verify_code', 'WeChatCtrl@check_verify_code');

    Route::get('/show_session', 'WeChatCtrl@show_session');

});
//
//Route::any('{undefinedRoute}', function(){
//    return view('gongchang.auth.login');
//})->where('undefinedRoute', '([A-z\d-\/_.]+)?');