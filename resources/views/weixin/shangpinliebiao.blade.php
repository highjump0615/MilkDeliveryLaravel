@extends('weixin.layout.master')
@section('title', '商品列表')

@section('css')
    <style>

        .swiper-container {
            width: 100%;
            height: 30px;
            margin: 20px auto;
        }

        .swiper-slide {
            min-width: 90px;
            margin-left: 7px;
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
            min-width: 100px;
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
    </div>

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
                            <div><a href="{{url('/weixin/shangpinliebiao?category='.$c->id)}}"
                                    onclick="">{{$c->name}}</a></div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    @if(isset($products))
        @foreach($products as $p)
            @if($p->category == $category)
                <dl class="pr_dl">
                    <div class="pr_img">
                        <a href="{{url('/weixin/tianjiadingdan?product='.$p->id)}}">
                            <img src="<?=asset('img/product/logo/' . $p->photo_url1)?>">
                        </a>
                    </div>
                    <div class="pr_ds">
                        <dt>{{$p->name}}</dt>
                        <dd>包装：{{$p->bottle_type_name}}</dd>
                        <dd>储存条件：{{$p->guarantee_req}}</dd>
                        <dd>￥{{$p->retail_price}}</dd>
                    </div>
                </dl>
            @endif
        @endforeach
    @endif

    @include('weixin.layout.footer')
@endsection

@section('script')
    <script>
        var swiper = new Swiper('.swiper-container', {
            spaceBetween: 30,
            slidesPerView: 4,
            freeMode: true,
        });

        var current_menu = 1;
        $(document).ready(function () {
            set_current_menu();

            $('.pr_img img').each(function () {
                var width = $(this).css('width');
                var height = parseInt(width);
                $(this).css('height', height);
            });
        });
    </script>
@endsection