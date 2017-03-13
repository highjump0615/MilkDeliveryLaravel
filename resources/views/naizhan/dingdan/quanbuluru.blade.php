@extends('naizhan.layout.master')

@section('content')
    @include('naizhan.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('naizhan.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg">
                <li class="active">
                    <a href="{{ url('naizhan/dingdan')}}">订单管理</a>
                </li>
                <li class="active">
                    <strong>全部订单</strong>
                </li>
            </ol>
        </div>

        <div class="row wrapper">
            <div class="wrapper-content">

                <!-- 筛选选择项 -->
                @include('gongchang.dingdan.orderlistfilter', [
                    'showState' => true,
                    'showEndDate' => false,
                ])

                <div class="feed-element">
                    <div class="col-md-4">
                        <a href="{{URL::to('/naizhan/dingdan/dingdanluru')}}"
                           class="btn btn-success col-md-4" type="button">订单录入</a>
                    </div>
                    <div class="col-md-2 col-md-offset-6 button-div">
                        <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选
                        </button>
                    </div>
                </div>

                <div class="ibox float-e-margins white-bg">
                    <div class="ibox-content">

                        <table id="order_table" class="table table-bordered footable" data-sort-ignore="true"
                               data-page-size="10">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">订单号</th>
                                <th data-sort-ignore="true">收货人</th>
                                <th data-sort-ignore="true">电话</th>
                                <th data-sort-ignore="true">地址</th>
                                <th data-sort-ignore="true">订单类型</th>
                                <th data-sort-ignore="true">订单金额</th>
                                <th data-sort-ignore="true">征订员</th>
                                <th data-sort-ignore="true">区域</th>
                                <th data-sort-ignore="true">分区</th>
                                <th data-sort-ignore="true">奶站</th>
                                <th data-sort-ignore="true">配送员</th>
                                <th data-sort-ignore="true">下单日期</th>
                                <th data-sort-ignore="true">支付</th>
                                <th data-sort-ignore="true">剩余金额</th>
                                <th data-sort-ignore="true">订单性质</th>
                                <th data-sort-ignore="true">状态</th>
                                <th data-sort-ignore="true">备注</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (isset($orders))
                                @for($i =0; $i<count($orders); $i++)
                                    <tr data-orderid="{{$orders[$i]->id}}" class="row-hover-light-blue">
                                        <td>{{$i+1}}</td>
                                        <td class="o_number">{{$orders[$i]->number}}</td>
                                        <td class="o_customer_name">{{$orders[$i]->customer_name}}</td>
                                        <td class="o_phone">{{$orders[$i]->phone}}</td>
                                        <td class="o_addr">{{$orders[$i]->addresses}}</td>
                                        <td class="o_type">{{$orders[$i]->all_order_types}}</td>
                                        <td class="o_total">{{$orders[$i]->total_amount}}</td>
                                        <td class="o_checker">{{$orders[$i]->order_checker_name}}</td>
                                        <td class="o_city">{{$orders[$i]->city_name}}</td>
                                        <td class="o_street">{{$orders[$i]->district_name}}</td>
                                        <td class="o_station">{{$orders[$i]->delivery_station_name}}</td>
                                        <td class="milkman">{{$orders[$i]->milkman_name}}</td>
                                        <td class="o_ordered">{{$orders[$i]->ordered_at}}</td>
                                        <td class="o_paytype">{{$orders[$i]->payment_type_name}}</td>
                                        <td class="o_remain">{{$orders[$i]->remaining_amount}}
                                        <td class="o_property">{{$orders[$i]->order_property_name}}</td>
                                        <td class="o_status" data-status="{{$orders[$i]->status}}"
                                            data-value="{{$orders[$i]->status_name}}" style="width: 70px;">
                                            {{$orders[$i]->status_name}}</td>
                                        <td>{{$orders[$i]->comment}}</td>
                                    </tr>
                                @endfor
                            @endif
                            </tbody>
                            <tfoot align="right">
                            <tr>
                                <td colspan="100%">
                                    <ul class="pagination pull-right"></ul>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                        <table id="filter_table" class="table table-bordered footable" data-sort-ignore="true"
                               data-page-size="10">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">订单号</th>
                                <th data-sort-ignore="true">收货人</th>
                                <th data-sort-ignore="true">电话</th>
                                <th data-sort-ignore="true">地址</th>
                                <th data-sort-ignore="true">订单类型</th>
                                <th data-sort-ignore="true">订单金额</th>
                                <th data-sort-ignore="true">征订员</th>
                                <th data-sort-ignore="true">区域</th>
                                <th data-sort-ignore="true">分区</th>
                                <th data-sort-ignore="true">奶站</th>
                                <th data-sort-ignore="true">配送员</th>
                                <th data-sort-ignore="true">下单日期</th>
                                <th data-sort-ignore="true">支付</th>
                                <th data-sort-ignore="true">剩余金额</th>
                                <th data-sort-ignore="true">订单性质</th>
                                <th data-sort-ignore="true">状态</th>
                                <th data-sort-ignore="true">备 注</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot align="right">
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
@section('script')
    <script src="<?=asset('js/pages/gongchang/order_list_filter.js') ?>"></script>
    <script type="text/javascript" src="<?=asset('js/pages/naizhan/order_select_export_print.js')?>"></script>
@endsection