@extends('weixin.layout.master')
@section('title','确认订单')
@section('css')
    <link href='<?=asset("weixin/css/fullcalendar.min.css")?>' rel='stylesheet'/>
@endsection
@section('content')

    <header>
        <a class="headl fanh" href="javascript:void(0)"></a>
        <h1>确认订单</h1>
    </header>
    <div class="ordsl">
        <input type="hidden" id="group_id" value="{{$group_id}}"/>
        <input type ="hidden" id="wxuser_id" value="{{$wxuser_id}}">
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
            <button class="tjord tjord2" id="make_order">去付款</button>
        @else
            <button class="tjord tjord2" disabled >去付款</button>
        @endif
    </div>
@endsection
@section('script')
    <script type="text/javascript">

        //make order based on cart
        $('#make_order').click(function () {
            var comment = $('#comment').val();
            var group_id =$('#group_id').val();

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/api/make_order_by_group",
                data: {'comment': comment, 'group_id':group_id},
                success: function (data) {
                    console.log(data);
                    if(data.status == 'success')
                    {
                        var order_id = data.order_id;
                        window.location = SITE_URL+"weixin/zhifuchenggong?order="+order_id;
                    } else {
                        if(data.message)
                        {
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
            var group_id = $('#group_id').val();
            var wechat_user_id = $('#wxuser_id').val();
            window.location = SITE_URL + "weixin/dizhiliebiao?user="+wechat_user_id+"&&group_id="+group_id;
        }

        //edit order product
        $('button.edit_order_product').click(function () {
            var wechat_order_product_id = $(this).data('pid');
            var group_id = $('#group_id').val();
            window.location = SITE_URL + "weixin/bianjidingdan?wechat_opid=" + wechat_order_product_id+"&&group_id="+group_id;
        })

    </script>
@endsection