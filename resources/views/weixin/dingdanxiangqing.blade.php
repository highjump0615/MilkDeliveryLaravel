@extends('weixin.layout.master')
@section('title','订单详情')
@section('css')
	<link href='css/fullcalendar.min.css' rel='stylesheet' />
@endsection
@section('content')

	<header>
		<a class="headl fanh" href="javascript:history.back();"></a>
		<h1>订单详情</h1>
		@if($order->status == \App\Model\OrderModel\Order::ORDER_PASSED_STATUS ||
                    $order->status == \App\Model\OrderModel\Order::ORDER_NOT_PASSED_STATUS ||
                    $order->status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS)
			<a class="headr" href="{{url('/weixin/dingdanxiugai?order='.$order->id)}}">修改</a>
		@endif
	</header>
	@if(isset($order))
	<div class="ordsl">
		<div class="ordnum">
			<span>{{$order->ordered_at}}</span>
			订单号 : {{$order->number}} &emsp; 状态 : {{$order->status_name}}
		</div>
		<div class="addrli2">
			<div class="adrtop pa2t">
				<p>{{$order->customer_name}} {{$order->phone}}<br>{{$order->address}}</p>
			</div>
		</div>
		<div class="ordnum lastcd">
			<span>订单金额: {{ $order->total_amount }}</span>
			@if($order->status == \App\Model\OrderModel\Order::ORDER_PASSED_STATUS ||
                    $order->status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS)
				奶站: {{$order->station_name}} &emsp; 配送员: {{$order->milkman->name}} {{$order->milkman->phone}}
			@endif
		</div>

		@forelse($order->order_products as $op)
		<div class="ordtop clearfix">
			<img class="ordpro" src="<?=asset('img/product/logo/' . $op->product->photo_url1)?>">
			<div class="ord-r">
				{{$op->product_name}}
				<br>
				单价：{{$op->product_price}}元
				<br>
				订单数量：{{$op->total_count}}
			</div>
			<div class="ordye">
				@if($order->status == \App\Model\OrderModel\Order::ORDER_PASSED_STATUS ||
                                    $order->status == \App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS)
					开始日期: {{$op->start_at}}&emsp;
				@endif
				金额：{{$op->total_amount}}元</div>
		</div>
		@empty
			没有项目
		@endforelse
		<h3 class="dnh3">我的订奶计划</h3>
		<div id='calendar'></div>
		<div class="ordbot">
			<textarea class="btxt" name="" cols="" rows="" placeholder="备注" >{{$comment}}</textarea>
		</div>
	</div>
	@else
		<p>没有数据</p>
	@endif

	@include('weixin.layout.footer')
@endsection
@section('script')
	<script src="<?=asset('weixin/js/fullcalendar.min.js')?>"></script>
	<script type="text/javascript">
		$(function() {
			$('#calendar').fullCalendar({
				header: {
					left: 'prev',
					center: 'title',
					right: 'next'
				},
				firstDay:0,
				editable: false,
				now: "{{$today}}",
				events: [
						@foreach($plans as $p)
					{
						title: "{{$p->product_simple_name}} {{$p->changed_plan_count}}",
						start: '{{$p->deliver_at}}',
						className:'ypsrl',
						textColor: '#00cc00'

					},
					@endforeach
				]
			});

		});
	</script>
@endsection
