<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Model\SystemModel\SysLog;
use Excel;

class ExportCtrl extends Controller
{
    public function export(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->input('data');

            Excel::create('exportlist', function ($excel) use ($data) {

                $excel->sheet('Sheet1', function ($sheet) use ($data) {

                    foreach ($data as $row) {
                        $new_row = [];
                        foreach ($row as $data_cell) {
                            //$data_cell = mb_convert_encoding($data_cell, 'UTF-16LE', 'UTF-8');
                            //$data_cell = "\xFF\xFE".$data_cell;
                            array_push($new_row, $data_cell);
                        }
                        $sheet->appendRow($new_row);
                    }
                });

            })->store('xls', 'exports');

            //
            // 添加系统日志
            //
            $nUserType = $request->input('usertype');

            if ($nUserType > 0) {
                $this->addSystemLog($request->input('usertype'), $request->input('page'), SysLog::SYSLOG_OPERATION_EXPORT);
            }

            return response()->json(['status' => 'success', 'path' => 'http://' . $request->server('HTTP_HOST') . '/milk/public/exports/exportlist.xls']);
        }
    }

    /**
     * 添加打印日志
     * @param Request $request
     */
    public function printLog(Request $request) {
        //
        // 添加系统日志
        //
        $nUserType = $request->input('usertype');

        if ($nUserType > 0) {
            $this->addSystemLog($request->input('usertype'), $request->input('page'), SysLog::SYSLOG_OPERATION_PRINT);
        }
    }
}
