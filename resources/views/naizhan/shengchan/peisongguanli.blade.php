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
					<strong>配送管理</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				@if ($is_received == 0)
					<label style="color: red; font-size: 18px;">今日还没签收, 无法生成配送列表</label>
				@endif

				<div class="ibox float-e-margins">
                    <div class="ibox">
                    	<div class="row">
							<input type="hidden" id="count" value="{{count($dsproduction_plans)}}">
							<div class="col-md-12">
								<table id="distribute" class="table table-bordered">
									<tbody>
									<?php $p = count($dsproduction_plans); ?>

										<!-- 无数据 -->
										<tr class="product_id_tr">
											<td colspan="2" class="col-md-5"></td>
											@if($p == 0)
												<td class="product_id">今天的配送数据现在不存在！</td>
											@else
												@foreach($dsproduction_plans as $dsp)
													<td class="product_id" value="{{$dsp->product_id}}">{{$dsp->product_name}}</td>
												@endforeach
											@endif
										</tr>

										<!-- 基础数据 -->
										<tr class="produced_tr">
											<td colspan="2" style="width: 20%">上日库存余量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="remained">{{$dsp->dp_remain_before}}</td>
											@endforeach
										</tr>
										<tr class="produced_tr">
											<td colspan="2" style="width: 20%">当日签收数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td>{{$dsp->confirm_count}}</td>
											@endforeach
										</tr>
										<tr class="produced_tr">
											<td colspan="2" style="width: 20%">当日奶站可出库数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td id="confirm_count{{$dsp->id}}" class="produced">{{$dsp->confirm_count + $dsp->dp_remain_before}}</td>
											@endforeach
										</tr>

										<!-- 配送业务数据 -->
										<tr class="order_tr">
											<td rowspan="3">配送业务</td>
											<td class="dingdan">订单数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td id="order_count{{$dsp->id}}" class="order ordered_amount">{{$dsp->order_count}}</td>
											@endforeach
										</tr>
										<tr class="order_tr">
											<td>调整数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td  class="order editable_amount @if($is_distributed!=1) editfill @endif" @if($is_distributed!=1) contenteditable="true" @endif>
													{{$dsp->changed_amount}}
												</td>
											@endforeach
										</tr>
										<tr class="sum_tr">
											<td>可配送数量合计</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td  class="sum order_sum" id="order_sum{{$dsp->id}}"></td>
											@endforeach
										</tr>

										<!-- 店内零售数据 -->
										<tr class="retail_tr">
											<td rowspan="3">店内零售</td>
											<td>订单数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="retail retail_origin" id="retail_count{{$dsp->id}}">{{$dsp->retail}}</td>
											@endforeach
										</tr>
										<tr class="retail_tr">
											<td>调整数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="retail retail_diff"></td>
											@endforeach
										</tr>
										<tr class="sum_tr">
											<td>合计</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td id="retail_sum{{$dsp->id}}" class="sum retail_sum">{{$dsp->dp_retail}}</td>
											@endforeach
										</tr>

										<!-- 试饮赠品数据 -->
										<tr class="drink_tr">
											<td rowspan="3">试饮赠品</td>
											<td>订单数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="drink drink_origin" id="drink_count{{$dsp->id}}">{{$dsp->test_drink}}</td>
											@endforeach
										</tr>
										<tr class="drink_tr">
											<td>调整数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="drink drink_diff"></td>
											@endforeach
										</tr>
										<tr class="sum_tr">
											<td>合计</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td id="drink_sum{{$dsp->id}}" class="sum drink_sum">{{$dsp->dp_test_drink}}</td>
											@endforeach
										</tr>

										<!-- 团购业务数据 -->
										<tr class="group_tr">
											<td rowspan="3">团购业务</td>
											<td>订单数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="group group_origin" id="group_count{{$dsp->id}}">{{$dsp->group_sale}}</td>
											@endforeach
										</tr>
										<tr class="group_tr">
											<td>调整数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="group group_diff"></td>
											@endforeach
										</tr>
										<tr class="sum_tr">
											<td>合计</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="sum group_sum" id="group_sum{{$dsp->id}}">{{$dsp->dp_group_sale}}</td>
											@endforeach
										</tr>

										<!-- 渠道业务数据 -->
										<tr class="channel_tr">
											<td rowspan="3">渠道业务</td>
											<td>订单数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="channel channel_origin" id="channel_count{{$dsp->id}}">{{$dsp->channel_sale}}</td>
											@endforeach
										</tr>
										<tr class="channel_tr">
											<td>调整数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="channel channel_diff"></td>
											@endforeach
										</tr>
										<tr class="sum_tr">
											<td>合计</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="sum channel_sum" id="channel_sum{{$dsp->id}}">{{$dsp->dp_channel_sale}}</td>
											@endforeach
										</tr>

										<!-- 结果数据 -->
										<tr>
											<td colspan="2">出库总计</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td id="total{{$dsp->id}}" class="total_sum"></td>
											@endforeach
										</tr>
										<tr>
											<td colspan="2">配送业务实际配送数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td id="delivered{{$dsp->id}}" class="delivered_sum">{{$dsp->deliverd_count}}</td>
											@endforeach
										</tr>
										<tr>
											<td colspan="2">当日库存剩余</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td id="remain_amount{{$dsp->id}}" class="remain_sum">{{$dsp->dp_remain}}</td>
											@endforeach
										</tr>
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
									@if($cp->plan_count != $cp->changed_plan_count)
									<?php $i++; ?>
									<tr id="{{$cp->id}}" value="{{$cp->order_product->product->id}}">
										<td>{{$i}}</td>
										<td>{{$cp->deliver_at}}</td>
										<td>{{$cp->order->number}}</td>
										<td>{{$cp->order->customer->name}}</td>
										<td>{{trim($cp->order->address," ")}}</td>
										<td>{{$cp->order_product->product->simple_name}}</td>
										<td>{{$cp->plan_count}}</td>
										<td>{{$cp->changed_plan_count}}</td>
										@if($is_distributed!=1)
											<td contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
										@else
											<td>{{$cp->delivery_count}}</td>
										@endif
										<td>{{$cp->order->phone}}</td>
										<td>{{$cp->milkman->name}}</td>
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
									@endif
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
							<button class="btn btn-success shengchan-peisong" style="width: 100%;" @if ($is_received == 0) disabled @endif>
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
	<!--Save & Update User Information-->
	<script src="<?=asset('js/ajax/naizhan_peisongguanli_ajax.js') ?>"></script>

@endsection