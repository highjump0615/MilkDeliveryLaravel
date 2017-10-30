@extends('gongchang.layout.master')

@section('css')
	<link href="<?=asset('css/pages/gongchang/floatingtop.css') ?>" rel="stylesheet">
	<link href="<?=asset('css/pages/gongchang/naizhanjihuashenhe.css') ?>" rel="stylesheet">
	<link href="<?=asset('css/pages/gongchang/topfilterbar.css') ?>" rel="stylesheet">

	<style type="text/css">
		#total_produce tbody tr td:nth-child(6) {
			padding: 1px;
		}
	</style>
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
					<a href=""><strong>奶站计划审核</strong></a>
				</li>
			</ol>
		</div>
			<div class="row">
				<input type="hidden" id="current_factory_id" value="{{$current_factory_id}}">
				<!--Table-->
                <div class="ibox float-e-margins">

					<div id="date_select">
						<label class="pull-left control-label">选择提交时间:</label>
						<div class="input-group date">
							<input type="text" class="form-control" value="{{$current_date}}" id="search_date">
							<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>

					<div class="floating">

                    <div class="ibox-content">

						<div id="alert_view" style="display: none">
							<p style="font-size:20px; color:white; background-color:#ff0000;"> 请解决待处理状态!</p>
						</div>
                        <table class="footable table table-bordered" id="total_produce" data-page-size="10">
                            <thead>
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">奶品</th>
									<th data-sort-ignore="true">原计划汇总量</th>
									<th data-sort-ignore="true">配送变化量</th>
									<th data-sort-ignore="true">生产计划量</th>
									<th data-sort-ignore="true" style="background-color:#0b8cc5; color: #fff; width: 30%">确定生产计划量：</th>
								</tr>
                            </thead>
                            <tbody>
								<?php $i=0; ?>
								@foreach($products as $p)
									<?php $i++; ?>
								<tr>
									<td>{{$i}}</td>
									<td>{{$p->simple_name}}</td>
									<td id="plan_count">{{$p->plan_count}}</td>
									<td id="changed_count">{{$p->change_order_amount}}</td>
									<td id="sum"></td>
									<td class="gray-bg" id="check{{$p->id}}">
										@if($p->isfactory_ordered == 0)
										<input type="text" id="produce_amount{{$p->id}}" value="">
										<button type="button" class="btn btn-primary btn-sm validate" id="validate{{$p->id}}" value="{{$p->id}}">生产确认</button>
										@elseif($p->isfactory_ordered == 1)
										{{$p->produce_count}}
										@else
											生产取消
										@endif
									</td>
									<input type="hidden" id="production_period{{$p->id}}" value="{{$p->production_period}}">
								</tr>
								@endforeach
                            </tbody>
							<tfoot>
								<tr>
									<td colspan="6">
										<ul class="pagination pull-right"></ul>
									</td>
								</tr>
							</tfoot>
                        </table>
                    </div>

					</div>
                </div>
				<div class="col-md-12">
					<label>上报记录</label>
				</div>
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table class="table table-bordered" id="plan_sent" data-page-size="10">
                            <thead>
								<tr>
									<th data-sort-ignore="true">计划上报</th>
									<th data-sort-ignore="true">地区</th>
									<th data-sort-ignore="true">奶站</th>
									<th data-sort-ignore="true">提交时间</th>
									<th data-sort-ignore="true">签收时间</th>
									<th data-sort-ignore="true">奶品</th>
									<th data-sort-ignore="true">计划订单</th>
									<th data-sort-ignore="true">站内零售</th>
									<th data-sort-ignore="true">试饮赠品</th>
									<th data-sort-ignore="true">团购业务</th>
									<th data-sort-ignore="true">渠道销售</th>
									<th data-sort-ignore="true">合计</th>
									<th data-sort-ignore="true">状态</th>
									<th data-sort-ignore="true">信用余额</th>
									<th data-sort-ignore="true">操作</th>
									<th data-sort-ignore="true">备注</th>
								</tr>
                            </thead>
                            <tbody>
								<?php $i=0;?>
								@foreach($getStations_info as $si)
									<?php $j=0; $i++; ?>
									@if($si->station_plan['count'] == 0)
									<tr>
										<td><i class="fa fa-times"></i></td>
										<td>{{$si->area}}</td>
										<td>{{$si->name}}</td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td>{{$si->business_credit_balance + $si->init_business_credit_amount}}</td>
										<td></td>
										<td></td>
									</tr>
									@else

									@foreach($si->station_plan['data'] as $ss)
										<?php $j++; $k=0; ?>
										@foreach($ss as $dp)
										<?php $k++; ?>
										<tr>
										@if ($j == 1 && $k == 1)
											<td rowspan="{{$si->station_plan['count']}}">
												@if($si->plan_status >0)
													<i class="fa fa-check"></i>
												@else
													<i class="fa fa-times"></i>
												@endif
												@if($dp->status == \App\Model\DeliveryModel\DSProductionPlan::DSPRODUCTION_PENDING_PLAN)
													<input type="hidden" class="pendding_status" value="1">
												@endif
											</td>
											<td rowspan="{{$si->station_plan['count']}}">{{$si->area}}</td>
											<td rowspan="{{$si->station_plan['count']}}">{{$si->name}}</td>
										@endif

										@if ($k == 1)
										<td rowspan="{{count($ss)}}">{{$dp->submit_at}}</td>
										@endif

										<td>{{$dp->receive_at}}</td>
										<td>{{$dp->product_name}}</td>
										<td>{{$dp->order_count}}</td>
										<td>{{$dp->retail}}</td>
										<td>{{$dp->test_drink}}</td>
										<td>{{$dp->group_sale}}</td>
										<td>{{$dp->channel_sale}}</td>
										<td>{{$dp->subtotal_count}}</td>
										<td class="status{{$si->id}}">
											@if ($dp->status == \App\Model\DeliveryModel\DSProductionPlan::DSPRODUCTION_PENDING_PLAN) 需审核
											@elseif ($dp->status == \App\Model\DeliveryModel\DSProductionPlan::DSPRODUCTION_PRODUCE_CANCEL) 生产取消
											@else 正常
											@endif
										</td>
										@if ($j == 1 && $k == 1)
										<td rowspan="{{$si->station_plan['count']}}">
											{{$si->business_credit_balance + $si->init_business_credit_amount}}
										</td>
										@endif
										@if ($k == 1)
										<td rowspan="{{count($ss)}}">
											@if ($dp->status == \App\Model\DeliveryModel\DSProductionPlan::DSPRODUCTION_PENDING_PLAN)
											<button class="btn btn-success btn-sm produce_determine" value="{{$dp->station_id}}" style="width: 55px;">同意</button><br>
											<button class="btn btn-success btn-sm produce_cancel" value="{{$dp->station_id}}" style="width: 55px;">拒绝</button>
											@endif
										</td>
										<td rowspan="{{count($ss)}}"></td>
										@endif
										</tr>
										@endforeach
									@endforeach
									@endif
								@endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
			</div>
	</div>
@endsection

@section('script')
	<!--Save & Cancel Information-->
	<script src="<?=asset('js/ajax/shengchan_naizhanjihuashenhe_ajax.js') ?>"></script>
	<script src="<?=asset('js/pages/gongchang/floatingtop.js') ?>"></script>
@endsection