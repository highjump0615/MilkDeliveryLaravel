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

            // 标题行数、列数
            $nRowFixed = $request->input('row_header');
            $nColFixed = $request->input('column_header');

            Excel::create('exportlist', function ($excel) use ($data, $nRowFixed, $nColFixed) {

                $excel->sheet('Sheet1', function ($sheet) use ($data, $nRowFixed, $nColFixed) {

                    // 添加表格内容
                    foreach ($data as $row) {
                        $new_row = [];
                        foreach ($row as $data_cell) {
                            //$data_cell = mb_convert_encoding($data_cell, 'UTF-16LE', 'UTF-8');
                            //$data_cell = "\xFF\xFE".$data_cell;
                            array_push($new_row, $data_cell);
                        }
                        $sheet->appendRow($new_row);
                    }

                    //
                    // 整理表格；合并
                    //
                    $nRowIndex = 0;

                    // 横向合并
                    foreach ($data as $row) {
                        $nColIndex = 0;
                        $nMergeStart = 0;

                        foreach ($row as $data_cell) {
                            $bDoMerge = false;

                            // 该行不是标题行，只考虑标题行
                            if ($nRowIndex >= $nRowFixed) {
                                if ($nColIndex >= $nColFixed) {
                                    // 行结束
                                    $bDoMerge = true;
                                }
                            }

                            // 从第二列开始对比
                            if ($nColIndex > 0) {
                                // 横向扫描，出现两个以上相同内容的单元格进行合并
                                if ($data_cell != $row[$nColIndex - 1]) {
                                    $bDoMerge = true;
                                }
                            }

                            // 最后一列，合并到底
                            if ($nColIndex >= count($row) - 1) {
                                if (!$bDoMerge) {
                                    $sheet->mergeCellsByColumnAndRow($nMergeStart, $nRowIndex + 1, $nColIndex, $nRowIndex + 1);
                                }
                            }
                            // 合并
                            else if ($bDoMerge) {
                                if ($nColIndex - $nMergeStart > 1) {
                                    $sheet->mergeCellsByColumnAndRow($nMergeStart, $nRowIndex + 1, $nColIndex - 1, $nRowIndex + 1);
                                }

                                $nMergeStart = $nColIndex;
                            }

                            $nColIndex++;
                        }

                        $nRowIndex++;
                    }

                    // 纵向合并
                    for ($i = 0; $i < $nColIndex; $i++) {
                        $nMergeStart = 0;

                        for ($j = 0; $j < $nRowIndex; $j++) {
                            $bDoMerge = false;

                            // 该列不是标题列，只考虑标题列
                            if ($i >= $nColFixed) {
                                if ($j >= $nRowFixed) {
                                    // 列结束
                                    $bDoMerge = true;
                                }
                            }

                            // 从第二行开始对比
                            if ($j > 0) {
                                // 纵向扫描，出现两个以上相同内容的单元格进行合并
                                if ($data[$j][$i] != $data[$j - 1][$i]) {
                                    $bDoMerge = true;
                                }
                            }

                            // 最后一行，合并到底
                            if ($j >= $nRowIndex - 1) {
                                if (!$bDoMerge) {
                                    $sheet->mergeCellsByColumnAndRow($i, $nMergeStart + 1, $i, $j + 1);
                                }
                            }
                            // 合并
                            else if ($bDoMerge) {
                                if ($j - $nMergeStart > 1) {
                                    $sheet->mergeCellsByColumnAndRow($i, $nMergeStart + 1, $i, $j);
                                }

                                $nMergeStart = $j;
                            }
                        }
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
