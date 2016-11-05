@extends('gongchang.layout.master')

@section('content')
	@include('gongchang.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('gongchang.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li>
					<a href={{URL::to('/gongchang/xinxi/zhongxin')}}"">消息中心</a>
				</li>
				<li class="active">
					<a href="">消息详情</a>
				</li>
			</ol>
		</div>

		 <div class="row wrapper">
			 <div class="wrapper-content">

				 <div class="bottom-hr-div">
					 <label class="col-lg-10">{{$fac_notifications->title}}</label>
					 <label class="col-lg-2">{{$fac_notifications->created_at}}</label>
				 </div>
				 <div class="col-lg-12">
					 <label class="col-lg-12">{{$fac_notifications->content}}</label>
				 </div>
				 <div class="col-md-offset-5 col-md-2" style="padding-top: 50px">
					 <button id="return" class="btn btn-success btn-outline" style="width: 70%"><i class="fa fa-reply"></i></button>
				 </div>
			 </div>
		 </div>
	</div>	
@endsection

@section('script')
	<script>
		$('#return').click(function () {
			window.location = SITE_URL + "gongchang/xinxi/zhongxin";
		})
	</script>
@endsection