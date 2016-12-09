<?php

namespace App\Http\Controllers;

use App\Model\FactoryModel\MilkCard;

use App\Model\UserModel\Page;
use App\Model\UserModel\User;
use App\Model\SystemModel\SysLog;

use Illuminate\Http\Request;
use Auth;
use DateTime;
use DateTimeZone;
use App\Http\Requests;
use App\Http\Controllers\Controller;


class MilkCardCtrl extends Controller
{
    public function showNaikaPage(Request $request){
        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;
        $child = 'naika_child';
        $parent = 'naika';
        $current_page = 'naika';

        $pages = Page::where('backend_type', '2')->where('parent_page', '0')->get();
        $milkcard_count = count(MilkCard::where('factory_id',$current_factory_id)->get());
        $milkcard_used = count(MilkCard::where('factory_id',$current_factory_id)->where('sale_status',1)->get());
        $milkcards = MilkCard::where('factory_id',$current_factory_id)->get()->groupBy(function ($sort){return $sort->batch_number;});
        $milkcard_balance = MilkCard::where('factory_id',$current_factory_id)->get()->groupBy(function ($balance_sort){return $balance_sort->balance;});
        $balance = array();
        foreach ($milkcard_balance as $k=>$mb){
            $balance[$k]['balance'] = $k;
        }

        return view('gongchang.naika.naika', [
            'pages' => $pages,
            'child' => $child,
            'parent' => $parent,
            'current_page' => $current_page,
            'milkcards'=>$milkcards,
            'milkcard_count'=>$milkcard_count,
            'milkcard_used'=>$milkcard_used,
            'balance'=>$balance,
        ]);
    }

    public function getNaikaInfo(Request $request){

        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;

//        $balance = $request->input('balance');
        $milkcard_info = MilkCard::where('factory_id',$current_factory_id)
//            ->where('balance',$balance)
            ->where('sale_status',0)
            ->orderby('number')
            ->get();

//        $start_number = 0;
//        $end_number = 0;
//        $number = array();
//        $i = 0;
//        foreach ($milkcard_info as $mi){
//            $i++;
//            $number[$i] = $mi->number;
//        }
//        if($milkcard_info->first()!=null){
//            $count = count($milkcard_info);
//            $start_number = $number[1];
//            $end_number = $number[$count];
//        }
//        else{
//            $count = 0;
//        }

        return $milkcard_info;
//        return response()->json(['count'=>$count,'start_number'=>$start_number,'end_number'=>$end_number]);
    }

    public function registerNaikaInfo(Request $request){

        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;

        $currentDate = new DateTime("now",new DateTimeZone('Asia/Shanghai'));
        $currentDate_str = $currentDate->format('Y-m-d');

        $user = $request->input('user');
        $balance = $request->input('balance');
        $quantity = $request->input('quantity');
        $start_num = $request->input('start_num');
        $end_num = $request->input('end_num');
        $payment_method = $request->input('payment_method');

        $card_status = 0;

        $milkcards = MilkCard::where('factory_id',$current_factory_id)
            ->wherebetween('number', [$start_num, $end_num])
            ->where('sale_status',0)
            ->get();

        foreach ($milkcards as $mc){
            for($i=0; $i<$quantity; $i++){
                if($mc->number == $start_num+$i){
                    $card_status++;
                }
            }
        }

        if($card_status == $quantity){
            for($i=0; $i<$quantity; $i++){
                foreach ($milkcards as $mc){
                    if($mc->number == $start_num+$i){
                        $mc->payment_method = $payment_method;
                        $mc->sale_status = 1;
                        $mc->recipient = $user;
                        $mc->sale_date = $currentDate_str;
                        $mc->save();
                    }
                }
            }

            return redirect()->back();
        }
        else{
            return redirect()->back()->with('card_order_status',['Card number does not exist!']);
        }
    }

    public function importCard(Request $request) {
        $user = Auth::guard('gongchang')->user();
        $factory_id = $user->factory_id;

        if($request->hasFile('csv_file')){

            $file = $request->file('csv_file');
            $name = time() . '-' . $file->getClientOriginalName();

            $path = base_path() . '/public/csv/';

            $file->move($path, $name);

            $msg = $this->import_csv($path.$name, $factory_id);

            // 添加系统日志
            $this->addSystemLog(User::USER_BACKEND_FACTORY, '奶卡管理', SysLog::SYSLOG_OPERATION_IMPORT);

            return redirect()->back()->with('status', $msg);
        }

        return redirect()->back()->with('status', '数据导入失败');
    }

    private function import_csv($csv, $factory_id)
    {
        if(!file_exists($csv) || !is_readable($csv))
            return "不能打开文件。";

        $success = 0; $total = 0;

        $header = NULL;
        $data = array();
        if (($handle = fopen($csv, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle)) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else
//                    $data[] = array_combine($header, $row);
                    $data[] = $row;
            }
            fclose($handle);
        }


        foreach($data as $d){

            $total ++;
            if(count($d) != 5)
                continue;

            $oc = MilkCard::where('number', $d['1'])->where('factory_id', $factory_id)->get()->first();
            if(!$oc) {
                $c = new MilkCard;

                $c->factory_id = $factory_id;
                $c->batch_number = $d[0];
                $c->number = "".$d[1];
                $c->product = "".$d[2];
                $c->balance = $d[3];
                $c->password = $d[4];

                $c->save();
                $success ++;
            }
        }

        $msg = "".$success."/".$total."导入成功。";
        return $msg;

    }

    public function verify_card(Request $request) {

        $current_factory_id = Auth::guard('gongchang')->user()->factory_id;

        $card_id = $request->input('card_id');
        $card_code = $request->input('card_code');

        $card = MilkCard::where('number', $card_id)->where('factory_id', $current_factory_id)->get()->first();


        if($card == null) {
            return response()->json([
                'status'=>'failed',
                'msg' => '没有卡'
            ]);

        } else {
            if($card->password != $card_code) {
                return response()->json([
                    'status'=>'failed',
                    'msg' => '密码错了'
                ]);
            } else if($card->pay_status == 1) {
                return response()->json([
                    'status'=>'failed',
                    'msg' => '卡已经用了'
                ]);
            } else if($card->sale_status == 0) {
                return response()->json([
                    'status'=>'failed',
                    'msg' => '此卡未领用'
                ]);
            }
        }

        return response()->json([
            'status'=>'success',
            'balance'=>$card->balance,
            'product'=>$card->product,
        ]);
    }
}
