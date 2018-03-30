@extends('weixin.layout.master')
@section('title','产品更改')
@section('css')
    <link rel="stylesheet" href="<?=asset('weixin/css/fullcalendar.min.css')?>">
    <link rel="stylesheet" href="<?=asset('weixin/css/pages/freeorder_calendar.css')?>">
@endsection

@section('content')

    <header>
        <a class="headl fanh" href="javascript:history.back();"></a>
        <h1>产品更改</h1>
    </header>

    @include('weixin.productinfo', [
        'showOrderInfo' => true,
        'showDayCount' => true,
    ])

    <div class="dnsbt clearfix">
        <button id="submit_order" class="dnsb1"><i class="fa fa-save"></i> 保存</button>
        <button id="cancel" class="dnsb2"><i class="fa fa-reply"></i> 取消</button>
    </div>
@endsection
@section('script')

    <!-- Date picker and Date Range Picker-->
    <script src="<?=asset('weixin/js/showmyweek.js')?>"></script>

    <script type="text/javascript">

        var errimg= "<?=asset('weixin/images/sb.png') ?>";
        var gap_day = 3;
        @if(!empty($gap_day))
            gap_day = parseInt("{{$gap_day}}");
        @endif
        var today = new Date("{{$today}}");

        var gbXudan = false;
        @if (!empty($for) && $for == "xuedan")
            gbXudan = true;
        @endif

        var gstrCurrentStart = new Date("{{$wop->start_at}}");
        var gnDeliveryType = parseInt("{{$wop->delivery_type}}");
        var previous = "{{$previous}}";

        var contentDetail = '{{$product->uecontent}}';

    </script>

    <script type="text/javascript" src="<?=asset('js/pages/order/order_bottle.js') ?>"></script>
    <script src="<?=asset('weixin/js/freeorder_calendar.js')?>"></script>
    <script src="<?=asset('weixin/js/pages/productinfo.js?180328')?>"></script>
    <script src="<?=asset('weixin/js/pages/editproductinfo.js')?>"></script>
    <script src="<?=asset('weixin/js/pages/bianjidingdan.js?180328')?>"></script>

@endsection



