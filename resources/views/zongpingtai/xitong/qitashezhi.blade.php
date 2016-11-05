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
					<strong>其他设置</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
			
				  <div class="ibox float-e-margins row wrapper-content col-lg-12">
                    <div class="ibox-content">
                        <div class="ibox-row">
                            <h3>全局设置</h3>
                                <div class="hr-line-dashed"></div>
                            <form class="form-horizontal m-t-md" action="#">
                                <div class="form-group">
                                    <div class="col-md-11 col-md-offset-1">
                                        <div class="row">
                                            <div class="col-sm-2">授权地址安全模式</div>
                                            <div class="col-sm-6">
                                                 <label class="radio-inline">
                                                <input type="radio" value="option1" id="inlineradio1">宽松</label>
                                                <label class="radio-inline">
                                                <input type="radio" value="option2" id="inlineradio2">严格</label>
                                                <span class="help-block text-italic">设置严格模式时，系统提供给用户的授权地址失效为3分钟，在这个时间内用户没有点击则失效。并且在严格模式下，授权地址为一次性地址，用户点击后该地址自动生效。 但不会影响已授权过的用户.</span>

                                                <button type="submit" class="btn btn-success">提交</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="ibox-row">
                            <h3>系统锁操作</h3>
                            <div class="hr-line-dashed"></div>
                            <form class="form-horizontal m-t-md" action="#">
                                <div class="form-group">
                                    <div class="col-md-11 col-md-offset-1">
                                        <div class="row">
                                            <div class="col-sm-2">删除升级锁</div>
                                            <div class="col-sm-6">
                                                <button type="submit" class="btn btn-success">删除</button>
                                                <span class="help-block text-italic">升级 "微信" 系统的, 需要先删除升级锁, 确保升级正常进行.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <form class="form-horizontal m-t-md" action="#">
                                <div class="form-group">
                                    <div class="col-md-11 col-md-offset-1">
                                        <div class="row">
                                            <div class="col-sm-2">删除安装锁</div>
                                            <div class="col-sm-6">
                                                <button type="submit" class="btn btn-success">删除</button>
                                                <span class="help-block text-italic">重新安装 "微信" 系统的, 需要先删除安装锁</span>
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
@endsection
