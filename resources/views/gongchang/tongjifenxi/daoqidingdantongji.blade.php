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
				<li class="active"><strong>到期订单统计</strong></li>
			</ol>
		</div>

		<div class="row">
			@include('gongchang.tongjifenxi.header', [
				'dateRange' => true,
			])

			<div class="ibox float-e-margins">
				<div class="ibox-content">

					<table id="table1" class="table table-bordered">
						<thead>
							<tr>
								<th data-sort-ignore="true">序号</th>
								<th data-sort-ignore="true">订单号</th>
								<th data-sort-ignore="true">收货人</th>
								<th data-sort-ignore="true">电话</th>
								<th data-sort-ignore="true">订单类型</th>
								<th data-sort-ignore="true">订单金额</th>
								<th data-sort-ignore="true">业务员</th>
								<th data-sort-ignore="true">区域</th>
								<th data-sort-ignore="true">分区</th>
								<th data-sort-ignore="true">奶站</th>
								<th data-sort-ignore="true">配送员</th>
								<th data-sort-ignore="true">下单日期</th>
								<th data-sort-ignore="true">支付</th>
								<th data-sort-ignore="true">到期日期</th>
								<th data-sort-ignore="true">订单来源</th>
								<th data-sort-ignore="true">客户类型</th>
								<th data-sort-ignore="true">订单详情</th>
								{{--<th data-sort-ignore="true">操     作</th>--}}
								{{--<th data-sort-ignore="true">备    注</th>--}}
							</tr>
						</thead>
						<tbody>
						<?php $i=0;?>
						@foreach($order_info as $o)
							<tr>
								<td>{{$i + $order_info->firstItem()}}</td>
								<td>{{$o->number}}</td>
								<td>{{$o->customer_name}}</td>
								<td>{{$o->phone}}</td>
								<td>{{$o->order_type}}</td>
								<td>{{$o->total_amount}}</td>
								<td>{{$o->getCheckerName()}}</td>
								<td>{{$o->getCityName()}}</td>
								<td>{{$o->getDistrictName()}}</td>
								<td>{{$o->station_name}}</td>
								<td>{{$o->milkman['name']}} {{$o->milkman['phone']}}</td>
								<td>{{$o->ordered_at}}</td>
								<td>{{$o->payment_type_name}}</td>
								<td>{{$o->order_end_date}}</td>
								<td>{{$o->status_changed_at}}</td>
								<td></td>
								<td><a href={{URL::to('/gongchang/dingdan/dingdanluru/xiangqing/'.$o->id)}}>查看</a></td>
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

@section('script')
	<script type="text/javascript">
		@if (!empty($pageName))
        var at_page = '{{$pageName}}';
		@endif

        // 全局变量
        var gnTotalPage = '{{$order_info->lastPage()}}';
        var gnCurrentPage = '{{$order_info->currentPage()}}';

        gnTotalPage = parseInt(gnTotalPage);
        gnCurrentPage = parseInt(gnCurrentPage);
	</script>

	<script type="text/javascript" src="<?=asset('js/plugins/pagination/jquery.twbsPagination.min.js')?>"></script>
	<script type="text/javascript" src="<?=asset('js/pages/gongchang/pagination.js')?>"></script>

	<script type="text/javascript" src="<?=asset('js/pages/gongchang/daoqidingdan.js?170905')?>"></script>
@endsection