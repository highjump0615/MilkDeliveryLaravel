<?php

namespace App\Http\Controllers;

use App\Model\BasicModel\Address;
use App\Model\DeliveryModel\DSDeliveryArea;
use App\Model\FactoryModel\Factory;
use App\Model\NotificationModel\FactoryNotification;
use App\Model\UserModel\User;
use App\Model\UserModel\UserRole;
use App\Model\SystemModel\SysLog;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Model\UserModel\Page;

use App\Model\BasicModel\ProvinceData;
use App\Model\BasicModel\CityData;
use App\Model\BasicModel\DistrictData;

use App\Model\DeliveryModel\DeliveryStation;
use App\Model\DeliveryModel\DSType;
use App\Model\DeliveryModel\DSPaymentCalcType;

use App\Model\BasicModel\Bank;
use Illuminate\Support\Facades\Response;
use File;
use Auth;
use Symfony\Component\HttpKernel\EventListener\AddRequestFormatsListener;


class pinyin
{

    public static function utf8_to($s, $isfirst = false)
    {
        return self::to(self::utf8_to_gb2312($s), $isfirst);
    }

    public static function utf8_to_gb2312($s)
    {
        return iconv('UTF-8', 'GB2312//IGNORE', $s);
    }

    // 字符串必须为GB2312编码
    public static function to($s, $isfirst = false)
    {
        $res = '';
        $len = strlen($s);
        $pinyin_arr = self::get_pinyin_array();
        for ($i = 0; $i < $len; $i++) {
            $ascii = ord($s[$i]);
            if ($ascii > 0x80) {
                $ascii2 = ord($s[++$i]);
                $ascii = $ascii * 256 + $ascii2 - 65536;
            }

            if ($ascii < 255 && $ascii > 0) {
                if (($ascii >= 48 && $ascii <= 57) || ($ascii >= 97 && $ascii <= 122)) {
                    $res .= $s[$i]; // 0-9 a-z
                } elseif ($ascii >= 65 && $ascii <= 90) {
                    $res .= strtolower($s[$i]); // A-Z
                } else {
                    $res .= '_';
                }
            } elseif ($ascii < -20319 || $ascii > -10247) {
                $res .= '_';
            } else {
                foreach ($pinyin_arr as $py => $asc) {
                    if ($asc <= $ascii) {
                        $res .= $isfirst ? $py[0] : $py;
                        break;
                    }
                }
            }
        }
        return $res;
    }

    public static function to_first($s)
    {
        $ascii = ord($s[0]);
        if ($ascii > 0xE0) {
            $s = self::utf8_to_gb2312($s[0] . $s[1] . $s[2]);
        } elseif ($ascii < 0x80) {
            if ($ascii >= 65 && $ascii <= 90) {
                return strtolower($s[0]);
            } elseif ($ascii >= 97 && $ascii <= 122) {
                return $s[0];
            } else {
                return false;
            }
        }

        if (strlen($s) < 2) {
            return false;
        }

        $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;

        if ($asc >= -20319 && $asc <= -20284) return 'a';
        if ($asc >= -20283 && $asc <= -19776) return 'b';
        if ($asc >= -19775 && $asc <= -19219) return 'c';
        if ($asc >= -19218 && $asc <= -18711) return 'd';
        if ($asc >= -18710 && $asc <= -18527) return 'e';
        if ($asc >= -18526 && $asc <= -18240) return 'f';
        if ($asc >= -18239 && $asc <= -17923) return 'g';
        if ($asc >= -17922 && $asc <= -17418) return 'h';
        if ($asc >= -17417 && $asc <= -16475) return 'j';
        if ($asc >= -16474 && $asc <= -16213) return 'k';
        if ($asc >= -16212 && $asc <= -15641) return 'l';
        if ($asc >= -15640 && $asc <= -15166) return 'm';
        if ($asc >= -15165 && $asc <= -14923) return 'n';
        if ($asc >= -14922 && $asc <= -14915) return 'o';
        if ($asc >= -14914 && $asc <= -14631) return 'p';
        if ($asc >= -14630 && $asc <= -14150) return 'q';
        if ($asc >= -14149 && $asc <= -14091) return 'r';
        if ($asc >= -14090 && $asc <= -13319) return 's';
        if ($asc >= -13318 && $asc <= -12839) return 't';
        if ($asc >= -12838 && $asc <= -12557) return 'w';
        if ($asc >= -12556 && $asc <= -11848) return 'x';
        if ($asc >= -11847 && $asc <= -11056) return 'y';
        if ($asc >= -11055 && $asc <= -10247) return 'z';
        return false;
    }

