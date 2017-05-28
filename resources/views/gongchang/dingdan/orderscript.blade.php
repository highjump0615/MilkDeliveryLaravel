@section('script')
    <script type="text/javascript">
        @if (!empty($pageName))
            var at_page = '{{$pageName}}';
        @endif

        // 全局变量
        var gnTotalPage = '{{$orders->lastPage()}}';
        var gnCurrentPage = '{{$orders->currentPage()}}';

        gnTotalPage = parseInt(gnTotalPage);
        gnCurrentPage = parseInt(gnCurrentPage);
    </script>

    <script type="text/javascript" src="<?=asset('js/plugins/pagination/jquery.twbsPagination.min.js')?>"></script>
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/pagination.js')?>"></script>
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/order_select_export_print.js')?>"></script>
    <script src="<?=asset('js/pages/gongchang/order_list_filter.js') ?>"></script>
@endsection