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
										<th colspan="3">{{$station->name}}</th>
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
												<input type="text" id="input_name" value="{{$sender_name}}" />
											</div>
											<div class="col-md-4">
												<label>车牌号：</label>
												<input type="text" id="input_carnum" value="{{$car_number}}" >
											</div>
											<!-- 奶站id -->
											<input type="hidden" id="input_stationid" value="{{$station->id}}" />
										</td>
									</tr>
									<tr>
										<td>货品</td>
										<td colspan="2">发货数量</td>
									</tr>
									@foreach($station->station_plan as $sp)
									<tr>
										<td>{{$sp->product_name}}</td>
										<td colspan="2">{{$sp->actual_count}}</td>
									</tr>
									@endforeach
									<?php $i=0; ?>
									@foreach($station->mfbox_type as $bt)
										<?php $i++; ?>
									<tr class="boxtype" value="{{$bt['box']->id}}">
										@if($i == 1)
										<td rowspan="{{count($station->mfbox_type)}}">奶筐</td>
										@endif
										<td width="20%">{{$bt['box']->name}}</td>
										<td class="boxcount" width="30%" contenteditable="true">{{$bt['count']}}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
                    </div>

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

	<script src="<?=asset('js/pages/gongchang/dayinchukudan.js') ?>"></script>

@endsection