    public static function get_pinyin_array()
    {
        static $py_arr;
        if (isset($py_arr)) return $py_arr;

        $k = 'a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo';
        $v = '-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274|-10270|-10262|-10260|-10256|-10254';
        $key = explode('|', $k);
        $val = explode('|', $v);
        $py_arr = array_combine($key, $val);
        arsort($py_arr);

        return $py_arr;
    }
}

class StationManageCtrl extends Controller
{
    //update station info
    public function update_station_info(Request $request)
    {
        if ($request->ajax()) {
            $st_user = Auth::guard('naizhan')->user();
            $st_id = $st_user->station_id;
            $ds = DeliveryStation::find($st_id);
            $factory_id = $ds->factory_id;

            $st_name = $request->input('st_name');
            $st_subaddr = $request->input('st_subaddr');
            $st_boss = $request->input('st_boss');
            $st_phone = $request->input('st_phone');

            $ds->name = $st_name;
            $ds->address = $ds->province_name . ' ' . $ds->city_name . ' ' . $ds->district_name . ' ' . $st_subaddr;
            $ds->boss = $st_boss;
            $ds->phone = $st_phone;
            $ds->save();

            $dest_dir = public_path() . '/img/station/logo/';

            if (!file_exists($dest_dir))
                $result = File::makeDirectory($dest_dir, 0777, true);


            if ($ds && $request->hasFile('station_img')) {
                $file = $request->file('station_img');

                if ($file->isValid()) {
                    $basename = $file->getClientOriginalName();
                    $ext = $file->getClientOriginalExtension();
                    $filename = basename($basename, '.' . $ext);
                    $new_file_name = 'F' . $factory_id . '_NZ' . $st_id . '_' . 'logo' . '.' . $ext;
                    $file->move($dest_dir, $new_file_name);
                    $ds->image_url = $new_file_name;
                    $ds->save();
                }
            }
            return response()->json(['status' => 'success']);
        }
    }

