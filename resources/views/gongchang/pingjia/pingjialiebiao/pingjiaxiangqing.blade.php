@extends('gongchang.layout.master')
@section('css')
	<link href="<?=asset('css/plugins/added/switchery.css') ?>" rel="stylesheet">
	
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
                            <form method="get" class="form-horizontal">
                                <div class="row">
                                    <div class="form-group">

                                        <label class="col-sm-2 control-label" style="padding-top: 15px;">综合评价</label>
                                        <input type="text" class="kv-fa rating-loading" value="{{$review->mark}}" data-size="xs" title="" readonly>
                                    </div>
								</div>
                                <div class="hr-line-dashed"></div>
								<div class="ibox">
									<label style="padding-left: 120px;">{{$review->content}}</label>
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
   
    <!-- Data picker -->
    <script src="<?=asset('js/plugins/datepicker/bootstrap-datepicker.js') ?>"></script>

	<script src="<?=asset('js/plugins/star-rating/star-rating.js') ?>" type="text/javascript"></script>

    <!-- optionally if you need to use a theme, then include the theme JS file as mentioned below -->
    <script src="<?=asset('css/plugins/star-rating/themes/krajee-fa/theme.js') ?>"></script>

    <script>
        $(document).ready(function(){

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
    </script>
@endsection