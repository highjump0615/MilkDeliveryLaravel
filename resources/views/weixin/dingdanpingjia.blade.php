
@extends('weixin.layout.master')
@section('title','订单评价')
@section('css')

@endsection
@section('content')
    <div class="top">
        <h1>订单评价</h1>
        <a class="topa1" href="jvascript:void(0)">&nbsp;</a>
        <a class="topa2" href="jvascript:void(0)"></a></div>

    <div class="pj_t">订单号：602178888</div>

    <div class="pj_img">
        <a href="#"><img src="images/23_03.png" border="0"></a>
        <a href="#"><img src="images/23_03.png" border="0"></a>

        <span><a href="#">共3种></a></span>

    </div>


    <div class="evali pa2">

        <div class="start"><b>整体评价</b>
            <span onclick="level_click(1)"></span>
            <span onclick="level_click(2)"></span>
            <span onclick="level_click(3)"></span>
            <span onclick="level_click(4)"></span>
            <span class="nostart" onclick="level_click(5)"></span>
        </div>

    </div>

    <p align="center">
        <textarea name="textarea" class="pj_k" placeholder="评价"></textarea>
    </p>

    <p align="center"><input type="submit" name="Submit" value="提交" class="pj_tj"></p>

    @include('weixin.layout.footer');
@endsection
@section('script')
    <script src="js/jquery-1.10.1.min.js"></script>
    <script language="javascript">
        function level_click(level)
        {
            $(".start > span:gt(" + (level-1) + ")").addClass("nostart");
            $(".start > span:lt(" + (level) + ")").removeClass("nostart");
        }
    </script>
@endsection