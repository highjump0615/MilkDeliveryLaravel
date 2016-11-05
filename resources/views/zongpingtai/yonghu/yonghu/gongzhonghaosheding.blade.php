@extends('zongpingtai.layout.master')

@section('css')
	<style>
		.ad-banner-image, .ad-promo-image {
			width: 200px;
			height: 200px;
			background-size: cover;
			background-repeat: no-repeat;
		}

		input.upload-banner, input.upload-promo {
			display:none;
		}
	</style>
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
					<strong>公众号设定</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<form action="{{url('/zongpingtai/yonghu/gongzhonghaosheding', $factory_id)}}" method="post" enctype="multipart/form-data">
			<div class="wrapper-content">
				
				<div class="col-lg-12 gray-bg">
					<label class="col-lg-12" style="padding:5px;">公众号信息</label>
				</div>
				<div class="col-lg-12">
					<div class="col-lg-1"></div>
					<div class="col-lg-10"  style="padding-top:10px;">
						<div class="col-lg-12">
							<table class="table footable table-bordered">
									<tbody>
										<tr>
											<td class="col-md-6"></td>
											<td class="col-md-6"></td>
										</tr>
										<tr>
											<td class="col-md-6"></td>
											<td class="col-md-6"></td>
										</tr>
										<tr>
											<td class="col-md-6"></td>
											<td class="col-md-6"></td>
										</tr>
										<tr>
											<td class="col-md-6"></td>
											<td class="col-md-6"></td>
										</tr>
										<tr>
											<td class="col-md-6"></td>
											<td class="col-md-6"></td>
										</tr>
										<tr>
											<td class="col-md-6"></td>
											<td class="col-md-6"></td>
										</tr>
										<tr>
											<td class="col-md-6"></td>
											<td class="col-md-6"></td>
										</tr>
									</tbody>
								</table>
						</div>
					</div>
					<div class="col-lg-1"></div>
				</div>
				
				<div class="col-lg-12 gray-bg">
					<label class="col-lg-12" style="padding:5px;">API接口</label>
				</div>
				&nbsp;
				<div class="col-lg-12">
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">AppID(公众号):</label>
						<div class="col-lg-5 col-md-8">
							<input type="" placeholder="" style="width:100%;" value="">
						</div>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">AppSecret:</label>
						<div class="col-lg-5 col-md-8">
							<input type="" placeholder="" style="width:100%;" value="">
						</div>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">二维码:</label>
						<div class="col-lg-4 col-md-7">
							<input type="" placeholder="" style="width:100%;" value="">
						</div>
						<div class="col-lg-1">
						<button style="width:100%;" class="btn btn-success">上传</button>
						</div>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">AppID(公众号):</label>
						<div class="col-lg-5 col-md-8">
							<input type="" placeholder="" style="width:100%;" value="">
						</div>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">AppSecret:</label>
						<div class="col-lg-5 col-md-8">
							<input type="" placeholder="" style="width:100%;" value="">
						</div>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">AppID(公众号):</label>
						<div class="col-lg-5 col-md-8">
							<input type="" placeholder="" style="width:100%;" value="">
						</div>
					</div>
					<div class="feed-element col-md-12">
						<label class="control-label col-lg-2 col-md-3">AppSecret:</label>
						<div class="col-lg-5 col-md-8">
							<input type="" placeholder="" style="width:100%;" value="">
						</div>
					</div>

				</div>
				
				<div class="col-lg-12 gray-bg">
					<label class="col-lg-12" style="padding:5px;">自定义菜单</label>
				</div>

				<?php
				$cn_nums = ['','一','二','三'];
				$submenus = ['一级菜单','二级菜单1','二级菜单2','二级菜单3','二级菜单4','二级菜单5'];
				$menu_types = ['未设定', 'Click', 'App', ''];
				?>
				@for($menu_no=1; $menu_no<=3; $menu_no++)
				<div class="col-lg-12">
					<div class="col-lg-1"></div>
					<div class="col-lg-10"  style="padding-top:10px;">
						<label class="col-lg-12 gray-bg">第{{$cn_nums[$menu_no]}}菜单</label>
						<div class="col-lg-12">
							<table class="table footable table-bordered">
									<thead>
										<tr>
											<th class="col-md-3">级别</th>
											<th class="col-md-3">类型</th>
											<th class="col-md-3">名称</th>
											<th class="col-md-3">关键字级域名</th>
										</tr>
									</thead>
									<tbody>

									@for($submenu_no=0; $submenu_no<=5; $submenu_no++)
										<tr>
											<td>{{$submenus[$submenu_no]}}</td>
											<td>
												<select data-placeholder="" class="chosen-select" style="width:100%;" tabindex="2">
													@if($submenu_no == 0)
														<option value="1">{{$menu_types[1]}}</option>
														<option value="0">{{$menu_types[0]}}</option>
													@else
														<option value="2">{{$menu_types[2]}}</option>
														<option value="0">{{$menu_types[0]}}</option>
													@endif
												</select>
											</td>
											<td><input type="text" style="width:100%;"></td>
											<td><input type="text" style="width:100%;"></td>
										</tr>
									@endfor
									</tbody>
								</table>
						</div>
					</div>
					<div class="col-lg-1"></div>
				</div>
				@endfor
				
				<div class="col-lg-12 gray-bg">
					<label class="col-lg-12" style="padding:5px;">广告管理</label>
				</div>
				<div class="feed-element col-md-12">
					<label class="control-label col-lg-6 col-md-8">首页轮播图：（可添加4张）:</label>
				</div>
				@for($i=1; $i<=4; $i++)
				<div class="col-lg-12">
					<div class="col-lg-1 col-md-1"></div>
					<div class="col-lg-10 col-md-10" style="padding-bottom:5px;">
						<div class="col-lg-3">
							<img id="img_ad_banner_{{$i}}" class="ad-banner-image"
								 @if(isset($banners[$i]))
								 src="<?=asset($banners[$i]->image_url)?>"
								 @endif
							>
						</div>
						<div class="col-lg-2">
							<input name="banner{{$i}}" id="upload-banner-{{$i}}" class="upload-banner" data-id="{{$i}}" type="file"/>
							<a class="upload-banner-link" data-id="{{$i}}">上传图片</a>
						</div>
						<div class="col-lg-1"><a class="delete-banner" data-id="{{$i}}">清除</a></div>
						<div class="col-lg-1"><label>链接</label></div>
						<div class="col-lg-2">
							<select name="product_banner_{{$i}}" data-placeholder="" class="chosen-select" style="width:100%;" tabindex="2">
								@forelse($products as $p)
									@if(isset($banners[$i]) && $banners[$i]->product_id == $p->id)
										<option value="{{$p->id}}" selected>{{$p->name}}</option>
									@else
										<option value="{{$p->id}}">{{$p->name}}</option>
									@endif
								@empty
								<option value="-1" disabled>没有商品</option>
								@endforelse
							</select>
						</div>
					</div>
				</div>
				@endfor

				<div class="feed-element col-md-12">
					<label class="control-label col-lg-6 col-md-8">首页促销位：（可添加4张）:</label>
				</div>
				@for($i=1; $i<=4; $i++)
				<div class="col-lg-12">
					<div class="col-lg-1 col-md-1"></div>
					<div class="col-lg-10 col-md-10" style="padding-bottom:5px;">
						<div class="col-lg-3">
							<img id="img_ad_promo_{{$i}}" class="ad-promo-image"
								 @if(isset($promos[$i]))
								 src="<?=asset($promos[$i]->image_url)?>"
								 @endif
							>
						</div>
						<div class="col-lg-2">
							<input name="promo{{$i}}" id="upload-promo-{{$i}}" class="upload-promo" data-id="{{$i}}" type="file"/>
							<a class="upload-promo-link" data-id="{{$i}}">上传图片</a>
						</div>
						<div class="col-lg-1"><a class="delete-promo" data-id="{{$i}}">清除</a></div>
						<div class="col-lg-1"><label>链接</label></div>
						<div class="col-lg-2">
							<select name="product_promo_{{$i}}" data-placeholder="" class="chosen-select" style="width:100%;" tabindex="2">
								@forelse($products as $p)
									@if(isset($promos[$i]) && $promos[$i]->product_id == $p->id)
									<option value="{{$p->id}}" selected>{{$p->name}}</option>
									@else
									<option value="{{$p->id}}">{{$p->name}}</option>
									@endif
								@empty
									<option value="-1" disabled>没有商品</option>
								@endforelse
							</select>
						</div>
					</div>
				</div>
				@endfor
				
				<div class="col-lg-12 gray-bg">
					<label class="col-lg-12" style="padding:5px;">其他设定</label>
				</div>
				
				<div class="feed-element col-md-12">
					<label class="control-label col-lg-1 col-md-2">客服电话:</label>
					<div class="col-lg-3 col-md-4">
						<input type="text" placeholder="" style="width:100%;" value="">
					</div>
				</div>
				<div class="feed-element col-md-12">
					<label class="control-label col-lg-1 col-md-2">退订电话:</label>
					<div class="col-lg-3 col-md-4">
						<input type="text" placeholder="" style="width:100%;" value="">
					</div>
				</div>

			</div>

			<div class="feed-element col-md-12">
				<div class="col-md-5"></div>
				<div class="col-md-2">
					<input type="submit" class="btn btn-success col-md-2" style="width:100%;" value="确定">
				</div>
			</div>
			</form>
		</div>
		
	</div>
