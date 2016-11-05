@extends('weixin.layout.master')
@section('title','新提交待审核')
@section('css')

@endsection
@section('content')

    <header>
        <a class="headl fanh" href="javascript:void(0)"></a>
        <h1>订单列表</h1>

        <script language="javascript">
            function onGoPage()
            {
                window.location = "天天送.html";
            }
        </script>
    </header>
    <div class="ordsl">
        <div class="ordnum">
            <span>2017-06-08</span>
            订单号：6048545
        </div>
        <div class="ordtop clearfix" onclick="onGoPage();">
            <img class="ordpro" src="images/zfx.jpg">
            <div class="ord-r">
                蒙牛纯甄酸奶低温
                <br>
                单价：
                <br>
                订单数量：32瓶
            </div>
            <div class="ordye">金额：162元</div>
        </div>
        <div class="ordtop clearfix" onclick="onGoPage();">
            <img class="ordpro" src="images/zfx.jpg">

            <div class="ord-r">
                蒙牛纯甄酸奶低温
                <br>
                单价：
                <br>
                订单数量：32瓶
            </div>
            <div class="ordye">金额：162元</div>
        </div>

        <div class="ordshz">
            <span class="shsp"><a href="审核完成.html">审核中</a></span></div>


    </div>
@endsection
@section('script')
    <script>

        $(".addSubtract .add").click(function () {
            $(this).prev().val(parseInt($(this).prev().val()) + 1);
        });
        $(".addSubtract .subtract").click(function () {
            if (parseInt($(this).next().val()) > 10) {
                $(this).next().val(parseInt($(this).next().val()) - 1);
                $(this).removeClass("subtractDisable");
            }
            if (parseInt($(this).next().val()) <= 10) {
                $(this).addClass("subtractDisable");
            }
        });
    </script>
@endsection
