@extends('weixin.layout.master')
@section('title','支付失败')
@section('css')

@endsection
@section('content')

    <header>
        <a class="headl fanh" href="{{url('weixin/querendingdan')}}"></a>
        <h1>支付失败</h1>
    </header>

    <div class="zfjg">
        <p align="center"><img src="images/sb.png"></p>
        <p align="center"><b>支付失败</b></p>
        <p align="center"><input type="submit" name="Submit" value="继续支付" class="jxzf" onclick="window.location.href='{{url('/weixin/querendingdan')}}'"></p>
    </div>
    @include('weixin.layout.footer');
@endsection
@section('script')
@endsection