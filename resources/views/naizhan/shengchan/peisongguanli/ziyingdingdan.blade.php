@extends('naizhan.layout.master')
@section('css')
@endsection

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="{{ url('naizhan/shengchan/jihuaguanli')}}">生产与配送</a>
				</li>
				<li class="active">
					<strong>自营出库任务分配</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">

				<!-- 库存统计 -->
				<div class="ibox float-e-margins">
					<div class="col-lg-12"><label class="col-lg-12">自营库存量：</label></div>
					<div class="col-lg-1"></div>
                    <div class="col-lg-10">
                        <table id="produced_milk" class="table footable table-bordered">
                            <thead style="background-color:#33cccc;">
								<tr>
									<th data-sort-ignore="true" rowspan="2">奶品</th>
									<th data-sort-ignore="true" rowspan="2">可出库数量</th>
									<th data-sort-ignore="true" colspan="4">出库数量</th>
									<th data-sort-ignore="true" rowspan="2">当日库存剩余</th>
								</tr>
								<tr>
									<th>店内零售</th>
									<th>团购业务</th>
									<th>渠道销售</th>
									<th>试饮赠品</th>
								</tr>
                            </thead>
                            <tbody>
							@foreach($delivery_plans as $ds)
								<tr>
									<td>{{$ds->product_name}}</td>
									<td>{{$ds->remain}}</td>
									<td id="retail{{$ds->product_id}}">{{$ds->retail}}</td>
									<td id="group{{$ds->product_id}}">{{$ds->group_sale}}</td>
									<td id="channel{{$ds->product_id}}">{{$ds->channel_sale}}</td>
									<td id="test{{$ds->product_id}}">{{$ds->test_drink}}</td>
									<td id="rest_amount{{$ds->product_id}}">{{$ds->remain_final}}</td>
								</tr>
							@endforeach
                            </tbody>
                        </table>
                    </div>
					<div class="col-lg-1"></div>
				</div>

				<div class="col-lg-12">
					<label class="col-lg-12 gray-bg" style="padding:10px;">添加自营出库任务</label>
				</div>
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-4">
							<label class="col-lg-4" style="padding-top: 5px;">收货人:</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="customer_name" style="width:100%;" value="">
							</div>
						</div>
						<label id="name_alert" style="color: red; padding-top: 5px; display: none;">(输入用户名!)</label>
					</div>
					<div class="feed-element">
						<div class="col-md-4">
							<label class="col-lg-4" style="padding-top: 5px;">电话:</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" name="phone" id="phone_number" style="width:100%;" value="">
							</div>	
						</div>
						<label id="phone_alert" style="color: red; padding-top: 5px; display: none;">(手机号码不符合!)</label>
					</div>
					<div class="feed-element col-md-8">
						<label class="control-label col-md-2" style="padding-top: 5px;">收货地址：</label>
						&nbsp;
						<label id="current_district">{{$current_district}}</label>
						<input type="hidden" id="addr_district" value="{{$addr_district}}">
						<select id="address4" data-placeholder="" class="street_list chosen-select form-control" style="width: 15%; display: inline">
							@foreach($streets as $st)
								<option value="{{$st}}">{{$st}}</option>
							@endforeach
						</select>
						<select id="address5" data-placeholder="" class="xiaoqu_list chosen-select form-control" style="width: 15%; display: inline">
						</select>
						<label id="address_message" class="address_message" style="padding-top: 10px; color: red"></label>
					</div>
					<div class="feed-element col-md-8">
						<div class="col-md-2"></div>
						<div class="col-md-5">
							<input id="address6" type="text" class="form-control bottle_input" placeholder="填写详细地址" value="" style="width: 100%;">
						</div>
					</div>
					<div class="feed-element col-md-8">
						<label class="control-label col-md-2" style="padding-top: 5px;">分类:</label>
						<div class="col-md-4" style="padding-left: 10px;">
							<select data-placeholder="" id="type" class="form-control chosen-select" style="width:100%;" tabindex="2">
								<option value="{{\App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK}}">试饮赠品</option>
								<option value="{{\App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_GROUP}}">团购订单</option>
								<option value="{{\App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL}}">渠道订单</option>
								<option value="{{\App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_RETAIL}}">店内零售</option>
							</select>
						</div>
					</div>
					<div class="feed-element col-md-8">
						<label class="control-label col-md-2" style="padding-top: 5px;">配送员:</label>
						<div class="col-md-4" style="padding-left: 10px;">
							<select data-placeholder="" id="milkman_name" class="form-control chosen-select" style="width:100%;" tabindex="2">
								@foreach($milk_man as $mm)
									<option value="{{$mm->id}}">{{$mm->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="feed-element col-md-8">
						<label class="control-label col-md-2" style="padding-top: 5px;">配送时间:</label>
						<div class="col-md-4" style="padding-left: 10px;">
							<select data-placeholder="" id="time" class="form-control chosen-select" style="width:100%;" tabindex="2">
								<option value="{{\App\Model\OrderModel\Order::ORDER_DELIVERY_TIME_MORNING}}">上午</option>
								<option value="{{\App\Model\OrderModel\Order::ORDER_DELIVERY_TIME_AFTERNOON}}">下午</option>
							</select>
						</div>	
					</div>
					
					<div class="feed-element col-md-12">
						<label class="control-label col-md-2" style="padding-top: 5px;">配送内容:</label>
						<div class="col-md-6">
							<table id="product_deliver" class="table footable table-bordered">
								<tbody>
								@foreach($delivery_plans as $dp)
									<tr id="{{$dp->product_id}}">
										<td class="col-md-6"><label>{{$dp->product_name}}</label>
										</td>
										<td classs="col-md-6">
											<label class="col-lg-4">数量</label>
											<div class="col-lg-6">
												<input name="{{$dp->product_name}}" id="amount{{$dp->product_id}}" class="amount" type="number" style="width:100%;">
											</div>
										</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
						<div class="col-md-4"><label id="alert_message" class="alert_message" style="padding-top: 10px; color: red"></label></div>
					</div>
					<div class="col-md-1 col-md-offset-10">
							<button id="add" class="btn btn-success" style="width:100%;">添加</button>
					</div>
				</div>

				<br>

				<div class="ibox-content">
                    <div class="col-lg-12">
                        <table id="delivery_milk" class="table footable table-bordered">
                            <thead style="background-color:#33cccc;">
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">分类</th>
									<th data-sort-ignore="true">地址</th>
									<th data-sort-ignore="true">收货人</th>
									<th data-sort-ignore="true">配送内容</th>
									<th data-sort-ignore="true">电话</th>
									<th data-sort-ignore="true">配送员</th>
									<th data-sort-ignore="true">配送时间</th>
									<th data-sort-ignore="true">备注</th>
								</tr>
                            </thead>
                            <tbody>
								<tr id="tr_add_notice">
									<td colspan="9" rowspan="2">请添加自营出库</td>
								</tr>
                            </tbody>
                        </table>
                    </div>

					<!-- 操作按钮 -->
					<div class="col-lg-4"></div>
					<div class="col-lg-1">
						<button id="but_print" type="button" class="btn btn-success" style="width:100%;">打印</button>
					</div>
					<div class="col-lg-2">
						<button id="save" type="button" class="btn btn-success" style="width:100%;">保 存</button>
					</div>
					<div class="col-lg-1">
						<button class="btn btn-success btn-outline" id="plan_cancel" style="width:100%;">取消</button>
					</div>
				</div>

				<div class="col-lg-12"><label></label></div>

				<!-- 今日自营配送单 -->
				@if (count($milkman_delivery_plans) > 0)

				<div class="col-lg-12">
					<label class="col-lg-12 gray-bg" style="padding:10px;">今日自营出库单</label>
				</div>

				<div class="col-lg-12">
					<table class="table footable table-bordered">
						<thead style="background-color:#33cccc;">
						<tr>
							<th data-sort-ignore="true">序号</th>
							<th data-sort-ignore="true">分类</th>
							<th data-sort-ignore="true">地址</th>
							<th data-sort-ignore="true">收货人</th>
							<th data-sort-ignore="true">配送内容</th>
							<th data-sort-ignore="true">电话</th>
							<th data-sort-ignore="true">配送员</th>
							<th data-sort-ignore="true">配送时间</th>
							<th data-sort-ignore="true">备注</th>
						</tr>
						</thead>
						<tbody>
						<?php $i=0; ?>
						@foreach($milkman_delivery_plans as $mdp)
							<?php $i++; ?>
							<tr>
								<td>{{$i}}</td>
								<td>
									@if ($mdp->delivery_type == \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK)
										试饮赠品
									@elseif ($mdp->delivery_type == \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_GROUP)
										团购订单
									@elseif ($mdp->delivery_type == \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL)
										渠道订单
									@elseif ($mdp->delivery_type == \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_RETAIL)
										赠品订单
									@endif
								</td>
								<td>{{$mdp->address}}</td>
								<td>{{$mdp->customer_name}}</td>
								<td>{{$mdp->product}}</td>
								<td>{{$mdp->phone}}</td>
								<td>{{$mdp->milkman_name}}</td>
								<td>
									@if($dp->delivery_time == 0)上午
									@elseif($dp->delivery_time == 1)下午
									@endif
								</td>
								<td>{{$mdp->comment_delivery}}</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>

				@endif

			</div>
		</div>

	</div>
@endsection

@section('script')
	<!--Save & Cancel Information-->
	<script src="<?=asset('js/ajax/naizhan_ziyingdingdan_ajax.js') ?>"></script>
@endsection
