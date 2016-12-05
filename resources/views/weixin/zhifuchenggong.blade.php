@extends('weixin.layout.master')
@section('title','支付成功')
@section('css')

@endsection
@section('content')

    <header>
        <a class="headl fanh" href="{{url('weixin/shangpinliebiao')}}"></a>
        <h1>支付成功</h1>
    </header>

    <div class="zfjg">
        <p align="center"><img src="images/cg.png"></p>
        <p align="center"><b class="cg">支付成功</b></p>
        <p align="center">（我们会马上安排客服进行核实！）</p>
        <p align="center">

        @if($check == 'cpop')
            <form name="form1" method="get" action="{{url('weixin/dingdanxiangqing')}}" style="text-align:center">
                <input type="hidden" value="{{$order_id}}" name="order"/>
                <button type="submit" class="jxzf">查看订单</button>
            </form>
        @elseif($check == "op")
            <button type="button" onclick="alert('请用该手机号登录查看')" class="jxzf">查看订单</button>
        @else
            <a  href = "{{url('weixin/dengji')}}"><button type="button" class="jxzf">查看订单</button></a>
        @endif
    </div>
    @include('weixin.layout.footer')
@endsection

@section('script')

    <script type="text/javascript">

        var current_menu = 2;

        $(document).ready(function () {
            set_current_menu();
        });

    </script>

@endsection