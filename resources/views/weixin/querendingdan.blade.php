@extends('weixin.layout.master')
@section('title','确认订单')
@section('css')
    <link href="<?=asset('weixin/css/fullcalendar.min.css')?>" rel="stylesheet"/>
@endsection


@section('content')

    <header>
        @if(isset($type) && isset($order))
            @if($type == "on_delivery")
                <a class="headl fanh" href="{{ url('weixin/dingdanliebiao?type=on_delivery') }}"></a>
            @elseif($type == "finished")
                <a class="headl fanh" href="{{ url('weixin/dingdanliebiao?type=finished') }}"></a>
            @endif
        @else
            <a class="headl fanh" href="{{url('weixin/shangpinliebiao')}}"></a>
        @endif
        <h1>确认订单</h1>
    </header>
    <div class="ordsl">
        <input type="hidden" id="group_id" value="{{$group_id}}"/>
        <input type="hidden" id="wxuser_id" value="{{$wxuser_id}}">
        <div class="addrli addrli2" style="cursor:pointer" onclick="go_page_address_list();">
            @if(isset($addr_obj) && ($addr_obj != null))
                <div class="adrtop pa2t">
                    <p>{{$addr_obj->name}} {{$addr_obj->phone}}<br>
                        {{$addr_obj->address}} {{$addr_obj->sub_address}}
                    </p>
                    <input type="hidden" value="{{$addr_obj->id}}" id="addr_obj_id">
                </div>
            @else
                <div class="adrtop pa2t">
                    <p style="color:red;"><i class="fa fa-warning"></i>请编辑您的地址信息</p>
                </div>
            @endif
        </div>

        @foreach($wechat_order_products as $wop)
            <div class="ordtop clearfix">
                <img class="ordpro" src="<?=asset('img/product/logo/' . $wop->product->photo_url1)?>">
                <span class="ordlr"><button data-pid="{{$wop->id}}" class="edit_order_product">编辑</button></span>
                <div class="ord-r">
                    {{$wop->product->name}}
                    <br>
                    单价：
                    @if ($wop->product_price)
                        {{$wop->product_price}}
                    @else
                        ??
                    @endif
                    <br>
                    订单数量：{{$wop->total_count}}瓶
                </div>
                <div class="ordye">金额：
                    @if ($wop->total_amount)
                        {{$wop->total_amount}}
                    @else
                        ??
                    @endif
                    元
                </div>
                <input type="hidden" id="total_amount" value="{{$wop->total_amount}}">
            </div>
        @endforeach

        <div class="ordbot">
            <textarea class="btxt" name="comment" id="comment" cols="" rows="" placeholder="备注"></textarea>
        </div>
    </div>
    <div class="ordrxg">
        <span>订奶计划预览:</span>
        <div id='calendar'></div>
    </div>
    <div class="he50"></div>
    <div class="dnsbt clearfix">
        @if( isset($passed) && $passed == 1 && count($wechat_order_products) > 0 && isset($addr_obj) && ($addr_obj != null) && $total_amount>0 )
            <button class="tjord tjord2" id="make_order">去付款</button>
        @else
            <button class="tjord tjord2" disabled>去付款</button>
        @endif
    </div>

@endsection
@section('script')
    <script src="<?=asset('weixin/js/fullcalendar.min.js')?>"></script>
    <script type="text/javascript">

        var today = "{{getCurDateString()}}";
        var gTradeNo;

        // 调用微信JS api 支付
        function jsApiCall(param) {
            var objParam = JSON.parse(param);

            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                objParam,
                function (res) {
                    WeixinJSBridge.log(res.err_msg);
                    // 支付成功
                    if (res.err_msg == 'get_brand_wcpay_request:ok') {
                        // 跳转到成功页面
                        window.location = SITE_URL + "weixin/zhifuchenggong?tradeNo=" + gTradeNo;
                    }
                    // 用户取消
                    else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                    }
                    // 支付失败
                    else {
                        window.location = SITE_URL + "weixin/zhifushibai";
                    }
                }
            );
        }

        function callpay(param) {
            if (typeof WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }

                // 支付模块不存在, 当支付失败
                window.location = SITE_URL + "weixin/zhifushibai";
            }
            else {
                jsApiCall(param);
            }
        }

        $(document).ready(function () {
                    @if(isset($message) && $message!="")
            var message = "{{$message}}";
            show_info_msg(message);
            @endif

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
                ]
            });

        });

        //make order based on cart
        $('#make_order').click(function () {

            var order_bt = this;

            var comment = $('#comment').val();
            var group_id = $('#group_id').val();
            var addr_obj_id = $('#addr_obj_id').val();

            // 调用预支付
            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/api/prepareMakeOrder",
                data: {
                    'comment': comment,
                    'groupId': group_id,
                    'addressId':addr_obj_id
                },
                success: function (data) {
                    console.log(data);
                    if (data.status === 'SUCCESS') {
                        // 调用支付
                        callpay(data.param);

                        gTradeNo = data.trade_no;
                    }
                    else {
                        if (data.message) {
                            show_err_msg(data.message);
                        }
                        $(order_bt).prop('disabled', false);
                    }
                },
                error: function (data) {
                    console.log(data);
                    $(order_bt).prop('disabled', false);
                    show_warning_msg("操作失败");
                }
            });
        });

        function go_page_address_list() {
            @if(isset($order) && isset($type))
                    window.location = SITE_URL + "weixin/dizhiliebiao?order=" + "{{ $order }}" + "&&type=" + "{{ $type }}";
            @else
                    window.location = SITE_URL + "weixin/dizhiliebiao";
            @endif

        }

        //edit order product
        $('button.edit_order_product').click(function () {
            var wechat_order_product_id = $(this).data('pid');
            var group_id = $('#group_id').val();

            @if(isset($for))
                    window.location = SITE_URL + "weixin/bianjidingdan?wechat_opid=" + wechat_order_product_id + '&from=queren&for=xuedan';
            @else
                    window.location = SITE_URL + "weixin/bianjidingdan?wechat_opid=" + wechat_order_product_id + '&from=queren';
            @endif
        })

    </script>
@endsection
