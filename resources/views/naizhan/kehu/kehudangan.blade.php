@extends('naizhan.layout.master')

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="">客户管理</a>
				</li>
				<li class="active">
					<strong>客户档案</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				<div class="ibox-content">
					<div class="feed-element">
						<div class="col-md-3">
							<label>用户名:</label>
							<input type="text" id="user_name">
						</div>
						<div class="col-md-3">
							<label>手机号:</label>
							<input type="text" id="phone_number">
						</div>
						<div class="col-md-3">
							<label>区域:</label>
							<input type="text" id="area_address">
						</div>
						<div class="col-md-3">
							<button type="button" class="btn btn-success btn-m-d" data-action="show_selected">筛选</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="export_csv">导出</button>
							&nbsp;
							<button class="btn btn-success btn-outline btn-m-d" data-action="print">打印</button>
						</div>
					</div>
				</div>
				
				<div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="customerTable" class="table footable table-bordered" data-page-size="15">
                            <thead style="background-color:#33cccc;">
								<tr>
									<th data-sort-ignore="true">序号</th>
									<th data-sort-ignore="true">区域</th>
									<th data-sort-ignore="true">分区</th>
									<th data-sort-ignore="true">收货人</th>
									<th data-sort-ignore="true">联系电话</th>
									<th data-sort-ignore="true">地址</th>
									{{--<th data-sort-ignore="true">奶站</th>--}}
									<th data-sort-ignore="true">配送员</th>
									<th data-sort-ignore="true">订单状态</th>
									<th data-sort-ignore="true">下单次数</th>
									<th data-sort-ignore="true">订单余额</th>
									<th data-sort-ignore="true">账户余额</th>
									{{--<th data-sort-ignore="true">操作</th>--}}
									<th data-sort-ignore="true">备注</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i=0; ?>
							@foreach($customers as $cu)
								<?php $i++; ?>
								<tr>
									<td>{{$i}}</td>
									<td class="area">{{$cu->area_addr}}</td>
									<td>{{$cu->sector_addr}}</td>
									<td class="user">{{$cu->name}}</td>
									<td class="phone">{{$cu->phone}}</td>
									<td>{{$cu->detail_addr}}</td>
									{{--<td>{{$cu->station_name}}</td>--}}
									<td>{{$cu->milkman_name}}</td>
									<td>{{$cu->order_status}}</td>
									<td>{{$cu->order_count}}</td>
									<td>{{$cu->order_balance}}</td>
									<td>{{$cu->remain_amount}}</td>
									{{--<td><a data-toggle="modal" href="#modal-form"><i class="fa fa-pencil"></i></a></td>--}}
									<td></td>
								</tr>
							@endforeach
                            </tbody>
                            <tfoot>
                            	<tr>
                            		<td colspan="100%"><ul class="pagination pull-right"></ul></td>
                            	</tr>
                            </tfoot>
                        </table>

						<table id="filteredTable" class="table footable table-bordered" data-page-size="15" style="display: none">
							<thead style="background-color:#33cccc;">
							<tr>
								<th data-sort-ignore="true">序号</th>
								<th data-sort-ignore="true">区域</th>
								<th data-sort-ignore="true">分区</th>
								<th data-sort-ignore="true">收货人</th>
								<th data-sort-ignore="true">联系电话</th>
								<th data-sort-ignore="true">地址</th>
								{{--<th data-sort-ignore="true">奶站</th>--}}
								<th data-sort-ignore="true">配送员</th>
								<th data-sort-ignore="true">订单状态</th>
								<th data-sort-ignore="true">下单次数</th>
								<th data-sort-ignore="true">订单余额</th>
								<th data-sort-ignore="true">账户余额</th>
								{{--<th data-sort-ignore="true">操作</th>--}}
								<th data-sort-ignore="true">备注</th>
							</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
							<tr>
								<td colspan="100%"><ul class="pagination pull-right"></ul></td>
							</tr>
							</tfoot>
						</table>
                    </div>
					<div id="modal-form" class="modal fade" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-12"><h3 class="m-t-none m-b"></h3>
                                            <form role="form" class="form-horizontal">
                                                <div class="form-group"><label class="col-sm-3">收货人：</label>
												<div class="col-sm-9"><input type="" placeholder="" class="form-control" value="李先生"></div></div>
                                                <div class="form-group"><label class="col-sm-3">电话：</label>
												<div class="col-sm-9"><input type="" placeholder="" class="form-control" value="15636548923"></div></div>
												<div class="form-group"><label class="col-sm-3">地址：</label>
													<div class="col-sm-9">
														<div class="input-group">
															<select data-placeholder="Choose..." class="chosen-select" style="height:35px;"tabindex="2">
																<option value="北京">北京</option>
																<option value="河北省">河北省</option>
															</select>
															&nbsp;
															<select data-placeholder="Choose..." class="chosen-select" style="height:35px;"tabindex="2">
																<option value="北京">北京</option>
																<option value="承德市">承德市</option>
																<option value="石家庄市">石家庄市</option>
															</select>
															&nbsp;
															<select data-placeholder="Choose..." class="chosen-select" style="height:35px;"tabindex="2">
																<option value="东城区">东城区</option>
																<option value="西城区">西城区</option>
															</select>
															&nbsp;
															<select data-placeholder="Choose..." class="chosen-select" style="height:35px;"tabindex="2">
																<option value="鼓楼东大街">鼓楼东大街</option>
																<option value="鼓楼西大街">鼓楼西大街</option>
																<option value="东四大街">东四大街</option>
															</select>
														</div>
													</div>
												</div>
												<div class="form-group"><div class="col-sm-3"></div>
												<div class="col-sm-9"><input type="" placeholder="" class="form-control" value="职教公寓08-3-603"></div></div>
												<div class="form-group"><label class="col-sm-3">配送站：</label>
													<div class="col-sm-9">
														<div class="input-group">
														<select data-placeholder="Choose..." class="chosen-select" style="min-width:395px; height:35px;" tabindex="2">
														<option value="四季青">四季青</option>
														<option value="天通苑">天通苑</option>
														<option value="上地">上地</option>
														</select>
														</div>
													</div>
												</div>
												<div class="form-group"><label class="col-sm-3">配送员：</label>
												<div class="col-sm-9"><input type="" placeholder="" class="form-control" value="张明敏   13678965423"></div></div>
                                            </form>
                                        </div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-white" data-dismiss="modal">确定</button>
                                    <button type="button" class="btn btn-white">取消</button>
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
	<script type="text/javascript" src="<?=asset('js/pages/naizhan/kehu_admin.js')?>"></script>
@endsection