@extends('gongchang.layout.master')
@section('css')
    <style>
        select, input {
            /*height: 35px;*/
            width: 100%;
        }

        .statics input {
            height: 30px;
            border: 0;
        }

        .section-name label {
            height: 30px;
            line-height: 30px;
        }

        .switchery {
            width: 60px;
        }

        #product_table tr td {
            padding: 2px;
        }

        #product_table label {
            line-height: 35px;
        }

        .one_p_amount, .avg {
            border: 0;
            height: 30px;
            max-width: 50px;
        }

        #product_table tr td .col-sm-5, #product_table tr td .col-sm-7,
        #product_table tr td .col-sm-4, #product_table tr td .col-sm-8,
        #product_table tr td .col-sm-6, #product_table tr td .col-sm-2,
        #product_table tr td .col-sm-5, #product_table tr td .col-sm-1 {
            padding: 0;
        }

        input.tip-input {
            text-align: center;
        }
    </style>
@endsection
@section('content')
    @include('gongchang.theme.sidebar')
    <div id="page-wrapper" class="white-bg">
        @include('gongchang.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">订单管理</a>
                </li>
                <li>
                    <a href=""><strong>订单录入</strong></a>
                </li>
            </ol>
        </div>
        <br>
        <div class="row">
            <!-- customer insert-->
            <div id="customer_info">
                <form method="POST" enctype="multipart/form-data" id="customer_form">
                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-2">订单性质:</label>
                        <div class="col-md-2">
                            <select required id="order_property" name="order_property" class="form-control">
                                @if(isset($order_property))
                                    @foreach($order_property as $orp)
                                        <option value="{{$orp->id}}">{{$orp->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-2">收货人:</label>
                        <div class="col-md-2">
                            <input type="text" required id="customer" name="customer" class="form-control">
                        </div>
                    </div>
                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-2">电话:</label>
                        <div class="col-md-2">
                            <input type="text" pattern="\d{11}"  id="phone" name="phone" class="form-control"
                                   oninvalid="this.setCustomValidity('手机号码得11位数')" oninput="this.setCustomValidity('')">
                        </div>
                    </div>

                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-2">收货地址：</label>
                        <div class="col-md-10" style="padding-left: 0px;">
                            <div class="col-md-2">
                                <select required id="province" name="c_province" class="province_list form-control">
                                    @if (isset($province))
                                        @for ($i = 0; $i < count($province); $i++)
                                            <option value="{{$province[$i]->name}}">{{$province[$i]->name}}</option>
                                        @endfor
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select required id="city" name="c_city" class="city_list col-md-2 form-control">
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select required id="district" name="c_district" class="district_list form-control">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select required id="street" name="c_street" class="street_list form-control">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="xiaoqu" required name="c_xiaoqu" class="xiaoqu_list form-control">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="feed-element col-md-12">
                        <div class="col-md-3 col-md-offset-2">
                            <input type="text" placeholder="填写详细地址" id="sub_addr" name="c_sub_addr"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="feed-element col-md-12">
                        <div class="col-md-2 col-md-offset-2">
                            <button class="btn btn-success btn-w-m" type="submit">确定</button>
                        </div>
                    </div>
                </form>
            </div>
            <form method="POST" enctype="multipart/form-data" id="order_form">

                <!--Station Info-->
                <div id="station_info">

                    <div class="col-md-12 gray-bg section-name">
                        <label class="col-md-12">奶站信息</label>
                    </div>

                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-2">奶站:</label>
                        <div class="col-md-3">
                            <select required class="form-control" id="station_list" name="station">
                            </select>
                        </div>
                    </div>
                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-2">征订员:</label>
                        <div class="col-md-3">
                            <select required class="form-control" id="order_checker_list" name="order_checker">
                                @if (isset($order_checkers))
                                    @foreach ($order_checkers as $orderchecker)
                                        <option value="{{$orderchecker->id}}">{{$orderchecker->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-2">票据号:</label>
                        <div class="col-md-3">
                            <input required type="text" name="receipt_number" class="form-control"
                                   id="receipt_number"/>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline btn-success" style="display:none;"
                                    id="reset_camera"
                            ><i class="fa fa-refresh" aria-hidden="true"></i>
                            </button>
                            <button type=button class="btn btn-outline btn-success" value="Take Snapshot"
                                    id="capture_camera"
                            ><i class="fa fa-camera"></i>
                            </button>
                        </div>
                    </div>
                    <div class="feed-element col-md-12">
                        <div class="col-md-6 col-md-offset-2">
                            <div id="my_camera"></div>
                            <input required type="hidden" name="receipt_path" id="receipt_path" \>
                            <i class="fa fa-check-square-o" id="check_capture"></i>
                        </div>
                    </div>

                    <input type="hidden" required name="customer_id" id="customer_id"/>
                </div>

                <!--Order Info -->
                <div id="order_info">
                    <div class="col-md-12 gray-bg section-name">
                        <label class="col-sm-12">订单内容</label>
                    </div>
                    <div class="feed-element col-md-12">
                        <label class="col-md-1 control-label" style="padding-top:7px;">起送日期</label>
                        <div class="input-group col-md-2">
                            <input required type="text" class="form-control" id="order_start_at" name="order_start_at" readonly="readonly" >
                        </div>
                    </div>
                    <div class="feed-element col-md-12">
                        <div class="col-md-1">
                            <label>奶箱安装</label>
                        </div>
                        <div class="col-md-3">
                            <input type="checkbox" class="js-switch" id="milk_box_install"
                                   name="milk_box_install"
                                   checked="false"/>
                        </div>
                    </div>
                    <div class="feed-element col-md-12">
                        <div class="col-md-1" id="check_2">
                            <label>奶卡支付</label>
                        </div>
                        <div class="col-md-2">
                            <input id="milk_card_check" name="milk_card_check" class="js-switch js-check-change"
                                   type="checkbox" data-toggle="modal" data-target="#card_info"/>
                            <input type="hidden" name="card_check_success" id="card_check_success" value="1">
                        </div>

                        <div class="col-md-4" id="form-card-panel" style="display:none">
                            奶卡号:<label id="form-card-id"></label> &nbsp;
                            金额:<label id="form-card-balance"></label>
                            商品:<label id="form-card-product"></label>
                        </div>
                    </div>
                    <br>

                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-1">配送时间</label>
                        &nbsp;
                        <div class="col-md-2">
                            <select required class="form-control" id="delivery_noon" name="delivery_noon">
                                <option value="1">上午</option>
                                <option value="2">下午</option>
                            </select>
                        </div>
                    </div>

                    <div class="feed-element col-md-12">
                        <button class="btn btn-outline btn-success" onclick="add_product();" type="button"><i
                                    class="fa fa-plus"></i> 添加奶品
                        </button>
                        <div class="ibox-content" style="padding: 20px 0; margin: 0 -15px;">
                            <table class="table" id="product_table" style="font-size: 14px;">
                                <!-- 奶品选择头部 -->
                                <thead>
                                <tr>
                                    <td class="col-sm-2">
                                        奶品
                                    </td>
                                    <td class="col-sm-1">
                                        订单类型
                                    </td>
                                    <td class="col-sm-1">
                                        数量
                                    </td>
                                    <td class="col-sm-1">
                                        配送规则
                                    </td>
                                    <td class="col-sm-1">
                                        每次（瓶）
                                    </td>
                                    <td class="col-sm-2">
                                        配送日期
                                    </td>
                                    <td class="col-sm-2">
                                        起送日期
                                    </td>
                                    <td class="col-sm-1">
                                        单数
                                    </td>
                                    <td class="col-sm-1">
                                        金额
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                </thead>

                                <!-- 奶品信息 -->
                                <tbody>
                                <tr id="first_data" class="one_product">
                                    <td>
                                        <select required class="form-control order_product_id"
                                                name="order_product_id[]"
                                                style="height:34px;">
                                            @if (isset($products))
                                                @foreach ($products as $product)
                                                    <option value="{{$product->id}}">{{$product->name}}</option>
                                                @endforeach
                                            @else
                                                <option value="none">这家工厂没有注册的产品</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <select required class="form-control factory_order_type"
                                                name="factory_order_type[]">
                                            @if (isset($factory_order_types))
                                                @foreach ($factory_order_types as $fot)
                                                    <option value="{{$fot->order_type}}"
                                                            data-content="{{$fot->id}}">{{$fot->order_type_name}}</option>
                                                @endforeach
                                            @else
                                                <option value="none">没有订单类型</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <div>
                                            <input required name="one_product_total_count[]"
                                                   class="one_product_total_count form-control"
                                                   type="number" min="1" value="30"
                                                   style="padding-left: 2px;"/>
                                            <select class="one_product_total_count_select control hidden form-control">
                                                @if(isset($products_count_on_fot))
                                                    @foreach($products_count_on_fot as $pcof)
                                                        <option data-otid="{{$pcof['fot']}}"
                                                                value="{{$pcof['pcfot']}}">{{$pcof['pcfot']}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <select required class="form-control order_delivery_type " name="order_delivery_type[]">
                                            @if (isset($order_delivery_types))
                                                @foreach ($order_delivery_types as $odt)
                                                    <option value="{{$odt->delivery_type}}"
                                                            data-content="{{$odt->id}}">{{$odt->name}}</option>
                                                @endforeach
                                            @else
                                                <option value="">没有配送规则</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <div class="bottle_number">
                                            <input type="number" min="1" required name="order_product_count_per[]"
                                                   class="form-control order_product_count_per" value="1"
                                                   style="display:inline-block;">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="calendar_show" style="display: none;">
                                            <div class="input-group date picker">
                                                <input type="text" class="form-control delivery_dates" name="delivery_dates[]">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group date single_date">
                                            <input required type="text" class="form-control start_at" name="start_at[]"><span
                                                    class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </td>
                                    <td>
                                        <label class="control-label product_count_per_day" style="padding-top: 7px;">
                                            <input type="text" required name="avg[]" class="avg" readonly value="1.0"/>
                                        </label>
                                    </td>
                                    <td>
                                        <label class="control-label total_amount_per_product" style="padding-top: 7px;">
                                            <input type="text" required name="one_p_amount[]" class="one_p_amount" readonly/>
                                        </label>
                                    </td>
                                    <td>
                                        <button type="button" class="remove_one_product"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-lg-9 col-md-8"></div>
                        <div class="col-lg-3 col-md-4 statics">
                            <div>
                                <label for="total_amount" class="control-label col-md-7">订单金额：</label>
                                <div class="col-md-5">
                                    <input required readonly id="total_amount" name="total_amount" value="0"/>
                                </div>
                            </div>
                            <div>
                                <label for="remaining" class="control-label col-md-7">账户余额：</label>
                                <div class="col-md-5">
                                    <input required readonly id="remaining" name="remaining" value="0"/>
                                </div>
                            </div>
                            <div>
                                <label for="real_amount" class="control-label col-md-7">本次应收金额：</label>
                                <div class="col-md-5">
                                    <input required readonly id="acceptable_amount" name="acceptable_amount"
                                           value="0"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="feed-element col-md-12" style="margin-top: 40px;">
                    <div class="col-md-2 col-md-offset-10">
                        <button type="submit" class="btn btn-success btn-w-m" type="submit">提交</button>
                    </div>
                </div>


                <div class="modal inmodal fade" id="card_info" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-body">
                                <br>
                                <div class="feed-element">
                                    <div class="col-md-6">
                                        <label>奶卡号:</label>
                                        <input required type="text" name="card_id" id="card_id" autocomplete="off">
                                    </div>
                                    <div class="col-md-6">
                                        <label>验证码:</label>
                                        <input required type="password" name="card_code" id="card_code" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6 col-md-offset-2">
                                    <label id="card_msg" style="display:none; font-size:16px;color:red"></label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-white verify-card">确定</button>
                                <button type="button" class="btn btn-white cancel-card">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="<?=asset('webcam/webcam.min.js') ?>"></script>
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/order_common.js') ?>"></script>

    <script language="JavaScript">

        function take_snapshot() {
            // take snapshot and get image data
            Webcam.snap(function (data_uri) {
                // display results in page
                document.getElementById('my_camera').innerHTML =
                        '<img src="' + data_uri + '"/>';
                Webcam.upload(data_uri, '<?=asset('webcam/upload.php') ?>', function (code, text) {
                    var img_filename = text;
                    $('#receipt_path').val(img_filename);
                });
            });
        }

        var firstday, lastday, firstm, lastm, dateToday;

        var customer_id, station_id, station_name;

        @if(isset($gap_day))
        var gap_day = parseInt("{{$gap_day}}");
        @endif

        var copy_tr_data = copy_tr_data = $("#first_data").html();
    </script>

    <script src="<?=asset('js/pages/gongchang/order_insert.js') ?>"></script>
@endsection