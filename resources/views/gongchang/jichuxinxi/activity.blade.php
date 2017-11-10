@extends('gongchang.layout.master')

@section('css')
    <style>
        #but_save {
            margin-top: 15px;
        }
        form {
            padding: 0;
        }
    </style>
@endsection

@section('content')
    @include('gongchang.theme.sidebar')
    <div id="page-wrapper" class="dashbard-1">
        @include('gongchang.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">基础信息管理</a>
                </li>
                <li>
                    <a href={{URL::to('/gongchang/jichuxinxi/activity')}}><strong>活动内容</strong></a>
                </li>
            </ol>
        </div>

        <div class="wrapper-content">
            @if (!empty($status))
            <div class="alert alert-success alert-dismissable">
                <button area-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <span>保存成功</span>
            </div>
            @endif

            <form id="form_content" action="{{url('/gongchang/jichuxinxi/activity')}}" method="post">
                <div id="editor" type="text/plain" style="width:100%;height:600px;"></div>
                <input type="hidden" name="content" />

                <button class="btn btn-success" id="but_save">保存</button>
            </form>
        </div>
    </div>
@endsection

@section('script')

    <!-- UE Editor -->
    <script type="text/javascript" charset="utf-8" src="<?=asset('ueditor/ueditor.config.js')?>"></script>
    <script type="text/javascript" charset="utf-8" src="<?=asset('ueditor/ueditor.all.min.js')?>"></script>
    <script type="text/javascript" charset="utf-8" src="<?=asset('ueditor/lang/zh-cn/zh-cn.js')?>"></script>

    <script type="text/javascript" defer src="<?=asset('js/pages/gongchang/ueditor_setting.js')?>"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // 填充内容
            @if (!empty($content))
            var ue_data = $.parseHTML("{{$content}}");
            show_ue_content(ue_data);
            @endif

            // 提交
            $('#form_content').submit(function() {
                var uecontent = ue_getContent();
                $("input[name=content]").val(uecontent);

                return true;
            });
        });
    </script>

@endsection