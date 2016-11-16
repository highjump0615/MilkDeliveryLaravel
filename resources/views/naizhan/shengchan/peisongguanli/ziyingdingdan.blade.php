@extends('naizhan.layout.master')
@section('css')
	<link href="<?=asset('css/plugins/iCheck/custom.css')?>" rel="stylesheet">
@endsection

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="{{ url('naizhan/shengchan/jihuaguanli')}}">生产与配送</a>
				</li>
				<li class="active">
					<strong>自营订单任务分配</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
			
				<div class="ibox float-e-margins">
					<div class="col-lg-12"><label class="col-lg-12">自营计划量：</label></div>
					<div class="col-lg-1"></div>
                    <div class="col-lg-10">
                        <table id="produced_milk" class="table footable table-bordered">
                            <thead style="background-color:#33cccc;">
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">奶品</th>
									<th data-sort-ignore="true">试饮</th>
									<th data-sort-ignore="true">团购业务（瓶）</th>
									<th data-sort-ignore="true">渠道销售(瓶)</th>
									<th data-sort-ignore="true">剩余量统计</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i=0; ?>
							@foreach($delivery_plans as $ds)
								<?php $i++; ?>
								<tr>
									<td>{{$i}}</td>
									<td>{{$ds->product_name}}</td>
									<td>{{$ds->test_drink}}</td>
									<td>{{$ds->group_sale}}</td>
									<td>{{$ds->channel_sale}}</td>
									<td id="rest_amount{{$ds->product_id}}">{{$ds->rest_amount}}</td>
								</tr>
							@endforeach
                            </tbody>
                        </table>
                    </div>
					<div class="col-lg-1"></div>
				</div>
				<div class="col-lg-12">
					<label class="col-lg-12 gray-bg" style="padding:10px;">添加任务</label>
				</div>
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-4">
							<label class="col-lg-4" style="padding-top: 5px;">收货人:</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="customer_name" style="width:100%;" value="">
							</div>
						</div>
						<label id="name_alert" style="color: red; padding-top: 5px; display: none;">(输入用户名!)</label>
					</div>
					<div class="feed-element">
						<div class="col-md-4">
							<label class="col-lg-4" style="padding-top: 5px;">电话:</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="phone_number" style="width:100%;" value="">
							</div>	
						</div>
						<label id="phone_alert" style="color: red; padding-top: 5px; display: none;">(输入电话号码!)</label>
					</div>
					<div class="feed-element col-md-8">
						<label class="control-label col-md-2" style="padding-top: 5px;">收货地址：</label>
						&nbsp;
						<label id="current_district">{{$current_district}}</label>
						<input type="hidden" id="addr_district" value="{{$addr_district}}">
						<select id="address4" data-placeholder="" class="street_list chosen-select form-control" style="width: 15%; display: inline">
							@foreach($streets as $st)
								<option value="{{$st}}">{{$st}}</option>
							@endforeach
						</select>
						<select id="address5" data-placeholder="" class="xiaoqu_list chosen-select form-control" style="width: 15%; display: inline">
						</select>
						<label id="address_message" class="address_message" style="padding-top: 10px; color: red"></label>
					</div>
					<div class="feed-element col-md-8">
						<div class="col-md-2"></div>
						<div class="col-md-5">
							<input id="address6" type="text" class="form-control bottle_input" placeholder="填写详细地址" value="" style="width: 100%;">
						</div>
					</div>
					<div class="feed-element col-md-8">
						<label class="control-label col-md-2" style="padding-top: 5px;">分类:</label>
						<div class="col-md-4" style="padding-left: 10px;">
							<select data-placeholder="" id="type" class="form-control chosen-select" style="width:100%;" tabindex="2">
								<option value="2">团购订单</option>
								<option value="3">渠道订单</option>
							</select>
						</div>
					</div>
					<div class="feed-element col-md-8">
						<label class="control-label col-md-2" style="padding-top: 5px;">配送员:</label>
						<div class="col-md-4" style="padding-left: 10px;">
							<select data-placeholder="" id="milkman_name" class="form-control chosen-select" style="width:100%;" tabindex="2">
								@foreach($milk_man as $mm)
									<option value="{{$mm->id}}">{{$mm->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="feed-element col-md-8">
						<label class="control-label col-md-2" style="padding-top: 5px;">配送时间:</label>
						<div class="col-md-4" style="padding-left: 10px;">
							<select data-placeholder="" id="time" class="form-control chosen-select" style="width:100%;" tabindex="2">
								<option value="0">上午</option>
								<option value="1">下午</option>
							</select>
						</div>	
					</div>
					
					<div class="feed-element col-md-12">
						<label class="control-label col-md-2" style="padding-top: 5px;">配送内容:</label>
						<div class="col-md-6">
							<table id="product_deliver" class="table footable table-bordered">
								<tbody>
								@foreach($delivery_plans as $dp)
									<tr id="{{$dp->product_id}}">
										<td class="col-md-6"><label>{{$dp->product_name}}</label>
											{{--&nbsp;<input type="checkbox" checked class="i-checks"\>--}}
										</td>
										<td classs="col-md-6">
											<label class="col-lg-4">数量</label>
											<div class="col-lg-6">
												<input name="{{$dp->product_name}}" id="amount{{$dp->product_id}}" class="amount" type="number" style="width:100%;">
											</div>
										</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
						<div class="col-md-4"><label id="alert_message" class="alert_message" style="padding-top: 10px; color: red"></label></div>
					</div>
					<div class="col-md-1 col-md-offset-10">
							<button id="add" class="btn btn-success" style="width:100%;">添加</button>
					</div>	
					
				</div>
				<br>
				<div class="ibox-content">
                    <div class="col-lg-12">
                        <table id="delivery_milk" class="table footable table-bordered">
                            <thead style="background-color:#33cccc;">
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">分类</th>
									<th data-sort-ignore="true">地址</th>
									<th data-sort-ignore="true">收货人</th>
									<th data-sort-ignore="true">配送内容</th>
									<th data-sort-ignore="true">电话</th>
									<th data-sort-ignore="true">配送员</th>
									<th data-sort-ignore="true">配送时间</th>
									<th data-sort-ignore="true">备注</th>
								</tr>
                            </thead>
                            <tbody>
							@if($milkman_delivery_plans != null)
							<?php $i=0; ?>
							@foreach($milkman_delivery_plans as $mdp)
								<?php $i++; ?>
								<tr>
									<td>{{$i}}</td>
									<td>
										@if($mdp->delivery_type==2)
											团购订单
										@elseif($mdp->delivery_type==3)
											渠道订单
										@elseif($mdp->delivery_type==4)
											赠品订单
										@endif
									</td>
									<td>{{$mdp->address}}</td>
									<td>{{$mdp->customer_name}}</td>
									<td>{{$mdp->product}}</td>
									<td>{{$mdp->phone}}</td>
									<td>{{$mdp->milkman_name}}</td>
									<td>
										@if($dp->delivery_time == 0)上午
										@elseif($dp->delivery_time == 1)下午
										@endif
									</td>
									<td></td>
								</tr>
							@endforeach
							@endif
                            </tbody>
                        </table>
                    </div>	
					<div class="col-lg-4"></div>
					<div class="col-lg-3">
						<button id="save" type="button" class="btn btn-success btn-outline" style="width:100%;">添加到今日配送单</button>
						{{--onclick="window.location='{{URL::to('/naizhan/shengchan/peisongliebiao')}}'"--}}
					</div>
					<div class="col-lg-1">
						<button class="btn btn-success" id="plan_cancel" style="width:100%;">取消</button>
					</div>
				</div>
				<div class="col-lg-12"><label></label></div>

			</div>
		</div>
		
	</div>
@endsection

@section('script')
	<script src="<?=asset('js/plugins/iCheck/icheck.min.js')?>"></script>
	<script type="text/javascript" src="<?=asset('js/global.js') ?>"></script>
	<!--Save & Cancel Information-->
	<script src="<?=asset('js/ajax/naizhan_ziyingdingdan_ajax.js') ?>"></script>
	<script type="text/javascript">
		$(document).ready(function(){
//			$('#produced_milk tr:not(:first)').each(function(){
//				var test_drink=0;
//				var group_sale=0;
//				var channel_sale=0;
//				if(!isNaN(parseInt($(this).find('td:eq(2)').text()))){
//					test_drink = parseInt($(this).find('td:eq(2)').text());
//				}
//				if(!isNaN(parseInt($(this).find('td:eq(3)').text()))){
//					group_sale = parseInt($(this).find('td:eq(3)').text());
//				}
//				if(!isNaN(parseInt($(this).find('td:eq(4)').text()))){
//					channel_sale = parseInt($(this).find('td:eq(4)').text());
//				}
//				$(this).find('td:eq(5)').html(test_drink+group_sale+channel_sale);
//			})

			$('.i-checks').iCheck({
				checkboxClass: 'icheckbox_square-green',
				radioClass: 'iradio_square-green',
			});

			if ($('.street_list').val() != "none")
				$('.street_list').trigger('change');

//			$('.table').treeTable();
		});

	</script>
@endsection
