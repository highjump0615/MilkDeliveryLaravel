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
	<div class="ordsl">
		<div class="ordnum">
			<span>2017-06-08</span>
			订单号：6048545
		</div>
		<div class="addrli addrli2">
			<div class="adrtop pa2t">
				<p>张天 4854545<br>广西壮族自治区</p>
				<p>17-2-2016</p></div></div>
		<!--<div class="ordnum">

        配送站：6048545
        <br>
         配送员：李生 6048545
        </div> -->
		<div class="ordnum lastcd">

			起送时间：6048545

		</div>

		<div class="ordtop clearfix">
			<img class="ordpro" src="images/zfx.jpg">
			<span class="ordlr">修改</span>
			<div class="ord-r">
				蒙牛纯甄酸奶低温
				<br>
				单价：
				<br>
				订单数量：32瓶
			</div>
			<div class="ordye">金额：162元</div>
		</div>
		<div class="ordtop clearfix lastcd">
			<img class="ordpro" src="images/zfx.jpg">
			<span class="ordlr">修改</span>

			<div class="ord-r">
				蒙牛纯甄酸奶低温
				<br>
				单价：
				<br>
				订单数量：32瓶
			</div>
			<div class="ordye">金额：162元</div>
		</div>
		<h3 class="dnh3">我的订奶计划</h3>
		<div id='calendar'></div>
		<div class="ordbot">
			<textarea class="btxt" name="" cols="" rows="" placeholder="备注"></textarea>
		</div>
	</div>
@endsection
@section('script')
	<script src="js/jquery-1.10.1.min.js"></script>
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
						start:'2016-09-01',
						className:'ypsrl'

					},
					{
						//title: '2',
						start:'2016-09-01',
						rendering: 'background',
						color: '#a3e2c3'
					},{
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
