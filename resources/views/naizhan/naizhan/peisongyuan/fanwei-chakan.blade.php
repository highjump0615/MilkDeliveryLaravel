@extends('naizhan.layout.master')

@section('css')
	<link href="<?=asset('css/plugins/chosen/chosen.css')?>" rel="stylesheet">
@endsection

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="{{ url('naizhan/naizhan')}}">奶站管理</a>
				</li>
				<li class="active">
					<a href="{{ url('naizhan/naizhan/peisongyuan')}}">配送员管理</a>
				</li>
				<li class="active">
					<strong>配送范围-查看编辑</strong>
				</li>
			</ol>
		</div>
		
		<div class="row wrapper">

			<div class="wrapper-content">
				<div class="ibox float-e-margins">
                    <div class="ibox-content">
						<button class="btn btn-success" id="add-street" type="button"><i class="fa fa-plus"></i> 添加街道</button>
						<table id="delivery_area_table" class="footable table table-bordered" data-page-size="5">
							<thead>
							<tr>
								<th data-sort-ignore="true">街道</th>
								<th data-sort-ignore="true">配送范围</th>
								<th data-sort-ignore="true">操作</th>
							</tr>
							</thead>
							<tbody>
							<?php $i = 0; ?>
							@foreach($area_address as $street_id=>$street)
								<tr data-street-id="{{$street_id}}">
									<td>{{$street[0]}}</td>
									<td>
										@foreach($street[1] as $xiaoqu_id => $xiaoqu_name)
											{{$xiaoqu_name}}
										@endforeach
									</td>
									<td>
										<button class="btn btn-success btn-sm" data-action="modify_xiaoqu"
												data-street-id="{{$street_id}}" data-street-name="{{$street[0]}}">修改
										</button>
										<button class="btn btn-success btn-sm" data-action="delete_xiaoqu"
												data-street-id="{{$street_id}}" value="{{$street_id}}">删除
										</button>
									</td>
								</tr>
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

						<div id="change_modal_form" class="modal fade" aria-hidden="true">
							<div class="modal-dialog modal-md">
								<div class="modal-content">
									<form id="change_xiaoqu_form">
										<div class="modal-body">
											<div class="row">
												<div class="col-sm-12">
													<div class="form-group" style="padding-top: 20px;">
														<label class="col-sm-3">街道</label>
														<div class="col-sm-9">
															<input id="selected_street_to_change" name="selected_street"
																   class="form-control" value="" type="text"
																   readonly/>
															<input type="hidden" id="street_id_to_change" name="street_id_to_change"
																   value="">
														</div>
													</div>
													<div class="form-group">
														<label class="col-md-12">小区名称：</label>
														<div class="row">
															<div class="col-xs-5">
																<select name="from[]" id="js_multiselect_from_1"
																		class="js-multiselect1 form-control" size="8"
																		multiple="multiple">
																</select>
															</div>

															<div class="col-xs-2">
																<button type="button" id="js_right_All_1" class="btn btn-block"><i
																			class="glyphicon glyphicon-forward"></i></button>
																<button type="button" id="js_right_Selected_1"
																		class="btn btn-block"><i
																			class="glyphicon glyphicon-chevron-right"></i></button>
																<button type="button" id="js_left_Selected_1" class="btn btn-block">
																	<i
																			class="glyphicon glyphicon-chevron-left"></i></button>
																<button type="button" id="js_left_All_1" class="btn btn-block"><i
																			class="glyphicon glyphicon-backward"></i></button>
															</div>

															<div class="col-xs-5">
																<select name="to[]" id="js_multiselect_to_1" class="form-control"
																		size="8" multiple="multiple"></select>

																<div class="row">
																	<div class="col-sm-6">
																		<button type="button" id="multiselect_move_up" class="btn btn-block"><i class="glyphicon glyphicon-arrow-up"></i></button>
																	</div>
																	<div class="col-sm-6">
																		<button type="button" id="multiselect_move_down" class="btn btn-block col-sm-6"><i class="glyphicon glyphicon-arrow-down"></i></button>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-white"
													id="submit_change_form">确定
											</button>
											<button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
										</div>
									</form>
								</div>
							</div>
						</div>

						<div id="add_modal_form" class="modal fade" aria-hidden="true">
							<div class="modal-dialog modal-md">
								<div class="modal-content">
									<form id="add_street_form" method="post" action="{{url('/naizhan/naizhan/fanwei-chakan/street')}}">
										<input type="hidden" id="milkman_id" name="milkman_id" value="{{$milkman_id}}">
										<div class="modal-body">
											<div class="row">
												<div class="col-sm-12">
													<div class="form-group" style="padding-top: 20px;">
														<label class="col-sm-3">街道</label>
														<div class="col-sm-9">
															<select id="street-sel" name="street_id_to_add">
																@foreach($available_address as $sid=>$as)
																	<option value="{{$sid}}">{{$as[0]}}</option>
																@endforeach
															</select>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-white" onclick="submit()">确定
											</button>
											<button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
										</div>
									</form>
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
	<script src="<?=asset('js/plugins/multiselect/multiselect.min.js') ?>"></script>
	<script src="<?=asset('js/pages/naizhan/peisongyuan_area_change.js')?>"></script>

	<script>
		//Availabe Address for this station
		<?php
        $avail = json_encode($available_address);
        echo "var avail_obj = ". $avail . ";\n";

        $used = json_encode($area_address);
        echo "var used_obj = ". $used . ";\n";
        ?>

	</script>
@endsection