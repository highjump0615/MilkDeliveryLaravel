@extends('weixin.layout.master')
@section('title','个人中心')
@section('css')

@endsection
@section('content')

    <div class="top">
        <h1>个人中心</h1>
        <a class="topa1" href="jvascript:void(0)">&nbsp;</a>
        <a class="topa2" href="jvascript:void(0)"></a></div>

    <div class="cen_t">
        <img src="images/11.jpg">
        <p>小明</p>

    </div>

    <div class="cen_1">
        金额<b>￥36.5</b> 剩余数量 <b>12</b>
    </div>

    <div class="cen_in">
        <h2>订单列表</h2>

        <ul class="cen_ul">
            <li><p>待付款</p>
                <a href="修改后的订单列表.html"><img src="images/dfk.png" border="0"></a></li>
            <li style="margin-left:25%"><p>已付款</p>
                <a href="修改后的订单列表.html"><img src="images/yfk.png" border="0"></a></li>
            <li style="margin-left:25%"><p>完成</p>
                <a href="修改后的订单列表.html"><img src="images/wc.png" border="0"></a></li>
        </ul>

        <ul class="cen_menu">
            <li class="boder_t"><a href="订单日计划修改-日历.html">我的订单计划</a></li>
            <li><a href="32消息中心.html">消息中心</a></li>
            <li><span><a href="31投诉建议.html">退订咨询</a></span><a href="31投诉建议.html">投诉建议</a></li>
            <li><a href="23我的评价.html">我的评价</a></li>
        </ul>

    </div>
    @include('weixin.layout.footer');
@endsection
@section('script')
@endsection
