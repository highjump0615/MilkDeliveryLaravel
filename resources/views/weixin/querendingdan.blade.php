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
                    <p style="color:red;"><i class="fa fa-warning"></i>请插入您的信息</p>
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

    <?php

    ini_set('date.timezone', 'Asia/Shanghai');

    //error_reporting(E_ERROR);

    include_once app_path() . "/Lib/Payweixin/WxPayConfig.php";
    include_once app_path() . "/Lib/Payweixin/WxPayApi.php";
    include_once app_path() . "/Lib/Payweixin/WxPayJsApiPay.php";
    include_once app_path() . "/Lib/Payweixin/WxPayException.php";
    include_once app_path() . "/Lib/Payweixin/WxPayData.php";


    $tools = new JsApiPay();


    $input = new WxPayUnifiedOrder();
    $input->SetBody("test");
    $input->SetAttach("test");
    $input->SetOut_trade_no(WxPayConfig::getMCHID() . date("YmdHis"));
    //    $input->SetTotal_fee("" . round($total_amount * 100, 0));
    $input->SetTotal_fee("1");
    $input->SetTime_start(date("YmdHis"));
    //$input->SetTime_expire(date("YmdHis", time() + 600));
    $input->SetGoods_tag("test");
    $input->SetNotify_url("http://niu.vfushun.com/milk/public/Payweixin/notify.php");
    $input->SetTrade_type("JSAPI");
    $input->SetOpenid($openid);

    $worder = WxPayApi::unifiedOrder($input);

//    function printf_info($data)
//    {
//        foreach($data as $key=>$value){
//            echo "<font color='#00ff55;'>$key</font> : $value <br/>";
//        }
//    }
//    printf_info($order);

    if ($total_amount > 0)
        $jsApiParameters = $tools->GetJsApiParameters($worder);
    else
        $jsApiParameters = '';

    $editAddress = $tools->GetEditAddressParameters();
    ?>

@endsection
@section('script')
    <script src="<?=asset('weixin/js/fullcalendar.min.js')?>"></script>
    <script type="text/javascript">

        var today = "{{$today}}";
        var order_id;

        //调用微信JS api 支付
        function jsApiCall() {
            WeixinJSBridge.invoke(
                    'getBrandWCPayRequest',

                    <?php
                            if (isset($jsApiParameters) && $jsApiParameters != '')
                                echo $jsApiParameters . ',';
                            ?>
                    function (res) {
                        WeixinJSBridge.log(res.err_msg);
                        if (res.err_msg == 'get_brand_wcpay_request:ok') {
                            //                            alert('支付成功了');
                            window.location = SITE_URL + "weixin/zhifuchenggong?order=" + order_id;
                        }
                        else {
                            //                            alert('支付失败了');
                            window.location = SITE_URL + "weixin/zhifushibai?order=" + order_id;
                        }
                    }
            );
        }

        function callpay() {
            if (typeof WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            } else {
                jsApiCall();
            }
        }

        window.onload = function () {
            if (typeof WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', editAddress, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', editAddress);
                    document.attachEvent('onWeixinJSBridgeReady', editAddress);
                }
            } else {
                editAddress();
            }
        };

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
            var comment = $('#comment').val();
            var group_id = $('#group_id').val();

            var order_bt = $(this);
            $(order_bt).prop('disabled', true);

            var total_amount = $('#total_amount').val();

            var addr_obj_id = $('#addr_obj_id').val();

            @if(isset($order))
                var origin_order_id = "{{$order}}";
            @endif

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/api/make_order_by_group",
                @if(isset($order))
                    data: {'comment': comment, 'group_id': group_id, 'order_id': origin_order_id, 'addr_obj_id':addr_obj_id},
                @else
                    data: {'comment': comment, 'group_id': group_id, 'addr_obj_id':addr_obj_id},
                @endif
                success: function (data) {
                    console.log(data);
                    if (data.status == 'success') {
                        $(order_bt).prop('disabled', false);
                        order_id = data.order_id;
                        callpay();
                    } else if(data.status == "fail") {
                        if (data.message) {
                            show_err_msg(data.message);
                        }

                        $(order_bt).prop('disabled', false);
                        window.location = SITE_URL + "weixin/zhifushibai";
                    } else {

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
            })
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
                    window.location = SITE_URL + "weixin/bianjidingdan?wechat_opid=" + wechat_order_product_id + '&&from=queren&&for=xuedan';
            @else
                    window.location = SITE_URL + "weixin/bianjidingdan?wechat_opid=" + wechat_order_product_id + '&&from=queren';
            @endif
        })

    </script>
@endsection