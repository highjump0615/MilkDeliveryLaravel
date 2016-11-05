@extends('naizhan.layout.master')
@section('css')
    <style>
        form {
            padding-left: 0;
            padding-right: 0;
        }

        select, input {
            height: 35px;
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

        input.remaining_bottle {
            width: 50px;
            display: inline-block;
        }

        .plan_count {
            display: inline-block;
            max-width: 150px;
            padding-left: 5px;
            border: none;
        }
    </style>
@endsection
@section('content')
    @include('naizhan.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('naizhan.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">订单管理</a>
                </li>
                <li class="active">
                    <strong>订单修改</strong>
                </li>
            </ol>
        </div>
        <div class="row white-bg">
            <form method="POST" enctype="multipart/form-data" id="customer_form">
                {{--Customer Info--}}
                <div class="col-md-12 gray-bg">
                    <label class="col-md-12">客户信息</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="col-md-2">收货人:</label>
                    <label class="col-md-2">{{$customer->name}}</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="col-md-2">电 话：</label>
                    <label class="col-md-2">{{$customer->phone}}</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">收货地址：</label>
                    <div class="col-md-10" style="padding-left: 0px;">
                        <div class="col-md-2">
                            <input required readonly id="province" name="c_province" value="{{$order->getAddrProvince()}}"
                                   class=" form-control"/>
                        </div>
                        <div class="col-md-2">
                            <input required readonly id="city" name="c_city" value="{{$order->getAddrCity()}}"
                                   class="form-control"/>
                        </div>
                        <div class="col-md-2">
                            <select data-origin="{{$order->getAddrCity()}}" required id="district" name="c_district"
                                    class="district_list form-control addr_info">
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select data-origin="{{$order->getAddrStreet()}}" required id="street" name="c_street"
                                    class="street_list form-control addr_info">
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select data-origin="{{$order->getAddrVillage()}}" id="xiaoqu" required name="c_xiaoqu"
                                    class="xiaoqu_list form-control addr_info">
                            </select>
                        </div>
                    </div>
                </div>

                <div class="feed-element col-md-12">
                    <div class="col-md-3 col-md-offset-2">
                        <input type="text" placeholder="填写详细地址" id="sub_addr" name="c_sub_addr"
                               data-origin="{{$customer->sub_addr}}"
                               class="form-control addr_info" value="{{$order->getAddrHouseNumber()}}">
                    </div>
                </div>
                <div class="feed-element col-md-12">
                    <div class="col-md-2 col-md-offset-2">
                        <button class="btn btn-success btn-w-m" type="submit">确定</button>
                        <div id="customer_spinner" class="submit-spinner-frame">
                            <div class="submit-spinner">
                                <div class="sk-spinner sk-spinner-circle">
                                    <div class="sk-circle1 sk-circle"></div>
                                    <div class="sk-circle2 sk-circle"></div>
                                    <div class="sk-circle3 sk-circle"></div>
                                    <div class="sk-circle4 sk-circle"></div>
                                    <div class="sk-circle5 sk-circle"></div>
                                    <div class="sk-circle6 sk-circle"></div>
                                    <div class="sk-circle7 sk-circle"></div>
                                    <div class="sk-circle8 sk-circle"></div>
                                    <div class="sk-circle9 sk-circle"></div>
                                    <div class="sk-circle10 sk-circle"></div>
                                    <div class="sk-circle11 sk-circle"></div>
                                    <div class="sk-circle12 sk-circle"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="customer_id" value="{{$customer->id}}">
                <input type="hidden" name="order_id" value="{{$order->id}}">
            </form>

            <form method="POST" enctype="multipart/form-data" id="order_form">
                <input type="hidden" name="customer_id" value="{{$customer->id}}">
                <input type="hidden" name="order_id" value="{{$order->id}}">
                <input type="hidden" name="init_order_total" value="{{$order->total_amount}}"
                       id="init_order_total">

                <div class="col-md-12 gray-bg">
                    <label class="col-md-12">奶站信息</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="col-md-2">奶站:</label>
                    <div class="col-md-2">
                        <input readonly type="text" data-staionid="{{$order->station_id}}" class="col-md-2 form-control"
                               id="station_name"
                               value="{{$order->delivery_station_name}}"/>
                    </div>
                    @if (isset($milkman))
                        <div class="col-md-2">
                            <input readonly type="text" class="form-control" id="milkman_name"
                                   value="{{$milkman->name}}"/>
                        </div>
                        <div class="col-md-2">
                            <input readonly type="text" class="form-control" id="milkman_phone"
                                   value="{{$milkman->phone}}"/>
                        </div>
                    @endif
                </div>
                <div class="feed-element col-md-12">
                    <label class="col-md-2">征订人：</label>
                    <div class="col-md-2">
                        <input readonly class="form-control" id="order_checker_name"
                               value="{{$order->order_checker_name}}"/>
                    </div>
                </div>

                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">票据号:</label>
                    <div class="col-md-3">
                        <input required type="text" name="receipt_number" class="form-control"
                               id="receipt_number" value="{{$order->receipt_number}}"/>
                    </div>
                    <div class="col-md-4">
                        @if($order->receipt_path)
                            <button type="button" class="btn btn-outline btn-success" id="reset_camera"
                            ><i class="fa fa-refresh"></i>
                            </button>
                            <button type=button class="btn btn-outline btn-success" value="Take Snapshot"
                                    style="display: none;" id="capture_camera"><i class="fa fa-camera"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-outline btn-success" id="reset_camera"
                                    style="display: none;"><i class="fa fa-refresh"></i>
                            </button>
                            <button type=button class="btn btn-outline btn-success" value="Take Snapshot"
                                    id="capture_camera"><i class="fa fa-camera"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="feed-element col-md-12">
                    @if($order->receipt_path)
                        <div class="col-md-offset-2" id="origin_ticket_div">
                            <img id="ticket" src="<?=asset('img/order/' . $order->receipt_path)?>"
                                 class="img-responsive"/>
                        </div>
                        <div class="col-md-6 col-md-offset-2" id="change_ticket_div" style="display:none;">
                            <div id="my_camera"></div>
                            <input required type="hidden" name="receipt_path" id="receipt_path"
                                   value="{{$order->receipt_path}}"/>
                            <i class="fa fa-check-square-o" id="check_capture"></i>
                        </div>
                    @else
                        <div class="col-md-offset-2" id="origin_ticket_div" style="display:none;">
                            <img id="ticket" src="<?=asset('img/order/' . $order->receipt_path)?>"
                                 class="img-responsive"/>
                        </div>
                        <div class="col-md-6 col-md-offset-2" id="change_ticket_div">
                            <div id="my_camera"></div>
                            <input required type="hidden" name="receipt_path" id="receipt_path"
                                   value="{{$order->receipt_path}}"/>
                            <i class="fa fa-check-square-o" id="check_capture"></i>
                        </div>
                    @endif
                </div>

                {{--Order Info--}}
                <div class="col-md-12 gray-bg">
                    <label class="col-sm-10">订单内容</label>
                </div>

                <div class="feed-element col-md-12">
                    <label class="col-md-2">起送日期：</label>
                    <label>{{$order->start_at}}</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">奶箱安装：</label>
                    <label>{{$order->milk_box_install_label}}</label>
                </div>
                <div class="feed-element col-md-12">
                    <label class="control-label col-md-2">支付方式：</label>
                    <label>{{$order->payment_type_name}}</label>
                </div>

                <div class="feed-element col-md-12">
                    <div class="feed-element col-md-12">
                        <div class="col-md-2">
                            <button class="btn btn-outline btn-success" onclick="add_product();" type="button"><i
                                        class="fa fa-plus"></i> 添加奶品
                            </button>
                        </div>
                        <div class="col-md-6 col-md-offset-4">
                            <label class="col-md-4">初始订单金额: <span class="init_total_sp">{{$order->total_amount}}</span></label>
                            <label class="col-md-4">当前订单金额: <span
                                        class="current_total_sp">{{$order->remain_order_money}}</span></label>
                            <label class="col-md-4">更改订单金额: <span
                                        class="updated_total_sp">{{$order->remain_order_money}}</span></label>
                        </div>
                    </div>
                    <div class="feed-element col-md-12">
                        <div class="ibox-content" style="padding: 20px 0;">
                            <table class="table table-bordered" id="product_table" style="font-size: 14px;">
                                <tbody>
                                @if(isset($order_products))
                                    @foreach($order_products as $op)
                                        <tr id="first_data" class="one_product">
                                            <td class="col-sm-1">
                                                <select required class="form-control order_product_id"
                                                        name="order_product_id[]"
                                                        style="height:34px;">
                                                    @if (isset($products))
                                                        @foreach ($products as $product)
                                                            @if($op->product_id == $product->id)
                                                                <option value="{{$product->id}}"
                                                                        selected>{{$product->name}}</option>
                                                            @else
                                                                <option value="{{$product->id}}">{{$product->name}}</option>
                                                            @endif
                                                        <!--option value="{{$product->id}}">{{$product->series_no}}</option-->
                                                        @endforeach
                                                    @else
                                                        <option value="none">这家工厂没有注册的产品</option>
                                                    @endif
                                                </select>
                                            </td>
                                            <td class="col-sm-2">
                                                <div class="col-sm-4">
                                                    <label class="control-label">订单类型</label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <select required class="form-control factory_order_type"
                                                            name="factory_order_type[]">
                                                        @if (isset($factory_order_types))
                                                            @foreach ($factory_order_types as $fot)
                                                                @if($op->order_type == $fot->order_type)
                                                                    <option value="{{$fot->order_type}}" selected
                                                                            data-content="{{$fot->id}}">{{$fot->order_type_name}}</option>
                                                                @else
                                                                    <option value="{{$fot->order_type}}"
                                                                            data-content="{{$fot->id}}">{{$fot->order_type_name}}</option>
                                                                @endif
                                                            <!--option value="{{$product->id}}">{{$product->series_no}}</option-->
                                                            @endforeach
                                                        @else
                                                            <option value="none">没有订单类型</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </td>
                                            <td class="col-sm-1">
                                                <div class="col-sm-5">
                                                    <label class="control-label">数量</label>
                                                </div>
                                                <div class="col-sm-7">
                                                    <input required name="one_product_total_count[]"
                                                           class="one_product_total_count form-control"
                                                           type="number" min="1" value="{{$op->total_count}}"
                                                           style="padding-left: 2px;"/>
                                                    <select class="one_product_total_count_select control hidden form-control">
                                                        @if(isset($products_count_on_fot))
                                                            @foreach($products_count_on_fot as $pcof)
                                                                @if($op->order_type == $pcof['fot'])
                                                                    <option data-otid="{{$pcof['fot']}}"
                                                                            value="{{$pcof['pcfot']}}">{{$pcof['pcfot']}}</option>
                                                                @else
                                                                    <option data-otid="{{$pcof['fot']}}"
                                                                            value="{{$pcof['pcfot']}}">{{$pcof['pcfot']}}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </td>
                                            <td class="col-sm-2">
                                                <div class="col-sm-6">
                                                    <label class="control-label">配送规则</label>
                                                </div>
                                                <div class="col-sm-6">
                                                    <select required class="form-control order_delivery_type "
                                                            name="order_delivery_type[]">
                                                        @if (isset($order_delivery_types))
                                                            @foreach ($order_delivery_types as $odt)
                                                                @if($op->delivery_type == $odt->delivery_type)
                                                                    <option value="{{$odt->delivery_type}}" selected
                                                                            data-content="{{$odt->id}}">{{$odt->name}}</option>
                                                                @else
                                                                    <option value="{{$odt->delivery_type}}"
                                                                            data-content="{{$odt->id}}">{{$odt->name}}</option>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <option value="">没有配送规则</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </td>
                                            <td class="col-sm-1">
                                                @if($op->delivery_type == \App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EVERY_DAY || $op->delivery_type == \App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY )
                                                    <div class="bottle_number">
                                                        <div class="col-sm-5">
                                                            <label class="control-label">每次</label>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <input type="text" name="order_product_count_per[]"
                                                                   class="form-control order_product_count_per"
                                                                   style="display:inline-block;"
                                                                   value="{{$op->count_per_day}}">
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <label class="control-label">瓶</label>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="bottle_number" style="display:none;">
                                                        <div class="col-sm-5">
                                                            <label class="control-label">每次</label>
                                                        </div>
                                                        <div class="col-sm-5">
                                                            <input type="text" name="order_product_count_per[]"
                                                                   class="form-control order_product_count_per"
                                                                   style="display:inline-block;"
                                                                   value="">
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <label class="control-label">瓶</label>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="col-sm-3">
                                                @if($op->delivery_type == \App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EVERY_DAY || $op->delivery_type == \App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_EACH_TWICE_DAY )
                                                    <div class="calendar_show" style="display:none;">
                                                        <div class="col-sm-4">
                                                            <label class="control-label">配送日期</label>
                                                        </div>
                                                        <div class="input-group date col-sm-8 picker">

                                                            <input type="text"
                                                                   class="form-control delivery_dates"
                                                                   name="delivery_dates[]"><span
                                                                    class="input-group-addon"><i
                                                                        class="fa fa-calendar"></i></span></input>

                                                        </div>
                                                    </div>
                                                @elseif ($op->delivery_type == \App\Model\DeliveryModel\DeliveryType::DELIVERY_TYPE_WEEK)
                                                    <div class="calendar_show" style="">
                                                        <div class="col-sm-4">
                                                            <label class="control-label">配送日期</label>
                                                        </div>
                                                        <div class="input-group multi_date_week date col-sm-8 picker">
                                                            <input type="text"
                                                                   class="form-control delivery_dates"
                                                                   name="delivery_dates[]"
                                                                   value="{{$op->custom_order_dates_on_this_month}}">
                                                                    <span class="input-group-addon"><i
                                                                                class="fa fa-calendar"></i></span>
                                                            </input>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="calendar_show" style="">
                                                        <div class="col-sm-4">
                                                            <label class="control-label">配送日期</label>
                                                        </div>
                                                        <div class="input-group multi_date date col-sm-8 picker">
                                                            <input type="text"
                                                                   class="form-control delivery_dates"
                                                                   name="delivery_dates[]"
                                                                   value="{{$op->custom_order_dates_on_this_month}}">
                                                                    <span class="input-group-addon"><i
                                                                                class="fa fa-calendar"></i></span>
                                                            </input>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="col-sm-1">
                                                <label class="control-label product_count_per_day"
                                                       style="padding-top: 7px;">单数:
                                                    <input type="text" required name="avg[]" class="avg" readonly
                                                           value="{{$op->avg}}"/></label>
                                            </td>
                                            <td class="col-sm-1">
                                                <label class="control-label total_amount_per_product"
                                                       style="padding-top: 7px;">金额:
                                                    <input type="text" required name="one_p_amount[]"
                                                           class="one_p_amount"
                                                           readonly value="{{$op->total_amount}}"/></label>
                                            </td>
                                            <td>
                                                <button type="button" class="remove_one_product"><i
                                                            class="fa fa-trash-o"
                                                            aria-hidden="true"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="feed-element col-md-12">
                    <div class="col-md-2 col-md-offset-10">
                        <button class="btn btn-success btn-w-m" id="order_submit" type="submit">确定</button>
                        <div id="order_spinner" class="submit-spinner-frame">
                            <div class="submit-spinner">
                                <div class="sk-spinner sk-spinner-circle">
                                    <div class="sk-circle1 sk-circle"></div>
                                    <div class="sk-circle2 sk-circle"></div>
                                    <div class="sk-circle3 sk-circle"></div>
                                    <div class="sk-circle4 sk-circle"></div>
                                    <div class="sk-circle5 sk-circle"></div>
                                    <div class="sk-circle6 sk-circle"></div>
                                    <div class="sk-circle7 sk-circle"></div>
                                    <div class="sk-circle8 sk-circle"></div>
                                    <div class="sk-circle9 sk-circle"></div>
                                    <div class="sk-circle10 sk-circle"></div>
                                    <div class="sk-circle11 sk-circle"></div>
                                    <div class="sk-circle12 sk-circle"></div>
                                </div>
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
    <script type="text/javascript" src="<?=asset('js/pages/order/order_xiugai_common.js') ?>"></script>
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
        var firstday, lastday, firstm, lastm;


        //Initialize data
        var order_id = "{{$order->id}}";
        var customer_id = "{{$customer->id}}";
        var station_id = "{{$order->station_id}}";
        var station_name = "{{$order->station_name}}";
        var order_checker_id = "{{$order->order_checker_id}}";

        var customer_province = "{{$order->getAddrProvince()}}";
        var customer_city = "{{$order->getAddrCity()}}";

        var customer_district = "{{$order->getAddrDistrict()}}";
        var customer_street = "{{$order->getAddrStreet()}}";
        var customer_xiaoqu = "{{$order->getAddrVillage()}}";

                @if(isset($gap_day))
        var gap_day = parseInt("{{$gap_day}}");
                @endif

        var copy_tr_data = copy_tr_data = $("#first_data").html();

        var milkman_id = "{{$order->milkman_id}}";

                @if(isset($milkman))
        var milkman_name = "{{$milkman->name}}";
        var milkman_phone = "{{$milkman->phone}}";
        @endif

        @if(!$order->receipt_path)
            show_camera();
        @endif

        var current_order_total = parseFloat("{{$order->remain_order_money}}");
    </script>
    <script src="<?= asset('/js/pages/naizhan/order_xiugai.js')?>"></script>
@endsection