@extends('gongchang.layout.master')

@section('css')
    <!-- Multi Select Combo -->
    <link href="<?=asset('css/plugins/multiselect/style.css') ?>" rel="stylesheet">
    <style>
        .footable tr td {
            background: white;
        }
    </style>
@endsection

@section('content')
    @include('gongchang.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('gongchang.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">基础信息管理</a>
                </li>
                <li>
                    <a href={{URL::to('/gongchang/jichuxinxi/naizhan')}}><strong>奶站管理</strong></a>
                </li>
                <li>
                    <strong>配送范围-查看编辑</strong>
                </li>
            </ol>
        </div>

        <div class="row white-bg">
            <div class="ibox-content" style="padding-left: 20px;">
                <button class="btn btn-success col-md-1" id="add_address" type="button"><i class="fa fa-plus"></i>添加小区
                </button>
                <div>
                    <label style="margin-left: 15px; padding-top: 5px;">{{$station->name}}</label>
                    <label style="padding-top: 5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <label style="padding-top: 5px;">{{$province.' '.$city.' '.$district}}</label>
                </div>
            </div>

            <input type="hidden" id="station_id" value="{{$station->id}}">

            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <table id="delivery_area_table" class="footable table table-bordered" data-page-size="5">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">街道</th>
                            <th data-sort-ignore="true">配送范围</th>
                            <th data-sort-ignore="true">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 0; ?>
                        @foreach($area_address as $street_id=>$street)
                            <tr data-street-id="{{$street_id}}">
                                <td>{{$street[0]}}</td>
                                <td>
                                    @foreach($street[1] as $xiaoqu_id => $xiaoqu_name)
                                        {{$xiaoqu_name}}
                                    @endforeach
                                </td>
                                <td>
                                    <button class="btn btn-success btn-sm" data-action="modify_xiaoqu"
                                            data-street-id="{{$street_id}}" data-street-name="{{$street[0]}}">修改
                                    </button>
                                    <button class="btn btn-success btn-sm" data-action="delete_xiaoqu"
                                            data-street-id="{{$street_id}}" value="{{$street_id}}">删除
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="100%">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div id="change_modal_form" class="modal fade" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <form id="change_xiaoqu_form">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group" style="padding-top: 20px;">
                                            <label class="col-sm-3">街道</label>
                                            <div class="col-sm-9">
                                                <input id="selected_street_to_change" name="selected_street"
                                                       class="form-control" value="" type="text"
                                                       readonly/>
                                                <input type="hidden" id="street_id_to_change" name="street_id_to_change"
                                                       value="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">小区名称：</label>
                                            <div class="row">
                                                <div class="col-xs-5">
                                                    <select name="from[]" id="js_multiselect_from_1"
                                                            class="js-multiselect1 form-control" size="8"
                                                            multiple="multiple">
                                                    </select>
                                                </div>

                                                <div class="col-xs-2">
                                                    <button type="button" id="js_right_All_1" class="btn btn-block">
                                                        <i class="glyphicon glyphicon-forward"></i>
                                                    </button>
                                                    <button type="button" id="js_right_Selected_1"
                                                            class="btn btn-block">
                                                        <i class="glyphicon glyphicon-chevron-right"></i>
                                                    </button>
                                                    <button type="button" id="js_left_Selected_1" class="btn btn-block">
                                                        <i class="glyphicon glyphicon-chevron-left"></i>
                                                    </button>
                                                    <button type="button" id="js_left_All_1" class="btn btn-block">
                                                        <i class="glyphicon glyphicon-backward"></i>
                                                    </button>
                                                </div>

                                                <div class="col-xs-5">
                                                    <select name="to[]" id="js_multiselect_to_1" class="form-control"
                                                            size="8" multiple="multiple"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-white"
                                        id="submit_change_form">确定
                                </button>
                                <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="add_modal_form" class="modal fade" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <form id="add_xiaoqu_form">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group" style="padding-top: 20px;">
                                            <label class="col-sm-3">街道</label>
                                            <div class="col-sm-9">
                                                <select id="add_street_list" style="width: 100%; height:35px;">
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-12">小区名称：</label>
                                            <div class="row">
                                                <div class="col-xs-5">
                                                    <select name="from[]" id="js_multiselect_from_2"
                                                            class="js-multiselect2 form-control" size="8"
                                                            multiple="multiple">
                                                    </select>
                                                </div>

                                                <div class="col-xs-2">
                                                    <button type="button" id="js_right_All_2" class="btn btn-block"><i
                                                                class="glyphicon glyphicon-forward"></i></button>
                                                    <button type="button" id="js_right_Selected_2"
                                                            class="btn btn-block"><i
                                                                class="glyphicon glyphicon-chevron-right"></i></button>
                                                    <button type="button" id="js_left_Selected_2" class="btn btn-block">
                                                        <i
                                                                class="glyphicon glyphicon-chevron-left"></i></button>
                                                    <button type="button" id="js_left_All_2" class="btn btn-block"><i
                                                                class="glyphicon glyphicon-backward"></i></button>
                                                </div>

                                                <div class="col-xs-5">
                                                    <select name="to[]" id="js_multiselect_to_2" class="form-control"
                                                            size="8" multiple="multiple"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="submit_add_form" class="btn btn-white">确定</button>
                                <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script src="<?=asset('js/plugins/multiselect/multiselect.min.js') ?>"></script>

    <script>
        //Availabe Address for this station
        <?php
        $avail = json_encode($available_address);
        echo "var avail_obj = ". $avail . ";\n";

        $used = json_encode($area_address);
        echo "var used_obj = ". $used . ";\n";
        ?>

        console.log(avail_obj);
        console.log(used_obj);

        //MultiSelect
        $(document).ready(function () {

            $('.js-multiselect1').multiselect({
                left: "#js_multiselect_from_1",
                right: '#js_multiselect_to_1',
                rightAll: '#js_right_All_1',
                rightSelected: '#js_right_Selected_1',
                leftSelected: '#js_left_Selected_1',
                leftAll: '#js_left_All_1'
            });

            $('.js-multiselect2').multiselect({
                left: "#js_multiselect_from_2",
                right: '#js_multiselect_to_2',
                rightAll: '#js_right_All_2',
                rightSelected: '#js_right_Selected_2',
                leftSelected: '#js_left_Selected_2',
                leftAll: '#js_left_All_2'
            });
        });

        //Modify Xiaoqus
        $(document).on('click', '[data-action="modify_xiaoqu"]', function () {

            var street_id = $(this).data('street-id');
            var street_name = $(this).data('street-name');

            $('#selected_street_to_change').val(street_name);
            $('#street_id_to_change').val(street_id);

            //get used xiaoqu and show them in right panel
            var left_select = $('select#js_multiselect_from_1');
            $(left_select).empty();

            var right_select = $('select#js_multiselect_to_1');
            $(right_select).empty();

            $.each(used_obj[street_id][1], function (xiaoqu_id, xiaoqu_name) {
                var option = '<option value="' + xiaoqu_id + '">' + xiaoqu_name + '</option>';
                $(right_select).append(option);
            });

            $.each(avail_obj[street_id][1], function (xiaoqu_id, xiaoqu_name) {
                if (!(used_obj[street_id][1]).hasOwnProperty(xiaoqu_id)) {
                    var option = '<option value="' + xiaoqu_id + '">' + xiaoqu_name + '</option>';
                    $(left_select).append(option);
                }
            });

            $('#change_modal_form').modal('show');
        });
        //Change Xiaoqu of station's delivery area
        $('#submit_change_form').on('click', function () {

            var station_id = $('#station_id').val();
            //make the elements in right list to be selected
            $('select#js_multiselect_to_1 > option').prop('selected', true);

            var sendData = $('#change_xiaoqu_form').serializeArray();
            sendData.push({'name': "station_id", 'value': station_id});
            console.log(sendData);

            $('select#js_multiselect_to_1 > option').prop('selected', false);

            $.ajax({
                type: "POST",
                url: API_URL + 'gongchang/jichuxinxi/naizhan/peisongfanwei-chakanbianji/change_delivery_area',
                data: sendData,
                success: function (data) {
                    console.log(data);
                    location.reload();
                },
                error: function (data) {
                    console.log(data);
                },
            })
        });

        //Delete Delivery Area of Station
        $(document).on('click', '[data-action="delete_xiaoqu"]', function () {
            var button = $(this);

            $.confirm({
                icon: 'fa fa-warning',
                title: '删除配送范围',
                text: '你会真的删除配送范围吗？',
                confirmButton: "是",
                cancelButton: "不",
                confirmButtonClass: "btn-success",
                confirm: function () {
                    delete_delivery_area(button);
                },
                cancel: function () {
                }
            });
        });
        function delete_delivery_area(button) {
            var street_id = $(button).data('street-id');
            var station_id = $('#station_id').val();

            $.ajax({
                type: "POST",
                url: API_URL + 'gongchang/jichuxinxi/naizhan/peisongfanwei-chakanbianji/delete_delivery_area',
                data: {
                    'street_id': street_id,
                    'station_id': station_id,
                },
                success: function (data) {
                    console.log(data);
                    location.reload();
                },
                error: function (data) {
                    console.log(data);
                },
            })
        }

        $(document).on('click', '#add_address', function () {

            //add street list to the select
            $('#add_street_list').empty();

            $.each(avail_obj, function (street_id, street) {

                if (used_obj.hasOwnProperty(street_id)) {
//                    var option = '<option disabled value="' + street_id + '">' + street[0] + '<i class="fa fa-asterisk">*</i></option>';
                    return true;    // continue
                } else {
                    var option = '<option value="' + street_id + '">' + street[0] + '</option>';
                }
                $('#add_street_list').append(option);
            });

            var street_id = $('#add_street_list').val();
            if(!street_id){
                show_warning_msg('所有街道已添加');
                return;
            }

            //get used xiaoqu and show them in right panel
            var left_select = $('select#js_multiselect_from_2');
            $(left_select).empty();

            var right_select = $('select#js_multiselect_to_2');
            $(right_select).empty();

            $.each(avail_obj[street_id][1], function (xiaoqu_id, xiaoqu_name) {
                var option = '<option value="' + xiaoqu_id + '">' + xiaoqu_name + '</option>';
                $(left_select).append(option);
            });

            $('#add_modal_form').modal('show');
        });

        $(document).on('change', '#add_street_list', function () {
            var street_id = $(this).val();

            //get used xiaoqu and show them in right panel
            var left_select = $('select#js_multiselect_from_2');
            $(left_select).empty();

            var right_select = $('select#js_multiselect_to_2');
            $(right_select).empty();

            $.each(avail_obj[street_id][1], function (xiaoqu_id, xiaoqu_name) {
                var option = '<option value="' + xiaoqu_id + '">' + xiaoqu_name + '</option>';
                $(left_select).append(option);
            });

        });

        $('#submit_add_form').on('click', function () {

            var station_id = $('#station_id').val();
            var street_id = $('#add_street_list').val();
            //make the elements in right list to be selected
            $('select#js_multiselect_to_2 > option').prop('selected', true);

            var sendData = $('#add_xiaoqu_form').serializeArray();
            sendData.push({'name': "station_id", 'value': station_id});
            sendData.push({'name': "street_id", 'value': street_id});
            console.log(sendData);

            $('select#js_multiselect_to_2 > option').prop('selected', false);

            $.ajax({
                type: "POST",
                url: API_URL + 'gongchang/jichuxinxi/naizhan/peisongfanwei-chakanbianji/add_delivery_area',
                data: sendData,
                success: function (data) {
                    console.log(data);
                    if(data.status =='success')
                    {
                        $('#add_modal_form').modal('hide');
                        location.reload();
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            })
        });

    </script>
@endsection