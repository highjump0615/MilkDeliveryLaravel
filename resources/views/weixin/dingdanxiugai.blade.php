@extends('weixin.layout.master')
@section('title','订单修改')
@section('css')
    <link href='css/fullcalendar.min.css' rel='stylesheet'/>
@endsection
@section('content')

    <header>
        <a class="headl fanh" href="{{url('weixin/dingdanliebiao?type=on_delivery')}}"></a>
        <h1>订单修改</h1>
    </header>
    @if(isset($order))
        <div class="ordsl">
            <div class="ordnum">
                <span>{{$order->ordered_at}}</span>
                订单号 : {{$order->number}} &emsp; 状态 : {{$order->status_name}}
            </div>
            <div class="addrli2">
                <div class="adrtop pa2t">
                    <p>{{$order->customer_name}} {{$order->phone}}<br>{{$order->address}}</p>
                </div>
            </div>
            <div class="ordnum lastcd">
                奶站: {{$order->station_name}} &emsp; 配送员: {{$order->milkman->name}} {{$order->milkman->phone}}
            </div>
            <div class="ordyg">
                订单金额: <span>{{ $order->total_amount }}</span>
                现在剩金额: <span>{{ $order_remain_amount }}</span>
                更改后金额: <span>{{$after_changed_amount}}</span>
                差额: <span id="left_amount">{{$left_amount}}</span>
            </div>
            <div>
                @if(isset($type))
                <a class="xiugai_link col-lg-2 text-center" style="float:none; margin-left: 4%;"
                   href="{{url('/weixin/shangpinliebiao')."?order_id=".$order->id."&&type=".$type}}"><i class="fa fa-plus-circle"></i>
                    附加</a>
                @else
                    <a class="xiugai_link col-lg-2 text-center" style="float:none; margin-left: 4%;"
                       href="{{url('/weixin/shangpinliebiao')."?order_id=".$order->id}}"><i class="fa fa-plus-circle"></i>
                        附加</a>
                @endif
            </div>

            @forelse($show_products as $index => $sp)
                <div class="ordtop clearfix">
                    <img class="ordpro" src="<?=asset('img/product/logo/' . $sp[2])?>">
                    <span class="ordlr">
                        @if(isset($type))
                        <a class="xiugai_link xiugai_product"
                           href="{{url('/weixin/naipinxiugai?index=').$index.'&order_id='.$order->id.'&left_amount='.$left_amount."&&type=".$type}}">修改</a>
                        @else
                            <a class="xiugai_link xiugai_product"
                               href="{{url('/weixin/naipinxiugai?index=').$index.'&order_id='.$order->id.'&left_amount='.$left_amount}}">修改</a>
                        @endif
                        <button class="xiugai_link remove_product" data-index="{{$index}}"
                                data-order-id="{{$order->id}}">删除</button>
                    </span>
                    <div class="ord-r">
                        {{$sp[1]}}
                        <br>
                        单价：{{$sp[4]}}元
                        <br>
                        订单数量：{{$sp[3]}}
                    </div>
                    <div class="ordye">金额：{{$sp[5]}}元</div>
                </div>
            @empty
                没有项目
            @endforelse

            <h3 class="dnh3">我的订奶计划</h3>
            <div id='calendar'></div>
            <div class="ordbot">
                <textarea class="btxt" name="" cols="" rows="" placeholder="备注">{{$comment}}</textarea>
            </div>

            <div class="dnsbt change_order clearfix">
                <button id="change_order" data-order-id="{{$order->id}}" class="dnsb1"><i
                            class="fa fa-check-circle"></i> 确认更改
                </button>
                <button id="cancel_change_order" data-order-id="{{$order->id}}" class="dnsb2"><i
                            class="fa fa-times-circle"></i> 取消更改
                </button>
            </div>

        </div>
    @else
        <p>没有数据</p>
    @endif

    @include('weixin.layout.footer')
@endsection
@section('script')
    <script src='js/fullcalendar.min.js'></script>
    <script type="text/javascript">
        var today = "{{$today}}";
        $(function () {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },
                firstDay: 0,
                editable: false,
                now: today,
                events: [
                        @foreach($plans as $p)
                    {
                        title: "{{$p->product_name}} {{$p->changed_plan_count}}",
                        start: '{{$p->deliver_at}}',
                        className: 'ypsrl',
                        textColor: '#00cc00'

                    },
                    @endforeach
                ],
            });


            $('button.remove_product').click(function () {

                var index = $(this).data('index');
                var order_id = $(this).data('order-id');
                //Should remove one product at least
                var length = $('.ordtop').length;
                if(length <=1)
                {
                    show_err_msg('您不能删除最后一个订单产品');
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "weixin/api/remove_product_from_order",
                    data: {
                        'index': index,
                        'order_id': order_id
                    },
                    success: function (data) {
                        if (data.status == "success") {
                            show_success_msg("删除奶品成功");
                            //go to dingdan xiangqing
                            window.location.href = SITE_URL + "weixin/dingdanxiugai?order=" + order_id;
                        } else {
                            if (data.message) {
                                show_warning_msg(data.message);
                            }
                        }
                    },
                    error: function (data) {
                        console.log(data);
                        show_warning_msg("删除产品失败");
                    }
                });
            });

            $('button#cancel_change_order').click(function () {

                var order_id = $(this).data('order-id');

                $.ajax({
                    type: "POST",
                    url: SITE_URL + "weixin/api/cancel_change_order",
                    data: {
                        'order_id': order_id,
                    },
                    success: function (data) {
                        if (data.status == "success") {
                            window.location.href = SITE_URL + "weixin/dingdanliebiao?type=on_delivery";
                        } else {
                            if (data.message) {
                                show_warning_msg(data.message);
                            }
                        }
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            });

            $('button#change_order').click(function () {
                var order_id = $(this).data('order-id');

                //left amount check
                if (parseFloat($('#left_amount').html()) < 0) {
                    show_err_msg('更改后金额不能超过订单余额');
                    return;
                }
                $.ajax({
                    type: "POST",
                    url: SITE_URL + "weixin/api/change_order",
                    data: {
                        'order_id': order_id
                    },
                    success: function (data) {
                        if (data.status == "success") {
                            show_success_msg('订单修改成功');
                            window.location.href = SITE_URL + "weixin/dingdanliebiao?type=on_delivery";
                        } else {
                            if (data.message) {
                                show_warning_msg(data.message);
                            }
                        }
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            });
        });
    </script>
@endsection
