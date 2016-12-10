@extends('gongchang.layout.master')

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
                    <a href=""><strong>奶卡款转账</strong></a>
                </li>
            </ol>
        </div>

        <div class="row border-bottom">
            <div class="col-md-4">
                <div style="background-color:#5badd7;">
                    <label style="text-align:center; width:100%; color: black; padding:10px;">奶卡订单金额</label>
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

        <div class="row white-bg">
            <div class="ibox-content">
                <div class="col-md-7">
                </div>
                <div class="col-md-5">
                    <div class="col-md-6">
                        <a href="{{URL::to('/gongchang/caiwu/taizhang/naikazhuanzhangjilu')}}"
                           class="btn btn-outline btn-success" type="button" style="width:100%;">查看已转账记录</a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{URL::to('/gongchang/caiwu/taizhang/naikazhuanzhangzhangchan')}}"
                           class="btn btn-outline btn-success" type="button" style="width:100%;">查看未转账单</a>
                    </div>
                </div>
            </div>

            <div class="ibox-content">
                <div class="col-md-3">
                    <label class="col-md-4">奶站选择</label>
                    <div class="col-md-8">
                        <select data-placeholder="Choose..." class="chosen-select" id="filter_station"
                                style="width:100%; height: 30px;">
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

            <div class="ibox-content">
                <form action="naikakuanzhuanzhang/create_transaction" method="POST">
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

            <div class="ibox float-e-margins">
                <div class="ibox-content">

                    <table class="footable table table-bordered" id="order_table"   data-limit-navigation="5" data-page-size="10">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">序号</th>
                            <th data-sort-ignore="true">下单时间</th>
                            <th data-sort-ignore="true">用户</th>
                            <th data-sort-ignore="true">奶卡金额</th>
                            <th data-sort-ignore="true">奶卡卡号</th>
                            <th data-sort-ignore="true">收款方</th>
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
                                    <td>{{$card_orders_not_checked[$i]->number}}</td>
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
                            <td colspan="8">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                    <table class="footable table table-bordered" id="filter_table"  data-page-size="10"   data-limit-navigation="5" style="display:none;">
                        <thead>
                        <tr>
                            <th data-sort-ignore="true">序号</th>
                            <th data-sort-ignore="true">下单时间</th>
                            <th data-sort-ignore="true">用户</th>
                            <th data-sort-ignore="true">奶卡金额</th>
                            <th data-sort-ignore="true">奶卡卡号</th>
                            <th data-sort-ignore="true">收款方</th>
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
                        </tr>\
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script src="<?=asset('js/pages/gongchang/naikakuanzhuanzhang.js') ?>"></script>

@endsection