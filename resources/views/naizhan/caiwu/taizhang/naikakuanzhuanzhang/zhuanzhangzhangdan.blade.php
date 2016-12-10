@extends('naizhan.layout.master')

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li>
					<a href="{{ url('/naizhan/caiwu/taizhang') }}">财务管理</a>
				</li>
				<li>
					<a href="{{ url('/naizhan/caiwu/taizhang') }}">奶站台帐</a>
				</li>
				<li>
					<a href="{{ url('/naizhan/caiwu/taizhang/naikakuanzhuanzhang/zhuanzhangjiru') }}">奶卡订单转账记录</a>
				</li>
				<li class="active">
					<a href="{{ url('/naizhan/caiwu/taizhang/naikakuanzhuanzhang/zhuanzhangzhangdan') }}"><strong>奶卡转账账单</strong></a>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				<div class="ibox-content">					
					<div class="col-md-2">
						<a href="{{ url('/naizhan/caiwu/taizhang/naikakuanzhuanzhang/zhuanzhangjiru') }}" class="btn btn-success btn-outline" type="button" style="width:100%">查看转账记录</a>
					</div>
					<div class="col-md-2 col-md-offset-8" style="padding-top:5px;">
						<button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
						<button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
					</div>
				</div>

				<div class="ibox float-e-margins">
					<div class="ibox-content">

						<table class="table footable table-bordered">
							<thead style="background-color:#00cc55;">
							<tr>
								<th data-sort-ignore="true">序号</th>
								<th data-sort-ignore="true">生成时间</th>
								<th data-sort-ignore="true">账单号</th>
								<th data-sort-ignore="true">账单日期</th>
								<th data-sort-ignore="true">金额</th>
								<th data-sort-ignore="true">订单数量</th>
								<th data-sort-ignore="true">状态</th>
								<th data-sort-ignore="true">账单详情</th>
								<th data-sort-ignore="true">备注</th>
							</tr>
							</thead>
							<tbody>
							<?php $i=0;?>
							@foreach($ncts as $ncs)
								<?php
								$j = 0;
								$first_row_span = count($ncs);
								?>
								@foreach($ncs as $nc)
									<?php $j++; ?>
									<tr>
										@if($j==1)
											<td rowspan="{{$first_row_span}}">{{$i+1}}</td>
										@endif
										<td>{{$nc->created_at}}</td>
										<td>{{$nc->id}}</td>
										<td>{{$nc->order_from}} ~ {{$nc->order_to}}</td>
										<td>{{$nc->total_amount}}</td>
										<td>{{$nc->order_count}}</td>
										<td>未转</td>
										<td>
											<a href="{{URL::to('/naizhan/caiwu/taizhang/naikakuanzhuanzhang/zhangdanmingxi/'.$nc->id)}}">查看明细</a>
										</td>
										<td></td>
									</tr>
									<?php $i++;?>
								@endforeach
							@endforeach

							</tbody>
							<tfoot>
							<tr>
								<td colspan="100%">
									<ul class="pagination pull-right"></ul>
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

@section('script')
	<script type="text/javascript">
		//Export
		$('button[data-action = "export_csv"]').click(function () {

			var od = $('#order_table').css('display');
			var fd = $('#filter_table').css('display');

			var sendData = [];

			var i = 0;
			if (od != "none") {
				//send order data
				$('#order_table thead tr').each(function () {
					var tr = $(this);
					var trdata = [];

					var j = 0;
					$(tr).find('th').each(function () {
						var td = $(this);
						var td_data = td.html().toString().trim();
						if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
							td_data = "";

						trdata[j] = td_data;
						j++;
					});
					sendData[i] = trdata;
					i++;
				});

				$('#order_table tbody tr').each(function () {
					var tr = $(this);
					var trdata = [];

					var j = 0;
					$(tr).find('td').each(function () {
						var td = $(this);
						var td_data = td.html().toString().trim();
						if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
							td_data = "";
						trdata[j] = td_data;
						j++;
					});
					sendData[i] = trdata;
					i++;
				});


			} else if (fd != "none") {
				//send filter data
				$('#filter_table thead tr').each(function () {
					var tr = $(this);
					var trdata = [];

					var j = 0;
					$(tr).find('th').each(function () {
						var td = $(this);
						var td_data = td.html().toString().trim();
						if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
							td_data = "";
						trdata[j] = td_data;
						j++;
					});
					sendData[i] = trdata;
					i++;
				});

				$('#filter_table tbody tr').each(function () {
					var tr = $(this);
					var trdata = [];

					var j = 0;
					$(tr).find('td').each(function () {
						var td = $(this);
						var td_data = td.html().toString().trim();
						if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
							td_data = "";

						trdata[j] = td_data;
						j++;
					});
					sendData[i] = trdata;
					i++;
				});

			} else {
				return;
			}

			var send_data = {"data": sendData};
			console.log(send_data);

			$.ajax({
				type: 'POST',
				url: API_URL + "export",
				data: send_data,
				success: function (data) {
					console.log(data);
					if (data.status == 'success') {
						var path = data.path;
						location.href = path;
					}
				},
				error: function (data) {
					//console.log(data);
				}
			})
		});

		//Print Table Data
		$('button[data-action = "print"]').click(function () {

			var od = $('#order_table').css('display');
			var fd = $('#filter_table').css('display');
			var sendData = [];
			var printContents;
			if (od != "none") {
				//print order data
				printContents = document.getElementById("order_table").outerHTML;
			} else if (fd != "none") {
				//print filter data
				printContents = document.getElementById("filter_table").outerHTML;
			} else {
				return;
			}
			var originalContents = document.body.innerHTML;
			document.body.innerHTML = printContents;
			window.print();
			document.body.innerHTML = originalContents;
			location.reload();
		});
	</script>
@endsection
