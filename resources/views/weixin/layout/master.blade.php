<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="description" content="订牛奶商城"/>
    <meta name="keywords" content="订牛奶商城"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=yes" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta name="format-detection" content="telphone=no, email=no"/>
    <title>@yield('title')</title>

    <link href="<?=asset('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?=asset('font-awesome/css/font-awesome.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?=asset('weixin/css/jquery-ui.css')?>">
    <link rel="stylesheet" href="<?=asset('weixin/css/style.css')?>">
    <link rel="stylesheet" href="<?=asset('weixin/css/swiper.min.css')?>">

    @yield('css')

</head>
<body>
@yield('content')
<script src="<?=asset('weixin/js/jquery-1.10.1.min.js')?>"></script>
<script src="<?=asset('weixin/js/jquery-ui.js')?>"></script>
<script src="<?=asset('js/bootstrap.min.js')?>"></script>
<script src="<?=asset('js/global.js')?>"></script>
<script src="<?=asset('js/plugins/notify/notify.min.js') ?>"></script>
<script src="<?=asset('weixin/js/moment.min.js')?>"></script>
<script src="<?=asset('weixin/js/swiper.min.js')?>"></script>
<script src="<?=asset('weixin/js/common.js')?>"></script>
@yield('script')

</body>
</html>
