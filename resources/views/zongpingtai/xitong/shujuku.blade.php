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
					<strong>数据库</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				  <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-1">备份</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-2">还原</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-3">数据库结构整理</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-4">优化</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-5">运行SQL</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <div class="panel-body">
                                <div class="ibox float-e-margins row wrapper-content col-lg-12">
                                    <div class="ibox-content">
                                        <form class="form-horizontal m-t-md" action="#">

                                            <div class="ibox-row">
                                                <h3>备份数据库</h3>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group">
                                                    <div class="row ibox-row">
                                                        <div class="col-md-1">备份操作说明</div>
                                                        <div class="col-md-11">
                                                        <p>并且在严格模式下, 授权地址为一次性地址, 用戶点击后该地址自动失效, 但不会景彡响已授权过的用户. 设置严格模式时, 系统提供给用戶的授权地址时效为3分钟, 在这个时间内用户没有点击则失效</p></div>
                                                    </div>

                                                    <div class="row ibox-row">
                                                        <div class="col-md-2 col-md-offset-2">
                                                            <button class="btn btn-success btn-w-m">Start</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="tab-2" class="tab-pane">
                            <div class="panel-body">
                                <div class="ibox float-e-margins row wrapper-content col-lg-12">
                                    <div class="ibox-content">
                                        <form class="form-horizontal m-t-md" action="#">

                                            <div class="ibox-row">
                                                <h3>Title2</h3>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group">
                                                    <div class="row ibox-row">
                                                        <div class="col-md-1">Current Setting Narration</div>
                                                        <div class="col-md-11">
                                                        <p>Here are settings narration. Here are settings narration. Here are settings narration. Here are settings narration.</p></div>
                                                    </div>

                                                    <div class="row ibox-row">
                                                        <div class="col-md-2 col-md-offset-2">
                                                            <button class="btn btn-success btn-w-m">Start</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div id="tab-3" class="tab-pane">
                            <div class="panel-body">
                                <div class="ibox float-e-margins row wrapper-content col-lg-12">
                                    <div class="ibox-content">
                                        <form class="form-horizontal m-t-md" action="#">

                                            <div class="ibox-row">
                                                <h3>Title3</h3>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group">
                                                    <div class="row ibox-row">
                                                        <div class="col-md-1">Current Setting Narration</div>
                                                        <div class="col-md-11">
                                                        <p>Here are settings narration. Here are settings narration. Here are settings narration. Here are settings narration.</p></div>
                                                    </div>

                                                    <div class="row ibox-row">
                                                        <div class="col-md-2 col-md-offset-2">
                                                            <button class="btn btn-success btn-w-m">Start</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div id="tab-4" class="tab-pane">
                            <div class="panel-body">
                                <div class="ibox float-e-margins row wrapper-content col-lg-12">
                                    <div class="ibox-content">
                                        <form class="form-horizontal m-t-md" action="#">

                                            <div class="ibox-row">
                                                <h3>Title4</h3>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group">
                                                    <div class="row ibox-row">
                                                        <div class="col-md-1">Current Setting Narration</div>
                                                        <div class="col-md-11">
                                                        <p>Here are settings narration. Here are settings narration. Here are settings narration. Here are settings narration.</p></div>
                                                    </div>

                                                    <div class="row ibox-row">
                                                        <div class="col-md-2 col-md-offset-2">
                                                            <button class="btn btn-success btn-w-m">Start</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div id="tab-5" class="tab-pane">
                            <div class="panel-body">
                                <div class="ibox float-e-margins row wrapper-content col-lg-12">
                                    <div class="ibox-content">
                                        <form class="form-horizontal m-t-md" action="#">

                                            <div class="ibox-row">
                                                <h3>Title5</h3>
                                                <div class="hr-line-dashed"></div>
                                                <div class="form-group">
                                                    <div class="row ibox-row">
                                                        <div class="col-md-1">Current Setting Narration</div>
                                                        <div class="col-md-11">
                                                        <p>Here are settings narration. Here are settings narration. Here are settings narration. Here are settings narration.</p></div>
                                                    </div>

                                                    <div class="row ibox-row">
                                                        <div class="col-md-2 col-md-offset-2">
                                                            <button class="btn btn-success btn-w-m">Start</button>
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
