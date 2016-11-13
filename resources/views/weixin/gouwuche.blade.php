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
                    <img class="ordpro" src="<?=asset('img/product/logo/' . $c->order_item->product->photo_url1)?>">

                    <div class="ord-r">
                        <button class="ordxz remove" type="submit" style="background-image:url(<?=asset('/weixin/images/button_delete_icon.png')?>)"></button>
                        <input class="ordxz cart_check" name="" type="checkbox" data-cartid="{{$c->id}}" checked>
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
            <div class="ordtop clearfix">
                没有项目
            </div>
        @endforelse
    </div>
    <div class="account clearfix">
        <div class="ac-l">
            共{{$total_count}}瓶<br>
            享受：季单优惠<br>
            总计：￥{{$total_amount}}
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
        $('#process_cart').click(function(){
            var cart_ids = "";
            // get all checked carts
            $('input.cart_check:checked').each(function(){
                if(cart_ids == "")
                {
                    cart_ids = $(this).data('cartid');
                } else {
                    cart_ids +=","+$(this).data('cartid');
                }
            });
            if(!cart_ids)
                return;

            $.ajax({
                type:"POST",
                url: SITE_URL+"weixin/gouwuche/api/make_wop_group",
                data: {'cart_ids':cart_ids},
                success: function(data){
                    if(data.status == "success")
                    {
                        window.location = SITE_URL+"weixin/querendingdan";
                    } else {

                        if (data.redirect_path == "phone_verify")
                        {
                            window.location  = SITE_URL+"weixin/dengji";
                        }
                    }

                },
                error: function(data){
                    console.log(data);
                }
            })

        });
    </script>
@endsection