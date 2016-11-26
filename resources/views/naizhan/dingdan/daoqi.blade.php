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
					<strong>到期订单</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				<div class="feed-element">
					<div class="vertical-align">

						<div class="col-md-3 col-sm-6 col-xs-12">
							<label class="col-xs-4 control-label">收货人:</label>
							<div class="col-xs-8"><input type="text" placeholder="" class="form-control" value=""></div>
						</div>

						<div class="col-md-3 col-sm-6 col-xs-12">
							<label class="col-xs-4 control-label">电话:</label>
							<div class="col-xs-8"><input type="text" placeholder="" class="form-control" value=""></div>
						</div>

						<div class="col-md-3 col-sm-6 col-xs-12">
							<label class="col-xs-4 control-label">奶站:</label>
							<div class="col-xs-8">
								<select data-placeholder="" class="chosen-select form-control" style="height:35px;" tabindex="2">
									<option value="开发区">开发区</option>
									<option value="大屯西">大屯西</option>
									<option value="新世纪">新世纪</option>
									<option value="新世纪">华联小区</option>
								</select>
							</div>
						</div>

						<div class="col-md-3 col-sm-6 col-xs-12">
							<label class="col-xs-4 control-label">订单性质:</label>
							<div class="col-xs-8">
								<select data-placeholder="" class="chosen-select form-control" style="height:35px;" tabindex="2">
									<option value="新单">新单</option>
									<option value="续单">续单</option>
								</select>
							</div>
						</div>

					</div>
				</div>
				<div class="feed-element">
					<div class="vertical-align">
						<div class="col-md-3 col-sm-6 col-xs-12">
							<label class="col-xs-4 control-label">订单编号:</label>
							<div class="col-xs-8"><input type="text" placeholder="" class="form-control" value=""></div>
						</div>

						<div class="col-md-3 col-sm-6 col-xs-12">
							<label class="col-xs-4 control-label">征订员:</label>
							<div class="col-xs-8"><input type="text" placeholder="" class="form-control" value=""></div>
						</div>

						<div class="col-md-3 col-sm-6 col-xs-12">
							<label class="col-xs-4 control-label">订单类型:</label>
							<div class="col-xs-8">
								<select data-placeholder="" class="chosen-select form-control" style="height:35px;" tabindex="2">
									<option value="月单">月单</option>
									<option value="季单">季单</option>
									<option value="半年单">半年单</option>
								</select>
							</div>
						</div>

						<div class="col-md-3 col-sm-6 col-xs-12">
							<label class="col-xs-4 control-label">支付:</label>
							<div class="col-xs-8">
								<select data-placeholder="" class="chosen-select form-control" style="height:35px;" tabindex="2">
									<option value="微信">微信</option>
									<option value="现金">现金</option>
									<option value="奶卡">奶卡</option>
								</select>
							</div>
						</div>
					</div>
				</div>

				<div class="feed-element form-group" id="data_range_select">
						<div class="col-md-6">
							<label class="col-xs-4 control-label">下单日期:</label>
							<div class="col-xs-8">
								<div class="input-daterange input-group" id="datepicker">
		                            <input type="text" class="input-sm form-control" name="start" />
		                            <span class="input-group-addon">至</span>
		                            <input type="text" class="input-sm form-control" name="end"  />
		                        </div>
	                        </div>
	                     </div>
                        <div class="col-md-6"></div>
				</div>
				
				<div class="feed-element">
					<div class="col-md-3 col-sm-6">
					</div>
					<div class="col-md-3 col-md-offset-6 col-sm-6">
						<a href="" class="col-sm-3 col-xs-6">打印</a> 
						<a data-toggle="modal" class="btn btn-success dim col-sm-3 col-xs-6" href="#modal-form" type="button">筛选</a>
					
					</div>
				</div>	

				<div class="ibox float-e-margins white-bg">
					<div class="ibox-content">

						<table class="table table-bordered footable" data-sort-ignore="true" data-page-size="10">
							<thead>
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">订单号</th>
									<th data-sort-ignore="true">收货人</th>
									<th data-sort-ignore="true">电话</th>
									<th data-sort-ignore="true">地址</th>
									<th data-sort-ignore="true">订单类型</th>
									<th data-sort-ignore="true">订单金额</th>
									<th data-sort-ignore="true">征订员</td>
									<th data-sort-ignore="true">奶站</th>
									<th data-sort-ignore="true">配送员</th>
									<th data-sort-ignore="true">下单日期</th>
									<th data-sort-ignore="true">支付</th>
									<th data-sort-ignore="true">到期日期</th>
									<th data-sort-ignore="true">客户类型</th>
									<th data-sort-ignore="true">操作</th>
									<th data-sort-ignore="true">备注</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>001</td>
									<td>16062933</td>
									<td>张鑫</td>
									<td>13665498523</td>
									<td>紫竹园未来建筑3栋2单元50</td>
									<td>季单*1<br>月单*1</td>
									<td>365.2</td>
									<td>李明亮</td>
									<td>四季青奶站</td>
									<td>李明亮 13645621985</td>
									<td></td>
									<td>微信</td>
									<td>2016-6-12</td>
									<td></td>
									<td><a href="{{ url('naizhan/dingdan/luruxudan')}}">续单</a></td>
									<td></td>
								</tr>
								<tr>
									<td>001</td>
									<td>16062933</td>
									<td>张鑫</td>
									<td>13665498523</td>
									<td>紫竹园未来建筑3栋2单元50</td>
									<td>季单*1<br>月单*1</td>
									<td>365.2</td>
									<td>李明亮</td>
									<td>四季青奶站</td>
									<td>李明亮 13645621985</td>
									<td></td>
									<td>微信</td>
									<td>2016-6-22</td>
									<td></td>
									<td><a href="{{ url('naizhan/dingdan/luruxudan')}}">续单</a></td>
									<td></td>
								</tr>
								<tr>
									<td>001</td>
									<td>16062933</td>
									<td>张鑫</td>
									<td>13665498523</td>
									<td>紫竹园未来建筑3栋2单元50</td>
									<td>季单*1<br>月单*1</td>
									<td>365.2</td>
									<td>李明亮</td>
									<td>四季青奶站</td>
									<td>李明亮 13645621985</td>
									<td></td>
									<td>微信</td>
									<td>2016-6-25</td>
									<td></td>
									<td><a href="{{ url('naizhan/dingdan/luruxudan')}}">续单</a></td>
									<td></td>
								</tr>
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
		$('.btn-group').button();
		$('#data_range_select .input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true
        });
</script>
@endsection
