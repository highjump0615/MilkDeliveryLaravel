<div class="row bgimg">
	<div style="margin: 10px;">
	 <!--				<nav class="navbar navbar-static-top align-center" role="navigation" style="margin-bottom: 0;">
						<div class="navbar-header col-lg-1 col-md-1 col-sm-2" style="padding-top:5px;">
							<a class="navbar-minimalize minimalize-styl-2 btn btn-success " href="#"><i class="fa fa-bars"></i> </a>
						</div>
						<div class="col-lg-11 col-md-11 col-sm-10 col-xs-10">-->
		<h1 class="top-title">牛奶配送ERP管理系统</h1>
	<!--					</div>
					</nav>-->
	</div>	
	<div class="border-bottom white-bg">
		<div class="col-lg-6 col-md-6 " style="padding-top:5px;">
		</div>


		<div class="col-lg-6 col-md-6 " style="padding-top:5px;">
			<div class="col-lg-5 col-md-5 col-xs-5">
				{{--<p>--}}
					<label  style="font-size: 14px;">用户：</label>
					<label id="user_id"  style="font-size: 16px;">{{Auth::guard('zongpingtai')->user()->name}}</label>
				{{--</p>--}}
			</div>
			<div class="col-lg-5 col-md-5 col-xs-5">
				{{--<p>--}}
					<label  style="font-size: 14px;">登录时间：</label>
					<label id="login_time"  style="font-size: 16px;">{{Auth::guard('zongpingtai')->user()->updated_at}}</label>
				{{--</p>--}}
			</div>
			<div class="col-lg-2 col-md-2">
				<a href="{{url('zongpingtai/logout')}}"  style="font-size: 16px;"><i class="glyphicon glyphicon-log-out"></i>登出</a>
			</div>
		</div>
	</div>
</div>