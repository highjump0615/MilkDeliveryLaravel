@extends('zongpingtai.layout.master')

@section('content')
	@include('zongpingtai.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('zongpingtai.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="{{ url('zongpingtai/tongji')}}">统计分析</a>
				</li>
				<li class="active">
					<strong>客户订单修改统计</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-3">
							<label>奶站名称:</label>
							<input class="form-control" type="text" id="station_name" style="width: 70%; display: inline" value="{{$current_station_name}}">
						</div>
						<div class="col-md-3">
							<label>编号:</label>
							<input class="form-control" type="text" id="station_number" style="width: 70%; display: inline" value="{{$current_station_number}}">
						</div>
						<div class="col-md-6 form-group">
							<label>区域:</label>
							<select id="province" data-placeholder="" class="form-control chosen-select province_list" style="width: 30%; display: inline" value="">
								<option value="none">全部</option>
								@if (isset($province))
									@foreach($province as $pr)
										<option value="{{$pr->name}}" @if($pr->name == $current_province) selected @endif>{{$pr->name}}</option>
									@endforeach
								@endif
							</select>
							<select id="city" data-placeholder="" class="form-control chosen-select city_list" style="width: 30%; display: inline">
								<option value="none">全部</option>
							</select>
							<input type="hidden" id="currrent_city" value="{{$current_city}}">
						</div>
					</div>
					<br>
					<div class="feed-element">
						<div class="form-group col-md-5" id="data_range_select">
							<label class="col-md-2 control-label" style="padding-top:5px;">日期:</label>
							<div class="input-daterange input-group col-md-8" id="datepicker">
								<input id="start_date" type="text" class="input-md form-control" name="start" value="{{$currrent_start_date}}"/>
								<span class="input-group-addon">至</span>
								<input id="end_date" type="text" class="input-md form-control" name="end" value="{{$current_end_date}}"/>
							</div>
						</div>
						<div class="col-md-offset-4 col-lg-3"  style="padding-top:5px;">
							<button id="search" type="button" class="btn btn-success btn-m-d">筛选</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action = "print">打印</button>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="table1" class="table footable table-bordered">
                            <thead>
								<tr>
									<th colspan="11" style="font-size:20px; text-align: center;">客户订单修改信息汇总表</th>
								</tr>
								<tr>
									<th rowspan="2">序号</th>
									<th rowspan="2">奶站名称</th>
									<th rowspan="2">联系电话修改</th>
									<th rowspan="2">配送地址変更</th>
									<th rowspan="2">暂停配送客户</th>
									<th colspan="5">客户订单产品变更</th>
									<th rowspan="2">配送规则修改</th>
								</tr>
								<tr>
									<th>增加单词配送量</th>
									<th>减少单词配送量</th>
									<th>鲜奶调换酸奶</th>
									<th>酸奶调换鲜奶</th>
									<th>酸奶变更口味</th>
									<th></th>
								</tr>
                            </thead>
							<tbody>
							<?php $i=0; ?>
							@foreach($stations as $s)
								<?php $i++; ?>
								<tr>
									<td>{{$i}}</td>
									<td><?php
										if(isset($s['name']))
											echo $s['name'];
										else
											echo "";
										?>
									</td>
									<td><?php
										if(isset($s['phone']))
											echo $s['phone'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['address']))
											echo $s['address'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['stopped']))
											echo $s['stopped'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['increased']))
											echo $s['increased'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['decreased']))
											echo $s['decreased'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['milk_yogurt']))
											echo $s['milk_yogurt'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['yogurt_milk']))
											echo $s['yogurt_milk'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['yogurt_kouwei']))
											echo $s['yogurt_kouwei'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['rule']))
											echo $s['rule'];
										else
											echo "0";
										?>
									</td>
								</tr>
							@endforeach
							</tbody>
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
			if ($('.province_list').val() != "none")
				$('.province_list').trigger('change');
		});

		$('#data_range_select .input-daterange').datepicker({
			keyboardNavigation: false,
			forceParse: false,
			autoclose: true
		});

		$('#search').click(function () {
			var station_name = $('#station_name').val();
			var station_number = $('#station_number').val();
			var province =$('#province option:selected').val();
			if(province == 'none'){
				province = '';
			}
			var city=$('#city option:selected').val();
			if(city == 'none'){
				city = '';
			}
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();

			window.location.href = SITE_URL+"zongpingtai/tongji/kehudingdanxiugai?station_name="+station_name+"&station_number="+station_number+
					"&province="+province+"&city="+city+"&start_date="+start_date+"&end_date="+end_date+"";
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

		$('.province_list').on('change', function () {

			var current_province = $(this).val();
			var city_list = $(this).parent().find('.city_list');
			if (current_province == "none" || current_province == null) {
				$(city_list).empty();
				$(city_list).append('<option value="none">全部</option>');
				return;
			}
			var dataString = {'province': current_province};
			$.ajax({
				type: "GET",
				url: API_URL + "province_to_city",
				data: dataString,
				success: function (data) {
					if (data.status == "success") {
						city_list.empty();

						var cities, city, citydata,inputdata;

						cities = data.city;

						city_list.append('<option value="none">全部</option>');

						for (var i = 0; i < cities.length; i++) {
							var citydata = cities[i];
							if($('#currrent_city').val() == citydata.name){
								inputdata = '<option value="' + citydata.name + '" selected>' + citydata.name + '</option>';
							}else {
								inputdata = '<option value="' + citydata.name + '" >' + citydata.name + '</option>';
							}

						}
						city_list.append(inputdata);
					}
				},
				error: function (data) {
					console.log(data);
				}
			})
		});
	</script>
@endsection