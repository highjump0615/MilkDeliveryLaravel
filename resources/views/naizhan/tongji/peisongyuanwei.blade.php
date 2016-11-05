@extends('naizhan.layout.master')

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="{{ url('naizhan/dingdan')}}">订单管理</a>
				</li>
				<li class="active">
					<strong>配送员业务统计</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-3">
							<label>配送员:</label>
							<input type="text" id="milkman_name" style="width:200px;" value="{{$milkman_name}}">
						</div>
						<div class="col-md-3">
							<label>编号:</label>
							<input type="text" id="milkman_number" style="width:200px;" value="{{$milkman_number}}">
						</div>
						<div class="form-group col-md-4" id="data_range_select">
							<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
							<div class="input-daterange input-group col-md-8" id="datepicker">
								<input type="text" class="input-sm form-control" id="start_date" name="start" value="{{$start_date}}"/>
								<span class="input-group-addon">至</span>
								<input type="text" class="input-sm form-control" id="end_date" name="end" value="{{$end_date}}"/>
							</div>
						</div>
						<div class="col-md-2"  style="padding-top:5px;">
							<button type="button" id="search" class="btn btn-success btn-m-d">筛选</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="statistics_by_milkman" class="table footable table-bordered" style="width: 2000px; overflow-x: auto">
                            <thead>
								<tr>
									<th rowspan="2">序号</th>
									<th rowspan="2">配送员</th>
									<th rowspan="2">配送客户数</th>
									<th colspan="{{count($products)+1}}">月单</th>
									<th colspan="{{count($products)+1}}">季单</th>
									<th colspan="{{count($products)+1}}">半年单</th>
									<th colspan="{{count($products)+1}}">团购或渠道数量</th>
									<th colspan="{{count($products)+1}}">配送数量</th>
									<th rowspan="2">回收空瓶数量</th>
								</tr>
								<tr>
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
									@foreach($products as $p)
										<th data-sort-ignore="true">{{$p->name}}</th>
									@endforeach
									<th data-sort-ignore="true">合计</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i =0; ?>
							@foreach($milkman_delivers as $name=>$md)
								<?php $i++; ?>
								<tr>
									<td>{{$i}}</td>
									<td>{{$md['milkman_name']}}</td>
									<td>{{$md['orders_count']}}</td>
									@foreach($md['yuedan'] as $p_id=>$yuedan)
										<td class="yuedan {{$p_id}}">{{$yuedan}}</td>
									@endforeach
									<td class="f_yuedan"></td>
									@foreach($md['jidan'] as $p_id=>$jidan)
										<td class="jidan {{$p_id}}">{{$jidan}}</td>
									@endforeach
									<td class="f_jidan"></td>
									@foreach($md['banniandan'] as $p_id=>$banniandan)
										<td class="banniandan {{$p_id}}">{{$banniandan}}</td>
									@endforeach
									<td class="f_banniandan"></td>
									@foreach($md['channel'] as $p_id=>$channel)
										<td class="channel {{$p_id}}">{{$channel}}</td>
									@endforeach
									<td class="f_channel"></td>
									@foreach($products as $p)
										<td class="f_{{$p->id}} total" product_type="{{$p->id}}"></td>
									@endforeach
									<td class="f_totalsum"></td>
									<td>{{$md['bottle_refund']}}</td>
								</tr>
							@endforeach
                            </tbody>
                            <tfoot align="right">
								<tr>
									<td colspan="100%"><ul class="pagination pull-right"></ul></td>
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
		$(document).ready(function () {
			$('#statistics_by_milkman tr:not(:first,:last)').each(function () {
				var f_yuedan = 0;
				$(this).find('td.yuedan').each(function () {
					var y_val = parseInt($(this).text());
					if(isNaN(y_val)){
						y_val = 0;
					}
					f_yuedan +=y_val;
				})
				$(this).find('td.f_yuedan').html(f_yuedan);

				var f_jidan = 0;
				$(this).find('td.jidan').each(function () {
					var j_val = parseInt($(this).text());
					if(isNaN(j_val)){
						j_val = 0;
					}
					f_jidan +=j_val;
				})
				$(this).find('td.f_jidan').html(f_jidan);

				var f_banniandan = 0;
				$(this).find('td.banniandan').each(function () {
					var b_val = parseInt($(this).text());
					if(isNaN(b_val)){
						b_val = 0;
					}
					f_banniandan +=b_val;
				})
				$(this).find('td.f_banniandan').html(f_banniandan);

				var f_channel = 0;
				$(this).find('td.channel').each(function () {
					var c_val = parseInt($(this).text());
					if(isNaN(c_val)){
						c_val = 0;
					}
					f_channel +=c_val;
				})
				$(this).find('td.f_channel').html(f_channel);

				var product_type = [];
				var j=0;
				$(this).find('td.total').each(function () {
					product_type[j] = $(this).attr('product_type');
					j++;
				})

				for(i = 0; i<product_type.length; i++)
				{
					var f_subtotal = 0;
					$(this).find('td.'+product_type[i]+'').each(function () {
						var t_val = parseInt($(this).text());
						if(isNaN(t_val)){
							t_val = 0;
						}
						f_subtotal +=t_val;
					})
					$(this).find('td.f_'+product_type[i]+'').html(f_subtotal);
				}

				var f_total = 0;
				$(this).find('td.total').each(function () {
					var total_val = parseInt($(this).text());
					if(isNaN(total_val)){
						total_val = 0;
					}
					f_total +=total_val;
				})
				$(this).find('td.f_totalsum').html(f_total);
			})
		})


		$('#data_range_select .input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true
        });

		$(document).on('click','#search',function () {
			var milkman_name = $('#milkman_name').val();
			var milkman_number = $('#milkman_number').val();
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			window.location.href = SITE_URL+"milk/public/naizhan/tongji/peisongyuanwei/?milkman_name="+milkman_name+"&milkman_number="+milkman_number+"&start_date="+start_date+"&end_date="+end_date+"";
		})

		$('button[data-action = "print"]').click(function () {

			var sendData = [];

			var printContents;

			printContents = document.getElementById("statistics_by_milkman").outerHTML;
			var originalContents = document.body.innerHTML;
			document.body.innerHTML = printContents;

			window.print();
			document.body.innerHTML = originalContents;
			location.reload();
		});
    </script>
@endsection