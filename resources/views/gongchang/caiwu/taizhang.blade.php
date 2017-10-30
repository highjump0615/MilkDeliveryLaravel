@extends('gongchang.layout.master')
@section('css')
    <style>
        select {
            height: 35px;
        }

        .station {
            border: 1px solid black;
        }

        .station_balance {
            font-size: 15px;
            color: #ff0000;
        }

        .station_body, .station_table {
            margin-top: 20px;
            overflow: auto;
        }

        .station_body label {
            padding: 5px;
            width: 100%;
        }

        .station_body a {
            width: 100%;
        }

        .operation a {
            width: 100%;
        }

    </style>
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
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">财务管理</a>
                </li>
                <li>
                    <a href=""><strong>奶站账户台账</strong></a>
                </li>
            </ol>
        </div>

        <div class="row">

            <!-- 奶厂操作 -->
            @if (!$is_station)
            <div class="ibox-content col-md-12 operation">
                <div class="col-md-2">
                    <a id="add-odder" data-toggle="modal" href="#insert_order" class="btn btn-sm btn-success" type="button">奶站现金订单收款</a>
                </div>
                &nbsp;
                <div class="col-md-2">
                    <a href="{{URL::to('/gongchang/caiwu/taizhang/qitanaizhanzhuanzhang/')}}"
                       class="btn btn-sm btn-success" type="button">其他奶站转账</a>
                </div>
                &nbsp;
                <div class="col-md-2">
                    <a href="{{URL::to('/gongchang/caiwu/taizhang/naikakuanzhuanzhang')}}"
                       class="btn btn-sm btn-success" type="button">奶卡款转账</a>
                </div>
                &nbsp;
                <div class="col-md-2">
                    <a id="add-self" data-toggle="modal" href="#insert_order" class="btn btn-sm btn-success" type="button">自营收款</a>
                </div>
            </div>
            @endif

            <div class="ibox-content col-md-12">
                <!-- 奶厂操作 -->
                <form class="form-horizontal no-vert-padding" method="get" id="form_filter">
                <div class="feed-element">
                    <div class="col-md-4" id="data_range_select">
                        <label class="label-control" style="padding-top:5px;">日期:</label>
                        <div class="input-daterange input-group col-md-8" id="datepicker">
                            <input type="text" class="input-sm form-control" name="start_date" value="{{$start_date}}"/>
                            <span class="input-group-addon">至</span>
                            <input type="text" class="input-sm form-control" name="end_date" value="{{$end_date}}"/>
                        </div>
                    </div>

                    @if (!$is_station)
                    <label class="col-md-1 text-right" style="padding-top: 5px;">奶站名称:</label>
                    <div class="col-md-2">
                        <select data-placeholder="Choose..." class="choose_station" name="station" style="width: 100%;">
                            <option value=""></option>
                            @if (isset($stations))
                                @foreach($stations as $st)
                                    <option value="{{$st->id}}"
                                            @if ($station->id == $st->id) selected @endif>
                                        {{$st->name}}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    @endif

                    <div class="col-md-2 pull-right">
                        <button class="btn btn-success btn-outline" type="submit" data-action="show_selected">筛选
                        </button>
                        <!--<button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>-->
                    </div>
                </div>
                </form>
            </div>

            <div class="col-md-12" id="station_list">
                <div class="ibox-content station" data-sid="{{$station->id}}">
                    <div class="station_head">
                        <label class="station_name" style="font-size:20px;">{{$station->name}}</label>
                        &emsp;
                        <label class="station_balance">配送业务信用额度：{{$station->init_delivery_credit_amount}}</label>
                        &emsp;
                        <label class="station_balance">自营信用额度：{{$station->init_business_credit_amount}}</label>
                    </div>
                    <div class="station_body">

                        <div class="col-md-12">
                            <div class="col-sm-2">
                                <label class="text-left">本站现金订单应收余额</label>
                            </div>
                            <div class="col-sm-2">
                                <input class="gray-bg text-center receivable_order_money" readonly
                                       value="{{$station->receivable_order_money}}">
                            </div>
                            <div class="col-sm-2 text-right">
                                <label class="">信用余额</label>
                            </div>
                            <div class="col-sm-2">
                                <input readonly class="gray-bg text-center delivery_credit_balance"
                                       value="{{$station->init_delivery_credit_amount+$station->delivery_credit_balance}}"/>
                            </div>
                            <div class="col-sm-2 col-sm-offset-2">
                                @if ($is_station)
                                    <a href="{{URL::to('/naizhan/caiwu/taizhang/benzhandingdan/')}}"
                                       class="btn btn-sm btn-success">查看订单金额统计</a>
                                @else
                                    <a href="{{URL::to('/gongchang/caiwu/taizhang/naizhandingdanjinetongji/'.$station->id)}}"
                                       class="btn btn-sm btn-success">查看订单金额统计</a>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="col-sm-2">
                                <label class="text-left">奶站账户期末余额</label>
                            </div>
                            <div class="col-sm-2">
                                <input readonly class="gray-bg text-center credit_balance"
                                       value="{{$station->calculation_balance}}"/>
                            </div>
                            <div class="col-sm-1 col-sm-offset-1">
                            </div>
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-2 col-sm-offset-2">
                                @if ($is_station)
                                    <a href="{{URL::to('/naizhan/caiwu/taizhang/zhanghuyue/')}}"
                                       class="btn btn-success btn-sm">查看详情</a>
                                @else
                                    <a href="{{URL::to('/gongchang/caiwu/naizhanzhanghuyue/'.$station->id)}}"
                                       class="btn btn-success btn-sm">查看详情</a>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="col-sm-2">
                                <label class="text-left">自营账户期末余额</label>
                            </div>
                            <div class="col-sm-2">
                                <input readonly class="gray-bg text-center"
                                       value="{{$station->business_credit_balance}}">
                            </div>
                            <div class="col-sm-2 text-right">
                                <label class="">信用余额</label>
                            </div>
                            <div class="col-sm-2">
                                <input readonly class="gray-bg text-center"
                                       value="{{$station->business_credit_balance+$station->init_business_credit_amount}}"/>
                            </div>
                            <div class="col-sm-2 col-sm-offset-2">
                                @if ($is_station)
                                    <a href="{{URL::to('/naizhan/caiwu/ziyingzhanghujiru/')}}"
                                       class="btn btn-success btn-sm">查看自营账户</a>
                                @else
                                    <a href="{{URL::to('/gongchang/caiwu/ziyingzhanghu/'.$station->id)}}"
                                       class="btn btn-success btn-sm">查看自营账户</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="station_table">
                        <table class="footable table table-bordered" data-page-size="10">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true" colspan="2">期初余额</th>
                                <th data-sort-ignore="true" colspan="2">本期订单金额增加</th>
                                <th data-sort-ignore="true" colspan="2">本期完成订单余额（减项）</th>
                                <th data-sort-ignore="true" colspan="2">期末金额</th>
                            </tr>
                            <tr>
                                <th data-sort-ignore="true">订单剩余数量（瓶）</th>
                                <th data-sort-ignore="true">金额</th>
                                <th data-sort-ignore="true">订单数量（瓶）</th>
                                <th data-sort-ignore="true">金额</th>
                                <th data-sort-ignore="true">配送数量（瓶）</th>
                                <th data-sort-ignore="true">金额</th>
                                <th data-sort-ignore="true">订单剩余数量（瓶）</th>
                                <th data-sort-ignore="true">金额</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{$station->fin_before_count}}</td>
                                <td>{{$station->fin_before_cost}}</td>
                                <td>{{$station->fin_added_count}}</td>
                                <td>{{$station->fin_added_cost}}</td>
                                <td>{{$station->fin_done_count}}</td>
                                <td>{{$station->fin_done_cost}}</td>
                                <td>{{$station->fin_after_count}}</td>
                                <td>{{$station->fin_after_cost}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id="insert_order" class="modal fade" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="insert_order_receipt_form">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="feed-element col-md-12">
                                        <label class="col-md-3" style="padding-top:5px;">选择奶站:</label>
                                        <div class="col-md-9">
                                            <select required data-placeholder="Choose..." class="chosen-select"
                                                    style="width:100%;" name="station_id">
                                                @if (isset($stations))
                                                    @foreach($stations as $station)
                                                        <option value="{{$station->id}}">{{$station->name}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="col-md-3">金额:</label>
                                        <div class="col-md-9">
                                            <input type="number" step="0.01" min="0" required name="amount"
                                                   style="width:100%;">
                                        </div>
                                    </div>
                                    <div class="feed-element col-md-12">
                                        <label class="col-md-3">流水号:</label>
                                        <div class="col-md-9">
                                            <input type="text" name="receipt_number" required style="width:100%;">
                                        </div>
                                    </div>
                                    <div class="feed-element col-md-12">
                                        <label class="col-md-3">备注:</label>
                                        <div class="col-md-9">
                                            <textarea rows="3" cols="40" name="comment"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-white">确定</button>
                                <button type="button" class="btn btn-white" data-dismiss="modal">取消 | 退出</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')

    <script src="<?=asset('js/pages/gongchang/taizhang.js') ?>"></script>

@endsection