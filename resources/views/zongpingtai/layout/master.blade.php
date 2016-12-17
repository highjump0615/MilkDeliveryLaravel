<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Basic Page Needs
    ================================================== -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title')</title>

    <link href="<?=asset('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?=asset('font-awesome/css/font-awesome.css') ?>" rel="stylesheet">

    <link href="<?=asset('css/plugins/datepicker/datepicker3.css')?>" rel="stylesheet">
    <link href="<?=asset('css/plugins/daterangepicker/daterangepicker-bs3.css') ?>" rel="stylesheet">

    <link href="<?=asset('css/plugins/added/switchery.css') ?>" rel="stylesheet">
    <link href="<?=asset('css/plugins/footable/footable.core.css') ?>" rel="stylesheet">

    {{--<i-check box css>--}}
    <link href="<?=asset('css/plugins/iCheck/custom.css') ?>" rel="stylesheet">

    <link href="<?=asset('css/animate.css') ?>" rel="stylesheet">
    <link href="<?=asset('css/style.css') ?>" rel="stylesheet">
    <link href="<?=asset('css/custom.css') ?>" rel="stylesheet">

    @yield('css')

</head>
<body>
	@yield('content')

    <script src="<?=asset('js/global.js')?>"></script>
    <script src="<?=asset('js/jquery-2.1.1.js') ?>"></script>
    <script src="<?=asset('js/bootstrap.min.js') ?>"></script>
    <script src="<?=asset('js/plugins/metisMenu/jquery.metisMenu.js') ?>"></script>

    <!-- Custom and plugin javascript -->
    <script src="<?=asset('js/inspinia.js') ?>"></script>
    <script src="<?=asset('js/plugins/pace/pace.min.js') ?>"></script>

    <!-- Switchery -->
    <script src="<?=asset('js/plugins/added/switchery.js') ?>"></script>

    <!-- Date picker and Date Range Picker-->
    <script src="<?=asset('js/plugins/daterangepicker/daterangepicker.js') ?>"></script>
    <script src="<?=asset('js/plugins/datepicker/bootstrap-datepicker.js') ?>"></script>

    <!-- Icheck -->
    <script src="<?=asset('js/plugins/iCheck/icheck.min.js') ?>"></script>

    <!-- FooTable -->
    <script src="<?=asset('js/plugins/footable/footable.all.min.js') ?>"></script>

    <!-- Jquery Nofity-->
    <script src="<?=asset('js/plugins/notify/notify.min.js') ?>"></script>

    <script src="<?=asset('js/plugins/added/common.js') ?>"></script>

    <!-- Jquery confirm-->
    <script src="<?=asset('js/plugins/confirm/jquery.confirm.min.js') ?>"></script>

    <!-- 打印插件 -->
    <script src="<?=asset('js/plugins/jQuery.print.js') ?>"></script>

    <!-- 导出 -->
    <script src="<?=asset('js/tableexport.js') ?>"></script>

    @yield('script')
</body>
</html>