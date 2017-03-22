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
					<strong>客户行为统计</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-3">
							<label>奶站名称:</label>
							<input class="form-control" type="text" id="station_name" style="width: 70%; display: inline" value="{{$current_station_name}}">
						</div>
						<div class="col-md-3">
							<label>编号:</label>
							<input class="form-control" type="text" id="station_number" style="width: 70%; display: inline" value="{{$current_station_number}}">
						</div>
						<div class="col-md-6 form-group">
							<label>区域:</label>
							<select id="province" data-placeholder="" class="form-control chosen-select province_list" style="width: 30%; display: inline" value="">
								<option value="none">全部</option>
								@if (isset($province))
									@foreach($province as $pr)
										<option value="{{$pr->name}}" @if($pr->name == $current_province) selected @endif>{{$pr->name}}</option>
									@endforeach
								@endif
							</select>
							<select id="city" data-placeholder="" class="form-control chosen-select city_list" style="width: 30%; display: inline">
								<option value="none">全部</option>
							</select>
							<input type="hidden" id="currrent_city" value="{{$current_city}}">
						</div>
					</div>
					<br>
					<div class="feed-element">					
						<div class="form-group col-md-5" id="data_range_select">
							<label class="col-md-2 control-label" style="padding-top:5px;">日期:</label>
							<div class="input-daterange input-group col-md-8" id="datepicker">
                                <input id="start_date" type="text" class="input-md form-control" name="start" value="{{$currrent_start_date}}"/>
                                <span class="input-group-addon">至</span>
                                <input id="end_date" type="text" class="input-md form-control" name="end" value="{{$current_end_date}}"/>
                            </div>
						</div>
						<div class="col-md-offset-4 col-lg-3"  style="padding-top:5px;">
							<button id="search" type="button" class="btn btn-success btn-m-d">筛选</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action = "print">打印</button>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="table1" class="table footable table-bordered">
                            <thead>
								<tr>
									<th rowspan="2">序号</th>
									<th rowspan="2">区域</th>
									<th rowspan="2">分区</th>
									<th rowspan="2">奶站 （经销商名称）</th>
									<th colspan="13">期间客户数量变化</th>
									<th colspan="6">期末客户状态汇总</th>
								</tr>
								<tr>
									<th>新增客户数</th>
									<th>新增-订单金额</th>
									<th>期间到期客户数</th>
									<th>本期到期续单客户数</th>
									<th>前期到期续单客户数</th>
									<th>续单金额</th>
									<th>续单率</th>
									<th>本期-退单款客户数</th>
									<th>退款金额</th>
									<th>订单金额合计</th>
									<th>划转公司奶款金额</th>
									<th>支付返利提成金额</th>
									<th>其他划转金额</th>
									<th>在配送客户数</th>
									<th>在配送-剩余订单金额</th>
									<th>暂停客户数</th>
									<th>剩余订单金额</th>
									<th>总之/退款客户数</th>
									<th>期末订单金额结余</th>
								</tr>
                            </thead>
							<tbody>
							@foreach($stations as $i=>$s)
								<tr>
									<td>{{$i}}</td>
									<td>{{$s['province_name']}}</td>
									<td>{{$s['city_name']}}</td>
									<td>{{$s['name']}}</td>
									<td><?php
										if(isset($s['new_customers']))
											echo $s['new_customers'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['new_order_price']))
											echo $s['new_order_price'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['finished_orders']))
											echo $s['finished_orders'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['xuedan_after_another']))
											echo $s['xuedan_after_another'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['xuedan_after_finished_prev']))
											echo $s['xuedan_after_finished_prev'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['xudan_price']))
											echo $s['xudan_price'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['xudan_ratio']))
											echo $s['xudan_ratio'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['canceled_orders']))
											echo $s['canceled_orders'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['canceled_orders_amount']))
											echo $s['canceled_orders_amount'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['new_order_amount_real']))
											echo $s['new_order_amount_real'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['trans_to_factory_amount']))
											echo $s['trans_to_factory_amount'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['trans_for_delivery_cost_amount']))
											echo $s['trans_for_delivery_cost_amount'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['trans_to_other_amount']))
											echo $s['trans_to_other_amount'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['delivery_orders']))
											echo $s['delivery_orders'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['delivery_orders_remaining_amount']))
											echo $s['delivery_orders_remaining_amount'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['stopped_orders']))
											echo $s['stopped_orders'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['stopped_orders_amount']))
											echo $s['stopped_orders_amount'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['ended_orders_amount']))
											echo $s['ended_orders_amount'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['total_orders_remaining_amount']))
											echo $s['total_orders_remaining_amount'];
										else
											echo "0";
										?>
									</td>

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
	<script type="text/javascript" src="<?=asset('js/pages/zongpingtai/kehuxingwei.js')?>"></script>
@endsection