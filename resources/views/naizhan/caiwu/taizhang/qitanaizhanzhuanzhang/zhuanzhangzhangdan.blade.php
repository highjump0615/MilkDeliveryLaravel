@extends('naizhan.layout.master')

@section('content')
    @include('naizhan.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('naizhan.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    财务管理
                </li>
                <li>
                    <a href="{{ url('/naizhan/caiwu/taizhang/qitanaizhanzhuanzhang/xianjinzhuanzhangjiru') }}">其他奶站现金转账记录</a>
                </li>
                <li>
                    <a href="{{ url('/naizhan/caiwu/taizhang/qitanaizhanzhuanzhang/zhuanzhangzhangdan') }}">账单列表</a>
                </li>
            </ol>
        </div>

        <div class="row wrapper">
            <div class="wrapper-content">
                <div class="ibox-content">
                    <div class="col-md-2">
                        <a href="{{ url('/naizhan/caiwu/taizhang/qitanaizhanzhuanzhang/zhuanzhangjilu') }}"
                           class="btn btn-success btn-outline" type="button" style="width:100%">查看转账记录</a>
                    </div>
                    <div class="col-md-8"></div>
                    <div class="col-md-2 col-md-offset-7" style="padding-top:5px;">
                        <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
                    </div>
                </div>
                <div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table class="table footable table-bordered">
                            <thead style="background-color:#00cc55;">
                            <tr>
                                <th data-sort-ignore="true">序号</th>
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
                            <?php $i=0;?>
                            @foreach($ncts as $ncs)
                                <?php
                                $j = 0;
                                $first_row_span = count($ncs);
                                ?>
                                @foreach($ncs as $nc)
                                    <?php $j++; ?>
                                    <tr>
                                        @if($j==1)
                                            <td rowspan="{{$first_row_span}}">{{$i+1}}</td>
                                            <td rowspan="{{$first_row_span}}">{{$nc->delivery_station_name}}</td>
                                        @endif
                                        <td class="o_station" data-trsid="{{$nc->id}}"
                                            data-sid="{{$nc->station_id}}">{{$nc->created_at}}</td>
                                        <td>{{$nc->id}}</td>
                                        <td>{{$nc->order_from}} ~ {{$nc->order_to}}</td>
                                        <td>{{$nc->total_amount}}</td>
                                        <td>{{$nc->order_count}}</td>
                                        <td>未转</td>
                                        <td>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?php $i++;?>
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
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
