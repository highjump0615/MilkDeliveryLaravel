@extends('zongpingtai.layout.master')

@section('css')
@endsection

@section('content')
	@include('zongpingtai.theme.sidebar')
	<div id="page-wrapper" class="gray-bg dashbard-1">
		@include('zongpingtai.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="{{ url('zongpingtai/yonghu')}}">用户管理</a>
				</li>
				<li class="active">
					<strong>用户详情</strong>
				</li>
			</ol>
		</div>
		<form action="{{url('api/zongpingtai/yonghu/xiangqing')}}/{{$factory->id}}" method="post" enctype="multipart/form-data">
			<div class="row wrapper">
				<div class="wrapper-content">

					<div class="feed-element">
						<label class="col-sm-12 gray-bg" style="padding:5px;background-color: #f3f3f4;"> 公司信息</label>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">公司名称:</label>
						<div class="col-lg-3 col-md-4">
							<input name="name" type="text" placeholder="" style="width:100%;" value="{{$factory->name}}">
						</div>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">用户编号：</label>
						<div class="col-lg-3 col-md-4">
							<input name="number" type="text" placeholder="" class="bottle_input" value="{{$factory->number}}" readonly>
						</div>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">联系人:</label>
						<div class="col-lg-3 col-md-4">
							<input name="contact" type="text" placeholder="" style="width:100%;" value="{{$factory->contact}}">
						</div>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">手机号:</label>
						<div class="col-lg-3 col-md-4">
							<input name="phonenumber" type="text" placeholder="" style="width:100%;" value="{{$factory->phone}}">
						</div>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">上传logo:</label>
						<div class="col-lg-3 col-md-4">
							<img id="logo_pic" src="<?=asset($factory->logo_url)?>" class="img-responsive" style="width:150px;"/>
							<label><input id="logo" name="logo" onchange="uploadlogo(this);" type="file"accept="image/gif|image/jpeg|image/png" class="hide">上传logo</label>
						</div>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">账户名称:</label>
						<div class="col-lg-3 col-md-4">
							<input name="factory_id" type="text" placeholder="" style="width:100%;" value="{{$factory->factory_id}}">
						</div>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">密码:</label>
						<div class="col-lg-3 col-md-4">
							<input name="factory_password" type="password" placeholder="" style="width:100%;" value="">
						</div>
						<label>数字、字母、符号</label>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">系统状态:</label>
						<div class="col-lg-3 col-md-4">
							<input id="status" type="checkbox" class="js-switch" onchange="changeStatus();" @if($factory->status == 1)checked @endif/>
							<input id="status_val" name="status" type="hidden" value="1">
						</div>
					</div>
					<div class="feed-element col-md-12" id="date_1">
						<label class="control-label col-lg-2 col-md-3">到期时间:</label>
						<div class="input-group date col-md-3">
							<input required name="end_at" type="text" class="form-control" id="end_date" value="{{$factory->end_at}}"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						</div>
					</div>
				</div>

				<div class="wrapper-content">
					<div class="feed-element">
						<label class="col-lg-12 gray-bg" style="padding:5px;background-color: #f3f3f4;"> 微信设置</label>
					</div>
                                        <div class="feed-element col-md-12">
                                            <label class="col-lg-12 gray-bg" style="padding:5px;"> 微信公众号</label>
					</div>
					<div class="feed-element col-md-12">
                                            <label class="control-label col-lg-4 col-md-5"> AppID(公众号):</label>
                                            <div class="col-lg-4 col-md-7">
                                                <input name="app_id" type="text" placeholder="" style="width:100%;" value="{{$factory->app_id}}">
                                            </div>
					</div>
					<div class="feed-element col-md-12">
                                            <label class="control-label col-lg-4 col-md-5"> AppSecret:</label>
                                            <div class="col-lg-4 col-md-7">
                                                <input name="app_secret" type="text" placeholder="" style="width:100%;" value="{{$factory->app_secret}}">
                                            </div>
					</div>
                                        <div class="feed-element col-md-12">
                                            <label class="control-label col-lg-4 col-md-5"> 服务器接口 (URL):</label>
                                            <div class="col-lg-4 col-md-7">
                                                <input name="app_url" type="text" placeholder="" style="width:100%;" value="{{$factory->app_url}}">
                                            </div>
					</div>
                                        <div class="feed-element col-md-12">
                                            <label class="control-label col-lg-4 col-md-5"> 令牌 (Token)：</label>
                                            <div class="col-lg-4 col-md-7">
                                               <input name="app_token" type="text" placeholder="" style="width:100%;" value="{{$factory->app_token}}">
                                            </div>
					</div>
                                        <div class="feed-element col-md-12">
                                            <label class="control-label col-lg-4 col-md-3"> 消息加解密密钥 (EncodingAESKey)：</label>
                                            <div class="col-lg-4 col-md-3">
                                                <input name="app_encoding_key" type="text" placeholder="" style="width:100%;" value="{{$factory->app_encoding_key}}">
                                            </div>
					</div>
					<div class="feed-element col-md-12">
						<div class="col-md-5"></div>
						<div class="col-md-2">
							<button id="submit" class="btn btn-success col-md-2" type="submit" style="width:100%;">确定</button>
						</div>
					</div>

				</div>
			</div>
		</form>
	</div>
@endsection

@section('script')
	<script>
		$('#date_1 .input-group.date').datepicker({
			todayBtn: "linked",
			keyboardNavigation: false,
			forceParse: false,
			calendarWeeks: false,
			autoclose: true
		});


		if (Array.prototype.forEach) {
			var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
			elems.forEach(function(html) {
				var switchery = new Switchery(html);
			});
		} else {
			var elems = document.querySelectorAll('.js-switch');
			for (var i = 0; i < elems.length; i++) {
				var switchery = new Switchery(elems[i]);
			}
		}

		function changeStatus(){
			if(document.getElementById('status').checked){
				$('#status_val').val(1);
			}
			else{
				$('#status_val').val(0);
			}
		}

		function uploadlogo(input) {
			if(input.files && input.files[0]){
				var reader = new FileReader();
				reader.onload = function(e) {
					$('#logo_pic').attr('src',e.target.result);
				};
				reader.readAsDataURL(input.files[0]);
			}
		}

	</script>
@endsection