@endsection

@section('script')
	<script>
		var factory_id = 0+"{{$factory_id}}";
		$(".upload-banner-link").on('click', function (e) {
			e.preventDefault();

			var id = $(this).data('id');
			console.log(id);
			var upload_id = "#upload-banner-" + id ;

			console.log(upload_id);
			$(upload_id + ":hidden").trigger('click');
		});

		$(".upload-banner").on('change', function(){

			var id = $(this).data('id');
			console.log(this);
			var img_id = "#img_ad_banner_" + id;
			readURL(this, img_id);
		});


		$(".upload-promo-link").on('click', function (e) {
			e.preventDefault();

			var id = $(this).data('id');
			console.log(id);
			var upload_id = "#upload-promo-" + id ;

			console.log(upload_id);
			$(upload_id + ":hidden").trigger('click');
		});

		$(".upload-promo").on('change', function(){

			var id = $(this).data('id');
			var img_id = "#img_ad_promo_" + id;
			readURL(this, img_id);
		});

		$(".delete-banner").on('click', function(e){
			var id = $(this).data('id');

			e.preventDefault();
			e.stopPropagation();

			$.confirm({
				icon: 'fa fa-warning',
				title: '删除',
				text: '你会真的删除吗？',
				confirmButton: "是",
				cancelButton: "不",
				confirmButtonClass: "btn-success",
				confirm: function () {
					delete_banner(id);


				},
				cancel: function () {
					return;
				}
			});
		});

		function delete_banner(banner_id) {

//			var product_id = $(button).data('target');

			var senddata = {'banner_id': banner_id, 'factory_id': factory_id};
			$.ajax({
				type: 'POST',
				url: API_URL + 'zongpingtai/yonghu/gongzhonghaosheding/delete_banner',
				data: senddata,
				success: function (data) {
					console.log(data);
					if (data.status == "success") {

						var img_id = "#img_ad_banner_" + banner_id;
						$(img_id).attr('src', '');

						show_success_msg("删除成功");
					} else {
						show_warning_msg(data.message);
					}
				},
				error: function (data) {
					console.log(data);
				}

			});
		};


		function readURL(input, img_id) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$(img_id).attr('src', e.target.result);
				}
				reader.readAsDataURL(input.files[0]);
			}
		}
	</script>
@endsection