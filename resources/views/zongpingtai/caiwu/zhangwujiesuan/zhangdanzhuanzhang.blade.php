@extends('zongpingtai.layout.master')
@section('css')
    <style>
        select {
            width: 100%;
            height: 30px;
        }

        label {
            padding-top: 5px;
        }

        .modal-body input {
            width: 100%;
            max-width: 100px;
            display: inline-block;
            position: relative;
        }

        .modal-header {
            background-color: #0b8cc5;
            overflow: auto;
            padding: 5px;
        }

        form {
            padding: 0px;
        }

    </style>
@endsection
@section('content')
    @include('zongpingtai.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('zongpingtai.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="{{ url('zongpingtai/caiwu/zhangwujiesuan') }}">财务管理</a>
                </li>
                <li>
                    <a href="{{ url('zongpingtai/caiwu/zhangwujiesuan') }}">账务结算</a>
                </li>
                <li class="active">
                    <a href=""><strong>账单转账</strong></a>
                </li>
            </ol>
        </div>

        <div class="row wrapper">
            <div class="wrapper-content">

                <div class="ibox-content">
                    <label class="col-md-1" style="padding-top: 5px;">奶站:</label>
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

                        <table id="order_table" class="table footable table-bordered">
                            <thead style="background-color:#00cc55;">
                            <tr>
                                <th data-sort-ignore="true">选择</th>
                                <th data-sort-ignore="true">奶站名称</th>
                                <th data-sort-ignore="true">上期余额</th>
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

                            @if (isset($ncts))
                                @foreach($ncts as $nct)
                                    <?php
                                    $j = 0;
                                    $first_row_span = count($nct);
                                    ?>
                                    @foreach($nct as $nc)
                                        <tr>
                                            @if($j == 0)
                                                <td rowspan="{{$first_row_span}}">
                                                    <input type="checkbox" checked class="i-checks"
                                                           data-tid="{{$nc->id}}"
                                                           data-station-id="{{$nc->delivery_station_id}}"/>
                                                </td>
                                                <td rowspan="{{$first_row_span}}">{{$nc->delivery_station_name}}</td>
                                                <td rowspan="{{$first_row_span}}">{{$nc->pre_remain_wechat}}</td>
                                            @endif

                                            <td>{{$nc->created_at}}</td>
                                            <td class="o_station" data-trsid="{{$nc->id}}"
                                                data-sid="{{$nc->delivery_station_id}}">{{$nc->id}}</td>
                                            <td>{{$nc->order_from}} ~ {{$nc->order_to}}</td>
                                            <td>{{$nc->total_amount}}</td>
                                            <td>{{$nc->order_count}}</td>
                                            <td>未转</td>
                                            <td>
                                                <a href="{{URL::to('/zongpingtai/caiwu/zhangwujiesuan/zhangdanmingxi/'.$nc->id)}}">查看明细</a>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <?php $j++?>
                                    @endforeach
                                @endforeach
                            @endif
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="100%">
                                    <ul class="pagination pull-right"></ul>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                        <table id="filter_table" class="table footable table-bordered">
                            <thead style="background-color:#00cc55;">
                            <tr>
                                <th data-sort-ignore="true">选择</th>
                                <th data-sort-ignore="true">奶站名称</th>
                                <th data-sort-ignore="true">上期余额</th>
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
                                <td colspan="100%">
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

                        <div id="insert_modal" class="modal fade" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form id="insert_modal_form" method="POST">
                                        <div class="modal-header" style="background-color:#0b8cc5;">
                                            <label class="col-md-offset-9 col-md-3"
                                                   style="color:white;">时间：{{$today}}</label>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <table class="footable table table-bordered" id="insert_modal_table"
                                                       data-page-size="5">
                                                    <thead>
                                                    <tr>
                                                        <th data-sort-ignore="true">序号</th>
                                                        <th data-sort-ignore="true">奶站名称</th>
                                                        <th data-sort-ignore="true">本期合计金额</th>
                                                        <th data-sort-ignore="true">上期余额</th>
                                                        <th data-sort-ignore="true">合计</th>
                                                        <th data-sort-ignore="true">实际转入金额</th>
                                                        <th data-sort-ignore="true">流水号</th>
                                                        <th data-sort-ignore="true">备注</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="100%">
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
                                        <label class="col-md-offset-9 col-md-3"
                                               style="color:white;">时间：{{$today}}</label>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <table id="print_modal_table" class="footable table table-bordered"
                                                   data-page-size="5">
                                                <thead>
                                                <tr>
                                                    <th data-sort-ignore="true">序号</th>
                                                    <th data-sort-ignore="true">奶站名称</th>
                                                    <th data-sort-ignore="true">本期合计金额</th>
                                                    <th data-sort-ignore="true">上期余额</th>
                                                    <th data-sort-ignore="true">合计</th>
                                                    <th data-sort-ignore="true">实际转入金额</th>
                                                    <th data-sort-ignore="true">流水号</th>
                                                    <th data-sort-ignore="true">备注</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td colspan="100%">
                                                        <ul class="pagination pull-right"></ul>
                                                    </td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-white" data-action="print_modal_data">
                                            打印
                                        </button>
                                        <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="<?=asset('js/pages/zongpingtai/finance_transfer.js') ?>"></script>
@endsection
