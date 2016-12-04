@extends('weixin.layout.master')
@section('title','消息中心')
@section('css')

@endsection
@section('content')

    <div class="top">
        <h1>消息中心</h1>
        <a class="topa1" href="{{url('/weixin/gerenzhongxin')}}">&nbsp;</a>
    </div>

    <ul class="tsjy_ul">
        @if(count($reviews) == 0)
            <p class="no_data"> 没有消息 </p>
        @else
            @foreach($reviews as $rw)
                <li><span>{{$rw->created_at}}</span><a href="#">{{$rw->content}}</a></li>
            @endforeach
        @endif
    </ul>


    <div class="height70"></div>

    @include('weixin.layout.footer')
@endsection