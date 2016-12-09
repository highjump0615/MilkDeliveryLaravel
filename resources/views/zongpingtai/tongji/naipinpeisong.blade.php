@extends('zongpingtai.layout.master')

@section('content')
	@include('zongpingtai.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('zongpingtai.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="{{ url('zongpingtai/tongji')}}">统计分析</a>
				</li>
				<li class="active">
					<strong>奶品配送统计</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-4">
							<label>公司名称:</label>
							<input class="form-control" type="text" id="factory_name" style="width:70%; display: inline;" value="{{$factory_name}}">
						</div>
						<div class="col-md-4">
							<label>编号:</label>
							<input class="form-control" type="text" id="factory_number" style="width:70%; display: inline" value="{{$factory_number}}">
						</div>
						{{--<div class="col-md-4">--}}
							{{--<label class="col-md-3">区域:</label>--}}
							{{--<div class="col-md-7">--}}
								{{--<div class="input-group">--}}
									{{--<select required id="province" name="c_province" class="province_list form-control col-md-3" style="width: 150px;">--}}
										{{--@if (isset($province))--}}
											{{--<option value="" @if($input_province == '') selected @endif>全部</option>--}}
											{{--@for ($i = 0; $i < count($province); $i++)--}}
												{{--<option value="{{$province[$i]->name}}" @if($input_province == $province[$i]->name) selected @endif>{{$province[$i]->name}}</option>--}}
											{{--@endfor--}}
										{{--@else--}}
											{{--<option value="">全部</option>--}}
										{{--@endif--}}
									{{--</select>--}}
								{{--</div>--}}
							{{--</div>	--}}
						{{--</div>--}}
					</div>
					<br>
					<div class="feed-element">
						<div class="form-group col-md-5" id="data_range_select">
							<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
							<div class="input-daterange input-group col-md-8" id="datepicker">
                                <input type="text" class="input-md form-control" id="start_date" name="start" value="{{$start_date}}"/>
                                <span class="input-group-addon">至</span>
                                <input type="text" class="input-md form-control" id="end_date" name="end" value="{{$end_date}}"/>
                            </div>
						</div>
						<div class="col-md-offset-4 col-md-3"  style="padding-top:5px;">
							<button type="button" id="search" class="btn btn-success btn-m-d">筛选</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action = "export_csv">导出</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action = "print">打印</button>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="total_balance" class="table footable table-bordered">
                            <thead>
								<tr>
									<th>序号</th>
									<th>公司名称</th>
									{{--<th>奶品</th>--}}
									<th>计划订单金额</th>
									<th>站内零售金额</th>
									<th>试饮赠品金额</th>
									<th>团购业务金额</th>
									<th>渠道销售金额</th>
									<th>合计</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i = 0; ?>
							@foreach($factories_bal as $fb)
								<?php $i++; ?>
								<tr>
									<td>{{$i}}</td>
									<td>{{$fb->name}}</td>
									{{--<td>SYS002</td>--}}
									<td>{{$fb->order_total}}</td>
									<td>{{$fb->retail}}</td>
									<td>{{$fb->gift}}</td>
									<td>{{$fb->group}}</td>
									<td>{{$fb->channel}}</td>
									<td></td>
								</tr>
							@endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

			</div>
		</div>
		
	</div>
@endsection

@section('script')
	<script type="text/javascript" src="<?=asset('js/pages/zongpingtai/naipinpeisong.js')?>"></script>
@endsection