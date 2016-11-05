@extends('gongchang.layout.master')
@section('css')
    <style>
        .modal-body input {
            height: 35px;
            width: 100%;
            display: inline-block;
            position: relative;
        }

        .modal-header {
            background-color: #0b8cc5;
            overflow: auto;
            padding: 5px;
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
                    <a href="">财务管理</a>
                </li>
                <li>
                    <a href={{URL::to('/gongchang/caiwu/taizhang')}}>奶站账户台账</a>
                </li>
                <li>
                    <a href={{URL::to('/gongchang/caiwu/taizhang/naikakuanzhuanzhang')}}>奶卡款转账</a>
                </li>
                <li>
                    <a href=""><strong>奶卡转账账单</strong></a>
                </li>
            </ol>
        </div>

        <div class="row white-bg">
            <div class="ibox-content">
                <label class="col-md-1" style="padding-top: 5px;">录入奶站:</label>
                <div class=" col-md-2">
                    <select id="filter_station" class="chosen-select form-control"
                            style="width:100%; height: 35px;">
                        <option value="none">全部</option>
                        @if(isset($station_name_list))
                            @foreach($station_name_list as $sid=>$sname)
                                <option value="{{$sid}}">{{$sname}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2 col-md-offset-7" style="padding-top:5px;">
                    <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
                    <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                    <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <table id="order_table" class="footable table table-bordered" data-page-size="10">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">选择</th>
                            <th data-sort-ignore="true">奶站名称</th>
                            <th data-sort-ignore="true">生成时间</th>
                            <th data-sort-ignore="true">账单号</th>
                            <th data-sort-ignore="true">账单日期</th>
                            <th data-sort-ignore="true">金额</th>
                            <th data-sort-ignore="true">订单数量</th>
                            <th data-sort-ignore="true">状态</th>
                            <th data-sort-ignore="true">操作</th>
                            <th data-sort-ignore="true">备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (isset($ncts))
                            @foreach($ncts as $nct)
                                <?php
                                $j = 0;
                                $first_row_span = count($nct);
                                ?>
                                @foreach($nct as $nc)
                                    <tr>
                                        @if($j == 0)
                                            <td rowspan="{{$first_row_span}}">
                                                <input type="checkbox" checked class="i-checks"
                                                       data-tid="{{$nc->id}}"
                                                       data-station-id="{{$nc->delivery_station_id}}"/>
                                            </td>
                                            <td rowspan="{{$first_row_span}}">{{$nc->delivery_station_name}}</td>
                                        @endif
                                        <td class="o_station" data-trsid="{{$nc->id}}"
                                            data-sid="{{$nc->delivery_station_id}}">{{$nc->created_at}}</td>
                                        <td>{{$nc->id}}</td>
                                        <td>{{$nc->order_from}} ~ {{$nc->order_to}}</td>
                                        <td>{{$nc->total_amount}}</td>
                                        <td>{{$nc->order_count}}</td>
                                        <td>未转</td>
                                        <td>
                                            <a href="{{URL::to('/gongchang/caiwu/taizhang/naikazhangdanmingxi/'.$nc->id)}}">查看明细</a>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <?php $j++?>
                                @endforeach
                            @endforeach
                        @endif

                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="10">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>

                    <table id="filter_table" class="footable table table-bordered" data-page-size="10"  data-limit-navigation="5">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">选择</th>
                            <th data-sort-ignore="true">奶站名称</th>
                            <th data-sort-ignore="true">生成时间</th>
                            <th data-sort-ignore="true">账单号</th>
                            <th data-sort-ignore="true">账单日期</th>
                            <th data-sort-ignore="true">金额</th>
                            <th data-sort-ignore="true">订单数量</th>
                            <th data-sort-ignore="true">状态</th>
                            <th data-sort-ignore="true">操作</th>
                            <th data-sort-ignore="true">备注</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="10">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>

                    <div class="col-md-12">
                        <div class="col-md-8">
                            <label id="all_check"><input type="checkbox" checked class="i-checks">全选</label>
                        </div>
                        <div class="col-md-2 col-md-offset-2">
                            <button class="btn btn-success" data-action="insert_modal" type="button"
                                    style="width:100%;">转入</button>
                        </div>
                    </div>
                </div>
                <div id="insert_modal" class="modal fade" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form id="insert_modal_form" method="POST">
                                <div class="modal-header">
                                    <label class="col-md-offset-9 col-md-3" style="color:white;">时间：{{$today}}</label>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <table id="insert_modal_table" class="footable table table-bordered" data-page-size="5"  data-limit-navigation="5">
                                            <thead>
                                            <tr>
                                                <th data-sort-ignore="true">序号</th>
                                                <th data-sort-ignore="true">收款方</th>
                                                <th data-sort-ignore="true">账单数量</th>
                                                <th data-sort-ignore="true">合计金额</th>
                                                <th data-sort-ignore="true">备注</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="5">
                                                    <ul class="pagination pull-right"></ul>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-white">确定</button>
                                    <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                                </div>
                            </form>
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

        $('button[data-action="insert_modal"]').click(function () {

            var trans_id_list = [];
            //get selected trans id

            var od = $('#order_table').css('display');
            if(od!="none"){
                $('#order_table input.i-checks').each(function () {
                    var checked = $(this).prop("checked");
                    if (checked) {
                        var sid = $(this).data('station-id');

                        $('#order_table tbody tr td.o_station[data-sid="'+sid+'"]').each(function(){
                            var tid = $(this).data('trsid');
                            trans_id_list.push(tid);
                        });
                    }
                });
            } else {
                $('#filter_table input.i-checks').each(function () {
                    var checked = $(this).prop("checked");
                    if (checked) {
                        var sid = $(this).data('station-id');
                        $('#filter_table tbody tr td.o_station[data-sid="'+sid+'"]').each(function(){
                            var tid = $(this).data('trsid');
                            trans_id_list.push(tid);
                        });
                    }
                });
            }

            if(trans_id_list.length == 0)
            {
                return;
            }

            console.log(trans_id_list);

            var sendData = {'tids': trans_id_list};

            $('#insert_modal').modal('show');

            $.ajax({
                type: "POST",
                url: API_URL + 'gongchang/caiwu/taizhang/naikazhuanzhangzhangchan/get_trans_data',
                data: sendData,
                success: function (data) {
                    console.log(data);
                    if (data.status == "success") {
                        var trs_list = data.trs;

                        var itable = $('#insert_modal_table');
                        var itbody = $('#insert_modal_table tbody');
                        $(itbody).empty();

                        var footable = $(itable).data('footable');

                        for (var i = 0; i < trs_list.length; i++) {
                            var trs = trs_list[i];
                            var trd = '<tr><td>' + (i + 1) + '</td><td>' + trs[0] + '<input type="hidden" name="delivery_station_id[]" value="'+trs[4]+'"></td>';
                            trd+= '<td>' + trs[1] +'<input type="hidden" name="same_trs_ids[]" value="'+trs[3]+'"></td><td >' + trs[2] + '<input type="hidden" name="total_amount[]" value="'+trs[2]+'"</td>';
                            trd+= '<td><input name="comment[]" type="text"/></td></tr>';
                            footable.appendRow(trd);
                        }
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            });
        });


        $('#insert_modal_form').submit(function (e) {
            e.preventDefault();
            var sendData = $(this).serializeArray();
            console.log(sendData);

            $.ajax({
                type: 'POST',
                url: API_URL + 'gongchang/caiwu/taizhang/naikazhuanzhangzhangchan/complete_trans',
                data: sendData,
                success: function (data) {
                    console.log(data);
                    if (data.status = "success") {
                        show_success_msg(" 转账账单成功");
                        $('#insert_modal_form').modal('hide');
                        window.location.href = SITE_URL+"gongchang/caiwu/taizhang/naikazhuanzhangjilu";
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            })

        });

        $('button[data-action="show_selected"]').click(function () {

            var order_table = $('#order_table');
            var filter_table = $('#filter_table');
            var filter_table_tbody = $('#filter_table tbody');

            var f_station = $('#filter_station').val();

            //show only rows in filtered table that contains the above field value
            var filter_rows = [];
            var i = 0;

            $('#order_table').find('tbody tr').each(function () {
                var tr = $(this);
                o_station = tr.find('td.o_station').data('sid');

                if (f_station == "none" || f_station == o_station) {
                    tr.attr('data-show-1', '1');
                } else {
                    tr.attr('data-show-1', '0');
                }

                if (tr.attr("data-show-1") == "1") {
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

        $(document).ready(function(){

            $('#all_check .iCheck-helper').css('display', 'none');

            $('#all_check').click(function () {

                var od = $('#order_table').css('display');

                var check = $(this).find('.i-checks')[0];
                var checked = $(check).prop('checked');

                if (checked) {

                    $('#order_table input.i-checks').each(function () {
                        $(this).prop("checked", true);
                        $(this).closest('.icheckbox_square-green').addClass('checked');
                    });

                    if (od == "none") {
                        $('#filter_table input.i-checks').each(function () {
                            $(this).prop("checked", true);
                            $(this).closest('.icheckbox_square-green').addClass('checked');
                        });
                    }

                } else {


                    $('#order_table input.i-checks').each(function () {
                        $(this).prop("checked", false);
                        $(this).closest('.icheckbox_square-green').removeClass('checked');
                    });

                    if (od == "none") {
                        $('#filter_table input.i-checks').each(function () {
                            $(this).prop("checked", true);
                            $(this).closest('.icheckbox_square-green').removeClass('checked');
                        });
                    }
                }
            });

            $('.icheckbox_square-green').click(function () {

                var check = $(this).find('.i-checks')[0];
                var checked = $(check).prop('checked');

                var od = $('#order_table').css('display');

                if (checked) {
                    $('#order_table input.i-checks').each(function () {
                        $(this).prop("checked", true);
                        $(this).addClass('checked');
                    });

                    if (od == "none") {
                        $('#filter_table input.i-checks').each(function () {
                            $(this).prop("checked", true);
                            $(this).addClass('checked');
                        });
                    }

                } else {
                    $('#order_table input.i-checks').each(function () {
                        $(this).prop("checked", false);
                        $(this).removeClass('checked');
                    });

                    if (od == "none") {
                        $('#filter_table input.i-checks').each(function () {
                            $(this).prop("checked", true);
                            $(this).removeClass('checked');
                        });
                    }
                }
            });

        });
    </script>
@endsection	