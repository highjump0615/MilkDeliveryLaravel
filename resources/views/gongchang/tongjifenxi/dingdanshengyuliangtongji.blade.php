@extends('gongchang.layout.master')
@section('css')
	<link href="<?=asset('css/plugins/datepicker/datepicker3.css') ?>" rel="stylesheet">
@endsection
@section('content')
	@include('gongchang.theme.sidebar')
	<div id="page-wrapper" class="gray-bg dashbard-1">
		@include('gongchang.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li>
					<a href="">统计分析</a>
				</li>
				<li class="active"><strong>订单剩余量统计</strong></li>
			</ol>
		</div>
		<div class="row">
			<!--Table-->
			<div class="ibox-content">
				<div class="feed-element">
					<div class="col-md-3">
						<label>奶站名称:</label>
						<input type="text" id="station_name" value="{{$station_name}}">
					</div>
					{{--<div class="col-md-3">--}}
					{{--<label>编号:</label>--}}
					{{--<input type="text" id="">--}}
					{{--</div>--}}
					<div class="col-md-3">
						<label>区域:</label>
						&nbsp;
						<select data-placeholder="" class="chosen-select" id="area_name" tabindex="2" style="width: 180px; height: 30px;">
							<option value="">全部</option>
							@foreach($address as $addr)
								@if($addr->name == $area_name)
									<option selected value="{{$addr->name}}">{{$addr->name}}</option>
								@else
									<option value="{{$addr->name}}">{{$addr->name}}</option>
								@endif
							@endforeach
						</select>
					</div>

					<div class="col-lg-4" id="date_1">
						<label class="col-md-2 control-label" style="padding-top:7px;">日期:</label>
						<div class="input-group date col-md-6">
							<input type="text" class="form-control" id="end_date" value="{{$end_date}}"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>

					{{--<div class="form-group col-md-4" id="data_range_select">--}}
						{{--<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>--}}
						{{--<div class="input-daterange input-group col-md-8" id="datepicker">--}}
							{{--<input type="text" class="input-sm form-control" name="start" id="start_date" value="{{$start_date}}"/>--}}
							{{--<span class="input-group-addon">至</span>--}}
							{{--<input type="text" class="input-sm form-control" name="end" id="end_date" value="{{$end_date}}"/>--}}
						{{--</div>--}}
					{{--</div>--}}
					<div class="col-md-2"  style="padding-top:5px;">
						<button type="button" id="search" class="btn btn-success btn-md">筛选</button>
						&nbsp;
						<button class="btn btn-success btn-outline btn-m-d" data-action="export_csv">导出</button>
						&nbsp;
						<button class="btn btn-outline btn-success btn-m-d" data-action="print">打印</button>
					</div>
				</div>
			</div>

			<div class="ibox float-e-margins">
				<div class="ibox-content">

					<table id="order_type_table" class="footable table table-bordered" data-page-size="{{$count*2+6}}">
						<thead>
						<tr>
							<th rowspan="2" data-sort-ignore="true">序号</th>
							<th rowspan="2" data-sort-ignore="true">区域</th>
							<th rowspan="2" data-sort-ignore="true">分区</th>
							<th rowspan="2" data-sort-ignore="true">奶站名称</th>
							<th rowspan="2" data-sort-ignore="true">奶品名称</th>
							<th colspan="2" data-sort-ignore="true">月单</th>
							<th colspan="2" data-sort-ignore="true">季单</th>
							<th colspan="2" data-sort-ignore="true">半年单</th>
							<th colspan="2" data-sort-ignore="true">合计</th>
						</tr>
						<tr>
							<th data-sort-ignore="true">总量</th>
							<th data-sort-ignore="true">剩余量</th>
							<th data-sort-ignore="true">总量</th>
							<th data-sort-ignore="true">剩余量</th>
							<th data-sort-ignore="true">总量</th>
							<th data-sort-ignore="true">剩余量</th>
							<th data-sort-ignore="true">总量</th>
							<th data-sort-ignore="true">剩余量</th>
						</tr>
						</thead>
						<tbody>
						<?php $i = 0; ?>
						@foreach($stations as $st)
							<?php $i++; $j =0; ?>
							@foreach($st->product as $p)
								<?php $j++; ?>
								<tr class="milk">
									@if($j == 1)
										<td rowspan="{{count($st->product)+3}}">{{$i}}</td>
										<td rowspan="{{count($st->product)+3}}">{{$st->city}}</td>
										<td rowspan="{{count($st->product)+3}}">{{$st->district}}</td>
										<td rowspan="{{count($st->product)+3}}">{{$st->name}}</td>
									@endif
									<td>{{$p->name}}</td>
									<td class="total">{{$p->t_yuedan}}</td>
									<td class="remain">{{$p->t_yuedan - $p->r_yuedan}}</td>
									<td class="total">{{$p->t_jidan}}</td>
									<td class="remain">{{$p->t_jidan - $p->r_jidan}}</td>
									<td class="total">{{$p->t_banniandan}}</td>
									<td class="remain">{{$p->t_banniandan - $p->r_banniandan}}</td>
									<td class="f_total"></td>
									<td class="f_remain"></td>
								</tr>
							@endforeach
							<tr class="milk">
								<td>订单产品数量合计</td>
								<td class="total">{{$st->t_yuedan}}</td>
								<td class="remain">{{$st->t_yuedan - $st->r_yuedan}}</td>
								<td class="total">{{$st->t_jidan}}</td>
								<td class="remain">{{$st->t_jidan - $st->r_jidan}}</td>
								<td class="total">{{$st->t_banniandan}}</td>
								<td class="remain">{{$st->t_banniandan - $st->r_banniandan}}</td>
								<td class="f_total"></td>
								<td class="f_remain"></td>
							</tr>
							<tr class="milk_amount">
								<td>单数合计</td>
								<td class="total">{{$st->t_yuedan/30}}</td>
								<td class="remain">{{round((($st->t_yuedan-$st->r_yuedan)/30),2)}}</td>
								<td class="total">{{$st->t_jidan/90}}</td>
								<td class="remain">{{round((($st->t_jidan-$st->r_jidan)/90),2)}}</td>
								<td class="total">{{$st->t_banniandan/180}}</td>
								<td class="remain">{{round((($st->t_banniandan-$st->r_banniandan)/180),2)}}</td>
								<td class="f_total"></td>
								<td class="f_remain"></td>
							</tr>
							<tr class="milk_amount">
								<td>订单金额合计</td>
								<td class="total">{{$st->t_yuedan_amount}}</td>
								<td class="remain">{{$st->t_yuedan_amount - $st->r_delivered_yuedan_amount}}</td>
								<td class="total">{{$st->t_jidan_amount}}</td>
								<td class="remain">{{$st->t_jidan_amount - $st->r_delivered_jidan_amount}}</td>
								<td class="total">{{$st->t_banniandan_amount}}</td>
								<td class="remain">{{$st->t_banniandan_amount - $st->r_delivered_banniandan_amount}}</td>
								<td class="f_total"></td>
								<td class="f_remain"></td>
							</tr>
						@endforeach
						</tbody>
						<tfoot>
						<tr>
							<td colspan="13">
								<ul class="pagination pull-right"></ul>
							</td>
						</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
	</div>
@endsection

@section('script')
	<script src="<?=asset('js/plugins/datepicker/bootstrap-datepicker.js') ?>"></script>

	<script type="text/javascript">
		$(document).ready(function() {
			$('#order_type_table tr.milk').each(function () {
				var f_total = 0;
				var f_remain = 0;
				$(this).find('.total').each(function () {
					f_total += parseInt($(this).text());
				})
				$(this).find('.remain').each(function () {
					f_remain += parseInt($(this).text());
				})
				$(this).find('.f_total').html(f_total);
				$(this).find('.f_remain').html(f_remain);
			})

			$('#order_type_table tr.milk_amount').each(function () {
				var f_total = 0;
				var f_remain = 0;
				$(this).find('.total').each(function () {
					f_total += parseFloat($(this).text());
				})
				$(this).find('.remain').each(function () {
					f_remain += parseFloat($(this).text());
				})
				$(this).find('.f_total').html(f_total.toFixed(2));
				$(this).find('.f_remain').html(f_remain.toFixed(2));
			})
			$('.footable').footable();
		});

		$(document).on('click','#search',function () {
			var station_name = $('#station_name').val();
			var area_name = $('#area_name option:selected').val();
			var end_date = $('#end_date').val();
			window.location.href = SITE_URL+"milk/public/gongchang/tongjifenxi/dingdanshengyuliangtongji/?station_name="+station_name+"&area_name="+area_name+"&end_date="+end_date+"";
		})

		$('#date_1 .input-group.date').datepicker({
			todayBtn: "linked",
			keyboardNavigation: false,
			forceParse: false,
			calendarWeeks: false,
			autoclose: true
		});

		$('#data_range_select .input-daterange').datepicker({
			keyboardNavigation: false,
			forceParse: false,
			autoclose: true
		});

		$('button[data-action = "print"]').click(function () {

			var sendData = [];

			var printContents;

			printContents = document.getElementById("order_type_table").outerHTML;
			var originalContents = document.body.innerHTML;
			document.body.innerHTML = printContents;

			window.print();
			document.body.innerHTML = originalContents;
			location.reload();
		});

		$('button[data-action = "export_csv"]').click(function () {

			var sendData = [];

			var i = 0;
			//send order data
			$('#order_type_table thead tr').each(function () {
				var tr = $(this);
				var trdata = [];

				var j = 0;
				$(tr).find('th').each(function () {
					var td = $(this);
					var td_data = td.html().toString().trim();
					td_data =td_data.split("<");
					// if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
					//     td_data = "";
					trdata[j] = td_data[0];
					j++;
				});
				sendData[i] = trdata;
				i++;
			});

			$('#order_type_table tbody tr').each(function () {
				var tr = $(this);
				var trdata = [];

				var j = 0;
				$(tr).find('td').each(function () {
					var td = $(this);
					var td_data = td.html().toString().trim();
					if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
						td_data = "";
					trdata[j] = td_data;
					j++;
				});
				sendData[i] = trdata;
				i++;
			});

			var send_data = {"data": sendData};
			console.log(send_data);

			$.ajax({
				type: 'POST',
				url: API_URL + "export",
				data: send_data,
				success: function (data) {
					console.log(data);
					if (data.status == 'success') {
						var path = data.path;
						location.href = path;
					}
				},
				error: function (data) {
					//console.log(data);
				}
			})
		});
	</script>
@endsection