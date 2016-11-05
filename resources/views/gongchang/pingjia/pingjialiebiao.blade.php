@extends('gongchang.layout.master')
@section('css')
	<link href="<?=asset('css/plugins/datepicker/datepicker3.css') ?>" rel="stylesheet">

	<link href="<?=asset('css/plugins/added/switchery.css') ?>" rel="stylesheet">
	
	<link href="<?=asset('css/plugins/iCheck/custom.css') ?>" rel="stylesheet">
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
                        <div class="ibox-content">
                            <form method="get" class="form-horizontal">
                                <div class="row">
                                    <div class="form-group col-md-4">

                                        <label class="col-sm-3 control-label">状态:</label>
                                        <div class="col-sm-9" style="padding-left:0px;">
                                            <select class="form-control m-b" name="account">
                                                {{--<option>通过</option>--}}
                                                {{--<option>未通过</option>--}}
                                                {{--<option>待审核</option>--}}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-5" id="data_5">
                                        <label class="col-sm-3 control-label">选择时间:</label>
                                        <div class="col-sm-9 input-daterange input-group" id="datepicker">
                                            <input type="text" class="input-sm form-control" name="start" />
                                            <span class="input-group-addon">至</span>
                                            <input type="text" class="input-sm form-control" name="end"  />
                                        </div>
                                    </div>
                                    <div class="col-md-offset-2 col-md-1">
                                        <button class="btn btn-success btn-md" style="width: 100%;">通过</button>
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
                                            <th data-sort-ignore="true"></th>
                                            <th data-sort-ignore="true">客户 </th>
                                            <th data-sort-ignore="true">评价内容 </th>
                                            <th data-sort-ignore="true">发送时间</th>
                                            <th data-sort-ignore="true">状态</th>
                                            <th data-sort-ignore="true">操作</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="6">没有数据!</td>
                                            </tr>
                                        {{--<tr>--}}
                                            {{--<td><input type="checkbox"  checked class="i-checks" name="input[]"></td>--}}
                                            {{--<td>客户1</td>--}}
                                            {{--<td style="text-align : left;">--}}
                                                {{--<input type="text" class="kv-fa rating-loading" value="4" data-size="xs" title="" readonly>--}}
                                                {{--订单到期通知--}}
                                            {{--</td>--}}
                                            {{--<td>2016-08-12 10:44:32</td>--}}
                                            {{--<td>通过</td>--}}
                                            {{--<td>--}}
                                                {{--<a href={{URL::to('/gongchang/pingjia/pingjiaxiangqing')}} class="btn btn-success btn-sm">查看</a>--}}
                                                {{--<button class="btn btn-success btn-sm">通过</button>--}}
                                                {{--<button class="btn btn-success btn-sm">屏蔽</button>--}}
                                                {{--<button class="btn btn-success btn-sm">编辑</button>--}}
                                            {{--</td>--}}
                                        {{--</tr>--}}
                                        {{--<tr>--}}
                                            {{--<td><input type="checkbox"  checked class="i-checks" name="input[]"></td>--}}
                                            {{--<td>客户2</td>--}}
                                            {{--<td style="text-align : left;">--}}
                                                {{--<input type="text" class="kv-fa rating-loading" value="3" data-size="xs" title="" readonly>--}}
                                                {{--奶站信用额度到期--}}
                                            {{--</td>--}}
                                            {{--<td>2016-08-12 10:44:32</td>--}}
                                            {{--<td>屏蔽</td>--}}
                                            {{--<td>--}}
                                                {{--<a href={{URL::to('/gongchang/pingjia/pingjiaxiangqing')}} class="btn btn-success btn-sm">查看</a>--}}
                                                {{--<button class="btn btn-success btn-sm">通过</button>--}}
                                                {{--<button class="btn btn-success btn-sm">屏蔽</button>--}}
                                                {{--<button class="btn btn-success btn-sm">编辑</button>--}}
                                            {{--</td>--}}
                                        {{--</tr>--}}
                                        {{--<tr>--}}
                                            {{--<td><input type="checkbox"  checked class="i-checks" name="input[]"></td>--}}
                                            {{--<td>客户3</td>--}}
                                            {{--<td style="text-align : left;">--}}
                                                {{--<input type="text" class="kv-fa rating-loading" value="2" data-size="xs" title="" readonly>--}}
                                                {{--未匹配奶站订单--}}
                                            {{--</td>--}}
                                            {{--<td>2016-08-12 10:44:32</td>--}}
                                            {{--<td>待审核</td>--}}
                                            {{--<td>--}}
                                                {{--<a href={{URL::to('/gongchang/pingjia/pingjiaxiangqing')}} class="btn btn-success btn-sm">查看</a>--}}
                                                {{--<button class="btn btn-success btn-sm">通过</button>--}}
                                                {{--<button class="btn btn-success btn-sm">屏蔽</button>--}}
                                                {{--<button class="btn btn-success btn-sm">编辑</button>--}}
                                            {{--</td>--}}
                                        {{--</tr>--}}
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

                            </form>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
@endsection

@section('script')
	<script src="<?=asset('js/plugins/added/switchery.js') ?>"></script>
   
	<script src="<?=asset('js/plugins/iCheck/icheck.min.js') ?>"></script>
    <!-- Data picker -->
    <script src="<?=asset('js/plugins/datepicker/bootstrap-datepicker.js') ?>"></script>

	<script src="<?=asset('js/plugins/star-rating/star-rating.js') ?>" type="text/javascript"></script>

    <!-- optionally if you need to use a theme, then include the theme JS file as mentioned below -->
    <script src="<?=asset('css/plugins/star-rating/themes/krajee-fa/theme.js') ?>"></script>

    <script>
        $(document).ready(function(){
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
            
            $('.kv-fa').rating({
                theme: 'krajee-fa',
                filledStar: '<i class="fa fa-star"></i>',
                emptyStar: '<i class="fa fa-star-o"></i>'
            });

            $('#data_5 .input-daterange').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true
            });

            $('.rating,.kv-gly-star,.kv-gly-heart,.kv-uni-star,.kv-uni-rook,.kv-fa,.kv-fa-heart,.kv-svg,.kv-svg-heart').on('change', function () {
                console.log('Rating selected: ' + $(this).val());
            });
        });
		$('.footable').footable();
    </script>
@endsection