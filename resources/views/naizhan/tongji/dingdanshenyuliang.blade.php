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
					<strong>订单剩余量统计</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
			
				<div class="ibox-content">
					<div class="feed-element">
						{{--<div class="col-md-3">--}}
							{{--<label>奶站名称:</label>--}}
							{{--<input type="text" id="station_name">--}}
						{{--</div>--}}
						{{--<div class="col-md-3">--}}
							{{--<label class="col-md-3">编号:</label>--}}
							{{--<input type="text" id="station_number">--}}
						{{--</div>--}}
						{{--<div class="col-md-3">--}}
							{{--<label class="col-md-3">区域:</label>--}}
							{{--<div class="col-md-7">--}}
								{{--<select data-placeholder="" class="chosen-select" style="width:100%;" tabindex="2">--}}
									{{--<option value="全部">全部</option>--}}
									{{--<option value="北京">北京</option>--}}
									{{--<option value="河北">河北</option>--}}
								{{--</select>--}}
							{{--</div>	--}}
						{{--</div>--}}
						<div class="form-group col-md-6" id="data_range_select" style="padding-top: 5px;">
							<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
							<div class="input-daterange input-group col-md-8" id="datepicker">
								<input type="text" class="input-sm form-control" id="start_date" name="start" value="{{$start_date}}"/>
								<span class="input-group-addon">至</span>
								<input type="text" class="input-sm form-control" id="end_date" name="end" value="{{$end_date}}"/>
							</div>
						</div>

						{{--<div class="col-md-6" id="date_1" style="padding-top: 5px;">--}}
							{{--<label class="col-md-2 control-label" style="padding-top:7px;">日期:</label>--}}
							{{--<div class="input-group date col-md-6">--}}
								{{--<input type="text" class="form-control" id="end_date" value="{{$end_date}}" style="text-align: center">--}}
								{{--<span class="input-group-addon"><i class="fa fa-calendar"></i></span>--}}
							{{--</div>--}}
						{{--</div>--}}

						<div class="col-md-2"  style="padding-top:5px;">
							<button type="button" id="search" class="btn btn-success btn-m-d">筛选</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="order_type_table" class="table table-bordered">
                            <thead>
								<tr>
									<th rowspan="2">奶品名称</th>
									<th colspan="2">月单</th>
									<th colspan="2">季单</th>
									<th colspan="2">半年单</th>
									<th colspan="2">合计</th>
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
							@foreach($product_info as $pi)
								<tr class="milk">
									<td>{{$pi->name}}</td>
									<td class="total">{{$pi->t_yuedan}}</td>
									<td class="remain">{{$pi->s_yuedan - $pi->r_yuedan}}</td>
									<td class="total">{{$pi->t_jidan}}</td>
									<td class="remain">{{$pi->s_jidan - $pi->r_jidan}}</td>
									<td class="total">{{$pi->t_banniandan}}</td>
									<td class="remain">{{$pi->s_banniandan - $pi->r_banniandan}}</td>
									<td class="f_total"></td>
									<td class="f_remain"></td>
								</tr>
							@endforeach
								<tr class="milk">
									<td>订单产品数量合计</td>
									<td class="total">{{$t_yuedan}}</td>
									<td class="remain">{{$s_yuedan - $r_yuedan}}</td>
									<td class="total">{{$t_jidan}}</td>
									<td class="remain">{{$s_jidan - $r_jidan}}</td>
									<td class="total">{{$t_banniandan}}</td>
									<td class="remain">{{$s_banniandan - $r_banniandan}}</td>
									<td class="f_total"></td>
									<td class="f_remain"></td>
								</tr>
								<tr class="milk_amount">
									<td>单数合计</td>
									<td class="total">{{round($t_yuedan/30,2)}}</td>
									<td class="remain">{{round(($s_yuedan-$r_yuedan)/30,2)}}</td>
									<td class="total">{{round($t_jidan/90,2)}}</td>
									<td class="remain">{{round(($s_jidan-$r_jidan)/90,2)}}</td>
									<td class="total">{{round($t_banniandan/180,2)}}</td>
									<td class="remain">{{round(($s_banniandan-$r_banniandan)/180,2)}}</td>
									<td class="f_total"></td>
									<td class="f_remain"></td>
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
	<script src="<?=asset('js/pages/naizhan/dingdanshenyuliang.js') ?>"></script>
@endsection