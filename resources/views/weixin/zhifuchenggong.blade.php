@extends('weixin.layout.master')
@section('title','支付成功')
@section('css')

@endsection
@section('content')
    <div class="top">
        <h1>支付成功</h1>
        <a class="topa1" href="jvascript:void(0)">&nbsp;</a>
        <a class="topa2" href="jvascript:void(0)"></a></div>
    <div class="zfjg">
        <p align="center"><img src="images/cg.png"></p>
        <p align="center"><b class="cg">支付成功</b></p>
        <p align="center">（我们会马上安排客服进行核实！）</p>
        <p align="center">
        <form name="form1" method="get" action="dingdanxiangqing" style="text-align:center">
            <input type="hidden" value="{{$order_id}}" name="order"/>
            <button type="submit" class="jxzf">查看订单</button>
        </form>
    </div>
    @include('weixin.layout.footer')
@endsection

@section('script')

    <script ype="text/javascript">

        var current_menu = 2;

        $(document).ready(function () {
            set_current_menu();
        });

    </script>

@endsection