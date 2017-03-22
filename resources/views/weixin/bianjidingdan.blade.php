@extends('weixin.layout.master')
@section('title','产品更改')
@section('css')
    <link rel="stylesheet" href="<?=asset('weixin/css/fullcalendar.min.css')?>">
@endsection

@section('content')

    <header>
        <a class="headl fanh" href="javascript:history.back();"></a>
        <h1>产品更改</h1>

    </header>
    <div class="bann">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @if(isset($file1) && $file1)
                    <div class="swiper-slide"><img class="bimg" src="{{$file1}}"></div>
                @endif
                @if(isset($file2) && $file2)
                    <div class="swiper-slide"><img class="bimg" src="{{$file2}}"></div>
                @endif
                @if(isset($file3) && $file3)
                    <div class="swiper-slide"><img class="bimg" src="{{$file3}}"></div>
                @endif
                @if(isset($file4) && $file4)
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
                <td height="16">半年单</td>
                <td class="dzmon">￥{{$half_year_price}}</td>
            </tr>
        </table>
    </div>

    <div class="dnsl pa2t">
        <input type="hidden" id="wechat_order_product_id" value="{{$wop->id}}">
        <input type="hidden" id="product_id" value="{{$product->id}}">
        @if(isset($group_id))
            <input type="hidden" id="group_id" value="{{$group_id}}"/>
        @endif

        <div class="dnsli clearfix">
            <div class="dnsti">订单类型：</div>
            <select class="dnsel" id="order_type">
                @if (isset($factory_order_types))
                    @foreach ($factory_order_types as $fot)
                        @if($fot->order_type == $wop->order_type)
                            <option value="{{$fot->order_type}}" selected>{{$fot->order_type_name}}</option>
                        @else
                            <option value="{{$fot->order_type}}">{{$fot->order_type_name}}</option>
                        @endif
                    @endforeach
                @endif
            </select>
            <div class="clear"></div>
        </div>
        <div class="dnsli clearfix">
            <div class="dnsti">订奶数量：</div>
                 <span class="minusplus product_total_count">
                  <a class="minus" href="javascript:;">-</a>
                  <input type="text" id="total_count" value="{{$wop->total_count}}" style="ime-mode: disabled;">
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
        <div class="dnsli clearfix dnsel_item" id="dnsel_item0" style="display: none;">
            <div class="dnsti">每天配送数量：</div>
            <span class="minusplus">
                <a class="minus" href="javascript:;">-</a>
                <input type="text" class="deliver_count_per_day" value="1" style="ime-mode: disabled;">
                <a class="plus" href="javascript:;">+</a>
            </span>（瓶）
        </div>

        <!--隔日送 -->
        <div class="dnsli clearfix dnsel_item" id="dnsel_item1" style="display: none;">
            <div class="dnsti">每天配送数量：</div>
            <span class="minusplus">
                <a class="minus" href="javascript:;">-</a>
                <input type="text" value="1" class="deliver_count_per_day" style="ime-mode: disabled;">
                <a class="plus" href="javascript:;">+</a>
            </span>（瓶）
        </div>

        <!-- 按周规则 -->
        <div class="dnsli clearfix dnsel_item" id="dnsel_item2" style="display: none;">
            <table class="psgzb" width="" border="0" cellspacing="0" cellpadding="0" id="week">
            </table>
        </div>

        <!-- 随心送 -->
        <div class="dnsel_item" id="dnsel_item3"  style="display: none;">
            <table class="psgzb" width="" border="0" cellspacing="0" cellpadding="0" id="calendar">
            </table>
        </div>

        <div class="dnsli clearfix">
            {{--<div class="dnsti">起送时间：</div>--}}
            {{--<div classs="input-group">--}}
                {{--<input class="qssj single_date" name="start_at" id="start_at" value="">--}}
                {{--<span><i class="fa fa-calendar"></i></span>--}}
            {{--</div>--}}
            <div class="ordrq">起送时间：<input class="qssj" id="start_at" name="start_at" type="date" value=""/></div>
        </div>

        <div class="dnsall">
            <div class="dnsts">
                订购天数：<span id="order_day_num"></span> 天
                <a class="cxsd" href="javascript:void(0);">重新设定</a>
            </div>
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

    <div class="dnsbt clearfix">
        <button id="submit_order" class="dnsb1"><i class="fa fa-save"></i> 保存</button>
        <button id="cancel" class="dnsb2"><i class="fa fa-reply"></i> 取消</button>
    </div>
