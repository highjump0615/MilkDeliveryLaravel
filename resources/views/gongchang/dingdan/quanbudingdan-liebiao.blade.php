@extends('gongchang.layout.master')
@section('css')
    <link href="<?=asset('css/pages/order_list.css') ?>" rel="stylesheet">
@endsection
@section('content')
    @include('gongchang.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('gongchang.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">订单管理</a>
                </li>
                <li>
                    <a href=""><strong>全部订单-列表</strong></a>
                </li>
            </ol>
        </div>

        <div class="row wrapper white-bg">
            <div class="wrapper-content">
                <div class="feed-element">
                    <a class="btn btn-success" href="{{URL::to('/gongchang/dingdan/quanbudingdan-liebiao/week_show')}}">本周</a>
                    <a class="btn btn-success"
                       href="{{URL::to('/gongchang/dingdan/quanbudingdan-liebiao/month_show')}}">本月</a>
                </div>

                <div class="row border-bottom">
                    <div class="col-md-4">
                        <div style="background-color:#5badd7;">
                            <label style="text-align:center; width:100%; color: black; padding:10px;">微信订单</label>
                            @if(isset($wechat_amount) && isset($wechat_dcount))
                                <label style="text-align:center; width:100%; color: white; font-size: 25px; padding:10px;">{{$wechat_dcount}}
                                    (单) &emsp; ￥{{$wechat_amount}}</label>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="background-color:#5badd7;">
                            <label style="text-align:center; width:100%; color: black; padding:10px;">现金订单</label>
                            @if(isset($money_amount) && isset($money_dcount))
                                <label style="text-align:center; width:100%; color: white; font-size: 25px; padding:10px;">{{$money_dcount}}
                                    (单) &emsp; ￥{{$money_amount}}</label>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div style="background-color:#5badd7;">
                            <label style="text-align:center; width:100%; color: black; padding:10px;">奶卡订单</label>
                            @if(isset($card_amount) && isset($card_dcount))
                                <label style="text-align:center; width:100%; color: white; font-size: 25px; padding:10px;">{{$card_dcount}}
                                    (单) &emsp; ￥{{$card_amount}}</label>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <hr>
                </div>

                <!-- 筛选选择项 -->
                @include('gongchang.dingdan.orderlistfilter', [
                    'showState' => true,
                    'showEndDate' => true,
                ])

                <div class="feed-element">
                    <div class="col-md-4">
                        <a href="{{URL::to('/gongchang/dingdan/dingdanluru')}}"
                           class="btn btn-success col-md-4" type="button">订单录入</a>
                    </div>
                    <div class="col-md-3 col-md-offset-5 button-div">
                        <a href="{{url('/api/order/export')}}" class="btn btn-success btn-outline">导出</a>
                        <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
                        <button class="btn btn-success" type="button" data-action="show_selected">筛选</button>
                    </div>
                </div>
                <div class="ibox float-e-margins white-bg">
                    <div class="ibox-content">

                        <table class="table table-bordered" id="order_table" >
                        <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">订单号</th>
                                <th data-sort-ignore="true">收货人</th>
                                <th data-sort-ignore="true">电话</th>
                                <th data-sort-ignore="true">地址</th>
                                <th data-sort-ignore="true">订单类型</th>
                                <th data-sort-ignore="true">订单金额</th>
                                <th data-sort-ignore="true">下单日期</th>
                                <th data-sort-ignore="true">到期日期</th>
                                <th data-sort-ignore="true">支付</th>
                                <th data-sort-ignore="true">订单性质</th>
                                <th data-sort-ignore="true">征订员</th>
                                <th data-sort-ignore="true">奶站</th>
                                <th data-sort-ignore="true">配送员</th>
                                <th class="o_receipt" data-sort-ignore="true">票据号</th>
                                <th data-sort-ignore="true">状态</th>
                                <th data-sort-ignore="true">备注</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (isset($orders))
                                @for($i =0; $i<count($orders); $i++)
                                    <tr data-orderid="{{$orders[$i]->id}}" data-status="{{$orders[$i]->status}}" class="row-hover-light-blue">
                                        <td>{{$i + $orders->firstItem()}}</td>
                                        <td class="o_number">{{$orders[$i]->number}}</td>
                                        <td class="o_customer_name">{{$orders[$i]->customer_name}}</td>
                                        <td class="o_phone">{{$orders[$i]->phone}}</td>
                                        <td class="o_addr align-left">{{$orders[$i]->addresses}}</td>
                                        <td class="o_type">{{$orders[$i]->all_order_types}}</td>
                                        <td class="o_total">{{$orders[$i]->total_amount}}</td>
                                        <td class="o_ordered">{{$orders[$i]->ordered_at}}</td>
                                        <td class="o_end">{{$orders[$i]->order_end_date}}</td>
                                        <td class="o_paytype">{{$orders[$i]->payment_type_name}}</td>
                                        <td class="o_property">{{$orders[$i]->getOrderPropertyName()}}</td>
                                        <td class="o_checker">{{$orders[$i]->getCheckerName()}}</td>
                                        <td class="o_station">{{$orders[$i]->delivery_station_name}}</td>
                                        <td class="milkman">
                                            @if($orders[$i]->milkman)
                                                {{$orders[$i]->milkman->name}} {{$orders[$i]->milkman->phone}}
                                            @endif
                                        </td>
                                        <td class="o_receipt">{{$orders[$i]->receipt_number}}</td>
                                        <td class="o_status" data-status="{{$orders[$i]->status}}" data-value="{{$orders[$i]->status_name}}" style="width: 70px;">
                                            {{$orders[$i]->status_name}}</td>
                                        <td>{{$orders[$i]->comment}}</td>
                                    </tr>
                            @endfor
                            @endif
                            </tbody>
                        </table>

                        <ul id="pagination_data" class="pagination-sm pull-right"></ul>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- script -->
@include('gongchang.dingdan.orderscript', [
    'pageName' => 'quanbu',
])