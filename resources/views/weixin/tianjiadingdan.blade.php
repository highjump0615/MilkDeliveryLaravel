@extends('weixin.layout.master')
@section('title','产品详情')
@section('css')
    <link rel="stylesheet" href="<?=asset('weixin/css/fullcalendar.min.css')?>">
@endsection

@section('content')

    <header>
        @if(isset($previous) && $previous == "none")
            <a class="headl fanh" href="{{url('weixin/shangpinliebiao')}}"></a>
        @else
            <a class="headl fanh" href="javascript:history.back();"></a>
        @endif
        <h1>产品详情</h1>

    </header>
    <div class="bann">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @if($file1)
                    <div class="swiper-slide"><img class="bimg" src="{{$file1}}"></div>
                @endif
                @if($file2)
                    <div class="swiper-slide"><img class="bimg" src="{{$file2}}"></div>
                @endif
                @if($file3)
                    <div class="swiper-slide"><img class="bimg" src="{{$file3}}"></div>
                @endif
                @if($file4)
                    <div class="swiper-slide"><img class="bimg" src="{{$file4}}"></div>
                @endif
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
    <div class="protop">
        <h3>{{$product->name}}</h3>
        <p>{{$product->introduction}}</p>
        <table class="prodz" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>月单</td>
                <td class="dzmon">￥{{$month_price}}</td>
            </tr>
            <tr>
                <td>季单</td>
                <td class="dzmon">￥{{$season_price}}</td>
            </tr>
            <tr>
                <td>半年单</td>
                <td class="dzmon">￥{{$half_year_price}}</td>
            </tr>
        </table>
    </div>

    <div class="dnsl pa2t">
        <input type="hidden" id="product_id" value="{{$product->id}}">

        <div class="dnsli clearfix">
            <div class="dnsti">订单类型：</div>
            <select class="dnsel" id="order_type">
                @if (isset($factory_order_types))
                    @foreach ($factory_order_types as $fot)
                        <option value="{{$fot->order_type}}">{{$fot->order_type_name}}</option>
                    @endforeach
                @endif
            </select>
            <div class="clear"></div>
        </div>
        <div class="dnsli clearfix">
            <div class="dnsti">订奶数量：</div>
                 <span class="minusplus">
                  <a class="minus" href="javascript:;">-</a>
                  <input type="text" min="1" id="total_count" value="30" style="ime-mode: disabled;">
                  <a class="plus" href="javascript:;">+</a>
                 </span>（瓶）
        </div>

        <div class="dnsli clearfix">
            <div class="dnsti">配送规则：</div>
            <select class="dnsel" id="delivery_type" onChange="javascript:dnsel_changed(this.value)">
                <option value="dnsel_item0"
                        data-value="{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EVERY_DAY}}">天天送
                </option>
                <option value="dnsel_item1"
                        data-value="{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY}}">隔日送
                </option>
                <option value="dnsel_item2"
                        data-value="{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_WEEK}}">按周送
                </option>
                <option value="dnsel_item3"
                        data-value="{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_MONTH}}">随心送
                </option>
            </select>
            <div class="clear"></div>
        </div>

        <!-- combo box change -->
        <!-- 天天送 -->
        <div class="dnsli clearfix dnsel_item" id="dnsel_item0">
            <div class="dnsti">每天配送数量：</div>
            <span class="minusplus">
                <a class="minus" href="javascript:;">-</a>
                <input type="text" value="1" style="ime-mode: disabled;">
                <a class="plus" href="javascript:;">+</a>
            </span>（瓶）
        </div>

        <!--隔日送 -->
        <div class="dnsli clearfix dnsel_item" id="dnsel_item1">
            <div class="dnsti">每天配送数量：</div>
            <span class="minusplus">
                <a class="minus" href="javascript:;">-</a>
                <input type="text" value="1" style="ime-mode: disabled;">
                <a class="plus" href="javascript:;">+</a>
            </span>（瓶）
        </div>

        <!-- 按周规则 -->
        <div class="dnsli clearfix dnsel_item" id="dnsel_item2">
            <table class="psgzb" width="" border="0" cellspacing="0" cellpadding="0" id="week">
            </table>
        </div>

        <!-- 随心送 -->
        <div class="dnsel_item" id="dnsel_item3">
            <table class="psgzb" width="" border="0" cellspacing="0" cellpadding="0" id="calendar">
            </table>
        </div>

        <div class="dnsli clearfix">
            {{--<div class="dnsti">起送时间：</div>--}}
            {{--<div class="input-group datepicker">--}}
                {{--<input type="text" required class="" name="start_at" id="start_at">--}}
                {{--<span><i class="fa fa-calendar"></i></span>--}}
            {{--</div>--}}
            <div class="ordrq">起送时间：<input class="qssj" id="start_at" name="start_at" type="date" value=""/></div>
        </div>

        <div class="dnsall">
            <div class="dnsli clearfix">
                <div class="dnsti">规格：</div>
                <div class="dnsti-r">{{$product->bottle_type_name}}</div>
            </div>
            <div class="dnsli clearfix">
                <div class="dnsti">保质期：</div>
                <div class="dnsti-r">{{$product->guarantee_period}}天</div>
            </div>
            <div class="dnsli clearfix">
                <div class="dnsti">储藏条件：</div>
                <div class="dnsti-r">{{$product->guarantee_req}}</div>
            </div>
            <div class="dnsli clearfix">
                <div class="dnsti">配料：</div>
                <div class="dnsti-r">{{$product->material}}</div>
            </div>
        </div>

    </div>
    <div class="dnxx">
        <div class="dnxti"><strong>详细介绍</strong>
            <span>DETAILED INTRODUCTION</span>
        </div>
        <div id="uecontent">

        </div>
    </div>
    <div class="sppj pa2t">
        <div class="sppti">商品评价</div>

        @if(isset($reviews))
            <ul class="sppul">
                @forelse($reviews as $review)
                    <li>
                        <div class="spnum"><span class="spstart">@for($i=0; $i<$review->mark; $i++)<i></i>@endfor</span>
                            <p>{{$review->tel_number}}</p>
                        </div>
                        <div class="pjxx">
                            {{$review->content}}
                        </div>
                    </li>
                @empty
                    <p style="text-align: center;">没有评价</p>
                @endforelse
            </ul>
        @endif
    </div>

    <div class="he50"></div>

    @if(!isset($order_id))
    <div class="dnsbt clearfix">
        <button id="make_order" class="dnsb1"><i class="fa fa-check-circle"></i> 立即订购</button>
        <button id="submit_order" class="dnsb2"><i class="fa fa-cart-plus"></i> 加入购物车</button>
    </div>
    @elseif (isset($order_id))
        <div class="dnsbt clearfix">
            <button id="add_order" data-order-id="{{$order_id}}" class="dnsb2"><i class="fa fa-plus-circle"></i> 加入订单</button>
        </div>
    @endif

