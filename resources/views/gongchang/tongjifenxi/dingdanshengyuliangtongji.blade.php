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

			@include('gongchang.tongjifenxi.header', [
				'dateRange' => false,
			])

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
	<script src="<?=asset('js/pages/gongchang/dingdanshengyuliangtongji.js?170905') ?>"></script>
@endsection