@extends('gongchang.layout.master')
@section('css')
    <style>
        .calc_statics label.title{
            background-color:#0b8cc5; color:#fff; width:100%; text-align: center;
        }

        .calc_statics label{
            width:100%;text-align: center;
        }

    </style>
@endsection

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
                    <a href=""><strong>奶站账户余额</strong></a>
                </li>
            </ol>
        </div>

        <!-- Static Info for calcultion account -->
        <div class="row white-bg">
            <div class="ibox-content">
                <div class="col-md-2">
                </div>
                <div class="col-md-6 calc_statics">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label class="title">期初余额</label>
                        </div>
                        <div class="col-md-8">
                            <label class="gray-bg">{{$station->term_start_amount}}</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label class="title">本期增加</label>
                        </div>
                        <div class="col-md-8">
                            <label class="gray-bg">{{$station->calc_in_total}}</label>
                        </div>

                    </div>
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label class="title">本期减少</label>
                        </div>
                        <div class="col-md-8">
                            <label class="gray-bg">{{$station->calc_out_total}}</label>
                        </div>

                    </div>
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label class="title">期末余额</label>
                        </div>
                        <div class="col-md-8">
                            <label class="gray-bg">{{$station->calculation_balance}}</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-8">
                </div>

                @if (!$is_station)
                <div class="col-md-4">
                    <button class="btn btn-md btn-success" data-toggle="modal" href="#calc_modal" type="button"
                            style="position: absolute; bottom:5px;"><i class="fa fa-plus"></i> 转出记录
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{--Selection--}}
        <div class="feed-element">

            <div class="col-md-4">
                <label class="col-md-4 text-right" style="padding-top: 8px;">项目</label>
                <div class="col-md-8">
                    <select id="filter_type" style="height:35px; width: 100%;"
                            tabindex="2">
                        <option value="none"></option>
                        <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_OUT_TRANSFER_MILK_FACTORY}}">
                            划转奶厂奶款
                        </option>
                        <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_OUT_SETTLEMENT_DELIVERY_COST}}">
                            结算配送费用
                        </option>
                        <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_OUT_SETTLEMENT_ROBATE_ROYALTY}}">
                            结算返利或提成费
                        </option>
                        <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_OUT_OTHER_USES}}">
                            其他用途划转
                        </option>
                        <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_OUT_MILK_CARD_ORDER_TRANSFER_FACTORY}}">
                            奶卡订单抵顶划转公司奶款
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group col-md-4" id="data_range_select">
                <label class="col-sm-3 control-label text-right" style="padding-top: 8px;">日期:</label>
                <div class="input-daterange input-group col-md-9" id="datepicker" style="padding-top: 5px;">
                    <input id="filter_start_date" type="text" class="input-sm form-control"/>
                    <span class="input-group-addon">至</span>
                    <input id="filter_end_date" type="text" class="input-sm form-control"/>
                </div>
            </div>

            <div class="col-md-3 col-md-offset-1">
                <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
                <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
            </div>
        </div>

        {{--Calculation History Table For Distribution = OUT--}}
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <table class="footable table table-bordered" id="order_table" data-page-size="10"  data-limit-navigation="5">
                    <thead>
                    <tr>
                        <th data-sort-ignore="true">摘要</th>
                        <th data-sort-ignore="true">时间</th>
                        <th data-sort-ignore="true">项目</th>
                        <th data-sort-ignore="true">金额</th>
                        <th data-sort-ignore="true">流水号</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (isset($calc_histories_out))
                        @foreach($calc_histories_out as $cho)
                            <tr>
                                <td>转出</td>
                                <td class="o_date">{{$cho->created_at}}</td>
                                <td class="o_type" data-type="{{$cho->type}}">{{$cho->type_name}}</td>
                                <td>{{$cho->amount}}</td>
                                <td>{{$cho->receipt_number}}</td>
                            </tr>
                        @endforeach
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

                <table class="footable table table-bordered" id="filter_table" data-page-size="10"
                       data-limit-navigation="5"
                       style="display:none;">
                    <thead>
                    <tr>
                        <th data-sort-ignore="true">摘要</th>
                        <th data-sort-ignore="true">时间</th>
                        <th data-sort-ignore="true">项目</th>
                        <th data-sort-ignore="true">金额</th>
                        <th data-sort-ignore="true">流水号</th>
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
                    </tfoot>
                </table>
            </div>
        </div>

        {{--Calculation History Add Modal--}}
        <div id="calc_modal" class="modal fade" aria-hidden="true">
            <div class="modal-dialog">
                <form id="calc_history_form">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#5badd7; color: #fff;">
                            <h4 class="modal-title">转出记录</h4>
                        </div>
                        <div class="modal-body">

                            <div class="row">
                                <div class="col-md-12">
                                    <label>日期：</label>
                                    <input required type="text" readonly name="time" id="time" value="{{$today}}"/>
                                    <br>
                                </div>
                                <div class="col-md-12">
                                    <label>奶站名称：</label><label>{{$station->name}}</label>
                                    <input required type="hidden" name="station_id" value="{{$station->id}}"/>
                                </div>
                                <div class="col-sm-12"><h3 class="m-t-none m-b"></h3>
                                    <div class="feed-element">
                                        <label class="col-md-3">项目:</label>
                                        <div class="col-md-9">
                                            <select required data-placeholder="Choose..." class="out_type form-control"
                                                    style="height:35px;" tabindex="2" name="type">
                                                <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_OUT_TRANSFER_MILK_FACTORY}}">
                                                    划转奶厂奶款
                                                </option>
                                                <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_OUT_SETTLEMENT_DELIVERY_COST}}">
                                                    结算配送费用
                                                </option>
                                                <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_OUT_SETTLEMENT_ROBATE_ROYALTY}}">
                                                    结算返利或提成费
                                                </option>
                                                <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_OUT_OTHER_USES}}">
                                                    其他用途划转
                                                </option>
                                                {{--<option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_OUT_MILK_CARD_ORDER_TRANSFER_FACTORY}}">--}}
                                                    {{--奶卡订单抵顶划转公司奶款--}}
                                                {{--</option>--}}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="feed-element">
                                        <label class="col-md-3">金额:</label>
                                        <div class="col-md-9">
                                            <input required type="text" name="amount" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="feed-element">
                                        <label class="col-md-3">流水号:</label>
                                        <div class="col-md-9">
                                            <input required type="text" class="form-control" name="receipt_number">
                                        </div>
                                    </div>
                                    <div class="feed-element">
                                        <label class="col-md-3">备注:</label>
                                        <div class="col-md-9">
                                            <input required type="text" class="form-control" name="comment">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-white">确定</button>
                            <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                        </div>
                    </div>
                </form>
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

        });

        $('#data_range_select .input-daterange').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            clearBtn: true,
            startDate: firstm,
            endDate: lastm
        });

        $('#calc_history_form').on('submit', function (e) {
            e.preventDefault();

            var sendData = $('#calc_history_form').serializeArray();
            console.log(sendData);

            $.ajax({
                type: "POST",
                url: API_URL + "gongchang/caiwu/naizhanzhanghuyue/add_calc_history",
                data: sendData,
                success: function (data) {
                    console.log(data);
                    if (data.status == "success") {
                        $('#calc_modal').modal("hide");
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                },
                error: function (data) {
                    console.log(data);
                    alert(data.message);
                }
            })
        });

        //Show selected
        $('button[data-action="show_selected"]').click(function () {

            var order_table = $('#order_table');
            var filter_table = $('#filter_table');
            var filter_table_tbody = $('#filter_table tbody');

            //get all selection
            var f_type = $('#filter_type').val();

            var f_start_date = $('#filter_start_date').val();
            var f_end_date = $('#filter_end_date').val();

            //show only rows in filtered table that contains the above field value
            var filter_rows = [];
            var i = 0;

            $('#order_table').find('tbody tr').each(function () {
                var tr = $(this);

                o_type = tr.find('td.o_type').data('type');
                o_date = tr.find('td.o_date').html();


                if (f_type == "none") {
                    tr.attr("data-show-1", "1");
                } else {
                    if (o_type == f_type)
                        tr.attr("data-show-1", "1");
                    else
                        tr.attr("data-show-1", "0");

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

                if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1")){
                    //tr.removeClass('hide');
                    filter_rows[i] = $(tr)[0].outerHTML;
                    i++;
                    //filter_rows += $(tr)[0].outerHTML;

                } else {
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
                            td_data="";

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
                            td_data="";
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
                            td_data="";
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
                            td_data="";

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