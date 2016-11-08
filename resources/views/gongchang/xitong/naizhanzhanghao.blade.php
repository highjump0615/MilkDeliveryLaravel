@extends('gongchang.layout.master')
@section('css')
    <style>
        #filter_table tbody tr td a, #filter_table tbody tr td button {
            display: inline-block;
            width: auto;
        }

        .form-group label {
            padding-top: 5px;
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
                    <a href="">系统管理</a>
                </li>
                <li>
                    <a href=""><strong>奶站账号管理</strong></a>
                </li>
            </ol>
        </div>
        <div class="row white-bg">

            <div class="ibox-content">
                <div class="col-md-1">
                    <a class="btn btn-success"
                       href={{URL::to('/gongchang/xitong/naizhanzhanghao/tianjianaizhanzhanghu')}} type="button">添加奶站</a>
                </div>

                <div class="col-md-11">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="col-md-5 text-right">奶站名称:</label>
                            <div class="col-md-7">
                                <input typy="text" class="form-control" id="filter_name" style="height: 35px;" value="">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="col-md-5 text-right">用户类别:</label>
                            <select id="filter_type" class="form-control col-md-7"
                                    style="height: 35px; width: 50%; display: inline">
                                @foreach($dstype as $dt)
                                    <option value="{{$dt->id}}">{{$dt->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="col-md-5 text-right">区域:</label>
                            <div class="col-md-7">
                                <select required id="filter_province" name="province"
                                        class="province_list form-control col-md-3" style="width: 150px;">
                                    <option value="none">全部</option>
                                    @if (isset($provinces))
                                        @foreach($provinces as $province)
                                            <option value="{{$province->name}}">{{$province->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-success btn-outline" type="button" data-action="show_selected"><i
                                    class="fa fa-search"></i>筛选
                        </button>
                        <button class="btn btn-success btn-outline" type="button" data-action="export_csv"><i
                                    class="fa fa-download"></i>导出
                        </button>
                        <button class="btn btn-success btn-outline" type="button" data-action="print"><i
                                    class="fa fa-print"></i>打印
                        </button>
                    </div>
                </div>
            </div>

            <div class="ibox-content" id="alert" style="display: none">
                <p style="font-size: 20px; color: #ff0000; padding-left: 20px;">这个牛奶站正在使用</p>
            </div>
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <table id="origin_table" class="footable table table-bordered" data-page-size="10">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">ID</th>
                            <th data-sort-ignore="true">奶站名称</th>
                            <th data-sort-ignore="true">区域</th>
                            <th data-sort-ignore="true">用户类别</th>
                            <th data-sort-ignore="true">最后登录IP</th>
                            <th data-sort-ignore="true">状态</th>
                            <th data-sort-ignore="true">管理操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 0; ?>
                        @foreach($stations as $station)
                            <?php $i++; ?>
                            <tr data-sid="{{$station->id}}"  class="row-hover-light-blue">
                                <td>{{$i}}</td>
                                <td class="o_name">{{$station->name}}</td>
                                <td class="o_province">{{$station->province_name}}</td>
                                <td class="o_type" data-id="{{$station->station_type}}">{{$station->type_name}}</td>
                                <td>{{$station->last_used_ip}}</td>
                                <td><input type="checkbox" class="js-switch js-check-change"
                                           @if($station->status == \App\Model\DeliveryModel\DeliveryStation::DELIVERY_STATION_STATUS_ACTIVE)checked @endif />
                                </td>
                                <td>
                                    <button data-sid="{{$station->id}}" class="edit_station btn-success btn btn-sm">修改</button>
                                    <button class="btn btn-success btn-outline btn-sm" data-action="delete_station"
                                            disabled
                                            data-sid="{{$station->id}}">删除
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
                    <table id="filter_table" class="footable table table-bordered" data-page-size="10">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">ID</th>
                            <th data-sort-ignore="true">奶站名称</th>
                            <th data-sort-ignore="true">区域</th>
                            <th data-sort-ignore="true">用户类别</th>
                            <th data-sort-ignore="true">最后登录IP</th>
                            <th data-sort-ignore="true">状态</th>
                            <th data-sort-ignore="true">管理操作</th>
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
        </div>
    </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">

        $('button[data-action="delete_station"]').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            var sid = $(this).data('sid');
            $.confirm({
                icon: 'fa fa-warning',
                title: '删除奶站',
                text: '你会真的删除奶站吗？',
                confirmButton: "是",
                cancelButton: "不",
                confirmButtonClass: "btn-success",
                confirm: function () {
                    delete_station(sid);
                },
                cancel: function () {
                    return;
                }
            });
        });

        function delete_station(sid) {
            $.ajax({
                type: "post",
                url: API_URL + 'gongchang/xitong/naizhanzhanghao/delete_station',
                data: {
                    'station_id': sid,
                },
                success: function (data) {
                    console.log(data);
                    if (data.status != "success") {
                        alert('奶站删除失败');
                    } else {
                        alert("奶站已成功删除");
                        location.reload();
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });

        }

        $(document).ready(function () {
            //Switchery
            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
            elems.forEach(function (html) {
                var switchery = new Switchery(html);
            });
        })

        $('#origin_table tbody tr').on('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            var station_id = $(this).data('sid');
            if(station_id)
            {
                var url = SITE_URL+"/gongchang/xitong/naizhanzhanghao/tianjianaizhanzhanghu/zhanghuxiangqing-chakan/"+station_id;
                window.location.href = url;
            }
        });

        $('.js-switch').change(function (e) {

            e.preventDefault();
            e.stopPropagation();

            var checked = $(this).prop('checked');
            var sid = $(this).closest('tr').data('sid');

            $.ajax({
                type: "post",
                url: API_URL + 'gongchang/xitong/naizhanzhanghao/change_status_of_station',
                data: {
                    'station_id': sid,
                    'checked': checked,
                },
                success: function (data) {
                    console.log(data);
                    if (data.status == "success") {

                    } else {
                        if (data.message) {
                            alert(data.message);
                        }
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });


        });

        //Filter Function
        $('button[data-action="show_selected"]').click(function () {

            var origin_table = $('#origin_table');
            var filter_table = $('#filter_table');
            var filter_table_tbody = $('#filter_table tbody');

            //get all selection
            var f_name = $('#filter_name').val().trim().toLowerCase();
            var f_type = $('#filter_type').val();
            var f_province = $('#filter_province').val().trim().toLowerCase();

            //show only rows in filtered table that contains the above field value
            var filter_rows = [];
            var i = 0;

            $('#origin_table').find('tbody tr').each(function () {
                var tr = $(this);
                o_name = tr.find('td.o_name').html().toString().toLowerCase();
                o_type = tr.find('td.o_type').data('id');
                o_province = tr.find('td.o_province').html().toString().toLowerCase();

                //customer
                if ((f_name != "" && o_name.includes(f_name)) || (f_name == "")) {
                    tr.attr("data-show-1", "1");
                } else {
                    tr.attr("data-show-1", "0")
                }

                if ((f_type != "" && o_type == f_type) || (f_type == "") || (f_type == undefined)) {
                    tr.attr("data-show-2", "1");
                } else {
                    tr.attr("data-show-2", "0")
                }

                if ((f_province != "none" && o_province.includes(f_province)) || (f_province == "")) {
                    tr.attr("data-show-3", "1");
                } else {
                    tr.attr("data-show-3", "0")
                }


                if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1") && (tr.attr("data-show-3") == "1")) {
                    filter_rows[i] = $(tr)[0].outerHTML;
                    i++;
                } else {
                    //tr.addClass('hide');
                }
            });

            $(origin_table).hide();
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

            var od = $('#origin_table').css('display');
            var fd = $('#filter_table').css('display');

            var sendData = [];

            var i = 0;
            if (od != "none") {
                //send order data
                $('#origin_table thead tr').each(function () {
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

                $('#origin_table tbody tr').each(function () {
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

        //Print
        $('button[data-action = "print"]').click(function () {

            var od = $('#origin_table').css('display');
            var fd = $('#filter_table').css('display');

            var sendData = [];

            var printContents;
            if (od != "none") {
                //print order data
                printContents = document.getElementById("origin_table").outerHTML;

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

        $('#origin_table .edit_station').on('click', function(e){

            e.preventDefault();
            e.stopPropagation();
            var station_id = $(this).data('sid');

            var url =SITE_URL +'/gongchang/xitong/naizhanzhanghao/naizhanxiugai/'+station_id;

            window.location.href = url;
        });



    </script>
@endsection