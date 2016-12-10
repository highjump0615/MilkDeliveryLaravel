@extends('naizhan.layout.master')

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a>生产与配送</a>
				</li>
				<li class="active">
					<strong>计划管理</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				@if ($alert_message != '')
					<div id="alert" class="ibox-content">
						<label style="color:red; font-size: 18px;">{{$alert_message}}</label>
					</div>
				@else
					<div class="ibox-content">
						<div class="col-md-2">
							<button id="import_plan" class="btn btn-success" onclick="window.location='{{ url('/naizhan/shengchan/tijiaojihua') }}'" type="button" style="width: 100%;">
								提交计划
							</button>
						</div>
					</div>
				@endif
				<div class="ibox-content">
					<div class="feed-element">	
						<div class="feed-element col-lg-5" id="date_1">
							<label class="col-lg-3 control-label" style="padding-top:7px;">选择日期:</label>
							<div class="input-group date col-lg-8">
								<input type="text" class="form-control" value="{{$current_date}}" id="search_date">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
						</div>
						<div class="col-lg-3 col-lg-offset-4 text-right"  style="padding-top:5px;">
							<button type="button" id="search" class="btn btn-success btn-m-d">筛选</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="export_csv">导出</button>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="table1" class="table table-bordered" id="plan-list">
                            <thead style="background-color:#33cccc;">
								<tr>
									<th data-sort-ignore="true">提交时间</th>
									<th data-sort-ignore="true">签收时间</th>
									<th data-sort-ignore="true">奶品</th>
									<th data-sort-ignore="true">配送计划</th>
									<th data-sort-ignore="true">站内零售（瓶）</th>
									<th data-sort-ignore="true">试饮赠品（瓶）</th>
									<th data-sort-ignore="true">团购业务（瓶）</th>
									<th data-sort-ignore="true">渠道销售(瓶)</th>
									<th data-sort-ignore="true">合计</th>
									<th data-sort-ignore="true">实际收货量</th>
									<th data-sort-ignore="true">状态</th>
									<th data-sort-ignore="true">备注</th>
								</tr>
                            </thead>
                            <tbody>
								@if(count($dsplan)==0)
									<tr>
										<td colspan="12">你没有发送今天的计划</td>
									</tr>
								@endif
								@foreach($dsplan as $dpDay)
									<?php $i =0; ?>
									@foreach($dpDay as $dp)
									<?php $i++; ?>
									<tr id="plan_row">
										@if($i == 1)
										<td rowspan="{{count($dpDay)}}">{{$dp->submit_at}}</td>
										@endif
										<td id="produce_date">{{$dp->receive_at}}</td>
										<td>{{$dp->product_name}}</td>
										<td class="plan_val">{{$dp->order_count}}</td>
										<td class="plan_val">{{$dp->retail}}</td>
										<td class="plan_val">{{$dp->test_drink}}</td>
										<td class="plan_val">{{$dp->group_sale}}</td>
										<td class="plan_val">{{$dp->channel_sale}}</td>
										<td class="total_sum"></td>
										<td>{{$dp->confirm_count}}</td>
										<td>{{$dp->getStatusString()}}</td>
										<td></td>
										<input type="hidden" id="set_date" value="{{$dp->produce_start_at}}">
									</tr>
									@endforeach
								@endforeach
                            </tbody>
                            <tfoot align="right">
								<tr>
									<td colspan="100%"><ul class="pagination pull-right"></ul></td>
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
	<script src="<?=asset('js/pages/naizhan/jihuaguanli.js') ?>"></script>
@endsection