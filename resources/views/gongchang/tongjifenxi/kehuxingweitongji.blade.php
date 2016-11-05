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
				<li class="active"><strong>客户行为统计</strong></li>
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
							<input class="form-control" type="text" id="station_number" style="width: 70%; display: inline">
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
							<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="table1" class="footable table table-bordered" data-page-size="10">
                            <thead>
								<tr>
									<th data-sort-ignore="true" rowspan="2">序号</th>
									<th data-sort-ignore="true" rowspan="2">区域</th>
									<th data-sort-ignore="true" rowspan="2">分区</th>
									<th data-sort-ignore="true" rowspan="2">奶站 （经销商名称）</th>
									<th data-sort-ignore="true" colspan="13">期间客户数量变化</th>
									<th data-sort-ignore="true" colspan="6">期末客户状态汇总</th>
								</tr>
								<tr>
									<th data-sort-ignore="true">新增客户数</th>
									<th data-sort-ignore="true">订单金额</th>
									<th data-sort-ignore="true">期间到期客户数</th>
									<th data-sort-ignore="true">本期到期续单客户数</th>
									<th data-sort-ignore="true">前期到期续单客户数</th>
									<th data-sort-ignore="true">续单金额</th>
									<th data-sort-ignore="true">续单率</th>
									<th data-sort-ignore="true">本期-退单款客户数</th>
									<th data-sort-ignore="true">退款金额</th>
									<th data-sort-ignore="true">订单金额合计</th>
									<th data-sort-ignore="true">划转公司奶款金额</th>
									<th data-sort-ignore="true">支付返利提成金额</th>
									<th data-sort-ignore="true">其他划转金额</th>
									<th data-sort-ignore="true">在配送客户数</th>
									<th data-sort-ignore="true">在配送-剩余订单金额</th>
									<th data-sort-ignore="true">暂停客户数</th>
									<th data-sort-ignore="true">剩余订单金额</th>
									<th data-sort-ignore="true">总之/退款客户数</th>
									<th data-sort-ignore="true">期末订单金额结余</th>
								</tr>
                            </thead>
                            <tbody>
							@foreach($stations as $i=>$s)
								<tr>
									<td>{{$i}}</td>
									<td><?php
										if(isset($s['province_name']))
											echo $s['province_name'];
										else
											echo "";
										?>
									</td>
									<td><?php
										if(isset($s['city_name']))
											echo $s['city_name'];
										else
											echo "";
										?>
									</td>
									<td><?php
										if(isset($s['name']))
											echo $s['name'];
										else
											echo "";
										?>
									</td>
									<td><?php
										if(isset($s['new_customers']))
											echo $s['new_customers'];
										else
											echo "0";
									?>
									</td>
									<td><?php
										if(isset($s['new_order_price']))
											echo $s['new_order_price'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['finished_orders']))
											echo $s['finished_orders'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['xuedan_after_another']))
											echo $s['xuedan_after_another'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['xuedan_after_finished_prev']))
											echo $s['xuedan_after_finished_prev'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['xudan_price']))
											echo $s['xudan_price'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['xudan_ratio']))
											echo $s['xudan_ratio'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['canceled_orders']))
											echo $s['canceled_orders'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['canceled_orders_amount']))
											echo $s['canceled_orders_amount'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['new_order_amount_real']))
											echo $s['new_order_amount_real'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['trans_to_factory_amount']))
											echo $s['trans_to_factory_amount'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['trans_for_delivery_cost_amount']))
											echo $s['trans_for_delivery_cost_amount'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['trans_to_other_amount']))
											echo $s['trans_to_other_amount'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['delivery_orders']))
											echo $s['delivery_orders'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['delivery_orders_remaining_amount']))
											echo $s['delivery_orders_remaining_amount'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['stopped_orders']))
											echo $s['stopped_orders'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['stopped_orders_amount']))
											echo $s['stopped_orders_amount'];
										else
											echo "0";
										?>
									</td>
									<td><?php
										if(isset($s['ended_orders_amount']))
											echo $s['ended_orders_amount'];
										else
											echo "0";
										?>
									</td>

									<td><?php
										if(isset($s['total_orders_remaining_amount']))
											echo $s['total_orders_remaining_amount'];
										else
											echo "0";
										?>
									</td>

								</tr>
							@endforeach
                            </tbody>
							<tfoot>
								<tr>
									<td colspan="23">
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
		$(document).ready(function(){
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        });
		$('.footable').footable();

		$('.input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: false,
            autoClose: true,
			todayBtn: false,

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