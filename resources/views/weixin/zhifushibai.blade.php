@extends('weixin.layout.master')
@section('title','支付失败')
@section('css')

@endsection
@section('content')
    <div class="top">
        <h1>支付失败</h1>
        <a class="topa1" href="jvascript:void(0)">&nbsp;</a>
        <a class="topa2" href="jvascript:void(0)"></a>
    </div>

    <div class="zfjg">
        <p align="center"><img src="images/sb.png"></p>
        <p align="center"><b>支付失败</b></p>
        <p align="center"><input type="submit" name="Submit" value="继续支付" class="jxzf"></p>
    </div>

    @include('weixin.layout.footer');
@endsection
@section('script')
@endsection