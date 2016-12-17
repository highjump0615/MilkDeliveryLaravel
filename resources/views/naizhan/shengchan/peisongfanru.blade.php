@extends('naizhan.layout.master')

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="{{ url('naizhan/shengchan')}}">生产与配送</a>
				</li>
				<li class="active">
					<strong>配送反录</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">

				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-3">
							<label class="col-lg-4" style="padding-top: 5px;">配送员:</label>
							<div class="col-lg-8">
								<select data-placeholder="" id="milkman_name" class="form-control chosen-select" style="width:100%;" tabindex="2">
									@foreach($milkman as $m)
										<option @if($current_milkman == $m->id) selected @endif value="{{$m->id}}">{{$m->name}}</option>
									@endforeach
								</select>
							</div>
							<input type="hidden" id="current_milkman_id" value="{{$current_milkman}}">
						</div>
						<div class="form-group col-md-5" id="date_select">
							<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
							<div class="input-group date col-lg-8">
								<input type="text" class="form-control" value="{{$deliver_date}}" id="search_date"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
							</div>
							{{--<div class="input-daterange input-group col-md-8" id="datepicker">--}}
							{{--<input type="text" class="input-sm form-control" name="start" value="05/14/2014"/>--}}
							{{--<span class="input-group-addon">至</span>--}}
							{{--<input type="text" class="input-sm form-control" name="end" value="05/22/2014" />--}}
							{{--</div>--}}
						</div>
						<div class="col-md-offset-1 col-md-3"  style="padding-top:5px;">
							<!--<button type="button" class="btn btn-success btn-m-d">筛选</button>-->
							{{--&nbsp;--}}
							{{--<a href="">导出</a>--}}
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
						</div>
					</div>
				</div>

				<div class="ibox float-e-margins">

					@if($is_todayrefund)
					<label style="color: red; font-size: 18px;">你已经完成了反录</label>
					@endif

                    <div class="ibox-content">
						<div id="delivered_info">
                        <table id="delivery_table" class="table table-bordered">
                            <thead>
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">地址</th>
									<th data-sort-ignore="true">收货人</th>
									<th data-sort-ignore="true">电话</th>
									<th data-sort-ignore="true">配送内容</th>
									<th data-sort-ignore="treu">配送数量</th>
									<th data-sort-ignore="true">反录情况</th>
									<th data-sort-ignore="true">奶箱安装</th>
									{{--<th data-sort-ignore="true">操作</th>--}}
									<th data-sort-ignore="true">备注</th>
								</tr>
                            </thead>
                            <tbody>
							@if(count($delivery_info) == 0)
								<tr>
									<td colspan="10">数据不存在</td>
								</tr>
							@endif
							<?php $i=0; ?>
							@foreach($delivery_info as $di)
								<?php $i++; $j=0; ?>
								@foreach($di->product as $pro)
									<?php $j++; ?>
								<tr class="order_info" id="{{$di->id}}" ordertype="{{$di->delivery_type}}">
									@if($j == 1)
									<td rowspan="{{count($di->product)}}" class="by_order" value="{{$di->id}}">{{$i}}</td>
									<td  rowspan="{{count($di->product)}}">
										{{$di->address}}
									</td>
									@if($di->delivery_type==1)
										<td rowspan="{{count($di->product)}}">
											{{$di->customer->name}}
										</td>
									@else
										<td rowspan="{{count($di->product)}}">
											{{$di->customer_name}}
										</td>
									@endif
									<td rowspan="{{count($di->product)}}">
										{{$di->phone}}
									</td>
									@endif
									<td id="{{$pro['order_product_id']}}">
										{{$pro['name']}}*{{$pro['count']}}
									</td>
									<td @if($deliver_date == $current_date && !$is_todayrefund) contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1" @endif id="{{$pro['order_product_id']}}" class="delivered_count">
										@if($is_todayrefund) {{$pro['delivered_count']}} @else {{$pro['count']}} @endif
									</td>
									<td class="report"
										@if($deliver_date == $current_date && !$is_todayrefund)
											contenteditable="true"
											style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"
										@endif>{{$pro['report']}}</td>
									@if($j == 1)
										<td rowspan="{{count($di->product)}}">
											@if($di->milkbox_install > 0){{$di->milkbox_install}}@endif
										</td>
									{{--<td rowspan="{{count($di->product)}}" id="status{{$di->id}}">--}}
										{{--@if($di->flag == 0)--}}
											{{--<button id="confirm{{$di->id}}" class="btn btn-success btn-sm confirm" value="{{$di->id}}" oreder_type="{{$di->delivery_type}}">确认</button>--}}
											{{--@if($di->delivery_type == 1)--}}
											{{--<button class="btn btn-success btn-sm" onclick="window.location='{{URL::to('/naizhan/dingdan/xiugai/?'.$di->id)}}'">修改订单</button>--}}
											{{--@endif--}}
										{{--@else--}}
											{{--交货了--}}
										{{--@endif--}}
									{{--</td>--}}
									@endif
									<td rowspan="{{count($di->product)}}">{{$pro['comment']}}</td>
								</tr>
								@endforeach
							@endforeach
                            </tbody>
                        </table>

                        <div class="col-md-8 col-md-offset-4" style="padding-right: 0;">
                        	<div class="col-md-2">
                        		<p>奶瓶回收数量:</p>
                        	</div>
                        	<div class="col-md-10" style="padding-right: 0;">
								<table id="refund_bottle" class="table table-bordered">
									<thead>
									<tr>
										<th data-sort-ignore="true">奶瓶型</th>
										<th data-sort-ignore="true">数量</th>
									</tr>
									</thead>
									<tbody>
									@if($deliver_date == $current_date && !$is_todayrefund)
										@foreach($bottle_types as $bt)
											<tr id="{{$bt->bottle_type}}">
												<td>{{\App\Model\FactoryModel\FactoryBottleType::find($bt->bottle_type)->name}}</td>
												<td id="{{$bt->bottle_type}}" contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1"></td>
											</tr>
										@endforeach
									@else
										@foreach($milkman_bottle_refunds as $mb)
											<tr>
												<td>{{$mb->bottle_name}}</td>
												<td id="{{$mb->count}}">{{$mb->count}}</td>
											</tr>
										@endforeach
									@endif
									</tbody>
								</table>
                        	</div>
                        </div>
						</div>
						@if($deliver_date == $current_date && !$is_todayrefund)
						<div style="text-align: center;">
							<button id="save" class="btn btn-success btn-m-d" style="width: 200px;">保存</button>
						</div>
						@else
						<div style="text-align: center;">
							<button id="return" class="btn btn-success btn-m-d" style="width: 200px;">查看今日配送列表</button>
						</div>
						@endif
                    </div>
                </div>

			</div>
		</div>
	</div>
@endsection
@section('script')
	<!--Save & Cancel Information-->
	<script src="<?=asset('js/ajax/shengchan_peisongfanru_ajax.js') ?>"></script>
@endsection