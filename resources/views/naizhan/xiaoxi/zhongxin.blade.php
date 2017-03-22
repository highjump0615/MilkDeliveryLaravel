@extends('naizhan.layout.master')

@section('css')
@endsection

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="white-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					消息中心
				</li>
				<li class="active">
					<strong>消息中心</strong>
				</li>
			</ol>
		</div>
		 <div class="row white-bg">
			 <div class="ibox-content white-bg">
				 <div><hr></div>
				 <div class="col-lg-12">
					 <div class="form-group col-lg-12">
						 <label class="col-md-2" style="padding-top: 5px;">阅读状态:</label>
						 &nbsp;
						 <select id="status" data-placeholder="Choose..." class="chosen-select" style="width:305px; height:35px;" tabindex="2">
							 <option value="">全部</option>
							 <option value="未读">未读</option>
							 <option value="已读">已读</option>
						 </select>
					 </div>
				 </div>
				 <div class="col-lg-12">
					 <div class="form-group col-lg-12">
						 <label class="col-md-2" style="padding-top: 5px;">消息分类:</label>
						 &nbsp;
						 <select id="type" data-placeholder="Choose..." class="chosen-select" style="width:305px; height:35px;" tabindex="2">
							 <option value="">全部</option>
							 @foreach($categories as $ca)
								 <option value="{{$ca["name"]}}">{{$ca["name"]}}</option>
							 @endforeach
						 </select>
					 </div>
				 </div>
				 <div class="col-lg-12">
					 <div class="col-lg-10" id="data_range_select">
						 <div class="col-md-2">
							 <label class="control-label" style="padding-top: 5px;">发送日期:</label>
						 </div>
						 <div class="col-md-4 input-daterange input-group" id="datepicker" style="padding-left: 45px;">
							 <input type="text" class="input-sm form-control" id="start_date" name="start" />
							 <span class="input-group-addon">至</span>
							 <input type="text" class="input-sm form-control" id="end_date" name="end"  />
						 </div>
					 </div>
					 <div class="col-lg-2"><button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button></div>
				 </div>
			 </div>
			 <div><hr></div>
			 <div class="ibox-content white-bg">
				 <div class="col-lg-2" style="padding-bottom:5px;">
					 <button class="btn-lg btn-success" id="active" type="button" style="width:100%;">标记为已读</button>
				 </div>
				 <div class="col-lg-2">
					 <button class="btn-lg btn-success" id="inactive" type="button" style="width:100%;">标记为未读</button>
				 </div>
			 </div>


			 <div class="ibox float-e-margins">
				 <div class="ibox-content">

					 <table class="table footable table-bordered" id="notification_table">
						 <thead>
						 <tr>
							 <th colspan="2">消息标题</th>
							 <th data-sort-ignore="true">发送时间</th>
							 <th data-sort-ignore="true">消息分类</th>
							 <th data-sort-ignore="true">状态</th>
						 </tr>
						 </thead>
						 <tbody>
						 @foreach($dsnotification as $dn)
							 <tr id="{{$dn->id}}" @if($dn->read == 0)style="font-weight: bold" @endif>
								 <td><input type="checkbox" class="i-checks" value="{{$dn->id}}" name="change_status"></td>
								 <td><a href={{ url('/naizhan/xiaoxi/xianqing/'.$dn->id)}}>{{$dn->title}}</a></td>
								 <td class="current_date">{{$dn->created_at}}</td>
								 <td class="type">{{$dn->category_name}}</td>
								 <td id="status{{$dn->id}}" class="status">@if($dn->read == 1) 已读 @else 未读 @endif</td>
							 </tr>
						 @endforeach
						 </tbody>
						 <tfoot align="right">
						 <tr>
							 <td colspan="7"><ul class="pagination pull-right"></ul></td>
						 </tr>
						 </tfoot>
					 </table>

					 <table class="table footable table-bordered" id="filter_table" style="display: none">
						 <thead>
						 <tr>
							 <th colspan="2">消息标题</th>
							 <th data-sort-ignore="true">发送时间</th>
							 <th data-sort-ignore="true">消息分类</th>
							 <th data-sort-ignore="true">状态</th>
						 </tr>
						 </thead>
						 <tbody>
						 </tbody>
						 <tfoot align="right">
						 <tr>
							 <td colspan="7"><ul class="pagination pull-right"></ul></td>
						 </tr>
						 </tfoot>
					 </table>
				 </div>
			 </div>
		</div>
	</div>
@endsection

@section('script')

	<script src="<?=asset('js/pages/naizhan/xiaoxizhongxin.js')?>"></script>
    <script type="text/javascript">
    	$(document).ready(function(){
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        });
		$('#data_range_select .input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true
        });
    </script>
@endsection