@endsection
@section('script')
    <script src="<?=asset('weixin/js/showfullcalendar.js')?>"></script>
    <script src="<?=asset('weixin/js/myweek.js')?>"></script>

    <script type="text/javascript">
        var calen, week;

        var obj = $('#uecontent');
        var content = '{{$product->uecontent}}';

        obj.html(content);

        $(obj).each(function () {
            var $this = $(this);
            var t = $this.text();
            $this.html(t.replace('&lt;', '<').replace('&gt;', '>'));
        })

        $(function () {
            calen = new showfullcalendar("calendar");
            week = new myweek("week");
            dnsel_changed("dnsel_item0");
        });

        $('select#order_type').change(function () {

            var count_input = $('#total_count');

            var cur_val = $(this).val();
            if (cur_val == "{{ \App\Model\OrderModel\OrderType::ORDER_TYPE_MONTH }}") {
                @if($previous == "0")
                count_input.attr('min', 30);
                @endif
                count_input.val(30);
            } else if (cur_val == "{{ \App\Model\OrderModel\OrderType::ORDER_TYPE_SEASON }}") {
                @if($previous == "0")
                count_input.attr('min', 90);
                @endif
                count_input.val(90);
            } else if (cur_val == "{{ \App\Model\OrderModel\OrderType::ORDER_TYPE_HALF_YEAR }}") {
                @if($previous == "0")
                count_input.attr('min', 180);
                @endif
                count_input.val(180);
            }
        });

        function dnsel_changed(id) {
            $(".dnsel_item").css("display", "none");
            $("#" + id).css("display", "block");
        }

        function pad(number){
            var r= String(number);
            if(r.length === 1){
                r= '0'+r;
            }
            return r;
        }


        var able_date, default_start_date;

        $(document).ready(function () {
            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                spaceBetween: 30
            });

                    @if(isset($gap_day))
            var gap_day = parseInt("{{$gap_day}}");
                    @endif

            var today = new Date("{{$today}}");
            able_date = today;
            if (gap_day)
                able_date.setDate(today.getDate() + gap_day);
            else {
                able_date.setDate(today.getDate() + 3);
            }

            Date.prototype.toISOString = function(){
              return this.getUTCFullYear() + '-' + pad(this.getUTCMonth() +1) + '-'+pad(this.getUTCDate());
            };

            //set default day for start at
            default_start_date = able_date.toISOString();
            $('#start_at').val(default_start_date);

            $('#start_at').attr('min', default_start_date);