@endsection
@section('script')

    <!-- Date picker and Date Range Picker-->
    <script src="<?=asset('weixin/js/showfullcalendar.js')?>"></script>
    <script src="<?=asset('weixin/js/showmyweek.js')?>"></script>

    <script type="text/javascript">
        var calen, week;

        var obj = $('#uecontent');
        var content = '{{$product->uecontent}}';

        obj.html(content);

        $(obj).each(function (){
            var $this = $(this);
            var t = $this.text();
            $this.html(t.replace('&lt;', '<').replace('&gt;', '>'));
        })
        


        var previous = "{{$previous}}";

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
                spaceBetween: 30,
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

            default_start_date = able_date.toISOString();
            $('#start_at').attr('min', default_start_date);

//            var default_start_date = able_date.toLocaleDateString();

//            //Single and Multiple Datepicker
//            $('#start_at').datepicker({
//                todayBtn: false,
//                keyboardNavigation: false,
//                forceParse: false,
//                calendarWeeks: false,
//                autoclose: true,
//                minDate: default_start_date,
//            });

            var current_start = '{{$wop->start_at}}';
            var current_start_date = new Date(current_start);
//            current_start_date = current_start_date.toLocaleDateString();
            current_start_date = current_start_date.toISOString();

            $('#start_at').val(current_start_date);

            $('select#order_type').trigger('change');


            calen = new showfullcalendar2("calendar",  change_order_day_num);
            week = new showmyweek2("week", change_order_day_num);
            dnsel_changed("dnsel_item0");

            init_wechat_order_product();

        });

        $('button#cancel').click(function(){
            if(previous == "queren")
            {
                var group_id = $('#group_id').val();
                window.location.href = SITE_URL + "weixin/querendingdan?group_id="+group_id;
            } else {
                window.location.href = SITE_URL + "weixin/qouwuche";
            }
        });


        $('select#order_type').change(function(){

            var count_input = $('#total_count');

            var cur_val = $(this).val();
            if(cur_val == "{{ \App\Model\OrderModel\OrderType::ORDER_TYPE_MONTH }}")
            {
                count_input.attr('min', 30);
                count_input.val(30);
            }else if(cur_val == "{{ \App\Model\OrderModel\OrderType::ORDER_TYPE_SEASON }}" ){
                count_input.attr('min', 90);
                count_input.val(90);
            }else if(cur_val == "{{ \App\Model\OrderModel\OrderType::ORDER_TYPE_HALF_YEAR }}" ){
                count_input.attr('min', 180);
                count_input.val(180);
            }
        });


        function check_bottle_count(){
            var count_input = $('#total_count');
            var min_b = parseInt( $(count_input).attr('min'));
            var current_b = $(count_input).val();
            if(current_b < min_b)
            {
                return true;
            }
            return false;
        }

        $('button#submit_order').click(function (e) {

            e.preventDefault();
            var send_data = new FormData();

            //wechat order product id
            var wechat_order_product_id = $('#wechat_order_product_id').val();
            send_data.append('wechat_order_product_id', wechat_order_product_id);

            //product_id
            var product_id = $('#product_id').val();
            send_data.append('product_id', product_id);

            //order_type
            var order_type = $('#order_type').val();
            send_data.append('order_type', order_type);
            //total_count
            var total_count = $('#total_count').val();
            send_data.append('total_count', total_count);

            //add delivery type and bottle_count or custom_dates
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

            //start at
            var start_at = $('#start_at').val();
            if (!start_at) {
                show_warning_msg('请选择起送时间');
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

            if (previous == "queren")
            {
                var group_id = $('#group_id').val();
            }

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/bianjidingdan/save_changed_order_item",
                data: send_data,
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.status == "success") {
                        show_success_msg("变化产品成功");

                        if(previous == "queren")
                        {
                            //go to shanpin qurendingdan
                            window.location.href = SITE_URL + "weixin/querendingdan?group_id="+group_id;
                        } else {
                            window.location.href = SITE_URL + "weixin/gouwuche";
                        }

                    } else
                    {
                        if(data.message)
                        {
                            show_warning_msg(data.message);
                        }
                    }
                },
                error: function (data) {
                    console.log(data);
                    show_warning_msg("附加产品失败");
                }
            });
        })

        function init_wechat_order_product()
        {
            var delivery_type = parseInt("{{$wop->delivery_type}}");

            var total_count = parseInt('{{$wop->total_count}}');
            $('#total_count').val(total_count);

            $('#delivery_type').find('option[data-value="'+delivery_type+'"]').prop('selected', true);

            $('#delivery_type').trigger('change');

            if(delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EVERY_DAY}}"))
            {
                var count_per = parseInt("{{$wop->count_per_day}}");
                $('#dnsel_item0 input').val(count_per);

            } else if ( delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY}}"))
            {
                var count_per = parseInt("{{$wop->count_per_day}}");
                $('#dnsel_item1 input').val(count_per);

            } else if ( delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_WEEK}}"))
            {
                //show custom bottle count on week
                week.custom_dates = "{{$wop->custom_order_dates}}";
                week.set_custom_date();

            } else if (delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_MONTH}}")){

                calen.custom_dates = "{{$wop->custom_order_dates}}";
                calen.set_custom_date();

            } else {
                return;
            }

            var order_day_num="{{$order_day_num}}";

            //get total order day numbers
            $('#order_day_num').text(order_day_num);

        }



        //calculate order days for this products
        $('input#total_count').change(function(){

            change_order_day_num();
        });

        $('.deliver_count_per_day').change(function(){

            change_order_day_num();
        });



        $('#start_at').change(function(){
            change_order_day_num();
        });

        $(document).on('click', '#week td', function(){
            change_order_day_num();
        });

        $(document).on('click', '#calendar td', function(){
            change_order_day_num();
        });


        function get_available_week_index(array, start)
        {
            if(array.hasOwnProperty(start))
                    return start;
            else {
                do{
                    start++;
                    if(start>7)
                    {
                        start  = 0;
                    }

                }while(! array.hasOwnProperty(start));

                return start;
            }
        }

        function get_available_month_index(array, start)
        {
            if(array.hasOwnProperty(start))
                return start;
            else {
                do{
                    start++;
                    if(start>31)
                    {
                        start  = 1;
                    }

                }while(! array.hasOwnProperty(start));

                return start;
            }
        }


        function get_order_days_from_week( total_count, custom_date){

            var order_dates = 0;

            custom_date = custom_date.slice(0,-1);
            var custom_array = custom_date.split(',');
            var value_array = [];
            for(var i = 0 ; i <custom_array.length; i++ )
            {
                var one_arr = custom_array[i].split(':');
                var index =one_arr[0];
                if(index == "0")
                        index = 7;
                value_array [index] = one_arr[1];
            }
            //set start_date based on start_at day of week
            var start_at = $('#start_at').val();
            var start_day =  new Date(start_at).getDay();
            if (start_day == 0)
            {
                start_day = 7;
            }

            i = get_available_week_index( value_array, start_day);

            do
            {
                if(i > 7)
                {
                    i = 1;
                }

                i = get_available_week_index(value_array, i);
                var value = value_array[i];
                total_count -= value;
                order_dates ++;
                i++;

            }while(total_count>0)

            return order_dates;
        }

        function get_order_days_from_calendar( total_count, custom_date){

            var order_dates = 0;

            custom_date = custom_date.slice(0,-1);
            var custom_array = custom_date.split(',');
            var value_array = [];
            for(var i = 0 ; i <custom_array.length; i++ )
            {
                var one_arr = custom_array[i].split(':');
                value_array [one_arr[0]] = one_arr[1];
            }

            //set start_date based on start_at day of week
            var start_at = $('#start_at').val();
            var start_day =  new Date(start_at).getDate();


            i = get_available_month_index( value_array, start_day);

            do
            {

                var value = value_array[i];
                total_count -= value;
                order_dates ++;
                i++;

                if(i > 31)
                {
                    i = 1;
                }

                i = get_available_month_index( value_array, i);

            }while(total_count>0)

            return order_dates;
        }

        function change_order_day_num()
        {
            //get total count
            var total_count = parseInt($('#total_count').val());

            var delivery_type = $('#delivery_type option:selected').data('value');

            var order_day_num=0;

            if(delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EVERY_DAY}}"))
            {
                var count_per = parseInt($('#dnsel_item0 .deliver_count_per_day').val());
                order_day_num = Math.ceil(total_count/count_per, 1);

            } else if ( delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY}}"))
            {
                var count_per = parseInt($('#dnsel_item1 .deliver_count_per_day').val());
                order_day_num = Math.round(total_count/count_per, 1);

            } else if ( delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_WEEK}}"))
            {
                //show custom bottle count on week
                var custom_date = week.get_submit_value();
                if(!custom_date || custom_date=="" || custom_date==undefined)
                    order_day_num="";
                else
                    order_day_num = get_order_days_from_week(total_count, custom_date);

            } else if (delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_MONTH}}")){

                var custom_date = calen.get_submit_value();
                if(!custom_date)
                    order_day_num="";
                else
                    order_day_num = get_order_days_from_calendar(total_count, custom_date);

            } else {
                return;
            }

            //get total order day numbers
            $('#order_day_num').text(order_day_num);
        }

        $(".plus").click(function () {
            $(this).prev().val(parseInt($(this).prev().val()) + 1);
            if(parseInt($(this).prev().val()) >1 )
            {
                $(this).parent().find('.minus').removeClass("minusDisable");
            }

            change_order_day_num();
        });
        $(".minus").click(function () {
            if (parseInt($(this).next().val()) > 1) {
                $(this).next().val(parseInt($(this).next().val()) - 1);
                $(this).removeClass("minusDisable");
            }
            if (parseInt($(this).next().val()) <= 1) {
                $(this).addClass("minusDisable");
            }

            change_order_day_num();
        });

    </script>

@endsection



