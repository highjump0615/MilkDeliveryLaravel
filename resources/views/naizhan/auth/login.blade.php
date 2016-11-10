<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>奶站管理 | 登录</title>

    <link href="<?=asset('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?=asset('font-awesome/css/font-awesome.css') ?>" rel="stylesheet">

    <link href="<?=asset('css/animate.css') ?>" rel="stylesheet">
    <link href="<?=asset('css/style.css') ?>" rel="stylesheet">

</head>

<body style="background-size:cover; background-position: center; background-repeat: no-repeat;" background="<?=asset('img/login-green.jpg') ?>">
    <div class="middle-box text-center animated fadeInDown car" style="padding-top:25%">
		<div class="row">
			<div class="ibox float-e-margins">
				<div class="ibox-content" style="border: 1px solid #10ee00;">
					<form class="form-horizontal" method="post" action="{{url('/naizhan/login')}}">
						{{ csrf_field() }}
						<br>
						<div class="form-group"><label class="col-lg-3 control-label">用户名:</label>
							<div class="col-lg-9"><input type="text" name="name" class="form-control"  value="{{ old('name') }}">
							</div>
						</div>
						<div class="form-group"><label class="col-lg-3 control-label">密 码:</label>
							<div class="col-lg-9"><input type="password" name="password" class="form-control"></div>
						</div>
						@if(Session::has('status'))
							<p style="padding-left: 30px; color: red;">用户名或密码不正确!</p>
						@endif
						<div class="form-group">
							<div class="col-xs-offset-1 col-xs-11">
								<button class="btn btn-primary btn-md" type="submit" style="width: 100px; font-size:15px; font-weight:600;">登录</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
    </div>

        <!-- Mainly scripts -->
    <script src="<?=asset('js/jquery-2.1.1.js') ?>"></script>
    <script src="<?=asset('js/bootstrap.min.js') ?>"></script>


</body>

</html>
