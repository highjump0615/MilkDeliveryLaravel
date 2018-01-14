@extends('weixin.layout.master')
@section('title','分享')

@section('css')
<style>
    .body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
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
        margin-top: 50px;
        display: flex;
        flex-direction: column;
        font-size: 9pt;
        font-weight: 200;
    }
    .content span {
    }
    .red-note {
        color: red;
        font-size: 12pt;
        text-align: center;
        margin-top: 10px;
    }
    .img-bonus {
        width: 150px;
        height: 77px;
        align-self: flex-end;
    }
    .qrcode {
        width: 220px;
        height: 220px;
    }
</style>
@endsection

@section('content')
    <div class="top">
        <h1>分享给好友</h1>
    </div>

    <div class="body">

        <div class="content">
            <span>1、长按二维码关注公众号"塞因苏有机到家"，线上订奶即可成为会员</span>
            <span>2、分享二维码到朋友圈，让好友也来分享来自基茵苏的新鲜牛奶</span>
            <span>3、好友在线上订奶，分享朋友圈的你就可以收到超值红包</span>
            <span class="red-note">把爱传下去快快行动起来哦~</span>
        </div>
        <img class="img-bonus" src="{{asset('/img/wechat/bonus.png')}}" />

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
            // 发送给朋友
            wx.onMenuShareAppMessage({
                title: '塞茵苏有机到家',
                desc: '圣牧低温牛奶',
                link: strUrlDecoded,
                imgUrl: '{{url("img/mark.png")}}',
                success: function() {
                },
                cancel: function() {
                }
            });
            // 朋友圈
            wx.onMenuShareTimeline({
                title: '塞茵苏有机到家',
                desc: '圣牧低温牛奶',
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