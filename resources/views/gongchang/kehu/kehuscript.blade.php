@section('script')
    <script type="text/javascript">
                @if (!empty($pageName))
        var at_page = '{{$pageName}}';
        @endif

        // 全局变量
        var gnTotalPage = '{{$customers->lastPage()}}';
        var gnCurrentPage = '{{$customers->currentPage()}}';

        gnTotalPage = parseInt(gnTotalPage);
        gnCurrentPage = parseInt(gnCurrentPage);
    </script>
    <script type="text/javascript" src="<?=asset('js/plugins/pagination/jquery.twbsPagination.min.js')?>"></script>
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/pagination.js')?>"></script>

    @if ($isStation)
        <script type="text/javascript" src="<?=asset('js/pages/naizhan/kehu_admin.js')?>"></script>
    @else
        <script type="text/javascript" src="<?=asset('js/pages/gongchang/kehu_admin.js')?>"></script>
    @endif
@endsection