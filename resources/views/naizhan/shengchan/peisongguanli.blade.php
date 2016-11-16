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

				<div class="ibox float-e-margins">
                    <div class="ibox">
                    	<div class="row">
							<input type="hidden" id="count" value="{{count($dsproduction_plans)}}">
							<div class="col-md-12">
								<table id="distribute" class="table table-bordered" style="font-size: 12px;">
									<tbody>
									<?php $p = count($dsproduction_plans); ?>
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
										<tr class="produced_tr">
											<td colspan="2" style="width: 20%">签收数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td id="confirm_count{{$dsp->id}}" class="produced">{{$dsp->confirm_count}}</td>
											@endforeach
										</tr>
										<tr class="order_tr">
											<td rowspan="3">配送计划</td>
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
												<td  class="order"  class="order">{{$dsp->changed_amount}}</td>
											@endforeach
										</tr>
										<tr class="sum_tr">
											<td>合计</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td  class="sum order_sum" id="order_sum{{$dsp->id}}"></td>
											@endforeach
										</tr>
										<tr class="retail_tr">
											<td rowspan="3">站内零售</td>
											<td>计划数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="retail" id="retail_count{{$dsp->id}}">{{$dsp->retail}}</td>
											@endforeach
										</tr>
										<tr class="retail_tr">
											<td>调整数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td @if($is_distributed != 1) contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1" @endif class="retail editable_amount" id="{{$dsp->id}}">{{$dsp->dp_retail}}</td>
											@endforeach
										</tr>
										<tr class="sum_tr">
											<td>合计</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td id="retail_sum{{$dsp->id}}" class="sum retail_sum"></td>
											@endforeach
										</tr>
										<tr class="drink_tr">
											<td rowspan="3">团购业务</td>
											<td>计划数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="drink" id="drink_count{{$dsp->id}}">{{$dsp->test_drink}}</td>
											@endforeach
										</tr>
										<tr class="drink_tr">
											<td>调整数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td @if($is_distributed != 1) contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1" @endif id="{{$dsp->id}}" class="editable_amount drink">{{$dsp->dp_test_drink}}</td>
											@endforeach
										</tr>
										<tr class="sum_tr">
											<td>合计</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td id="drink_sum{{$dsp->id}}" class="sum drink_sum"></td>
											@endforeach
										</tr>
										<tr class="group_tr">
											<td rowspan="3">渠道销售</td>
											<td>计划数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="group" id="group_count{{$dsp->id}}">{{$dsp->group_sale}}</td>
											@endforeach
										</tr>
										<tr class="group_tr">
											<td>调整数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td @if($is_distributed != 1) contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1" @endif class="editable_amount group" id="{{$dsp->id}}">{{$dsp->dp_group_sale}}</td>
											@endforeach
										</tr>
										<tr class="sum_tr">
											<td>合计</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="sum group_sum" id="group_sum{{$dsp->id}}"></td>
											@endforeach
										</tr>
										<tr class="channel_tr">
											<td rowspan="3">试饮赠品</td>
											<td>计划数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="channel" id="channel_count{{$dsp->id}}">{{$dsp->channel_sale}}</td>
											@endforeach
										</tr>
										<tr class="channel_tr">
											<td>调整数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td @if($is_distributed!=1) contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1" @endif class="editable_amount channel" id="{{$dsp->id}}">{{$dsp->dp_channel_sale}}</td>
											@endforeach
										</tr>
										<tr class="sum_tr">
											<td>合计</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td class="sum channel_sum" id="channel_sum{{$dsp->id}}"></td>
											@endforeach
										</tr>
										<tr>
											<td colspan="2">总计</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td id="total{{$dsp->id}}" class="total_sum"></td>
											@endforeach
										</tr>
										<tr>
											<td colspan="2">计划差异</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td id="plan{{$dsp->id}}" class="plan_sum"></td>
											@endforeach
										</tr>
										<tr>
											<td colspan="2">可配送数量</td>
											@if($p == 0)
												<td></td>
											@endif
											@foreach($dsproduction_plans as $dsp)
												<td id="delivery_amount{{$dsp->id}}" class="remain_as_order"></td>
											@endforeach
										</tr>
									</tbody>

								</table>
							</div>
						</div>
						<div>
							<div class="col-md-3 col-md-offset-8">
								@if($is_distributed != 1)
									@if($p != 0)
									<div class="col-md-6"><button id="save_distribution" class="btn btn-success btn-outline" style="width:90%; bottom:0;">确定</button></div>
									@endif
									@if(count($changed_plans) != 0)
									<div class="col-md-6"><button class="btn btn-success auto_distribute" style="width:90%; bottom:0;">自动调配</button></div>
									@endif
								@endif
							</div>
						</div>
						<div class="ibox-content">
	                        <table id="changed_distribute" class="table footable table-bordered" style="font-size: 14px;">
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
										<td>{{$cp->order_product->product->name}}</td>
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
						@if($is_distributed!=1)
						<button class="btn btn-outline btn-success shengchan-peisong" style="width: 100%;">生成配送列表</button>
						@else
						<button onclick="window.location='{{ url('/naizhan/shengchan/peisongliebiao') }}'" class="btn btn-outline btn-success" style="width: 100%;">查看配送列表</button>
						@endif
					</div>
				</div>

			</div>
		</div>

	</div>
@endsection

@section('script')
	<!--Get API_URL-->
	<script type="text/javascript" src="<?=asset('js/global.js') ?>"></script>
	<!--Save & Update User Information-->
	<script src="<?=asset('js/ajax/naizhan_peisongguanli_ajax.js') ?>"></script>

@endsection