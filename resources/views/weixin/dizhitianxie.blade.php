@extends('weixin.layout.master')
@section('title','填写收货地址')
@section('css')
    <link rel="stylesheet" href="<?=asset('weixin/css/city-picker.css')?>">
    <link rel="stylesheet" href="<?=asset('weixin/css/city-main.css')?>">
@endsection
@section('content')
    <header>
        <a class="headl fanh" href="{{url('weixin/dizhiliebiao')}}"></a>
        <h1>填写收货地址</h1>
    </header>

    <div class="addrbox">
        <form method="post" action="{{url('/weixin/dizhitianxie')}}">
            <input type="hidden" name="wxuser_id" value="{{$wxuser_id}}">
            @if(isset($address_id))
            <input type="hidden" name="address_id" value="{{$address_id}}">
            @endif
            <ul class="adrul">
                <li>
                    <label>收货人：</label><input required name="name" type="text" value="{{$name}}">
                </li>
                <li>
                    <label>电话：</label><input required pattern = "^1[345678][0-9]{9}$" name="phone" type="text" value="{{$phone}}">
                </li>
                <li>
                    <div style="position: relative;">
                        <span>所在地区：</span>
                        <div style="display: inline-block; ">
                            <input required id="city-picker3" name="address" class="form-control" type="text"
                                   value="{{$address}}" data-toggle="city-picker">
                        </div>
                    </div>


                </li>
                <li>
                    <label>门牌号：</label><input required name="sub_address" type="text" placeholder="如：7-3-503"
                                              value="{{$sub_address}}">
                </li>

                <li>
                    <label>设为默认地址：</label>
                <span class='tg-list-item'>
                <input name="primary" class='tgl tgl-ios' id='cb2' type='checkbox'
                       @if($primary)
                       checked
                        @endif
                >
                <label class='tgl-btn' for='cb2'></label>
                </span>
                </li>
            </ul>

            <input type="submit" class="ordqr" value="确认">
        </form>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        <?php
        $avail = json_encode($address_list);
        echo "var ChineseDistricts = " . $avail;
        ?>
    </script>
    <script src="<?=asset('weixin/js/city-picker.js')?>"></script>
    <script src="<?=asset('weixin/js/city-main.js')?>"></script>
@endsection