@extends('zongpingtai.layout.master')

@section('content')
	@include('zongpingtai.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('zongpingtai.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a href="index.html">瓶框管理</a>
				</li>
				<li class="active">
					<strong>瓶框库存管理</strong>
				</li>
			</ol>
		</div>
	</div>

@endsection
