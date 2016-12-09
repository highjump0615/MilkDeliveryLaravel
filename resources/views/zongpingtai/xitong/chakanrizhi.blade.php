@extends('zongpingtai.layout.master')

@section('content')
	@include('zongpingtai.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('zongpingtai.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="{{ url('zongpingtai/xitong')}}">统计分析</a>
				</li>
				<li class="active">
					<strong>查看日志</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
                <div class="ibox-content">
                    <form method="get" action="{{url('/zongpingtai/xitong/chakanrizhi')}}">
                        <div class="feed-element col-md-4">
                            <label class="col-md-3 control-label text-right">登录ID:</label>
                            <div class="col-md-8">
                               <input type="text" class="input-md form-control" name="username" value="{{$username}}"/>
                            </div>
                        </div>
                        <div class="feed-element col-md-4">
                            <label class="col-md-6 control-label text-right">操作日期:</label>
                            <div class="input-group date col-md-6">
                                <input type="text" class="form-control" name="date" value="{{$date}}">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="feed-element col-md-4 text-right">
                            <button class="btn btn-success btn-outline" type="submit" >筛选</button>
                            <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                        </div>
                    </form>
                </div>

                <div class="tabs-container">

                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active"  style="border-color:#fff;">
                        <div class="panel-body">
                           <table id="table_syslog" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th data-sort-ignore="true">序号</th>
                                        <th data-sort-ignore="true">登录ID</th>
                                        <th data-sort-ignore="true">角色权限</th>
                                        <th data-sort-ignore="true">管理端</th>
                                        <th data-sort-ignore="true">IP地址</th>
                                        <th data-sort-ignore="true">页面</th>
                                        <th data-sort-ignore="true">操作</th>
                                        <th data-sort-ignore="true">时间</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if ($count > 0)
                                    <?php $i = 1; ?>
                                    @foreach($logdata as $data)
                                    <tr>
                                        <!-- 序号 -->
                                        <td>{{$i}}</td>
                                        <!-- 用户名 -->
                                        <td>{{$data->user->name}}</td>
                                        <!-- 角色权限 -->
                                        <td>{{$data->user->role->name}}</td>
                                        <!-- 管理端 -->
                                        <td>{{$data->user->getBackendTypeName()}}</td>
                                        <!-- IP地址 -->
                                        <td>{{$data->ipaddress}}</td>
                                        <!-- 页面 -->
                                        <td>{{$data->page}}</td>
                                        <!-- 操作 -->
                                        <td>{{$data->getOperationName()}}</td>
                                        <!-- 时间 -->
                                        <td>{{$data->created_at}}</td>
                                    </tr>
                                    <?php $i++; ?>
                                    @endforeach
                                @else
                                    <tr>
                                       <td colspan="9">无系统记录</td>
                                    </tr>
                                @endif
                                </tbody>
                             </table>

                            <ul id="pagination_data" class="pagination-sm pull-right"></ul>

                        </div>
                    </div>
                </div>
            </div>

		</div>

	</div>
@endsection

@section('script')
    <script type="text/javascript">
        // 全局变量
        var gnTotalPage = '{{$total_page}}';
        var gnCurrentPage = '{{$page}}';

        gnTotalPage = parseInt(gnTotalPage);
        gnCurrentPage = parseInt(gnCurrentPage);

    </script>

     <script type="text/javascript" src="<?=asset('js/plugins/pagination/jquery.twbsPagination.min.js')?>"></script>
     <script type="text/javascript" src="<?=asset('js/pages/zongpingtai/syslog.js')?>"></script>

@endsection