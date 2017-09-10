@extends('weixin.layout.master')
@section('title','分享')

@section('css')
<style>
    .body {
        padding: 10px;
    }
    .title img {
        width: 40px;
        height: 40px;
        border-radius: 20px;
        -moz-border-radius: 20px;
        float: left;
    }
    .title span {
        line-height: 40px;
        margin-left: 10px;
    }
    .content {
        margin-top: 20px;
    }
    .qrcode {
        width: 220px;
        height: 220px;
    }
    .qrcode {
        margin-top: 30px;
        margin-left: auto;
        margin-right: auto;
    }
</style>
@endsection

@section('content')
    <div class="top">
        <h1>分享给好友</h1>
    </div>

    <div class="body">
        <div class="title">
            <img src="{{$user->image_url}}">
            <span><b>{{$user->name}}</b>推荐送奶平台</span>
        </div>

        <h4 class="content">
            长按二维码关注公众号，红包满满。。。
        </h4>

        <div class="qrcode">
            <img src="{{$qrcode}}">
        </div>
    </div>
@endsection

@section('script')

    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <script type="text/javascript">

        @if (!empty($appid))
        wx.config({
            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            appId: '{{$appid}}', // 必填，公众号的唯一标识
            timestamp: '{{$timestamp}}', // 必填，生成签名的时间戳
            nonceStr: '{{$nonce}}', // 必填，生成签名的随机串
            signature: '{{$signature}}',// 必填，签名，见附录1
            jsApiList: [
                'checkJsApi',
                'onMenuShareTimeline',
                'onMenuShareAppMessage'
            ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
        });

        // Decode url
        var strUrlEncoded = '{{$url}}';
        var elem = document.createElement('textarea');
        elem.innerHTML = strUrlEncoded;
        var strUrlDecoded = elem.value;

        wx.ready(function() {
            wx.onMenuShareAppMessage({
                title: '食尚新鲜',
                desc: '圣母低温牛奶',
                link: strUrlDecoded,
                imgUrl: '{{url("img/mark.png")}}',
                success: function() {
                },
                cancel: function() {
                }
            });
        });
        @endif
    </script>

@endsection