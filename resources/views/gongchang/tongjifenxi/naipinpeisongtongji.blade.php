@extends('gongchang.layout.master')
@section('css')
	<link href="<?=asset('css/plugins/datepicker/datepicker3.css') ?>" rel="stylesheet">
	<link href="<?=asset('css/plugins/iCheck/custom.css') ?>" rel="stylesheet">
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
				<li class="active"><strong>奶品配送统计</strong></li>
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

						<div class="form-group col-md-4" id="data_range_select">
							<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
							<div class="input-daterange input-group col-md-8" id="datepicker">
								<input type="text" class="input-sm form-control" name="start" id="start_date" value="{{$start_date}}"/>
								<span class="input-group-addon">至</span>
								<input type="text" class="input-sm form-control" name="end" id="end_date" value="{{$end_date}}"/>
							</div>
						</div>
						<div class="col-md-2"  style="padding-top:5px;">
							<button type="button" id="search" class="btn btn-success btn-md">筛选</button>
							&nbsp;
							<button class="btn-outline btn btn-success btn-m-d" data-action="export_csv">导出</button>
							&nbsp;
							<button class="btn btn-outline btn-success btn-m-d" data-action="print">打印</button>
						</div>
					</div>
					{{--<div class="feed-element">--}}
						{{--<div class="form-group col-md-5" id="data_range_select">--}}
							{{--<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>--}}
							{{--<div class="input-daterange input-group col-md-8" id="datepicker">--}}
                                {{--<input type="text" class="input-sm form-control" name="start" />--}}
                                {{--<span class="input-group-addon">至</span>--}}
                                {{--<input type="text" class="input-sm form-control" name="end"  />--}}
                            {{--</div>--}}
						{{--</div>--}}
						{{--<div class="col-md-3"  style="padding-top:5px;">--}}
							{{--<button type="button" class="btn btn-success btn-md">筛选</button>--}}
							{{--&nbsp;--}}
							{{--<a href="">导出</a>--}}
							{{--&nbsp;--}}
							{{--<a href="">打印</a>--}}
						{{--</div>--}}
					{{--</div>--}}
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="table1" class="footable table table-bordered" data-page-size="{{$count *3}}">
                            <thead>
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">区域</th>
									<th data-sort-ignore="true">分区</th>
									<th data-sort-ignore="true">奶站名称</th>
									<th data-sort-ignore="true">奶品</th>
									<th data-sort-ignore="true">微信支付（瓶）</th>
									<th data-sort-ignore="true">奶卡支付（瓶）</th>
									<th data-sort-ignore="true">现金支付（瓶）</th>
									<th data-sort-ignore="true">站内零售（瓶）</th>
									<th data-sort-ignore="true">试饮赠品（瓶）</th>
									<th data-sort-ignore="true">团购业务（瓶）</th>
									<th data-sort-ignore="true">渠道销售数量（瓶）</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i = 0; ?>
							@foreach($stations as $st)
								<?php $i++; $j = 0; ?>
								@foreach($st->product as $p)
									<?php $j++; ?>
								<tr>
									@if($j == 1)
									<td rowspan="{{count($st->product)}}">{{$i}}</td>
									<td rowspan="{{count($st->product)}}">{{$st->province}}</td>
									<td rowspan="{{count($st->product)}}">{{$st->district}}</td>
									<td rowspan="{{count($st->product)}}">{{$st->name}}</td>
									@endif
									<td>{{$p->name}}</td>
									<td>{{$p->weixin}}</td>
									<td>{{$p->card}}</td>
									<td>{{$p->xianjin}}</td>
									<td>{{$p->retail}}</td>
									<td>{{$p->test_drink}}</td>
									<td>{{$p->group_sale}}</td>
									<td>{{$p->channel_sale}}</td>
								</tr>
								@endforeach
							@endforeach
                            </tbody>
							<tfoot>
								<tr>
									<td colspan="12">
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
	<script type="text/javascript" src="<?=asset('js/global.js') ?>"></script>
	<script src="<?=asset('js/plugins/iCheck/icheck.min.js') ?>"></script>
    <!-- Data picker -->
    <script src="<?=asset('js/plugins/datepicker/bootstrap-datepicker.js') ?>"></script>

    <script type="text/javascript">
		$(document).ready(function(){
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        });

		$(document).on('click','#search',function () {
			var station_name = $('#station_name').val();
			var area_name = $('#area_name option:selected').val();
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			window.location.href = SITE_URL+"milk/public/gongchang/tongjifenxi/naipinpeisongtongji/?station_name="+station_name+"&area_name="+area_name+"&start_date="+start_date+"&end_date="+end_date+"";
		})
		
		$('.footable').footable();

		$('#data_range_select .input-daterange').datepicker({
			keyboardNavigation: false,
			forceParse: false,
			autoclose: true
		});

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

		$('button[data-action = "export_csv"]').click(function () {

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