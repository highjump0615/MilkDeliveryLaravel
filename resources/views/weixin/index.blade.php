@extends('weixin.layout.master')
@section('title','首页')
@section('css')
@endsection
@section('content')
    <header>
        <a class="headl mesa" href="javascript:void(0)"></a>
        <div class="addr1" id="prov_title" style="cursor:pointer">北京</div>
        <div class="seartop">
            <form method="GET" action="{{url('weixin/shangpinliebiao')}}">
                <input type="search" name="search_product" placeholder="产品搜索"/>
            </form>
        </div>

    </header>
    <div class="bann">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach($banners as $b)
                    <div class="swiper-slide"><img class="bimg img-responsive" onClick="go_to_product({{$b->product_id}})" src="<?=asset($b->image_url)?>"></div>
                @endforeach

            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
    <nav>
        <ul class="navi">
            <li><a href="{{url('/weixin/dingdanrijihua')}}"><img src="<?=asset('weixin/images/nav1.png')?>">订单计划</a>
            </li>
            <li><a href="{{url('/weixin/toushu')}}"><img src="<?=asset('weixin/images/nav2.png')?>">咨询投诉</a></li>
            <li><a href="{{url('/weixin/dingdanliebiao?type=on_delivery')}}"><img src="<?=asset('weixin/images/nav3.png')?>">修改订单</a>
            </li>
        </ul>
    </nav>
    <div class="boxer pa2t">
        <dl class="prob clearfix">
            <dt class="proti"><a href="javascript:void(0)">新品上市</a></dt>
            @forelse($products as $p)
                <dd class="prol"><a href="{{url('/weixin/tianjiadingdan?product='.$p[0]->id).'&&previous=index'}}">
                        <div class="milk_img_div">
                            <img class="bimg img-responsive" src="<?=asset('img/product/logo/' . $p[0]->photo_url1)?>">
                        </div>
                        <h3 class="proh3">{{$p[0]->name}}</h3>
                        <div class="proml">{{$p[0]->bottle_type_name}}</div>
                        <div class="promon"><strong>￥{{$p[1]}}</strong>(人民币)</div>
                    </a></dd>
            @empty
                <dd class="prol">
                    <h3>没有商品</h3>
                </dd>
            @endforelse
        </dl>
        <dl class="prob under_banner clearfix">
            <dt class="proti"><a href="javascript:void(0)">促销活动</a></dt>
            <dd>
                @foreach($promos as $p)
                    @if($p->image_url)
                        <img class="bimg img-responsive" onClick = "go_to_product({{$p->product_id}})" src="<?=asset($p->image_url)?>"/>
                    @endif
                @endforeach
            </dd>
        </dl>
    </div>
    @include('weixin.layout.footer')

    @if(isset($addr_list))
        <div class="adrtc">
            <div class="adrtcul">
                <ul>
                    @foreach($addr_list as $province_name => $city_list)
                        <li class="dropdown province">
                            @if(isset($prov) && $prov!="" && $province_name == $prov)
                                <p class="dropbtn active">{{$province_name}}</p>
                            @else
                                <p class="dropbtn">{{$province_name}}</p>
                            @endif
                            <div class="dropdown-content">
                                @foreach($city_list as $city_name)
                                    @if(isset($prov) && $prov!="" && isset($city) && $city!="" && $province_name == $prov && $city_name == $city)
                                        <p class="city active" data-province="{{$province_name}}">{{$city_name}}</p>
                                    @else
                                        <p class="city" data-province="{{$province_name}}">{{$city_name}}</p>
                                    @endif
                                @endforeach
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

@endsection

@section('script')

    <!-- Initialize Swiper -->
    <script>

        var current_menu = 0;
        set_current_menu();

        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            spaceBetween: 30,
        });


        $(document).ready(function () {
            var address = "{{$address}}";

            if(!address)
            {
//                show_warning_msg('选择您的地址');
                alert('选择您所在的区域')
            } else {
                var province_name = address.split(' ')[0];
                $('#prov_title').text(province_name);
            }


            $('.milk_img_div').each(function () {

                var height = parseInt($(this).css('width'));
                $(this).css('height', height);

                var img = $(this).find('img');
                $(img).css('height', height);
            });


        });


        $('.addr1').click(function () {
            $('.adrtc').stop().slideToggle();
        });

        $('p.city').click(function () {
            var prov = $(this).data('province');
            var city = $(this).text();
            var city_obj = $(this);
            var prov_obj = $(this).parent().parent().find('p.dropbtn');

            //remove active class from all city
            $('p.city').each(function () {
                $(this).removeClass('active');
            });
            $('p.dropbtn').each(function () {
                $(this).removeClass('active');
            });

            $.ajax({
                type: "POST",
                url: SITE_URL + "weixin/api/set_session_address",
                data: {"province": prov, "city": city},
                success: function (data) {
                    if (data.status == "success") {
                        show_success_msg('地址已更新');
                        $(city_obj).addClass('active');
                        $(prov_obj).addClass('active');
                        $('#prov_title').text(prov);
                        $('.adrtc').delay(2000).fadeOut(200);
                    } else {
                        if(data.message)
                        {
                            show_err_msg(data.message);
                        }
                    }
                },
                error: function (data) {
                },
            })
        });

        function go_to_product(pid)
        {
            location.href = SITE_URL+"weixin/tianjiadingdan?product="+pid+'&&previous=index';
        }

    </script>
@endsection