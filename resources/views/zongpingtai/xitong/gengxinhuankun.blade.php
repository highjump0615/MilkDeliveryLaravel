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
					<strong>更新缓存</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				<div class="feed-element">
	                <div class="col-md-5 col-xs-12">

	                    <form method="get" class="form-horizontal">
	                    
	                        <h3>更新缓存</h3>
	                        <div class="hr-line-dashed"></div>

	                        <div class="form-group row">
	                            <label class="col-md-4">緩存类型</label>
	                            <div class="col-md-8">
	                                <label class="checkbox-inline col-"><input type="checkbox" value="option1" id="inlineCheckbox1" checked="checked"> 数据緩存 </label> 
	                                <label class="checkbox-inline  col-"><input type="checkbox" value="option2" id="inlineCheckbox2" checked="checked"> 模板緩存 </label>

	                            </div>
	                        </div>
	                        <input type="button" class="btn btn-success col-md-offset-4" value="提交"/>

	                    </form>    
	                </div>
	            </div>
			</div>
		</div>
		
	</div>
@endsection
