@extends('weixin.layout.master')
@section('title','续单')
@section('css')

	<link href='css/fullcalendar.min.css' rel='stylesheet' />
	<link rel="stylesheet" href="css/swiper.min.css">
@endsection
@section('content')
	<header>
		<a class="headl fanh" href="javascript:void(0)"></a>
		<h1>我的购物车</h1>

	</header>
	<div class="ordsl">
		<div class="dnsli clearfix">
			<div class="dnsti">起送日期 &nbsp;</div><input class="qssj" name="" type="date">
		</div>
		<div class="ordtop clearfix">
			<img class="ordpro" src="images/zfx.jpg">

			<div class="ord-r">
				<input class="ordxz" name="" type="checkbox" value="" checked>
				蒙牛纯甄酸奶低温
				<br>
				单价：
				<br>
				订单数量：32瓶
			</div>
			<div class="ordye">金额：162元</div>
		</div>
		<div class="ordtop clearfix">
			<img class="ordpro" src="images/zfx.jpg">


			<div class="ord-r"><input class="ordxz" name="" type="checkbox" value="">
				蒙牛纯甄酸奶低温
				<br>
				单价：
				<br>
				订单数量：32瓶
			</div>
			<div class="ordye">金额：162元</div>
		</div>
	</div>
	<!-- <h3 class="dnh3">订奶计划预览</h3>
          <div id='calendar'></div> -->

	<div class="account clearfix">
		<div class="ac-l">
			共90瓶<br>
			享受：季单优惠
		</div>
		<div class="ac-r">
			<span>总计：￥474</span>
			<a class="" href="javascript:void(0)">结算</a>
		</div>
	</div>
	<div class="he50"></div>
@endsection
@section('script')


	<script src='js/moment.min.js'></script>
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
				editable: true,
				events: [
					{
						title: '2',
						start:'2016-09-28',
						//className:'ypsrl'

					},
					{
						//title: '2',
						start:'2016-09-28',
						rendering: 'background',
						color: '#00a040'
					},
					{
						title: '5',
						start:'2016-09-29',
					},
					{
						//title: '5',
						start:'2016-09-29',
						rendering: 'background',
						color: '#00a040'
					},
					{
						title: '3',
						start:'2016-09-30',
					},
					{
						//title: '3',
						start:'2016-09-30',
						rendering: 'background',
						color: '#00a040'
					}
				]
			});

		});
	</script>

	<script>

		$(".addSubtract .add").click(function() { $(this).prev().val(parseInt($(this).prev().val()) + 1);});
		$(".addSubtract .subtract").click(function() {
			if(parseInt($(this).next().val())>10){
				$(this).next().val(parseInt($(this).next().val()) - 1);
				$(this).removeClass("subtractDisable");}
			if(parseInt($(this).next().val())<=10){$(this).addClass("subtractDisable");} });
	</script>
@endsection