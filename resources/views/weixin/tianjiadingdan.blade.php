@extends('weixin.layout.master')
@section('title','天天送')
@section('css')
    <link rel="stylesheet" href="<?=asset('weixin/css/swiper.min.css')?>">
    <link rel="stylesheet" href="<?=asset('weixin/css/fullcalendar.min.css')?>">
    <link rel="stylesheet" href="<?=asset('weixin/css/swiper.min.css')?>">
    <link href="<?=asset('font-awesome/css/font-awesome.css') ?>" rel="stylesheet">
    <link href="<?=asset('css/plugins/datepicker/datepicker3.css')?>" rel="stylesheet">

@endsection
@section('content')

    <header>
        <a class="headl fanh" href="{{url('weixin/shangpinliebiao')}}"></a>
        <h1>产品详情</h1>

    </header>
    <div class="bann">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide"><img class="bimg" src="{{$file1}}"></div>
                <div class="swiper-slide"><img class="bimg" src="{{$file2}}"></div>
                <div class="swiper-slide"><img class="bimg" src="{{$file3}}"></div>
                <div class="swiper-slide"><img class="bimg" src="{{$file4}}"></div>
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
    <div class="protop">
        <h3>{{$product->name}} {{$product->bottle_type_name}}</h3>
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
                 <span class="addSubtract">
                  <a class="subtract" href="javascript:;">-</a>
                  <input type="text" min="1" id="total_count" value="30" style="ime-mode: disabled;">
                  <a class="add" href="javascript:;">+</a>
                 </span>（瓶）
        </div>

        <div class="dnsall">
            <div class="dnsts">
                订购天数：<span>16天</span>
                <a class="cxsd" href="javascript:void(0);">重新设定</a>
            </div>
            <p>规格：{{$product->bottle_type_name}}</p>
            <p>保质期：{{$product->guarantee_period}}天</p>
            <p>储藏条件：{{$product->guarantee_req}}</p>
            {{--<p>包装：玻璃瓶</p>--}}
            <p>配料：{{$product->material}}</p>
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
        <ul class="sppul">
            <li>
                <div class="spnum"><span class="spstart"><i></i><i></i><i></i><i></i><i></i></span>137*******125</div>
                <div class="pjxx">
                    牛奶配送人员很守时，每天按时配送，也很贴心的提醒我家里哈登三角符
                    可见哈登哈哈客和卡号的好多号喝酒肯定很
                </div>

            </li>
            <li>
                <div class="spnum"><span class="spstart"><i></i><i></i><i></i><i></i><i class="stno"></i></span>137*******125
                </div>
                <div class="pjxx">
                    牛奶配送人员很守时，每天按时配送，也很贴心的提醒我家里哈登三角符
                    可见哈登哈哈客和卡号的好多号喝酒肯定很
                </div>

            </li>
        </ul>
    </div>
    <div class="he50"></div>

    <div class="dnsbt clearfix">
        <button id="make_order" class="dnsb1"><i class="fa fa-check-circle"></i> 立即订购</button>
        <button id="submit_order" class="dnsb2"><i class="fa fa-cart-plus"></i> 加入购物车</button>
    </div>
@endsection
@section('script')

    <!-- Date picker and Date Range Picker-->
    <script src="<?=asset('js/plugins/datepicker/bootstrap-datepicker.js') ?>"></script>
    <script src="<?=asset('weixin/js/showfullcalendar.js')?>"></script>
    <script src="<?=asset('weixin/js/myweek.js')?>"></script>

    <script type="text/javascript">

        var obj = $('#uecontent');
        var content = '{{$product->uecontent}}';

        obj.html(content);

        $(obj).each(function (){
            var $this = $(this);
            var t = $this.text();
            $this.html(t.replace('&lt;', '<').replace('&gt;', '>'));
        })


        $(document).ready(function () {
            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                spaceBetween: 30,
            });
        });

        $('button#make_order').click(function(){
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

            console.log(send_data);

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/api/make_order_directly",
                data: send_data,
                processData: false,
                contentType: false,
                success: function (data) {
                    if(data.status == "success" && data.group_id)
                    {
                        window.location.href = SITE_URL+"weixin/querendingdan?group_id="+data.group_id;
                    } else {
                        if (data.redirect_path == "phone_verify" && data.group_id)
                        {
                            window.location.href = SITE_URL+"weixin/dengji?group_id="+data.group_id;
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

            console.log(send_data);

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/api/insert_order_item_to_cart",
                data: send_data,
                processData: false,
                contentType: false,
                success: function (data) {
                    if(data.status == "success")
                    {
                        show_success_msg("附加产品成功");
                        //go to shanpin liebiao
                        window.location.href = SITE_URL+"weixin/shangpinliebiao";
                    }
                },
                error: function (data) {
                    console.log(data);
                    show_warning_msg("附加产品失败");
                }
            });
        })
    </script>

@endsection



