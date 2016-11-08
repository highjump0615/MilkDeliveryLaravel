@extends('weixin.layout.master')
@section('title','首页')
@section('css')
    <link rel="stylesheet" href="<?=asset('weixin/css/swiper.min.css')?>">
@endsection
@section('content')
    <header>
        <a class="headl mesa" href="javascript:void(0)"></a>
        <div class="addr1">北京</div>
        <div class="seartop">
            <input type="search" name=""/>
        </div>

    </header>
    <div class="bann">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach($banners as $b)
                <div class="swiper-slide"><img class="bimg" src="<?=asset($b->image_url)?>"></div>
                @endforeach

            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
    <nav>
        <ul class="navi">
            <li><a href="{{url('/weixin/dingdanrijihua')}}"><img src="<?=asset('weixin/images/nav1.png')?>">订单计划</a></li>
            <li><a href="{{url('/weixin/toushu')}}"><img src="<?=asset('weixin/images/nav2.png')?>">咨询投诉</a></li>
            <li><a href="{{url('/weixin/dingdanliebiao')}}"><img src="<?=asset('weixin/images/nav3.png')?>">修改订单</a></li>
        </ul>
    </nav>
    <div class="boxer pa2t">
        <dl class="prob clearfix">
            <dt class="proti"><a href="javascript:void(0)">新品上市</a></dt>
            @forelse($products as $p)
                <dd class="prol"><a href="{{url('/weixin/tianjiadingdan?product='.$p->id)}}">
                    <img class="bimg" src="<?=asset('img/product/logo/'.$p->photo_url1)?>">
                    <h3 class="proh3">{{$p->name}}</h3>
                    <div class="proml">{{$p->bottle_type_name}}</div>
                    <div class="promon"><strong>￥4.8</strong>(人民币)</div>
                </a></dd>
            @empty
                <dd class="prol">
                <h3>没有商品</h3>
                </dd>
            @endforelse
        </dl>
        <dl class="prob clearfix">
            <dt class="proti"><a href="javascript:void(0)">促销活动</a></dt>
            <dd>
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        @foreach($promos as $p)
                            <div class="swiper-slide"><img class="bimg" src="<?=asset($p->image_url)?>"></div>
                        @endforeach
                    </div>
                    <!-- Add Pagination -->
                    <div class="swiper-pagination"></div>
                </div>

            </dd>
        </dl>
    </div>
    @include('weixin.layout.footer')
    <div class="adrtc">
        <ul class="adrtcul">
            <li class="on"><a href="javascript:void(0)">北京</a></li>
            <li><a href="javascript:void(0)">上海</a></li>
            <li><a href="javascript:void(0)">天津</a></li>
            <li><a href="javascript:void(0)">重庆</a></li>
            <li><a href="javascript:void(0)">河北</a></li>
            <li><a href="javascript:void(0)">山西</a></li>
            <li><a href="javascript:void(0)">河南</a></li>
            <li><a href="javascript:void(0)">北京</a></li>
            <li><a href="javascript:void(0)">上海</a></li>
            <li><a href="javascript:void(0)">天津</a></li>
            <li><a href="javascript:void(0)">重庆</a></li>
            <li><a href="javascript:void(0)">河北</a></li>
            <li><a href="javascript:void(0)">山西</a></li>
            <li><a href="javascript:void(0)">河南</a></li>
            <li><a href="javascript:void(0)">北京</a></li>
            <li><a href="javascript:void(0)">上海</a></li>
            <li><a href="javascript:void(0)">天津</a></li>
            <li><a href="javascript:void(0)">重庆</a></li>
            <li><a href="javascript:void(0)">河北</a></li>
            <li><a href="javascript:void(0)">山西</a></li>
            <li><a href="javascript:void(0)">河南</a></li>
        </ul>
    </div>
@endsection

@section('script')
    <!-- Swiper JS -->
    <script src="<?=asset('weixin/js/swiper.min.js')?>"></script>

    <!-- Initialize Swiper -->
    <script>

        var address = "{{$address}}";

        var current_menu = 0;
        set_current_menu();

        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            spaceBetween: 30,
        });
        $('.addr1').click(function () {
            $('.adrtc').stop().slideToggle();
        });

    </script>
@endsection