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
				<li class="active"><strong>到期订单统计</strong></li>
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
						<div class="col-md-3">
							<label>编号:</label>
							<input type="text" id="station_number" value="{{$station_number}}">
						</div>

						<div class="col-md-6">
							<label class="col-md-2" style="padding-top: 5px; padding-left: 20px;">范围:</label>
							<select required id="province" name="c_province" class="province_list form-control col-md-3" style="width: 150px;">
								@if (isset($province))
									<option value="" @if($input_province == '') selected @endif>全部</option>
									@for ($i = 0; $i < count($province); $i++)
										<option value="{{$province[$i]->name}}" @if($input_province == $province[$i]->name) selected @endif>{{$province[$i]->name}}</option>
									@endfor
								@else
									<option value="">全部</option>
								@endif
							</select>
							<input type="hidden" id="input_city" value="{{$input_city}}">
							<select required id="city" name="c_city" class="city_list col-md-2 form-control" style = "width: 150px;">
							</select>
							<input type="hidden" id="input_district" value="{{$input_district}}">
							<select required id="district" name="c_district" class="district_list form-control" style="width: 150px;">
							</select>
						</div>
					</div>
					<br>
					<div class="feed-element">					
						<div class="form-group col-md-offset-4 col-md-5" id="data_range_select">
							<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
							<div class="input-daterange input-group col-md-8" id="datepicker">
                                <input type="text" class="input-sm form-control" id="start_date" name="start" value="{{$start_date}}"/>
                                <span class="input-group-addon">至</span>
                                <input type="text" class="input-sm form-control" id="end_date" name="end" value="{{$end_date}}"/>
                            </div>
						</div>

						<div class="col-md-3">
							<button id="search" type="button" class="btn btn-success btn-md"  data-action="show_selected">筛选</button>
							&emsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="table1" class=" footable table table-bordered" data-page-size="10">
                            <thead>
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">订单号</th>
									<th data-sort-ignore="true">收货人</th>
									<th data-sort-ignore="true">电话</th>
									<th data-sort-ignore="true">订单类型</th>
									<th data-sort-ignore="true">订单金额</th>
									<th data-sort-ignore="true">业务员</th>
									<th data-sort-ignore="true">区域</th>
									<th data-sort-ignore="true">分区</th>
									<th data-sort-ignore="true">奶站</th>
									<th data-sort-ignore="true">配送员</th>
									<th data-sort-ignore="true">下单日期</th>
									<th data-sort-ignore="true">支付</th>
									<th data-sort-ignore="true">到期日期</th>
									<th data-sort-ignore="true">订单来源</th>
									<th data-sort-ignore="true">客户类型</th>
									<th data-sort-ignore="true">订单详情</th>
									{{--<th data-sort-ignore="true">操     作</th>--}}
									{{--<th data-sort-ignore="true">备    注</th>--}}
								</tr>
                            </thead>
                            <tbody>
							<?php $i=0;?>
							@foreach($order_info as $oi)
								@foreach($oi as $o)
									<?php $i++; ?>
								<tr>
									<td>{{$i}}</td>
									<td>{{$o->number}}</td>
									<td>{{$o->customer_name}}</td>
									<td>{{$o->phone}}</td>
									<td>{{$o->order_type}}</td>
									<td>{{$o->total_amount}}</td>
									<td>{{$o->order_checker_name}}</td>
									<td>{{$o->city_name}}</td>
									<td>{{$o->district_name}}</td>
									<td>{{$o->station_name}}</td>
									<td>{{$o->milkman['name']}} {{$o->milkman['phone']}}</td>
									<td>{{$o->ordered_at}}</td>
									<td>{{$o->payment_type_name}}</td>
									<td>{{$o->order_end_date}}</td>
									<td>{{$o->status_changed_at}}</td>
									<td></td>
									<td><a href={{URL::to('/gongchang/dingdan/dingdanluru/xiangqing/'.$o->id)}}>查看</a></td>
									{{--<td></td>--}}
									{{--<td></td>--}}
								</tr>
								@endforeach
							@endforeach
                            </tbody>
							<tfoot>
								<tr>
									<td colspan="19">
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
	<script type="text/javascript" src="<?=asset('js/pages/gongchang/daoqidingdan.js')?>"></script>
@endsection