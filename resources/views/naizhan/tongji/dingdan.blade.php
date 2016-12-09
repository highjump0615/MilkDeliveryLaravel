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
					<strong>订单统计</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
			

			<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-4" style="padding-top: 5px;">
							<label class="col-lg-4" style="padding-top: 5px;">订单类型:</label>
							<select data-placeholder="" class="chosen-select" id="order_type" tabindex="2" style="width: 180px; height: 30px;">
								@if($order_type == 1)
									<option value="1" selected>月单</option>
									<option value="2">季单</option>
									<option value="3">半年单</option>
								@elseif($order_type == 2)
									<option value="1">月单</option>
									<option value="2" selected>季单</option>
									<option value="3">半年单</option>
								@elseif($order_type == 3)
									<option value="1">月单</option>
									<option value="2">季单</option>
									<option value="3" selected>半年单</option>
								@endif
							</select>
						</div>
						<div class="form-group col-md-5" id="data_range_select" style="padding-top: 5px;">
							<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
							<div class="input-daterange input-group col-md-8" id="datepicker">
								<input type="text" class="input-sm form-control" id="start_date" name="start" value="{{$start_date}}"/>
								<span class="input-group-addon">至</span>
								<input type="text" class="input-sm form-control" id="end_date" name="end" value="{{$end_date}}"/>
							</div>
						</div>
						<div class="col-md-3"  style="padding-top:5px;">
							<button type="button" id="search" class="btn btn-success btn-m-d">筛选</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="table1" class="table table-bordered">
                            <thead>
								<tr>
									<th rowspan="2">奶品名称</th>
									@if($order_type == 1)
									<th colspan="4">月单</th>
									@elseif($order_type == 2)
										<th colspan="4">季单</th>
									@elseif($order_type == 3)
										<th colspan="4">半年单</th>
									@endif
								</tr>
								<tr>
									<th data-sort-ignore="true">新单</th>
									<th data-sort-ignore="true">续单</th>
									<th data-sort-ignore="true">总量</th>
									<th data-sort-ignore="true">剩余量</th>
								</tr>
                            </thead>
                            <tbody>
							@foreach($product_info as $pi)
								<tr>
									<td>{{$pi->name}}</td>
									<td>@if($pi->xin_property=='')0 @else {{$pi->xin_property}}@endif</td>
									<td>@if($pi->xu_property == '') 0 @else {{$pi->xu_property}} @endif</td>
									<td>@if($pi->t_type == '') 0 @else {{$pi->t_type}} @endif</td>
									<td>{{$pi->s_type - $pi->r_type}}</td>
								</tr>
							@endforeach
								<tr>
									<td>订单产品数量合计</td>
									<td>{{$xin_property}}</td>
									<td>{{$xu_property}}</td>
									<td>{{$t_type}}</td>
									<td>{{$s_type - $r_type}}</td>
								</tr>
								<tr>
									<td>单数合计</td>
									<td>{{round($xin_property/$timming,2)}}</td>
									<td>{{round($xu_property/$timming,2)}}</td>
									<td>{{round($s_type/$timming,2)}}</td>
									<td>{{round(($s_type - $r_type)/$timming,2)}}</td>
								</tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                
			</div>
		</div>
		
	</div>
@endsection
@section('script')
	<script src="<?=asset('js/pages/naizhan/dingdantongji.js') ?>"></script>
@endsection