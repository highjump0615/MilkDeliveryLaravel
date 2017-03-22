@extends('gongchang.layout.master')
@section('css')
@endsection
@section('content')
    @include('gongchang.theme.sidebar')
    <div id="page-wrapper" class="white-bg dashbard-1">
        @include('gongchang.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="index.html">系统管理</a>
                </li>
                <li>
                    <a href={{URL::to('/gongchang/xitong/naizhanzhanghao')}}>奶站账号管理</a>
                </li>
                <li class="active">
                    <a href=""><strong>账户详情-查看</strong></a>
                </li>
            </ol>
        </div>

        <div class="row wrapper">
            <div class="wrapper-content">
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-sm-2 control-label">奶站名称:</label>
                        <div class="col-sm-4">
                            <input type="text" placeholder="" class="form-control" value="{{$delivery_station->name}}"
                                   readonly>
                        </div>
                        <div class="col-sm-6"></div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-sm-2 control-label">负责人:</label>
                        <div class="col-sm-4"><input type="text" placeholder="" class="form-control"
                                                     value="{{$delivery_station->boss}}" readonly></div>
                        <div class="col-sm-6"></div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-sm-2 control-label">手 机:</label>
                        <div class="col-sm-4"><input type="text" placeholder="" class="form-control"
                                                     value="{{$delivery_station->phone}}" readonly></div>
                        <div class="col-sm-6"></div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-sm-2 control-label">奶站类型:</label>
                        <div class="col-sm-4"><input type="text" placeholder="" class="form-control"
                                                     value="{{$delivery_station->type}}" readonly></div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-sm-2 control-label">费用结算方式:</label>
                        <div class="col-sm-4"><input type="text" placeholder="" class="form-control"
                                                     value="{{$delivery_station->pay_type}}" readonly></div>
                    </div>
                </div>
                <div class="feed-element mt-20">
                    <div class="vertical-align">
                        <label class="col-sm-2 control-label">结算账户:</label>
                        <div class="col-sm-4">
                            <label class="col-md-4">帐户名:</label>
                            <label class="col-md-8">{{$delivery_station->billing_account_name}}</label>
                            <label class="col-md-4">卡号:</label>
                            <label class="col-md-8">{{$delivery_station->billing_account_card_no}}</label>
                        </div>
                        <label class="col-sm-2 control-label">自由支付账户:</label>
                        <div class="col-sm-4">
                            <label class="col-md-4">帐户名:</label>
                            <label class="col-md-8">{{$delivery_station->freepay_account_name}}</label>
                            <label class="col-md-4">卡号:</label>
                            <label class="col-md-8">{{$delivery_station->freepay_account_card_no}}</label>
                        </div>
                    </div>
                </div>

                <div class="feed-element mt-20">
                    <div class="vertical-align col-md-4">
                        <label class="col-sm-6 control-label text-left">配送业务信用额度:</label>
                        <label class="col-sm-6 control-label text-left">{{$delivery_station->init_delivery_credit_amount}}</label>
                    </div>
                    <div class="vertical-align col-md-4">
                        <label class="col-sm-6 control-label text-left">自营业务信用额度:</label>
                        <label class="col-sm-6 control-label text-left">{{$delivery_station->init_business_credit_amount}}</label>
                    </div>
                    <div class="vertical-align col-md-4">
                        <label class="col-sm-6 control-label text-left">保证金金额:</label>
                        <label class="col-sm-6 control-label text-left">{{$delivery_station->init_guarantee_amount}}</label>
                    </div>
                </div>
                <div class="feed-element mt-20">
                    <div class="vertical-align">
                        <label class="col-sm-2 control-label">收据凭证:</label>
                        <div class="col-sm-4">
                            @if(isset($delivery_station->guarantee_receipt_path))
                                <img class="station_receipt_img" src="<?=asset('/img/station/receipt/'.$delivery_station->guarantee_receipt_path)?>"/>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <hr>
                </div>
                {{--<div class="feed-element">--}}
                    {{--<div class="vertical-align">--}}
                        {{--<label class="col-sm-2 control-label">账户分类:</label>--}}
                        {{--<div class="col-sm-4"><input type="text" placeholder="" class="form-control"--}}
                                                     {{--value="{{$delivery_station->user_type}}" readonly></div>--}}

                    {{--</div>--}}
                {{--</div>--}}
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-sm-2 control-label">账号:</label>
                        <div class="col-sm-4"><input type="text" placeholder="" class="form-control"
                                                     value="{{$delivery_station->getUser()->name}}" readonly></div>
                        <div class="col-sm-6"></div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-sm-2 control-label">密码:</label>
                        <div class="col-sm-4"><input type="text" placeholder="" class="form-control" value="**********"
                                                     readonly></div>
                        <div class="col-sm-6"></div>
                    </div>
                </div>
                <div>
                    <hr>
                </div>
                <div class="wrapper">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="wrapper-content">
                            <table class="table table-bordered white-bg">
                                <thead>
                                <tr>
                                   <th data-sort-ignore="true">配送范围</th>
                                   <th data-sort-ignore="true">小区</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($delivery_area as $street=>$st)
                                    <tr>
                                        <td class="col-sm-2">{{$street}}</td>
                                        <td>
                                            @foreach($st as $s)
                                                <div class="col-sm-3" style="padding-bottom:5px;">{{$s}}</div>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('script')
@endsection