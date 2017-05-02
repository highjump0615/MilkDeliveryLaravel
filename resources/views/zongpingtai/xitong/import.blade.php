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
                        <form id="upload-form" method="post" action="{{url('zongpingtai/xitong/import/upload')}}" enctype="multipart/form-data">

                            {{-- 导入数据类型 --}}
                            <input type="hidden" name="type" id="input-type" value="0" />

                            <button type="button" class="btn btn-success btn-outline" id="btn-order">
                                订单数据导入
                            </button>

                            <button type="button" class="btn btn-success btn-outline" id="btn-customer">
                                客户账户余额数据导入
                            </button>

                            {{-- 只接受excel文件 --}}
                            <input type="file" name="upload" id="input-upload"
                                   accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"/>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="<?=asset('js/pages/zongpingtai/import.js')?>"></script>
@endsection