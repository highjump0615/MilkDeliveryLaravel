{{--Show transaction list that has not been transfered--}}
@extends('gongchang.layout.master')
@section('css')
    <style>
        .modal-body input {
            height: 35px;
            width: 100%;
            display: inline-block;
            position: relative;
        }

        .modal-header {
            background-color: #0b8cc5;
            overflow: auto;
            padding: 5px;
        }
    </style>
@endsection

@section('content')
    @include('gongchang.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('gongchang.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">财务管理</a>
                </li>
                <li>
                    <a href={{URL::to('/gongchang/caiwu/taizhang')}}>奶站账户台账</a>
                </li>
                <li>
                    <a href={{URL::to('/gongchang/caiwu/taizhang/qitanaizhanzhuanzhang')}}>其他奶站转账</a>
                </li>
                <li>
                    <a href=""><strong>转账账单</strong></a>
                </li>
            </ol>
        </div>

        <div class="row white-bg">
            <div class="ibox-content">
                <label class="col-md-1" style="padding-top: 5px;">录入奶站:</label>
                <div class=" col-md-2">
                    <select id="filter_station" class="chosen-select form-control"
                            style="width:100%; height: 35px;">
                        <option value="none">全部</option>
                        @if(isset($station_name_list))
                            @foreach($station_name_list as $sid=>$sname)
                                <option value="{{$sid}}">{{$sname}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2 col-md-offset-7" style="padding-top:5px;">
                    <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
                    <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                    <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <table class="footable table table-bordered" id="order_table" data-page-size="10">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">选择</th>
                            <th data-sort-ignore="true">录入奶站</th>
                            <th data-sort-ignore="true">配送奶站</th>
                            <th data-sort-ignore="true">生成时间</th>
                            <th data-sort-ignore="true">账单号</th>
                            <th data-sort-ignore="true">账单日期</th>
                            <th data-sort-ignore="true">金额</th>
                            <th data-sort-ignore="true">订单数量</th>
                            <th data-sort-ignore="true">状态</th>
                            <th data-sort-ignore="true">操作</th>
                            <th data-sort-ignore="true">备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 0;?>
                        @foreach($ncts as $ncs)
                            <?php
                            $i++; $j = 0; $first_row_span = 0;
                            foreach ($ncs as $ncsd) {
                                $first_row_span += count($ncsd);
                            }
                            ?>
                            @foreach($ncs as $ncd)
                                <?php $k = 0;?>
                                @foreach($ncd as $nc)
                                    <?php $j++; $k++;?>
                                    <tr>
                                        @if($j==1)
                                            <td rowspan="{{$first_row_span}}">
                                                <input type="checkbox" checked class="i-checks" name="input[]"
                                                       data-tid="{{$nc->id}}" data-station-id="{{$nc->station_id}}">
                                            </td>
                                            <td rowspan="{{$first_row_span}}">{{$nc->station_name}}</td>
                                        @endif
                                        @if($k==1)
                                            <td rowspan="{{count($ncd)}}">{{$nc->delivery_station_name}}</td>
                                        @endif
                                        <td class="o_station" data-trsid="{{$nc->id}}"
                                            data-sid="{{$nc->station_id}}">{{$nc->created_at}}</td>
                                        <td>{{$nc->id}}</td>
                                        <td>{{$nc->order_from}} ~ {{$nc->order_to}}</td>
                                        <td>{{$nc->total_amount}}</td>
                                        <td>{{$nc->order_count}}</td>
                                        <td>未转</td>
                                        <td>
                                            <a href="{{URL::to('/gongchang/caiwu/taizhang/qitanaizhanzhuanzhang/zhangdanmingxi/'.$nc->id)}}">查看明细</a>
                                        </td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="100%">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>

                    <table class="footable table table-bordered" id="filter_table" data-page-size="10">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">选择</th>
                            <th data-sort-ignore="true">录入奶站</th>
                            <th data-sort-ignore="true">配送奶站</th>
                            <th data-sort-ignore="true">生成时间</th>
                            <th data-sort-ignore="true">账单号</th>
                            <th data-sort-ignore="true">账单日期</th>
                            <th data-sort-ignore="true">金额</th>
                            <th data-sort-ignore="true">订单数量</th>
                            <th data-sort-ignore="true">状态</th>
                            <th data-sort-ignore="true">操作</th>
                            <th data-sort-ignore="true">备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="11">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>

                    <div class="col-md-12">
                        <div class="col-md-8">
                            <label id="all_check"><input type="checkbox" checked class="i-checks"> 全选</label>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success" data-action="print_modal" type="button"
                                    style="width:100%;">生成转账单
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success" data-action="insert_modal" type="button"
                                    style="width:100%;">转入
                            </button>
                        </div>
                    </div>
                </div>
                <div id="insert_modal" class="modal fade" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form id="insert_modal_form" method="POST">
                                <div class="modal-header">
                                    <label class="col-md-offset-9 col-md-3" style="color:white;">时间：{{$today}}</label>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <table id="insert_modal_table" class="footable table table-bordered"
                                               data-page-size="5">
                                            <thead>
                                            <tr>
                                                <th data-sort-ignore="true">序号</th>
                                                <th data-sort-ignore="true">付款方</th>
                                                <th data-sort-ignore="true">收款方</th>
                                                <th data-sort-ignore="true">账单数量</th>
                                                <th data-sort-ignore="true">金额</th>
                                                <th data-sort-ignore="true">上期余额</th>
                                                <th data-sort-ignore="true">合计金额</th>
                                                <th data-sort-ignore="true">实际转入金额</th>
                                                <th data-sort-ignore="true">流水号</th>
                                                <th data-sort-ignore="true">备注</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="10">
                                                    <ul class="pagination pull-right"></ul>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-white">确定</button>
                                    <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="print_modal" class="modal fade" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <label class="col-md-offset-9 col-md-3" style="color:white;">时间：{{$today}}</label>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <table id="print_modal_table" class="footable table table-bordered"
                                           data-page-size="5">
                                        <thead>
                                        <tr>
                                            <th data-sort-ignore="true">序号</th>
                                            <th data-sort-ignore="true">付款方</th>
                                            <th data-sort-ignore="true">收款方</th>
                                            <th data-sort-ignore="true">账单数量</th>
                                            <th data-sort-ignore="true">金额</th>
                                            <th data-sort-ignore="true">上期余额</th>
                                            <th data-sort-ignore="true">合计金额</th>
                                            <th data-sort-ignore="true">实际转入金额</th>
                                            <th data-sort-ignore="true">流水号</th>
                                            <th data-sort-ignore="true">备注</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="10">
                                                <ul class="pagination pull-right"></ul>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-white" data-action="print_modal_data">打印
                                </button>
                                <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="<?=asset('js/pages/gongchang/station_finance_transfer.js') ?>"></script>
@endsection