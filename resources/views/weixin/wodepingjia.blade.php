@extends('weixin.layout.master')
@section('title','我的评价')
@section('css')

@endsection
@section('content')

    <div class="top">
        <h1>我的评价</h1>
        <a class="topa1" href="{{url('/weixin/gerenzhongxin')}}">&nbsp;</a>
    </div>

    @if(count($reviews) == 0)
        <p class="no_data"> 没有消息 </p>
    @else
        @foreach($reviews as $review)
            <div class="pj_t">
                订单号：{{\App\Model\OrderModel\Order::where('id',$review->order_id)->get()->first()->number}}</div>
            <div class="pj_img">
                <div class="swiper-container" style="height:100%">
                    <div class="swiper-wrapper">
                        @forelse(\App\Model\OrderModel\Order::find($review->order_id)->order_products as $op)
                            <div class="swiper-slide"><img class="bimg img-responsive"
                                                           src="{{asset('img/product/logo/'.$op->product->photo_url1)}}"
                                                           border="0"/></div>
                        @empty
                        @endforelse
                    </div>
                    <!-- Add Pagination -->
                    <div class="swiper-pagination"></div>
                </div>
                <!--<a href="#"><img src="images/23_03.png" border="0"></a>
                <a href="#"><img src="images/23_03.png" border="0"></a>
                <span><a href="#">共3种></a></span> -->
            </div>

            <div class="evali pa2">
                <div class="start"><b>整体评价</b>
                    <span @if($review->mark < 1) class="nostart" @endif></span>
                    <span @if($review->mark < 2) class="nostart" @endif></span>
                    <span @if($review->mark < 3) class="nostart" @endif></span>
                    <span @if($review->mark < 4) class="nostart" @endif></span>
                    <span @if($review->mark < 5) class="nostart" @endif></span>
                </div>
                <div class="evaxx">
                    &nbsp;&nbsp;&nbsp;&nbsp;{{$review->content}}
                </div>
            </div>
        @endforeach
    @endif
    <div class="he50"></div>
    @include('weixin.layout.footer')
@endsection
@section('script')
@endsection