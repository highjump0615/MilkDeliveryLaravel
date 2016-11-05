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
					<strong>查看日志</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				 <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#tab-1">微信曰志</a></li>
                            <li><a data-toggle="tab" href="#tab-2">系统曰志</a></li>
                            <li><a data-toggle="tab" href="#tab-3">数据库曰志</a></li>
                            <li><a data-toggle="tab" href="#tab-4">短信发送曰志</a></li>
                        </ul>

                        <div class="tab-content">
                            <div id="tab-1" class="tab-pane active">
                                <div class="panel-body">
                                   <div class="ibox float-e-margins row wrapper-content col-lg-12">
                                        <div class="ibox-content">
                                            <h3>日志</h3>
                                            <div class="hr-line-dashed"></div>
                                            <div class="panel panel-wb">
                                                <div class="panel-heading">
                                                 曰志信息
                                                </div>
                                                <div class="panel-body">
                                                    <label class="col-sm-2 control-label">曰期选择</label>
                                                    <div class="col-sm-7">
                                                        <select class="form-control m-b" name="account">
                                                            <option>20170820</option>
                                                            <option>20170821</option>
                                                            <option>20170822</option>
                                                            <option>20170823</option>
                                                        </select>
                                                    </div>
                                                    <button class="col-sm-1 col-sm-offset-2 btn btn-outline btn-default wgray-bg" type="button">&nbsp;&nbsp;搜索</button>
                                                </div>

                                            </div>
                                            <textarea class="wgray-bg" rows="20"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="tab-2" class="tab-pane">
                                <div class="panel-body">
                                    <strong>Donec quam felis</strong>

                                    <p>Thousand unknown plants are noticed by me: when I hear the buzz of the little world among the stalks, and grow familiar with the countless indescribable forms of the insects
                                        and flies, then I feel the presence of the Almighty, who formed us in his own image, and the breath </p>

                                    <p>I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine. I am so happy, my dear friend, so absorbed in the exquisite
                                        sense of mere tranquil existence, that I neglect my talents. I should be incapable of drawing a single stroke at the present moment; and yet.</p>
                                </div>
                            </div>

                            <div id="tab-3" class="tab-pane">
                                <div class="panel-body">
                                    <strong>Donec quam felis</strong>

                                    <p>Thousand unknown plants are noticed by me: when I hear the buzz of the little world among the stalks, and grow familiar with the countless indescribable forms of the insects
                                        and flies, then I feel the presence of the Almighty, who formed us in his own image, and the breath </p>

                                    <p>I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine. I am so happy, my dear friend, so absorbed in the exquisite
                                        sense of mere tranquil existence, that I neglect my talents. I should be incapable of drawing a single stroke at the present moment; and yet.</p>
                                </div>
                            </div>

                            <div id="tab-4" class="tab-pane">
                                <div class="panel-body">
                                    <strong>Donec quam felis</strong>

                                    <p>Thousand unknown plants are noticed by me: when I hear the buzz of the little world among the stalks, and grow familiar with the countless indescribable forms of the insects
                                        and flies, then I feel the presence of the Almighty, who formed us in his own image, and the breath </p>

                                    <p>I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine. I am so happy, my dear friend, so absorbed in the exquisite
                                        sense of mere tranquil existence, that I neglect my talents. I should be incapable of drawing a single stroke at the present moment; and yet.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
			</div>
		</div>
		
	</div>
@endsection
