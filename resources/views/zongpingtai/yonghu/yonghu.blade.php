@extends('zongpingtai.layout.master')

@section('content')
	@include('zongpingtai.theme.sidebar')
	 <div id="page-wrapper" class="gray-bg dashbard-1">
		@include('zongpingtai.theme.header')
		<div class="row border-bottom">
			<ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
				<li class="active">
					<a>用户管理</a>
				</li>
				<li class="active">
					<strong>用户管理</strong>
				</li>
			</ol>
		</div>

		<div class="row wrapper">
			<div class="wrapper-content">
				
				<div class="ibox-content vertical-align">
					<div class="col-lg-3">
						<div class="col-lg-6">
							<a  href="{{url('zongpingtai/yonghu/tianjia')}}" class="btn btn-success btn-outline" type="button" style="width:100%;">添加用户</a> 
						</div>
						{{--<div class="col-lg-6">--}}
							{{--<a  href="" class="btn btn-success btn-outline" type="button" style="width:100%;">用户列表</a> --}}
						{{--</div>--}}
					</div>
				</div>
				<div><hr></div>
				<!--User_Admin_Table-->
                <div class="ibox float-e-margins">
                    <form class="ibox-content">
                    <div class="ibox-content">
                        <table class="table footable table-bordered" data-page-size="10">
                            <thead>
								<tr>
									<th>ID</th>
									<th>公司名称</th>
									<th>账户名</th>
									<th>微信公众号</th>
									<th>最后登录IP</th>
									<th>状态</th>
									<th>管理操作</th>
								</tr>
                            </thead>
                            <tbody>
							<?php $i=0
							?>
							@foreach($factory as $fa)
								<?php $i++;?>
								<tr>
									<td>{{$i}}</td>
									<td>{{$fa->name}}</td>
									<td>{{$fa->factory_id}}</td>
									<td>{{$fa->wechat_id}}</td>
									<td>{{$fa->last_used_ip}}</td>
									<td><input type="checkbox" class="js-switch changeStatus" @if($fa->status == 1)checked @endif value="{{$fa->id}}"/></td>
									<td>
										<a href="{{url('zongpingtai/yonghu/xiangqing')}}/{{$fa->id}}">修改</a>
										&nbsp;
										<a href="{{url('zongpingtai/yonghu/gongzhonghaosheding', $fa->id)}}">页面管理</a>
									</td>
								</tr>
							@endforeach
                            </tbody>
							<tfoot align="right">
								<tr>
									<td colspan="7"><ul class="pagination hide-if-no-paging pull-right"></ul></td>
								</tr>
							</tfoot>
                        </table>
					</div>
                    </form>
                </div>
                
			</div>
		</div>
		
	</div>
@endsection
@section('script')
	<script type="text/javascript">
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function(html) {
            var switchery = new Switchery(html);
        });
		$('.footable').footable();
        $(document).ready(function(){
        	$("td input.js-switch").change(function(){
        		if($(this).is(':checked')){
        			$(this).closest('td').next('td').find('a').unbind('click', false).css('color', '#337ab7');
        		} else {
        			$(this).closest('td').next('td').find('a').bind('click', false).css('color', 'gray');
        		}
        		
        	});
        });

		$(document).on('change','.changeStatus',function (e) {

			var url = API_URL + 'zongpingtai/yonghu/yonghu/changeStatus';
			var id = $(this).val();
			var status = 0;

			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
				}
			});

			e.preventDefault();

			var type = "POST"; //for creating new resource
			if($(this).is(':checked')){
				status = 1;
			}
			else {
				status = 0;
			}

			var formData = {
				id: id,
				status: status
			};

			$.ajax({
				type: type,
				url: url,
				data: formData,
				dataType: 'json',
				success: function (data) {
					//console.log(data);
				},
				error: function (data) {
					console.log('Error:', data);
				}
			});
		});
	</script>
@endsection