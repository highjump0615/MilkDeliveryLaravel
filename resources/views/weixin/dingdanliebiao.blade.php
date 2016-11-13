@extends('weixin.layout.master')
@section('title','订单列表')
@section('content')
    <header>
        @if(isset($set_type))
            <a class="headl fanh" href="{{url('weixin/gerenzhongxin')}}"></a>
        @else
            <a class="headl fanh" href="{{url('weixin/qianye')}}"></a>
        @endif
        <h1>订单列表</h1>
    </header>
    @forelse($orders as $o)
        <div class="ordsl">
            <div class="ordnum">
                <span>{{$o->ordered_at}}</span>
                <label>订单号：{{$o->number}}</label>&emsp;<label>状态: {{$o->status_name}}</label>
            </div>
            @forelse($o->order_products as $op)
                <a href="{{url('/weixin/dingdanxiangqing?order='.$o->id)}}">
                <div class="ordtop clearfix">
                    <img class="ordpro" src="<?=asset('img/product/logo/' . $op->product->photo_url1)?>">
                    <div class="ord-r">
                        {{$op->product_name}}
                        <br>
                        单价：{{$op->product_price}}
                        <br>
                        订单数量：{{$op->total_count}}

                    </div>
                    <div class="ordye">金额：{{$op->total_amount}}元</div>
                </div>
                </a>
            @empty
                <div>没有订单项目</div>
            @endforelse

            <div class="ordshz">
                @if($o->status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS || $o->status == \App\Model\OrderModel\Order::ORDER_FINISHED_STATUS)
                    <span class="shsp">
                        <a href="{{url('/weixin/api/show_xuedan?order='.$o->id)}}">续单</a>
                    </span>
                @endif

                @if($o->status == \App\Model\OrderModel\Order::ORDER_FINISHED_STATUS)
                    <span class="shsp">
                        <a href="{{url('/weixin/dingdanpingjia?order='.$o->id)}}">评价</a>
                    </span>
                @endif

                @if($o->status == \App\Model\OrderModel\Order::ORDER_PASSED_STATUS ||
                    $o->status == \App\Model\OrderModel\Order::ORDER_NOT_PASSED_STATUS ||
                    $o->status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS)
                    <span class="shsp">
                        <a href="{{url('/weixin/dingdanxiangqing?order='.$o->id)}}">修改</a>
                    </span>
                @endif
                @if($o->status == \App\Model\OrderModel\Order::ORDER_WAITING_STATUS)
                    <span class="shsp">
                            <a href="{{url('/weixin/dingdanxiangqing?order='.$o->id)}}">{{$o->status_name}}</a>
                        </span>
                @endif
            </div>
        </div>
    @empty
    @endforelse
    @include('weixin.layout.footer')
@endsection
@section('script')
@endsection
