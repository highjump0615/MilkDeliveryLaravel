<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Basic Page Needs -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title')</title>

    <link href="<?=asset('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?=asset('font-awesome/css/font-awesome.css') ?>" rel="stylesheet">

    <!-- Toastr style -->
    <link href="<?=asset('css/plugins/toastr/toastr.min.css')?>" rel="stylesheet">

    <!-- Gritter -->
    <link href="<?=asset('js/plugins/gritter/jquery.gritter.css')?>" rel="stylesheet">
    <link href="<?=asset('js/plugins/jquery-ui/jquery-ui.css')?>" rel="stylesheet">

    <link href="<?=asset('css/plugins/datepicker/datepicker3.css')?>" rel="stylesheet">
    <link href="<?=asset('css/plugins/daterangepicker/daterangepicker-bs3.css')?>" rel="stylesheet">

    <link href="<?=asset('css/animate.css') ?>" rel="stylesheet">
    <link href="<?=asset('css/style.css') ?>" rel="stylesheet">

    <link href="<?=asset('css/plugins/added/switchery.css') ?>" rel="stylesheet">
    <link href="<?=asset('css/plugins/footable/footable.core.css') ?>" rel="stylesheet">

    {{--<i-check box css>--}}
    <link href="<?=asset('css/plugins/iCheck/custom.css') ?>" rel="stylesheet">

    <!-- Multi Select Combo -->
    <link href="<?=asset('css/plugins/chosen/chosen.css') ?>" rel="stylesheet">
    <link href="<?=asset('css/plugins/select2/select2.min.css') ?>" rel="stylesheet">


    <link href="<?=asset('css/animate.css') ?>" rel="stylesheet">
    <link href="<?=asset('css/style.css') ?>" rel="stylesheet">
    <link href="<?=asset('css/custom.css') ?>" rel="stylesheet">

    @yield('css')

</head>
<body>
@yield('content')
<!-- Mainly scripts -->
<script src="<?=asset('js/global.js')?>"></script>
<script src="<?=asset('js/jquery-2.1.1.js') ?>"></script>
<script src="<?=asset('js/bootstrap.min.js') ?>"></script>
<script src="<?=asset('js/plugins/metisMenu/jquery.metisMenu.js') ?>"></script>
<script src="<?=asset('js/plugins/slimscroll/jquery.slimscroll.min.js') ?>"></script>

<!-- Flot -->
<script src="<?=asset('js/plugins/flot/jquery.flot.js') ?>"></script>
<script src="<?=asset('js/plugins/flot/jquery.flot.tooltip.min.js') ?>"></script>
<script src="<?=asset('js/plugins/flot/jquery.flot.spline.js') ?>"></script>
<script src="<?=asset('js/plugins/flot/jquery.flot.resize.js') ?>"></script>
<script src="<?=asset('js/plugins/flot/jquery.flot.pie.js') ?>"></script>

<!-- Peity -->
<script src="<?=asset('js/plugins/peity/jquery.peity.min.js') ?>"></script>
<script src="<?=asset('js/demo/peity-demo.js') ?>"></script>

<!-- Custom and plugin javascript -->
<script src="<?=asset('js/inspinia.js') ?>"></script>
<script src="<?=asset('js/plugins/pace/pace.min.js') ?>"></script>

<!-- jQuery UI -->
<script src="<?=asset('js/plugins/jquery-ui/jquery-ui.min.js') ?>"></script>

<!-- GITTER -->
<script src="<?=asset('js/plugins/gritter/jquery.gritter.min.js') ?>"></script>

<!-- Sparkline -->
<script src="<?=asset('js/plugins/sparkline/jquery.sparkline.min.js') ?>"></script>

<!-- Sparkline demo data  -->
<script src="<?=asset('js/demo/sparkline-demo.js') ?>"></script>

<!-- ChartJS-->
<script src="<?=asset('js/plugins/chartJs/Chart.min.js') ?>"></script>

<!-- Toastr -->
<script src="<?=asset('js/plugins/toastr/toastr.min.js') ?>"></script>

<!-- Switchery -->
<!--   <script src="js/plugins/switchery/switchery.js"></script>-->
<script src="<?=asset('js/plugins/added/switchery.js') ?>"></script>

<!-- Date range picker -->
<script src="<?=asset('js/plugins/daterangepicker/daterangepicker.js') ?>"></script>
<!-- Data picker -->
<script src="<?=asset('js/plugins/datepicker/bootstrap-datepicker.js') ?>"></script>
<!-- FooTable -->
<script src="<?=asset('js/plugins/footable/footable.all.min.js') ?>"></script>

<!-- Icheck -->
<script src="<?=asset('js/plugins/iCheck/icheck.min.js') ?>"></script>

<!-- jquery confirm --->
<script src="<?=asset('js/plugins/confirm/jquery.confirm.min.js') ?>"></script>

<!-- Jquery Nofity-->
<script src="<?=asset('js/plugins/notify/notify.min.js') ?>"></script>

<script src="<?=asset('js/plugins/added/common.js') ?>"></script>

@yield('script')
</body>
</html>