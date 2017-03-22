@extends('naizhan.layout.master')

@section('css')
@endsection

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li>
					<a href="">系统管理</a>
				</li>
				<li>
					<a href=""><strong>用户管理</strong></a>
				</li>
			</ol>
		</div>
			<div class="row white-bg" style="padding: 10px;">
				<input type="hidden" id="current_station_id" value="{{$current_station_id}}">
				<div class="ibox-content white-bg vertical-align">
					<a data-toggle="modal" id="btn-add" class="btn btn-success" href="#modal-form" type="button">添加账号</a>
					<div id="modal-form" class="modal fade" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
									<h4 class="modal-title">用户</h4>
								</div>
								<div class="modal-body">
									<div class="row">
										<div class="col-sm-12"><h3 class="m-t-none m-b"></h3>

											<form role="form" class="form-horizontal">
												<div class="form-group"><label class="col-sm-3">登陆名:</label>
													<div class="col-sm-9"><input type="" placeholder="" class="form-control" id="username" onKeyup="checkusername(); return false;"></div></div>
												<div class="form-group"><label class="col-sm-3"></label>
													<div class="col-sm-9"><span id="userconfirmMessage" class="userconfirmMessage"></span></div></div>
												<div class="form-group"><label class="col-sm-3">密码:</label>
													<div class="col-sm-9"><input type="password" placeholder="" class="form-control" id="password" onKeyup="clearalert();"></div></div>
												<div class="form-group"><label class="col-sm-3"></label>
													<div class="col-sm-9"><span id="passwordMessage" class="passwordMessage"></span></div></div>
												<div class="form-group"><label class="col-sm-3">确认密码:</label>
													<div class="col-sm-9"><input id="password2" type="password" onKeyup="checkpassword(); return false;" placeholder="" class="form-control"></div></div>
												<div class="form-group"><label class="col-sm-3"></label>
													<div class="col-sm-9"><span id="confirmMessage" class="confirmMessage"></span></div></div>
												<div id="permission_info">
													<div class="form-group"><label class="col-sm-3">账号状态:</label>
														<div class="col-sm-9">
															<div class="input-group">
																<select data-placeholder="Choose a Country..." class="chosen-select" style="min-width:365px; height:35px;" id="status">
																	<option value="1">启用</option>
																	<option value="0">停用</option>
																</select>
															</div>
														</div>
													</div>

													<div class="form-group"><label class="col-sm-3">角色</label>
														<div class="col-sm-9">
															<div class="input-group">
																<select data-placeholder="Choose..." class="chosen-select" style="min-width:365px; height:35px;" id="permission">
																	@foreach($role_name as $rn)
																		@if($rn->id != 200)
																			<option value="{{$rn->id}}">{{$rn->name}}</option>
																		@endif
																	@endforeach
																</select>
															</div>
														</div>
													</div>
												</div>

											</form>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-white" id="btn-save" value="add">确定</button>
									<button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
									<input type="hidden" id="user_id" name="user_id" value="0">
								</div>
							</div>

							<div class="modal-spinner-frame">
								<div class="modal-spinner">
									<div class="sk-spinner sk-spinner-circle">
										<div class="sk-circle1 sk-circle"></div>
										<div class="sk-circle2 sk-circle"></div>
										<div class="sk-circle3 sk-circle"></div>
										<div class="sk-circle4 sk-circle"></div>
										<div class="sk-circle5 sk-circle"></div>
										<div class="sk-circle6 sk-circle"></div>
										<div class="sk-circle7 sk-circle"></div>
										<div class="sk-circle8 sk-circle"></div>
										<div class="sk-circle9 sk-circle"></div>
										<div class="sk-circle10 sk-circle"></div>
										<div class="sk-circle11 sk-circle"></div>
										<div class="sk-circle12 sk-circle"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<!--User_Admin_Table-->
                <div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table class="footable table table-bordered" data-page-size="10" id="user-list">
                            <thead>
								<tr>
									<th data-sort-ignore="true">ID</th>
									<th data-sort-ignore="true">账号名称</th>
									<th data-sort-ignore="true">角色</th>
									<th data-sort-ignore="true">最后登录IP</th>
									<th data-sort-ignore="true">状态</th>
									<th data-sort-ignore="true">管理操作</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i=0 ?>
							@foreach($userinfo as $uf)
								<tr id="user{{$uf->id}}">
									<?php $i++ ?>
									<td>{{$i}}</td>
									<td>{{$uf->name}}</td>
									<td>{{$uf->current_role_name->name}}</td>
									<td>{{$uf->last_used_ip}}</td>
									<td>
										<input type="checkbox"
											   class="js-switch changeStatus"
											   @if($uf->status == 1) checked @endif
											   @if($uf->isSuperUser(\App\Model\UserModel\User::USER_BACKEND_STATION)) disabled @endif
											   value="{{$uf->id}}"/>
									</td>
									<td>
										<button class="btn btn-sm btn-success update-user" data-toggle="modal" href="#modal-form" value="{{$uf->id}}" user_role="{{$uf->user_role_id}}">修改</button>
										@if($uf->user_role_id != \App\Model\UserModel\UserRole::USERROLE_NAIZHAN_TOTAL_ADMIN)
											<button class="btn btn-sm btn-success delete-user" id="user_id{{$uf->id}}" value="{{$uf->id}}">删除</button>
										@endif
									</td>
								</tr>
							@endforeach
                            </tbody>
							<tfoot>
								<tr>
									<td colspan="6">
										<ul class="pagination hide-if-no-paging pull-right"></ul>
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
	<script src="<?=asset('js/plugins/confirm/jquery.confirm.min.js') ?>"></script>

	<!--Save & Update User Information-->
	<script src="<?=asset('js/pages/naizhan/useradminajax.js') ?>"></script>
@endsection