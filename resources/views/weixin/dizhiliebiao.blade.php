@extends('weixin.layout.master')
@section('title','地址列表')
@section('css')

@endsection
@section('content')
    <header>
        <a class="headl fanh" href="javascript:void(0)"></a>
        <h1>管理收货地址</h1>

    </header>

    <div class="addrbox">

        <div class="addrli">
            <div class="adrtop pa2t">
                <p>张天 4854545<br>广西壮族自治区</p>
                <p>17-2-2016</p></div>
            <div class="mrsz clearfix pa2t">
                <span class="adrdz"><input name="dzrad" type="radio" value="" checked>默认地址</span>
                <span class="adrsc">删除</span>
            </div>

        </div>


        <div class="addrli">
            <div class="adrtop pa2t">
                <p>张天 4854545<br>广西壮族自治区</p>
                <p>17-2-2016</p></div>
            <div class="mrsz clearfix pa2t">
                <span class="adrdz"><input name="dzrad" type="radio" value="">默认地址</span>
                <span class="adrsc">删除</span>
            </div>

        </div>


    </div>
    <div class="rqtc">
        <div class="rqtti rqtadr">确认要删除该地址吗？</div>


        <div class="rqbot">
            <span><a class="rqbot1" href="javascript:void(0)">确定</a></span>
            <span> <a class="rqbot2" href="javascript:void(0)">取消</a></span>
        </div>


    </div>
@endsection
@section('script')
    <script>
        $('.adrsc').click(function() {
            $('.rqtc').show();



        });
        $('.rqbot1').click(function() {
            $('.rqtc').hide();
        });
        $('.rqbot2').click(function() {
            $('.rqtc').hide();

        });
    </script>
@endsection