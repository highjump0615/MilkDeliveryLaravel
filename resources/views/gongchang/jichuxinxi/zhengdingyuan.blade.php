@extends('gongchang.layout.master')

@section('content')
	@include('gongchang.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('gongchang.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li>
					<a href="">基础信息管理</a>
				</li>
				<li>
					<a href=""><strong>征订员管理</strong></a>
				</li>
			</ol>
		</div>
		
			<div class="row white-bg">	
<!--Table-->				
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-3">
							<label>征订员:</label>
							<input type="text" id="filter_name" class="form-control" style="width: 70%; display: inline">
						</div>
						<div class="col-md-3">
							<label>编号:</label>
							<input type="text" id="filter_number" class="form-control" style="width: 70%; display: inline">
						</div>
						<div class="col-md-3">
							<label>所属:</label>
							<input type="text" id="filter_station" class="form-control" style="width: 70%; display: inline">
						</div>				
						<div class="col-md-3"  style="padding-top:5px;">
							<button type="button" class="btn btn-success btn-md" data-action="show_selected">筛选</button>
							&nbsp;
							&nbsp;
							<button type="button" class="btn btn-success btn-md btn-outline" data-action="print">打印</button>
						</div>
					</div>
					<div><hr></div>
					@if (count($errors) > 0)
						<div class="row">
							<div class="form-group">
								<div class="col-sm-10 col-sm-offset-1">
									<div class="alert alert-danger">
										<ul>
											@foreach ($errors->all() as $error)
												<li>{{ $error }}</li>
											@endforeach
										</ul>
									</div>
								</div>
							</div>
						</div>
					@endif
					@if (session('status'))
						<div class="row">
							<div class="form-group">
								<div class="col-sm-10 col-sm-offset-1">
									<div class="alert alert-success">
										{{ session('status') }}
									</div>
								</div>
							</div>
						</div>
					@endif

					<form method="post" action="{{url('/gongchang/jichuxinxi/zhengdingyuan')}}">
					<div class="feed-element">
						<div class="col-md-3">
							<label>姓名: &nbsp;</label>
							<input type="text" name="name" class="form-control" style="width: 70%; display: inline">
						</div>
					</div>
					<div class="feed-element">
						<div class="col-md-3">
							<label>电话: &nbsp;</label>
							<input type="text" pattern="\d{11}"  id="phone" name="phone" class="form-control"
								   oninvalid="this.setCustomValidity('手机号码得11位数')" oninput="this.setCustomValidity('')"
								   style="width: 70%; display: inline">
						</div>
					</div>
					<div class="feed-element">
						<div class="col-md-3">
							<label>所属:</label>
							&nbsp;
							<select data-placeholder="" class="chosen-select form-control" name="station" style="width: 70%; display: inline">
								<option value="-1">奶厂</option>
								@foreach($stations as $s)
									<option value="{{$s->id}}">{{$s->name}}</option>
								@endforeach
							</select>
						</div>
						<button class="btn btn-success btn-md" type="submit"><i class="fa fa-plus"></i> 保存并添加</button>
					</div>
					</form>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="origin_table" class="footable table table-bordered" data-page-size="10">
							<thead>
							<tr>
								<th data-sort-ignore="true">姓名</th>
								<th data-sort-ignore="true">编号</th>
								<th data-sort-ignore="true">电话</th>
								<th data-sort-ignore="true">所属</th>
								<th data-sort-ignore="true">操作</th>
							</tr>
							</thead>
							<tbody>
							@foreach($checkers as $c)
								<tr id="checker{{$c->id}}">
									<td class="o_checker_name">{{$c->name}}</td>
									<td class="o_checker_number">{{$c->number}}</td>
									<td class="o_checker_phone">{{$c->phone}}</td>
									<td class="o_checker_station">{{$c->DanweiName}}</td>
									<td>
										<button class="btn btn-sm btn-success update-checker" value="{{$c->id}}">修改</button>
										&nbsp;
										<button class="btn btn-sm btn-success delete-checker" value="{{$c->id}}">删除</button>
									</td>
								</tr>
							@endforeach
							</tbody>
							<tfoot>
							<tr>
								<td colspan="5">
									<ul class="pagination pull-right"></ul>
								</td>
							</tr>
							</tfoot>
						</table>


						<table id="checker_filter_table" class="footable table table-bordered" data-page-size="10" style="display:none;">
							<thead>
							<tr>
								<th data-sort-ignore="true">姓名</th>
								<th data-sort-ignore="true">编号</th>
								<th data-sort-ignore="true">电话</th>
								<th data-sort-ignore="true">所属</th>
								<th data-sort-ignore="true">操作</th>
							</tr>
							</thead>
							<tbody>

							</tbody>
							<tfoot>
							<tr>
								<td colspan="5">
									<ul class="pagination pull-right"></ul>
								</td>
							</tr>
							</tfoot>
						</table>
                    </div>

					<div id="checker_modal" class="modal fade" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-body">
									<div class="row">
										<div class="col-sm-12"><h3 class="m-t-none m-b"></h3>
											<form role="form" class="form-horizontal">
												<div class="feed-element">
													<label class="col-lg-3">姓名:</label>
													<div class="col-lg-9">
														<input type="text" placeholder="" class="form-control" id="checker_name" value="">
													</div>
												</div>
												<div class="feed-element">
													<label class="col-lg-3">编号:</label>
													<div class="col-lg-9">
														<input type="text" placeholder="" class="form-control" id="checker_number" value="">
													</div>
												</div>
												<div class="feed-element">
													<label class="col-lg-3">电话:</label>
													<div class="col-lg-9">
														<input type="text" placeholder="" class="form-control" id="checker_phone">
													</div>
												</div>
												<div class="feed-element">
													<label class="col-lg-3">所属:</label>
													<div class="col-lg-9">
														<select data-placeholder="" class="chosen-select form-control" id="checker_station" tabindex="2">
															<option value="-1">奶厂</option>
															@foreach($stations as $s)
																<option value="{{$s->id}}">{{$s->name}}</option>
															@endforeach
														</select>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" id="post_update_checker" class="btn btn-white" data-dismiss="modal">确定</button>
									<button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
								</div>
							</div>
						</div>
					</div>
                </div>
			</div>
		</div>
	</div>
@endsection

@section('script')


	<script type="text/javascript">
		$(document).ready(function() {

			$(document).on('click', '.update-checker', function () {
				console.log('update checker modal dialog');

				var checker_id = $(this).val();
				current_row_number = $(this).closest('tr').find('td:first').text();
				var url = API_URL + 'gongchang/jichuxinxi/zhengdingyuan/' +checker_id;
				console.log(url);
				$.get(url, function (data) {
					//success data
					console.log(data);
					$('#checker_name').val(data.name);
					$('#checker_phone').val(data.phone);
					$('#checker_number').val(data.number);
					if(data.station_id != null){
						$('#checker_station').val(data.station_id);
					}else {
						$('#checker_station').val(-1);
					}

					$('#post_update_checker').val(data.id);

					$('#checker_modal').modal("show");
				})
			});
		});

		function deleteChecker(checker_id) {
			var url = API_URL + 'gongchang/jichuxinxi/zhengdingyuan';
			$.ajax({
				type: "DELETE",
				url: url + '/' + checker_id,
				success: function (data) {
					console.log(data);
					$("#checker" + checker_id).remove();
				},
				error: function (data) {
					console.log('Error:', data);
				}
			});
		}

		$(document).on('click', '.delete-checker', function () {
			var checker_id = $(this).val();
			$.confirm({
				icon: 'fa fa-warning',
				title: '征订员删除',
				text:'您要删除征订员吗？',
				confirmButton: "是",
				cancelButton: "不",
				confirmButtonClass: "btn-success",
				confirm: function () {
					deleteChecker(checker_id);
				},
				cancel: function () {
					return;
				}
			});
		});

		$('#post_update_checker').click(function () {
			var checker_id = $(this).val();
			console.log("updating checker");
			$.ajax({
				type: 'POST',
				url: API_URL + 'gongchang/jichuxinxi/zhengdingyuan/' + checker_id,
				data: {
					'name': $('#checker_name').val(),
					'phone': $('#checker_phone').val(),
					'number': $('#checker_number').val(),
					'station': $('#checker_station').val(),
				},
				success: function (data) {
					console.log(data);

					var role = '';
					role += '<tr id="checker' + data.id + '">';
					role += '<td>' + data.name + '</td>';
					role += '<td>' + data.number + '</td>';
					role += '<td>' + data.phone + '</td>';
					role += '<td>' + data.danwei_name + '</td>';
					role += '<td>';
					role += '<button class="btn btn-sm btn-success update-checker" value="' + data.id + '">修改</button>';
					role += '&emsp;'; role += '<button class="btn btn-sm btn-success delete-checker" value="' + data.id + '">删除</button>';
					role += '</td>';
					role += '</tr>';

					$("#checker" + checker_id).replaceWith( role );
					$('#checker_modal').modal('hide');
				},
				error: function (data) {
					console.log(data);
				},
			});
		});

		//Print Table Data
		$('button[data-action = "print"]').click(function () {

			var printContents;
			printContents = document.getElementById("origin_table").outerHTML;

			var originalContents = document.body.innerHTML;
			document.body.innerHTML = printContents;
			window.print();
			document.body.innerHTML = originalContents;
			location.reload();
		});

		$('button[data-action="show_selected"]').click(function () {

			console.log("filtering");

			var origin_table = $('#origin_table');
			var checker_filter_table = $('#checker_filter_table');
			var checker_filter_table_tbody = $('#checker_filter_table tbody');

			//get all selection
			var f_name = $('#filter_name').val().trim().toLowerCase();
			var f_number = $('#filter_number').val().trim().toLowerCase();
			var f_station = $('#filter_station').val();

			//show only rows in filtered table that contains the above field value
			var filter_rows = [];
			var i = 0;

			$('#origin_table').find('tbody tr').each(function () {
				var tr = $(this);

				var o_checker_name = tr.find('td.o_checker_name').html().toString().toLowerCase();
				var o_station = tr.find('td.o_checker_station').html().toString().toLowerCase();
				var o_number = tr.find('td.o_checker_number').html().toString().toLowerCase();

				//customer
				if ((f_name != "" && o_checker_name.includes(f_name)) || (f_name == "")) {
					tr.attr("data-show-1", "1");
				} else {
					tr.attr("data-show-1", "0")
				}

				if ((f_number != "" && o_number.includes(f_number)) || (f_number == "")) {
					tr.attr("data-show-2", "1");
				} else {
					tr.attr("data-show-2", "0")
				}

				if ((f_station != "none" && o_station.includes(f_station)) || (f_station == "none")) {
					tr.attr("data-show-3", "1");
				} else {
					tr.attr("data-show-3", "0")
				}


				if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1") && (tr.attr("data-show-3") == "1")) {

					console.log("---" + $(tr)[0]);
					filter_rows[i] = $(tr)[0].outerHTML;
					console.log('here?');
					i++;


				} else {
					//tr.addClass('hide');
				}


			});
			console.log('there?');
			$(origin_table).hide();
			$(checker_filter_table_tbody).empty();

			var length = filter_rows.length;

			var footable = $('#checker_filter_table').data('footable');

			for (i = 0; i < length; i++) {
				var trd = filter_rows[i];
				footable.appendRow(trd);
			}

			$(checker_filter_table).show();
		});
	</script>
@endsection