@extends('weixin.layout.master')
@section('title','订单修改')
@section('css')

@endsection
@section('content')
    <header>
        <a class="headl fanh" href="javascript:history.back()"></a>
        <h1>修改订单</h1>
    </header>

    <div class="ordtop pa2t clearfix">
        <img class="ordpro" src="<?=asset('img/product/logo/' . $order_product->product->photo_url1)?>">
        <p>{{$order_product->product_name}} <span>剩余数量：{{$order_product->remain_count}}</span></p>
        <div class="ordye">金额：{{$order_product->remain_amount}}元</div>
    </div>

    <input type="hidden" id="opid" value = "{{$order_product->id}}"/>
    <input type="hidden" id="order_id" value = "{{$order_product->order_id}}"/>

    <div class="dnsli clearfix dnsli2">
        <div class="dnsti">更改奶品：</div>
        <select class="dnsel" name="" id="product_list">
            @foreach($products as $product)
                @if($product->id == $order_product->product_id)
                    <option selected value="{{$product->id}}">{{$product->name}}</option>
                @else
                    <option value="{{$product->id}}">{{$product->name}}</option>
                @endif
            @endforeach
        </select>
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
            <span class="addSubtract">
                <a class="subtract" href="javascript:;">-</a>
                <input type="text" value="1" style="ime-mode: disabled;">
                <a class="add" href="javascript:;">+</a>
            </span>（瓶）
    </div>

    <!--隔日送 -->
    <div class="dnsli clearfix dnsel_item" id="dnsel_item1">
        <div class="dnsti">每天配送数量：</div>
            <span class="addSubtract">
                <a class="subtract" href="javascript:;">-</a>
                <input type="text" value="1" style="ime-mode: disabled;">
                <a class="add" href="javascript:;">+</a>
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

    <div class="he50"></div>
    <div class="dnsbt clearfix">
        <button class="tjord tjord2" id="change_order_product">提交</button>
    </div>
@endsection
@section('script')
    <script src="<?=asset('weixin/js/showfullcalendar.js')?>"></script>
    <script src="<?=asset('weixin/js/showmyweek.js')?>"></script>

    <script type="text/javascript">

        var calen, week;
        $(function () {
            calen = new showfullcalendar("calendar");
            week = new showmyweek("week");
            dnsel_changed("dnsel_item0");

            init_wechat_order_product();
        });

        function dnsel_changed(id) {
            $(".dnsel_item").css("display", "none");
            $("#" + id).css("display", "block");
        }

        function init_wechat_order_product() {
            var delivery_type = parseInt("{{$order_product->delivery_type}}");

            $('#delivery_type').find('option[data-value="'+delivery_type+'"]').prop('selected', true);

            $('#delivery_type').trigger('change');


            if(delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EVERY_DAY}}"))
            {
                var count_per = parseInt("{{$order_product->count_per_day}}");
                $('#dnsel_item0 input').val(count_per);

            } else if ( delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY}}"))
            {
                var count_per = parseInt("{{$order_product->count_per_day}}");
                $('#dnsel_item1 input').val(count_per);

            } else if ( delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_WEEK}}"))
            {
                //show custom bottle count on week
                week.custom_dates = "{{$order_product->custom_order_dates}}";
                week.set_custom_date();

            } else if (delivery_type == parseInt("{{\App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_MONTH}}")){

                calen.custom_dates = "{{$order_product->custom_order_dates}}";
                calen.set_custom_date();

            } else {
                return;
            }

        }

        $('#change_order_product').click(function(){

            var order_id = $('#order_id').val();

            var send_data = new FormData();

            var order_product_id = $('#opid').val();
            send_data.append('order_product_id', order_product_id);

            var product_id = $('#product_list').val();
            send_data.append('product_id', product_id);

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

            console.log(send_data);

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/api/change_order_product",
                data: send_data,
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.status == "success") {
                        show_success_msg("更改奶品成功");
                        //go to dingdan xiangqing
                        window.location.href = SITE_URL + "weixin/dingdanxiangqing?order="+order_id;
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

        });

    </script>
@endsection