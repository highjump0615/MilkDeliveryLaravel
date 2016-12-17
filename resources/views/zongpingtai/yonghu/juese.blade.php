@extends('zongpingtai.layout.master')
@section('css')
	<link href="<?=asset('css/plugins/iCheck/custom.css') ?>" rel="stylesheet">
	<link href="<?=asset('css/plugins/added/build.css') ?>" rel="stylesheet">

	<style>
		/*.treetable-expanded > td:first-child,*/
		/*.treetable-collapsed > td:first-child {*/
			/*padding-left: 2em;*/
		/*}*/

		/*.treetable-expanded > td:first-child > .treetable-expander,*/
		/*.treetable-collapsed > td:first-child > .treetable-expander {*/
			/*top: 0.05em;*/
			/*position: relative;*/
			/*margin-left: -1.5em;*/
			/*margin-right: 0.25em;*/
		/*}*/

		/*.treetable-expanded .treetable-expander,*/
		/*.treetable-expanded .treetable-expander {*/
			/*width: 1em;*/
			/*height: 1em;*/
			/*cursor: pointer;*/
			/*position: relative;*/
			/*display: inline-block;*/
		/*}*/

		/*.treetable-depth-1 > td:first-child {*/
			/*padding-left: 3em;*/
		/*}*/

		/*.treetable-depth-2 > td:first-child {*/
			/*padding-left: 4.5em;*/
		/*}*/

		/*.treetable-depth-3 > td:first-child {*/
			/*padding-left: 6em;*/
		/*}*/

		table, th, td {
			text-align: left;
		}

		tr {
			cursor: default;
		}

		.btn span.glyphicon {
			opacity: 0;
		}
		.btn.active span.glyphicon {
			opacity: 1;
		}
	</style>
