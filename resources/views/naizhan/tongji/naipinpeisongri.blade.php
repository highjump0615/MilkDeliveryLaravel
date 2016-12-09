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
								<input type="text" class="form-control" id="start_date" value=""><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
						</div>
						<div class="col-lg-3"  style="padding-top:5px;">
							<button type="button" id="search" class="btn btn-success btn-m-d">筛选</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
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
									<th colspan="{{count($products)+1}}">配赠数量</th>
									<th colspan="{{count($products)+1}}">团购或渠道数量</th>
									<th colspan="{{count($products)+1}}">配送数量</th>
									<th rowspan="2">回收空瓶数量</th>
								</tr>
								<tr>
									@foreach($products as $p)
									<th data-sort-ignore="true">{{$p->name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i=0; ?>
							@foreach($result as $date=>$md)
								<?php $i++; ?>
								<tr>
									<td>{{$i}}</td>
									<td>{{$date}}</td>
									<td>{{$md['orders']}}</td>
									@foreach($md['yuedan'] as $p_id=>$yuedan)
										<td class="yuedan {{$p_id}}">{{$yuedan}}</td>
									@endforeach
									<td class="f_yuedan"></td>
									@foreach($md['jidan'] as $p_id=>$jidan)
										<td class="jidan {{$p_id}}">{{$jidan}}</td>
									@endforeach
									<td class="f_jidan"></td>
									@foreach($md['banniandan'] as $p_id=>$banniandan)
										<td class="banniandan {{$p_id}}">{{$banniandan}}</td>
									@endforeach
									<td class="f_banniandan"></td>
									@foreach($md['gift'] as $p_id=>$gift)
										<td class="gift {{$p_id}}">{{$gift}}</td>
									@endforeach
									<td class="f_gift"></td>
									@foreach($md['channel'] as $p_id=>$channel)
										<td class="channel {{$p_id}}">{{$channel}}</td>
									@endforeach
									<td class="f_channel"></td>
									@foreach($products as $p)
										<td class="f_{{$p->id}} total" product_type="{{$p->id}}"></td>
									@endforeach
									<td class="f_totalsum"></td>
									<td class="">{{$md['bottle_refunds']}}</td>
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