@extends('naizhan.layout.master')

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="">瓶框管理</a>
				</li>
				<li class="active">
					<strong>瓶框收回记录</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				@if($today_status == 0)
					<div class="ibox-content">
						<p><b>今天状态</b></p>
						<table id="today_input" class="table table-bordered">
							<thead>
							<tr>
								<th data-sort-ignore="true">瓶类</th>
								<th data-sort-ignore="true">期初库存</th>
								<th data-sort-ignore="true">配送员回收量</th>
								<th data-sort-ignore="true">返厂数量</th>
								<th data-sort-ignore="true">站内破损</th>
								<th data-sort-ignore="true">期末库存</th>
								<th data-sort-ignore="true">收货数量</th>
							</tr>
							</thead>
							<tbody>
							<?php $i=0; ?>
							@foreach($todaybottlerefunds as $bottle_type=>$tb)
								<tr>
									<td  value="{{$bottle_type}}">{{$tb['name']}}</td>
									<td>{{$tb['init_count']}}</td>
									<td>{{$tb['milkman_refund']}}</td>
									<td>{{$tb['return_to_factory']}}</td>
									<td class="damaged" @if($tb['damaged']=='') contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1" @endif>{{$tb['damaged']}}</td>
									<td></td>
									<td contenteditable="true">{{$tb['received']}}</td>
									<input type="hidden" id="type" value="0">
								</tr>
							@endforeach
							@foreach($todayboxrefunds as $box_type=>$xb)
								<tr>
								<td value="{{$box_type}}">{{$xb['name']}}</td>
								<td>{{$xb['init_count']}}</td>
								<td></td>
								<td>{{$xb['return_to_factory']}}</td>
								<td class="damaged" @if($xb['damaged'] == '') contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1" @endif>{{$xb['damaged']}}</td>
								<td></td>
								<td contenteditable="true"></td>
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
				<div><hr></div>
				<div class="feed-element">
					<div class="col-md-4">
					</div>
					<div class="form-group col-md-5" id="data_range_select">
						<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
						<div class="input-daterange input-group col-md-8" id="datepicker">
                            <input type="text" class="input-sm form-control" id="start" value="{{$start_date}}"/>
                            <span class="input-group-addon">至</span>
                            <input type="text" class="input-sm form-control" id="end" value="{{$end_date}}" />
                        </div>
					</div>
					<div class="col-md-3"  style="padding-top:5px;">
						<button id="find" type="button" class="btn btn-success btn-m-d">筛选</button>
						{{--&nbsp;--}}
						{{--<button class="btn btn-success btn-outline btn-m-d" data-action="export_csv">导出</button>--}}
						&nbsp;
						<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="table1" class="table table-bordered">
                            <thead>
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">日期</th>
									<th data-sort-ignore="true">瓶类</th>
									<th data-sort-ignore="true">期初库存</th>
									<th data-sort-ignore="true">配送员回收量</th>
									<th data-sort-ignore="true">返厂数量</th>
									<th data-sort-ignore="true">站内破损</th>
									<th data-sort-ignore="true">期末库存</th>
									<th data-sort-ignore="true">收货数量</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i=0; ?>
							@foreach($refund_info as $date=>$ri)
								<?php $i++; $j=0; ?>
								@foreach($ri as $type=>$r)
									<?php $j++; ?>
								<tr>
									@if($j == 1)
									<td rowspan="{{count($ri)}}">{{$i}}</td>
									<td rowspan="{{count($ri)}}">{{$date}}</td>
									@endif
									<td>{{$type}}</td>
									<td>{{$r['init_store']}}</td>
									<td>{{$r['milkman_return']}}</td>
									<td>{{$r['return_to_factory']}}</td>
									<td>{{$r['station_damaged']}}</td>
									<td>{{$r['end_store']}}</td>
									<td>{{$r['received']}}</td>
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
								{{--</tr>--}}
                            </tbody>
                        </table>
                    </div>
                </div>
			</div>
		</div>
	</div>
@endsection
@section('script')
	<script type="text/javascript" src="<?=asset('js/pages/naizhan/pingkuangshouhui.js') ?>"></script>
@endsection