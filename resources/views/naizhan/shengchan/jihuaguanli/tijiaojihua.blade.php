@extends('naizhan.layout.master')

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a>生产与配送</a>
				</li>
				<li class="active">
					<strong>提交计划</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				<label id="limit_alert" style="color: red; padding-left: 20px; font-size: 18px; display: none">你的钱不足以生产这些产品! 信用商业钱超过</label>

				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-2">
							 <label style="font-size:20px;">填报计划：</label>
						</div>
						<div class="col-lg-7"></div>
						<label id="balance" class="col-lg-3" style="font-size:20px; color:white; background-color:#ff0000;">自营业务余额：{{$current_station_status->business_credit_balance}}</label>
					</div>
				</div>
				<input type="hidden" id="init_business_credit_amount" value="{{$current_station_status->init_business_credit_amount}}">
				<input type="hidden" id="business_credit_balance" value="{{$current_station_status->business_credit_balance}}">
				<div class="ibox float-e-margins">
                    <div class="ibox-content"><table class="table table-bordered" id="plan-list">
                        	<thead style="background-color:#33cccc;">
								<tr>
									<th rowspan="2" data-sort-ignore="true">序号</th>
									<th rowspan="2" data-sort-ignore="true">出库时间</th>
									<th rowspan="2" data-sort-ignore="true">奶品</th>
									<th rowspan="2" data-sort-ignore="true">单位</th>
                                    <th rowspan="2" data-sort-ignore="true">配送计划</th>
									<th colspan="6" data-sort-ignore="true">奶站自营业务</th>
									<th rowspan="2" data-sort-ignore="true">订单数量合计</th>
                                    <th rowspan="2" data-sort-ignore="true">操作</th>
								</tr>
                                <tr>
                                    <th data-sort-ignore="true">站内零售</th>
                                    <th data-sort-ignore="true">试饮赠品</th>
                                    <th data-sort-ignore="true">团购业务</th>
                                    <th data-sort-ignore="true">渠道销售</th>
                                    <th data-sort-ignore="true">数量合计</th>
                                    <th data-sort-ignore="true">金额合计</th>
                                </tr>
                            </thead>
                            <tbody>
							<?php $j = 0; ?>
							@foreach ($product_list as $pl)
								@if ($pl->current_price != null)
								<?php $j++; ?>
								@endif
							@endforeach

								<?php
								$i = 0;
								$ordered_money = 0;
								?>

								@foreach ($product_list as $pl)
									@if ($pl->current_price != null)
									<?php $i++; $ordered_money += $pl->total_money; ?>

								<tr id="id{{$pl->id}}">
									<td>{{$i}}</td>
									<td>{{$pl['out_date']}}</td>
									<td id="name{{$pl->id}}">{{$pl->name}}</td>
									<td>瓶</td>
									<td class="ordered_count sales_val" id="ordered_count{{$pl->id}}">{{$pl->total_count}}</td>

									<td contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1" class="retail sales_val" id="retail{{$pl->id}}">@if($is_sent > 0){{$pl['ds_info']['retail']}}@endif</td>
									<td contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1" class="test_drint sales_val" id="test_drink{{$pl->id}}">@if($is_sent > 0){{$pl['ds_info']['test_drink']}}@endif</td>
									<td contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1" class="group_sale sales_val" id="group{{$pl->id}}">@if($is_sent > 0){{$pl['ds_info']['group_sale']}}@endif</td>
									<td contenteditable="true" style="border-bottom-width: 2px; border-bottom-color: #0a6aa1" class="channel_sale  sales_val" id="channel{{$pl->id}}">@if($is_sent > 0){{$pl['ds_info']['channel_sale']}}@endif</td>
									<td class="total_count">@if($is_sent > 0){{$pl['ds_info']['subtotal_count']}}@endif</td>
									<td class="total_price">@if($is_sent > 0){{$pl['ds_info']['subtotal_money']}}@endif</td>
									<td class="total_count_all" id="subtotal_count{{$pl->id}}"></td>
									<!-- 隐藏字段；总金额 -->
									<td class="total_price_all" id="subtotal_price{{$pl->id}}" style="display:none;"></td>

									@if($i==1)
									<td rowspan="{{$j+1}}">
										<button class="btn btn-success btn-sm confirm_submit" id="confirm{{$pl->id}}" value="{{$pl->id}}" @if($is_sent>0)style="display: none" @endif>确认提交</button>
										<button class="btn btn-success btn-sm modify" id="modify{{$pl->id}}" value="{{$pl->id}}" @if($is_sent==0)style="display: none" @endif>修改</button>
									</td>
									@endif

									<input type="hidden" class="current_price" value="{{$pl->current_price}}">
									<input type="hidden" class="ordered_price" value="{{$pl->total_money}}">
									<input type="hidden" id="current_id{{$pl->id}}" value="{{$pl->id}}">
									<input type="hidden" id="product_id" value="{{$pl->id}}">
									<input type="hidden" id="production_period{{$pl->id}}" value="{{$pl->production_period}}">
								</tr>

									@endif
								@endforeach

								<input type="hidden" id="total_ordered_money" value="{{$ordered_money}}">
								<tr>
									<td colspan="4">合计</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td id="total_amount"></td>
									<td></td>
								</tr>
                            </tbody>
                        </table>
                    </div>
                </div>
				<div class="ibox-content">
					<div class="col-lg-5"></div>
					<div class="col-lg-2">
						<a href="{{ url('/naizhan/shengchan/jihuaguanli') }}" class="btn btn-outline btn-success" style="width: 100%;">查看全部计划</a>
					</div>
					<div class="col-lg-5"></div>
				</div>

			</div>
		</div>
	</div>
@endsection

@section('script')
	<!--Save & Update User Information-->
	<script src="<?=asset('js/ajax/shengchan_tijiaoajax.js') ?>"></script>
@endsection