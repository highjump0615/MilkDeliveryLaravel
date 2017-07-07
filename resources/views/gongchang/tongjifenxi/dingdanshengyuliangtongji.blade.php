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
					<a href="">统计分析</a>
				</li>
				<li class="active"><strong>订单剩余量统计</strong></li>
			</ol>
		</div>
		<div class="row">
			<!--Table-->
			<div class="ibox-content">
				<div class="feed-element">
					<div class="col-md-3">
						<label>奶站名称:</label>
						<input type="text" id="station_name" value="{{$station_name}}">
					</div>
					{{--<div class="col-md-3">--}}
					{{--<label>编号:</label>--}}
					{{--<input type="text" id="">--}}
					{{--</div>--}}
					<div class="col-md-3">
						<label>区域:</label>
						&nbsp;
						<select data-placeholder="" class="chosen-select" id="area_name" tabindex="2" style="width: 180px; height: 30px;">
							<option value="">全部</option>
							@foreach($address as $addr)
								@if($addr->name == $area_name)
									<option selected value="{{$addr->name}}">{{$addr->name}}</option>
								@else
									<option value="{{$addr->name}}">{{$addr->name}}</option>
								@endif
							@endforeach
						</select>
					</div>

					<div class="col-lg-4" id="date_1">
						<label class="col-md-2 control-label" style="padding-top:7px;">日期:</label>
						<div class="input-group date col-md-6">
							<input type="text" class="form-control" id="end_date" value="{{$end_date}}"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>

					{{--<div class="form-group col-md-4" id="data_range_select">--}}
						{{--<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>--}}
						{{--<div class="input-daterange input-group col-md-8" id="datepicker">--}}
							{{--<input type="text" class="input-sm form-control" name="start" id="start_date" value="{{$start_date}}"/>--}}
							{{--<span class="input-group-addon">至</span>--}}
							{{--<input type="text" class="input-sm form-control" name="end" id="end_date" value="{{$end_date}}"/>--}}
						{{--</div>--}}
					{{--</div>--}}
					<div class="col-md-2"  style="padding-top:5px;">
						<button type="button" id="search" class="btn btn-success btn-md">筛选</button>
						&nbsp;
						<button class="btn btn-success btn-outline btn-m-d" data-action="export_csv">导出</button>
						&nbsp;
						<button class="btn btn-outline btn-success btn-m-d" data-action="print">打印</button>
					</div>
				</div>
			</div>

			<div class="ibox float-e-margins">
				<div class="ibox-content">

					<table id="order_type_table" class="footable table table-bordered" data-page-size="{{count($products)*2+6}}">
						<thead>
						<tr>
							<th rowspan="2" data-sort-ignore="true">序号</th>
							<th rowspan="2" data-sort-ignore="true">区域</th>
							<th rowspan="2" data-sort-ignore="true">分区</th>
							<th rowspan="2" data-sort-ignore="true">奶站名称</th>
							<th rowspan="2" data-sort-ignore="true">奶品名称</th>
							<th colspan="2" data-sort-ignore="true">月单</th>
							<th colspan="2" data-sort-ignore="true">季单</th>
							<th colspan="2" data-sort-ignore="true">半年单</th>
							<th colspan="2" data-sort-ignore="true">合计</th>
						</tr>
						<tr>
							<th data-sort-ignore="true">总量</th>
							<th data-sort-ignore="true">剩余量</th>
							<th data-sort-ignore="true">总量</th>
							<th data-sort-ignore="true">剩余量</th>
							<th data-sort-ignore="true">总量</th>
							<th data-sort-ignore="true">剩余量</th>
							<th data-sort-ignore="true">总量</th>
							<th data-sort-ignore="true">剩余量</th>
						</tr>
						</thead>
						<tbody>
						<?php $i = 0; ?>
						@foreach($stations as $st)
							<?php $i++; $j =0; ?>
							@foreach ($products as $pd)
								<?php $j++; ?>
								<tr class="milk">
									@if($j == 1)
										<!-- 序号 -->
										<td rowspan="{{count($products)+3}}">{{$i}}</td>
										<!-- 区域 -->
										<td rowspan="{{count($products)+3}}">{{$st[0]['province']}}</td>
										<!-- 分区 -->
										<td rowspan="{{count($products)+3}}">{{$st[0]['district']}}</td>
										<!-- 奶站名称 -->
										<td rowspan="{{count($products)+3}}">{{$st[0]['name']}}</td>
									@endif
									<!-- 奶品 -->
									<td>{{$pd->simple_name}}</td>
									<!-- 月单总量 -->
									<td class="total">
										{{getEmptyArrayValue($st, 1, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_YUEDAN, $pd->id, 0)}}
									</td>
									<!-- 月单剩余量 -->
									<td class="remain">
										{{getEmptyArrayValue($st, 1, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_YUEDAN, $pd->id, 1)}}
									</td>
									<!-- 季单总量 -->
									<td class="total">
										{{getEmptyArrayValue($st, 1, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_JIDAN, $pd->id, 0)}}
									</td>
									<!-- 季单剩余量 -->
									<td class="remain">
										{{getEmptyArrayValue($st, 1, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_JIDAN, $pd->id, 1)}}
									</td>
									<!-- 半年单总量 -->
									<td class="total">
										{{getEmptyArrayValue($st, 1, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_BANNIANDAN, $pd->id, 0)}}
									</td>
									<!-- 半年单剩余量 -->
									<td class="remain">
										{{getEmptyArrayValue($st, 1, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_BANNIANDAN, $pd->id, 1)}}
									</td>
									<!-- 合计总量 -->
									<td class="f_total"></td>
									<!-- 合计剩余量 -->
									<td class="f_remain"></td>
								</tr>
							@endforeach
							<tr class="milk">
								<td>订单产品数量合计</td>
								<!-- 月单总量 -->
								<td class="total">
									{{getEmptyArrayValue($st, 2, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_YUEDAN, 0)}}
								</td>
								<!-- 月单剩余量 -->
								<td class="remain">
									{{getEmptyArrayValue($st, 2, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_YUEDAN, 1)}}
								</td>
								<!-- 季单总量 -->
								<td class="total">
									{{getEmptyArrayValue($st, 2, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_JIDAN, 0)}}
								</td>
								<!-- 季单剩余量 -->
								<td class="remain">
									{{getEmptyArrayValue($st, 2, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_JIDAN, 1)}}
								</td>
								<!-- 半年单总量 -->
								<td class="total">
									{{getEmptyArrayValue($st, 2, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_BANNIANDAN, 0)}}
								</td>
								<!-- 半年单剩余量 -->
								<td class="remain">
									{{getEmptyArrayValue($st, 2, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_BANNIANDAN, 1)}}
								</td>
								<td class="f_total"></td>
								<td class="f_remain"></td>
							</tr>
							<tr class="milk_amount">
								<td>单数合计</td>
								<!-- 月单总量 -->
								<td class="total">
									{{round(getEmptyArrayValue($st, 2, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_YUEDAN, 0)/30, 2)}}
								</td>
								<!-- 月单剩余量 -->
								<td class="remain">
									{{round(getEmptyArrayValue($st, 2, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_YUEDAN, 1)/30, 2)}}
								</td>
								<!-- 季单总量 -->
								<td class="total">
									{{round(getEmptyArrayValue($st, 2, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_JIDAN, 0)/90, 2)}}
								</td>
								<!-- 季单剩余量 -->
								<td class="remain">
									{{round(getEmptyArrayValue($st, 2, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_JIDAN, 1)/90,2)}}
								</td>
								<!-- 半年单总量 -->
								<td class="total">
									{{round(getEmptyArrayValue($st, 2, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_BANNIANDAN, 0)/180, 2)}}
								</td>
								<!-- 半年单剩余量 -->
								<td class="remain">
									{{round(getEmptyArrayValue($st, 2, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_BANNIANDAN, 1)/180,2)}}
								</td>
								<td class="f_total"></td>
								<td class="f_remain"></td>
							</tr>
							<tr class="milk_amount">
								<td>订单金额合计</td>
								<!-- 月单总量 -->
								<td class="total">
									{{round(getEmptyArrayValue($st, 3, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_YUEDAN, 0), 2)}}
								</td>
								<!-- 月单剩余量 -->
								<td class="remain">
									{{round(getEmptyArrayValue($st, 3, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_YUEDAN, 1), 2)}}
								</td>
								<!-- 季单总量 -->
								<td class="total">
									{{round(getEmptyArrayValue($st, 3, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_JIDAN, 0), 2)}}
								</td>
								<!-- 季单剩余量 -->
								<td class="remain">
									{{round(getEmptyArrayValue($st, 3, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_JIDAN, 1), 2)}}
								</td>
								<!-- 半年单总量 -->
								<td class="total">
									{{round(getEmptyArrayValue($st, 3, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_BANNIANDAN, 0), 2)}}
								</td>
								<!-- 半年单剩余量 -->
								<td class="remain">
									{{round(getEmptyArrayValue($st, 3, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_BANNIANDAN, 1), 2)}}
								</td>
								<td class="f_total"></td>
								<td class="f_remain"></td>
							</tr>
						@endforeach
						</tbody>
						<tfoot>
						<tr>
							<td colspan="13">
								<ul class="pagination pull-right"></ul>
							</td>
						</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('script')
	<script src="<?=asset('js/pages/gongchang/dingdanshengyuliangtongji.js') ?>"></script>
@endsection