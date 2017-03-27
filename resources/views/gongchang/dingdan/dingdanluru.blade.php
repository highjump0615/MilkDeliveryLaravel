<?php
    use App\Model\BasicModel\PaymentType;

    // 订单是否需要新订单录入界面
    $isPassed = false;

    if (isset($order)) {
        // 奶卡订单不管通过不通过，都用修改界面
        if ($order->payment_type == PaymentType::PAYMENT_TYPE_CARD) {
            $isPassed = true;
        }
        else {
            $isPassed = $order->isNewPassed();
        }
    }
?>

@extends('gongchang.layout.master')
@section('css')
    <link href="<?=asset('css/pages/order_input.css') ?>" rel="stylesheet">
@endsection

@section('content')

<!-- 奶站需要奶站的菜单 -->
@if (isset($station))
    @include('naizhan.theme.sidebar')
@else
    @include('gongchang.theme.sidebar')
@endif

    <div id="page-wrapper" class="white-bg">

        <!-- 头部 -->
        @if (isset($station))
            @include('naizhan.theme.header')
        @else
            @include('gongchang.theme.header')
        @endif

        <!-- 面包屑导航 -->
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">订单管理</a>
                </li>
                <li>
                    @if ($is_edit)
                        <strong>订单修改</strong>
                    @elseif (isset($order))
                        <strong>续单</strong>
                    @else
                        <a href=""><strong>订单录入</strong></a>
                    @endif
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
                                @if (isset($order_property))
                                    @foreach($order_property as $orp)
                                        <!-- 续单需要默认选择 -->
                                        @if (!$is_edit && isset($order) && $orp->id == 2)
                                            <option value="{{$orp->id}}" selected>{{$orp->name}}</option>
                                        @else
                                            <option value="{{$orp->id}}">{{$orp->name}}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-2">收货人:</label>
                        <div class="col-md-2">
                            <input type="text" required id="customer" name="customer" class="form-control"
                                   @if (isset($customer)) value="{{$customer->name}}" @endif>
                        </div>
                    </div>
                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-2">电话:</label>
                        <div class="col-md-2">
                            <input required type="text" id="phone" name="phone" class="form-control"
                                   @if (isset($order)) value="{{$order->phone}}" @endif >
                        </div>
                    </div>

                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-2">收货地址：</label>
                        <div class="col-md-10" style="padding-left: 0px;">
                            <div class="col-md-2">
                                <select required id="province" name="c_province" class="province_list form-control">
                                    @if (isset($province))
                                        <?php
                                        $province_name = "";
                                        if (isset($order))   // 修改订单
                                            $province_name = $order->getAddrProvince();
                                        else if (isset($station))   // 奶站订单录入
                                            $province_name = $station->province_name;
                                        ?>

                                        @for ($i = 0; $i < count($province); $i++)
                                            @if ($province[$i]->name == $province_name)
                                                <option value="{{$province[$i]->name}}" selected>{{$province[$i]->name}}</option>
                                            @else
                                                <option value="{{$province[$i]->name}}">{{$province[$i]->name}}</option>
                                            @endif
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
                                   class="form-control"
                                   @if (isset($order)) value="{{$order->getAddrHouseNumber()}}" @endif>
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

                @if ($is_edit)
                    <input type="hidden" name="order_id" id="input_order_id" value="{{$order->id}}">
                @endif

                <!--Station Info-->
                <div id="station_info">

                    <div class="col-md-12 gray-bg section-name">
                        <label class="col-md-12">奶站信息</label>
                    </div>

                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-2">奶站:</label>
                        <div class="col-md-3">
                            <select required class="form-control" id="station_list" name="station">
                                @if (isset($order))
                                    <option data-milkman="{{$milkman->id}}" value="{{$order->delivery_station_id}}" selected>
                                        {{$order->delivery_station_name}}
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-2">征订员:</label>
                        <div class="col-md-3">
                            <select required class="form-control" id="order_checker_list" name="order_checker">
                                @if (isset($order_checkers))
                                    @foreach ($order_checkers as $orderchecker)
                                    <?php
                                        $optSelected = "";
                                        if (isset($order)) {
                                            if ($orderchecker->id == $order->order_checker_id) {
                                                $optSelected = " selected";
                                            }
                                        }
                                    ?>
                                        <option value="{{$orderchecker->id}}"{{$optSelected}}>{{$orderchecker->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- 微信订单修改不显示票据号 -->
                    @if (!($is_edit && isset($order) && $order->payment_type == \App\Model\BasicModel\PaymentType::PAYMENT_TYPE_WECHAT))
                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-2">票据号:</label>
                        <div class="col-md-3">
                            <input required type="text" name="receipt_number" class="form-control"
                                   id="receipt_number"
                                   @if (isset($order) && $is_edit) value="{{$order->receipt_number}}" @endif/>
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
                            <!-- 票据号照片 -->
                            <div id="my_camera">
                                @if(isset($order) && $order->receipt_path !="")
                                    <img src="<?=asset('img/order/' . $order->receipt_path)?>" />
                                @endif
                            </div>

                            <input required type="hidden" name="receipt_path" id="receipt_path"
                                   @if (isset($order)) value="{{$order->receipt_path}}" @endif \>
                            <i class="fa fa-check-square-o" id="check_capture"></i>
                        </div>
                    </div>
                    @endif

                    <input type="hidden"
                           required
                           name="customer_id"
                           id="customer_id"
                           @if (isset($order)) value="{{$order->customer_id}}" @endif />
                </div>

                <!--Order Info -->
                <div id="order_info">
                    <div class="col-md-12 gray-bg section-name">
                        <label class="col-sm-12">订单内容</label>
                    </div>
                    <div class="feed-element col-md-12">
                        <label class="col-md-1 control-label" style="padding-top:7px;">起送日期</label>
                        <div class="input-group col-md-2">
                            <input required type="text" class="form-control" id="order_start_at" name="order_start_at"
                                   readonly="readonly" />
                        </div>
                    </div>
                    <div class="feed-element col-md-12">
                        <div class="col-md-1">
                            <label>奶箱安装</label>
                        </div>
                        <div class="col-md-3">
                            <input type="checkbox" class="js-switch" id="milk_box_install"
                                   name="milk_box_install"
                                   @if (!(isset($order) && !$order->milk_box_install)) checked="checked" @endif/>
                        </div>
                    </div>

                    <!-- 只能在奶厂录入奶卡订单 -->
                    <div class="feed-element col-md-12">
                        <div class="col-md-1" id="check_2">
                            <label @if ($is_edit || isset($station)) style="opacity: 0.5;" @endif>奶卡支付</label>
                        </div>
                        <div class="col-md-2">
                            <input id="milk_card_check" name="milk_card_check" class="js-switch js-check-change"
                                   type="checkbox"
                                   @if (isset($order) && $order->order_by_milk_card) checked="checked" @endif
                            />
                            <input type="hidden" name="card_check_success" id="card_check_success" value="1">
                        </div>

                        <button type="button"
                                class="btn btn-sm btn-success pull-left"
                                data-toggle="modal" href="#card_info"
                                @if ($is_edit || isset($station)) disabled @endif>
                            添加奶卡
                        </button>

                        <div id="form-card-panel">
                            <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;金额:</span>
                            <span id="form-card-balance"></span>
                        </div>
                    </div>

                    <br>

                    <div class="feed-element col-md-12">
                        <label class="control-label col-md-1">配送时间</label>
                        &nbsp;
                        <div class="col-md-2">
                            <select required class="form-control" id="delivery_noon" name="delivery_noon">
                                <option value="{{\App\Model\OrderModel\Order::ORDER_DELIVERY_TIME_MORNING}}"
                                        @if (isset($order) && $order->delivery_time == \App\Model\OrderModel\Order::ORDER_DELIVERY_TIME_MORNING)
                                        selected
                                        @endif>
                                    上午
                                </option>
                                <option value="{{\App\Model\OrderModel\Order::ORDER_DELIVERY_TIME_AFTERNOON}}"
                                        @if (isset($order) && $order->delivery_time == \App\Model\OrderModel\Order::ORDER_DELIVERY_TIME_AFTERNOON)
                                        selected
                                        @endif>
                                    下午
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="feed-element col-md-12">
                        <div class="feed-element col-md-12">
                            <div class="col-md-2">
                                <button class="btn btn-outline btn-success" onclick="add_product();" type="button"><i
                                        class="fa fa-plus"></i> 添加奶品
                                </button>
                            </div>

                            @if ($is_edit && $isPassed)
                                <!-- 订单修改 -->
                                <div class="col-md-2 col-md-offset-8"> 
                                    <label>订单余额: <span class="current_total_sp">{{$order->remain_order_money}}</span></label> 
                                </div>
                            @endif
                        </div>
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
                                    <td class="col-sm-2">
                                        起送日期
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
                                @if (isset($order_products)) 
                                    <!-- 订单修改 -->
                                    @foreach ($order_products as $op)
                                        <!-- 筛选选择项 -->
                                        @include('gongchang.dingdan.dingdanluru.addproduct', [
                                            'order_product' => $op,
                                        ])
                                    @endforeach
                                @else
                                    <!-- 订单录入 -->
                                    @include('gongchang.dingdan.dingdanluru.addproduct')
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-lg-9 col-md-8"></div>
                        <div class="col-lg-3 col-md-4 statics">
                            <div>
                                <label for="total_amount" class="control-label col-md-7">
                                    @if ($is_edit && $isPassed)
                                        更改后金额：
                                    @else
                                        订单金额：
                                    @endif
                                </label>
                                <div class="col-md-5">
                                    <input required readonly id="total_amount" name="total_amount" value="0"/>
                                </div>
                            </div>
                            @if ($is_edit && $isPassed)
                                <div>
                                    <label for="edit_remaining" class="control-label col-md-7">差额：</label>
                                    <div class="col-md-5">
                                        <input required readonly id="remaining_after" name="remaining_after" value="0"/>
                                    </div>
                                </div>
                            @else
                                <div>
                                    <label for="remaining" class="control-label col-md-7">账户余额：</label>
                                    <div class="col-md-5">
                                        <input required readonly id="remaining" name="remaining" value="{{$remain_amount}}"/>
                                    </div>
                                </div>
                                <div>
                                    <label for="real_amount" class="control-label col-md-7">本次应收金额：</label>
                                    <div class="col-md-5">
                                        <input required readonly id="acceptable_amount" name="acceptable_amount"
                                               value="0"/>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="feed-element col-md-12" style="margin-top: 40px;">
                    <div class="col-md-2 col-md-offset-10">
                        <button type="submit" class="btn btn-success btn-w-m" type="submit">
                            提交@if ($is_edit)修改 @endif
                        </button>
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
                                        <input required type="text" name="card_code" id="card_code" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6 col-md-offset-2">
                                    <label id="card_msg" style="display:none; font-size:16px;color:red"></label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-white verify-card">确定</button>
                                <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 添加奶瓶object js, 这个要比datepicker早加载 -->
    <script type="text/javascript" src="<?=asset('js/pages/order/order_bottle.js') ?>"></script>

@endsection

@section('script')
    <script type="text/javascript" src="<?=asset('webcam/webcam.min.js') ?>"></script>

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
        var city_name, district_name, street_name, village_name;

        var gbIsStation = false;

        // 是否奶站录入
        @if (isset($station))
            gbIsStation = true;
        @endif

        @if ($is_edit && isset($order))
            dateToday = new Date("{{$order->deliveryStation->getChangeStartDate()}}");
        @else
            dateToday = new Date(s_timeCurrent);
        @endif

        @if (isset($order))
            city_name = "{{$order->getAddrCity()}}";
            district_name = "{{$order->getAddrDistrict()}}";
            street_name = "{{$order->getAddrStreet()}}";
            village_name = "{{$order->getAddrVillage()}}";
        @elseif (isset($station))
            city_name = "{{$station->city_name}}";
            district_name = "{{$station->district_name}}";
        @endif

        @if(isset($gap_day))
        var gap_day = parseInt("{{$gap_day}}");
        @endif

        var copy_tr_data = $("#first_data").html();

    </script>

    <script type="text/javascript" src="<?=asset('js/pages/gongchang/order_common.js') ?>"></script>
    <script src="<?=asset('js/pages/gongchang/order_insert.js') ?>"></script>
@endsection