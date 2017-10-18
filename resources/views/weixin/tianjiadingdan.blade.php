<?php

$showOrderInfo = true;
if (isset($previous) && $previous == "naipinxiugai") {
    $showOrderInfo = false;
}

?>

@extends('weixin.layout.master')
@section('title','产品详情')
@section('css')
    <link rel="stylesheet" href="<?=asset('weixin/css/fullcalendar.min.css')?>">
    <link rel="stylesheet" href="<?=asset('weixin/css/pages/freeorder_calendar.css')?>">
@endsection

@section('content')

    <header>
        @if(isset($previous) && $previous == "none")
            <a class="headl fanh" href="{{url('weixin/shangpinliebiao')}}"></a>
        @else
            <a class="headl fanh" href="javascript:history.back();"></a>
        @endif
        <h1>产品详情</h1>
    </header>

    @include('weixin.productinfo', [
        'showOrderInfo' => $showOrderInfo,
    ])

    <!-- 奶品修改的选择奶品页面 -->
    @if (!(isset($previous) && $previous == "naipinxiugai"))
    <div class="dnsbt clearfix">
        @if (!isset($order_id))
            <button id="make_order" class="dnsb1"><i class="fa fa-check-circle"></i> 立即订购</button>
            <button id="submit_order" class="dnsb2"><i class="fa fa-cart-plus"></i> 加入购物车</button>
        @elseif (isset($order_id))
            <button id="add_order" data-order-id="{{$order_id}}" class="dnsb2"><i class="fa fa-plus-circle"></i> 加入订单</button>
        @endif
    </div>
    @endif

@endsection
@section('script')
    <script src="<?=asset('weixin/js/showmyweek.js')?>"></script>

    <script type="text/javascript">

    var errimg= "<?=asset('weixin/images/sb.png') ?>";
    var gap_day = 3;
    @if(!empty($gap_day))
        gap_day = parseInt("{{$gap_day}}");
    @endif
    var today = new Date("{{$today}}");
    var type = '';
    @if (!empty($type))
        type = "{{$type}}";
    @endif

    var contentDetail = '{{$product->uecontent}}';

    </script>

    <script type="text/javascript" src="<?=asset('js/pages/order/order_bottle.js') ?>"></script>
    <script src="<?=asset('weixin/js/freeorder_calendar.js')?>"></script>
    <script src="<?=asset('weixin/js/pages/productinfo.js')?>"></script>
    <script src="<?=asset('weixin/js/pages/tianjiadingdan.js?171017')?>"></script>
@endsection