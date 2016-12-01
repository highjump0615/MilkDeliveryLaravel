@extends('naizhan.layout.master')
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
    </style>

@endsection
@section('content')
    @include('naizhan.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('naizhan.theme.header')
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

            <div class="col-md-12" style="padding-top: 50px;">
                @if (isset($station))
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
                                    <input class="gray-bg text-center" readonly
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
                                    <a href="{{URL::to('/naizhan/caiwu/taizhang/benzhandingdan/')}}"
                                       class="btn btn-success">查看订单金额统计</a>
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
                                    <a href="{{URL::to('/naizhan/caiwu/taizhang/zhanghuyue/')}}"
                                       class="btn btn-success">查看详情</a>
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
                                    <a href="{{URL::to('/naizhan/caiwu/ziyingzhanghujiru/')}}"
                                       class="btn btn-success">查看自营账户</a>
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
                                    <th data-sort-ignore="true">订单数量</th>
                                    <th data-sort-ignore="true">金额</th>
                                    <th data-sort-ignore="true">配送数量</th>
                                    <th data-sort-ignore="true">金额</th>
                                    <th data-sort-ignore="true">订单剩余数量（瓶）</th>
                                    <th data-sort-ignore="true">金额</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{$station->bottle_count_before_this_term}}</td>
                                    <td>{{$station->term_start_amount}}</td>
                                    <td>{{$station->bottle_count_increased_this_term}}</td>
                                    <td>{{$station->order_amount_increased_this_term}}</td>
                                    <td>{{$station->bottle_count_done_this_term}}</td>
                                    <td>{{$station->order_amount_done_this_term}}</td>
                                    <td>{{$station->bottle_count_before_this_term + $station->bottle_count_increased_this_term - $station->bottle_count_done_this_term}}</td>
                                    <td>{{$station->term_start_amount + $station->order_amount_done_this_term}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
    </script>
@endsection