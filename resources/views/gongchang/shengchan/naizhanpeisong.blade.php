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
					<a href=""><strong>奶站配送管理</strong></a>
				</li>
			</ol>
		</div>
			<div class="row">	
<!--Table-->				
                <div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table class="footable table table-bordered" id="current_status" data-page-size="10">
                            <thead>
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">奶品</th>
									<th data-sort-ignore="true">生产计划量</th>
									<th data-sort-ignore="true">实际生产量</th>
									<th data-sort-ignore="true">富余量</th>
									<th data-sort-ignore="true">实际发货总量</th>
									<th data-sort-ignore="true">库存结余</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i=0; ?>
							@foreach($products as $p)
								<?php $i++; ?>
								<tr id="{{$p->id}}">
									<td>{{$i}}</td>
									<td>{{$p->simple_name}}</td>
									<td>{{$p->produce_count}}</td>
									<td class="editfill product_count" contenteditable="true" id="produce_count{{$p->id}}">{{$p->produce_count}}</td>
									<td></td>
									<td id="total_confirm{{$p->id}}"></td>
									<td id="rest{{$p->id}}"></td>
								</tr>
							@endforeach
                            </tbody>
							<tfoot>
								<tr>
									<td colspan="7">
										<ul class="pagination pull-right"></ul>
									</td>
								</tr>
							</tfoot>
                        </table>
                    </div>
                </div>

				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table class="footable table table-bordered" id="by_station" data-page-size="{{$page_count}}">
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
									<th data-sort-ignore="true">实际签收量</th>
									<th data-sort-ignore="true">状态</th>
									<th data-sort-ignore="true">操作</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i=0; ?>
							@foreach($DeliveryStations_info as $di)
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
									<td>{{$ds->subtotal_count}}</td>
									<td>{{$ds->diff}}</td>
									<td @if($ds->status < \App\Model\DeliveryModel\DSProductionPlan::DSPRODUCTION_PRODUCE_SENT) contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1" @endif
										class="confirm_count"
										id="confirm{{$i}}{{$ds->product_id}}"
										value="{{$ds->product_id}}">
										@if($ds->status < \App\Model\DeliveryModel\DSProductionPlan::DSPRODUCTION_PRODUCE_SENT) {{$ds->subtotal_count + $ds->diff}} @else {{$ds->actual_count}} @endif
									</td>
									<td>{{$ds->confirm_count}}</td>
									@if($j==1)
									<td rowspan="{{count($di->station_plan)}}">@if($ds->status > \App\Model\DeliveryModel\DSProductionPlan::DSPRODUCTION_PRODUCE_FINNISHED) 已发货 @endif</td>
									<!-- 已发货 -->
									<td rowspan="{{count($di->station_plan)}}">
										@if($ds->status > \App\Model\DeliveryModel\DSProductionPlan::DSPRODUCTION_PRODUCE_FINNISHED)
											<button class="btn btn-success"
													onclick="window.location='{{URL::to('/gongchang/shengchan/naizhanpeisong/dayinchukuchan?station_name='.$di->name)}}'"
													id="detail{{$i}}"
													type="button" >打印出库单</button>
										@else
											<button type="button"
													class="btn btn-success btn-md determine_count"
													value="{{$i}}" id="detail{{$i}}">发货确认</button>
											<button class="btn btn-success"
													onclick="window.location='{{URL::to('/gongchang/shengchan/naizhanpeisong/dayinchukuchan?station_name='.$di->name)}}'"
													id="f_detail{{$i}}"
													type="button" >打印出库单</button>
										@endif
									</td>
									@endif
									<input type="hidden" id="station_id{{$i}}" value="{{$di->id}}">
								</tr>
								@endforeach
							@endforeach
                            </tbody>
							<tfoot>
								<tr>
									<td colspan="16">
										<ul class="pagination pull-right"></ul>
									</td>
								</tr>
							</tfoot>
                        </table>
                    </div>
                </div>
				<div class="ibox float-e-margins bg-white">
					<div class="col-lg-offset-5 col-lg-2">
						<button class="btn btn-success" onclick="window.location='{{URL::to('/gongchang/shengchan/naizhanpeisong/naizhanshouhuoqueren')}}'" style="width: 100%;">
							查看历史
						</button>
					</div>
				</div>
			</div>
	</div>
@endsection

@section('script')
	<!--Save & Update User Information-->
	<script src="<?=asset('js/ajax/shengchan_naizhanpeisong_ajax.js') ?>"></script>
    <script src="<?=asset('js/pages/gongchang/naizhanpeisong.js') ?>"></script>
@endsection