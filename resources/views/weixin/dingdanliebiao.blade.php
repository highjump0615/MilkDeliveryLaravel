@extends('weixin.layout.master')
@section('title','新提交待审核')
@section('content')
    <header>
        <a class="headl fanh" href="javascript:history.back();"></a>
        <h1>订单列表</h1>
    </header>
    @forelse($orders as $o)
        <div class="ordsl">
            <div class="ordnum">
                <span>{{$o->ordered_at}}</span>
                <label>订单号：{{$o->number}}</label>&emsp;<label>状态: {{$o->status_name}}</label>
            </div>
            @forelse($o->order_products as $op)
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

                <!--<span class="shsp">
                    <a href="{{url('/weixin/dingdanxiangqing?order='.$o->id)}}">{{$o->status_name}}</a>
                </span>-->
            </div>
        </div>
    @empty
    @endforelse
@endsection
@section('script')
@endsection
