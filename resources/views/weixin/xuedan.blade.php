@extends('weixin.layout.master')
@section('title','续单')
@section('css')
    <link href="<?=asset('css/fullcalendar.min.css')?> " rel='stylesheet'/>
@endsection

@section('content')
    <header>
        @if(isset($type))
            <a class="headl fanh" href="{{ url('weixin/dingdanliebiao?type=').$type }}"></a>
        @else
            <a class="headl fanh" href="{{url('weixin/dingdanliebiao')}}"></a>
        @endif
        <h1>我的购物车</h1>

    </header>
    <div class="ordsl">
        <div class="addrli addrli2" onclick="onGoPage1();">
            @if(isset($customer) && ($customer != null))
                <div class="adrtop pa2t">
                    <p>{{$customer->name}} {{$customer->phone}}<br>
                        {{$customer->address}}
                    </p>
                </div>
            @else
                <div class="adrtop pa2t">
                    <p>请插入您的信息</p>
                </div>
            @endif
        </div>

        @forelse($wechat_order_products as $wop)

            <div class="ordtop clearfix">
                <img class="ordpro" src="<?=asset('img/product/logo/' . $wop->product->photo_url1)?>">
                <span class="ordlr"><button data-pid="{{$wop->id}}" class="edit_order_product">编辑</button></span>
                <div class="ord-r">
                    蒙牛纯甄酸奶低温
                    <br>
                    单价：{{$wop->product_price}}
                    <br>
                    订单数量：{{$wop->total_count}}瓶
                </div>
                <div class="ordye">金额：{{$wop->total_amount}}元</div>
            </div>
        @empty

        @endforelse

        <div class="ordbot">
            <textarea class="btxt" name="comment" id="comment" cols="" rows="" placeholder="备注"></textarea>
        </div>
    </div>

    <div class="he50"></div>
    <div class="dnsbt clearfix">
        @if(count($wechat_order_products) > 0)
            <button class="tjord tjord2" id="make_order" data-wpoids = "{{$wopids}}" >去付款</button>
        @else
            <button class="tjord tjord2" id="make_order" disabled>去付款</button>
        @endif
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        //make order based on cart
        $('#make_order').click(function () {
            var comment = $('#comment').val();
            var wopids = $(this).data('wopids');

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/api/make_order_from_wopids",
                data: {'comment': comment, 'wopids':wopids},
                success: function (data) {
                    console.log(data);
                    if (data.status == 'success') {
                        var order_id = data.order_id;
                        window.location = SITE_URL + "weixin/zhifuchenggong/?order=" + order_id;
                    } else {
                        if (data.message) {
                            show_err_msg(data.message);
                        }
                    }
                },
                error: function (data) {
                    console.log(data);
                },
            })
        });

        function onGoPage1() {
            window.location = SITE_URL + "weixin/dizhiliebiao";
        }

        //edit order product
        $('button.edit_order_product').click(function () {
            var wechat_order_product_id = $(this).data('pid');
            window.location = SITE_URL + "weixin/bianjidingdan/" + wechat_order_product_id;
        })

    </script>
@endsection