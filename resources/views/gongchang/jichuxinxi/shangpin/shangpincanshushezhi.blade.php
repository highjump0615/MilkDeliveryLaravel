@extends('gongchang.layout.master')
@section('css')
    <link href="<?=asset('css/plugins/added/switchery.css') ?>" rel="stylesheet">

    <link href="<?=asset('css/plugins/daterangepicker/daterangepicker-bs3.css') ?>" rel="stylesheet">
@endsection
@section('content')
    @include('gongchang.theme.sidebar')
    <div id="page-wrapper" class="white-bg dashbard-1">
        @include('gongchang.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">基础信息管理</a>
                </li>
                <li>
                    <a href={{URL::to('/gongchang/jichuxinxi/shangpin')}}>商品管理</a>
                </li>
                <li>
                    <a href=""><strong>商品参数设置</strong></a>
                </li>
            </ol>
        </div>
        <div class="row white-bg">
            <br>
            <div class=" col-md-12 gray-bg">
                <label class="col-md-12">配送规则设定</label>
            </div>
            <div class="ibox-content">

                @foreach ($fdts as $fdto)
                    <div class="col-md-12">
                        <label class="col-md-10 dts-name" data-fdtsid="{{$fdto->delivery_type}}">{{$fdto->name}}</label>
                        <div class="col-md-2">
                            @if ($fdto->is_active)
                                <label class="act_dt label-success">启用</label> | <label class="deact_dt ">停用</label>
                            @else
                                <label class="act_dt">启用</label> | <label class="deact_dt label-success">停用</label>
                            @endif
                        </div>
                        <div class="hr-line-dashed"></div>
                    </div>
                @endforeach
            </div>
            <div class=" col-md-12 gray-bg">
                <label class="col-md-12">配送时间设定</label>
            </div>
            <div class="ibox-content">
                <div class="col-md-12">
                    <label class="col-md-12">作为提醒发送给用户，大概的配送时间</label>
                </div>
                <div class="col-md-12" style="padding: 10px;">
                    <div class="col-md-12">
                        <label>上午:</label>
                        &nbsp;
                        <select data-placeholder="" id="morning_start_at" class="chosen-select" style="width:150px;"
                                tabindex="2">
                            <?php
                            $times = ["6:00", "7:00", "8:00", "9:00"];
                            ?>

                            @foreach($times as $time) {
                            @if($time == $delivery_time->morning_start_at)
                                <option value="{{$time}}" selected="selected">{{$time}}</option>
                            @else
                                <option value="{{$time}}">{{$time}}</option>
                            @endif
                            @endforeach
                        </select>
                        <label>至</label>
                        <select data-placeholder="" id="morning_end_at" class="chosen-select" style="width:150px;"
                                tabindex="2">
                            <?php
                            $times = ["10:00", "11:00", "12:00"];
                            ?>

                            @foreach($times as $time) {
                            @if($time == $delivery_time->morning_end_at)
                                <option value="{{$time}}" selected="selected">{{$time}}</option>
                            @else
                                <option value="{{$time}}">{{$time}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12" style="padding: 10px;">
                    <div class="col-md-12">
                        <label>下午: </label>
                        &nbsp;
                        <select data-placeholder="" id="afternoon_start_at" class="chosen-select" style="width:150px;"
                                tabindex="2">
                            <?php
                            $times = ["14:00", "15:00", "16:00"];
                            ?>

                            @foreach($times as $time) {
                            @if($time == $delivery_time->afternoon_start_at)
                                <option value="{{$time}}" selected="selected">{{$time}}</option>
                            @else
                                <option value="{{$time}}">{{$time}}</option>
                            @endif
                            @endforeach
                        </select>
                        <label>至</label>
                        <select data-placeholder="" id="afternoon_end_at" class="chosen-select" style="width:150px;"
                                tabindex="2">
                            <?php
                            $times = ["17:00", "18:00", "19:00", "20:00"];
                            ?>

                            @foreach($times as $time) {
                            @if($time == $delivery_time->afternoon_end_at)
                                <option value="{{$time}}" selected="selected">{{$time}}</option>
                            @else
                                <option value="{{$time}}">{{$time}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-1 ">
                        <button type="button" id="submit_delivery_time" class="btn btn-success btn-md"
                                data-action="show_selected">确定
                        </button>
                    </div>
                    <label id="delivery_time_msg" class="col-md-2"></label>
                </div>

            </div>

            <div class=" col-md-12 gray-bg">
                <label class="col-md-12">订单类型</label>
            </div>
            <div class="col-md-12">
                <div class="col-md-2" style="padding: 10px;">
                    <button id="add_order_type" class="btn btn-success" style="width:100%;"><i
                                class="fa fa-plus"></i>添加订单类型
                    </button>
                </div>
                <table class="table table-bordered">
                    <tbody id="order_type_list">
                    @foreach($order_types as $ot)
                        @if($ot->active == true)
                            <tr id="order_type{{$ot->id}}">
                                <td class="col-md-4">{{$ot->name}}</td>
                                <td class="col-md-4">{{$ot->days}}</td>
                                <td class="col-md-4">
                                    <button class="btn btn-sm btn-success delete-order-type" value="{{$ot->id}}">删除
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class=" col-md-12 gray-bg">
                <label class="col-md-12">其他参数</label>
            </div>
            <div class="col-md-12">
                <div class="col-md-2" style="padding: 10px;">
                    <button id="add_bottle_type" class="btn btn-success" style="width:100%;"><i class="fa fa-plus"></i>
                        奶瓶规格
                    </button>
                </div>
                <table class="col-md-12 footable table table-bordered" data-page-size="5">
                    <thead>
                    <tr>
                        <th data-sort-ignore="true" class="col-md-4">奶瓶规格</th>
                        <th data-sort-ignore="true" class="col-md-4">编号</th>
                        <th data-sort-ignore="true" class="col-md-4">操作</th>
                    </tr>
                    </thead>
                    <tbody id="bottle_type_list">
                    @foreach($bottle_types as $bt)
                        @if($bt->is_deleted == 0)
                            <tr id="bottle_type{{$bt->id}}">
                                <td>{{$bt->name}}</td>
                                <td>{{$bt->number}}</td>
                                <td>
                                    <button class="btn btn-sm btn-success delete-bottle-type" value="{{$bt->id}}">删除
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3">
                            <ul class="pagination pull-right"></ul>
                        </td>
                    </tr>
                    </tfoot>
                </table>
                <hr>
                <div class="col-md-2" style="padding: 10px;">
                    <button id="add_box_type" class="btn btn-success" style="width:100%;"><i class="fa fa-plus"></i>
                        奶框规格
                    </button>
                </div>
                <table class="col-md-12 footable table table-bordered" data-page-size="5">
                    <thead>
                    <tr>
                        <th data-sort-ignore="true" class="col-md-4">奶框规格</th>
                        <th data-sort-ignore="true" class="col-md-4">编号</th>
                        <th data-sort-ignore="true" class="col-md-4">操作</th>
                    </tr>
                    </thead>
                    <tbody id="box_type_list">
                    @foreach($box_types as $xt)
                        <tr id="box_type{{$xt->id}}">
                            <td>{{$xt->name}}</td>
                            <td>{{$xt->number}}</td>
                            <td>
                                <button class="btn btn-sm btn-success delete-box-type" value="{{$xt->id}}">删除</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3">
                            <ul class="pagination pull-right"></ul>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class=" col-md-12 gray-bg">
                <label class="col-md-12">新单开始配送时间设定</label>
            </div>
            <div class="ibox-content">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <label>从下单之日算起: </label>
                        &nbsp;
                        <select id="gap_select" data-placeholder="" class="chosen-select" style="width:150px;"
                                tabindex="2">
                            <option value="3">3天</option>
                            <option value="4">4天</option>
                            <option value="5">5天</option>
                        </select>
                        <label>后</label>
                    </div>
                    <div class="col-md-1">
                        <button type="button" id="gap_day" class="btn btn-success btn-md"
                                data-action="set_gap">确定
                        </button>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <br>
        </div>

        <div id="order_type_modal" class="modal fade" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="feed-element">
                                <label class="col-md-3" style="padding-top:5px;">订单类型</label>
                                <div class="col-md-7">
                                    <select id="sel-order-type" data-placeholder="" class="chosen-select"
                                            style="width:150px; padding-top: 5px;" tabindex="2">
                                        @foreach($order_types as $ot)
                                            @if($ot->active == false)
                                                <option value="{{$ot->id}}">{{$ot->name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="post_add_order_type" class="btn btn-success add_cbt">添加
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="bottle_type_modal" class="modal fade" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="feed-element">
                                <label class="col-md-3" style="padding-top:5px;">奶瓶规格</label>
                                <div class="col-md-6">
                                    <input type="text" id="bottle_type_name" style="padding-top: 5px;">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="post_add_bottle_type" class="btn btn-success add_cbt">添加
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="box_type_modal" class="modal fade" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="feed-element">
                                <label class="col-md-3" style="padding-top:5px;">奶框规格</label>
                                <div class="col-md-6">
                                    <input type="text" id="box_spec_name" style="padding-top: 5px;">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" id="post_add_box_type" class="btn btn-success add_cbt">添加
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        $(document).ready(function () {
            var gap = "{{$gap_day}}";
            $('#gap_select').val(gap);

            if ($('#order_type_list').find('tr').length == 3) {
                $('#add_order_type').prop('disabled', true);
            }
        });

        $('#gap_day').click(function () {

            console.log('Set Gap Day');

            $.ajax({
                type: 'POST',
                url: API_URL + 'gongchang/jichuxinxi/shangpin/shangpincanshushezhi/set_gap_day',
                data: {
                    'gap_day': $('#gap_select').val(),
                },
                success: function (data) {
                    if (data.status == "success") {
                        alert("新单开始配送时间设定成功");

                    } else {
                        if (data.message)
                            alert(data.message);
                    }
                },
                error: function (data) {
                    console.log(data);
                },
            });
        });


        $('.deact_dt').click(function () {
            var cur_label = $(this);
            var fdts_id = $(this).parent().parent().find('.dts-name').data('fdtsid');
            var dataString = {'fdts_id': fdts_id, 'action': 'unuse'};
            $.ajax({
                type: 'POST',
                url: API_URL + 'gongchang/jichuxinxi/shangpin/shangpincanshushezhi/set_use_delivery_type',
                data: dataString,
                success: function (data) {
                    if (data.status == "success") {
                        cur_label.addClass('label-success');
                        cur_label.prev('.act_dt').removeClass('label-success');
                    }

                },
                error: function (data) {
                    console.log(data);
                },
            });


        });
        $('.act_dt').click(function () {
            //deactivate the delivery type
            var cur_label = $(this);
            var fdts_id = $(this).parent().parent().find('.dts-name').data('fdtsid');
            var dataString = {'fdts_id': fdts_id, 'action': 'use'};
            $.ajax({
                type: 'POST',
                url: API_URL + 'gongchang/jichuxinxi/shangpin/shangpincanshushezhi/set_use_delivery_type',
                data: dataString,
                success: function (data) {
                    if (data.status == "success") {
                        cur_label.addClass('label-success');
                        cur_label.next('.deact_dt').removeClass('label-success');
                    }
                },
                error: function (data) {
                    console.log(data);
                },
            });
        });

        $('#submit_delivery_time').click(function () {
            $.ajax({
                type: 'POST',
                url: API_URL + 'gongchang/jichuxinxi/shangpin/shangpincanshushezhi/delivery_time',
                data: {
                    'morning_start_at': $('#morning_start_at').val(),
                    'morning_end_at': $('#morning_end_at').val(),
                    'afternoon_start_at': $('#afternoon_start_at').val(),
                    'afternoon_end_at': $('#afternoon_end_at').val(),
                },
                success: function (data) {
                    console.log("delivery time set");
                    $('#delivery_time_msg').text("保存成功");

                    setTimeout(function () {
                        $("#delivery_time_msg").hide();
                    }, 5000);
                },
                error: function (data) {
                    console.log(data);
                },
            });
        });

        $('#add_order_type').click(function () {
            $('#order_type_modal').modal("show");
        });

        $('#add_box_type').click(function () {
            $('#box_type_modal').modal("show");
        });

        $('#add_bottle_type').click(function () {
            $('#bottle_type_modal').modal("show");
        });

        $('#post_add_order_type').click(function () {
            $.ajax({
                type: 'POST',
                url: API_URL + 'gongchang/jichuxinxi/shangpin/shangpincanshushezhi/order_type',
                data: {
                    'order_type': $('#sel-order-type').val(),
                },
                success: function (data) {
                    console.log(data);

                    var role = '';
                    role += '<tr id="order_type{{$ot->id}}">';
                    role += '<td class="col-md-4">{{$ot->name}}</td>';
                    role += '<td class="col-md-4">{{$ot->days}}</td>';
                    role += '<td class="col-md-4">';
                    role += '<button class="btn btn-sm btn-success delete-order-type" value="{{$ot->id}}">删除</button>';
                    role += '</td>';
                    role += '</tr>';

                    $('#order_type_list').append(role);
                    $('#frmroles').trigger("reset");
                    $('#order_type_modal').modal('hide');
                    location.reload();
                },
                error: function (data) {
                    console.log(data);
                },
            });
        });

        $(document).on('click', '.delete-order-type', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var button = $(this);

            $.confirm({
                icon: 'fa fa-warning',
                title: '删除订单类型',
                text: '你会真的删除订单类型吗？',
                confirmButton: "是",
                cancelButton: "不",
                confirmButtonClass: "btn-success",
                confirm: function () {
                    delete_order_type(button);
                },
                cancel: function () {
                    return;
                }
            });
        });

        function delete_order_type(button) {

            var order_type = $(button).val();
            var url = API_URL + 'gongchang/jichuxinxi/shangpin/shangpincanshushezhi/order_type';
            console.log("order type:" + order_type);
            $.ajax({
                type: "DELETE",
                url: url + '/' + order_type,
                success: function (data) {
                    console.log(data);
                    $("#order_type" + order_type).remove();
                    location.reload();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }

        $('#post_add_bottle_type').click(function () {
            console.log('adding bottle type');
            $.ajax({
                type: 'POST',
                url: API_URL + 'gongchang/jichuxinxi/shangpin/shangpincanshushezhi/bottle_type',
                data: {
                    'bottle_type_name': $('#bottle_type_name').val(),
                },
                success: function (data) {
                    console.log(data);

                    var role = '';
                    role += '<tr id="bottle_type' + data.id + '">';
                    role += '<td class="col-md-4">' + data.name + '</td>';
                    role += '<td class="col-md-4">' + data.number + '</td>';
                    role += '<td class="col-md-4">';
                    role += '<button class="btn btn-sm btn-success delete-bottle-type" value="' + data.id + '">删除</button>';
                    role += '</td>';
                    role += '</tr>';

                    $('#bottle_type_list').append(role);
                    $('#bottle_type_modal').modal('hide');
                },
                error: function (data) {
                    console.log(data);
                },
            });
        });


        $(document).on('click', '.delete-bottle-type', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var button = $(this);

            $.confirm({
                icon: 'fa fa-warning',
                title: '删除奶瓶规格',
                text: '你会真的删除奶瓶规格吗？',
                confirmButton: "是",
                cancelButton: "不",
                confirmButtonClass: "btn-success",
                confirm: function () {
                    delete_bottle_type(button);
                },
                cancel: function () {
                    return;
                }
            });


        });

        function delete_bottle_type(button) {
            var bottle_type = $(button).val();
            var url = API_URL + 'gongchang/jichuxinxi/shangpin/shangpincanshushezhi/bottle_type';
            $.ajax({
                type: "DELETE",
                url: url + '/' + bottle_type,
                success: function (data) {
                    console.log(data);
                    $("#bottle_type" + bottle_type).remove();
                    location.reload();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }

        $('#post_add_box_type').click(function () {
            console.log('adding box type');
            $.ajax({
                type: 'POST',
                url: API_URL + 'gongchang/jichuxinxi/shangpin/shangpincanshushezhi/box_type',
                data: {
                    'box_spec_name': $('#box_spec_name').val(),
                    'box_spec_number': $('#box_spec_number').val(),
                },
                success: function (data) {
                    console.log(data);

                    var role = '';
                    role += '<tr id="box_type' + data.id + '">';
                    role += '<td class="col-md-4">' + data.name + '</td>';
                    role += '<td class="col-md-4">' + data.number + '</td>';
                    role += '<td class="col-md-4">';
                    role += '<button class="btn btn-sm btn-success delete-box-type" value="' + data.id + '">删除</button>';
                    role += '</td>';
                    role += '</tr>';

                    $('#box_type_list').append(role);
                    $('#box_type_modal').modal('hide');
                },
                error: function (data) {
                    console.log(data);
                },
            });
        });

        $(document).on('click', '.delete-box-type', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var button = $(this);

            $.confirm({
                icon: 'fa fa-warning',
                title: '删除奶瓶规格',
                text: '你会真的删除奶瓶规格吗？',
                confirmButton: "是",
                cancelButton: "不",
                confirmButtonClass: "btn-success",
                confirm: function () {
                    delete_box_type(button);
                },
                cancel: function () {
                    return;
                }
            });

        });

        function delete_box_type(button) {
            var box_type = $(button).val();
            alert(box_type);
            var url = API_URL + 'gongchang/jichuxinxi/shangpin/shangpincanshushezhi/box_type';
            $.ajax({
                type: "DELETE",
                url: url + '/' + box_type,
                success: function (data) {
                    console.log(data);
                    $("#box_type" + box_type).remove();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }

    </script>
@endsection