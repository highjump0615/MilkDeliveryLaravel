@extends('zongpingtai.layout.master')

@section('content')
	@include('zongpingtai.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('zongpingtai.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="{{ url('zongpingtai/yonghu')}}">用户管理</a>
				</li>
				<li class="active">
					<strong>管理员中心</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				<div class="ibox-content vertical-align">
					<div class="col-lg-3">
						<div class="col-lg-6">
							<a id="btn-add" href="#modal-form" data-toggle="modal" class="btn btn-success btn-outline" type="button" style="width:100%;">添加管理员</a>
						</div>
						<div class="col-lg-6">
							<a  href="{{ url('/zongpingtai/yonghu/juese')}}" class="btn btn-success btn-outline" type="button" style="width:100%;">角色添加</a> 
						</div>
					</div>
				</div>

				<div><hr></div>

				<!--User_Admin_Table-->
                <div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table class="footable table table-bordered" data-page-size="10" id="user-list">
                            <thead>
								<tr>
									<th>ID</th>
									<th>用户名称</th>
									<th>角色</th>
									<th>状态</th>
									<th>注册时间</th>
									<th>操作</th>
								</tr>
                            </thead>
							<tbody>
							<?php $i=0
							?>
							@foreach($userinfo as $uf)
								<tr id="user{{$uf->id}}">
									<?php $i++
									?>
									<td>{{$i}}</td>
									<td>{{$uf->name}}</td>
									<td>{{$uf->current_role_name->name}}</td>
									<td>@if($uf->status == 1) <i class="fa fa-check"></i> @else <i class="fa fa-times"></i> @endif</td>
									<td>{{$uf->login_time}}</td>
									<td>
										@if($uf->user_role_id == 100)
											<button type="button" data-toggle="modal" class="btn btn-success update-user" href="#modal-form" value="{{$uf->id}}" user_role="{{$uf->user_role_id}}">编辑</button>
										@else
											<button type="button" data-toggle="modal" class="btn btn-success update-user" href="#modal-form" value="{{$uf->id}}" user_role="{{$uf->user_role_id}}">编辑</button>

											<a type="button" class="btn btn-success" href="{{ url('/zongpingtai/yonghu/juese/'.$uf->user_role_id)}}" value="{{$uf->id}}">查看操作权限</a>
											@if($uf->status == 1)
												<button class="btn btn-success stop-user" value="{{$uf->id}}">禁止用户</button>
											@else
												<button class="btn btn-success start-user" value="{{$uf->id}}">允许用户</button>
											@endif
											<button class="btn btn-success delete-user" value="{{$uf->id}}">删除用户</button>
										@endif
									</td>
								</tr>
								@endforeach
                            </tbody>
							<tfoot align="right">
							<tr>
								<td colspan="7">
									<ul class="pagination hide-if-no-paging pull-right"></ul>
								</td>
							</tr>
							</tfoot>
                        </table>
                    </div>
                </div>
				<input type="hidden" id="current_id" value="">
				<div id="modal-form" class="modal fade" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<h4 class="modal-title">添加用户</h4>
							</div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12"><h3 class="m-t-none m-b"></h3>

                                        <form role="form" class="form-horizontal">
                                            <div class="form-group"><label class="col-sm-3">用户各称:</label> 
											<div class="col-sm-9"><input type="text" placeholder="" class="form-control" id="username" onkeyup="hide_user_alert();"></div></div>
											<div class="form-group"><label class="col-sm-3"></label>
												<div class="col-sm-9"><span id="userconfirmMessage" class="userconfirmMessage"></span></div></div>
                                            <div class="form-group"><label class="col-sm-3">密码:</label> 
											<div class="col-sm-9"><input id="pass1" type="password" placeholder="" class="form-control" onkeyup="hide_password_alert();"></div></div>
											<div class="form-group"><label class="col-sm-3"></label>
												<div class="col-sm-9"><span id="passwordMessage" class="passwordMessage"></span></div></div>
											<div class="form-group"><label class="col-sm-3">确认密码:</label>
											<div class="col-sm-9"><input id="pass2" type="password" onKeyup="checkpassword(); return false;" placeholder="" class="form-control"></div></div>
											<div class="form-group"><label class="col-sm-3"></label>
												<div class="col-sm-9"><span id="confirmMessage" class="confirmMessage"></span></div></div>
											<input type="hidden" id="backend_type" value="1">
											<div id="permission_info">
												<div class="form-group"><label class="col-sm-3">用户角色:</label>
													<div class="col-sm-9">
														<div class="input-group">
														<select data-placeholder="Choose..." class="chosen-select" id="permission" style="min-width:365px; height:35px;" tabindex="2">
															@foreach($role_name as $rn)
																@if($rn->id != 100)
																<option value="{{$rn->id}}">{{$rn->name}}</option>
																@endif
															@endforeach
														</select>
														</div>
													</div>
												</div>
												<div class="form-group"><label class="col-sm-3">用户状态:</label>
													<div class="col-sm-9">
														<div class="input-group">
															<select data-placeholder="Choose..." id="status" class="chosen-select" style="min-width:365px; height:35px;" tabindex="2">
																<option value="1">启用</option>
																<option value="0">关闭</option>
															</select>
														</div>
													</div>
												</div>
											</div>
											<div class="form-group"><label class="col-sm-3">备注说明:</label>
											<div class="col-sm-9"><input type="text" id="description" placeholder="" class="form-control"></div></div>
                                        </form>
                                    </div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-white" id="btn-save" value="add">确定</button>
                                <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
							</div>
                        </div>
					</div>
				</div>

			</div>
		</div>
		
	</div>
@endsection

@section('script')
	<!--Save & Update User Information-->
	<script src="<?=asset('js/ajax/zongpingtai_useradminajax.js') ?>"></script>
@endsection
