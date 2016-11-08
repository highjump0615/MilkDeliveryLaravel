@extends('weixin.layout.master')
@section('title','订单详情')
@section('css')
	<link href='css/fullcalendar.min.css' rel='stylesheet' />
@endsection
@section('content')

	<header>
		<a class="headl fanh" href="javascript:void(0)"></a>
		<h1>订单列表</h1>

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

		</div>

		@forelse($order->order_products as $op)
		<div class="ordtop clearfix">
			<img class="ordpro" src="<?=asset('img/product/logo/' . $op->product->photo_url1)?>">
			@if($order->status == App\Model\OrderModel\Order::ORDER_PASSED_STATUS  ||
			$order->status == App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS)
				<span class="ordlr"><a href="{{url('/weixin/dingdanxiugai?order-item=').$op->id}}">修改</a></span>
			@endif
			<div class="ord-r">
				{{$op->product_name}}
				<br>
				单价：{{$op->product_price}}元
				<br>
				订单数量：{{$op->total_count}}
			</div>
			<div class="ordye">金额：{{$op->total_amount}}元</div>
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
@endsection
@section('script')
	<script src='js/fullcalendar.min.js'></script>
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

				events: [
						@foreach($plans as $p)
					{
						title: "{{$p->product_name}} {{$p->changed_plan_count}}",
						start: '{{$p->deliver_at}}',
						className:'ypsrl',
						textColor: '#00cc00'

					},
					@endforeach
				],
			});

		});
	</script>
@endsection
