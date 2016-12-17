@extends('gongchang.layout.master')
@section('css')
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
                    @if ($is_station)
                    <a href={{URL::to('/naizhan/caiwu/taizhang')}}>奶站账户台账</a>
                    @else
                    <a href={{URL::to('/gongchang/caiwu/taizhang')}}>奶站账户台账</a>
                    @endif
                </li>
                <li>
                    <a href=""><strong>奶站订单金额统计</strong></a>
                </li>
            </ol>
        </div>

        <div class="row white-bg">
            <div class="ibox float-e-margins">
                <div class="col-md-6"> 
                    <label style="padding:10px; font-size:15px; width:100%;">&emsp;奶站订单金额统计：{{$station->name}}</label>
                </div>

                @if ($is_station)
                <div class="col-md-6"> 
                    <div class="col-lg-6" style="margin-right: -5px"> 
                        <a class="btn btn-success" 
                           href="{{ url('/naizhan/caiwu/taizhang/qitanaizhanzhuanzhang/xianjinzhuanzhangjiru') }}" 
                           type="type" style="width:100%;">
                            查看其他奶站现金转账记录
                        </a> 
                    </div> 
                    <div class="col-md-6"> 
                        <a class="btn btn-success" 
                           href="{{ url('/naizhan/caiwu/taizhang/naikakuanzhuanzhang/dingdanjiru') }}" 
                           type="type" style="width:100%; ">
                            查看奶卡订单转账记录
                        </a> 
                    </div> 
                </div> 
                @endif

                <div class="ibox-content">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th rowspan="2">项目</th>
                            <th colspan="2">本站订单金额</th>
                            <th colspan="2">代理订单</th>
                            <th colspan="2">奶卡订单</th>
                            <th colspan="2">其他奶站订单转入</th>
                            <th colspan="2">合计</th>
                        </tr>
                        <tr>
                           <th data-sort-ignore="true">订单笔数</th>
                           <th data-sort-ignore="true">金额</th>
                           <th data-sort-ignore="true">订单笔数</th>
                           <th data-sort-ignore="true">金额</th>
                           <th data-sort-ignore="true">订单笔数</th>
                           <th data-sort-ignore="true">金额</th>
                           <th data-sort-ignore="true">订单笔数</th>
                           <th data-sort-ignore="true">金额</th>
                           <th data-sort-ignore="true">订单笔数</th>
                           <th data-sort-ignore="true">金额</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>本站录入金额订单</td>
                            <td>{{$money_orders_count}}</td>
                            <td>{{$money_orders_sum}}</td>
                            <td>{{$wechat_orders_count}}</td>
                            <td>{{$wechat_orders_sum}}</td>
                            <td>{{$card_orders_count}}</td>
                            <td>{{$card_orders_sum}}</td>
                            <td>{{$other_orders_count}}</td>
                            <td>{{$other_orders_sum}}</td>
                            <td>{{$money_orders_count+$wechat_orders_count+$card_orders_count+$other_orders_count}}</td>
                            <td>{{$money_orders_sum+$wechat_orders_sum+$card_orders_sum+$other_orders_sum}}</td>
                        </tr>
                        <tr>
                            <td>本站实收金额</td>
                            <td></td>
                            <td>{{$money_orders_really_got_sum}}</td>
                            <td></td>
                            <td>{{$wechat_orders_really_got_sum}}</td>
                            <td></td>
                            <td>{{$card_orders_really_got_sum}}</td>
                            <td></td>
                            <td>{{$other_orders_really_got_sum}}</td>
                            <td></td>
                            <td>{{$money_orders_really_got_sum + $wechat_orders_really_got_sum + $card_orders_really_got_sum + $other_orders_really_got_sum}}</td>
                        </tr>
                        <tr>
                            <td>本站应收订单款余额</td>
                            <td></td>
                            <td>{{$receivable_order_money}}</td>
                            <td></td>
                            <td>{{$wechat_orders_sum - $wechat_orders_really_got_sum}}</td>
                            <td></td>
                            <td>{{$card_orders_sum - $card_orders_really_got_sum}}</td>
                            <td></td>
                            <td>{{$other_orders_sum - $other_orders_really_got_sum}}</td>
                            <td></td>
                            <td>{{$receivable_order_money + $wechat_orders_sum - $wechat_orders_really_got_sum+$card_orders_sum - $card_orders_really_got_sum+ $other_orders_sum - $other_orders_really_got_sum}}</td>
                        </tr>
                        <tr>
                            <td>转出其他奶站订单余额</td>
                            <td>{{$money_orders_of_others_count}}</td>
                            <td>{{$money_orders_of_others_sum}}</td>
                            <td>--</td>
                            <td>--</td>
                            <td>--</td>
                            <td>--</td>
                            <td>--</td>
                            <td>--</td>
                            <td>{{$money_orders_of_others_count}}</td>
                            <td>{{$money_orders_of_others_sum}}</td>
                        </tr>
                        <tr>
                            <td>本站配送订单</td>
                            <td>{{$money_orders_of_mine_count}}</td>
                            <td>{{$money_orders_of_mine_sum}}</td>
                            <td>{{$wechat_orders_count}}</td>
                            <td>{{$wechat_orders_sum}}</td>
                            <td>{{$card_orders_count}}</td>
                            <td>{{$card_orders_sum}}</td>
                            <td>{{$other_orders_count}}</td>
                            <td>{{$other_orders_sum}}</td>
                            <td>{{$money_orders_of_mine_count + $wechat_orders_count + $card_orders_count + $other_orders_count}}</td>
                            <td>{{$money_orders_of_mine_sum + $wechat_orders_sum + $card_orders_sum + $other_orders_sum}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="feed-element">
                <div class="col-md-3 col-md-offset-1">
                    <label class="col-md-4">摘要:</label>
                    <div class=" col-md-8">
                        <select id="filter_io" data-placeholder="Choose..." class="chosen-select"
                                style="width:100%; height:30px;">
                            <option value="none"></option>
                            <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_TYPE_IN}}">收款</option>
                            <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_IN_ORDER_OUT_OTHER}}">转出</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 text-right">
                    <label class="col-md-3" style="padding-top:5px;">项目:</label>
                    <div class=" col-md-9">
                        <select id="filter_kind" data-placeholder="Choose..." class="chosen-select"
                                style="width:100%; height:30px;">
                            <option value="none"></option>
                            <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_IN_MONEY_STATION}}">
                                本站实收金额
                            </option>
                            <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_IN_ORDER_WECHAT}}">
                                代理商转账
                            </option>
                            <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_IN_ORDER_CARD}}">奶卡转账
                            </option>
                            <option value="{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_IN_ORDER_OTHER_STATION}}">
                                其他奶站订单转入
                            </option>
                        <!--option value="{{\App\Model\FinanceModel\DSDeliveryCreditBalanceHistory::DSDCBH_TYPE_OUT_OTHER_STATION}}">转出其他奶站订单款</option-->
                        </select>
                    </div>
                </div>
                <div class="form-group col-md-4" id="data_range_select">
                    <label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
                    <div class="input-daterange input-group col-md-8" id="datepicker">
                        <input id="filter_start_date" type="text" class="input-sm form-control"/>
                        <span class="input-group-addon">至</span>
                        <input id="filter_end_date" type="text" class="input-sm form-control"/>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" data-action="show_selected" class="btn btn-success btn-md">筛选</button>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <!--TODO : show transfers-->
                    <table id="order_table" class="footable table table-bordered" data-page-size="10"  data-limit-navigation="5">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">摘要</th>
                            <th data-sort-ignore="true">转入时间</th>
                            <th data-sort-ignore="true">项目</th>
                            <th data-sort-ignore="true">金额</th>
                            <th data-sort-ignore="true">流水号</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($calc_histories))
                            @for($i = 0; $i<count($calc_histories); $i++)
                                <tr>
                                    <td class="o_io"
                                        data-io="{{$calc_histories[$i]->io_type}}">{{$calc_histories[$i]->io_name}}</td>
                                    <td class="o_date">{{$calc_histories[$i]->created_at}}</td>
                                    <td class="o_kind"
                                        data-kind="{{$calc_histories[$i]->type}}">{{$calc_histories[$i]->type_name}}</td>
                                    <td>{{$calc_histories[$i]->amount}}</td>
                                    <td>{{$calc_histories[$i]->receipt_number}}</td>
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

                    <table id="filter_table" class="footable table table-bordered" data-page-size="10"  data-limit-navigation="5"
                           style="display:none;">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">摘要</th>
                            <th data-sort-ignore="true">转入时间</th>
                            <th data-sort-ignore="true">项目</th>
                            <th data-sort-ignore="true">金额</th>
                            <th data-sort-ignore="true">流水号</th>
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

        </div>
    </div>
    </div>
