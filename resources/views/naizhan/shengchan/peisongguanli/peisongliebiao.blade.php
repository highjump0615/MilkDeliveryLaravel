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
					<strong>配送列表</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">

				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-2"></div>
						<div class="col-lg-8">
							<div class="col-lg-3">
								<a href="{{ url('/naizhan/shengchan/ziyingdingdan') }}" class="btn btn-success" type="button" style="width:100%;">自营出库</a>
							</div>
							<div class="col-lg-3">
								<a href="{{ url('/naizhan/shengchan/jinripeisongdan') }}" class="col-lg-3 btn btn-success" type="button" style="width:100%;">今日配送单</a>
							</div>
							<div class="col-lg-3">
								<a href="{{ url('/naizhan/shengchan/peisongfanru') }}" class="col-lg-3 btn btn-success" type="button" style="width:100%;">配送返录</a>
							</div>
						</div>
						<div class="col-md-2"></div>
					</div>

					@if(session('page_status'))
					<div class="feed-element">
						<div class="col-md-2"></div>
						<div class="col-lg-8">
							<div class="col-lg-3">
								<label style="color: red; padding: 5px;font-size: 14px;">{{session('page_status')}}</label>
							</div>
						</div>
						<div class="col-md-2"></div>
					</div>
					@endif
				</div>

				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table class="footable table table-bordered">
                            <thead>
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">地址</th>
									<th data-sort-ignore="true">配送内容</th>
									<th data-sort-ignore="true">收货人</th>
									<th data-sort-ignore="true">配送时间</th>
									<th data-sort-ignore="true">电话</th>
									<th data-sort-ignore="true">备注</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i=0; ?>
							@foreach($delivery_plans as $dp)
								<?php $i++; ?>
								<tr>
									<td>
										{{$i}}
									</td>
									<td>{{$dp->address}}</td>
									<td>{{$dp->product}}</td>
									@if($dp->delivery_type==1)
										<td>{{$dp->customer->name}}</td>
									@else
										<td>{{$dp->customer_name}}</td>
									@endif
									<td>
										@if($dp->delivery_time == \App\Model\OrderModel\Order::ORDER_DELIVERY_TIME_MORNING)
											上午
										@elseif($dp->delivery_time == \App\Model\OrderModel\Order::ORDER_DELIVERY_TIME_AFTERNOON)
											下午
										@endif
									</td>
									<td>{{$dp->phone}}</td>
									<td>{{$dp->comment_delivery}}</td>
								</tr>
							@endforeach
                            </tbody>
							<tfoot>
                                <tr>
                                    <td colspan="8">
										<label  class="control-label pull-right" style="padding-top:5px;"></label>
                                        <ul class="pagination pull-right"></ul>
										<label class="control-label pull-right" style="padding-top:5px;"></label>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

			</div>
		</div>

	</div>
@endsection
