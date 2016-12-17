@extends('gongchang.layout.master')
@section('css')

	<!-- Star Rating -->
    <link href="<?=asset('css/plugins/star-rating/star-rating.css') ?>" media="all" rel="stylesheet" type="text/css" />
	<link href="<?=asset('css/plugins/star-rating/themes/krajee-fa/theme.css') ?>" media="all" rel="stylesheet" type="text/css" />

    <style type="text/css">
        td {vertical-align: middle !important;}
    </style>
@endsection
@section('content')
	@include('gongchang.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		 @include('gongchang.theme.header')
		 <div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li>
					<a href="">评价管理</a>
				</li>
				<li class="active">
					<a href=""><strong>评价列表</strong></a>
				</li>
			</ol>
		 </div>
		 <div class="row border-bottom">
            <div class="col-lg-12" style="background-color:white">
                <div class="ibox float-e-margins">
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="form-group col-md-4">

                                <label class="col-sm-3 control-label">状态:</label>
                                <div class="col-sm-9" style="padding-left:0px;">
                                    <select id="status" class="form-control m-b" name="account">
                                        <option @if($status=='') selected @endif value="">全部</option>
                                        <option @if($status=='3') selected @endif value="3">通过</option>
                                        <option @if($status=='2') selected @endif value="2">屏蔽</option>
                                        <option @if($status=='1') selected @endif value="1">待审核</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-5" id="data_5">
                                <label class="col-sm-3 control-label">选择时间:</label>
                                <div class="col-sm-9 input-daterange input-group" id="datepicker">
                                    <input id="start_date" type="text" class="input-sm form-control" name="start" value="{{$start_date}}"/>
                                    <span class="input-group-addon">至</span>
                                    <input id="end_date" type="text" class="input-sm form-control" name="end" value="{{$end_date}}"/>
                                </div>
                            </div>
                            <div class="col-md-offset-2 col-md-1">
                                <button id="search" class="btn btn-success btn-md" style="width: 100%;">通过</button>
                            </div>
                        </div>
                        {{--<div class="ibox-content">--}}
                        {{--<div class="col-lg-2" style="padding-bottom:5px;">--}}
                        {{--<button class="btn-lg btn-success" type="button" style="width:100%;">标记为已读</button>--}}
                        {{--</div>--}}
                        {{--<div class="col-lg-2">--}}
                        {{--<button class="btn-lg btn-success" type="button" style="width:100%;">标记为未读</button>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        <div class="hr-line-dashed"></div>
                        <div class="table-responsive">
                            <table class="footable table table-bordered" data-page-size="10">
                                <thead>
                                <tr>
                                    {{--<th data-sort-ignore="true"></th>--}}
                                    <th data-sort-ignore="true">客户 </th>
                                    <th data-sort-ignore="true">评价内容 </th>
                                    <th data-sort-ignore="true">发送时间</th>
                                    <th data-sort-ignore="true">状态</th>
                                    <th data-sort-ignore="true">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($reviews)==0)
                                <tr>
                                <td colspan="5">没有数据!</td>
                                </tr>
                                @endif
                                @foreach($reviews as $re)
                                    <tr id="review{{$re['review_id']}}">
                                        {{--<td><input type="checkbox" class="i-checks" name="input[]"></td>--}}
                                        <td>{{$re['customer_name']}}</td>
                                        <td>
                                            <input type="text" class="kv-fa rating-loading" value="{{$re['marks']}}" data-size="xs" title="" readonly>
                                            <a href={{URL::to('/gongchang/pingjia/pingjiaxiangqing/'.$re['review_id'])}}>{{$re['substr']}}</a>
                                        </td>
                                        <td>{{$re['time']}}</td>
                                        <td>{{$re['status']}}</td>
                                        <td id="{{$re['status_number']}}">
                                            <a href={{URL::to('/gongchang/pingjia/pingjiaxiangqing/'.$re['review_id'])}} class="btn btn-success btn-sm">查看</a>
                                            @if($re['status_number'] == 1)
                                                <button class="btn btn-success btn-sm pass" value="{{$re['review_id']}}">通过</button>
                                                <button class="btn btn-success btn-sm isolate" value="{{$re['review_id']}}">屏蔽</button>
                                            @elseif($re['status_number'] == 2)
                                                <button class="btn btn-success btn-sm pass" value="{{$re['review_id']}}">通过</button>
                                                <button class="btn btn-success btn-sm remove" value="{{$re['review_id']}}">删除</button>
                                            @elseif($re['status_number'] == 3)
                                                <button class="btn btn-success btn-sm modify" value="{{$re['review_id']}}">修改</button>
                                                <button class="btn btn-success btn-sm remove" value="{{$re['review_id']}}">删除</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="6">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
         </div>
         <div id="modal-form" class="modal fade" aria-hidden="true">
             <div class="modal-dialog">
                 <div class="modal-content">
                     <div class="modal-header">
                         <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                         <h4 class="modal-title">评价修改</h4>
                     </div>
                     <div class="modal-body">
                         <div class="row">
                             <div class="col-sm-12"><h3 class="m-t-none m-b"></h3>
                                 <form role="form" class="form-horizontal">
                                     <input type="hidden" id="current_id">
                                     <div class="form-group"><label class="col-sm-3" style="padding-top: 15px;">评价修改:</label>
                                         <div id="current_rate" class="col-sm-9">
                                             {{--<input id="mark" type="text" class="kv-fa rating-loading" value="5" data-size="xs" title="">--}}
                                         </div>
                                     </div>
                                     <label>评价内容:</label>
                                     <textarea id="content" name="textarea" rows="10" cols="30"></textarea>
                                 </form>
                             </div>
                         </div>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-white" id="save" value="add">确定</button>
                         <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                     </div>
                 </div>
             </div>
         </div>
		</div>
	</div>
@endsection

@section('script')
	<script src="<?=asset('js/plugins/star-rating/star-rating.js') ?>" type="text/javascript"></script>

    <script src="<?=asset('js/pages/gongchang/pingjia.js') ?>" type="text/javascript"></script>

    <!-- optionally if you need to use a theme, then include the theme JS file as mentioned below -->
    <script src="<?=asset('css/plugins/star-rating/themes/krajee-fa/theme.js') ?>"></script>

@endsection