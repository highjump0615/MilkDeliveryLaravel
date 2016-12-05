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

						<form action="{{url('zongpingtai/xitong/gengxinhuankun')}}" method="post" enctype="multipart/form-data">

							<h3>更新缓存<span style='margin-left: 20%;color:#1c84c6;'>{{$mass}}</span></h3>
							<div class="hr-line-dashed"></div>

							<div class="form-group row">
								<label class="col-md-4">更新缓存</label>
								<div class="col-md-8">
									<label class="checkbox-inline col-">
										<input type="checkbox" value="data" name='option1' id="inlineCheckbox1" checked="checked"> 更新缓存
									</label>
								</div>
							</div>

							<button id="submit" class="btn btn-success col-md-2" type="submit" style="width:100%;">提交</button>
						</form>
					</div>
				</div>
			</div>
		</div>

	</div>
@endsection