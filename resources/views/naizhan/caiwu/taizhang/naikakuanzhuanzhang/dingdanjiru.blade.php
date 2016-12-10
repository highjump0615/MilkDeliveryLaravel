@extends('naizhan.layout.master')

@section('content')
    @include('naizhan.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('naizhan.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="{{ url('/naizhan/caiwu/taizhang') }}">财务管理</a>
                </li>
                <li>
                    <a href="{{ url('/naizhan/caiwu/taizhang') }}">奶站台帐</a>
                </li>
                <li class="active">
                    <a href="{{ url('/naizhan/caiwu/taizhang/naikakuanzhuanzhang/dingdanjiru') }}"><strong>奶卡订单转账记录</strong></a>
                </li>
            </ol>
        </div>

        <div class="row border-bottom">
            <div class="col-md-4">
                <div style="background-color:#5badd7;">
                    <label style="text-align:center; width:100%; color: black; padding:10px;">其他奶站订单总额</label>
                    <label style="text-align:center; width:100%; color: white; font-size: 25px; padding:10px;">{{$card_orders_total_money}}</label>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background-color:#5badd7;">
                    <label style="text-align:center; width:100%; color: black; padding:10px;">已转账金额</label>
                    <label style="text-align:center; width:100%; color: white; font-size: 25px; padding:10px;">{{$card_orders_checked_money}}</label>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background-color:#5badd7;">
                    <label style="text-align:center; width:100%; color: black; padding:10px;">未转账金额</label>
                    <label style="text-align:center; width:100%; color: white; font-size: 25px; padding:10px;">{{$card_orders_unchecked_money}}</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="ibox-content white-bg">
                <div class="col-md-7">
                </div>
                <div class="col-md-5">
                    <div class="col-md-6">
                        <a href="{{URL::to('/naizhan/caiwu/taizhang/naikakuanzhuanzhang/zhuanzhangjiru')}}" class="btn
                           btn-outline btn-success" type="button" style="width:100%;">查看已转账记录</a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{URL::to('/naizhan/caiwu/taizhang/naikakuanzhuanzhang/zhuanzhangzhangdan')}}"
                           class="btn
                           btn-outline btn-success" type="button" style="width:100%;">查看未转账单</a>
                    </div>
                </div>

                <div class="ibox-content">
                    <div class="form-group col-md-6 data_range_select">
                        <label class="col-md-3 col-md-offset-2 control-label" style="padding-top:5px;">选择查看日期:</label>
                        <div class="input-daterange input-group col-md-7" id="datepicker">
                            <input id="filter_start_date" type="text" class="input-sm form-control"/>
                            <span class="input-group-addon">至</span>
                            <input id="filter_end_date" type="text" class="input-sm form-control"/>
                        </div>
                    </div>
                    <div class="col-md-2 col-md-offset-1" style="padding-top:5px;">
                        <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
                    </div>
                </div>

                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <table class="footable table table-bordered" id="order_table" data-page-size="10">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">下单时间</th>
                                <th data-sort-ignore="true">用户</th>
                                <th data-sort-ignore="true">奶卡金额</th>
                                <th data-sort-ignore="true">奶卡卡号</th>
                                <th data-sort-ignore="true">配送奶站</th>
                                <th data-sort-ignore="true">订单号</th>
                                <th data-sort-ignore="true">状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($card_orders_not_checked))
                                @for($i= 0; $i< count($card_orders_not_checked); $i++)
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td class="o_date">{{$card_orders_not_checked[$i]->created_at}}</td>
                                        <td>{{$card_orders_not_checked[$i]->customer_name}}</td>
                                        <td>{{$card_orders_not_checked[$i]->total_amount}}</td>
                                        <td>{{$card_orders_not_checked[$i]->milk_card_id}}</td>
                                        <td class="o_delivery_station"
                                            data-sid="{{$card_orders_not_checked[$i]->delivery_station_id}}">{{$card_orders_not_checked[$i]->delivery_station_name}}</td>
                                        <td>{{$card_orders_not_checked[$i]->id}}</td>
                                        @if($card_orders_not_checked[$i]->transaction_id)
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
                        </table>

                        <table class="footable table table-bordered" id="filter_table" data-page-size="10"
                               style="display:none;">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">下单时间</th>
                                <th data-sort-ignore="true">用户</th>
                                <th data-sort-ignore="true">奶卡金额</th>
                                <th data-sort-ignore="true">奶卡卡号</th>
                                <th data-sort-ignore="true">配送奶站</th>
                                <th data-sort-ignore="true">订单号</th>
                                <th data-sort-ignore="true">状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="100%">
                                    <ul class="pagination pull-right"></ul>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
@section('script')
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
        });

        $('button[data-action="show_selected"]').click(function () {

            var order_table = $('#order_table');
            var filter_table = $('#filter_table');
            var filter_table_tbody = $('#filter_table tbody');

            var f_start_date = $('#filter_start_date').val();
            var f_end_date = $('#filter_end_date').val();

            //show only rows in filtered table that contains the above field value
            var filter_rows = [];
            var i = 0;

            $('#order_table').find('tbody tr').each(function () {
                var tr = $(this);

                o_date = tr.find('td.o_date').html();

                if ((f_start_date == "" && f_end_date == "")) {
                    tr.attr("data-show-1", "1");

                } else if (f_start_date == "" && f_end_date != "") {

                    var f2 = new Date(f_end_date);
                    var oo = new Date(o_date);
                    if (oo <= f2) {
                        tr.attr("data-show-1", "1");
                    } else {
                        tr.attr("data-show-1", "0");
                    }

                } else if (f_start_date != "" && f_end_date == "") {

                    var f1 = new Date(f_start_date);
                    var oo = new Date(o_date);
                    if (oo >= f1) {
                        tr.attr("data-show-1", "1");
                    } else {
                        tr.attr("data-show-1", "0");
                    }
                } else {
                    //f_start_date, f_end_date, o_date
                    var f1 = new Date(f_start_date);
                    var f2 = new Date(f_end_date);
                    var oo = new Date(o_date);
                    if (f1 <= f2 && f1 <= oo && oo <= f2) {
                        tr.attr("data-show-1", "1");

                    } else if (f1 >= f2 && f1 >= oo && oo >= f2) {
                        tr.attr("data-show-1", "1");

                    } else {
                        tr.attr("data-show-1", "0");
                    }
                }

                if (tr.attr("data-show-1") == "1" ) {
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