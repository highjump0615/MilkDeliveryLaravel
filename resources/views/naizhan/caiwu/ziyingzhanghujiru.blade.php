@extends('naizhan.layout.master')
@section('css')
	<style>
		.business_header label {
			width: 100%;
			text-align: center;
		}

		.business_header label.title {
			background-color: #5ce1df;
			color: #333333;
		}
	</style>
@endsection
@section('content')
	@include('naizhan.theme.sidebar')
	<div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li>
					<a href="">财务管理</a>
				</li>
				<li>
					<a href=""><strong>自营账户</strong></a>
				</li>
			</ol>
		</div>

		<div class="row white-bg">
			<!--Table-->
			<div class="ibox-content white-bg business_header">
				<div class="col-md-6 col-md-offset-2">
					<div class="col-md-4">
						<label class="title">期初余额</label>
					</div>
					<div class="col-md-8">
						<label class="gray-bg">{{$station->business_term_start_amount}}</label>
					</div>
					<div class="col-md-4">
						<label class="title">本期增加</label>
					</div>
					<div class="col-md-8">
						<label class="gray-bg">{{$station->business_in}}</label>
					</div>
					<div class="col-md-4">
						<label class="title">本期减少</label>
					</div>
					<div class="col-md-8">
						<label class="gray-bg">{{$station->business_out}}</label>
					</div>
					<div class="col-md-4">
						<label class="title">期末余额</label>
					</div>
					<div class="col-md-8">
						<label class="gray-bg">{{$station->business_credit_balance}}</label>
					</div>
					{{--<div class="col-md-4">--}}
						{{--<label class="title">信用余额</label>--}}
					{{--</div>--}}
					{{--<div class="col-md-8">--}}
						{{--<label class="gray-bg">{{$station->init_business_credit_amount+$station->business_credit_balance}}</label>--}}
					{{--</div>--}}
				</div>
				<div class="col-md-4"></div>
				<div class="col-md-8"></div>
				<div class="col-md-4">
					<button class="btn btn-md btn-success" data-toggle="modal" href="#self_business_modal" type="button"
							style="position: absolute; bottom:5px; display: none;"><i class="fa fa-plus"></i> 添加收款记录
					</button>
				</div>
			</div>

		</div>
		<div class="feed-element">
			<div class="col-md-5"></div>
			<div class="form-group col-md-5" id="data_range_select">
				<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
				<div class="input-daterange input-group col-md-8" id="datepicker">
					<input type="text" class="input-sm form-control" id="filter_start_date" name="start"/>
					<span class="input-group-addon">至</span>
					<input type="text" class="input-sm form-control" id="filter_end_date" name="end"/>
				</div>
			</div>
			<div class="col-md-2" style="padding-top:5px;">
				<button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
				<button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
				<button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
			</div>
		</div>

		<div class="ibox float-e-margins">
			<div class="ibox-content">

				<table class="footable table table-bordered" id="order_table" data-page-size="10">
					<thead>
					<tr>
						<th data-sort-ignore="true">摘要</th>
						<th data-sort-ignore="true">时间</th>
						<th data-sort-ignore="true">项目</th>
						<th data-sort-ignore="true">金额</th>
						<th data-sort-ignore="true">流水号</th>
						<th data-sort-ignore="true">最新余额</th>
					</tr>
					</thead>
					<tbody>
					@if(isset($self_business_history))
						@foreach($self_business_history as $sbh)
							<tr>
								<td>{{$sbh->io_name}}</td>
								<td class="o_date">{{$sbh->time}}</td>
								<td>{{$sbh->type_name}}</td>
								<td>{{$sbh->amount}}</td>
								<td>{{$sbh->receipt_number}}</td>
								<td>{{$sbh->return_amount}}</td>
							</tr>
						@endforeach
					@endif
					</tbody>
					<tfoot>
					<tr>
						<td colspan="6">
							<ul class="pagination pull-right"></ul>
						</td>
					</tr>
					</tfoot>
				</table>

				<table class="footable table table-bordered" id="filter_table" data-page-size="10"
					   style="display:none;">
					<thead>
					<tr>
						<th data-sort-ignore="true">摘要</th>
						<th data-sort-ignore="true">时间</th>
						<th data-sort-ignore="true">项目</th>
						<th data-sort-ignore="true">金额</th>
						<th data-sort-ignore="true">流水号</th>
						<th data-sort-ignore="true">最新余额</th>
					</tr>
					</thead>
					<tbody>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="6">
							<ul class="pagination pull-right"></ul>
						</td>
					</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
@endsection

@section('script')
	<script type="text/javascript">
		var date = new Date();
		firstm = new Date(date.getFullYear(), date.getMonth(), 1);
		lastm = new Date(date.getFullYear(), date.getMonth() + 1, 0);

		$(document).ready(function () {
			$('#data_range_select .input-daterange').datepicker({
				keyboardNavigation: false,
				forceParse: false,
				autoclose: true,
				clearBtn: true,
				startDate: firstm,
				endDate: lastm,
			});
		});

		//Show selected
		$('button[data-action="show_selected"]').click(function () {

			var order_table = $('#order_table');
			var filter_table = $('#filter_table');
			var filter_table_tbody = $('#filter_table tbody');

			var f_start_date = $('#filter_start_date').val();
			var f_end_date = $('#filter_end_date').val();

			//show only rows in filtered table that contains the above field value
			var filter_rows = [];
			var i = 0;

			$('#order_table').find('tbody tr').each(function () {
				var tr = $(this);

				o_date = tr.find('td.o_date').html();

				if ((f_start_date == "" && f_end_date == "")) {
					tr.attr("data-show-1", "1");

				} else if (f_start_date == "" && f_end_date != "") {

					var f2 = new Date(f_end_date);
					var oo = new Date(o_date);
					if (oo <= f2) {
						tr.attr("data-show-1", "1");
					} else {
						tr.attr("data-show-1", "0");
					}

				} else if (f_start_date != "" && f_end_date == "") {

					var f1 = new Date(f_start_date);
					var oo = new Date(o_date);
					if (oo >= f1) {
						tr.attr("data-show-1", "1");
					} else {
						tr.attr("data-show-1", "0");
					}
				} else {
					//f_start_date, f_end_date, o_date
					var f1 = new Date(f_start_date);
					var f2 = new Date(f_end_date);
					var oo = new Date(o_date);
					if (f1 <= f2 && f1 <= oo && oo <= f2) {
						tr.attr("data-show-1", "1");

					} else if (f1 >= f2 && f1 >= oo && oo >= f2) {
						tr.attr("data-show-1", "1");

					} else {
						tr.attr("data-show-1", "0");
					}
				}

				if ((tr.attr("data-show-1") == "1" )) {
					//tr.removeClass('hide');
					filter_rows[i] = $(tr)[0].outerHTML;
					i++;
					//filter_rows += $(tr)[0].outerHTML;
				}
				else {
					//tr.addClass('hide');
				}
			});

			$(order_table).hide();
			$(filter_table_tbody).empty();

			var length = filter_rows.length;

			var footable = $('#filter_table').data('footable');

			for (i = 0; i < length; i++) {
				var trd = filter_rows[i];
				footable.appendRow(trd);
			}
			$(filter_table).show();
		});

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