@extends('weixin.layout.master')
@section('title','登录页')
@section('css')

@endsection
@section('content')
    <header>
        <a class="headl fanh" href="javascript:void(0)"></a>
        <h1>注册</h1>

    </header>

    <div class="pa2">
        <img class="lglg" src="images/lglogo.jpg">
        <div class="lgbox">
            <h2 class="lgh2">塞茵苏订奶查询系统</h2>
            <div class="pa2">
                <div class="lgli">
                    <span class="lg-l">手机号码：</span>
                    <input class="lgin1" name="" type="text" placeholder="请输入订奶人的手机号码">
                </div>
                <div class="lgli">
                    <span class="lg-l">验 证 码：</span>
                    <input class="lgin2" name="" type="text" placeholder="请输入验证码">
                    <a class="fsyzm"  href="javascript:void(0);">发送验证码</a>
                </div>
                <a class="lgcx"  href="填写收货地址.html">点击查询</a>
            </div>
        </div>
        <div class="lgcopy">
            版权所有：内蒙古圣牧低温乳品有限公司<br>
            网站策划：宇盈科技
        </div>
    </div>
@endsection
@section('script')
    <script>

        $(".addSubtract .add").click(function() { $(this).prev().val(parseInt($(this).prev().val()) + 1);});
        $(".addSubtract .subtract").click(function() {
            if(parseInt($(this).next().val())>10){
                $(this).next().val(parseInt($(this).next().val()) - 1);
                $(this).removeClass("subtractDisable");}
            if(parseInt($(this).next().val())<=10){$(this).addClass("subtractDisable");} });
    </script>
@endsection
