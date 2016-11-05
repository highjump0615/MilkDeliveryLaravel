@extends('weixin.layout.master')
@section('title','支付成功')
@section('css')

@endsection
@section('content')
      <div class="top">
            <h1>支付成功</h1>
            <a class="topa1" href="jvascript:void(0)">&nbsp;</a>
            <a class="topa2" href="jvascript:void(0)"></a></div>
      <div class="zfjg">
            <p align="center" ><img src="images/cg.png"></p>
            <p align="center"><b class="cg">支付成功</b></p>
            <p align="center">（我们会马上安排客服进行核实！）</p>
            <p align="center" >
            <form name="form1" method="post" action="新提交待审核.html" style="text-align:center">
                  <input type="submit" name="Submit" value="查看订单" class="jxzf" style="background:#f28b4d">
            </form>
      </div>

      @include('weixin.layout.footer');
@endsection
