@extends('weixin.layout.master')
@section('title','结算')
@section('content')

    <header class="top">
        <h1>我的购物车</h1>
    </header>
    <div class="ordsl">
        @forelse($carts as $c)
            <form method="post" action="{{url('/weixin/gouwuche/delete_cart')}}">
                <input type="hidden" class="cart_id" name="cart_id" value="{{$c->id}}">
                <div class="ordtop clearfix">
                    <div class="ord-l">
                        <input class="ordxz cart_check" name="" type="checkbox"
                               data-count="{{$c->order_item->total_count}}"
                               data-order-type="{{$c->order_item->order_type}}"
                               data-amount="{{$c->order_item->total_amount}}" data-cartid="{{$c->id}}" checked/>
                    </div>
                    <a href="{{url('weixin/bianjidingdan').'?wechat_opid='.$c->wxorder_product_id.'&&from=gouwuche'}}"><img
                                class="ordpro"
                                src="<?=asset('img/product/logo/' . $c->order_item->product->photo_url1)?>"></a>
                    <div class="ord-r">
                        <button class="ordxz remove" type="submit"><i class="fa fa-remove"></i></button>
                        {{$c->order_item->product->name}}
                        <br>
                        单价：{{$c->order_item->product_price}}
                        <br>
                        订单数量：{{$c->order_item->total_count}}瓶
                    </div>
                    <div class="ordye">金额：{{$c->order_item->total_amount}}元</div>
                </div>
            </form>
        @empty
            <div class="ordtop clearfix nocart">
                <p class="text-center">没有项目</p>
            </div>
        @endforelse

        @if(count($carts) != 0)
            <button id="del_selected" class="del_selected"><i class="fa fa-remove"></i> 删除所选</button>
        @endif

    </div>
    <div class="account clearfix">
        <div class="ac-l">
            共: <span id="total_count">{{$total_count}}</span>瓶<br>
            {{--享受：季单优惠<br>--}}
            总计： ￥<span id="total_amount">{{$total_amount}}</span>
        </div>

        <div class="ac-r">
            @if(count($carts)>0)
                <button id="process_cart">结算</button>
            @else
                <a href="#" class="btn" disabled>结算</a>
            @endif
        </div>
    </div>
    @include('weixin.layout.footer')
@endsection
@section('script')

    <script ype="text/javascript">

        var current_menu = 2;
        $(document).ready(function () {
            set_current_menu();
        });

        $('.cart_check').change(function () {

            var total_count = 0;
            var total_amount = 0;

            // get all checked carts
            $('input.cart_check:checked').each(function () {
                total_count += $(this).data('count');
                total_amount += $(this).data('amount');
            });

            $('#total_count').text(total_count);
            $('#total_amount').text(total_amount);

            if (total_count == 0) {
                $('#process_cart').prop('disabled', true);
            } else {
                $('#process_cart').prop('disabled', false);
            }

        });

        $('#del_selected').click(function () {

            var cart_ids = "";
            // get all checked carts
            $('input.cart_check:checked').each(function () {
                if (cart_ids == "") {
                    cart_ids = $(this).data('cartid');
                } else {
                    cart_ids += "," + $(this).data('cartid');
                }
            });
            if (!cart_ids)
                return;

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/gouwuche/api/delete_selected_wop",
                data: {'cart_ids': cart_ids},
                success: function (data) {
                    location.reload();
                },
                error: function (data) {
                    console.log(data);
                }
            })


        });

        function check_bottle_count_limit() {

            var total_amount_checked = 0;
            var max_order_type = 1;

            $('input.cart_check:checked').each(function () {
                total_amount_checked += parseInt($(this).data('count'));
                var order_type = parseInt($(this).data('order_type'));
                if (max_order_type < order_type) {
                    max_order_type = order_type;
                }
            });

            if (max_order_type == "{{\App\Model\OrderModel\OrderType::ORDER_TYPE_MONTH}}" && total_amount_checked < 30) {
                return true;
            } else if (max_order_type == "{{\App\Model\OrderModel\OrderType::ORDER_TYPE_SEASON}}" && total_amount_checked < 90) {
                return true;
            } else if (max_order_type == "{{\App\Model\OrderModel\OrderType::ORDER_TYPE_HALF_YEAR}}" && total_amount_checked < 180) {
                return true;
            }

            return false;
        }

        $('#process_cart').click(function () {
            var cart_ids = "";
            // get all checked carts
            $('input.cart_check:checked').each(function () {
                if (cart_ids == "") {
                    cart_ids = $(this).data('cartid');
                } else {
                    cart_ids += "," + $(this).data('cartid');
                }
            });
            if (!cart_ids)
                return;

            if (check_bottle_count_limit()) {
                show_warning_msg('订单数量总合得符合订单类型条件');
                return;
            }

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/gouwuche/api/make_wop_group",
                data: {'cart_ids': cart_ids},
                success: function (data) {
                    if (data.status == "success") {
                        window.location = SITE_URL + "weixin/querendingdan";
                    } else {

                        if (data.redirect_path == "phone_verify") {
                            window.location = SITE_URL + "weixin/dengji?to=queren";
                        }
                    }

                },
                error: function (data) {
                    console.log(data);
                }
            })

        });
    </script>
@endsection