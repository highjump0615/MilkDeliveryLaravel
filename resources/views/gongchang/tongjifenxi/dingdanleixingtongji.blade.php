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
				<li class="active"><strong>订单类型统计</strong></li>
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

					<div class="form-group col-md-4" id="data_range_select">
						<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
						<div class="input-daterange input-group col-md-8" id="datepicker">
							<input type="text" class="input-sm form-control" name="start" id="start_date" value="{{$start_date}}"/>
							<span class="input-group-addon">至</span>
							<input type="text" class="input-sm form-control" name="end" id="end_date" value="{{$end_date}}"/>
						</div>
					</div>
					<div class="col-md-2"  style="padding-top:5px;">
						<button type="button" id="search" class="btn btn-success btn-md">筛选</button>
						&nbsp;
						<button class="btn btn-success btn-outline btn-m-d" data-action="export_csv">导出</button>
						&nbsp;
						<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
					</div>
				</div>
			</div>

			<div class="ibox float-e-margins">
				<div class="ibox-content">

					<table id="order_type_table" class="footable table table-bordered" data-page-size="{{$count*2+6}}">
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
							<th data-sort-ignore="true">新单</th>
							<th data-sort-ignore="true">续单</th>
							<th data-sort-ignore="true">新单</th>
							<th data-sort-ignore="true">续单</th>
							<th data-sort-ignore="true">新单</th>
							<th data-sort-ignore="true">续单</th>
							<th data-sort-ignore="true">新单</th>
							<th data-sort-ignore="true">续单</th>
						</tr>
						</thead>
						<tbody>
						<?php $i = 0; ?>
						@foreach($stations as $st)
							<?php $i++; $j =0; ?>
							@foreach($st->product as $p)
								<?php $j++; ?>
								<tr class="milk">
									@if($j == 1)
										<td rowspan="{{count($st->product)+3}}">{{$i}}</td>
										<td rowspan="{{count($st->product)+3}}">{{$st->city}}</td>
										<td rowspan="{{count($st->product)+3}}">{{$st->district}}</td>
										<td rowspan="{{count($st->product)+3}}">{{$st->name}}</td>
									@endif
									<td>{{$p->name}}</td>
									<td class="total">{{$p->yuedan_xin}}</td>
									<td class="remain">{{$p->yuedan_xu}}</td>
									<td class="total">{{$p->jidan_xin}}</td>
									<td class="remain">{{$p->jidan_xu}}</td>
									<td class="total">{{$p->banniandan_xin}}</td>
									<td class="remain">{{$p->banniandan_xu}}</td>
									<td class="f_total"></td>
									<td class="f_remain"></td>
								</tr>
							@endforeach
							<tr class="milk">
								<td>订单产品数量合计</td>
								<td class="total">{{$st->yuedan_xin_total}}</td>
								<td class="remain">{{$st->yuedan_xu_total}}</td>
								<td class="total">{{$st->jidan_xin_total}}</td>
								<td class="remain">{{$st->jidan_xu_total}}</td>
								<td class="total">{{$st->banniandan_xin_total}}</td>
								<td class="remain">{{$st->banniandan_xu_total}}</td>
								<td class="f_total"></td>
								<td class="f_remain"></td>
							</tr>
							<tr class="milk_amount">
								<td>单数合计</td>
								<td class="total">{{round($st->yuedan_xin_total/30, 2)}}</td>
								<td class="remain">{{round($st->yuedan_xu_total/30, 2)}}</td>
								<td class="total">{{round($st->jidan_xin_total/90, 2)}}</td>
								<td class="remain">{{round($st->jidan_xu_total/90, 2)}}</td>
								<td class="total">{{round($st->banniandan_xin_total/180, 2)}}</td>
								<td class="remain">{{round($st->banniandan_xu_total/180, 2)}}</td>
								<td class="f_total"></td>
								<td class="f_remain"></td>
							</tr>
							<tr class="milk_amount">
								<td>订单金额合计</td>
								<td class="total">{{$st->yuedan_xin_amount}}</td>
								<td class="remain">{{$st->yuedan_xu_amount}}</td>
								<td class="total">{{$st->jidan_xin_amount}}</td>
								<td class="remain">{{$st->jidan_xu_amount}}</td>
								<td class="total">{{$st->banniandan_xin_amount}}</td>
								<td class="remain">{{$st->banniandan_xu_amount}}</td>
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
	<script src="<?=asset('js/pages/gongchang/dingdanleixingtongji.js') ?>"></script>
@endsection