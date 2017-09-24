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
				<li class="active"><strong>奶品配送统计</strong></li>
			</ol>
		</div>
		<div class="row">	

		@include('gongchang.tongjifenxi.header', [
			'dateRange' => true,
		])

		<div class="ibox float-e-margins">
			<div class="ibox-content">

				<table id="table1" class="footable table table-bordered" data-page-size="{{count($products)*3}}">
					<thead>
						<tr>
							<th data-sort-ignore="true">序号</th>
							<th data-sort-ignore="true">区域</th>
							<th data-sort-ignore="true">分区</th>
							<th data-sort-ignore="true">奶站名称</th>
							<th data-sort-ignore="true">奶品</th>
							<th data-sort-ignore="true">微信支付（瓶）</th>
							<th data-sort-ignore="true">奶卡支付（瓶）</th>
							<th data-sort-ignore="true">现金支付（瓶）</th>
							<th data-sort-ignore="true">站内零售（瓶）</th>
							<th data-sort-ignore="true">试饮赠品（瓶）</th>
							<th data-sort-ignore="true">团购业务（瓶）</th>
							<th data-sort-ignore="true">渠道销售数量（瓶）</th>
						</tr>
					</thead>
					<tbody>
					<?php $i = 0; ?>
					@foreach($stations as $st)
						<?php $i++; $j = 0; ?>
						@foreach ($products as $pd)
						<tr>
							@if ($j == 0)
							<!-- 序号 -->
							<td rowspan="{{count($products)}}">{{$i}}</td>
							<!-- 区域 -->
							<td rowspan="{{count($products)}}">{{$st[0]['province']}}</td>
							<!-- 分区 -->
							<td rowspan="{{count($products)}}">{{$st[0]['district']}}</td>
							<!-- 奶站名称 -->
							<td rowspan="{{count($products)}}">{{$st[0]['name']}}</td>
							@endif
							<!-- 奶品 -->
							<td>{{$pd->simple_name}}</td>
							<!-- 微信支付（瓶） -->
							<td>{{getEmptyArrayValue($st, 1, $pd->id, \App\Model\BasicModel\PaymentType::PAYMENT_TYPE_WECHAT)}}</td>
							<!-- 奶卡支付（瓶） -->
							<td>{{getEmptyArrayValue($st, 1, $pd->id, \App\Model\BasicModel\PaymentType::PAYMENT_TYPE_CARD)}}</td>
							<!-- 现金支付（瓶） -->
							<td>{{getEmptyArrayValue($st, 1, $pd->id, \App\Model\BasicModel\PaymentType::PAYMENT_TYPE_MONEY_NORMAL)}}</td>
							<!-- 站内零售（瓶） -->
							<td>{{getEmptyArrayValue($st, 2, $pd->id, \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_RETAIL)}}</td>
							<!-- 试饮赠品（瓶） -->
							<td>{{getEmptyArrayValue($st, 2, $pd->id, \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_TESTDRINK)}}</td>
							<!-- 团购业务（瓶） -->
							<td>{{getEmptyArrayValue($st, 2, $pd->id, \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_GROUP)}}</td>
							<!-- 渠道销售数量（瓶） -->
							<td>{{getEmptyArrayValue($st, 2, $pd->id, \App\Model\DeliveryModel\MilkManDeliveryPlan::MILKMAN_DELIVERY_PLAN_TYPE_CHANNEL)}}</td>
						</tr>
						<?php $j++; ?>
						@endforeach
					@endforeach
					</tbody>
					<tfoot>
						<tr>
							<td colspan="12">
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
	<script src="<?=asset('js/pages/gongchang/naipinpeisongtongji.js?170905') ?>"></script>
@endsection