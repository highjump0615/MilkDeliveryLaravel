@extends('weixin.layout.master')
@section('title','个人中心')
@section('css')
@endsection
@section('content')

    <div class="top">
        <h1>个人中心</h1>
        <a class="topa2" href="javascript:void(0)"></a>
    </div>

    <div class="cen_t">
        <img src="{{$user->image_url}}">
        <p>{{$user->name}}</p>
    </div>

    <div class="cen_1">
        金额<b>￥{{$remain_amount}}</b> 剩余数量 <b>{{$remaining_bottle_count}}</b>
    </div>

    <div class="cen_in">
        <h2>订单列表</h2>

        <ul class="cen_ul">
            <li><p>待审核</p>
                <a href="{{url('/weixin/dingdanliebiao?type=waiting')}}"><img src="<?=asset('/weixin/images/dfk.png')?>" border="0"></a></li>
            <li style="margin-left:25%"><p>在配送</p>
                <a href="{{url('/weixin/dingdanliebiao?type=on_delivery')}}"><img src="<?=asset('/weixin/images/yfk.png')?>" border="0"></a></li>
            <li style="margin-left:25%"><p>已完成</p>
                <a href="{{url('/weixin/dingdanliebiao?type=finished')}}"><img src="<?=asset('/weixin/images/wc.png')?>" border="0"></a></li>
        </ul>

        <ul class="cen_menu">
            <li class="boder_t"><a href="{{url('/weixin/dingdanrijihua?from=geren')}}">我的订单计划</a></li>
            <!--li><a href="{{url('/weixin/xinxizhongxin')}}">消息中心</a></li-->
            <li><span><a href="{{url('/weixin/toushu')}}">退订咨询</a></span><a href="{{url('/weixin/toushu')}}">投诉建议</a></li>
            <li><a href="{{url('/weixin/wodepingjia')}}">我的评价</a></li>
        </ul>

    </div>
    @include('weixin.layout.footer')
@endsection
@section('script')
    <script>
        var current_menu = 3;
        $(document).ready(function(){
            set_current_menu();
        });
    </script>
@endsection
