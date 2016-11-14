@extends('weixin.layout.master')
@section('title','我的评价')
@section('css')

@endsection
@section('content')

      <div class="top">
            <h1>订单列表</h1>
            <a class="topa1" href="jvascript:void(0)">&nbsp;</a>
            <a class="topa2" href="jvascript:void(0)"></a></div>

      <div class="pj_t">订单号：602178888</div>

      <div class="pj_img">
            <a href="#"><img src="images/23_03.png" border="0"></a>
            <a href="#"><img src="images/23_03.png" border="0"></a>

            <span><a href="#">共3种></a></span>

      </div>



      <div class="evali pa2">

            <div class="start"><b>整体评价</b>
                  <span @if($marks < 1) class="nostart" @endif></span>
                  <span @if($marks < 2) class="nostart" @endif></span>
                  <span @if($marks < 3) class="nostart" @endif></span>
                  <span @if($marks < 4) class="nostart" @endif></span>
                  <span @if($marks < 5) class="nostart" @endif></span>
            </div>
            <div class="evaxx">
                  &nbsp;&nbsp;&nbsp;&nbsp;{{$content}}
            </div>
      </div>


      <div class="he50"></div>
      <div class="menu">
            <ul class="menul">
                  <li class="curr">
                        <a class="curr" href="javascript:void(0)"><i class="mni1"></i>奶吧</a>
                  </li>
                  <li>
                        <a class="" href="javascript:void(0)"><i class="mni2"></i>快速订奶</a>
                  </li>
                  <li>
                        <a class="" href="javascript:void(0)"><i class="mni3"></i>购物车<span class="cartn">1</span></a>
                  </li>
                  <li>
                        <a class="" href="javascript:void(0)"><i class="mni4"></i>个人中心</a>
                  </li>
            </ul>
      </div>

@endsection
@section('script')
@endsection