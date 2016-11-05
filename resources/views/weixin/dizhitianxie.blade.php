@extends('weixin.layout.master')
@section('title','填写收货地址')
@section('css')

@endsection
@section('content')
    <header>
        <a class="headl fanh" href="javascript:void(0)"></a>
        <h1>填写收货地址</h1>
    </header>

    <div class="addrbox">
        <ul class="adrul">
            <li>
                <label>收货人：</label><input name="" type="text">
            </li>
            <li>
                <label>电话：</label><input name="" type="text">
            </li>
            <li>
                <label>所在地区：</label><a class="adrxz" href="javascript:void(0);">请选择</a>
            </li>
            <li>
                <label>门牌号：</label><input name="" type="text" placeholder="如：7-3-503">
            </li>

            <li>
                <label>设为默认地址：</label>
                <span class='tg-list-item'>
                <input class='tgl tgl-ios' id='cb2' type='checkbox'>
                <label class='tgl-btn' for='cb2'></label>
                </span>
            </li>
        </ul>
        <a class="ordqr" href="天天送.html">确认</a>
    </div>
@endsection
@section('script')
@endsection