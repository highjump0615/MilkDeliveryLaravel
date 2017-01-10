@extends('naizhan.layout.master')

@section('css')

    <style type="text/css">
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

        .hide_after_click{
            height: 300px;
            width: 300px;
            position: relative;
            overflow: hidden;
            background-color: #ffffff;
            margin-left: 10px;
            color: #ecf0f1;
            border: 1px solid gray;
            background-size: contain;
            background-repeat: no-repeat;
            margin-bottom: 10px;
        }

        .img-preview, .vertical-align .img-preview {

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
            margin-bottom: 10px;
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

        .logo {
            width: 300px;
            height: auto;
            border: 1px solid #f0f0f0;
        }

        .receipt {
            width: 300px;
            height: auto;
            border: 1px solid #f0f0f0;
        }

        form {
            padding: 0;
        }

        label {
            padding-top: 5px;
        }

    </style>
@endsection
@section('content')
    @include('naizhan.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('naizhan.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li class="active">
                    <a href="{{ url('naizhan/naizhan')}}">奶站管理</a>
                </li>
                <li class="active">
                    <strong>基本资料</strong>
                </li>
            </ol>
        </div>

        <div class="row wrapper">
            <div class="wrapper-content">
                <form method="post" class="form-vertical" id="station_info" enctype="multipart/form-data" action="">

                    <div class="feed-element gray-bg">
                        <div class="vertical-align">
                            <label class="col-md-12" style="padding-top:10px; padding-left:10px;">站点信息</label>
                        </div>
                    </div>

                    <div class="feed-element">
                        <div class="vertical-align">
                            <label class="col-md-2 control-label">奶站名称:</label>
                            <div class="col-md-2">
                                <input required name="st_name" type="text" class="form-control"
                                       value="{{$dsinfo->name}}" readonly/>
                            </div>
                        </div>
                    </div>

                    <div class="feed-element">
                        <div class="vertical-align">
                            <label class="col-md-2 control-label">地址:</label>
                            <label class="control-label" style="float:left">{{$dsinfo->province_name." ".$dsinfo->city_name." ".$dsinfo->district_name}}</label>
                            <div class="col-md-2">
                                <input required name="st_subaddr" id="select_street_xiaoqu" type="text"
                                       placeholder="手动填写" style="width: 80%;" class="form-control"
                                       value="{{$dsinfo->sub_address}}" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="feed-element">
                        <div class="vertical-align">
                            <label class="col-md-2 control-label">负责人:</label>
                            <div class="col-md-2">
                                <input required type="text" name="st_boss" id="st_boss"
                                       class="form-control" value="{{$dsinfo->boss}}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="feed-element">
                        <div class="vertical-align">
                            <label class="col-md-2 control-label">电话:</label>
                            <div class="col-md-2"><input required type="" name="st_phone" id="st_phone"
                                                         class="form-control" value="{{$dsinfo->phone}}" readonly></div>
                        </div>
                    </div>
                    <div class="feed-element">

                        <label class="col-md-2 control-label">店招:</label>
                        <div class="hide_after_click col-md-4">
                            @if($dsinfo->image_url)
                                <img class="logo"
                                     src="<?=asset('/img/station/logo/' . $dsinfo->image_url)?>">
                            @endif
                        </div>
                        <div id="img_station_logo" class="img-preview col-md-4">
                            <div class="file-panel" style="height: 0px;">
                                <span class="cancel">删除</span>
                            </div>
                        </div>
                        <div class="col-md-1 image-preview">
                            <button type="button" class="btn btn-success btn-outline" id="img_upload_button">
                                上传图片
                            </button>
                            <input type="file" name="station_img" class="img-upload"
                                   id="st_img_upload"/>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-3 col-md-offset-2">
                                <button type="submit" class="btn btn-outline btn-success btn-md" id="update_station">更新
                                </button>
                            </div>
                        </div>
                    </div>


                </form>

                <div class="feed-element gray-bg">
                    <label style="padding-top:10px; padding-left:10px;">账户信息</label>
                </div>
                <!--<div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-md-2 control-label">奶站名称：</label>
                        <div class="col-md-4"><input type="text" placeholder="" class="form-control bottle_input"
                                                     value="{{$dsinfo->name}}" readonly></div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-md-2 control-label">负责人：</label>
                        <div class="col-md-4"><input type="text" placeholder="" class="form-control bottle_input"
                                                     value="{{$dsinfo->boss}}" readonly></div>
                        <div class="col-md-6"></div>
                    </div>
                </div>-->
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-md-2 control-label">奶站类型：</label>
                        <div class="col-md-4">
                            <input type="text" placeholder="" class="form-control bottle_input"
                                   value="{{$dsinfo->type_name}}" readonly>
                        </div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-md-2 control-label">费用结算方式：</label>
                        <div class="col-md-4">
                            <input type="text" placeholder="" class="form-control bottle_input"
                                   value="{{$dsinfo->payment_calc_type_str}}" readonly>
                        </div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-md-2 control-label">结算账户：</label>
                        <div class="col-md-4">

                            <label class="col-md-4">帐户名：</label>
                            <label class="col-md-8">{{$dsinfo->billing_account_name}}</label>
                            <label class="col-md-4">卡号：</label>
                            <label class="col-md-8">{{$dsinfo->billing_account_card_no}}</label>
                        </div>
                        <label class="col-md-2 control-label">自由支付账户：</label>
                        <div class="col-md-4">

                            <label class="col-md-4">帐户名：</label>
                            <label class="col-md-8">{{$dsinfo->freepay_account_name}}</label>
                            <label class="col-md-4">卡号：</label>
                            <label class="col-md-8">{{$dsinfo->freepay_account_card_no}}</label>
                        </div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-md-2 control-label">初期信用额度：</label>
                        <div class="col-md-4">
                            <input type="text" placeholder="" class="form-control bottle_input"
                                   value="{{$dsinfo->init_business_credit_amount}}" readonly>
                        </div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-md-2 control-label">奶站编号：</label>
                        <div class="col-md-4"><input type="text" placeholder="" class="form-control"
                                                     value="{{$dsinfo->number}}" readonly></div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-md-2 control-label">收据凭证：</label>
                        <div class="col-md-6">
                            <img class="receipt"
                                 src="<?= asset('img/station/receipt/') ?>/{{$dsinfo->guarantee_receipt_path}}"
                                 class="img-responsive"/>
                        </div>
                    </div>
                </div>

                <div>
                    <hr>
                </div>
                <div class="wrapper">
                    <div>
                        <label>配送范围</label>
                    </div>
                    <div class="col-md-10 col-md-offset-1">
                        <div class="wrapper-content">
                            <table class="table table-bordered white-bg">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">顺序</th>
                                    <th data-sort-ignore="true">街道</th>
                                    <th data-sort-ignore="true">配送范围</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 0; ?>
                                @foreach($dsinfo->getDeliveryAreaGrouped() as $district_areas)
                                    <?php $j = 0; $i++;?>
                                    @foreach($district_areas as $key=>$da)
                                        <?php $addrs = explode(" ", $da->address) ?>
                                        <tr>
                                            @if($j==0)
                                                <td rowspan="{{count($district_areas)}}">{{$i}}</td>
                                                <td rowspan="{{count($district_areas)}}">{{$addrs[3]}}</td>
                                            @endif
                                            <td>{{$addrs[4]}}</td>
                                            <?php $j++; ?>
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="<?=asset('js/plugins/simpleimgupload/jquery.uploadimg.js') ?>"></script>

    <script>
        $(document).ready(function () {

            //Image File Upload
            $.simpleimgupload({
                input_field: ".img-upload",
                preview_box: ".img-preview",
                file_panel: ".file-panel",
                cancel_bt: ".file-panel span.cancel",
            });
        });

        $('form#station_info').on('submit', function (e) {

            e.preventDefault();

            var sendData = new FormData();

            var data = $('#station_info').serializeArray();

            $.each(data, function (i, field) {
                sendData.append(field.name, field.value);
            });

            var img_file = null;

            if ($('#st_img_upload')[0].files[0]) {
                img_file = $('#st_img_upload')[0].files[0];
                sendData.append('station_img', img_file);
            }

            console.log(sendData);

            $.ajax({
                type: "POST",
                url: API_URL + "naizhan/naizhan/jibenziliao/update_station_info",
                data: sendData,
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.status == "success") {
                        show_success_msg("更新成功");

                    } else {
                        if (data.message) {
                            show_info_msg(data.message);
                        }
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });

        });

    </script>

@endsection