@extends('gongchang.layout.master')

@section('content')
	@include('gongchang.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('gongchang.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a>客户管理</a>
				</li>
				<li class="active">
					<strong>客户管理</strong>
				</li>
			</ol>
		</div>
		<div class="row white-bg">

			<!-- 筛选选择项 -->
			@include('gongchang.kehu.kehufilter')

			<!--Table-->
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<table id="customerTable" class="table table-bordered">
						<thead>
							<tr>
								<th data-sort-ignore="true">序号</th>
								<th data-sort-ignore="true">区域</th>
								<th data-sort-ignore="true">分区</th>
								<th data-sort-ignore="true">收货人</th>
								<th data-sort-ignore="true">联系电话</th>
								<th data-sort-ignore="true">奶站</th>
								<th data-sort-ignore="true">配送员</th>
								<th data-sort-ignore="true">地址</th>
								<th data-sort-ignore="true">订单状态</th>
								<th data-sort-ignore="true">下单次数</th>
								<th data-sort-ignore="true">订单余额</th>
								<th data-sort-ignore="true">账户余额</th>
								{{--<th data-sort-ignore="true">操作</th>--}}
								<th data-sort-ignore="true">备注</th>
							</tr>
						</thead>
						<tbody>
						<?php $i=0; ?>
						@foreach($customers as $cu)
							<tr>
								<td>{{$i + $customers->firstItem()}}</td>
								<td class="area align-left">{{$cu->area_addr}}</td>
								<td class="align-left">{{$cu->sector_addr}}</td>
								<td class="user">{{$cu->name}}</td>
								<td class="phone">{{$cu->phone}}</td>
								<td>{{$cu->station_name}}</td>
								<td>{{$cu->milkman_name}}</td>
								<td class="align-left">{{$cu->detail_addr}}</td>
								<td>{{$cu->order_status}}</td>
								<td>{{$cu->order_count}}</td>
								<td>{{$cu->order_balance}}</td>
								<td>{{$cu->remain_amount}}</td>
								<td></td>
							</tr>
                            <?php $i++; ?>
						@endforeach
						</tbody>
					</table>

					<ul id="pagination_data" class="pagination-sm pull-right"></ul>

				</div>
			</div>
		</div>
	</div>
@endsection

<!-- script -->
@include('gongchang.kehu.kehuscript', [
    'isStation' => false,
])