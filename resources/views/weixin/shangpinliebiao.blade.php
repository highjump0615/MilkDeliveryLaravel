@extends('weixin.layout.master')
@section('title', '投诉建议');
@section('css')
    <link rel="stylesheet" href="css/swiper.min.css">
    <style>
        .swiper-container {
            width: 100%;
            height: 30px;
            margin: 20px auto;
        }

        .swiper-slide {
            text-align: center;
            background: #fff;

            /* Center slide text vertically */
            display: -webkit-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
        }

        /*RCU*/
        .swiper-slide div {
            width: 100%;
            text-align: center;
            line-height: 28px;
            padding: 0px 10px 0px 10px;
            background: #e1e1e1;
            border-radius: 10px;
            -moz-border-radius: 10px;
        }

        .swiper-slide div.current {
            border: solid 1px #e17c04;
            background: none
        }

        .swiper-slide div.current a {
            color: #e99d43
        }

    </style>



@endsection
@section('content')

    <div class="top">
        <h1>商品列表</h1>
        <a class="topa1" href="jvascript:void(0)">&nbsp;</a>
        <a class="topa2" href="jvascript:void(0)"></a></div>

    <div class="bann">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach($categories as $c)
                    @if($c->id == $category)
                        <div class="swiper-slide">
                            <div class="current"><a href="#">{{$c->name}}</a></div>
                        </div>
                    @else
                        <div class="swiper-slide">
                            <div><a href="{{url('/weixin/shangpinliebiao?category='.$c->id)}}" onclick="">{{$c->name}}</a></div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    @foreach($products as $p)
        @if($p->category == $category)
            <dl class="pr_dl">
                <img src="<?=asset('/img/product/logo/', $p->photo_url1)?>">
                <dt>{{$p->name}}</dt>
                <dd>包装：</dd>
                <dd>储存条件：</dd>
                <dd><b>￥4.8</b></dd>

            </dl>
        @endif
    @endforeach

    @include('weixin.layout.footer')
@endsection

@section('script')
    <script src="js/swiper.min.js"></script>
    <script>
        var swiper = new Swiper('.swiper-container', {
            spaceBetween: 30,
            slidesPerView: 4,
            freeMode: true,
        });
    </script>

    <script src='js/moment.min.js'></script>
@endsection