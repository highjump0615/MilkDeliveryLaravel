@extends('gongchang.layout.master')

@section('css')

@endsection

@section('content')
	@include('gongchang.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('gongchang.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a>瓶框管理</a>
				</li>
				<li class="active">
					<strong>瓶框库存管理</strong>
				</li>
			</ol>
		</div>
		<div class="row">
			@if($today_status == 0)
				<div class="ibox-content">
					<p><b>&nbsp;&nbsp;&nbsp;今天状态</b></p>
					<table id="today_input" class="table table-bordered">
						<thead>
						<tr>
							<th data-sort-ignore="true">瓶类</th>
							<th data-sort-ignore="true">期初库存</th>
							<th data-sort-ignore="true">物流退回数</th>
							<th data-sort-ignore="true">其他退回数</th>
							<th data-sort-ignore="true">生产领用数</th>
							<th data-sort-ignore="true">库内盘亏数</th>
							<th data-sort-ignore="true">期未库存</th>
							<th data-sort-ignore="true" style="background-color: #0b8cc5; color: #FFFFFF">奶站交物流数</th>
							<th data-sort-ignore="true" style="background-color: #0b8cc5; color: #FFFFFF">物流存量</th>
						</tr>
						</thead>
						<tbody>
						<?php $i=0; ?>
						@foreach($today_bottle_info as $bottle_type=>$tb)
							<tr>
								<td  value="{{$bottle_type}}">{{$tb['name']}}</td>
								<td>{{$tb['init_store_count']}}</td>
								<td>{{$tb['station_refunds_count']}}</td>
								<td class="inputable_cell" contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
								<td class="inputable_cell" contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
								<td class="inputable_cell" contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
								<td></td>
								<td contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
								<td contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
								<input type="hidden" id="type" value="0">
							</tr>
						@endforeach
						@foreach($today_box_info as $box_type=>$xb)
							<tr>
								<td value="{{$box_type}}">{{$xb['name']}}</td>
								<td>{{$xb['init_store_count']}}</td>
								<td>{{$xb['station_refunds_count']}}</td>
								<td class="inputable_cell" contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
								<td class="inputable_cell" contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
								<td class="inputable_cell" contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
								<td></td>
								<td contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
								<td contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
								<input type="hidden" id="type" value="1">
							</tr>
						@endforeach
						</tbody>
					</table>
					<div class="ibox-content" style="text-align: right">
						<button id="save" class="btn btn-md btn-success" style="width: 120px;">保存</button>
					</div>
				</div>
		@endif
				<div class="ibox-content">
					<div class="feed-element">
						<div class="form-group col-lg-4" id="data_calendar">
							<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
							<div class="input-daterange input-group col-md-8" id="datepicker">
								<input type="text" class="input-sm form-control" name="start" value="{{$start_date}}"/>
								<span class="input-group-addon">至</span>
								<input type="text" class="input-sm form-control" name="end" value="{{$end_date}}"/>
							</div>
						</div>
						<div class="col-md-3">
							<label>奶瓶规格:</label>
							<input type="text" id="bottle_type" value="{{$bottle_name}}">
						</div>
						<div class="col-md-3">
							<label>奶筐规格:</label>
							<input type="text" id="box_type" value="{{$box_name}}">
						</div>
						<div class="col-md-2">
							<button type="button" id="search" class="btn btn-success btn-md">筛选</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="export_csv">导出</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
						</div>
					</div>
				</div>
				<div class="ibox-content">

					<table id="table1" class="table table-bordered" data-page-size="10">
						<thead>
						<tr>
							<th data-sort-ignore="true">序号</th>
							<th data-sort-ignore="true">时间</th>
							<th data-sort-ignore="true">瓶类</th>
							<th data-sort-ignore="true">期初库存</th>
							<th data-sort-ignore="true">物流退回数</th>
							<th data-sort-ignore="true">其他退回数</th>
							<th data-sort-ignore="true">生产领用数</th>
							<th data-sort-ignore="true">库内盘亏损</th>
							<th data-sort-ignore="true">期未库存</th>
							<th data-sort-ignore="true" style="background-color: #0b8cc5; color: #FFFFFF">奶站交物流数</th>
							<th data-sort-ignore="true" style="background-color: #0b8cc5; color: #FFFFFF">物流存量</th>
						</tr>
						</thead>
						<tbody>
						<?php $i = 0; ?>
						@foreach($refund_info as $date=>$ri)
							<?php $i++; $j=0; ?>
							@foreach($ri as $type=>$r)
								<?php $j++; ?>
								<tr>
									@if($j == 1)
										<td rowspan="{{count($ri)}}">{{$i}}</td>
										<td  rowspan="{{count($ri)}}">{{$date}}</td>
									@endif
									<td>{{$type}}</td>
									<td>{{$r['init_store_count']}}</td>
									<td>{{$r['station_refunds_count']}}</td>
									<td>{{$r['etc_refunds_count']}}</td>
									<td>{{$r['production_count']}}</td>
									<td>{{$r['store_damaged_count']}}</td>
									<td>{{$r['final_count']}}</td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							@endforeach
						@endforeach
						{{--<tr>--}}
						{{--<td colspan="2">合计</td>--}}
						{{--<td></td>--}}
						{{--<td></td>--}}
						{{--<td></td>--}}
						{{--<td></td>--}}
						{{--<td></td>--}}
						{{--<td></td>--}}
						{{--<td></td>--}}
						{{--<td></td>--}}
						{{--<td></td>--}}
						{{--</tr>--}}
						</tbody>
						{{--<tfoot>--}}
						{{--<tr>--}}
						{{--<td colspan="11">--}}
						{{--<ul class="pagination pull-right"></ul>--}}
						{{--</td>--}}
						{{--</tr>--}}
						{{--</tfoot>--}}
					</table>
				</div>
		</div>	
	</div>
@endsection

@section('script')
	<script type="text/javascript" src="<?=asset('js/pages/gongchang/pingkuang.js')?>"></script>
@endsection