    //Show delivery Stations
    public function show_delivery_stations()
    {
        $factory_id = Auth::guard('gongchang')->user()->factory_id;
        $factory = Factory::find($factory_id);

        //get all delivery stations type
        $dstype = DStype::all();

        //get all districts that included in this factory
        $delivery_stations = DeliveryStation::where('factory_id', $factory_id)->where('is_deleted', 0)->get();

        $provinces = $factory->factory_provinces;

        $child = 'naizhanzhanghao';
        $parent = 'xitong';
        $current_page = 'naizhanzhanghao';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.xitong.naizhanzhanghao', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations' => $delivery_stations,
            'dstype' => $dstype,
            'provinces'=>$provinces,
        ]);
    }

    //Delete Station
    public function delete_station(Request $request)
    {
        if ($request->ajax()) {
            $sid = $request->input('station_id');
            $station = DeliveryStation::find($sid);
            if ($station) {
                //$station->delete();
                $station->is_deleted = 1;
                $station->save();
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'fail']);
            }
        }
    }

    //Activate-Deactivate the station
    public function change_status_of_station(Request $request)
    {
        if ($request->ajax()) {
            $sid = $request->input('station_id');
            $checked = $request->input('checked');

            $station = DeliveryStation::find($sid);

            $user_info = User::where('station_id',$sid)->where('user_role_id',200)->get()->first();
            if($user_info){
                if($checked == "true")
                    $user_info->status = User::USER_STATUS_ACTIVE;
                else
                    $user_info->status = User::USER_STATUS_INACTIVE;

                $user_info->save();
            }

            if ($station) {
                if ($checked == "true")
                    $station->status = DeliveryStation::DELIVERY_STATION_STATUS_ACTIVE;
                else
                    $station->status = DeliveryStation::DELIVERY_STATION_STATUS_INACTIVE;

                $station->save();

                return response()->json(['status' => 'success']);

            } else {
                return response()->json(['status' => 'fail']);
            }
        }
    }

    //Insert New Station
    public function insert_station(Request $request)
    {
        if ($request->ajax()) {
            $factory_id = $this->getCurrentFactoryId(true);

            $name = $request->input('st_name');

            $addr = ($request->input('select_province')) . ' ' .
                    ($request->input('select_city')) . ' ' .
                    ($request->input('select_district')) . ' ' .
                    ($request->input('select_street_xiaoqu'));

            $boss = $request->input('st_boss');

            $phone = $request->input('st_phone');
            $type = $request->input('st_type');

            $payment_calc_type = $request->input('fee_settle');

            $settle_account_name = $request->input('settle_account_name');
            $settle_account_card = $request->input('settle_account_card');

            $free_pay_name = $request->input('free_pay_name');
            $free_pay_card = $request->input('free_pay_card');

            $deliver_business_credit = $request->input('deliver_business_credit');
            $self_business_credit = $request->input('self_business_credit');
            $margin = $request->input('margin');

            $user_number = $request->input('user_number');
            $user_pwd = $request->input('user_pwd');
            $user_repwd = $request->input('user_repwd');

            // 奶站信息
            $ds = new DeliveryStation;

            $ds->name = $name;
            $ds->address = $addr;
            $ds->boss = $boss;
            $ds->phone = $phone;
            $ds->factory_id = $factory_id;
            $ds->station_type = $type;
            $ds->payment_calc_type = $payment_calc_type;
            $ds->status = DeliveryStation::DELIVERY_STATION_STATUS_ACTIVE;
            $ds->calculation_balance = 0;

            $ds->billing_account_name = $settle_account_name;
            $ds->billing_account_card_no = $settle_account_card;

            $ds->freepay_account_name = $free_pay_name;
            $ds->freepay_account_card_no = $free_pay_card;

            $ds->init_delivery_credit_amount = $deliver_business_credit;
            $ds->delivery_credit_balance = 0;

            $ds->init_business_credit_amount = $self_business_credit;
            $ds->business_credit_balance = 0;

            $ds->init_guarantee_amount = $margin;

            $ds->save();

            $dsid = $ds->id;
            $ds->number = $this->get_station_number($factory_id, $dsid);
            $ds->save();

            // 用户信息
            $account = new User;
            $account->name = $user_number;
            $account->password = bcrypt($user_pwd);
            $account->created_at = date("Y-m-d H:i:s");
            $account->status = User::USER_STATUS_ACTIVE;
            $account->backend_type = 3;
            $account->user_role_id = UserRole::USERROLE_NAIZHAN_TOTAL_ADMIN;
            $account->factory_id = $factory_id;
            $account->station_id = $ds->id;
            $account->save();

            //save delivery area ($dsid)
            $area_count = count($request->input('area_xiaoqu'));
            if ($area_count > 0) {
                for ($i = 0; $i < $area_count; $i++) {
                    $xiaoqu_id = $request->input('area_xiaoqu')[$i];
                    $xiaoqu = Address::find($xiaoqu_id);
                    if ($xiaoqu) {
                        $address = $xiaoqu->full_address_name;
                        $dsarea = new DSDeliveryArea;
                        $dsarea->address = $address;
                        $dsarea->station_id = $dsid;
                        $dsarea->save();
                    }

                }
            }

            // 添加系统日志
            $this->addSystemLog(User::USER_BACKEND_FACTORY, '奶站账户管理', SysLog::SYSLOG_OPERATION_ADD);

            // 添加奶厂通知
            $notification = new NotificationsAdmin();
            $notification->sendToFactoryNotification($factory_id, FactoryNotification::CATEGORY_PRODUCE, "奶站已添加成功", $name . "奶站已添加成功。");

            return response()->json(['status' => 'success', 'sid' => $dsid]);
        }
    }

    //Get Station Number
    public function get_station_number($fid, $sid)
    {
        $number = "F" . $fid . "_NZ" . $sid;
        return $number;
    }

    //Insert Station Image
    public function insert_station_image(Request $request)
    {
        if ($request->ajax()) {
            $fuser = Auth::guard('gongchang')->user();
            $factory_id = $fuser->factory_id;

            $dest_dir = public_path() . '/img/station/receipt/';

            if (!file_exists($dest_dir))
                $result = File::makeDirectory($dest_dir, 0777, true);

            $dsid = $request->input('sid');
            $ds = DeliveryStation::find($dsid);

            if ($ds && $request->hasFile('station_img')) {
                $file = $request->file('station_img');

                if ($file->isValid()) {
                    $basename = $file->getClientOriginalName();
                    $ext = $file->getClientOriginalExtension();
                    $filename = basename($basename, '.' . $ext);
                    $new_file_name = 'F' . $factory_id . '_NZ' . $dsid . '_' . 'Receipt' . '.' . $ext;
                    $file->move($dest_dir, $new_file_name);
                    $ds->guarantee_receipt_path = $new_file_name;
                    $ds->save();
                }
            }
            return response()->json(['status' => 'success', 'sid' => $dsid]);
        }
    }

    //Update Station
    public function update_station(Request $request)
    {
        if ($request->ajax()) {
            $fuser = Auth::guard('gongchang')->user();
            $factory_id = $fuser->factory_id;

            $name = $request->input('st_name');

            $addr = ($request->input('select_province')) . ' ' . ($request->input('select_city')) . ' ' . ($request->input('select_district')) . ' ' . ($request->input('select_street_xiaoqu'));

            $boss = $request->input('st_boss');

            $phone = $request->input('st_phone');
            $type = $request->input('st_type');

            $payment_calc_type = $request->input('fee_settle');

            $settle_account_name = $request->input('settle_account_name');
            $settle_account_card = $request->input('settle_account_card');

            $free_pay_name = $request->input('free_pay_name');
            $free_pay_card = $request->input('free_pay_card');

            $deliver_business_credit = $request->input('deliver_business_credit');
            $self_business_credit = $request->input('self_business_credit');
            $margin = $request->input('margin');

            $user_number = $request->input('user_number');
            $user_pwd = $request->input('user_pwd');
            $user_repwd = $request->input('user_repwd');

            $station_id = $request->input('station_id');

            // 奶站信息
            $ds = DeliveryStation::find($station_id);

            $ds->name = $name;
            $ds->address = $addr;
            $ds->boss = $boss;
            $ds->phone = $phone;
            $ds->factory_id = $factory_id;
            $ds->station_type = $type;
            $ds->payment_calc_type = $payment_calc_type;

            $ds->billing_account_name = $settle_account_name;
            $ds->billing_account_card_no = $settle_account_card;

            $ds->freepay_account_name = $free_pay_name;
            $ds->freepay_account_card_no = $free_pay_card;

            $ds->init_delivery_credit_amount = $deliver_business_credit;
            $ds->init_business_credit_amount = $self_business_credit;
            $ds->init_guarantee_amount = $margin;

            $ds->save();

            // 用户信息
            $account = $ds->getUser();
            $account->name = $user_number;

            if ($user_pwd)
                $account->password = bcrypt($user_pwd);

            $account->save();

            $dsid = $ds->id;

            $dssa = DSDeliveryArea::where('station_id', $dsid)->get();
            foreach ($dssa as $dsa) {
                $dsa->delete();
            }

            $area_count = count($request->input('area_xiaoqu'));
            for ($i = 0; $i < $area_count; $i++) {
                $xiaoqu_id = $request->input('area_xiaoqu')[$i];
                $xiaoqu = Address::find($xiaoqu_id);
                if ($xiaoqu) {
                    $address = $xiaoqu->full_address_name;
                    $dsarea = new DSDeliveryArea;
                    $dsarea->address = $address;
                    $dsarea->station_id = $dsid;
                    $dsarea->save();
                }

            }

            $sid = $ds->id;

            // 添加系统日志
            $this->addSystemLog(User::USER_BACKEND_FACTORY, '奶站账户管理', SysLog::SYSLOG_OPERATION_EDIT);

            return response()->json(['status' => 'success', 'sid' => $sid]);
        }
    }

    //Show change Station page
    public function show_change_station_page($station_id)
    {
        $child = 'naizhanzhanghao';
        $parent = 'xitong';
        $current_page = 'naizhanxiugai';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        $station = DeliveryStation::find($station_id);
        $fuser = Auth::guard('gongchang')->user();
        $fid = $fuser->factory_id;
        $factory = Factory::find($fid);

        $province = $factory->factory_provinces;

        $station_type = DSType::all();
        $calctype = DSPaymentCalcType::all();

        $area_address = array();
        $delivery_area = DSDeliveryArea::where('station_id', $station_id)->get();
        $i = 0;
        foreach ($delivery_area as $da) {
            $flag = 0;
            $cur_addr = explode(" ", $da->address);
            if ($i == 0) {
                $area_address[$i] = $cur_addr[3];
                $i++;
            }
            for ($j = 0; $j < $i; $j++) {
                if ($area_address[$j] == $cur_addr[3]) {
                    $flag = 1;
                }
            }
            if ($flag == 0) {
                $area_address[$i] = $cur_addr[3];
                $i++;
            }
        }
        $finial_area = array();
        foreach ($area_address as $aa) {
            $xiaoqu = array();
            $get_xiaoqu = DSDeliveryArea::where('station_id', $station_id)->where('address', 'LIKE', '%' . " " . $aa . " " . '%')->get();
            $l = 0;
            foreach ($get_xiaoqu as $gx) {
                $address = explode(' ', $gx->address);
                $xiaoqu_id = $gx->id;
                if (count($address) > 3) {
                    $xiaoqu[$l]['id'] = $xiaoqu_id;
                    $xiaoqu[$l]['name'] = $address[4];
                    $l++;
                }
            }
            $finial_area[$aa] = $xiaoqu;
        }

        $station['delivery_area'] = $delivery_area;

        return view('gongchang.xitong.naizhanzhanghao.naizhanxiugai', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'province' => $province,
            'station_type' => $station_type,
            'calctype' => $calctype,
            'station' => $station,
            'delivery_area' => $finial_area,
        ]);
    }

    //Show detail of station page
    public function show_detail_of_station_page($station_id)
    {
        $child = 'naizhanzhanghao';
        $parent = 'xitong';
        $current_page = 'zhanghuxiangqing-chakan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        $delivery_station = DeliveryStation::find($station_id);
        $delivery_station['type'] = DSType::find($delivery_station->station_type)->name;
        $delivery_station['pay_type'] = DSPaymentCalcType::find($delivery_station->payment_calc_type)->name;

        $area_address = array();
        $delivery_area = DSDeliveryArea::where('station_id', $station_id)->get();
        $i = 0;
        foreach ($delivery_area as $da) {
            $flag = 0;
            $cur_addr = explode(" ", $da->address);
            if ($i == 0) {
                $area_address[$i] = $cur_addr[3];
                $i++;
            }
            for ($j = 0; $j < $i; $j++) {
                if ($area_address[$j] == $cur_addr[3]) {
                    $flag = 1;
                }
            }
            if ($flag == 0) {
                $area_address[$i] = $cur_addr[3];
                $i++;
            }
        }
        $finial_area = array();
        foreach ($area_address as $aa) {
            $xiaoqu = array();
            $get_xiaoqu = DSDeliveryArea::where('station_id', $station_id)->where('address', 'LIKE', '%' . " " . $aa . " " . '%')->get();
            $l = 0;
            foreach ($get_xiaoqu as $gx) {
                $address = explode(' ', $gx->address);
                if (count($address) > 3) {
                    $xiaoqu[$l] = $address[4];
                    $l++;
                }
            }
            $finial_area[$aa] = $xiaoqu;
        }

        $delivery_station['delivery_area'] = $delivery_area;

        return view('gongchang.xitong.naizhanzhanghao.zhanghuxiangqing-chakan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'delivery_station' => $delivery_station,
            'delivery_area' => $finial_area,
        ]);
    }

    //Show insert Station page
    public function show_insert_station_page()
    {
        $factory_id = Auth::guard('gongchang')->user()->factory_id;
        $factory = Factory::find($factory_id);

        $province = $factory->factory_provinces;

        $station_type = DSType::all();
        $calctype = DSPaymentCalcType::all();

        $child = 'naizhanzhanghao';
        $parent = 'xitong';
        $current_page = 'tianjianaizhanzhanghu';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.xitong.naizhanzhanghao.tianjianaizhanzhanghu', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'province' => $province,
            'station_type' => $station_type,
            'calctype' => $calctype,
        ]);
    }

    //get station admin name automatically
    //Factory + Local Area + NO
    public function get_station_admin_name(Request $request)
    {
        if ($request->ajax()) {
            //get factory label
            $factory_id = Auth::guard('gongchang')->user()->factory_id;
            $factory = Factory::find($factory_id);
            $factory_name = $factory->name;

            $factory_label = pinyin::utf8_to($factory_name, 1);

            //get local area label
            $city_name = $request->input('city_name');
            $city_label = pinyin::utf8_to($city_name, 1);

            //
            // 获取最后的位数
            //
            $prefix = $factory_label . "_" . $city_label . "_";

            // get same station count
            $stations = User::where('name', 'like', $prefix . '%')->get();
            $count = count($stations);

            $index = $count + 1;

            $station_admin_name = $prefix . $index;
            return response()->json(['status' => 'success', 'name' => $station_admin_name]);
        }
    }


    /*Show station list for delivery area*/
    public function show_station_list_for_delivery_area()
    {
        $factory_id = Auth::guard('gongchang')->user()->factory_id;

        $factory = Factory::find($factory_id);

        $stations = $factory->active_stations;

        $provinces = $factory->factory_provinces;

        $child = 'naizhan';
        $parent = 'jichuxinxi';
        $current_page = 'naizhan';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();


        //Delivery area of this station: street and xiaoqu
        $area_address = array();

        foreach($stations as $s) {
            $station_id = $s->id;
            $delivery_areas = DSDeliveryArea::where('station_id', $station_id)->get();
            if ($delivery_areas->first() != null) {
                foreach ($delivery_areas as $da) {
                    if ($da->address != null) {
                        $xiaoqu = Address::addressObjFromName($da->address, $factory_id);

                        if ($xiaoqu) {
                            $area_address[$station_id][$xiaoqu->parent_id][0] = $xiaoqu->street->name;
                            $area_address[$station_id][$xiaoqu->parent_id][1][$xiaoqu->id] = $xiaoqu->name;
                        }
                    }
                }
            }
        }

        // 添加系统日志
        $this->addSystemLog(User::USER_BACKEND_FACTORY, '配送范围管理', SysLog::SYSLOG_OPERATION_VIEW);

        return view('gongchang.jichuxinxi.naizhan', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'stations' => $stations,
            'provinces' => $provinces,
            'area_address' => $area_address,
        ]);
    }

    //Show Station's Delivery Area
    public function show_delivery_area_of_station($station_id)
    {
        $factory_id = Auth::guard('gongchang')->user()->factory_id;

        //first get province, city, district of station: give limitation to this
        $station = DeliveryStation::where('id', $station_id)
            ->where('factory_id', $factory_id)
            ->where('status', DeliveryStation::DELIVERY_STATION_STATUS_ACTIVE)
            ->where('is_deleted', 0)->get()->first();

        $station_address = explode(" ", $station->address);

        $province = $station_address[0];
        $city = $station_address[1];
        $district = $station_address[2];

        //get all streets and xiaoqus in district
        $available_address = array();

        $district_entity = Address::addressObjFromName($province . " " . $city . " " . $district, $factory_id);

        $district_id = $district_entity->id;

        $street_entities = Address::where('parent_id', $district_id)->where('level', 4)
            ->where('factory_id', $factory_id)
            ->where('is_active', Address::ADDRESS_ACTIVE)
            ->where('is_deleted', 0)->get();

        foreach ($street_entities as $street_entity) {
            $street_id1 = $street_entity->id;
            $xiaoqus = Address::where('parent_id', $street_id1)->where('level', 5)
                ->where('factory_id', $factory_id)
                ->where('is_active', Address::ADDRESS_ACTIVE)
                ->where('is_deleted', 0)->get();
            foreach ($xiaoqus as $xiaoqu) {
                $available_address[$street_id1][0] = $street_entity->name;
                $xiaoqu_id1 = $xiaoqu->id;
                $available_address[$street_id1][1][$xiaoqu_id1] = $xiaoqu->name;
            }
        }

        //Delivery area of this station: street and xiaoqu
        $delivery_areas = DSDeliveryArea::where('station_id', $station_id)->get();

        $area_address = array();
        if ($delivery_areas->first() != null) {
            foreach ($delivery_areas as $da) {
                if ($da->address != null) {

                    $xiaoqu = Address::addressObjFromName($da->address, $factory_id);

                    if ($xiaoqu) {
                        $area_address[$xiaoqu->parent_id][0] = $xiaoqu->street->name;
                        $area_address[$xiaoqu->parent_id][1][$xiaoqu->id] = $xiaoqu->name;
                    }
                }
            }
        }

        //Sort address list
        asort($area_address);
        asort($available_address);

        $child = 'naizhan';
        $parent = 'jichuxinxi';
        $current_page = 'peisongfanwei-chakanbianji';
        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();

        return view('gongchang.jichuxinxi.naizhan.peisongfanwei-chakanbianji', [
            // 页面信息
            'pages'             => $pages,
            'child'             => $child,
            'parent'            => $parent,
            'current_page'      => $current_page,

            // 数据
            'station'           => $station,
            'area_address'      => $area_address,
            'province'          => $province,
            'city'              => $city,
            'district'          => $district,
            'available_address' => $available_address,
        ]);
    }

    //Delete station's deliveyr area
    public function delete_delivery_area(Request $request)
    {
        if ($request->ajax()) {
            $station_id = $request->input('station_id');
            $street_id = $request->input('street_id');
            $street = Address::find($street_id);

            $street_name = $street->name;
            $district = $street->district->name;
            $city = $street->city->name;
            $province = $street->province->name;

            $areas = DSDeliveryArea::where('station_id', $station_id)->where('address', 'like', $province . '%' . $city . '%' . $district . '%' . $street_name . '%')->get();

            foreach ($areas as $da) {
                $da->delete();
            }

            return Response::json(['status' => "success"]);
        }
    }

    //Add station's Delivery Area
    public function add_delivery_area(Request $request)
    {

        $station_id = $request->input('station_id');
        $street_id = $request->input('street_id');

        //Delete pre-exist delivery areas
        $xiaoqus = $request->input('to');
        $count = count($xiaoqus);


        if ($count > 0) {
            foreach ($xiaoqus as $xid) {
                $xiaoqu = Address::find($xid);
                if ($xiaoqu) {
                    $full_address = $xiaoqu->full_address_name;
                    $delivery_area = new DSDeliveryArea;
                    $delivery_area->station_id = $station_id;
                    $delivery_area->address = $full_address;
                    $delivery_area->save();
                }
            }
            return Response::json(['status' => 'success']);

        } else {
            return Response::json(['status' => 'fail']);
        }
    }

    //Change delivery area of station
    public function change_delivery_area(Request $request)
    {

        $station_id = $request->input('station_id');
        $street_id = $request->input('street_id_to_change');

        $xiaoqus = $request->input('to');
        $changed_count = count($xiaoqus);

        //Delete pre-exist delivery areas
        $street = Address::find($street_id);
        $street_name = $street->name;
        $district_name = $street->district->name;
        $city_name = $street->city->name;
        $province_name = $street->province->name;

        $areas = DSDeliveryArea::where('station_id', $station_id)->
        where('address', 'like', $province_name . '%' . $city_name . '%' . $district_name . '%' . $street_name . '%')->get();

        foreach ($areas as $area) {
            $area->delete();
        }

        if ($changed_count != 0) {
            //Make new delivery area
            foreach ($xiaoqus as $xiaoqu_id) {
                $this->make_delivery_area_for_xiaoqu($station_id, $xiaoqu_id);
            }
        }

        return response()->json(['status' => 'success']);

    }

    //Make new delivery area for xiaoqu with station
    public function make_delivery_area_for_xiaoqu($sid, $xid)
    {
        $xiaoqu = Address::find($xid);
        if (!$xiaoqu)
            return false;

        $address = $xiaoqu->full_address_name;

        $da = new DSDeliveryArea;
        $da->station_id = $sid;
        $da->address = $address;
        $da->save();

    }

    //Show
    public function showJibenziliao(Request $request)
    {
        $current_factory_id = Auth::guard('naizhan')->user()->station_id;

        $dsinfo = DeliveryStation::find($current_factory_id);
        $billing_bank = Bank::find($dsinfo->billing_bank_id);
        $freepay_bank = Bank::find($dsinfo->freepay_bank_id);
        $deliveryarea = DSDeliveryArea::where('station_id', $current_factory_id)->get()->groupBy(function ($area) {
            $addr = $area->address;
            $addrs = explode(" ", $addr);
            return $addrs[0] . $addrs[1] . $addrs[2] . $addrs[3];
        });

        $dsinfo["billing_bank"] = $billing_bank;
        $dsinfo["freepay_bank"] = $freepay_bank;
        $dsinfo["deliveryarea"] = $deliveryarea;

        $child = 'jibenziliao';
        $parent = 'naizhan';
        $current_page = 'jibenziliao';
        $pages = Page::where('backend_type','3')->where('parent_page', '0')->orderby('order_no')->get();

        return view('naizhan.naizhan.jibenziliao', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'dsinfo' => $dsinfo,
        ]);
    }
}

