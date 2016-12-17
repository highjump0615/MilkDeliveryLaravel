@extends('gongchang.layout.master')

@section('content')
	@include('gongchang.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('gongchang.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li>
					<a href="">基础信息管理</a>
				</li>
				<li>
					<a href=""><strong>征订员管理</strong></a>
				</li>
			</ol>
		</div>
		
		<div class="row white-bg">
			<!--Table-->
			<div class="ibox-content">
				<div class="feed-element">
					<div class="col-md-3">
						<label>征订员:</label>
						<input type="text" id="filter_name" class="form-control" style="width: 70%; display: inline">
					</div>
					<div class="col-md-3">
						<label>编号:</label>
						<input type="text" id="filter_number" class="form-control" style="width: 70%; display: inline">
					</div>
					<div class="col-md-3">
						<label>所属:</label>
						&nbsp;
						<select data-placeholder="" class="form-control" id="filter_station" style="width: 70%; display: inline">
							<option value="奶厂">奶厂</option>
							@foreach($stations as $s)
								<option value="{{$s->name}}">{{$s->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-3"  style="padding-top:5px;">
						<button type="button" class="btn btn-success btn-md" data-action="show_selected">筛选</button>
						&nbsp;&nbsp;
						<button type="button" class="btn btn-success btn-md btn-outline" data-action="print">打印</button>
					</div>
				</div>
				<div><hr></div>
				@if (count($errors) > 0)
					<div class="row">
						<div class="form-group">
							<div class="col-sm-10 col-sm-offset-1">
								<div class="alert alert-danger">
									<ul>
										@foreach ($errors->all() as $error)
											<li>{{ $error }}</li>
										@endforeach
									</ul>
								</div>
							</div>
						</div>
					</div>
				@endif
				@if (session('status'))
					<div class="row">
						<div class="form-group">
							<div class="col-sm-10 col-sm-offset-1">
								<div class="alert alert-success">
									{{ session('status') }}
								</div>
							</div>
						</div>
					</div>
				@endif

				<form method="post" action="{{url('/gongchang/jichuxinxi/zhengdingyuan')}}">
				<div class="feed-element">
					<div class="col-md-3">
						<label>姓名: &nbsp;</label>
						<input type="text" name="name" class="form-control" style="width: 70%; display: inline">
					</div>
				</div>
				<div class="feed-element">
					<div class="col-md-3">
						<label>电话: &nbsp;</label>
						<input type="text" id="phone" name="phone" class="form-control" style="width: 70%; display: inline">
					</div>
				</div>
				<div class="feed-element">
					<div class="col-md-3">
						<label>所属:</label>
						&nbsp;
						<select data-placeholder="" class="chosen-select form-control" name="station" style="width: 70%; display: inline">
							<option value="-1">奶厂</option>
							@foreach($stations as $s)
								<option value="{{$s->id}}">{{$s->name}}</option>
							@endforeach
						</select>
					</div>
					<button class="btn btn-success btn-md" type="submit"><i class="fa fa-plus"></i> 保存并添加</button>
				</div>
				</form>
			</div>

			<div class="ibox float-e-margins">
				<div class="ibox-content">

					<table id="origin_table" class="footable table table-bordered" data-page-size="10">
						<thead>
						<tr>
							<th data-sort-ignore="true">姓名</th>
							<th data-sort-ignore="true">编号</th>
							<th data-sort-ignore="true">电话</th>
							<th data-sort-ignore="true">所属</th>
							<th data-sort-ignore="true">操作</th>
						</tr>
						</thead>
						<tbody>
						@foreach($checkers as $c)
							<tr id="checker{{$c->id}}">
								<td class="o_checker_name">{{$c->name}}</td>
								<td class="o_checker_number">{{$c->number}}</td>
								<td class="o_checker_phone">{{$c->phone}}</td>
								<td class="o_checker_station">{{$c->DanweiName}}</td>
								<td>
									<button class="btn btn-sm btn-success update-checker" value="{{$c->id}}">修改</button>
									&nbsp;
									<button class="btn btn-sm btn-success delete-checker" value="{{$c->id}}">删除</button>
								</td>
							</tr>
						@endforeach
						</tbody>
						<tfoot>
						<tr>
							<td colspan="5">
								<ul class="pagination pull-right"></ul>
							</td>
						</tr>
						</tfoot>
					</table>


					<table id="checker_filter_table" class="footable table table-bordered" data-page-size="10" style="display:none;">
						<thead>
						<tr>
							<th data-sort-ignore="true">姓名</th>
							<th data-sort-ignore="true">编号</th>
							<th data-sort-ignore="true">电话</th>
							<th data-sort-ignore="true">所属</th>
							<th data-sort-ignore="true">操作</th>
						</tr>
						</thead>
						<tbody>

						</tbody>
						<tfoot>
						<tr>
							<td colspan="5">
								<ul class="pagination pull-right"></ul>
							</td>
						</tr>
						</tfoot>
					</table>
				</div>

				<div id="checker_modal" class="modal fade" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<form role="form" id="post_update_checker" class="form-horizontal">
								<div class="modal-body">
									<div class="row">
										<div class="col-sm-12"><h3 class="m-t-none m-b"></h3>
											<div class="feed-element">
												<label class="col-lg-3">姓名:</label>
												<div class="col-lg-9">
													<input type="text" placeholder="" class="form-control" id="checker_name" value="">
												</div>
											</div>
											<div class="feed-element">
												<label class="col-lg-3">编号:</label>
												<div class="col-lg-9">
													<input type="text" placeholder="" class="form-control" id="checker_number" value="">
												</div>
											</div>
											<div class="feed-element">
												<label class="col-lg-3">电话:</label>
												<div class="col-lg-9">
													<input type="text" placeholder="" name="phone" class="form-control" id="checker_phone">
												</div>
											</div>
											<div class="feed-element">
												<label class="col-lg-3">所属:</label>
												<div class="col-lg-9">
													<select data-placeholder="" class="chosen-select form-control" id="checker_station" tabindex="2">
														<option value="-1">奶厂</option>
														@foreach($stations as $s)
															<option value="{{$s->id}}">{{$s->name}}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-white" >确定</button>
									<button type="button" class="btn btn-white" id="but_cancel" data-dismiss="modal">取消</button>
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
	<script src="<?=asset('js/pages/gongchang/zhengdingyuan.js')?>"></script>
@endsection