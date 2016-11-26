@extends('gongchang.layout.master')
@section('css')
    <style>
        .plan_count {
            display: inline-block;
            max-width: 150px;
            padding-left: 5px;
            border: none;
        }

        .btn {
            margin: 0 8px;
        }
        .calendar_show {
            width: 0px;
            margin: 0;
            top: -30px;
            left: 70px;
            position: relative;
            visibility: hidden;
            height: 0;
        }

    </style>
@endsection
@section('content')

<!-- 奶站需要奶站的菜单 -->
@if (isset($station))
    @include('naizhan.theme.sidebar')
@else
    @include('gongchang.theme.sidebar')
@endif

    <div id="page-wrapper" class="white-bg dashbard-1">

        <!-- 头部 -->
        @if (isset($station))
            @include('naizhan.theme.header')
        @else
            @include('gongchang.theme.header')
        @endif

        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>订单管理</li>
                <li class="active">
                    <strong>订单详情</strong>
                </li>
            </ol>
        </div>

        <br>

        <div class="row white-bg pas-wrapper">
            <div class="col-md-12 gray-bg">
                <label class="col-sm-10">订单信息</label>
            </div>
            <div class="feed-element col-md-12">
                <input type="hidden" id="order_status" value="{{$order->status}}"/>
                <input type="hidden" id="order_id" value="{{$order->id}}"/>
                <div class="col-md-4" style="padding-left: 0px;">
                    <label class="col-md-6">订单号:</label>
                    <label class="col-md-6">{{$order->number}}</label>
                    <label class="col-md-6">订单状态:</label>
                    <label class="col-md-6">{{$order->status_name}}</label>

                </div>
                <div class="feed-element col-md-5 col-md-offset-3">

                    @if ($order->status == \App\Model\OrderModel\Order::ORDER_WAITING_STATUS ||
                        $order->status == \App\Model\OrderModel\Order::ORDER_NEW_WAITING_STATUS ||
                        $order->status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS ||
                        $order->status == \App\Model\OrderModel\Order::ORDER_NOT_PASSED_STATUS ||
                        $order->status == \App\Model\OrderModel\Order::ORDER_NEW_NOT_PASSED_STATUS)

                        @if (isset($station))
                            <a href="{{URL::to('/naizhan/dingdan/xiugai/'.$order->id)}}"
                               class="btn btn-success btn-outline btn-md col-md-2"><i class="fa fa-pencil"></i>修改</a>
                        @else
                            <a href="{{URL::to('/gongchang/dingdan/dingdanxiugai/'.$order->id)}}"
                               class="btn btn-success btn-outline btn-md col-md-2"><i class="fa fa-pencil"></i>修改</a>
                        @endif
                    @endif

                    @if($order->status == \App\Model\OrderModel\Order::ORDER_PASSED_STATUS || $order->status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS)
                        <button class="btn btn-success btn-outline btn-md  col-md-3"
                                data-orderid="{{$order->id}}" data-toggle="modal"
                                data-target="#stop_order_modal"
                                id="stop_order_bt">暂停订单
                        </button>
                    @endif

                    @if($order->status == \App\Model\OrderModel\Order::ORDER_STOPPED_STATUS)
                        <button class="btn btn-success btn-outline btn-md  col-md-3"
                                data-orderid="{{$order->id}}"
                                data-stop-at="{{$order->stop_at}}"
                                data-restart-at="{{$order->restart_at}}"
                                data-target="#restart_order_modal"
                                data-toggle="modal"
                                id="restart_order_bt">开始订单
                        </button>
                    @endif

                    @if ($order->status == \App\Model\OrderModel\Order::ORDER_PASSED_STATUS || $order->status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS)
                        <button class="btn btn-success btn-outline btn-md  col-md-3"
                                data-orderid="{{$order->id}}"
                                id="postpone_order_bt">顺延订单
                        </button>
                    @endif

                    @if ($order->status == \App\Model\OrderModel\Order::ORDER_PASSED_STATUS || $order->status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS)
                        <button class="btn btn-success btn-outline btn-md  col-md-2"
                                data-orderid="{{$order->id}}"
                                id="cancel_order_bt">退订
                        </button>
                    @endif

                </div>
            </div>
            <div class="col-md-12">
                @if($order->isAvailable())
                    <label class="col-md-2">起送日期:</label>
                    <label class="col-md-10">{{$order->start_at}}</label>
                @endif
                @if($order->has_stopped)
                    <label class="col-md-2">暂停日期:</label>
                    <label class="col-md-2">{{$order->stop_at}} ~ {{$order->order_stop_end_date}}</label>
                @endif
            </div>
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
                    @if($order->receipt_path !="")
                        <img id="ticket" src="<?=asset('img/order/' . $order->receipt_path)?>"
                             class="img-responsive"/>
                    @endif
                </div>
            </div>
            <div class="col-md-12 gray-bg">
                <label class="col-md-12">订单内容</label>
            </div>


            <div class="feed-element col-md-12">
                <label class="control-label col-md-2">起送日期：</label>
                <label>{{$order->start_at}}</label>
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
                    <table id="product_table" class="table table-bordered">
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
                            <th data-sort-ignore="true">起送日期</th>
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
                                    <td class="order_product_count_per">{{$order_products[$i]->count_per_day}}</td>
                                    <td>
                                        <button class="btn btn-outline show_delivery_date" data-type="{{$order_products[$i]->delivery_type}}">
                                            {{$order_products[$i]->delivery_type_name}}
                                        </button>

                                        <!-- 日期 -->
                                        @if ($order_products[$i]->delivery_type != \App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY &&
                                            $order_products[$i]->delivery_type != \App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EVERY_DAY)
                                        <div class="calendar_show show_only">
                                            <div class="input-group date picker">
                                                <input type="text" class="form-control delivery_dates" name="delivery_dates[]"
                                                       value="{{$order_products[$i]->custom_order_dates}}" />
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                        </div>
                                        @endif
                                    </td>
                                    <td>{{$order_products[$i]->start_at}}</td>
                                    <td>{{$order_products[$i]->total_amount}}</td>
                                </tr>
                            @endfor
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            &nbsp;
            <div class=" col-md-12 gray-bg">
                <label class="col-md-12">配送明细</label>
            </div>

            <div class="ibox-content">
                <table class="footable table table-bordered" data-page-size="10">
                    <thead>
                    <tr>
                        <th data-sort-ignore="true">序号</th>
                        <th data-sort-ignore="true">配送时间</th>
                        <th data-sort-ignore="true">奶品</th>
                        <th data-sort-ignore="true">数量</th>
                        <th data-sort-ignore="true">状态</th>
                        <!-- 正常状态才允许单日修改 -->
                        @if ($order->isAvailable())
                        <th data-sort-ignore="true">操作</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($grouped_plans_per_product))
                        <?php $i = 0;?>
                        @foreach($grouped_plans_per_product as $gpp)
                            @if(! ( date($gpp['time']) >= date($order->stop_at)  &&  date($gpp['time'])<=date($order->order_stop_end_date)) )
                                <tr data-planid="{{$gpp['plan_id']}}">
                                    <td>{{$i+1}}</td>
                                    <td>{{$gpp['time']}}</td>
                                    <td>{{$gpp['product_name']}}</td>
                                    @if($gpp['status'] == \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_STATUS_FINNISHED )
                                        <td>{{$gpp['count']}} (余 {{$gpp['remain']}}）</td>
                                    @else
                                        <td>
                                            <input type="number" min="0" class="plan_count"
                                                   origin_plan_count="{{$gpp['count']}}"
                                                   value="{{$gpp['count']}}"/>(余{{$gpp['remain']}})
                                        </td>
                                    @endif
                                    <td>{{$gpp['status_name']}}</td>

                                    <!-- 正常状态才允许单日修改 -->
                                    @if ($order->isAvailable())
                                    <td>
                                        @if($gpp['can_edit'])
                                            <button type="button" class="btn btn-success xiugai_plan_bt"
                                                    disabled="disabled">修改
                                            </button>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                <?php $i++; ?>
                            @endif
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
            </div>
        </div>

        <div id="stop_order_modal" class="animated modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <form id="stop_order_modal_form" method="POST" style="padding:0;">
                        <div class="modal-body">
                            <label style="margin-bottom: 30px;">选择暂停的日期</label>
                            <div class="input-daterange input-group col-md-12" id="datepicker">
                                <input type="text" required class="input-sm form-control"
                                       name="start" id="stop_start"/>
                                <span class="input-group-addon">至</span>
                                <input type="text" id="stop_end" required class="input-sm form-control"
                                       name="end"/>
                            </div>
                            <input type="hidden" name="order_id" value="{{$order->id}}"/>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-white">确定</button>
                            <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="restart_order_modal" class="animated modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <form id="restart_order_modal_form" method="POST" style="padding:0;">
                        <div class="modal-body">
                            <label class="align-center btn-block">暂停的日期</label>
                            <label class="align-center  btn-block" id="stop_period"></label>
                            <hr>
                            <label class="align-center  btn-block" style="margin-top: 30px;">选择开启的日期</label>
                            <div class="input-group date single_date">
                                <input required type="text" class="form-control" id="start_at"
                                       name="start_at"><span
                                        class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                            <input type="hidden" name="order_id" id="restart_order_id" value=""/>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-white">确定</button>
                            <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="<?=asset('js/pages/gongchang/order_xiangqing.js') ?>"></script>

    <script type="text/javascript">

        var gbIsStation = false;
        // 是否奶站录入
        @if (isset($station))
            gbIsStation = true;
        @endif


        //set calendar start date limit for various status

        var firstday = startofweek();
        var lastday = endofweek();

        // 解析当前服务器的时间 (2014-08-12 09:25:24)
        var time = s_timeCurrent.replace(/-/g,':').replace(' ',':');
        time = time.split(':');
        dateToday = new Date(time[0], (time[1]-1), time[2], time[3], time[4], time[5]);

        var firstm = new Date(dateToday.getFullYear(), dateToday.getMonth(), 1);
        var lastm = new Date(dateToday.getFullYear(), dateToday.getMonth() + 1, 0);


        var today = new Date();
        var gap_day = parseInt("{{$gap_day}}");
        var status = $('#order_status').val();
        var order_start_at = "{{$order->start_at}}";
        var order_start_date = new Date(order_start_at);
        var order_end_date = new Date("{{$order->order_end_date}}");

        var stop_start_able_date = new Date();
        //        stop_start_able_date.setDate(today.getDate()+gap_day);

        if (order_start_date > stop_start_able_date) {
            stop_start_able_date = order_start_date;
        }

        var restart_able_date = new Date();
        if (order_start_date > restart_able_date) {
            restart_able_date = order_start_date;
        }
        //        if(gap_day)
        //            restart_able_date.setDate(today.getDate()+gap_day);
        //        else
        //        {
        //            gap_day = 3; //default
        //            restart_able_date.setDate(today.getDate()+3);
        //        }


        //set the stop modal 's start stop date val as orders's start date at least.
        if (status == "{{\App\Model\OrderModel\Order::ORDER_PASSED_STATUS}}" || status == "{{\App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS}}") {

            $('#stop_order_modal').find('.input-daterange #stop_start').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
                calendarWeeks: false,
                clearBtn: true,
                startDate: stop_start_able_date,
                endDate: order_end_date,
            });

            $('#stop_order_modal').find('.input-daterange #stop_end').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
                calendarWeeks: false,
                clearBtn: true,
                startDate: order_start_date,
            });
        } else if (status == "{{\App\Model\OrderModel\Order::ORDER_STOPPED_STATUS}}") {
            $('#restart_order_modal').find('.single_date').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
                calendarWeeks: false,
                clearBtn: true,
                startDate: restart_able_date,
            });
        }


        $('#product_table').find('tbody > tr').each(function() {
            initBottleNumCalendar($(this));
        })
    </script>


@endsection