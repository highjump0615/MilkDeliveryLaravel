@extends('naizhan.layout.master')

@section('content')
	@include('naizhan.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('naizhan.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					消息中心
				</li>
				<li class="active">
					<strong>消息详情</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				<div class="bottom-hr-div">
					<label class="col-lg-10">{{$dsnotification->title}}</label>
					<label class="col-lg-2">{{$dsnotification->created_at}}</label>
				</div>
				<div class="col-lg-12">
					<label class="col-lg-12">{{$dsnotification->content}}</label>
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
			window.location = SITE_URL + "naizhan/xiaoxi/zhongxin";
		})
	</script>
@endsection	
