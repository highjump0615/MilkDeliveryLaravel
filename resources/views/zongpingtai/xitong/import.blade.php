@extends('zongpingtai.layout.master')
@section('css')
    <style>
        input[type=file] {
            width: 1px;
            /*visibility: hidden;*/
            display:none;
        }
    </style>
@endsection
@section('content')
    @include('zongpingtai.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('zongpingtai.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">数据导入</a>
                </li>
            </ol>
        </div>

        <div class="row">

            <div class="ibox float-e-margins">
                <div class="ibox-content">

                    <div class="feed-element">
                        <div class="col-md-3">
                            <form id="upload-form" method="post" action="{{url('zongpingtai/xitong/import/upload')}}" enctype="multipart/form-data">

                                <button type="button" class="btn btn-success btn-outline" id="csv_file_upload_btn">
                                    数据导入
                                </button>

                                {{-- 只接受excel文件 --}}
                                <input type="file" name="upload" id="csv_file_upload_input"
                                       accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
                            </form>
                        </div>
                    </div>

                    @if(Session::has('status'))
                        <div class="row">
                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-1">
                                    <div class="alert alert-success">
                                        {{ Session::get('status') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/naika_admin.js')?>"></script>
@endsection