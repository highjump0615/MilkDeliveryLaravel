@extends('weixin.layout.master')
@section('title','确认订单')
@section('content')

    <header>
        <a class="headl fanh" href="{{url('weixin/shangpinliebiao')}}"></a>
        <h1>确认订单</h1>
    </header>
    <div class="ordsl">
        <input type="hidden" id="group_id" value="{{$group_id}}"/>
        <input type ="hidden" id="wxuser_id" value="{{$wxuser_id}}">
        <div class="addrli addrli2" style="cursor:pointer" onclick="go_page_address_list();">
            @if(isset($primary_addr_obj) && ($primary_addr_obj != null))
                <div class="adrtop pa2t">
                    <p>{{$primary_addr_obj->name}} {{$primary_addr_obj->phone}}<br>
                        {{$primary_addr_obj->address}} {{$primary_addr_obj->sub_address}}
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
                    @endif元</div>
                <input type="hidden" id="total_amount" val="{{$wop->total_amount}}">
            </div>
        @empty

        @endforelse

        <div class="ordbot">
            <textarea class="btxt" name="comment" id="comment" cols="" rows="" placeholder="备注"></textarea>
        </div>
    </div>
    <div class="he50"></div>
    <div class="dnsbt clearfix">
        @if( isset($passed) && $passed == 1 && count($wechat_order_products) > 0 && isset($primary_addr_obj) && ($primary_addr_obj != null) )
            <button class="tjord tjord2" id="make_order">去付款</button>
        @else
            <button class="tjord tjord2" disabled >去付款</button>
        @endif
    </div>
@endsection
@section('script')
    <script type="text/javascript">

        $(document).ready(function(){
            @if(isset($message) && $message!="")
                var message = "{{$message}}";
                show_info_msg(message);
            @endif
        });
        //make order based on cart
        $('#make_order').click(function () {
            var comment = $('#comment').val();
            var group_id =$('#group_id').val();

            var order_bt = $(this);
            $(order_bt).prop('disabled', true);

            var total_amount = $('#total_amount').val();

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/api/make_order_by_group",
                data: {'comment': comment, 'group_id':group_id},
                success: function (data) {
                    console.log(data);
                    if(data.status == 'success')
                    {
                        $(order_bt).prop('disabled', true);

                        var order_id = data.order_id;
                        window.location = SITE_URL+"weixin/zhifuchenggong?order="+order_id;
                    } else {
                        if(data.message)
                        {
                            show_err_msg(data.message);
                            $(order_bt).prop('disabled', false);
                        }
                    }
                },
                error: function (data) {
                    console.log(data);
                },
            })
        });

        function go_page_address_list() {
            window.location = SITE_URL + "weixin/dizhiliebiao";
        }

        //edit order product
        $('button.edit_order_product').click(function () {
            var wechat_order_product_id = $(this).data('pid');
            var group_id = $('#group_id').val();
            window.location = SITE_URL + "weixin/bianjidingdan?wechat_opid=" + wechat_order_product_id;
        })

    </script>
@endsection