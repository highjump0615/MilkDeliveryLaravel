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
                                <label class="col-md-1 text-right">登录ID:</label>
                                <div class="col-md-2">
                                   <input id="filter_start_date" type="text" class="input-md form-control" name="start"/>
                                </div>
                                <div class="feed-element form-group" id="data_range_select">
                                    <label class="col-md-2 control-label">下单日期:</label>
                                    <div class="input-daterange input-group col-md-3" id="datepicker">
                                        <input type="text" id="filter_order_start_date" class="input-sm form-control" name="start"/>
                                    </div>
                                </div>
                                <div class="col-md-4" style="float:right;margin-top: -40px;">
                                    <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
                                    <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                                </div>
                            </div>
                        <div class="tabs-container">

                        <div class="tab-content">
                            <div id="tab-1" class="tab-pane active"  style="border-color:#fff;">
                                <div class="panel-body">
                                   <table id="order_table" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th data-sort-ignore="true">序号</th>
                                                <th data-sort-ignore="true">登录ID</th>
                                                <th data-sort-ignore="true">角色权限</th>
                                                <th data-sort-ignore="true">管理端</th>
                                                <th data-sort-ignore="true">IP地址</th>
                                                <th data-sort-ignore="true">模块</th>
                                                <th data-sort-ignore="true">页面</th>
                                                <th data-sort-ignore="true">操作</th>
                                                <th data-sort-ignore="true">时间</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                               <td>1</td>
                                               <td>sanyuan</td>
                                               <td>客服</td>
                                               <td>总平台</td>
                                               <td>185.15.123</td>
                                               <td>订单管理</td>
                                               <td>未生成</td>
                                               <td>导出</td>
                                               <td>2016-11-11 14:45:55</td>
                                            </tr>
                                            <tr>
                                               <td>1</td>
                                               <td>sanyuan</td>
                                               <td>客服</td>
                                               <td>总平台</td>
                                               <td>185.15.123</td>
                                               <td>订单管理</td>
                                               <td>未生成</td>
                                               <td>导出</td>
                                               <td>2016-11-11 14:45:55</td>
                                            </tr>
                                            <tr>
                                               <td>1</td>
                                               <td>sanyuan</td>
                                               <td>客服</td>
                                               <td>总平台</td>
                                               <td>185.15.123</td>
                                               <td>订单管理</td>
                                               <td>未生成</td>
                                               <td>导出</td>
                                               <td>2016-11-11 14:45:55</td>
                                            </tr>
                                            <tr>
                                               <td>1</td>
                                               <td>sanyuan</td>
                                               <td>客服</td>
                                               <td>总平台</td>
                                               <td>185.15.123</td>
                                               <td>订单管理</td>
                                               <td>未生成</td>
                                               <td>导出</td>
                                               <td>2016-11-11 14:45:55</td>
                                            </tr>
                                            <tr>
                                               <td>1</td>
                                               <td>sanyuan</td>
                                               <td>客服</td>
                                               <td>总平台</td>
                                               <td>185.15.123</td>
                                               <td>订单管理</td>
                                               <td>未生成</td>
                                               <td>导出</td>
                                               <td>2016-11-11 14:45:55</td>
                                            </tr>
                                            <tr>
                                               <td>1</td>
                                               <td>sanyuan</td>
                                               <td>客服</td>
                                               <td>总平台</td>
                                               <td>185.15.123</td>
                                               <td>订单管理</td>
                                               <td>未生成</td>
                                               <td>导出</td>
                                               <td>2016-11-11 14:45:55</td>
                                            </tr>
                                        </tbody>
                                     </table>
                                        <ul class="pagination">
                                            <li><a target="_blank" href="https://www.baidu.com/">&laquo;</a></li>
                                            <li><a target="_blank" href="https://www.baidu.com/">1</a></li>
                                            <li><a target="_blank" href="https://www.baidu.com/">2</a></li>
                                            <li><a target="_blank" href="https://www.baidu.com/">3</a></li>
                                            <li><a target="_blank" href="https://www.baidu.com/">4</a></li>
                                            <li><a target="_blank" href="https://www.baidu.com/">5</a></li>
                                            <li><a target="_blank" href="https://www.baidu.com/">&raquo;</a></li>
                                        </ul>

                                </div>
                            </div>
                        </div>
                    </div>
                    
			</div>
		</div>
		
	</div>
@endsection
@section('script')
    <script src="<?=asset('js/pages/naizhan/order_select_export_print.js') ?>"></script>
@endsection
