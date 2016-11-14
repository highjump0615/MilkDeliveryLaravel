@extends('weixin.layout.master')
@section('title','投诉建议')
@section('css')
    <link rel="stylesheet" href="<?=asset('weixin/css/swiper.min.css')?>">
@endsection
@section('content')

    <div class="top">
        <h1>投诉建议</h1>
        <a class="topa1" href="javascript:history.back()">&nbsp;</a>
        <a class="topa2" href="javascript:void(0)"></a>
    </div>


    <ul class="tsjy_ul">
        <li><span>{{$phone1}}</span>客服电话：</li>

        <li><span>{{$phone2}}</span>投诉电话：</li>
    </ul>


    @include('weixin.layout.footer')


@endsection
