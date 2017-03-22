@extends('gongchang.layout.master')
@section('css')
@endsection
@section('content')
	@include('gongchang.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('gongchang.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li>
					<a href="">生产管理</a>
				</li>
				<li class="active">
					<a href={{URL::to('/gongchang/shengchan/naizhanpeisong')}}>奶站配送管理</a>
				</li>
				<li class="active">
					<strong>奶站收货确认</strong>
				</li>
			</ol>
		</div>
			<div class="row border-bottom">
			</div>
			<div class="row">	
				<!--Table-->
				<div class="ibox-content">
					<div class="ibox-content">
						<div class="feed-element">
							<div class="col-md-3">
								<label>奶站名称:</label>
								<input type="text" id="station_name" class="form-control" value="{{$station_name}}" style="width: 180px; display: inline">
							</div>
							<div class="col-md-3">
								<label style="display: inline">编号:</label>
								<input type="text" id="station_number" class="form-control" value="{{$station_number}}" style="width: 180px; display: inline">
							</div>
							<div class="col-md-3">
								<label>区域:</label>
								<input type="text" id="address" class="form-control" value="{{$address}}" style="width: 180px; display: inline">
								{{--&nbsp;--}}
								{{--<select data-placeholder="" class="chosen-select form-control" tabindex="2" style="width: 180px; display: inline">--}}
								{{--<option value="全部">全部</option>--}}
								{{--<option value="北京">北京</option>--}}
								{{--<option value="河北">河北</option>--}}
								{{--</select>--}}
							</div>
							<div class="col-md-3" id="date_2">
								<label class="col-md-4 control-label" style="padding-top: 7px;">签收日期:</label>
								<div class="input-group date col-md-8">
									<input type="text" id="date" class="form-control" value="{{$current_date}}"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								</div>
							</div>
						</div>
						<div class="feed-element">

							<div class="col-md-offset-10 col-md-2"  style="padding-top:5px;">
								<button type="button" id="search" class="btn btn-success btn-md">筛选</button>
								&nbsp;
								<button class="btn btn-success btn-outline btn-m-d" data-action="expert_csv">导出</button>
								&nbsp;
								<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
							</div>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

						<table id="table1" class="table table-bordered" id="by_station">
							<thead>
							<tr>
								<th data-sort-ignore="true">序号</th>
								<th data-sort-ignore="true">区域</th>
								<th data-sort-ignore="true">奶站名称</th>
								<th data-sort-ignore="true">奶品</th>
								<th data-sort-ignore="true">计划订单(瓶)</th>
								<th data-sort-ignore="true">站内零售（瓶）</th>
								<th data-sort-ignore="true">试饮赠品（瓶）</th>
								<th data-sort-ignore="true">团购业务（瓶）</th>
								<th data-sort-ignore="true">渠道销售(瓶)</th>
								<th data-sort-ignore="true">计划生产量</th>
								<th data-sort-ignore="true">配送变化量</th>
								<th data-sort-ignore="true">实际发货量</th>
								{{--<th data-sort-ignore="true">操作</th>--}}
							</tr>
							</thead>
							<tbody>
							<?php $i=0; ?>
							@foreach($DSPlan_info as $di)
								<?php $i++; $j=0; ?>
								@foreach($di->station_plan as $ds)
									<?php $j++; ?>
									<tr id="tablerow{{$i}}" value="{{$ds->product_id}}" order="{{$i}}">
										@if($j==1)
											<td rowspan="{{count($di->station_plan)}}">{{$i}}</td>
											<td rowspan="{{count($di->station_plan)}}">{{$di->area}}</td>
											<td rowspan="{{count($di->station_plan)}}">{{$di->name}}</td>
										@endif
										<td>{{$ds->product_name}}</td>
										<td>{{$ds->order_count}}</td>
										<td>{{$ds->retail}}</td>
										<td>{{$ds->test_drink}}</td>
										<td>{{$ds->group_sale}}</td>
										<td>{{$ds->channel_sale}}</td>
										<td>{{$ds->actual_count}}</td>
										<td>{{$ds->diff}}</td>
										<td class="confirm_count" id="confirm{{$i}}{{$ds->product_id}}" value="{{$ds->product_id}}">{{$ds->actual_count}}</td>
										@if($j==1)
											{{--已发货--}}
											{{--<td rowspan="{{count($di->station_plan)}}"><i class="fa fa-pencil"></i></td>--}}
										@endif
										<input type="hidden" id="station_id{{$i}}" value="{{$di->id}}">
										<input type="hidden" id="name_field" value="{{$di->name}}">
										<input type="hidden" id="number_field" value="{{$i}}">
										<input type="hidden" id="area_field" value="{{$di->area}}">
									</tr>
								@endforeach
							@endforeach
							</tbody>
						</table>
                    </div>
                </div>
			</div>
	</div>
@endsection

@section('script')
    <script type="text/javascript">
		$('#date_2 .input-group.date').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: false,
            autoclose: true
        });
		$(document).ready(function() {
			$('.footable').footable();
		});

		$('#search').click(function () {
			var station_name = $('#station_name').val();
			var station_number = $('#station_number').val();
			var address = $('#address').val();
			var date = $('#date').val();
			window.location.href = SITE_URL+"milk/public/gongchang/shengchan/naizhanpeisong/naizhanshouhuoqueren/?station_name="+station_name+"&date="+date+"&station_number="+station_number+"&address="+address+"";
		})

//		$('#date_picker').on("change",function(){
//			var value = $(this).val();
//			var ymd = value.split("/",3);
//			var date = ymd[2]+"-"+ymd[0]+"-"+ymd[1];
//			$('input[name="date"]').val(date);
//			$('#showbydate').submit();
//		})

		$('button[data-action = "print"]').click(function () {

			var sendData = [];

			var printContents;

			printContents = document.getElementById("table1").outerHTML;
			var originalContents = document.body.innerHTML;
			document.body.innerHTML = printContents;

			window.print();
			document.body.innerHTML = originalContents;
			location.reload();
		});

		$('button[data-action = "expert_csv"]').click(function () {

			var sendData = [];

			var i = 0;
			//send order data
			$('#table1 thead tr').each(function () {
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

			$('#table1 tbody tr').each(function () {
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