@extends('gongchang.layout.master')
@section('css')
	<link href="<?=asset('css/plugins/datepicker/datepicker3.css') ?>" rel="stylesheet">
@endsection
@section('content')
	@include('gongchang.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('gongchang.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li>
					<a href="">统计分析</a>
				</li>
				<li class="active"><strong>客户订单修改统计</strong></li>
			</ol>
		</div>
		<div class="row">	
<!--Table-->				
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-3">
							<label>奶站名称:</label>
							<input class="form-control" type="text" id="station_name" style="width: 70%; display: inline">
						</div>
						<div class="col-md-3">
							<label>编号:</label>
							<input class="form-control" type="text" id="station_id" style="width: 70%; display: inline">
						</div>
						<div class="col-md-6">
							<label>区域:</label>
							&nbsp;
							<select id="province" data-placeholder="" class="chosen-select form-control" style="width: 40%; display: inline">
								<option value="北京">北京</option>
								<option value="河北">河北</option>
							</select>
							<select id="city" data-placeholder="" class="chosen-select form-control" style="width: 40%; display: inline">
								<option value="东城区">东城区</option>
								<option value="西城区">西城区</option>
							</select>
						</div>
					</div>
					<br>
					<div class="feed-element">					
						<div class="form-group col-md-5" id="data_range_select">
							<label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
							<div class="input-daterange input-group col-md-8" id="datepicker">
                                <input type="text" class="input-md form-control" name="start" />
                                <span class="input-group-addon">至</span>
                                <input type="text" class="input-md form-control" name="end"  />
                            </div>
						</div>
						<div class="col-md-offset-4 col-lg-3"  style="padding-top:5px;">
							<button type="button" class="btn btn-success btn-md">筛选</button>
							&nbsp;
							<button class="btn-outline btn-success btn btn-m-d" data-action="print">打印</button>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="table1" class="footable table table-bordered" data-page-size="10">
                            <thead>
								<tr>
									<th colspan="11" style="font-size:20px; text-align: center;">客户订单修改信息汇总表</th>
								</tr>
								<tr>
									<th data-sort-ignore="true" rowspan="2">序号</th>
									<th data-sort-ignore="true" rowspan="2">奶站名称</th>
									<th data-sort-ignore="true" rowspan="2">联系电话修改</th>
									<th data-sort-ignore="true" rowspan="2">配送地址変更</th>
									<th data-sort-ignore="true" rowspan="2">暂停配送客户</th>
									<th data-sort-ignore="true" colspan="5">客户订单产品变更</th>
									<th data-sort-ignore="true" rowspan="2">配送规则修改</th>
								</tr>
								<tr>
									<th data-sort-ignore="true">增加单词配送量</th>
									<th data-sort-ignore="true">减少单词配送量</th>
									<th data-sort-ignore="true">鲜奶调换酸奶</th>
									<th data-sort-ignore="true">酸奶调换鲜奶</th>
									<th data-sort-ignore="true">酸奶变更口味</th>
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
											echo "0";
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
							<tfoot>
								<tr>
									<td colspan="11">
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
    <script src="<?=asset('js/plugins/datepicker/bootstrap-datepicker.js') ?>"></script>

    <script type="text/javascript">
		$(document).ready(function() {
			$('.footable').footable();
		});

		$('#date_2 .input-group.date').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: false,
            autoclose: true
        });
		$('#data_range_select .input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true
        });

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

	</script>
@endsection