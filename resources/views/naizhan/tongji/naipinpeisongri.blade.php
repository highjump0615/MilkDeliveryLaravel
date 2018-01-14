@extends('naizhan.layout.master')

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
					<strong>奶品配送日统计</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				<div class="ibox-content">
					{{--<div class="feed-element">--}}
						{{--<div class="col-md-3">--}}
							{{--<label>奶站名称:</label>--}}
							{{--<input type="text" id="" style="width:200px;">--}}
						{{--</div>--}}
						{{--<div class="col-md-3">--}}
							{{--<label>编号:</label>--}}
							{{--<input type="text" id="" style="width:200px;">--}}
						{{--</div>--}}
						{{--<div class="col-md-3">--}}
							{{--<label>区域:</label>--}}
							{{--<select data-placeholder="" class="chosen-select" tabindex="2" style="width:200px;">--}}
									{{--<option value="全部">全部</option>--}}
									{{--<option value="北京">北京</option>--}}
									{{--<option value="河北">河北</option>--}}
							{{--</select>--}}
						{{--</div>--}}
					{{--</div>--}}
					<div class="feed-element">	
						<div class="feed-element col-lg-5" id="date_1" style="padding-top: 5px;">
							<label class="col-md-2 control-label" style="padding-top:7px;">日期:</label>
							<div class="input-group date col-md-6">
								<input type="text" class="form-control" id="start_date" value="{{$start_date}}"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
						</div>
						<div class="col-lg-3"  style="padding-top:5px;">
							<button type="button" id="search" class="btn btn-success btn-m-d">筛选</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="export_csv">导出</button>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="statistics_by_day" class="table footable table-bordered"  style="width: 2000px; overflow-x: auto">
                            <thead>
								<tr>
									<th rowspan="2">序号</th>
									<th rowspan="2">时间</th>
									<th rowspan="2">配送客户数</th>
									<th colspan="{{count($products)+1}}">月单</th>
									<th colspan="{{count($products)+1}}">季单</th>
									<th colspan="{{count($products)+1}}">半年单</th>
									<th colspan="{{count($products)+1}}">团购或渠道数量</th>
									<th colspan="{{count($products)+1}}">配送数量</th>
									<th rowspan="2">回收空瓶数量</th>
								</tr>
								<tr>
									<!-- 月单 -->
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->simple_name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
									<!-- 季单 -->
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->simple_name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
									<!-- 半年单 -->
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->simple_name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
									<!-- 团购或渠道数量 -->
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->simple_name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
									<!-- 配送数量 -->
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->simple_name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i=0; ?>
							@foreach($result as $date=>$md)
								<?php $i++; ?>
								<tr>
									<!-- 序号 -->
									<td>{{$i}}</td>
									<!-- 日期 -->
									<td>{{$date}}</td>
									<!-- 配送客户数 -->
									<td>{{getEmptyArrayValue($result, $date, 0)}}</td>
									<!-- 月单 -->
									@foreach ($products as $p)
										<td class="yuedan {{$p->id}}">
											{{getEmptyArrayValue($result, $date, 1, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_YUEDAN, $p->id)}}
										</td>
									@endforeach
									<td class="f_yuedan"></td>
									<!-- 季单 -->
									@foreach ($products as $p)
										<td class="jidan {{$p->id}}">
											{{getEmptyArrayValue($result, $date, 1, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_JIDAN, $p->id)}}
										</td>
									@endforeach
									<td class="f_jidan"></td>
									<!-- 半年单 -->
									@foreach ($products as $p)
										<td class="banniandan {{$p->id}}">
											{{getEmptyArrayValue($result, $date, 1, \App\Model\OrderModel\OrderProduct::ORDER_PRODUCT_ORDERTYPE_BANNIANDAN, $p->id)}}
										</td>
									@endforeach
									<td class="f_banniandan"></td>
									<!-- 团购或渠道 -->
									@foreach ($products as $p)
										<td class="channel {{$p->id}}">
											{{getEmptyArrayValue($result, $date, 2, $p->id)}}
										</td>
									@endforeach
									<td class="f_channel"></td>
									<!-- 配送数量 -->
									@foreach($products as $p)
										<td class="f_{{$p->id}} total" product_type="{{$p->id}}"></td>
									@endforeach
									<td class="f_totalsum"></td>
									<!-- 回收空瓶数量 -->
									<td>{{getEmptyArrayValue($result, $date, 3)}}</td>
								</tr>
							@endforeach
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
	<script src="<?=asset('js/pages/naizhan/naipinpeisongri.js') ?>"></script>
@endsection