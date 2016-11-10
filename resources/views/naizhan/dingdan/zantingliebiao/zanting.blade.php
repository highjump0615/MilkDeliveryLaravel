@extends('naizhan.layout.master')

@section('content')
    @include('naizhan.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('naizhan.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li class="active">
                    <a href="{{ url('naizhan/dingdan')}}">订单管理</a>
                </li>
                <li class="active">
                    <strong>暂停订单</strong>
                </li>
            </ol>
        </div>

        @if(isset($mine) && $mine)
            <div class="row white-bg pad-wrapper">

                <div class=" col-md-12 gray-bg">
                    <label class="col-md-12">客户信息</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">订单性质：</label>
                    <label>{{$order->order_property_name}}</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">收货人:</label>
                    <label>{{$order->customer_name}}</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">电话:</label>
                    <label>{{$order->phone}}</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="col-md-2">收货地址：</label>
                    <label>{{$order->address}}</label>
                </div>

                <div class=" col-md-12 gray-bg">
                    <label class="col-md-12">奶站信息</label>
                </div>

                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">奶站:</label>
                    <label>{{$order->delivery_station_name}}</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">配送员：</label>
                    <label>{{$order->milkman_name}}</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">征订员：</label>
                    <label>{{$order->order_checker_name}}</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">票据号:</label>
                    <label>{{$order->receipt_number}}</label>
                </div>
                <div class="feed-element col-md-12">
                    <div class="col-md-offset-2">
                        <img id="ticket" src="<?=asset('img/order/' . $order->receipt_path)?>" class="img-responsive"/>
                    </div>
                </div>
                <div class="col-md-12 gray-bg">
                    <label class="col-md-12">订单内容</label>
                </div>

                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">暂停日期：</label>
                    <label class="col-md-2">{{$order->stop_at}} ~ {{$order->restart_at}}</label>
                    <div class="col-md-2">
                        <button class="btn btn-success" data-toggle="modal" data-target="#restart_modal">开始</button>
                    </div>
                </div>
                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">奶箱安装：</label>
                    <label>{{$order->milk_box_install_label}}</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">支付方式：</label>
                    <label>{{$order->payment_type_name}}</label>
                </div>
                <div class="col-md-12">
                    <div class="ibox-content">
                        <table class="footable table table-bordered" data-sort-ignore="true" data-page-size="10" data-limit-navigation="5">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">奶品</th>
                                <th data-sort-ignore="true">订单类型</th>
                                <th data-sort-ignore="true">数量</th>
                                <th data-sort-ignore="true">剩余量</th>
                                <th data-sort-ignore="true">单数</th>
                                <th data-sort-ignore="true">瓶/次</th>
                                <th data-sort-ignore="true">配送规则</th>
                                <th data-sort-ignore="true">配送日期</th>
                                <th data-sort-ignore="true">订单余额</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($order_products))
                                @for ($i =0; $i< count($order_products); $i++)
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td>{{$order_products[$i]->product_name}}</td>
                                        <td>{{$order_products[$i]->order_type_name}}</td>
                                        <td>{{$order_products[$i]->total_count}}</td>
                                        <td>{{$order_products[$i]->remain_count}}</td>
                                        <td>{{$order_products[$i]->avg}}</td>
                                        <td>{{$order_products[$i]->count_per_day}}</td>
                                        <td>{{$order_products[$i]->delivery_type_name}}</td>
                                        <td>
                                            @if($order_products[$i]->delivery_type != \App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY && $order_products[$i]->delivery_type != \App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EVERY_DAY  )
                                                <button class="btn btn-outline">查看日历</button>
                                            @endif
                                        </td>
                                        <td>{{$order_products[$i]->total_amount}}</td>
                                    </tr>
                                @endfor
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
                    </div>
                </div>
                &nbsp;
                <div class=" col-md-12 gray-bg">
                    <label class="col-md-12">配送明细</label>
                </div>
                <div class="ibox-content">
                    <table class="footable table table-bordered" data-page-size="10" data-limit-navigation="5">
                        <thead>
                        <tr>
                           <th data-sort-ignore="true">序号</th>
                           <th data-sort-ignore="true">配送时间</th>
                           <th data-sort-ignore="true">奶品</th>
                           <th data-sort-ignore="true">数量</th>
                           <th data-sort-ignore="true">状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($grouped_delivery_plans))
                            @for($i=0; $i < count($grouped_delivery_plans); $i++)
                                <tr>
                                    <td>{{$i+1}}</td>
                                    <td>{{$grouped_delivery_plans[$i]->deliver_at}}</td>
                                    <td>{{$grouped_delivery_plans[$i]->product_name}}</td>
                                    @if($grouped_delivery_plans[$i]->status == \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
                                        {{--*/ $price = $grouped_delivery_plans[$i]->delivered_count * $grouped_delivery_plans[$i]->price; /*--}}
                                        <td>{{$grouped_delivery_plans[$i]->delivered_count}} (余 {{$price}}）</td>
                                    @else
                                        {{--*/ $price = $grouped_delivery_plans[$i]->delivery_count * $grouped_delivery_plans[$i]->price; /*--}}
                                        <td>{{$grouped_delivery_plans[$i]->delivery_count}} (余{{$price}})</td>
                                    @endif

                                    @if($grouped_delivery_plans[$i]->status == \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_WAITING)
                                        <td>待审核</td>
                                    @elseif($grouped_delivery_plans[$i]->status == \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_PASSED)
                                        <td>通过</td>
                                    @elseif($grouped_delivery_plans[$i]->status == \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_SENT)
                                        <td>在配送</td>
                                    @elseif($grouped_delivery_plans[$i]->status == \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED)
                                        <td>已送</td>
                                    @else
                                    @endif
                                </tr>
                            @endfor
                        @else
                        @endif

                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="6">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @else
            <div class="row white-bg pas-wrapper">
                <div class="ibox-content" style="margin-left: 50px;">
                    <h1>您只能看到您的订单。</h1>
                </div>
            </div>
        @endif
    </div>
@endsection
@section('script')
    <script type="text/javascript">
    </script>
@endsection