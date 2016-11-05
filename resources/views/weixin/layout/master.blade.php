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
    <link rel="stylesheet" href="<?=asset('weixin/css/style.css')?>">
    @yield('css')
    <script src="<?=asset('weixin/js/jquery-1.10.1.min.js')?>"></script>
</head>
<body>
@yield('content')
@yield('script')
</body>
</html>
