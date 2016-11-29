@extends('naizhan.layout.master')

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li>
					<a href="{{ url('naizhan/pingkuang')}}">瓶框管理</a>
				</li>
				<li>
					<strong>配送员瓶框回收记录</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				<div><hr></div>
				<div class="feed-element">
					<div class="col-md-4">
						<label class="col-lg-3" style="padding-top:5px;">配送员 </label>
						<div class="col-lg-9">
							<select id="milkman" data-placeholder="" class="chosen-select" style="height:34px; width:100%;" tabindex="2">
								@foreach($milkmans as $mm)
									<option @if($milkman_id == $mm->id) selected @endif value="{{$mm->id}}">{{$mm->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-group col-md-5" id="data_range_select">
						<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
						<div class="input-daterange input-group col-md-8" id="datepicker">
                            <input type="text" class="input-sm form-control" id="start" value="{{$start_date}}"/>
                            <span class="input-group-addon">至</span>
                            <input type="text" class="input-sm form-control" id="end" value="{{$end_date}}"/>
                        </div>
					</div>
					<div class="col-md-2"  style="padding-top:5px;">
						<button id="find" type="button" class="btn btn-success btn-m-d">筛选</button>
						&nbsp;
						<button class="btn btn-success btn-outline btn-m-d" data-action="export_csv">导出</button>
						&nbsp;
						<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
					</div>
				</div>

				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="table1" class="table table-bordered">
                            <thead>
								<tr>
									<th data-sort-ignore="true">日期</th>
									<th data-sort-ignore="true">瓶类</th>
									<th data-sort-ignore="true">配送员回收量</th>
									<th data-sort-ignore="true">配送数量</th>
									<th data-sort-ignore="true"></th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i=0; ?>
							@foreach($milkmanbottlerefunds as $date=>$mb)
								<?php $i++; $j=0; $refund_sum = 0; $delivered_sum = 0; ?>
								@foreach($mb as $type=>$m)
									<?php $j++; ?>
								<tr>
									@if($j==1)
									<td rowspan="{{count($mb)+1}}">{{$date}}</td>
									@endif
									<td>{{$type}}</td>
									<td>{{$m['refund']}}</td>
									<td>@if($m['delivered'] == '') 0 @else {{$m['delivered']}} @endif</td>
									<td></td>
									<?php $refund_sum += $m['refund']; $delivered_sum += $m['delivered'];?>
								</tr>
								@endforeach
								<tr>
									<td>合计</td>
									<td>{{$refund_sum}}</td>
									<td>{{$delivered_sum}}</td>
									<td></td>
								</tr>
							@endforeach
                            </tbody>
                            {{--<tfoot>--}}
                            	{{--<tr>--}}
                            		{{--<td colspan="100%"><ul class="pagination pull-right"></ul></td>--}}
                            	{{--</tr>--}}
                            {{--</tfoot>--}}
                        </table>
                    </div>
                </div>

			</div>
		</div>
	</div>
@endsection
@section('script')
	<script type="text/javascript" src="<?=asset('js/global.js') ?>"></script>

    <script type="text/javascript">
		$('#data_range_select .input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true
        });
		
		$(document).on('click','#find',function () {
			var milkman_id = $('#milkman option:selected').val();
			var start_date = $('#start').val();
			var end_date = $('#end').val();
			window.location.href = SITE_URL+"milk/public/naizhan/pingkuang/peisongyuanpingkuang/?milkman_id="+milkman_id+"&start_date="+start_date+"&end_date="+end_date+"";
		})

		$('button[data-action = "print"]').click(function () {

			var sendData = [];

			var printContents;

			printContents = document.getElementById("table1").outerHTML;
			var originalContents = document.body.innerHTML;
			document.body.innerHTML = printContents;

			window.print();
			document.body.innerHTML = originalContents;
			location.reload();
		});

		$('button[data-action = "export_csv"]').click(function () {

			var sendData = [];

			var i = 0;
			//send order data
			$('#table1 thead tr').each(function () {
				var tr = $(this);
				var trdata = [];

				var j = 0;
				$(tr).find('th').each(function () {
					var td = $(this);
					var td_data = td.html().toString().trim();
					td_data =td_data.split("<");
					// if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
					//     td_data = "";
					trdata[j] = td_data[0];
					j++;
				});
				sendData[i] = trdata;
				i++;
			});

			$('#table1 tbody tr').each(function () {
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
    </script>
@endsection