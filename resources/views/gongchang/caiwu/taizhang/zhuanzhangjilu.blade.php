{{--Show Station Money Transfer--}}
@extends('gongchang.layout.master')
@section('css')
@endsection

@section('content')

    <!-- 奶站需要奶站的菜单 -->
    @if ($is_station)
        @include('naizhan.theme.sidebar')
    @else
        @include('gongchang.theme.sidebar')
    @endif

    <div id="page-wrapper" class="gray-bg dashbard-1">
        <!-- 头部 -->
        @if ($is_station)
            @include('naizhan.theme.header')
        @else
            @include('gongchang.theme.header')
        @endif

        <!-- 面包屑导航 -->
        <div class="row">
            <div class="ibox-content">
                <div class="feed-element">
                    <div class="form-group col-md-5" id="data_range_select">
                        <label class="col-md-3 control-label" style="padding-top:5px;">选择日期:</label>
                        <div class="input-daterange input-group col-md-8" id="datepicker">
                            <input type="text" class="input-sm form-control" name="start" id="filter_start_date"/>
                            <span class="input-group-addon">至</span>
                            <input type="text" class="input-sm form-control" name="end" id="filter_end_date"/>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
                    </div>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-content">

                    <table class="footable table table-bordered" id="order_table" data-page-size="10">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">转账时间</th>
                            <th data-sort-ignore="true">付款方</th>
                            <th data-sort-ignore="true">收款方</th>
                            <th data-sort-ignore="true">账单号</th>
                            <th data-sort-ignore="true">金额</th>
                            <th data-sort-ignore="true">订单数量</th>
                            <th data-sort-ignore="true">账单日期</th>
                            <th data-sort-ignore="true">明细</th>
                            <th data-sort-ignore="true">状态</th>
                            <th data-sort-ignore="true">流水号</th>
                            <th data-sort-ignore="true">实际转账</th>
                            <th data-sort-ignore="true">余额</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($result))
                            @foreach($result as $trspay_id=>$trspay_trs)
                                <?php
                                $transactions = $trspay_trs[1];//transactions included in this transaction pay
                                $stm = $trspay_trs[0];//station money transfer
                                $trs_count = count($transactions);
                                $j = 0;
                                ?>
                                @foreach($transactions as $trs)
                                    <tr data-trstime="{{$stm->created_at}}">
                                        @if($j==0)
                                            <td rowspan="{{$trs_count}}">{{$stm->created_at}}</td>
                                            <td rowspan="{{$trs_count}}">{{$stm->station_name}}</td>
                                            <td rowspan="{{$trs_count}}">{{$stm->delivery_station_name}}</td>
                                        @endif
                                        <td>{{$trs->id}}</td>
                                        <td>{{$trs->total_amount}}</td>
                                        <td>{{$trs->order_count}}</td>
                                        <td>{{$trs->order_from}} ~ {{$trs->order_to}}</td>
                                        <td>
                                            @if ($is_station)
                                                <a href="{{URL::to('/naizhan/caiwu/taizhang/qitanaizhanzhuanzhang/zhangdanmingxi/'.$trs->id)}}">查看明细</a>
                                            @else
                                                <a href="{{URL::to('/gongchang/caiwu/taizhang/qitanaizhanzhuanzhang/zhangdanmingxi/'.$trs->id)}}">查看明细</a>
                                            @endif
                                        </td>
                                        <td>已转</td>
                                        {{--show transaction pay id, total real input amount, remain amount--}}
                                        @if($j==0)
                                            <td rowspan="{{$trs_count}}">{{$stm->transaction_pay_id}}</td>
                                            <td rowspan="{{$trs_count}}">{{$stm->amount}}</td>
                                            <td rowspan="{{$trs_count}}">{{$stm->remaining}}</td>
                                        @endif
                                    </tr>
                                    <?php
                                    $j++;
                                    ?>
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
                    <table class="footable table table-bordered" id="filter_table" data-page-size="10">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">转账时间</th>
                            <th data-sort-ignore="true">付款方</th>
                            <th data-sort-ignore="true">收款方</th>
                            <th data-sort-ignore="true">账单号</th>
                            <th data-sort-ignore="true">金额</th>
                            <th data-sort-ignore="true">订单数量</th>
                            <th data-sort-ignore="true">账单日期</th>
                            <th data-sort-ignore="true">明细</th>
                            <th data-sort-ignore="true">状态</th>
                            <th data-sort-ignore="true">流水号</th>
                            <th data-sort-ignore="true">实际转账</th>
                            <th data-sort-ignore="true">余额</th>
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

        </div>
    </div>
@endsection

@section('script')
    <script src="<?=asset('js/pages/gongchang/zhuanzhangjilu.js') ?>"></script>
@endsection