@endsection

@section('script')

    <script type="text/javascript">
        $(document).ready(function () {
            $('#data_range_select .input-daterange').datepicker({
                keyboardNavigation: false,
                forceParse: false,
                autoclose: true,
                calendarWeeks: false,
                clearBtn: true,
            });
        });

        $('button[data-action="show_selected"]').click(function () {

            var order_table = $('#order_table');
            var filter_table = $('#filter_table');
            var filter_table_tbody = $('#filter_table tbody');

            //get all selection
            var f_io = $('#filter_io').val();
            var f_kind = $('#filter_kind').val();

            var f_start_date = $('#filter_start_date').val() + ' 00:00';
            var f_end_date = $('#filter_end_date').val() + ' 24:00';

            //show only rows in filtered table that contains the above field value
            var filter_rows = [];
            var i = 0;

            $('#order_table').find('tbody tr').each(function () {
                var tr = $(this);

                o_io = tr.find('td.o_io').data('io');
                o_kind = tr.find('td.o_kind').data('kind');
                o_date = tr.find('td.o_date').html();


                if (f_io == "none" && f_kind == "none") {
                    tr.attr("data-show-1", "1");
                    tr.attr("data-show-2", "1");
                } else if (f_io == "none" && f_kind != "none") {
                    tr.attr("data-show-1", "1");
                    if (o_kind == f_kind)
                        tr.attr("data-show-2", "1");
                    else
                        tr.attr("data-show-2", "0");
                } else if (f_io == "{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_TYPE_IN}}") {
                    // 只显示收款记录
                    if (o_kind < "{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_IN_ORDER_OUT_OTHER}}") {
                        tr.attr("data-show-1", "1");
                    }

                    if (f_kind == "none") {
                        tr.attr("data-show-2", "1");
                    } else {

                        if (o_kind == f_kind)
                            tr.attr("data-show-2", "1");
                        else
                            tr.attr("data-show-2", "0");

                    }
                } else if (f_io == "{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_IN_ORDER_OUT_OTHER}}") {
                    if (f_io == o_kind) {
                        tr.attr("data-show-1", "1");
                        tr.attr("data-show-2", "1");
                    } else {
                        tr.attr("data-show-1", "0");
                        tr.attr("data-show-2", "0");
                    }
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

                if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1") && (tr.attr("data-show-3") == "1")) {
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

        //filter_io 's out => disable item filter_type
        $('#filter_io').change(function () {
            if ($(this).val() == "{{\App\Model\FinanceModel\DSCalcBalanceHistory::DSCBH_IN_ORDER_OUT_OTHER}}") {
                $('#filter_kind').find('option:eq(0)').prop('selected', true);
                $('#filter_kind').prop("disabled", true);
            }
            else
                $('#filter_kind').prop("disabled", false);
        });
    </script>
@endsection