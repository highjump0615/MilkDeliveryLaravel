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
					<strong>打印出库单</strong>
				</li>
			</ol>
		</div>
			<div class="row border-bottom">
			</div>
			<div class="row">	
<!--Table-->				
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
							<label class="col-md-3 control-label" style="padding-top: 7px;">日期:</label>
							<div class="input-group date col-md-9">
								<input type="text" id="date" class="form-control" value="{{$current_date}}"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
						</div>
					</div>
					<div class="feed-element">
						<div class="col-md-offset-10 col-md-2">
							<button type="button" id="search" class="btn btn-success btn-md">筛选</button>
							{{--&nbsp;--}}
							{{--<button class="btn btn-success btn-outline btn-m-d" data-action="export_csv">导出</button>--}}
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
						</div>
					</div>
				</div>
				<div class="ibox float-e-margins">
					@foreach($station as $st)
                    <div class="ibox-content col-md-12">
						{{--<div class="col-md-1">--}}
							{{--<input type="checkbox" checked class="i-checks" name="input[]">--}}
                        {{--</div>--}}
						<div class="col-md-12">
							{{--<input type="checkbox" class="i-checks"--}}
								   {{--data-tid="{{$st->name}}"--}}
								   {{--data-station-id="{{$st->id}}" style="display: inline"/>--}}
							<table class="table table-bordered" id="table1">
								<thead class="gray-bg">
									<tr>
										<th colspan="3">{{$st->name}}</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="3">
											<div class="col-md-4">
												<label>发货日期：</label><label>{{$current_date}}</label>
											</div>
											<div class="col-md-4">
												<label>发货人：</label>
												<input type="text" id="input_name">
											</div>
											<div class="col-md-4">
												<label>车牌号：</label>
												<input type="text" id="input_carnum">
											</div>
										</td>
									</tr>
									<tr>
										<td>货品</td>
										<td colspan="2">发货数量</td>
									</tr>
									@foreach($st->station_plan as $sp)
									<tr>
										<td>{{$sp->product_name}}</td>
										<td colspan="2">{{$sp->actual_count}}</td>
									</tr>
									@endforeach
									<?php $i=0; ?>
									@foreach($st->mfbox_type as $bt)
										<?php $i++; ?>
									<tr>
										@if($i == 1)
										<td rowspan="{{count($st->mfbox_type)}}">奶筐</td>
										@endif
										<td width="20%">{{$bt->name}}</td>
										<td width="30%" contenteditable="true"></td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
                    </div>
					@endforeach
                </div>
				<div class="col-md-offset-5 col-md-2" style="padding:15px">
					<button id="return" class="btn btn-success" style="width: 70%"><i class="fa fa-reply"></i></button>
				</div>
			</div>
	</div>
@endsection

@section('script')
	<script src="<?=asset('js/plugins/added/switchery.js') ?>"></script>
   
    <!-- Data picker -->
    <script src="<?=asset('js/plugins/datepicker/bootstrap-datepicker.js') ?>"></script>

    <script type="text/javascript">
		$(document).ready(function(){
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green'
            });
        });
		$('.footable').footable();
		
			$('#date_2 .input-group.date').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: false,
            autoclose: true
        });

		$('#search').click(function () {
			var station_name = $('#station_name').val();
			var station_number = $('#station_number').val();
			var address = $('#address').val();
			var date = $('#date').val();
			window.location.href = SITE_URL+"milk/public/gongchang/shengchan/naizhanpeisong/dayinchukuchan/?station_name="+station_name+"&date="+date+"&station_number="+station_number+"&address="+address+"";
		});

		$('button[data-action = "print"]').click(function () {
			var sendData = [];

			// 填写里面的文字输入框
			$('#table1 input[type=text]').each(function () {
				$(this).prop('outerHTML', $(this).val());
			});

			printContent('table1');
		});

		$('#return').click(function () {
			window.location.href = SITE_URL + "gongchang/shengchan/naizhanpeisong";
		})
    </script>
@endsection