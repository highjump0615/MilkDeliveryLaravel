@extends('gongchang.layout.master')

@section('css')
    <link href="<?=asset('css/pages/order_list.css') ?>" rel="stylesheet">
@endsection

@section('content')
    @include('gongchang.theme.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('gongchang.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">订单管理</a>
                </li>
                <li>
                    <a href=""><strong>待审核订单</strong></a>
                </li>
            </ol>
        </div>
        <div class="row wrapper white-bg">
            <div class="wrapper-content">
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-lg-1 control-label">收货人:</label>
                        <div class="col-lg-2"><input type="text" placeholder="" class="form-control" value=""
                                                     id="filter_customer"></div>
                        <label class="col-lg-1 control-label">电话:</label>
                        <div class="col-lg-2"><input type="text" placeholder="" class="form-control" value=""
                                                     id="filter_phone"></div>
                        <label class="col-lg-1 control-label">奶站:</label>
                        <div class="col-lg-2">
                            <select id="filter_station" class="chosen-select form-control" style="height:35px;"
                                    tabindex="2">
                                <option value="none"></option>
                                @if (isset($factory) and ($factory->deliveryStations))
                                    @foreach($factory->deliveryStations as $station)
                                        <option value="{{$station->name}}">{{$station->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <label class="col-lg-1 control-label">订单性质:</label>
                        <div class="col-lg-2">
                            <select id="filter_delivery_property" class="chosen-select form-control"
                                    style="height:35px;" tabindex="2">
                                <option value="none"></option>
                                @if (isset($order_properties))
                                    @foreach($order_properties as $orderproperty)
                                        <option value="{{$orderproperty->name}}">{{$orderproperty->name}}</option>
                                    @endforeach
                                @else
                                    <option value="1">新单</option>
                                    <option value="2">续单</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="vertical-align">
                        <label class="col-lg-1 control-label">订单编号:</label>
                        <div class="col-lg-2"><input type="text" placeholder="" class="form-control" value=""
                                                     id="filter_number"></div>
                        <label class="col-lg-1 control-label">征订员:</label>
                        <div class="col-lg-2"><input id="filter_order_checker" type="text" placeholder=""
                                                     class="form-control" value=""></div>
                        <label class="col-lg-1 control-label">订单类型:</label>
                        <div class="col-lg-2">
                            <select data-placeholder="" id="filter_term_kind" class="chosen-select form-control"
                                    style="height:35px;" tabindex="2">
                                <option value="none"></option>
                                @if ( isset($factory) and ($factory->factory_order_types) )
                                    @foreach($factory->factory_order_types as $fot)
                                        <option value="{{$fot->order_type_name}}">{{$fot->order_type_name}}</option>
                                    @endforeach
                                @else
                                    <option value="月单">月单</option>
                                    <option value="季单">季单</option>
                                    <option value="半年单">半年单</option>
                                @endif
                            </select>
                        </div>
                        <label class="col-lg-1 control-label">支付:</label>
                        <div class="col-lg-2">
                            <select data-placeholder="" id="filter_payment_type" class="chosen-select form-control"
                                    style="height:35px;" tabindex="2">
                                <option value="none"></option>
                                @if ( isset($payment_types))
                                    @foreach($payment_types as $pt)
                                        <option value="{{$pt->name}}">{{$pt->name}}</option>
                                    @endforeach
                                @else
                                    <option value="微信">微信</option>
                                    <option value="现金">现金</option>
                                    <option value="奶卡">奶卡</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="feed-element form-group" id="data_range_select">
                        <label class="col-lg-1 control-label">下单日期:</label>
                        <div class="input-daterange input-group col-lg-3" id="datepicker">
                            <input type="text" id="filter_order_start_date" class="input-sm form-control" name="start"/>
                            <span class="input-group-addon">至</span>
                            <input type="text" id="filter_order_end_date" class="input-sm form-control" name="end"/>
                        </div>
                    </div>
                </div>
                <div class="feed-element">
                    <div class="col-md-2 col-md-offset-10">
                        <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
                    </div>
                </div>

                <div class="ibox float-e-margins white-bg">
                    <div class="ibox-content">
                        <table class="footable table table-bordered" id="order_table" data-page-size="10" data-limit-navigation="5">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">订单号</th>
                                <th data-sort-ignore="true">收货人</th>
                                <th data-sort-ignore="true">电话</th>
                                <th data-sort-ignore="true">地址</th>
                                <th data-sort-ignore="true">订单类型</th>
                                <th data-sort-ignore="true">订单金额</th>
                                <th data-sort-ignore="true">下单日期</th>
                                <th data-sort-ignore="true">到期日期</th>
                                <th data-sort-ignore="true">支付</th>
                                <th data-sort-ignore="true">订单性质</th>
                                <th data-sort-ignore="true">征订员</th>
                                <th data-sort-ignore="true">奶站</th>
                                <th data-sort-ignore="true">配送员</th>
                                <th class="o_receipt" data-sort-ignore="true">票据号</th>
                                <th data-sort-ignore="true">备注</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (isset($orders))
                                @for($i =0; $i<count($orders); $i++)
                                    <tr data-orderid="{{$orders[$i]->id}}" class="row-hover-light-blue">
                                        <td>{{$i+1}}</td>
                                        <td class="o_number">{{$orders[$i]->number}}</td>
                                        <td class="o_customer_name">{{$orders[$i]->customer_name}}</td>
                                        <td class="o_phone">{{$orders[$i]->phone}}</td>
                                        <td class="o_addr">{{$orders[$i]->addresses}}</td>
                                        <td class="o_type">{{$orders[$i]->all_order_types}}</td>
                                        <td class="o_total">{{$orders[$i]->total_amount}}</td>
                                        <td class="o_ordered">{{$orders[$i]->ordered_at}}</td>
                                        <td class="o_end">{{$orders[$i]->order_end_date}}</td>
                                        <td class="o_paytype">{{$orders[$i]->payment_type_name}}</td>
                                        <td class="o_property">{{$orders[$i]->order_property_name}}</td>
                                        <td class="o_checker">{{$orders[$i]->order_checker_name}}</td>
                                        <td class="o_station">{{$orders[$i]->delivery_station_name}}</td>
                                        <td class="milkman">
                                            @if($orders[$i]->milkman)
                                                {{$orders[$i]->milkman->name}} {{$orders[$i]->milkman->phone}}
                                            @endif
                                        </td>
                                        <td class="o_receipt">{{$orders[$i]->receipt_number}}</td>
                                        <td>{{$orders[$i]->comment}}</td>
                                    </tr>
                                @endfor
                            @endif
                            </tbody>
                            <tfoot align="right">
                            <tr>
                                <td colspan="16">
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
                               <th data-sort-ignore="true">序号</th>
                               <th data-sort-ignore="true">订单号</th>
                               <th data-sort-ignore="true">客户名</th>
                               <th data-sort-ignore="true">电话</th>
                               <th data-sort-ignore="true">地址</th>
                               <th data-sort-ignore="true">订单类型</th>
                               <th data-sort-ignore="true">订单金额</th>
                               <th data-sort-ignore="true">下单日期</th>
                               <th data-sort-ignore="true">到期日期</th>
                               <th data-sort-ignore="true">支付</th>
                               <th data-sort-ignore="true">订单性质</th>
                               <th data-sort-ignore="true">征订员</th>
                               <th data-sort-ignore="true">奶站</th>
                               <th data-sort-ignore="true">配送员</th>
                               <th data-sort-ignore="true">备注</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="16">
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
        var at_page = "daishenhe";
        //Filter Function
        $('button[data-action="show_selected"]').click(function () {

            var order_table = $('#order_table');
            var filter_table = $('#filter_table');
            var filter_table_tbody = $('#filter_table tbody');

            //get all selection
            var f_customer = $('#filter_customer').val().trim().toLowerCase();
            var f_phone = $('#filter_phone').val().trim().toLowerCase();
            var f_station = $('#filter_station').val().toLowerCase().trim();;
            var f_delivery_property = $('#filter_delivery_property').val();
            var f_number = $('#filter_number').val().toLowerCase().trim();
            var f_checker = $('#filter_order_checker').val().toLowerCase().trim();
            var f_term_kind = $('#filter_term_kind').val();
            var f_payment_type = $('#filter_payment_type').val();
            var f_order_start_date = $('#filter_order_start_date').val();
            var f_order_end_date = $('#filter_order_end_date').val();

            //show only rows in filtered table that contains the above field value
            var filter_rows = [];
            var i = 0;

            $('#order_table').find('tbody tr').each(function () {
                var tr = $(this);

                o_customer_name = tr.find('td.o_customer_name').html().toString().toLowerCase();
                o_phone = tr.find('td.o_phone').html().toString().toLowerCase();
                o_station = tr.find('td.o_station').html().toString().toLowerCase();
                o_property = tr.find('td.o_property').html().toString().toLowerCase();

                o_number = tr.find('td.o_number').html().toString().toLowerCase();
                o_checker = tr.find('td.o_checker').html().toString().toLowerCase();
                o_type = tr.find('td.o_type').html().toString().toLowerCase();
                o_paytype = tr.find('td.o_paytype').html().toString().toLowerCase();
                o_ordered = tr.find('td.o_ordered').html().toString().toLowerCase();

                //customer
                if ((f_customer != "" && o_customer_name.includes(f_customer)) || (f_customer == "")) {
                    tr.attr("data-show-1", "1");
                } else {
                    tr.attr("data-show-1", "0")
                }

                if ((f_phone != "" && o_phone.includes(f_phone)) || (f_phone == "")) {
                    tr.attr("data-show-2", "1");
                } else {
                    tr.attr("data-show-2", "0")
                }

                if ((f_station != "none" && o_station.includes(f_station)) || (f_station == "none")) {
                    tr.attr("data-show-3", "1");
                } else {
                    tr.attr("data-show-3", "0")
                }

                if ((f_delivery_property != "none" && o_property.includes(f_delivery_property)) || (f_delivery_property == "none")) {
                    tr.attr("data-show-4", "1");
                } else {
                    tr.attr("data-show-4", "0")
                }

                if ((f_number != "" && o_number.includes(f_number)) || (f_number == "")) {
                    tr.attr("data-show-5", "1");
                } else {
                    tr.attr("data-show-5", "0")
                }
                if ((f_checker != "" && o_checker.includes(f_checker)) || (f_checker == "")) {
                    tr.attr("data-show-6", "1");
                } else {
                    tr.attr("data-show-6", "0")
                }
                if ((f_term_kind != "none" && o_type.includes(f_term_kind)) || (f_term_kind == "none")) {
                    tr.attr("data-show-7", "1");
                } else {
                    tr.attr("data-show-7", "0")
                }
                if ((f_payment_type != "none" && o_paytype.includes(f_payment_type)) || (f_payment_type == "none")) {
                    tr.attr("data-show-8", "1");
                } else {
                    tr.attr("data-show-8", "0");
                }

                if ((f_order_start_date == "" && f_order_end_date == "") || (!o_ordered)) {
                    tr.attr("data-show-10", "1");
                } else if (f_order_start_date == "" && f_order_end_date != "") {

                    var f2 = new Date(f_order_end_date);
                    var oo = new Date(o_ordered);
                    if (oo <= f2) {
                        tr.attr("data-show-10", "1");
                    } else {
                        tr.attr("data-show-10", "0");
                    }

                } else if (f_order_start_date != "" && f_order_end_date == "") {

                    var f1 = new Date(f_order_start_date);
                    var oo = new Date(o_ordered);
                    if (oo >= f1) {
                        tr.attr("data-show-10", "1");
                    } else {
                        tr.attr("data-show-10", "0");
                    }
                } else {
                    //f_order_start_date, f_order_end_date, o_ordered
                    var f1 = new Date(f_order_start_date);
                    var f2 = new Date(f_order_end_date);
                    var oo = new Date(o_ordered);
                    if (f1 <= f2 && f1 <= oo && oo <= f2) {
                        tr.attr("data-show-10", "1");

                    } else if (f1 >= f2 && f1 >= oo && oo >= f2) {
                        tr.attr("data-show-10", "1");

                    } else {

                        tr.attr("data-show-10", "0");
                    }
                }

                if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1") && (tr.attr("data-show-3") == "1") && (tr.attr("data-show-4") == "1" ) && (tr.attr("data-show-5") == "1" ) && (tr.attr("data-show-6") == "1" ) && (tr.attr("data-show-7") == "1" ) && (tr.attr("data-show-8") == "1" ) && (tr.attr("data-show-10") == "1" )) {
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

    </script>
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/order_select_export_print.js')?>"></script>
@endsection