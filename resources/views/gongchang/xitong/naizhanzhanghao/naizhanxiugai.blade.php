@extends('gongchang.layout.master')
@section('css')
    <!--Multi Selector-->
    <link href="<?=asset('css/plugins/select2/select2.min.css') ?>" rel="stylesheet">
    <link href="<?=asset('css/plugins/chosen/chosen.css') ?>" rel="stylesheet">

    <style type="text/css">

        select {
            height: 35px;
            width: 100%;
        }

        span.select2 {
            width: 100%;
        }

        .input_hidden {
            position: absolute;
            left: -9999px;
        }

        .unselected {
            opacity: 0.4;
        }

        .selected {
            background-color: #888;
            opacity: 1;
        }

        .account_img_div label {
            display: inline-block;
            cursor: pointer;
        }

        .account_img_div label:hover {
            background-color: #ccc;
        }

        .account_img_div label img {
            padding: 3px;
        }

        .img-upload {
            line-height: 20px;
            font-size: 20px;
            position: absolute;
            opacity: 0;
            z-index: 10;
            max-width: 80px;
            top: 0px;
        }

        .img-preview {

            height: 300px;
            width: 300px;
            position: relative;
            overflow: hidden;
            background-color: #ffffff;
            margin-left: 10px;
            color: #ecf0f1;
            border: 1px solid gray;
            display: none;
            background-size: contain;
            background-repeat: no-repeat;
        }

        div.file-panel {
            position: absolute;
            height: 0;
            filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0, startColorstr='#80000000', endColorstr='#80000000') \0;
            background: rgba(0, 0, 0, 0.5);
            width: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: 300;
        }

        div.file-panel span.cancel {
            background-position: -48px -24px;
        }

        div.file-panel span {
            width: 24px;
            height: 24px;
            display: inline;
            float: right;
            text-indent: -9999px;
            overflow: hidden;
            margin: 5px 1px 1px;
            cursor: pointer;
            background: url( {{ URL::asset('js/plugins/simpleimgupload/icons.png') }}) no-repeat;
        }

        .remove_delivery_area {
            font-size: 22px;
            line-height: 35px;
        }

        .remove_delivery_area:hover, .remove_delivery_area:active {
            cursor: pointer;
        }

        #errmsg {
            display: none;
            padding: 5px 15px;
        }

        .receipt {
            width: 300px;
            height: auto;
            border: 1px solid #f0f0f0;
        }

        #origin_delivery_area {
            display: none;
        }

        .delivery_xiaoqu_tb .col-sm-3 {
            text-align: left;
        }
    </style>
