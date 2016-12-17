
@extends('weixin.layout.master')
@section('title','订单评价')

@section('css')
@endsection

@section('content')
    <div class="top">
        <h1>订单评价</h1>
        <a class="topa1" href="javascript:history.back();">&nbsp;</a>
        <a class="topa2" href="javascript:void(0)"></a></div>

    <div class="pj_t">订单号：{{$order->number}}</div>

    <div class="pj_img">
        <div class="swiper-container" style="height:100%">
            <div class="swiper-wrapper">
                @forelse($order->order_products as $op)
                    <div class="swiper-slide"><img class="bimg img-responsive" src="{{asset('img/product/logo/'.$op->product->photo_url1)}}" border="0"/></div>
                @empty
                @endforelse
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>

    </div>

    <input type="hidden" id="order_id" value="{{$order->id}}">
    <div class="evali pa2">

        <div class="start"><b>整体评价</b>
            <span onclick="level_click(1)"></span>
            <span onclick="level_click(2)"></span>
            <span onclick="level_click(3)"></span>
            <span onclick="level_click(4)"></span>
            <span onclick="level_click(5)"></span>
        </div>
    </div>

    <p align="center">
        <textarea id="content" name="textarea" class="pj_k" placeholder="评价"></textarea>
    </p>

    <p align="center"><button id="submit" onclick="sendPingjia()" class="pj_tj">提交</button></p>

    @include('weixin.layout.footer')
@endsection
@section('script')
    <script language="javascript">

        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            spaceBetween: 30,
        });


        var marks = 5;
        function level_click(level)
        {
            $(".start > span:gt(" + (level-1) + ")").addClass("nostart");
            $(".start > span:lt(" + (level) + ")").removeClass("nostart");
            marks = level;
        }

        function sendPingjia() {
            var order_id = $('#order_id').val();
            var content = $('#content').val();
            var url = SITE_URL + 'weixin/dingdanpingjia/addpingjia';
            var formData = {
                'order_id': order_id,
                'marks': marks,
                'contents': content,
            }
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    window.location.href = SITE_URL+"weixin/wodepingjia";
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }
    </script>
@endsection