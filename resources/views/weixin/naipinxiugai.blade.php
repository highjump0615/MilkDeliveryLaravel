@extends('weixin.layout.master')
@section('title','产品修改')
@section('css')
    <link rel="stylesheet" href="<?=asset('weixin/css/pages/freeorder_calendar.css')?>">
@endsection
@section('content')
    <header>
        @if(isset($type))
            <a class="headl fanh" href="{{url('weixin/dingdanxiugai?order='.$order_id.'&&type='.$type)}}"></a>
        @else
            <a class="headl fanh" href="{{url('weixin/dingdanxiugai?order='.$order_id)}}"></a>
        @endif
        <h1>产品修改</h1>
    </header>

    <div class="ordtop pa2t clearfix">
        @if(isset($current_product_photo_url))
        <img id="pimg" class="ordpro" src="<?=asset('img/product/logo/' . $current_product_photo_url)?>">
        @endif
        <div class="ordyf">
            <span id="pname">{{ $current_product_name }}</span>
        </div>
        <div class="ordyf">
            <span>单价: <b id="product_price"> {{ $current_product_price }}</b></span>
            <span>剩余数量：<b id="product_count"> {{  $current_product_count }}</b></span>
        </div>
        <div class="ordyf">
            <span>现在金额：<b id="current_amount">{{ $current_product_amount }}</b>元</span>
        </div>
        <div class="ordye">
            <span>更改后金额：<b id="after_changed_amount">{{ $current_product_amount }}</b>元</span>
            <span>差额：<b id="left_amount">{{$current_order_remain_amount}}</b>元</span>
        </div>
    </div>

    <div class="dnsli  dnsli2 clearfix">
        <div class="dnsti">订奶数量：</div>
        <span class="minusplus">
            <a class="minus" href="javascript:;">-</a>
            <input type="text" min="1" id="changed_product_count" value="{{$current_product_count}}"
                         max="{{$current_product_count}}">
            <a class="plus" href="javascript:;">+</a>
        </span>（瓶）
    </div>

    @include('weixin.productdeliverytype')

    <div class="dnsli clearfix dnsli2">
        <p class="">选择奶品：</p>
        <div class="product_list">
            @foreach($products as $product)
                @if($product[0] != $current_product_id)
                    <div class="orddp pa2t clearfix">
                        @if(isset($type))
                            <a href="{{url('weixin/tianjiadingdan?product='.$product[0].'&previous=naipinxiugai&&type=').$type}}"><img class="ordpro img_select" src="<?=asset('img/product/logo/' . $product[2])?>"></a>
                        @else
                            <a href="{{url('weixin/tianjiadingdan?product='.$product[0].'&previous=naipinxiugai')}}"><img class="ordpro img_select" src="<?=asset('img/product/logo/' . $product[2])?>"></a>
                        @endif
                        <div class="spp spp1">
                            <p class="spname">{{$product[1]}}</p>
                            <p class="spname">{{$product[3]}}元</p>
                        </div>
                        <div class="spp spp2">
                            <input class="ordxz cart_check" name="" type="checkbox" data-id="{{$product[0]}}"/>
                        </div>
                        <div class="spp spp3">可换：{{$product[4]}}瓶</div>
                    </div>
                @endif
            @endforeach
        </div>

    </div>

    <div class="he50"></div>
    <div class="dnsbt clearfix">
        <button id="change_order_product" class="dnsb1"><i class="fa fa-check-circle"></i> 提交</button>
        <button id="cancel_change_order_product" class="dnsb2"><i class="fa fa-times-circle"></i> 取消</button>
    </div>
@endsection
@section('script')
    <script src="<?=asset('weixin/js/showfullcalendar.js')?>"></script>
    <script src="<?=asset('weixin/js/showmyweek.js')?>"></script>

    <script type="text/javascript">

        <?php echo "var products = " . json_encode($products); ?>

        var logo_base_url = "{{asset('img/product/logo/')}}";

        console.log(products);

        var order_id = "{{$order_id}}";
        var index = "{{$index}}";
        var current_product_amount = parseFloat("{{$current_product_amount}}");
        var current_order_remain_amount = parseFloat("{{$current_order_remain_amount}}");

        //origin product
        var current_product_id = "{{$current_product_id}}";
        var selected_product_id = "{{$current_product_id}}";

        var gnDeliveryType = parseInt("{{$current_delivery_type}}");
        var gstrCurProductName = "{{$current_product_name}}";
        var gstrCurProductCount = "{{$current_product_count}}";
        var gstrCurProductPhotoUrl = "{{$current_product_photo_url}}";
        var gstrCurProductPrice = "{{$current_product_price}}";
        var gstrCurProductAmount = "{{$current_product_amount}}";
        var gstrCurProductRemainAmount = "{{$current_order_remain_amount}}";

        var type = '';
        @if (!empty($type))
            type = "{{$type}}";
        @endif
        var errimg= "<?=asset('weixin/images/sb.png') ?>";

    </script>

    <script type="text/javascript" src="<?=asset('js/pages/order/order_bottle.js') ?>"></script>
    <script src="<?=asset('weixin/js/freeorder_calendar.js')?>"></script>
    <script src="<?=asset('weixin/js/pages/productinfo.js')?>"></script>
    <script src="<?=asset('weixin/js/pages/editproductinfo.js')?>"></script>
    <script src="<?=asset('weixin/js/pages/naipinxiugai.js')?>"></script>

@endsection