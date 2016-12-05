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
					<a href="{{ url('naizhan/dingdan')}}">订单管理</a>
				</li>
				<li class="active">
					<strong>续单列表</strong>
				</li>
			</ol>
		</div>
		<div class="row wrapper">
			<div class="wrapper-content">
				<div class="feed-element">
					<div class="vertical-align">
						<label class="col-md-1 control-label">收货人:</label>
						<div class="col-md-2"><input type="text" placeholder="" class="form-control" value=""
													 id="filter_customer"></div>
						<label class="col-md-1 control-label">电话:</label>
						<div class="col-md-2"><input type="text" placeholder="" class="form-control" value=""
													 id="filter_phone"></div>
						<label class="col-md-1 control-label">奶站:</label>
						<div class="col-md-2">
							<select id="filter_station" class="chosen-select form-control" style="height:35px;"
									tabindex="2">
								<option value="none"></option>
								@if (isset($factory) and ($factory->deliveryStations))
									@foreach($factory->deliveryStations as $station)
										<option value="{{$station->name}}">{{$station->name}}</option>
									@endforeach
								@endif
							</select>
						</div>
						<label class="col-md-1 control-label">订单性质:</label>
						<div class="col-md-2">
							<select id="filter_delivery_property" class="chosen-select form-control"
									style="height:35px;" tabindex="2">
								<option value="none"></option>
								@if (isset($order_properties))
									@foreach($order_properties as $orderproperty)
										<option value="{{$orderproperty->name}}">{{$orderproperty->name}}</option>
									@endforeach
								@else
									<option value="1">新单</option>
									<option value="2">续单</option>
								@endif
							</select>
						</div>
					</div>
				</div>
				<div class="feed-element">
					<div class="vertical-align">
						<label class="col-md-1 control-label">订单编号:</label>
						<div class="col-md-2"><input type="text" placeholder="" class="form-control" value=""
													 id="filter_number"></div>
						<label class="col-md-1 control-label">征订员:</label>
						<div class="col-md-2"><input id="filter_order_checker" type="text" placeholder=""
													 class="form-control" value=""></div>
						<label class="col-md-1 control-label">订单类型:</label>
						<div class="col-md-2">
							<select data-placeholder="" id="filter_term_kind" class="chosen-select form-control"
									style="height:35px;" tabindex="2">
								<option value="none"></option>
								@if ( isset($factory) and ($factory->factory_order_types) )
									@foreach($factory->factory_order_types as $fot)
										<option value="{{$fot->order_type_name}}">{{$fot->order_type_name}}</option>
									@endforeach
								@else
									<option value="月单">月单</option>
									<option value="季单">季单</option>
									<option value="半年单">半年单</option>
								@endif
							</select>
						</div>
						<label class="col-md-1 control-label">支付:</label>
						<div class="col-md-2">
							<select data-placeholder="" id="filter_payment_type" class="chosen-select form-control"
									style="height:35px;" tabindex="2">
								<option value="none"></option>
								@if ( isset($payment_types))
									@foreach($payment_types as $pt)
										<option value="{{$pt->name}}">{{$pt->name}}</option>
									@endforeach
								@else
									<option value="微信">微信</option>
									<option value="现金">现金</option>
									<option value="奶卡">奶卡</option>
								@endif
							</select>
						</div>
					</div>
				</div>
				<div class="feed-element">
					<div class="vertical-align">
						<label class="col-md-1 control-label">订单状态:</label>
						<div class="col-md-2">
							<select data-placeholder="" id="filter_status" class="chosen-select form-control"
									style="height:35px;" tabindex="2">
								<option value="none"></option>
								<option value="{{\App\Model\OrderModel\Order::ORDER_WAITING_STATUS}}">待审核</option>
								<option value="{{\App\Model\OrderModel\Order::ORDER_ON_DELIVERY_STATUS}}">在配送</option>
								<option value="{{\App\Model\OrderModel\Order::ORDER_PASSED_STATUS}}">未起奶</option>
								<option value="{{\App\Model\OrderModel\Order::ORDER_STOPPED_STATUS}}">暂停</option>
								<option value="{{\App\Model\OrderModel\Order::ORDER_FINISHED_STATUS}}">已完成</option>
								<option value="{{\App\Model\OrderModel\Order::ORDER_NOT_PASSED_STATUS}}">未通过</option>
								<option value="{{\App\Model\OrderModel\Order::ORDER_CANCELLED_STATUS}}">退订</option>
							</select>
						</div>
					</div>
					<div class="feed-element form-group" id="data_range_select">
						<label class="col-md-2 control-label">下单日期:</label>
						<div class="input-daterange input-group col-md-4" id="datepicker">
							<input type="text" id="filter_order_start_date" class="input-sm form-control" name="start"/>
							<span class="input-group-addon">至</span>
							<input type="text" id="filter_order_end_date" class="input-sm form-control" name="end"/>
						</div>
					</div>
				</div>
				<div class="feed-element">
					<div class="col-md-2 col-md-offset-10 button-div">
						<button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
						<button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
					</div>
				</div>

				<div class="ibox float-e-margins white-bg">
					<div class="ibox-content">
						<table id="order_table" class="table table-bordered footable" data-sort-ignore="true" data-page-size="10" data-limit-navigation="5" style="display:none;">
							<thead>
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">订单号</th>
									<th data-sort-ignore="true">收货人</th>
									<th data-sort-ignore="true">电话</th>
									<th data-sort-ignore="true">地址</th>
									<th data-sort-ignore="true">订单类型</th>
									<th data-sort-ignore="true">订单金额</th>
									<th data-sort-ignore="true">征订员</th>
									<th data-sort-ignore="true">区域</th>
									<th data-sort-ignore="true">分区</th>
									<th data-sort-ignore="true">奶站</th>
									<th data-sort-ignore="true">配送员</th>
									<th data-sort-ignore="true">下单日期</th>
									<th data-sort-ignore="true">支付</th>
									<th data-sort-ignore="true">订单性质</th>
									<th data-sort-ignore="true">状态</th>
									<th data-sort-ignore="true">备注</th>
								</tr>
							</thead>
							<tbody>
								@if (isset($orders))
									@for($i =0; $i<count($orders); $i++)
										<tr data-orderid="{{$orders[$i]->id}}" class="row-hover-light-blue">
											<td>{{$i+1}}</td>
											<td class="o_number">{{$orders[$i]->number}}</td>
											<td class="o_customer_name">{{$orders[$i]->customer_name}}</td>
											<td class="o_phone">{{$orders[$i]->phone}}</td>
											<td class="o_addr">{{$orders[$i]->customer->address}}</td>
											<td class="o_type">{{$orders[$i]->all_order_types}}</td>
											<td class="o_total">{{$orders[$i]->total_amount}}</td>
											<td class="o_checker">{{$orders[$i]->order_checker_name}}</td>
											<td class="o_city">{{$orders[$i]->city_name}}</td>
											<td class="o_street">{{$orders[$i]->district_name}}</td>
											<td class="o_station">{{$orders[$i]->delivery_station_name}}</td>
											<td class="milkman">{{$orders[$i]->milkman_name}}</td>
											<td class="o_ordered">{{$orders[$i]->ordered_at}}</td>
											<td class="o_paytype">{{$orders[$i]->payment_type_name}}</td>
											<td class="o_property">{{$orders[$i]->order_property_name}}</td>
											<td class="o_status" data-status="{{$orders[$i]->status}}" data-value="{{$orders[$i]->status_name}}" style="width: 70px;">
												{{$orders[$i]->status_name}}</td>
											<td>{{$orders[$i]->comment}}</td>
										</tr>
									@endfor
								@endif

							</tbody>
							<tfoot align="right">
								<tr>
									<td colspan="100%"><ul class="pagination pull-right"></ul></td>
								</tr>
							</tfoot>
						</table>
						<table id="filter_table" class="table table-bordered footable" data-sort-ignore="true" data-page-size="10">
							<thead>
							<tr>
								<th data-sort-ignore="true">序号</th>
								<th data-sort-ignore="true">订单号</th>
								<th data-sort-ignore="true">收货人</th>
								<th data-sort-ignore="true">电话</th>
								<th data-sort-ignore="true">地址</th>
								<th data-sort-ignore="true">订单类型</th>
								<th data-sort-ignore="true">订单金额</th>
								<th data-sort-ignore="true">征订员</td>
								<th data-sort-ignore="true">区域</td>
								<th data-sort-ignore="true">分区</td>
								<th data-sort-ignore="true">奶站</th>
								<th data-sort-ignore="true">配送员</th>
								<th data-sort-ignore="true">下单日期</th>
								<th data-sort-ignore="true">支付</th>
								<th data-sort-ignore="true">订单性质</th>
								<th data-sort-ignore="true">状态</th>
								<th data-sort-ignore="true">备注</th>
							</tr>
							</thead>
							<tbody>
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
<script type="text/javascript">
	var at_page = "xudan";
</script>
<script src="<?=asset('js/pages/naizhan/order_select_export_print.js') ?>"></script>
@endsection
