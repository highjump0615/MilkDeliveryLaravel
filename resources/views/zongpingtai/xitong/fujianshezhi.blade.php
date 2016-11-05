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
					<strong>附件设置</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				<div class="ibox float-e-margins row wrapper-content col-lg-12">
                    <div class="ibox-content">
                        <form class="form-horizontal m-t-md" action="#">

                            <div class="ibox-row">
                                <h3>PHP 环境</h3>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group">
                                    <div class="col-md-11 col-md-offset-1">
                                        <div class="row">
                                            <div class="col-sm-2">PHP 环境说明</div>
                                            <div class="col-sm-6">
                                                <ol>
                                                    <li>当前 PHP 环境允许最大单个上传文件大小为 2M</li>
                                                    <li>当前 PHP 环境允许最大 POST表单个大小为: 8M</li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ibox-row">
                                <h3>附件缩略设置</h3>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group">
                                    <div class="col-md-11 col-md-offset-1">
                                        <div class="row">
                                            <div class="col-sm-2">缩略设置</div>
                                            <div class="col-sm-6">

                                                <label class="radio-inline">
                                                <input type="radio" value="option1" id="inlineradio1">不启用缩略</label>
                                                <label class="radio-inline">
                                                <input type="radio" value="option2" id="inlineradio2">启用缩略</label> 

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ibox-row">
                                <h3>图片附件设置</h3>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group">
                                    <div class="col-md-11 col-md-offset-1">
                                        <div class="row">
                                            <div class="col-sm-2">支持文件后缀</div>
                                            <div class="col-sm-6">
                                                <textarea rows="5">gif&#10;jpg&#10;jpeg&#10;png&#10;</textarea>
                                                <span class="help-block text-italic">填写图片后缀名称, 如: jpg, 换行输入, 一行一个后缀.</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-2">支持文件大小</div>
                                            <div class="col-sm-6">
                                                <div class="input-group m-b"><input type="text" class="form-control" placeholder="5000"> <span class="input-group-addon">KB</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ibox-row">
                                <h3>Media Setting</h3>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group">
                                    <div class="col-md-11 col-md-offset-1">
                                        <div class="row">
                                            <div class="col-sm-2">Media Extension</div>
                                            <div class="col-sm-6">
                                                <textarea rows="5">mp3&#10;</textarea>
                                                <span class="help-block text-italic">填写图片后缀名称, 如: mp3, 换行输入, 一行一个后缀.</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-2">支持文件大小</div>
                                            <div class="col-sm-6">
                                                <div class="input-group m-b"><input type="text" class="form-control" placeholder="5000"> <span class="input-group-addon">KB</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ibox-row">
                                    <button type="submit" class="col-md-2 col-md-offset-1 btn btn-success btn-lg" >提交</button>
                            </div>

                        </form>
                    </div>
                </div>
                
			</div>
		</div>
		
	</div>
@endsection
