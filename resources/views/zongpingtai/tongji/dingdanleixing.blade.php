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
					<strong>订单类型统计</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-4">
							<label>公司名称:</label>
							<input class="form-control" type="text" id="factory_name" style="width:70%; display: inline" value="{{$factory_name}}">
						</div>
						<div class="form-group col-md-4" id="data_range_select">
							<label class="col-md-3 control-label" style="padding-top:5px;">日期:</label>
							<div class="input-daterange input-group col-md-9" id="datepicker">
								<input type="text" class="input-md form-control" id="start_date" name="start" value="{{$start_date}}"/>
								<span class="input-group-addon">至</span>
								<input type="text" class="input-md form-control" id="end_date" name="end" value="{{$end_date}}"/>
							</div>
						</div>
						<div class="col-md-3 col-md-offset-1"  style="padding-top:5px;">
							<button type="button" id="search" class="btn btn-success btn-m-d">筛选</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action = "export_csv">导出</button>
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
									<th rowspan="2">公司名称</th>
									{{--<th rowspan="2">区域</th>--}}
									<th rowspan="2">奶品名称</th>
									<th colspan="2">月单</th>
									<th colspan="2">季单</th>
									<th colspan="2">半年单</th>
									<th colspan="2">合计</th>
								</tr>
								<tr>
									<th>总量</th>
									<th>剩余量</th>
									<th>总量</th>
									<th>剩余量</th>
									<th>总量</th>
									<th>剩余量</th>
									<th>总量</th>
									<th>剩余量</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i = 0; ?>
							@foreach($factories as $st)
								<?php $i++; $j =0; ?>
								@foreach($st->product as $p)
									<?php $j++; ?>
									<tr class="milk">
										@if($j == 1)
											<td rowspan="{{count($st->product)+3}}">{{$st->name}}</td>
											{{--<td rowspan="{{count($st->product)+3}}"></td>--}}
										@endif
										<td>{{$p->name}}</td>
										<td class="total">{{$p->t_yuedan}}</td>
										<td class="remain">{{$p->t_yuedan - $p->r_yuedan}}</td>
										<td class="total">{{$p->t_jidan}}</td>
										<td class="remain">{{$p->t_jidan - $p->r_jidan}}</td>
										<td class="total">{{$p->t_banniandan}}</td>
										<td class="remain">{{$p->t_banniandan - $p->r_banniandan}}</td>
										<td class="f_total"></td>
										<td class="f_remain"></td>
									</tr>
								@endforeach
								@if(count($st->product)>0)
								<tr class="milk">
									<td>订单产品数量合计</td>
									<td class="total">{{$st->t_yuedan}}</td>
									<td class="remain">{{$st->t_yuedan - $st->r_yuedan}}</td>
									<td class="total">{{$st->t_jidan}}</td>
									<td class="remain">{{$st->t_jidan - $st->r_jidan}}</td>
									<td class="total">{{$st->t_banniandan}}</td>
									<td class="remain">{{$st->t_banniandan - $st->r_banniandan}}</td>
									<td class="f_total"></td>
									<td class="f_remain"></td>
								</tr>
								<tr class="milk_amount">
									<td>单数合计</td>
									<td class="total">{{round($st->t_yuedan/30,2)}}</td>
									<td class="remain">{{round((($st->t_yuedan-$st->r_yuedan)/30),2)}}</td>
									<td class="total">{{round($st->t_jidan/90,2)}}</td>
									<td class="remain">{{round((($st->t_jidan-$st->r_jidan)/90),2)}}</td>
									<td class="total">{{round($st->t_banniandan/180,2)}}</td>
									<td class="remain">{{round((($st->t_banniandan-$st->r_banniandan)/180),2)}}</td>
									<td class="f_total"></td>
									<td class="f_remain"></td>
								</tr>
								<tr class="milk_amount">
									<td>订单金额合计</td>
									<td class="total">{{$st->t_yuedan_amount}}</td>
									<td class="remain">{{$st->t_yuedan_amount - $st->r_delivered_yuedan_amount}}</td>
									<td class="total">{{$st->t_jidan_amount}}</td>
									<td class="remain">{{$st->t_jidan_amount - $st->r_delivered_jidan_amount}}</td>
									<td class="total">{{$st->t_banniandan_amount}}</td>
									<td class="remain">{{$st->t_banniandan_amount - $st->r_delivered_banniandan_amount}}</td>
									<td class="f_total"></td>
									<td class="f_remain"></td>
								</tr>
								@endif
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
	<script type="text/javascript" src="<?=asset('js/pages/zongpingtai/dingdanleixing.js')?>"></script>
@endsection