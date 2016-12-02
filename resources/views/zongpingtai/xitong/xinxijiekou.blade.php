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
                                <div class="col-xs-12 col-md-12">短信接口<span style='margin-left: 20%;color:#1c84c6;'>{{$mass}}</span></div>
                            </div>
                           <div class="box-body "> 
                               <form action="{{url('zongpingtai/xitong/xinxijiekou')}}" method="post" enctype="multipart/form-data">
                                    <table id="example2" class="table table-bordered table-hover" align="center">
                                        <thead>
                                          <tr>
                                            <th class="col-xs-4">  属性名称  </th>			
                                            <th class="col-xs-8" style="text-align: left;"> 属性值 </th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                            <tr role="row" class="odd">
                                                <td>短信网址</td>
                                                <td>
                                                    <input value="{{$yimeiurl}}" style="width:100%;" name="yimeiurl" type="text">
                                                </td>
                                            </tr>
                                            <tr role="row" class="odd">
                                                <td>短信账号</td>
                                                <td>
                                                    <input value="{{$yimeiurlserial}}" style="width:100%;" name="yimeiurlserial" type="text">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>短信密码</td>
                                                <td>
                                                    <input value="{{$yimeiurlpassword}}" style="width:100%;" name="yimeiurlpassword" type="text">
                                                </td>
                                            </tr>
                                            <tr>
                                              <td colspan="2">
                                                   <button class="btn btn-large btn-info" style="width:120px;font-size: 15px;background-color:#1c84c6;" type="submit" style="width:100%;">修改</button>   
                                              </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                    
			</div>
		</div>
		
	</div>
@endsection
