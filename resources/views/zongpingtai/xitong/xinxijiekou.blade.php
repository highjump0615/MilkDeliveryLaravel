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
					<strong>信息接口</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				  <div class="ibox float-e-margins">
                        <div class="ibox-content">
                            <div class="row show-grid">
                                <div class="col-xs-12 col-md-12">短信接口</div>
                            </div>
                            <div class="row show-grid">
                                Here is content
                            </div>
                            <div class="row show-grid">
                                <div class="col-xs-12 col-md-12">微信接口</div>
                            </div>
                            <div class="row show-grid">
                                Here is content
                            </div>
                        </div>
                    </div>
                    
			</div>
		</div>
		
	</div>
@endsection
