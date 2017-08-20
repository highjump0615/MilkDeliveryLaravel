@extends('gongchang.layout.master')
@section('css')
@endsection
@section('content')
	@include('gongchang.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('gongchang.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="">消息中心</a>
				</li>
			</ol>
		</div>

		<div class="row white-bg">
			<div class="ibox-content white-bg">
				<div><hr></div>

                <!-- 筛选选择项 -->
                @include('gongchang.xinxi.filter')
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
						 @foreach($notifications as $dn)
							 <tr id="{{$dn->id}}" @if($dn->read == 0) style="font-weight: bold" @endif>
								 <td>
                                     <input type="checkbox" class="i-checks" value="{{$dn->id}}" name="change_status">
                                 </td>
                                 <!-- 消息标题 -->
								 <td>
                                     <a href={{ url('/gongchang/xinxi/xiangxi/'.$dn->id)}}>{{$dn->title}}</a>
                                 </td>
                                 <!-- 时间 -->
								 <td class="current_date">
                                     {{$dn->created_at}}
                                 </td>
                                 <!-- 消息分类 -->
								 <td class="type">
                                     {{\App\Model\NotificationModel\FactoryNotification::getCategoryName($dn->category)}}
                                 </td>
                                 <!-- 状态 -->
								 <td id="status{{$dn->id}}" class="status">
                                     @if($dn->read == 1) 已读 @else 未读 @endif
                                 </td>
							 </tr>
						 @endforeach
						 </tbody>
					 </table>

					 <ul id="pagination_data" class="pagination-sm pull-right"></ul>

				 </div>
			 </div>
		</div>
	</div>
@endsection

@section('script')

	<script src="<?=asset('js/pages/gongchang/xiaoxizhongxin.js')?>"></script>

    <script type="text/javascript">
        // 全局变量
        var gnTotalPage = '{{$notifications->lastPage()}}';
        var gnCurrentPage = '{{$notifications->currentPage()}}';

        gnTotalPage = parseInt(gnTotalPage);
        gnCurrentPage = parseInt(gnCurrentPage);

		$(document).ready(function(){
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green'
            });
        });
		$('#data_range_select .input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true
        });
    </script>

	<script type="text/javascript" src="<?=asset('js/plugins/pagination/jquery.twbsPagination.min.js')?>"></script>
	<script type="text/javascript" src="<?=asset('js/pages/gongchang/pagination.js')?>"></script>

@endsection