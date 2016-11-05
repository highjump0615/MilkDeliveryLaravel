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
					<strong>检查工具</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				 <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-1">检测系统 BOM</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <div class="panel-body">
                                <div class="ibox float-e-margins row wrapper-content col-lg-12">
                                    <div class="ibox-content">
                                        <form class="form-horizontal m-t-md" action="#">

                                            <div class="ibox-row">
                                                <h3>检测系统 BOM</h3>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group">

                                                    <div class="row ibox-row">
                                                        <div class="col-md-1"><h4>操作说明</h4></div>
                                                        <div class="col-md-11">
                                                            <p class="help-block text-italic">Image size should not bigger that 220*220.</p>
                                                            
                                                            <strong class="help-block text-italic">Image size should not bigger that 220*220.</strong>
                                                            
                                                            <strong class="help-block text-italic">Image size should not bigger that 220*220.</strong>
                                                            
                                                            <strong class="help-block text-italic">Image size should not bigger that 220*220.</strong>
                                                        </div>
                                                    </div>

                                                    <div class="row ibox-row">
                                                        <div class="col-md-1"><h4>处理说明</h4></div>
                                                        <div class="col-md-11">
                                                        <p class="help-block text-italic">Image size should not bigger that 220*220.</p>
                                                        </div>
                                                    </div>

                                                    <div class="row ibox-row">
                                                        <div class="col-md-1 col-md-offset-1">
                                                            <button class="btn btn-success btn-m-w">检测BOM异常</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


			</div>
		</div>
		
	</div>
@endsection
