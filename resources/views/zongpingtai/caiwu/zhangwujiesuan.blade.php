@extends('zongpingtai.layout.master')
@section('css')
    <style>
        select {
            width: 100%;
            height: 30px;
        }

        label {
            padding-top: 5px;
        }

        button#create_transaction {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 50px;
        }

        form#create_transaction_form {
            padding: 0;
        }
    </style>
@endsection

@section('content')
    @include('zongpingtai.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('zongpingtai.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="{{ url('zongpingtai/caiwu/zhangwujiesuan/zhangwujiesuan') }}">财务管理</a>
                </li>
                <li>
                    <a href="{{ url('zongpingtai/caiwu/zhangwujiesuan/zhangwujiesuan') }}"><strong>账务结算</strong></a>
                </li>
            </ol>
        </div>

        <div class="row wrapper">
            <div class="wrapper-content">

                <div class="ibox-content">
                    <label class="col-md-1 text-right">选择公司</label>
                    <div class="col-md-2">
                        <select id="filter_factory" class="chosen-select form-control">
                            @if(isset($factories))
                                @foreach($factories as $factory)
                                    <option value="{{$factory->id}}">{{$factory->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <label class="col-md-1 text-right">奶站</label>
                    <div class="col-md-2">
                        <select id="filter_station" class="chosen-select form-control">
                            <option value="none">全部</option>
                        </select>
                    </div>

                    <label class="col-md-1 control-label text-right" style="padding-top:5px;">选择日期:</label>
                    <div class="col-md-3">
                        <div class="input-daterange input-group">
                            <input id="filter_start_date" type="text" class="input-md form-control" name="start"/>
                            <span class="input-group-addon">至</span>
                            <input id="filter_end_date" type="text" class="input-md form-control" name="end"/>
                        </div>
                    </div>

                    <div class="col-md-2" style="padding-top:5px;">
                        <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
                    </div>
                </div>

                <div class="ibox-content">
                    <form class="col-md-7" method="POST" id="create_transaction_form">
                        <input type="hidden" id="factory_id" name="factory_id" value="none">
                        <input type="hidden" id="station_id" name="station_id" value="none">
                        <div class="col-md-8 form-group data_range_select">
                            <label class="col-md-4 control-label" style="padding-top:5px;">选择账单日期:</label>
                            <div class="input-daterange input-group col-md-8">
                                <input type="text" class="input-md form-control" name="start"/>
                                <span class="input-group-addon">至</span>
                                <input type="text" class="input-md form-control" name="end"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-md btn-warning" id="create_transaction">生成账单</button>
                        </div>
                    </form>

                    <div class="col-md-4 col-md-offset-1">
                        <a type="button" class="btn btn-md btn-success" id="show_history"
                           href="{{ url('/zongpingtai/caiwu/zhangwujiesuan/lishizhuanzhangjiru/')}}">查看历史转账记录</a>
                        <a type="button" class="btn btn-md btn-success" id="show_todo"
                           href="{{ url('/zongpingtai/caiwu/zhangwujiesuan/zhangdanzhuanzhang/')}}">查看未转记录</a>
                    </div>

                </div>

                <div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="order_table" class="footable table table-bordered" data-page-size="10">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">时间</th>
                                <th data-sort-ignore="true">客户名</th>
                                <th data-sort-ignore="true">金额</th>
                                <th data-sort-ignore="true">交易号</th>
                                <th data-sort-ignore="true">收款方</th>
                                <th data-sort-ignore="true">状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($wechat_orders))
                                @for($i= 0; $i< count($wechat_orders); $i++)
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td class="o_date">{{$wechat_orders[$i]->ordered_at}}</td>
                                        <td>{{$wechat_orders[$i]->customer_name}}</td>
                                        <td>{{$wechat_orders[$i]->total_amount}}</td>
                                        <td>{{$wechat_orders[$i]->number}}</td>
                                        <td class="o_factory_station" data-fid="{{$wechat_orders[$i]->factory_id}}"
                                            data-sid="{{$wechat_orders[$i]->delivery_station_id}}">{{$wechat_orders[$i]->delivery_station_name}}</td>
                                        @if($wechat_orders[$i]->transaction_id)
                                            <td>已生成</td>
                                        @else
                                            <td>未生成</td>
                                        @endif
                                    </tr>
                                @endfor
                            @endif
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="100%">
                                    <ul class="pagination pull-right"></ul>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                        <table id="filter_table" class="footable table table-bordered" data-page-size="10">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">时间</th>
                                <th data-sort-ignore="true">客户名</th>
                                <th data-sort-ignore="true">金额</th>
                                <th data-sort-ignore="true">交易号</th>
                                <th data-sort-ignore="true">收款方</th>
                                <th data-sort-ignore="true">状态</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
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

            </div>
        </div>

    </div>
@endsection

@section('script')
    <!-- Data picker -->
    <script type="text/javascript">
        var date = new Date();
        firstm = new Date(date.getFullYear(), date.getMonth(), 1);
        lastm = new Date(date.getFullYear(), date.getMonth() + 1, 0);

        $(document).ready(function () {
            $('.data_range_select .input-daterange').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
                clearBtn: true,
                startDate: firstm,
                endDate: lastm,
            });

            $('#filter_factory').trigger('change');

        });

        $('#create_transaction_form').submit(function (e) {
            e.preventDefault();

            var sendData = $(this).serializeArray();
            $.ajax({
                type: "POST",
                url: API_URL + 'zongpingtai/caiwu/zhangwu/create_transaction',
                data: sendData,
                success: function (data) {
                    console.log(data);
                    if (data.status == "success") {
                        var fid = data.factory_id;
                        window.location = SITE_URL + "zongpingtai/caiwu/zhangwujiesuan/zhangdanzhuanzhang/" + fid;
                    } else {
                        return;
                    }
                },
                error: function (data) {
                    console.log(data);

                }
            })

        });

        //According to factory, show station list
        $('#filter_factory').on('change', function () {

            var factory_id = $(this).val();
            $('#factory_id').val(factory_id);

            $('#show_history').attr('href', 'zhangwujiesuan/lishizhuanzhangjiru/' + factory_id);
            $('#show_todo').attr('href', 'zhangwujiesuan/zhangdanzhuanzhang/' + factory_id);

            var station_list = $('#filter_station');
            $(station_list).empty();

            var sendData = {'factory_id': factory_id};
            $.ajax({
                type: "POST",
                url: API_URL + 'factory_to_station',
                data: sendData,
                success: function (data) {
                    console.log(data);
                    var stations = data.stations;
                    if (stations) {
                        station_list.append('<option value="none">全部</option>');

                        for (var i = 0; i < stations.length; i++) {
                            var sid = stations[i][0];
                            var sname = stations[i][1];

                            var option = '<option value="' + sid + '">' + sname + '</option>';
                            station_list.append(option);
                        }

                        $(station_list).trigger('change');
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            })
        });

        $('#filter_station').change(function () {

            var station_id = $(this).val();
            $('#station_id').val(station_id);
        });

        $('button[data-action="show_selected"]').click(function () {

            var order_table = $('#order_table');

            var filter_table = $('#filter_table');
            var filter_table_tbody = $('#filter_table tbody');

            var f_factory = $('#filter_factory').val();
            var f_station = $('#filter_station').val();

            var f_start_date = $('#filter_start_date').val();
            var f_end_date = $('#filter_end_date').val();

            //show only rows in filtered table that contains the above field value
            var filter_rows = [];
            var i = 0;

            $('#order_table').find('tbody tr').each(function () {
                var tr = $(this);

                o_date = tr.find('td.o_date').html();
                o_factory = tr.find('td.o_factory_station').data('fid');
                o_station = tr.find('td.o_factory_station').data('sid');


                if (f_factory == "none" || f_factory == o_factory) {
                    tr.attr('data-show-1', '1');
                } else {
                    tr.attr('data-show-1', '0');
                }

                if (f_station == "none" || f_station == o_station) {
                    tr.attr('data-show-2', '1');
                } else {
                    tr.attr('data-show-2', '0');
                }


                if ((f_start_date == "" && f_end_date == "")) {
                    tr.attr("data-show-3", "1");

                } else if (f_start_date == "" && f_end_date != "") {

                    var f2 = new Date(f_end_date);
                    var oo = new Date(o_date);
                    if (oo <= f2) {
                        tr.attr("data-show-3", "1");
                    } else {
                        tr.attr("data-show-3", "0");
                    }

                } else if (f_start_date != "" && f_end_date == "") {

                    var f1 = new Date(f_start_date);
                    var oo = new Date(o_date);
                    if (oo >= f1) {
                        tr.attr("data-show-3", "1");
                    } else {
                        tr.attr("data-show-3", "0");
                    }
                } else {
                    //f_start_date, f_end_date, o_date
                    var f1 = new Date(f_start_date);
                    var f2 = new Date(f_end_date);
                    var oo = new Date(o_date);
                    if (f1 <= f2 && f1 <= oo && oo <= f2) {
                        tr.attr("data-show-3", "1");

                    } else if (f1 >= f2 && f1 >= oo && oo >= f2) {
                        tr.attr("data-show-3", "1");

                    } else {
                        tr.attr("data-show-3", "0");
                    }
                }

                if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1" ) && (tr.attr("data-show-3") == "1" )) {
                    //tr.removeClass('hide');
                    filter_rows[i] = $(tr)[0].outerHTML;
                    i++;
                    //filter_rows += $(tr)[0].outerHTML;
                }
                else {
                    //tr.addClass('hide');
                }
            });

            $(order_table).hide();
            $(filter_table_tbody).empty();

            var length = filter_rows.length;

            var footable = $('#filter_table').data('footable');

            for (i = 0; i < length; i++) {
                var trd = filter_rows[i];
                footable.appendRow(trd);
            }
            $(filter_table).show();
        });

        //Export
        $('button[data-action = "export_csv"]').click(function () {

            var od = $('#order_table').css('display');
            var fd = $('#filter_table').css('display');

            var sendData = [];

            var i = 0;
            if (od != "none") {
                //send order data
                $('#order_table thead tr').each(function () {
                    var tr = $(this);
                    var trdata = [];

                    var j = 0;
                    $(tr).find('th').each(function () {
                        var td = $(this);
                        var td_data = td.html().toString().trim();
                        if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
                            td_data = "";

                        trdata[j] = td_data;
                        j++;
                    });
                    sendData[i] = trdata;
                    i++;
                });

                $('#order_table tbody tr').each(function () {
                    var tr = $(this);
                    var trdata = [];

                    var j = 0;
                    $(tr).find('td').each(function () {
                        var td = $(this);
                        var td_data = td.html().toString().trim();
                        if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
                            td_data = "";
                        trdata[j] = td_data;
                        j++;
                    });
                    sendData[i] = trdata;
                    i++;
                });


            } else if (fd != "none") {
                //send filter data
                $('#filter_table thead tr').each(function () {
                    var tr = $(this);
                    var trdata = [];

                    var j = 0;
                    $(tr).find('th').each(function () {
                        var td = $(this);
                        var td_data = td.html().toString().trim();
                        if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
                            td_data = "";
                        trdata[j] = td_data;
                        j++;
                    });
                    sendData[i] = trdata;
                    i++;
                });

                $('#filter_table tbody tr').each(function () {
                    var tr = $(this);
                    var trdata = [];

                    var j = 0;
                    $(tr).find('td').each(function () {
                        var td = $(this);
                        var td_data = td.html().toString().trim();
                        if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
                            td_data = "";

                        trdata[j] = td_data;
                        j++;
                    });
                    sendData[i] = trdata;
                    i++;
                });

            } else {
                return;
            }

            var send_data = {"data": sendData};
            console.log(send_data);

            $.ajax({
                type: 'POST',
                url: API_URL + "export",
                data: send_data,
                success: function (data) {
                    console.log(data);
                    if (data.status == 'success') {
                        var path = data.path;
                        location.href = path;
                    }
                },
                error: function (data) {
                    //console.log(data);
                }
            })
        });

        //Print Table Data
        $('button[data-action = "print"]').click(function () {

            var od = $('#order_table').css('display');
            var fd = $('#filter_table').css('display');
            var sendData = [];
            var printContents;
            if (od != "none") {
                //print order data
                printContents = document.getElementById("order_table").outerHTML;
            } else if (fd != "none") {
                //print filter data
                printContents = document.getElementById("filter_table").outerHTML;
            } else {
                return;
            }
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        });

    </script>
@endsection