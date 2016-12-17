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
					<strong>签收计划</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
			@if($sent_status != 0)
				<label style="color: red; font-size: 18px;">您今日已签收。</label>
			@endif
				<div class="ibox float-e-margins">
                    <div class="ibox-content">
						<label style="font-size:20px;">录入实际签收订单：</label>
                        <table id="confrim_arrived_product" class="table footable table-bordered">
                            <thead style="background-color:#33cccc;">
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">奶品</th>
									<th data-sort-ignore="true">配送计划</th>
									<th data-sort-ignore="true">站内零售（瓶）</th>
									<th data-sort-ignore="true">试饮赠品（瓶）</th>
									<th data-sort-ignore="true">团购业务（瓶）</th>
									<th data-sort-ignore="true">渠道销售(瓶)</th>
									<th data-sort-ignore="true">合计</th>
									<th data-sort-ignore="true">实际收货量</th>
									<th data-sort-ignore="true">操作</th>
								</tr>
                            </thead>
                            <tbody>
							@if(count($dsplan)==0)
								<tr>
									<td colspan="10">
										你没有收到今天的产品
									</td>
								</tr>
							@endif
							<?php $i=0; ?>
							@foreach($dsplan as $dp)
								<?php $i++;?>
								<tr id="{{$dp->product_id}}" value="{{$dp->station_id}}">
									<td>{{$i}}</td>
									<td>{{$dp->product_name}}</td>
									<td>{{$dp->order_count}}</td>
									<td>{{$dp->retail}}</td>
									<td>{{$dp->test_drink}}</td>
									<td>{{$dp->group_sale}}</td>
									<td>{{$dp->channel_sale}}</td>
									<td>{{$dp->subtotal_count}}</td>
									<td class="input_td @if($sent_status == 0) editfill @endif">
										<input id="product{{$dp->product_id}}" type="text" class="inputable" value="@if ($dp->status >6){{$dp->confirm_count}}@else{{$dp->actual_count}}@endif"
										@if($dp->status >6) readonly @endif/></td>
									<td></td>								
								</tr>
							@endforeach
                            </tbody>
                        </table>
						<label style="font-size:20px;">返厂瓶框：</label>
                        <table id="refund_table" class="table footable table-bordered">
                            <thead style="background-color:#33cccc;">
								<tr>
									<th data-sort-ignore="true"></th>
									@foreach($fbottle as $fb)
									<th data-sort-ignore="true">{{\App\Model\FactoryModel\FactoryBottleType::find($fb->bottle_type)->name}}</th>
									@endforeach
									@foreach($fbox as $fx)
									<th data-sort-ignore="true">{{\App\Model\FactoryModel\FactoryBoxType::find($fx->basket_spec)->name}}</th>
									@endforeach
								</tr>
                            </thead>
                            <tbody>
								<tr>
									<td style="background:gainsboro;">返厂数量</td>
									@foreach($fbottle as $fb)
										<?php $is_exist_bottle = 0; ?>
										<td class="input_td @if($sent_status == 0) editfill @endif" types="1" object_type="{{$fb->bottle_type}}">
											@foreach($bottle_refund as $bf)
												@if($fb->bottle_type == $bf->bottle_type)
													<input id="1{{$fb->bottle_type}}" type="text" class="inputable" value="{{$bf->return_to_factory}}" @if($sent_status != 0) readonly @endif/>
													<?php $is_exist_bottle = 1; ?>
												@endif
											@endforeach
											@if($is_exist_bottle !=1)
												<input id="1{{$fb->bottle_type}}" type="text" class="inputable" value=""/>
											@endif
										</td>
									@endforeach
									@foreach($fbox as $fx)
										<td class="input_td @if($sent_status == 0) editfill @endif" types="2" object_type="{{$fx->basket_spec}}">
											<?php $is_exist_box = 0; ?>
											@foreach($box_refund as $xf)
												@if($fx->basket_spec == $xf->box_type)
													<input id="2{{$fx->basket_spec}}" type="text" class="inputable" value="{{$xf->return_to_factory}}" @if($sent_status != 0) readonly @endif/>
													<?php $is_exist_box = 1; ?>
												@endif
											@endforeach
											@if($is_exist_box != 1 )
												<input id="2{{$fx->basket_spec}}" type="text" class="inputable" value=""/>
											@endif
										</td>
									@endforeach
								</tr>
                            </tbody>
                        </table>
                    </div>
                </div>
				<div class="ibox-content">
					<div class="col-lg-8"></div>
					<div class="col-lg-2">
						@if ($sent_status)
							<button class="btn btn-success" style="width: 100%;" disabled>已签收</button>
						@else
							<button class="btn btn-success confirm_values" style="width: 100%;">确定</button>
						@endif
					</div>
					<div class="col-lg-2">
						<a href="{{ url('/naizhan/shengchan/peisongguanli') }}" class="btn btn-outline btn-success" style="width: 100%;">调配配送计划</a>
					</div>
				</div>
				
			</div>
		</div>
		
	</div>
@endsection

@section('script')
	<!--Save & Update User Information-->
	<script src="<?=asset('js/ajax/shengchan_qianshoujihua_ajax.js') ?>"></script>
@endsection