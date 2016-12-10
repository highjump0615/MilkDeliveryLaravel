@extends('gongchang.layout.master')

@section('content')

    <!-- 奶站需要奶站的菜单 -->
    @if ($is_station)
        @include('naizhan.theme.sidebar')
    @else
        @include('gongchang.theme.sidebar')
    @endif

    <div id="page-wrapper" class="gray-bg dashbard-1">

        <!-- 头部 -->
        @if ($is_station)
            @include('naizhan.theme.header')
        @else
            @include('gongchang.theme.header')
        @endif

        <!-- 面包屑导航 -->
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">财务管理</a>
                </li>
                <li>
                    @if ($is_station)
                        <a href={{URL::to('/naizhan/caiwu/taizhang')}}>奶站账户台账</a>
                    @else
                        <a href={{URL::to('/gongchang/caiwu/taizhang')}}>奶站账户台账</a>
                    @endif
                </li>
                <li>
                    <a href=""><strong>其他奶站转账</strong></a>
                </li>
            </ol>
        </div>

        <div class="row border-bottom">
            <div class="col-md-4">
                <div style="background-color:#5badd7;">
                    <label style="text-align:center; width:100%; color: black; padding:10px;">其他奶站订单总额</label>
                    <label style="text-align:center; width:100%; color: white; font-size: 25px; padding:10px;">{{$other_orders_total_money}}</label>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background-color:#5badd7;">
                    <label style="text-align:center; width:100%; color: black; padding:10px;">已转账金额</label>
                    <label style="text-align:center; width:100%; color: white; font-size: 25px; padding:10px;">{{$other_orders_checked_money}}</label>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background-color:#5badd7;">
                    <label style="text-align:center; width:100%; color: black; padding:10px;">未转账金额</label>
                    <label style="text-align:center; width:100%; color: white; font-size: 25px; padding:10px;">{{$other_orders_unchecked_money}}</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="ibox-content white-bg">
                <div class="col-md-7">
                </div>
                <div class="col-md-5">
                    <div class="col-md-6">
                        <a href="@if ($is_station) {{URL::to('/naizhan/caiwu/taizhang/qitanaizhanzhuanzhang/zhuanzhangjilu')}}@else {{URL::to('/gongchang/caiwu/taizhang/qitanaizhanzhuanzhang/zhuanzhangjilu')}}@endif"
                           class="btn btn-outline btn-success"
                           type="button"
                           style="width:100%;">查看已转账记录</a>
                    </div>
                    <div class="col-md-6">
                        <a href="@if ($is_station) {{URL::to('/naizhan/caiwu/taizhang/qitanaizhanzhuanzhang/zhuanzhangzhangdan')}}@else {{URL::to('/gongchang/caiwu/taizhang/qitanaizhanzhuanzhang/zhuanzhangzhangchan')}}@endif"
                           class="btn btn-outline btn-success"
                           type="button"
                           style="width:100%;">查看未转账单</a>
                    </div>
                </div>

                <div class="ibox-content">
                    <div class="col-md-3">
                        <label class="col-md-4">奶站选择</label>
                        <div class="col-md-8">
                            <select data-placeholder="Choose..." id="filter_station"
                                    style="width:100%; height: 30px;"
                                    tabindex="2">
                                <option value="none"></option>
                                @if(isset($stations))
                                    @foreach($stations as $station)
                                        <option value="{{$station->id}}">{{$station->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
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

                @if (!$is_station)
                <div class="ibox-content">
                    <form action="qitanaizhanzhuanzhang/create_transaction" method="POST">
                        <div class="form-group col-md-6 col-md-offset-3 data_range_select">
                            <label class="col-md-3 col-md-offset-2 control-label" style="padding-top:5px;">
                                选择账单日期:</label>
                            <div class="input-daterange input-group col-md-7" id="datepicker">
                                <input type="text" class="input-sm form-control" name="start"/>
                                <span class="input-group-addon">至</span>
                                <input type="text" class="input-sm form-control" name="end"/>
                            </div>
                        </div>
                        <div class="col-md-2 col-md-offset-1" style="padding-top:5px;">
                            <button type="submit" class="btn btn-danger btn-md">生成账单</button>
                        </div>
                    </form>
                </div>
                @endif

                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <table class="footable table table-bordered" id="order_table" data-page-size="10">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">下单时间</th>
                                <th data-sort-ignore="true">录入奶站</th>
                                <th data-sort-ignore="true">用户</th>
                                <th data-sort-ignore="true">金额</th>
                                <th data-sort-ignore="true">配送奶站</th>
                                <th data-sort-ignore="true">订单号</th>
                                <th data-sort-ignore="true">状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($other_orders_nc))
                                @for($i= 0; $i< count($other_orders_nc); $i++)
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td class="o_date">{{$other_orders_nc[$i]->created_at}}</td>
                                        <td class="o_station"
                                            data-sid="{{$other_orders_nc[$i]->station_id}}">{{$other_orders_nc[$i]->station_name}}</td>
                                        <td>{{$other_orders_nc[$i]->customer_name}}</td>
                                        <td>{{$other_orders_nc[$i]->total_amount}}</td>
                                        <td class="o_delivery_station"
                                            data-sid="{{$other_orders_nc[$i]->delivery_station_id}}">{{$other_orders_nc[$i]->delivery_station_name}}</td>
                                        <td>{{$other_orders_nc[$i]->id}}</td>
                                        @if($other_orders_nc[$i]->transaction_id)
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
                                <td colspan="8">
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
                                <th data-sort-ignore="true">录入奶站</th>
                                <th data-sort-ignore="true">用户</th>
                                <th data-sort-ignore="true">金额</th>
                                <th data-sort-ignore="true">配送奶站</th>
                                <th data-sort-ignore="true">订单号</th>
                                <th data-sort-ignore="true">状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="8">
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
    <!-- Data picker -->
    <script type="text/javascript">
//        var date = new Date();
//        firstm = new Date(date.getFullYear(), date.getMonth(), 1);
//        lastm = new Date(date.getFullYear(), date.getMonth() + 1, 0);

        $(document).ready(function () {
            $('.data_range_select .input-daterange').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
                clearBtn: true,
//                startDate: firstm,
//                endDate: lastm,
            });
        });

        $('button[data-action="show_selected"]').click(function () {

            var order_table = $('#order_table');
            var filter_table = $('#filter_table');
            var filter_table_tbody = $('#filter_table tbody');

            var f_station = $('#filter_station').val();
            var f_start_date = $('#filter_start_date').val();
            var f_end_date = $('#filter_end_date').val();

            //show only rows in filtered table that contains the above field value
            var filter_rows = [];
            var i = 0;

            $('#order_table').find('tbody tr').each(function () {
                var tr = $(this);

                o_date = tr.find('td.o_date').html();
                o_station = tr.find('td.o_station').data('sid');

                if (f_station == "none" || f_station == o_station) {
                    tr.attr('data-show-1', '1');
                } else {
                    tr.attr('data-show-1', '0');
                }

                if ((f_start_date == "" && f_end_date == "")) {
                    tr.attr("data-show-2", "1");

                } else if (f_start_date == "" && f_end_date != "") {

                    var f2 = new Date(f_end_date);
                    var oo = new Date(o_date);
                    if (oo <= f2) {
                        tr.attr("data-show-2", "1");
                    } else {
                        tr.attr("data-show-2", "0");
                    }

                } else if (f_start_date != "" && f_end_date == "") {

                    var f1 = new Date(f_start_date);
                    var oo = new Date(o_date);
                    if (oo >= f1) {
                        tr.attr("data-show-2", "1");
                    } else {
                        tr.attr("data-show-2", "0");
                    }
                } else {
                    //f_start_date, f_end_date, o_date
                    var f1 = new Date(f_start_date);
                    var f2 = new Date(f_end_date);
                    var oo = new Date(o_date);
                    if (f1 <= f2 && f1 <= oo && oo <= f2) {
                        tr.attr("data-show-2", "1");

                    } else if (f1 >= f2 && f1 >= oo && oo >= f2) {
                        tr.attr("data-show-2", "1");

                    } else {
                        tr.attr("data-show-2", "0");
                    }
                }

                if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1" )) {
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