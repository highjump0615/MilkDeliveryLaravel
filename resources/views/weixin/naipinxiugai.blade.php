@extends('weixin.layout.master')
@section('title','产品修改')
@section('css')

@endsection
@section('content')
    <header>
        @if(isset($type))
            <a class="headl fanh" href="{{url('weixin/dingdanxiugai?order='.$order_id.'&&type='.$type)}}"></a>
        @else
            <a class="headl fanh" href="{{url('weixin/dingdanxiugai?order='.$order_id)}}"></a>
        @endif
        <h1>产品修改</h1>
    </header>

    <div class="ordtop pa2t clearfix">
        @if(isset($current_product_photo_url))
        <img id="pimg" class="ordpro" src="<?=asset('img/product/logo/' . $current_product_photo_url)?>">
        @endif
        <div class="ordyf">
            <span id="pname">{{ $current_product_name }}</span>
        </div>
        <div class="ordyf">
            <span>单价: <b id="product_price"> {{ $current_product_price }}</b></span>
            <span>剩余数量：<b id="product_count"> {{  $current_product_count }}</b></span>
        </div>
        <div class="ordyf">
            <span>现在金额：<b id="current_amount">{{ $current_product_amount }}</b>元</span>
        </div>
        <div class="ordye">
            <span>更改后金额：<b id="after_changed_amount">{{ $current_product_amount }}</b>元</span>
            <span>差额：<b id="left_amount">{{$current_order_remain_amount}}</b>元</span>
        </div>
    </div>

    <div class="dnsli  dnsli2 clearfix">
        <div class="dnsti">订奶数量：</div>
                 <span class="minusplus">
                  <a class="minus" href="javascript:;">-</a>
                  <input type="text" min="1" id="changed_product_count" value="{{$current_product_count}}"
                         max="{{$current_product_count}}">
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
            <span class="minusplus deliver_plan_as">
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

    <div class="dnsli clearfix dnsli2">
        <p class="">选择奶品：</p>
        <div class="product_list">
            @foreach($products as $product)
                @if($product[0] != $current_product_id)
                    <div class="orddp pa2t clearfix">
                        @if(isset($type))
                            <a href="{{url('weixin/tianjiadingdan?product='.$product[0].'&previous=naipinxiugai&&type=').$type}}"><img class="ordpro img_select" src="<?=asset('img/product/logo/' . $product[2])?>"></a>
                        @else
                            <a href="{{url('weixin/tianjiadingdan?product='.$product[0].'&previous=naipinxiugai')}}"><img class="ordpro img_select" src="<?=asset('img/product/logo/' . $product[2])?>"></a>
                        @endif
                        <div class="spp spp1">
                            <p class="spname">{{$product[1]}}</p>
                            <p class="spname">{{$product[3]}}元</p>
                        </div>
                        <div class="spp spp2">
                            <input class="ordxz cart_check" name="" type="checkbox" data-id="{{$product[0]}}"/>
                        </div>
                        <div class="spp spp3">可换：{{$product[4]}}瓶</div>
                    </div>
                @endif
            @endforeach
        </div>

    </div>

    <div class="he50"></div>
    <div class="dnsbt clearfix">
        <button id="change_order_product" class="dnsb1"><i class="fa fa-check-circle"></i> 提交</button>
        <button id="cancel_change_order_product" class="dnsb2"><i class="fa fa-times-circle"></i> 取消</button>
    </div>
