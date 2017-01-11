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
					<strong>今日配送单</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">

				@if(isset($alert_msg) != 0)
					<label style="color: red; font-size: 18px;">{{$alert_msg}}</label>
				@endif
			
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-3 col-md-offset-3">
							<label class="col-lg-4" style="padding-top: 5px;">配送员:</label>
							<div class="col-lg-8">
								<select data-placeholder="" id="milkman_name" class="form-control chosen-select" style="width:100%;" tabindex="2">
									@foreach($milkman_info as $mi)
										<option value="{{$mi['milkman_name']}}">{{$mi['milkman_name']}}</option>
									@endforeach
								</select>
							</div>	
						</div>
						<div class="col-md-4 col-md-offset-2">
							<button type="button" class="btn btn-success btn-m-d">筛选</button>
							{{--&nbsp;--}}
							{{--<a href="">导出</a>--}}
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
						</div>
					</div>
				</div>

				<div><hr></div>

				<div id="deliver_info">
				@foreach($milkman_info as $mi)
				<div id="{{$mi['milkman_name']}}" class="milkman_plans">
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
									<th data-sort-ignore="true">计划订单(瓶)</th>
									<th data-sort-ignore="true">赠品数量(瓶)</th>
									<th data-sort-ignore="true">配送团购量(瓶)</th>
									<th data-sort-ignore="true">配送渠道量(瓶)</th>
									<th data-sort-ignore="true">店内零售(瓶)</th>
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
									<td>{{$mp[\App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_USER]}}</td>
									<td>{{$mp[\App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK]}}</td>
									<td>{{$mp[\App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_GROUP]}}</td>
									<td>{{$mp[\App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL]}}</td>
									<td>{{$mp[\App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_RETAIL]}}</td>
									<td></td>
									@if($i==1)
									<td rowspan="{{count($mi['milkman_products'])}}">
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
										@if($oi->flag == 1)
											<i class="fa fa-star"></i>
										@endif
										{{$i}}
									</td>
									<td>{{$oi->address}}</td>
									<td>{{$oi->product}}</td>
									@if($oi->delivery_type==1)
										<td>{{$oi->customer->name}}</td>
									@else
										<td>{{$oi->customer_name}}</td>
									@endif
									<td>
										@if($oi->delivery_time == \App\Model\OrderModel\Order::ORDER_DELIVERY_TIME_MORNING)
											上午
										@elseif($oi->delivery_time == \App\Model\OrderModel\Order::ORDER_DELIVERY_TIME_AFTERNOON)
											下午
										@endif
									</td>
									<td>{{$oi->phone}}</td>
									<td>{{$oi->comment_delivery}}</td>
								</tr>
								@endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
				</div>
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
		$(document).ready(function(){
			/**
			 * 计算配送统计的合计
			 */
			$('.delivery_amount tr:not(:first)').each(function(){
				var plan_sale=0;
				var test_drink=0;
				var group_sale=0;
				var channel_sale=0;
				var store_sale=0;

				if(!isNaN(parseInt($(this).find('td:eq(1)').text()))){
					plan_sale = parseInt($(this).find('td:eq(1)').text());
				}
				if(!isNaN(parseInt($(this).find('td:eq(2)').text()))){
					test_drink = parseInt($(this).find('td:eq(2)').text());
				}
				if(!isNaN(parseInt($(this).find('td:eq(3)').text()))){
					group_sale = parseInt($(this).find('td:eq(3)').text());
				}
				if(!isNaN(parseInt($(this).find('td:eq(4)').text()))){
					channel_sale = parseInt($(this).find('td:eq(4)').text());
				}
				if(!isNaN(parseInt($(this).find('td:eq(5)').text()))){
					store_sale = parseInt($(this).find('td:eq(5)').text());
				}
				$(this).find('td:eq(6)').html(plan_sale + test_drink + group_sale + channel_sale + store_sale);
			})
		});

		$(document).on('change','#milkman_name',function () {
			$('.milkman_plans').hide();
			var currentMilkman = $('#milkman_name option:selected').text();
			$('#'+currentMilkman+'').show();
		})

		$('button[data-action = "print"]').click(function () {

			var sendData = [];

			var printContents;

			printContents = document.getElementById("deliver_info").outerHTML;
			var originalContents = document.body.innerHTML;
			document.body.innerHTML = printContents;

			window.print();
			document.body.innerHTML = originalContents;
			location.reload();
		});

		$('#return').click(function () {
			window.location = SITE_URL + "naizhan/shengchan/peisongliebiao";
		})
	</script>
@endsection
