@extends('weixin.layout.master')
@section('title','活动详情')

@section('css')
<style>
    #uecontent {
        margin: 10px;
    }
</style>
@endsection

@section('content')
    <div class="top">
        <h1>活动详情</h1>
    </div>

    <div id="uecontent"></div>

@endsection

@section('script')

    <script type="text/javascript">

        var contentDetail = '{{$content}}';
        var obj = $('#uecontent');
        obj.html(contentDetail);

        $(obj).each(function () {
            var $this = $(this);
            var t = $this.text();
            $this.html(t.replace('&lt;', '<').replace('&gt;', '>'));
        });

    </script>

@endsection