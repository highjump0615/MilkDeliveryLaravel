@extends('gongchang.layout.master')
@section('content')
	@include('gongchang.theme.sidebar')
	<div id="page-wrapper" class="gray-bg dashbard-1">
		@include('gongchang.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">

				<li>
					<a href=""><strong>首页</strong></a>
				</li>
			</ol>
		</div>
		<div class="ibox-content gray-bg vertical-align">
			<div class="col-lg-2"></div>
			<div class="col-lg-8">
				<?php $role_id = Auth::guard('gongchang')->user()->user_role_id;
				$role_pages = \App\Model\UserModel\UserPageAccess::where('user_role_id',$role_id)->get();?>
				@foreach($role_pages as $rp)
					@if($rp->page_id == 1)
						<div class="col-lg-6">
							<button class="btn btn-lg btn-success dim" onclick="location.href='xitong/yonghu'" type="button" style="width:100%; height: 100px;"><i class="fa fa-wrench"></i> 系统管理</button>
						</div>
					@endif
					@if($rp->page_id == 3)
						<div class="col-lg-6">
							<button class="btn btn-lg btn-success dim" onclick="location.href='jichuxinxi/dizhiku'" type="button" style="width:100%; height: 100px;"><i class="fa fa-info-circle"></i> 基础信息管理</button>
						</div>
					@endif
					@if($rp->page_id == 2)
						<div class="col-lg-6">
							<button class="btn btn-lg btn-success dim" onclick="location.href='dingdan/quanbudingdan-liebiao'" type="button" style="width:100%; height: 100px;"><i class="fa fa-check-circle"></i> 订单管理</button>
						</div>
					@endif
					@if($rp->page_id == 5)
						<div class="col-lg-6">
							<button class="btn btn-lg btn-success dim" onclick="location.href='shengchan/naizhanpeisong'" type="button" style="width:100%; height: 100px;"><i class="fa fa-tasks"></i> 生产管理</button>
						</div>
					@endif
					@if($rp->page_id == 9)
						<div class="col-lg-6">
							<button class="btn btn-lg btn-success dim" onclick="location.href='kehu/kehu'" type="button" style="width:100%; height: 100px;"><i class="fa fa-user"></i> 客户管理</button>
						</div>
					@endif
					@if($rp->page_id == 8)
						<div class="col-lg-6">
							<button class="btn btn-lg btn-success dim" onclick="location.href='pingkuang/pingkuang'" type="button" style="width:100%; height: 100px;"><i class="fa fa-flask"></i> 瓶框管理</button>
						</div>
					@endif
					@if($rp->page_id == 4)
						<div class="col-lg-6">
							<button class="btn btn-lg btn-success dim" onclick="location.href='caiwu/taizhang'" type="button" style="width:100%; height: 100px;"><i class="fa fa-cny"></i> 财务管理</button>
						</div>
					@endif
					@if($rp->page_id == 6)
						<div class="col-lg-6">
							<button class="btn btn-lg btn-success dim" onclick="location.href='tongjifenxi/naipinpeisongtongji'" type="button" style="width:100%; height: 100px;"><i class="fa fa-table"></i> 统计分析</button>
						</div>
					@endif
					@if($rp->page_id == 7)
						<div class="col-lg-6">
							<button class="btn btn-lg btn-success dim" onclick="location.href='naika/naika'" type="button" style="width:100%; height: 100px;"><i class="fa fa-credit-card"></i> 奶卡管理</button>
						</div>
					@endif
					@if($rp->page_id == 10)
						<div class="col-lg-6">
							<button class="btn btn-lg btn-success dim" onclick="location.href='pingjia/pingjialiebiao'" type="button" style="width:100%; height: 100px;"><i class="fa fa-star-half-o"></i> 评价管理</button>
						</div>
					@endif
				@endforeach
			</div>
			<div class="col-lg-2"></div>
		</div>
	</div>
@endsection