@extends('naizhan.layout.master')

@section('css')
	<link href="<?=asset('css/print.css') ?>" rel="stylesheet">
@endsection

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
					<strong>今日配送单</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
			
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-3">
							<label class="col-lg-4" style="padding-top: 5px;">配送员:</label>
							<div class="col-lg-8">
								<select data-placeholder="" id="milkman_name" class="form-control chosen-select" style="width:100%;" tabindex="2">
									@foreach($milkman_info as $mi)
										@if (array_key_exists('milkman_id', $mi))
											<option value="{{$mi['milkman_id']}}">{{$mi['milkman_name']}}</option>
										@endif
									@endforeach
								</select>
							</div>	
						</div>
						<div class="form-group col-md-5" id="date_select">
							<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
							<div class="input-group date col-lg-8">
								<input type="text" class="form-control" value="{{$date}}" id="search_date"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
						</div>
						<div class="col-md-offset-1 col-md-3"  style="padding-top:5px;">
							{{--<button type="button" class="btn btn-success btn-m-d">筛选</button>--}}
							{{--&nbsp;--}}
							<a href="{{url('/naizhan/shengchan/jinripeisongdan/export')}}" class="btn btn-success btn-sm">导出</a>
							&nbsp;
							<button class="btn btn-success btn-outline btn-sm" data-action="print">打印</button>
						</div>
					</div>
				</div>

				@if(isset($alert_msg) != 0)
					<label class="redalert">{{$alert_msg}}</label>
				@endif

				<div><hr></div>

				<div id="deliver_info">
				@foreach($milkman_info as $mi)
					@if (array_key_exists('milkman_id', $mi))
					<div id="milkman{{$mi['milkman_id']}}" class="milkman_plans">
					<div class="feed-element">
						<div class="col-lg-4">
							<label class="col-lg-4">配送员:</label>
							<label class="col-lg-8">{{$mi['milkman_number']}} {{$mi['milkman_name']}} </label>
						</div>
					</div>
					<div class="ibox float-e-margins">
						<div class="col-lg-12"><label class="col-lg-12">配送统计</label></div>
						<div class="col-lg-1"></div>
						<div class="col-lg-10">
							<table class="table footable table-bordered delivery_amount">
								<thead style="background-color:#33cccc;">
									<tr>
										<th data-sort-ignore="true">奶品名称</th>
										<th data-sort-ignore="true">计划订单</th>
										<th data-sort-ignore="true">赠品数量</th>
										<th data-sort-ignore="true">配送团购量</th>
										<th data-sort-ignore="true">配送渠道量</th>
										<th data-sort-ignore="true">店内零售</th>
										<th data-sort-ignore="true">合计</th>
										<th data-sort-ignore="true">变化量统计</th>
									</tr>
								</thead>
								<tbody>
								@if(count($mi['milkman_products'])==0)
								<tr>
									<td colspan="7">数据不存在</td>
								</tr>
								@endif
								<?php $i = 0; ?>
								@foreach($mi['milkman_products'] as $mp)
									<?php $i++; ?>
									<tr>
										<td>{{$mp['name']}}</td>
										<td>{{getEmptyArrayValue($mp, \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER)}}</td>
										<td>{{getEmptyArrayValue($mp, \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK)}}</td>
										<td>{{getEmptyArrayValue($mp, \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_GROUP)}}</td>
										<td>{{getEmptyArrayValue($mp, \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL)}}</td>
										<td>{{getEmptyArrayValue($mp, \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_RETAIL)}}</td>
										<td></td>
										@if($i==1)
										<td rowspan="{{count($mi['milkman_products'])}}">
											还有三次到期心型符号标记<br>
											今日到期X型符号标记<br>
											新增订单星型符号标记<br>
											新增数量：{{$mi['milkman_changestatus']['new_order_amount']}}瓶<br>
											配送规则修改：{{$mi['milkman_changestatus']['new_changed_order_amount']}}瓶<br>
											奶箱安装数量：{{$mi['milkman_changestatus']['milkbox_amount']}}
										</td>
										@endif
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
						<div class="col-lg-1"></div>
					</div>
					<div class="ibox float-e-margins">
						<div class="ibox-content">

							<table class="table table-bordered">
								<thead>
									<tr>
										<th data-sort-ignore="true">序号</th>
										<th data-sort-ignore="true">地址</th>
										<th data-sort-ignore="true">配送内容</th>
										<th data-sort-ignore="true">收货人</th>
										<th data-sort-ignore="true">配送时间</th>
										<th data-sort-ignore="true">电话</th>
										<th data-sort-ignore="true">备注</th>
									</tr>
								</thead>
								<tbody>
									<?php $i=0; ?>
									@foreach($mi['delivery_info'] as $oi)
										<?php $i++; ?>
										<tr>
										<td>
											<!-- 如果是订单第一次配送，加星号标出来 -->
											<!-- 如果是订单还有三天到期，加时间符号标出来 -->
											<!-- 如果是订单第一次配送，加X号标出来 -->
											@if ($oi['notice'] == \App\Model\DeliveryModel\MilkManDeliveryPlan::NOTICE_FIRST_DEVLIVER)
												<i class="fa fa-star"></i>
											@elseif ($oi['notice'] == \App\Model\DeliveryModel\MilkManDeliveryPlan::NOTICE_ALMOST_END)
												<i class="fa fa-heart"></i>
											@elseif ($oi['notice'] == \App\Model\DeliveryModel\MilkManDeliveryPlan::NOTICE_END_TODAY)
												<i class="fa fa-remove"></i>
											@endif
											{{$i}}
										</td>
										<!-- 地址 -->
										<td class="text-left pl-15">{{$oi->getAddressSmall(\App\Model\BasicModel\Address::LEVEL_VILLAGE)}}</td>
										<td>
											@foreach($oi->products as $pd)
												{{$pd}}
											@endforeach
										</td>
										@if($oi->delivery_type==1)
											<td>{{$oi->customer->name}}</td>
										@else
											<td>{{$oi->customer_name}}</td>
										@endif
										<td>{{$oi->getDeliveryTimeDesc()}}</td>
										<td>{{$oi->phone}}</td>
										<td>{{$oi->comment_delivery}}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
					</div>
					@endif
				@endforeach
				</div>
				<div class="col-md-offset-5 col-md-2">
					<button id="return" class="btn btn-success">查看今日配送列表</button>
				</div>
			</div>
		</div>
		
	</div>
@endsection

@section('script')
	<script type="text/javascript">
        $(document).ready(function() {
            $('#date_select .input-group.date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: false,
                autoclose: true
            });
		});

        $(document).on('change','#date_select',function(){
            var current_date = $('#search_date').val();
            // 日期筛选
            var strUrl = SITE_URL+"naizhan/shengchan/jinripeisongdan?current_date="+current_date + "";
            window.location.href = strUrl;
        });
	</script>
	<script src="<?=asset('js/pages/naizhan/jinripeisongdan.js') ?>"></script>
@endsection