@endsection
@section('content')
    @include('gongchang.theme.sidebar')
    <div id="page-wrapper" class="white-bg dashbard-1">
        @include('gongchang.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">系统管理</a>
                </li>
                <li>
                    <a href={{URL::to('/gongchang/xitong/naizhanzhanghao')}}>奶站账号管理</a>
                </li>
                <li>
                    <a href=""><strong>修改奶站账户</strong></a>
                </li>
            </ol>

            <div class="row wrapper">
                <div class="wrapper-content">
                    <form method="post" id="station_update_form" class="form-horizontal" enctype="multipart/form-data">
                        <input required type="hidden" name="station_id" value="{{$station->id}}">
                        <div class="feed-element">
                            <div class="vertical-align">
                                <label class="col-md-2 control-label">奶站名称:</label>
                                <div class="col-md-4"><input required name="st_name" id="st_name" type="text"
                                                             value="{{$station->name}}" class="form-control"></div>
                            </div>
                        </div>
                        <div class="feed-element">
                            <div class="vertical-align">
                                <label class="col-md-2 control-label">地址:</label>
                                <div class="col-md-2">
                                    <select name="select_province" id="select_province"
                                            class="province_list form-control">
                                        @if (isset($province))
                                            @for ($i = 0; $i < count($province); $i++)
                                                @if($station->province_name == $province[$i]->name)
                                                    <option data-station-name="{{$station->province_name}}"
                                                            value="{{$province[$i]->name}}"
                                                            selected>{{$province[$i]->name}}</option>
                                                @else
                                                    <option value="{{$province[$i]->name}}">{{$province[$i]->name}}</option>
                                                @endif
                                            @endfor
                                        @else
                                            <option value="none">省</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="select_city" id="select_city" class="city_list form-control">
                                        <option value="none">市</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="select_district" class="district_list form-control"
                                            name="select_district">
                                        <option value="none">区&emsp;（县）</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input required name="select_street_xiaoqu" id="select_street_xiaoqu" type="text"
                                           placeholder="手动填写" style="width: 80%;" class="form-control"
                                           value="{{$station->sub_address}}"/>
                                </div>
                            </div>
                        </div>
                        <div class="feed-element">
                            <div class="vertical-align">
                                <label class="col-md-2 control-label">负责人:</label>
                                <div class="col-md-4"><input required type="text" name="st_boss" id="st_boss"
                                                             class="form-control" value="{{$station->boss}}"></div>
                            </div>
                        </div>
                        <div class="feed-element">
                            <div class="vertical-align">
                                <label class="col-md-2 control-label">电话:</label>
                                <div class="col-md-4"><input required type="" name="st_phone" id="st_phone"
                                                             class="form-control"
                                                             value="{{$station->phone}}"></div>
                                <div class="col-md-6"></div>
                            </div>
                        </div>
                        <div class="feed-element">
                            <div class="vertical-align">
                                <label class="col-md-2 control-label">奶站类型:</label>
                                <div class="col-md-3">
                                    <select class="form-control" name="st_type" id="station_type" style="height:35px;"
                                            tabindex="2">
                                        @if (isset($station_type))
                                            @foreach ($station_type as $station_tt)
                                                @if($station->station_type == $station_tt->id)
                                                    <option value="{{$station_tt->id}}"
                                                            selected>{{$station_tt->name}}</option>
                                                @else
                                                    <option value="{{$station_tt->id}}">{{$station_tt->name}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="奶站">奶站</option>
                                            <option value="代理商">代理商</option>
                                            <option value="渠道">渠道</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="feed-element">
                            <div class="vertical-align">
                                <label class="col-md-2 control-label">费用结算方式:</label>
                                <div class="col-md-3">
                                    <select class="form-control" id="fee_settle" name="fee_settle" style="height:35px;"
                                            tabindex="2">
                                        @if (isset($calctype))
                                            @foreach($calctype as $ct)
                                                @if($station->payment_calc_type == $ct->id)
                                                    <option value="{{$ct->id}}" selected>{{$ct->name}}</option>
                                                @else
                                                    <option value="{{$ct->id}}">{{$ct->name}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="配送费结算">配送费结算</option>
                                            <option value="差价结算">差价结算</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="feed-element mt-20">
                            <label class="col-md-2 control-label">结算账户:</label>
                            <div class="col-md-8">
                                <div class="vertical-align">
                                    <label class="col-md-2 control-label">开户行</label>
                                    <div class="col-md-3"><input required type="" id="settle_account_name"
                                                                 name="settle_account_name" class="form-control"
                                                                 value="{{$station->billing_account_name}}"></div>
                                    <label class="col-md-2 control-label">卡号</label>
                                    <div class="col-md-3"><input required type="" id="settle_account_card"
                                                                 name="settle_account_card" class="form-control"
                                                                 value="{{$station->billing_account_card_no}}"></div>
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                        <div class="feed-element">
                            <label class="col-md-2 control-label">自由支付账户:</label>
                            <div class="col-md-8">
                                <div class="vertical-align">
                                    <label class="col-md-2 control-label">帐户名</label>
                                    <div class="col-md-3">
                                        <input required name="free_pay_name" id="free_pay_name"
                                               type="" value="{{$station->freepay_account_name}}"
                                               class="form-control"/>
                                    </div>
                                    <label class="col-md-2 control-label">卡号</label>
                                    <div class="col-md-3">
                                        <input required type="" name="free_pay_card"
                                               value="{{$station->freepay_account_card_no}}"
                                               id="free_pay_card" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                        </div>

                        <div class="feed-element mt-20">
                            <label class="col-md-2 control-label">配送业务信用额度:</label>
                            <div class="col-md-2">
                                <div class="col-md-10">
                                    <input id="init_delivery_credit_amount" name="deliver_business_credit"
                                           class="form-control" style="height:35px;" tabindex="2">
                                    </input>
                                </div>
                                <div class="col-md-2" style="padding:  5px 0;">
                                    <span>元</span>
                                </div>
                            </div>
                            <label class="col-md-2 control-label">自营业务信用额度:</label>
                            <div class="col-md-2">
                                <div class="col-md-10">
                                    <input id="init_business_credit_amount" name="self_business_credit"
                                           class="form-control" style="height:35px;" tabindex="2">
                                    </input>
                                </div>
                                <div class="col-md-2" style="padding:  5px 0;">
                                    <span>元</span>
                                </div>
                            </div>
                            <label class="col-md-2 control-label">保证金金额:</label>
                            <div class="col-md-2">
                                <div class="col-md-10">
                                    <input id="init_guarantee_amount" class="form-control" style="height:35px;"
                                           tabindex="2" name="margin">
                                    </input>
                                </div>
                                <div class="col-md-2" style="padding:  5px 0;">
                                    <span>元</span>
                                </div>
                            </div>
                        </div>

                        <div class="feed-element">

                            <label class="col-md-2 control-label">收据凭证:</label>
                            <div class="col-md-1 image-preview">
                                <button type="button" class="btn btn-success btn-outline" id="img_upload_button">
                                    上传图片
                                </button>
                                <input type="file" name="station_img" class="img-upload"
                                       id="st_img_upload"/>
                            </div>
                            <div class="hide_after_click col-md-4">
                                @if($station->guarantee_receipt_path)
                                    <img class="receipt"
                                         src="<?=asset('/img/station/receipt/' . $station->guarantee_receipt_path)?>">
                                @endif
                            </div>

                            <div id="receipt_voucher_img" class="img-preview">
                                <div class="file-panel" style="height: 0px;">
                                    <span class="cancel">删除</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <hr>
                        </div>
                        <div class="feed-element">
                            <div class="vertical-align">
                                <label class="col-md-2 control-label">账号: </label>
                                <div class="col-md-4"><input required name="user_number" id="user_number" type="text"
                                                             value="{{$station->getUser()->name}}" readonly class="form-control">
                                </div>
                                <div class="col-md-6"></div>
                            </div>
                        </div>
                        <div class="feed-element">
                            <div class="vertical-align">
                                <label class="col-md-2 control-label">密码: </label>
                                <div class="col-md-4"><input name="user_pwd" id="user_pwd" type="password"
                                                             placeholder="********" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="feed-element">
                            <div class="vertical-align">
                                <label class="col-md-2 control-label">确认密码: </label>
                                <div class="col-md-4"><input type="password" name="user_repwd" id="user_repwd"
                                                             placeholder="********" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="feed-element">
                            <div class="vertical-align">
                                <label id="errmsg" class="alert alert-danger col-md-offset-2 col-md-4">密码不匹配</label>
                            </div>
                        </div>
                        <div>
                            <hr>
                        </div>
                        {{--<button class="btn btn-success" type="button" data-action="add_new_area"><i--}}
                        {{--class="fa fa-plus"></i>添加配送范围--}}
                        {{--</button>--}}
                        <div id="origin_delivery_area">
                            <div class="wrapper">
                                <button id="change_delivery_area" type="button" class="btn btn-success">更改配送范围</button>
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
                                                    <td class="col-md-2">{{$street}}</td>
                                                    <td>
                                                        @foreach($st as $s)
                                                            <div class="col-md-3" style="padding-bottom:5px;">
                                                                {{$s['name']}}
                                                            </div>
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

                        <div id="new_delivery_area">
                            <label class="col-md-offset-1">添加配送范围</label>
                            <div class="feed-element" id="delivery_area">
                                <section class="delivery_area_one" id="delivery_area_one">

                                    <div class="vertical-align delivery_area_select">
                                        <label class="col-md-2 control-label">地址:</label>
                                        <div class="col-md-2">
                                            <input type="text" readonly class="province_name form-control"
                                                   value="{{$station->province_name}}" name="area_province[]">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" readonly class="city_name form-control"
                                                   value="{{$station->city_name}}" name="area_city[]"/>
                                        </div>

                                        <div class="col-md-2">
                                            <input type="text" readonly class="district_name form-control"
                                                   value="{{$station->district_name}}" name="area_district[]"/>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="area_street_list" class="chosen-select form-control"
                                                    multiple="multiple" name="area_street[]">
                                            </select>
                                        </div>
                                    </div>

                                    <!-- 配送范围 -->
                                    <div class="wrapper" id="delivery_result">
                                    </div>
                                </section>
                            </div>
                        </div>
                        <div class="wrapper">
                            <div class="col-md-2 col-md-offset-10">
                                <button class="btn btn-success btn-lg" type="submit" style="width:100%;">保存</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- Chosen -->
    <script src="<?=asset('js/plugins/chosen/chosen.jquery.js') ?>"></script>

    <script src="<?=asset('js/plugins/simpleimgupload/jquery.uploadimg.js') ?>"></script>
    <script type="text/javascript">
        var delivery_area_data;
        var station_province, station_city, station_district;

        station_province = "{{$station->province_name}}";
        station_city = "{{$station->city_name}}";
        station_district = "{{$station->district_name}}";

        // 获取配送范围
        var ary_deliver_street = new Array();

        // 街道列表
                @foreach($delivery_area as $street=>$st)

        var obj_deliver_street = [];

        // 街道名称
        obj_deliver_street.name = '{{$street}}';
        obj_deliver_street.xiaoqu = new Array();

        // 小区列表
                @foreach($st as $s)

        var obj_xiaoqu = [];
        obj_xiaoqu.id = '{{$s["id"]}}';
        obj_xiaoqu.name = '{{$s["name"]}}';

        obj_deliver_street.xiaoqu.push(obj_xiaoqu);

        @endforeach

        ary_deliver_street.push(obj_deliver_street);

        @endforeach

        $(document).ready(function () {
            var init_delivery_credit = "{{$station->init_delivery_credit_amount}}";
            var init_business_credit = "{{$station->init_business_credit_amount}}";
            var init_guarantee = "{{$station->init_guarantee_amount}}";

            $('#init_delivery_credit_amount').val(init_delivery_credit);
            $('#init_business_credit_amount').val(init_business_credit);
            $('#init_guarantee_amount').val(init_guarantee);

        })

    </script>
    <script src="<?=asset('js/pages/gongchang/station_update.js') ?>"></script>
@endsection