@extends('zongpingtai.layout.master')
@section('css')
    <style>
        .filter_table{
            display:none;
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
                    财务管理
                </li>
                <li>
                    <a href="{{ url('zongpingtai/caiwu/zhanghuguanli')}}"><strong>账户管理</strong></a>
                </li>
            </ol>
        </div>

        <div class="row wrapper">
            <div class="wrapper-content">

                <div class="ibox-content">
                    <label class="col-md-1" style="padding-top:5px;">公司名称</label>
                    <div class="col-md-2">
                        <select id="filter_factory" class="chosen-select form-control">
                            <option value="none">全部</option>
                            @if(isset($fac_sta))
                                @foreach($fac_sta as $fs)
                                    <option value="{{$fs[0]}}">{{$fs[1]}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <label class="col-md-1" style="padding-top:5px;">奶站</label>
                    <div class="col-md-2">
                        <select id="filter_station" class="chosen-select form-control">
                            <option value="none">全部</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-md-offset-3">
                        <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
                    </div>
                </div>

                @if(isset($fac_sta))
                    @foreach($fac_sta as $fs)
                        <div class="one_factory" data-fid="{{$fs[0]}}">
                            <div class="ibox-content">
                                <div class="feed-element">
                                    <div class="col-md-1 col-md-2">
                                        <img alt="img" class="img-responsive" src="{{asset($fs[2])}}"
                                             style="height:60px;">
                                    </div>
                                    <label class="col-md-1 col-md-2" style="padding-top: 20px;">{{$fs[1]}}</label>
                                    <div class="col-md-2" style="padding-top:15px;">
                                        <a type="button"
                                           href="{{ url('zongpingtai/caiwu/zhanghu/zhanghugaikuang/'.$fs[0])}}"
                                           class="btn btn-success btn-outline" style="width:100%;">查看账户概况</a>
                                    </div>
                                </div>
                                <label class="gray-bg col-md-12" style="padding:5px;">公众号活跃度</label>
                            </div>
                            <div class="ibox float-e-margins">
                                <div class="ibox-content">
                                    <table class="table footable table-bordered order_table" data-page-size="10">
                                        <thead>
                                        <tr>
                                           <th data-sort-ignore="true">序号</th>
                                           <th data-sort-ignore="true">账户名称</th>
                                           <th data-sort-ignore="true">开户行</th>
                                           <th data-sort-ignore="true">开户人</th>
                                           <th data-sort-ignore="true">卡号</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 0;?>
                                        @if(isset($fs[3]))
                                            @foreach($fs[3] as $station)
                                                <tr data-sid="{{$station[0]}}">
                                                    <td>{{$i+1}}</td>
                                                    <td>{{$station[1]}}</td>
                                                    <td>交行</td>
                                                    <td>{{$station[2]}}</td>
                                                    <td>{{$station[3]}}</td>
                                                </tr>
                                                <?php $i++;?>
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
                                    <table class="table footable table-bordered filter_table" data-page-size="10">
                                        <thead>
                                        <tr>
                                           <th data-sort-ignore="true">序号</th>
                                           <th data-sort-ignore="true">账户名称</th>
                                           <th data-sort-ignore="true">开户行</th>
                                           <th data-sort-ignore="true">开户人</th>
                                           <th data-sort-ignore="true">卡号</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 0;?>
                                        @if(isset($fs[3]))
                                            @foreach($fs[3] as $station)
                                                <tr data-sid="{{$station[0]}}">
                                                    <td>{{$i+1}}</td>
                                                    <td>{{$station[1]}}</td>
                                                    <td>交行</td>
                                                    <td>{{$station[2]}}</td>
                                                    <td>{{$station[3]}}</td>
                                                </tr>
                                                <?php $i++;?>
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
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!-- Data picker -->
    <script type="text/javascript">

        var first = true;
        $(document).ready(function () {

        });

        $('button[data-action = "show_selected"]').click(function () {

            var fid = $('#filter_factory').val();
            var sid = $('#filter_station').val();

            if ((fid == "none")) {
                if (!first) {
                    location.reload();
                }
            } else if (fid != "none") {

                first = false;

                $('div.one_factory').each(function () {
                    if ($(this).data('fid') == fid) {
                        $(this).show();

                        var otable = $(this).find('.order_table');
                        var ftable = $(this).find('.filter_table');

                        if(sid != "none")
                        {
                            $(ftable).find('tbody').empty();

                            var tr = $(otable).find('tbody tr[data-sid="'+sid+'"]');
                            var trd = $(tr)[0].outerHTML;

                            var footable = $(ftable).data('footable');
                            footable.appendRow(trd);

                            $(otable).hide();
                            $(ftable).show();

                        } else {
                            $(otable).show();
                            $(ftable).hide();
                        }

                    } else {
                        $(this).hide();
                    }
                });


            }


        });

        //According to factory, show station list
        $('#filter_factory').on('change', function () {

            var factory_id = $(this).val();
            $('#factory_id').val(factory_id);

            var station_list = $('#filter_station');
            $(station_list).empty();

            if (factory_id == "none") {
                station_list.append('<option value="none">全部</option>');
                return;
            }

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
