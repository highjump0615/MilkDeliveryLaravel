@extends('naizhan.layout.master')

@section('css')
	<style type="text/css">
		#date_select {
			margin-bottom: 10px;
		}

		#date_select label {
			margin-bottom: 0;
			margin-right: 20px;
			margin-left: 20px;
			line-height: 30px;
		}

		#date_select .date {
			width: 200px;
		}
	</style>
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
					<strong>配送管理</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content pt-10">
				{{-- Deprecated --}}
				@if ($is_received == 0)
					<label style="color: red; font-size: 18px;">今日还没签收, 无法生成配送列表</label>
				@endif

				<div id="date_select">
					<label class="pull-left control-label">生成配送单日期:</label>
					<div class="input-group date">
						<input type="text" class="form-control" value="{{$date_current}}" id="search_date">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					</div>
				</div>

                <div class="alert alert-danger alert-dismissable @if(empty($errMsg)) hidden @endif">
                    <button area-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <span>{{$errMsg}}</span>
                </div>

				<div class="ibox float-e-margins">
                    <div class="ibox">
                    	<div class="row">
							<input type="hidden" id="count" value="{{count($dsproduction_plans)}}">
							<div class="col-md-12">
								<table id="distribute" class="table table-bordered">
									<thead>
										<tr>
											<td rowspan="2">奶品</td>
											<td rowspan="2">上日库存余量</td>
											<td rowspan="2">当日签收数量</td>
											<td rowspan="2">当日奶站可出库数量</td>
											<td colspan="3">配送业务</td>
											<td colspan="2">店内零售</td>
											<td colspan="2">团购业务</td>
											<td colspan="2">渠道业务</td>
											<td colspan="2">试饮赠品</td>
											<td rowspan="2">出库总计</td>
											<td rowspan="2">配送业务实际配送数量</td>
											<td rowspan="2">当日库存剩余</td>
										</tr>
										<tr>
											<!-- 配送业务 -->
											<td>订单数量</td>
											<td>调整数量</td>
											<td>可配送数量合计</td>

											<!-- 店内零售	 -->
											<td>订单数量</td>
											<td>出库数量</td>

											<!-- 团购业务 -->
											<td>订单数量</td>
											<td>出库数量</td>

											<!-- 渠道业务 -->
											<td>订单数量</td>
											<td>出库数量</td>

											<!-- 试饮赠品 -->
											<td>订单数量</td>
											<td>出库数量</td>
										</tr>
									</thead>
									<tbody>
									<?php $p = count($dsproduction_plans); ?>
										<!-- 无数据 -->
										@if($p == 0)
											<tr>
												<td colspan="18">数据不存在！</td>
											</tr>
										@endif

										<!-- 遍历奶品 -->
										@foreach($dsproduction_plans as $dsp)
											<tr class="product_tr">
												<!-- 奶品名称 -->
												<td class="product_id" value="{{$dsp->product_id}}">{{$dsp->product_name}}</td>
												<!-- 上日库存余量 -->
												<td class="remained">{{$dsp->dp_remain_before}}</td>
												<!-- 当日签收数量 -->
												<td>{{$dsp->confirm_count}}</td>
												<!-- 当日奶站可出库数量 -->
												<td id="confirm_count{{$dsp->id}}" class="produced">{{$dsp->confirm_count + $dsp->dp_remain_before}}</td>

												<!-- 配送业务 -->
												<!-- 订单数量 -->
												<td id="order_count{{$dsp->id}}" class="order ordered_amount">{{$dsp->order_count}}</td>
												<!-- 调整数量 -->
												<td class="order editable_amount @if($is_distributed!=1) editfill @endif" @if($is_distributed!=1) contenteditable="true" @endif>
													{{$dsp->changed_amount}}
												</td>
												<!-- 可配送数量合计 -->
												<td class="sum order_sum" id="order_sum{{$dsp->id}}"></td>

												<!-- 店内零售 -->
												<!-- 订单数量 -->
												<td class="retail origin" id="retail_count{{$dsp->id}}">{{$dsp->retail}}</td>
												<!-- 出库数量 -->
												<td id="retail_sum{{$dsp->id}}" class="sum retail_sum">{{$dsp->dp_retail}}</td>

												<!-- 团购业务 -->
												<!-- 订单数量 -->
												<td class="group origin" id="group_count{{$dsp->id}}">{{$dsp->group_sale}}</td>
												<!-- 出库数量 -->
												<td class="sum group_sum" id="group_sum{{$dsp->id}}">{{$dsp->dp_group_sale}}</td>

												<!-- 渠道业务 -->
												<!-- 订单数量 -->
												<td class="channel origin" id="channel_count{{$dsp->id}}">{{$dsp->channel_sale}}</td>
												<!-- 出库数量 -->
												<td class="sum channel_sum" id="channel_sum{{$dsp->id}}">{{$dsp->dp_channel_sale}}</td>

												<!-- 试饮赠品 -->
												<!-- 订单数量 -->
												<td class="drink origin" id="drink_count{{$dsp->id}}">{{$dsp->test_drink}}</td>
												<!-- 出库数量 -->
												<td id="drink_sum{{$dsp->id}}" class="sum drink_sum">{{$dsp->dp_test_drink}}</td>

												<!-- 出库总计 -->
												<td id="total{{$dsp->id}}" class="total_sum"></td>

												<!-- 配送业务实际配送数量 -->
												<td id="delivered{{$dsp->id}}" class="delivered_sum">{{$dsp->deliverd_count}}</td>

												<!-- 当日库存剩余 -->
												<td id="remain_amount{{$dsp->id}}" class="remain_sum">{{$dsp->dp_remain}}</td>
											</tr>
										@endforeach
									</tbody>

								</table>
							</div>
						</div>

						<!-- 调配按钮 -->
						<div>
							<div class="col-md-3 col-md-offset-9">
								<div class="col-md-6 col-md-offset-6">
									<button class="btn btn-success btn-outline auto_distribute" style="width:90%;" @if($is_distributed==1) disabled @endif>
										调配
									</button>
								</div>
							</div>
						</div>

						<div class="ibox-content">
	                        <table id="changed_distribute" class="table footable table-bordered" >
	                            <thead style="background-color:#33cccc;">
									<tr>
										<th data-sort-ignore="true">序号</th>
										<th data-sort-ignore="true">订单修改时间</th>
										<th data-sort-ignore="true">订单号</th>
										<th data-sort-ignore="true">收货人</th>
										<th data-sort-ignore="true">地址</th>
										<th data-sort-ignore="true">奶品</th>
										<th data-sort-ignore="true">原计划量</th>
										<th data-sort-ignore="true">变化后计划量</th>
										<th data-sort-ignore="true">修改后量</th>
										<th data-sort-ignore="true">电话</th>
										<th data-sort-ignore="true">配送员</th>
										<th data-sort-ignore="true">状态</th>
										<th data-sort-ignore="true">备注</th>
									</tr>
	                            </thead>
	                            <tbody>
								@if(count($changed_plans) == 0)
								<tr>
									<td colspan="13">数据不存在!</td>
								</tr>
								@endif
								<?php $i=0; ?>
								@foreach($changed_plans as $cp)
									<?php $i++; ?>
									<tr id="{{$cp->id}}" value="{{$cp->order_product->product->id}}">
										<td>{{$i}}</td>
										<td>{{$cp->deliver_at}}</td>
										<td>{{$cp->order->number}}</td>
										<td>{{$cp->order->customer->name}}</td>
										<td class="text-left pl-15">{{$cp->order->addresses}}</td>
										<td>{{$cp->order_product->product->simple_name}}</td>
										<td>{{$cp->plan_count}}</td>
										<td>{{$cp->changed_plan_count}}</td>
										@if($is_distributed!=1)
											<td contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
										@else
											<td>{{$cp->delivery_count}}</td>
										@endif
										<td>{{$cp->order->phone}}</td>
										<td>@if (!empty($cp->order->milkman)) {{$cp->order->milkman->name}} @endif</td>
										@if($is_distributed!=1)
											<td>未调配</td>
										@else
											@if($cp->changed_plan_count == $cp->delivery_count)
												<td>己调配</td>
											@else
												<td>未调配</td>
											@endif
										@endif
										@if($is_distributed!=1)
											<td contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
										@else
											<td>{{$cp->comment}}</td>
										@endif
									</tr>
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
				<div class="ibox-content">
					<div class="col-lg-10"></div>
					<div class="col-lg-2">
						@if($is_distributed != 1)
							<button class="btn btn-success shengchan-peisong"
									style="width: 100%;"
									@if ($is_received == 0 || !empty($errMsg)) disabled @endif>
								生成今日配送单
							</button>
						@else
							<button onclick="window.location='{{ url('/naizhan/shengchan/jinripeisongdan') }}'" class="btn btn-success" style="width: 100%;">
								查看今日配送单
							</button>
						@endif
					</div>
				</div>

			</div>
		</div>

	</div>
@endsection

@section('script')

	<script type="text/javascript">
		var gbReported = false;
		@if ($is_reported)
            gbReported = true;
		@endif

		$(document).ready(function() {
            /**
             * 初始化日期选择器
             */
            $('.input-group.date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: false,
                autoclose: true,
                startDate: new Date('{{$date_start}}'),
                endDate: new Date('{{$date_end}}')
            }).on('changeDate', function(e) {
                // 用新的日期刷新页面
                var strDate = $('#search_date').val();
                window.location.href = SITE_URL + "naizhan/shengchan/peisongguanli?date=" + strDate;
            });
		});

	</script>

	<!--Save & Update User Information-->
	<script src="<?=asset('js/ajax/naizhan_peisongguanli_ajax.js') ?>"></script>

@endsection