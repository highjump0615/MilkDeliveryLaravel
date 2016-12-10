@extends('zongpingtai.layout.master')

@section('content')
    @include('zongpingtai.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('zongpingtai.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="{{ url('zongpingtai/caiwu/zhangwujiesuan') }}">财务管理</a>
                </li>
                <li>
                    <a href="{{ url('zongpingtai/caiwu/zhangwujiesuan') }}">账务结算</a>
                </li>
                <li>
                    <a href="">账单转账</a>
                </li>
                <li class="active">
                    <a href=""><strong>账单明细</strong></a>
                </li>
            </ol>
        </div>

        <div class="row wrapper">
            <div class="wrapper-content">

                <div class="col-md-12">
                    <label class="col-md-2">配送奶站：</label>
                    <label class="col-md-2">{{$trans->delivery_station_name}}</label>
                </div>
                <div class="col-md-12">
                    <label class="col-md-2">账单号：</label>
                    <label class="col-md-2">{{$trans->id}}</label>
                </div>
                <div class="col-md-12">
                    <label class="col-md-2">账单日期：</label>
                    <div class="col-md-3">
                        <label>{{$trans->order_from}}&emsp; 至 &emsp;{{$trans->order_to}}</label>
                    </div>
                </div>
                <div class="col-md-12">
                    <label class="col-md-2">金额：</label>
                    <label class="col-md-2">{{$trans->total_amount}}</label>
                </div>
                <div class="col-md-12">
                    <label class="col-md-2">订单数：</label>
                    <label class="col-md-2">{{$trans->order_count}}</label>
                    <div class="col-md-2 col-md-offset-6 text-right" style="padding-top:5px;">
                        <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
                    </div>
                </div>

                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <table class="footable table table-bordered" id="order_table" data-page-size="10"  data-limit-navigation="5">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">时间</th>
                                <th data-sort-ignore="true">客户名</th>
                                <th data-sort-ignore="true">金额</th>
                                <th data-sort-ignore="true">订单编号</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($orders))
                                @for($i = 0; $i< count($orders); $i++)
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td>{{$orders[$i]->ordered_at}}</td>
                                        <td>{{$orders[$i]->customer_name}}</td>
                                        <td>{{$orders[$i]->total_amount}}</td>
                                        <td>{{$orders[$i]->number}}</td>
                                    </tr>
                                @endfor
                            @endif
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

            </div>
        </div>

    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
        });

        //Export
        $('button[data-action = "export_csv"]').click(function () {

            var od = $('#order_table').css('display');

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
            }

            console.log(sendData);

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

        //Print Table Data
        $('button[data-action = "print"]').click(function () {

            var sendData = [];
            var printContents = document.getElementById("order_table").outerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        });
    </script>
@endsection