@endsection
@section('content')
	@include('zongpingtai.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('zongpingtai.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a>用户管理</a>
				</li>
				<li class="active">
					<strong>角色管理</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<input type="hidden" id="#backend_type" value="1">
			<div class="wrapper-content">
				<div class="ibox col-md-offset-1 col-md-3"  style="padding-top: 30px;">
					<a data-toggle="modal" href="#myModal" class="btn btn-success dim" type="button"><i class="fa fa-plus"></i> 添加角色</a>
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<h5>角色列表</h5>
							<div class="ibox-tools">
								<a class="collapse-link">
									<i class="fa fa-chevron-up"></i>
								</a>
							</div>
						</div>
						<div class="ibox-content">
							<input type="hidden" id="user_role_id" value="{{$role_id}}">
							<table class="table table-bordered table-hover " id="roles-list" >
								<tbody>
								@foreach($role_name as $rn)
									<tr id="role{{$rn->id}}" class="clickable-row gradeX @if ($role_id == $rn->id) active @endif" idnumber="{{$rn->id}}" style="height: 50px;">
										<td>{{$rn->name}}</td>
										@if($rn->id == 100)
											<td class="center">不可删</td>
										@else
											<td class="center"><button class="btn btn-md btn-success delete-role" id="role{{$rn->id}}" value="{{$rn->id}}">删除</button></td>
										@endif
									</tr>
								@endforeach
								</tbody>
								<tfoot>
								</tfoot>
							</table>
							<div class="col-sm-9"><span id="alertMessage" class="alertMessage"></span></div>
						</div>
					</div>
				</div>

				<div class="col-md-5" style="padding-top: 65px;">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<h5>权限列表</h5>
							<div class="ibox-tools">
								<a class="collapse-link">
									<i class="fa fa-chevron-up"></i>
								</a>
							</div>
						</div>

						<form action="{{ url('api/zongpingtai/yonghu/juese/store/')}}" role="form" method="post">
							{{ csrf_field() }}
						<div class="ibox-content">
							<table class="table tree table-inverse" id="permissionTable">
								<tbody>
								<input type="hidden" name="roleId" value="{{$role_id}}">

								{{--<tr data-node="treetable-650__1" data-pnode="">--}}
									{{--<td width="30%" class="average-score sm-text color-gray"><input type="checkbox"--}}
																									{{--class="i-checks"> 全选--}}
									{{--</td>--}}
									{{--<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 查看--}}
									{{--</td>--}}
									{{--<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 修改--}}
									{{--</td>--}}
									{{--<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 新增--}}
									{{--</td>--}}
									{{--<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 删除--}}
									{{--</td>--}}
									{{--<td class="average-score sm-text color-gray"></td>--}}
									{{--<td class="average-score sm-text color-gray"><input type="checkbox" class="i-checks"> 全选--}}
									{{--</td>--}}
								{{--</tr>--}}
								<?php $i = 0;?>
								@foreach($pages as $p)
									<?php $i++;	?>
									<tr class="gray-bg">
										<td colspan="7" class="competency sm-text">
											<div class="checkbox checkbox-primary">
											<input type="checkbox" class="" id="parenticheck{{$i}}" name="input{{$p->id}}"
													   @foreach($access_pages as $ap) @if($ap->page_id == $p->id) Checked @endif @endforeach>
											<label for="parenticheck{{$i}}">{{$p->name}}</label>
											</div>
										</td>
									</tr>
									@foreach($p->sub_pages as $s)
										<tr>
											<td width="30%" class="average-score sm-text color-gray">
												<div class="checkbox checkbox-primary">
												&emsp;<input type="checkbox" id="child{{$s->id}}" class="childicheck{{$i}}" name="input{{$s->id}}"@foreach($access_pages as $ap) @if($ap->page_id == $s->id) Checked @endif @endforeach>
												<label for="child{{$s->id}}">{{$s->name}}
												</label>
												</div>
											{{--</td>--}}
											{{--<td class="average-score sm-text color-gray"><input type="checkbox"--}}
																								{{--class="i-checks"> 查看--}}
											{{--</td>--}}
											{{--<td class="average-score sm-text color-gray"><input type="checkbox"--}}
																								{{--class="i-checks"> 修改--}}
											{{--</td>--}}
											{{--<td class="average-score sm-text color-gray"><input type="checkbox"--}}
																								{{--class="i-checks"> 新增--}}
											{{--</td>--}}
											{{--<td class="average-score sm-text color-gray"><input type="checkbox"--}}
																								{{--class="i-checks"> 删除--}}
											{{--</td>--}}
											{{--<td class="average-score sm-text color-gray"></td>--}}
											{{--<td class="average-score sm-text color-gray"><input type="checkbox"--}}
																								{{--class="i-checks"> 全选--}}
											</td>
										</tr>
									@endforeach
								@endforeach
								</tbody>
							</table>
							<input type="hidden" id="permission_count" value="{{$i+1}}">
							<div class="col-md-offset-5 col-md-2">
								<button type="submit"  id="save_change" class="btn btn-md btn-success" @if($role_id == 100) style="display: none" @endif>保存</button>
							</div>
						</div>
						</form>
					</div>
				</div>

				<!--Add Rolename Modalbox-->
				<div id="myModal" class="modal fade" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<form id="frmroles" name="frmroles">
								<div class="modal-body">
									<div class="row">
										&nbsp;
										<div class="col-lg-12">
											<label class="col-lg-3" style="padding-top: 5px;">角色名称 :</label>

											<div class="col-lg-9">
												<input class="form-control" type="text" style="width:100%;" id="role" required name="role_name">
												<input type="hidden" style="width:100%;" id="type" required name="backend_type" value="1">
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-white" id="btn-save" value="add" data-dismiss="modal">确定</button>
									<button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
									<input type="hidden" id="role_id" name="role_id" value="0">
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
@endsection
@section('script')
	<meta name="_token" content="{!! csrf_token() !!}" />
	<script src="<?=asset('js/plugins/iCheck/icheck.min.js') ?>"></script>

	<!--Apply Ajax for Role_name table-->
	<script type="text/javascript" src="<?=asset('js/pages/zongpingtai/jueseajax.js') ?>"></script>
	<!--Apply Ajax for Permission table-->
	<script type="text/javascript" src="<?=asset('js/pages/zongpingtai/juesepermission.js') ?>"></script>

	<script type="text/javascript" src="<?=asset('js/plugins/iCheck/icheck-new.js') ?>"></script>

	<script>

        $(document).ready(function(){
			var role_id = $('#user_role_id').val();
			$('#role'+role_id+'').addClass('active').siblings().removeClass('active');
        });

		$('#roles-list').on('click','.clickable-row',function(event){
			$(this).addClass('active').siblings().removeClass('active');
			});

		$('#parenticheck1').change(function () {
			if($(this).is(':checked')){
				$('.childicheck1').prop('checked',true);
			}
			else {
				$('.childicheck1').prop('checked',false);
			}
		});
		$('.childicheck1').change(function () {
			if($(this).is(':checked')){
				$('#parenticheck1').prop('checked',true);
			}
			else {
				var i = 0;
				$(this).parent().parent().parent().parent().find('.childicheck1').each(function () {
					if($(this).is(':checked')){
						i++;
					}
				});
				if(i==0){
					$('#parenticheck1').prop('checked',false);
				}
			}
		});

		$('#parenticheck2').change(function () {
			if($(this).is(':checked')){
				$('.childicheck2').prop('checked',true);
			}
			else {
				$('.childicheck2').prop('checked',false);
			}
		})
		$('.childicheck2').change(function () {
			if($(this).is(':checked')){
				$('#parenticheck2').prop('checked',true);
			}
			else {
				var i = 0;
				$(this).parent().parent().parent().parent().find('.childicheck2').each(function () {
					if($(this).is(':checked')){
						i++;
					}
				})
				if(i==0){
					$('#parenticheck2').prop('checked',false);
				}
			}
		})
		$('#parenticheck3').change(function () {
			if($(this).is(':checked')){
				$('.childicheck3').prop('checked',true);
			}
			else {
				$('.childicheck3').prop('checked',false);
			}
		})
		$('.childicheck3').change(function () {
			if($(this).is(':checked')){
				$('#parenticheck3').prop('checked',true);
			}
			else {
				var i = 0;
				$(this).parent().parent().parent().parent().find('.childicheck3').each(function () {
					if($(this).is(':checked')){
						i++;
					}
				})
				if(i==0){
					$('#parenticheck3').prop('checked',false);
				}
			}
		})
		$('#parenticheck4').change(function () {
			if($(this).is(':checked')){
				$('.childicheck4').prop('checked',true);
			}
			else {
				$('.childicheck4').prop('checked',false);
			}
		})
		$('.childicheck4').change(function () {
			if($(this).is(':checked')){
				$('#parenticheck4').prop('checked',true);
			}
			else {
				var i = 0;
				$(this).parent().parent().parent().parent().find('.childicheck4').each(function () {
					if($(this).is(':checked')){
						i++;
					}
				})
				if(i==0){
					$('#parenticheck4').prop('checked',false);
				}
			}
		})
		$('#parenticheck5').change(function () {
			if($(this).is(':checked')){
				$('.childicheck5').prop('checked',true);
			}
			else {
				$('.childicheck5').prop('checked',false);
			}
		})
		$('.childicheck5').change(function () {
			if($(this).is(':checked')){
				$('#parenticheck5').prop('checked',true);
			}
			else {
				var i = 0;
				$(this).parent().parent().parent().parent().find('.childicheck5').each(function () {
					if($(this).is(':checked')){
						i++;
					}
				})
				if(i==0){
					$('#parenticheck5').prop('checked',false);
				}
			}
		})
    </script>
@endsection