//            $('#start_at').datepicker({
//                todayBtn: false,
//                keyboardNavigation: false,
//                forceParse: false,
//                calendarWeeks: false,
//                autoclose: true,
//                minDate: able_date,
//            });

            $('select#order_type').trigger('change');
        });

        function check_bottle_count() {
            var count_input = $('#total_count');
            var min_b = parseInt($(count_input).attr('min'));
            var current_b = $(count_input).val();
            if (current_b < min_b) {
                return true;
            }
            return false;
        }

        $('button#make_order').click(function () {

            if (check_bottle_count()) {
                show_info_msg('请正确设置订奶数量');
                return;
            }

            var send_data = new FormData();

            //product_id
            var product_id = $('#product_id').val();
            send_data.append('product_id', product_id);

            //order_type
            var order_type = $('#order_type').val();
            send_data.append('order_type', order_type);
            //total_count
            var total_count = $('#total_count').val();
            send_data.append('total_count', total_count);

            var delivery_type = $('#delivery_type option:selected').data('value');
            send_data.append('delivery_type', delivery_type);

            var count = 0;
            var custom_date = "";
            if (($('#dnsel_item0')).css('display') != "none") {
                count = $('#dnsel_item0 input').val();
                if (!count) {
                    show_warning_msg('请填写产品的所有字段')
                    return;
                }
                send_data.append('count_per', count);

            }
            else if (($('#dnsel_item1')).css('display') != "none") {
                count = $('#dnsel_item1 input').val();
                if (!count) {
                    show_warning_msg('请填写产品的所有字段')
                    return;
                }
                send_data.append('count_per', count);

            }
            else if (($('#dnsel_item2')).css('display') != "none") {
                //week dates
                custom_date = week.get_submit_value();
                if (!custom_date) {
                    show_warning_msg('请填写产品的所有字段')
                    return;
                }
                send_data.append('custom_date', custom_date);

            }
            else {
                //month dates
                custom_date = calen.get_submit_value();
                if (!custom_date) {
                    show_warning_msg('请填写产品的所有字段')
                    return;
                }
                send_data.append('custom_date', custom_date);
            }

            var start_at = $('#start_at').val();
            if (!start_at) {
                show_warning_msg("请选择起送时间");
                return;
            }

            var start_time = new Date(start_at);
            if(start_time < able_date)
            {
                show_warning_msg("选择"+default_start_date+"之后的日期.");
                return;
            }

            send_data.append('start_at', start_at);

            console.log(send_data);

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/api/make_order_directly",
                data: send_data,
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.status == "success") {
                        window.location.href = SITE_URL + "weixin/querendingdan";
                    } else {
                        if (data.redirect_path == "phone_verify") {
                            window.location.href = SITE_URL + "weixin/dengji";
                        }
                    }
                },
                error: function (data) {
                    console.log(data);
                    show_warning_msg("附加产品失败");
                }
            });

        });

        $('button#submit_order').click(function (e) {

            e.preventDefault();
            var send_data = new FormData();

            //product_id
            var product_id = $('#product_id').val();
            send_data.append('product_id', product_id);

            //order_type
            var order_type = $('#order_type').val();
            send_data.append('order_type', order_type);
            //total_count
            var total_count = $('#total_count').val();
            send_data.append('total_count', total_count);

            var delivery_type = $('#delivery_type option:selected').data('value');
            send_data.append('delivery_type', delivery_type);

            var count = 0;
            var custom_date = "";
            if (($('#dnsel_item0')).css('display') != "none") {
                count = $('#dnsel_item0 input').val();
                if (!count) {
                    show_warning_msg('请填写产品的所有字段')
                    return;
                }
                send_data.append('count_per', count);

            }
            else if (($('#dnsel_item1')).css('display') != "none") {
                count = $('#dnsel_item1 input').val();
                if (!count) {
                    show_warning_msg('请填写产品的所有字段')
                    return;
                }
                send_data.append('count_per', count);

            }
            else if (($('#dnsel_item2')).css('display') != "none") {
                //week dates
                custom_date = week.get_submit_value();
                if (!custom_date) {
                    show_warning_msg('请填写产品的所有字段')
                    return;
                }
                send_data.append('custom_date', custom_date);

            }
            else {
                //month dates
                custom_date = calen.get_submit_value();
                if (!custom_date) {
                    show_warning_msg('请填写产品的所有字段')
                    return;
                }
                send_data.append('custom_date', custom_date);
            }

            var start_at = $('#start_at').val();
            if (!start_at) {
                show_warning_msg("请选择起送时间");
                return;
            }

            var start_time = new Date(start_at);
            if(start_time < able_date)
            {
                show_warning_msg("选择"+default_start_date+"之后的日期.");
                return;
            }

            send_data.append('start_at', start_at);

            console.log(send_data);

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/api/insert_order_item_to_cart",
                data: send_data,
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.status == "success") {
                        show_success_msg("附加产品成功");
                        //go to shanpin liebiao
                        window.location.href = SITE_URL + "weixin/shangpinliebiao";
                    }
                },
                error: function (data) {
                    console.log(data);
                    show_warning_msg("附加产品失败");
                }
            });
        })

        $('button#add_order').click(function () {

//            if (check_bottle_count()) {
//                show_info_msg('请正确设置订奶数量');
//                return;
//            }

            var send_data = new FormData();

            //product_id
            var product_id = $('#product_id').val();
            send_data.append('product_id', product_id);

            //order_type
            var order_type = $('#order_type').val();
            send_data.append('order_type', order_type);
            //total_count
            var total_count = $('#total_count').val();
            send_data.append('total_count', total_count);

            var delivery_type = $('#delivery_type option:selected').data('value');
            send_data.append('delivery_type', delivery_type);

            var count = 0;
            var custom_date = "";
            if (($('#dnsel_item0')).css('display') != "none") {
                count = $('#dnsel_item0 input').val();
                if (!count) {
                    show_warning_msg('请填写产品的所有字段')
                    return;
                }
                send_data.append('count_per', count);

            }
            else if (($('#dnsel_item1')).css('display') != "none") {
                count = $('#dnsel_item1 input').val();
                if (!count) {
                    show_warning_msg('请填写产品的所有字段')
                    return;
                }
                send_data.append('count_per', count);

            }
            else if (($('#dnsel_item2')).css('display') != "none") {
                //week dates
                custom_date = week.get_submit_value();
                if (!custom_date) {
                    show_warning_msg('请填写产品的所有字段')
                    return;
                }
                send_data.append('custom_date', custom_date);

            }
            else {
                //month dates
                custom_date = calen.get_submit_value();
                if (!custom_date) {
                    show_warning_msg('请填写产品的所有字段')
                    return;
                }
                send_data.append('custom_date', custom_date);
            }

            var start_at = $('#start_at').val();
            if (!start_at) {
                show_warning_msg("请选择起送时间");
                return;
            }

            var start_time = new Date(start_at);
            if(start_time < able_date)
            {
                show_warning_msg("选择"+default_start_date+"之后的日期.");
                return;
            }

            send_data.append('start_at', start_at);

            var order_id = $(this).data('order-id');
            send_data.append('order_id', order_id);

            console.log(send_data);

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/api/add_product_to_order_for_xiugai",
                data: send_data,
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.status == "success") {
                        @if(isset($type))
                            window.location.href = SITE_URL + "weixin/dingdanxiugai?order="+order_id+"type={{$type}}";
                        @else
                            window.location.href = SITE_URL + "weixin/dingdanxiugai?order="+order_id;
                        @endif
                    } else {
                        show_warning_msg("附加产品失败");
                    }
                },
                error: function (data) {
                    console.log(data);
                    show_warning_msg("附加产品失败");
                }
            });

        });

        $(".plus").click(function () {
            $(this).prev().val(parseInt($(this).prev().val()) + 1);
            if(parseInt($(this).prev().val()) >1 )
            {
                $(this).parent().find('.minus').removeClass("minusDisable");
            }
        });
        $(".minus").click(function () {
            if (parseInt($(this).next().val()) > 1) {
                $(this).next().val(parseInt($(this).next().val()) - 1);
                $(this).removeClass("minusDisable");
            }
            if (parseInt($(this).next().val()) <= 1) {
                $(this).addClass("minusDisable");
            }
        });

    </script>

@endsection