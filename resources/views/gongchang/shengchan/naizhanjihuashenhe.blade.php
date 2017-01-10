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
					<a href=""><strong>奶站计划审核</strong></a>
				</li>
			</ol>
		</div>
			<div class="row">
				<input type="hidden" id="current_factory_id" value="{{$current_factory_id}}">
				<!--Table-->
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
						<div id="alert_view" style="display: none">
							<p style="font-size:20px; color:white; background-color:#ff0000;"> 请解决待处理状态!</p>
						</div>
                        <table class="footable table table-bordered" id="total_produce" data-page-size="10">
                            <thead>
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">奶品</th>
									<th data-sort-ignore="true">原计划汇总量（瓶)</th>
									<th data-sort-ignore="true">配送变化量（瓶）</th>
									<th data-sort-ignore="true">生产计划量（瓶）</th>
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
										<button type="button" class="btn btn-danger btn-sm cancel" id="cancel{{$p->id}}" value="{{$p->id}}">生产取消</button>
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
									<th data-sort-ignore="true">奶品</th>
									<th data-sort-ignore="true">计划订单(瓶)</th>
									<th data-sort-ignore="true">站内零售（瓶）</th>
									<th data-sort-ignore="true">试饮赠品（瓶）</th>
									<th data-sort-ignore="true">团购业务（瓶）</th>
									<th data-sort-ignore="true">渠道销售(瓶)</th>
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
									@if(count($si->station_plan) == 0)
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
										<td>{{$si->business_credit_balance + $si->init_business_credit_amount}}</td>
										<td></td>
										<td></td>
									</tr>
									@else

									@foreach($si->station_plan as $ss)
										<?php $j++; ?>
									<tr>
										@if($j==1)
										<td rowspan="{{count($si->station_plan)}}">
											@if($si->plan_status >0)
												<i class="fa fa-check"></i>
											@else
												<i class="fa fa-times"></i>
											@endif
											@if($ss->status ==2)
												<input type="hidden" class="pendding_status" value="1">
											@endif
										</td>
										<td rowspan="{{count($si->station_plan)}}">{{$si->area}}</td>
										<td rowspan="{{count($si->station_plan)}}">{{$si->name}}</td>
										@endif
										<td>{{$ss->product_name}}</td>
										<td>{{$ss->order_count}}</td>
										<td>{{$ss->retail}}</td>
										<td>{{$ss->test_drink}}</td>
										<td>{{$ss->group_sale}}</td>
										<td>{{$ss->channel_sale}}</td>
										<td>{{$ss->subtotal_count}}</td>
										<td class="status{{$si->id}}">
											@if($ss->status == 2) 需审核
											@elseif($ss->status == 3) 生产取消
											@else 正常
											@endif
										</td>
										@if ($j == 1)
										<td rowspan="{{count($si->station_plan)}}">
											{{$si->business_credit_balance + $si->init_business_credit_amount}}
										</td>
										<td rowspan="{{count($si->station_plan)}}">
											@if($ss->status == 2)
											<button class="btn btn-success btn-sm produce_determine" value="{{$ss->station_id}}" style="width: 55px;">同意</button><br>
											<button class="btn btn-success btn-sm produce_cancel" value="{{$ss->station_id}}" style="width: 55px;">拒绝</button>
											@endif
										</td>
										<td rowspan="{{count($si->station_plan)}}"></td>
										@endif
									</tr>
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
@endsection