@endsection
@section('script')
    <script src="<?=asset('weixin/js/showfullcalendar.js')?>"></script>
    <script src="<?=asset('weixin/js/showmyweek.js')?>"></script>

    <script type="text/javascript">

        <?php echo "var products = " . json_encode($products); ?>

        var logo_base_url = "{{asset('img/product/logo/')}}";

        console.log(products);

        var order_id = "{{$order_id}}";
        var index = "{{$index}}";
        var current_product_amount = parseFloat("{{$current_product_amount}}");
        var current_order_remain_amount = parseFloat("{{$current_order_remain_amount}}");

        //origin product
        var current_product_id = "{{$current_product_id}}";
        var selected_product_id = "{{$current_product_id}}";

        var calen, week;
        $(function () {
            calen = new showfullcalendar("calendar");
            week = new showmyweek("week");
            dnsel_changed("dnsel_item0");
            init_wechat_order_product();

        });

        //show image product
        {{--$('.img_select').click(function(){--}}
            {{--var img_product_id = $(this).data('pid');--}}
            {{--var previous_loaction = location.href;--}}
            {{--var new_location = "{{url('weixin/tianjiadingdan')}}"+"?product="+img_product_id;--}}
            {{--console.log(new_location);--}}
        {{--});--}}

        function dnsel_changed(id) {
            $(".dnsel_item").css("display", "none");
            $("#" + id).css("display", "block");
        }

        function init_wechat_order_product() {
            var delivery_type = parseInt("{{$current_delivery_type}}");

            $('#delivery_type').find('option[data-value="' + delivery_type + '"]').prop('selected', true);

            $('#delivery_type').trigger('change');


            if (delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EVERY_DAY}}")) {
                var count_per = parseInt("{{$current_count_per_day}}");
                $('#dnsel_item0 input').val(count_per);

            } else if (delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY}}")) {
                var count_per = parseInt("{{$current_count_per_day}}");
                $('#dnsel_item1 input').val(count_per);

            } else if (delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_WEEK}}")) {
                //show custom bottle count on week
                week.custom_dates = "{{$current_custom_order_dates}}";
                week.set_custom_date();

            } else if (delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_MONTH}}")) {

                calen.custom_dates = "{{$current_custom_order_dates}}";
                calen.set_custom_date();

            } else {
                return;
            }

        }

        function set_original_product()
        {
            var origin_img_url = logo_base_url + '/' + '{{$current_product_photo_url}}';
            $('#pimg').attr('src', origin_img_url);
            $('#pname').html("{{ $current_product_name }}");
            var current_product_count ="{{  $current_product_count }}";
            $('#product_count').html(current_product_count);
            $('#changed_product_count').val(current_product_count).attr('max', current_product_count);
            $('#product_price').html("{{ $current_product_price }}");

            $('#after_changed_amount').html("{{ $current_product_amount }}");
            $('#left_amount').html("{{$current_order_remain_amount}}");
        }

        //show current selected or origin product info above
        function reset_product_above() {
            if(current_product_id == selected_product_id)
            {
                //show origin product
                set_original_product();
            } else {
                //show selected new product
                var current_p = products[selected_product_id];
                var new_img_url = logo_base_url + '/' + current_p[2];
                $('#pimg').attr('src', new_img_url);
                $('#pname').html(current_p[1]);
                $('#product_count').html(current_p[4]);
                $('#changed_product_count').val(current_p[4]);
                $('#changed_product_count').attr('max', current_p[4]);
                $('#product_price').html(current_p[3]);

                //order product amount after changed
                var after_changed_amount = parseFloat(current_p[4]) * parseFloat(current_p[3]);
                $('#after_changed_amount').html(after_changed_amount.toFixed(2));

                var left_amount = current_product_amount + current_order_remain_amount - after_changed_amount;
                $('#left_amount').html(left_amount.toFixed(2));
            }

        }

        function deselect_all() {
            $('.cart_check').each(function () {
                $(this).prop('checked', false);
                $(this).parent().parent().find('.spp3').css('visibility', "hidden");
            });

            selected_product_id = current_product_id;
        }

        var check_count = 0;
        //select other product
        $('.cart_check').click(function () {
            if ($(this).prop('checked')) {
                if (check_count) {
                    //deselect all
                    deselect_all();

                    //select this
                    $(this).prop('checked', true);
                    check_count++;

                } else {
                    //choose new
                    check_count++;
                }

                //show beside bottle count
                $(this).parent().parent().find('.spp3').css('visibility', "visible");

                selected_product_id = $(this).data('id');
                //show selected product info
                reset_product_above();

            } else {
                //no choose
                check_count--;
                $(this).parent().parent().find('.spp3').css('visibility', "hidden");

                selected_product_id = current_product_id;
                reset_product_above();
            }
        });

        //change order product count

        function reset_order_info(){
            var pcount = $('#changed_product_count').val();
            var price = parseFloat($('#product_price').html());

            var after_changed_amount = parseFloat(pcount * price).toFixed(2);
            $('#after_changed_amount').html(after_changed_amount);

            var left_amount = current_product_amount + current_order_remain_amount - after_changed_amount;
            $('#left_amount').html(left_amount.toFixed(2));
        }

        $('#changed_product_count').change(function(){
          reset_order_info();
        });

        $('.plus').click(function(){
            reset_order_info();
        });

        $('.minus').click(function(){
            reset_order_info();
        });


        //cancel change of order product
        $('#cancel_change_order_product').click(function () {
            //return to dingdanxiugai page
            history.back();
        });

        //change order product
        $('#change_order_product').click(function () {

            //check left amount
//            if( parseFloat($('#left_amount').html()) < 0 )
//            {
//                show_err_msg('更改后金额不能超过订单余额');
//                return;
//            }

            var send_data = new FormData();

            //order_id
            send_data.append('order_id', order_id);

            //origin order product id
            send_data.append('index', index);

            //current origin product id
            send_data.append('current_product_id', current_product_id);

            //current selected product id
            send_data.append('new_product_id', selected_product_id);

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

            var product_count = $('#changed_product_count').val();
            send_data.append('product_count', product_count);

            var product_amount = parseFloat($('#after_changed_amount').html()).toFixed(2);
            send_data.append('product_amount', product_amount);


            var product_price = parseFloat($('#product_price').html()).toFixed(2);
            send_data.append('product_price', product_price);


            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/api/change_temp_order_product",
                data: send_data,
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.status == "success") {
                        show_success_msg("更改奶品成功");
                        //go to dingdan xiangqing
                        @if(isset($type))
                            window.location.href = SITE_URL + "weixin/dingdanxiugai?order=" + order_id+"&&type={{$type}}";
                        @else
                            window.location.href = SITE_URL + "weixin/dingdanxiugai?order=" + order_id;
                        @endif
                    } else {
                        if (data.message) {
                            show_warning_msg(data.message);
                        }
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