@extends('gongchang.layout.master')
@section('content')
    @include('gongchang.theme.sidebar')
    <div id="page-wrapper" class="white-bg dashbard-1">
        @include('gongchang.theme.header')
        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">订单管理</a>
                </li>
                <li>
                    <a href={{URL::to('/gongchang/dingdan/xudanliebiao')}}><strong>暂停订单</strong></a>
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
                        <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选
                        </button>
                    </div>
                </div>

                <div class="ibox float-e-margins white-bg">
                    <div class="ibox-content">
                        <table id="order_table" class="footable table table-bordered" data-page-size="10" data-limit-navigation="5">
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
                                <th data-sort-ignore="true">支付</th>
                                <th data-sort-ignore="true">订单性质</th>
                                <th data-sort-ignore="true">征订员</th>
                                <th data-sort-ignore="true">奶站</th>
                                <th data-sort-ignore="true">配送员</th>
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
                                        <td class="o_paytype">{{$orders[$i]->payment_type_name}}</td>
                                        <td class="o_property">{{$orders[$i]->order_property_name}}</td>
                                        <td class="o_checker">{{$orders[$i]->order_checker_name}}</td>
                                        <td class="o_station">{{$orders[$i]->delivery_station_name}}</td>
                                        <td class="milkman">
                                            @if($orders[$i]->milkman)
                                                {{$orders[$i]->milkman->name}} {{$orders[$i]->milkman->phone}}
                                            @endif
                                        </td>
                                        <td></td>
                                    </tr>
                                @endfor
                            @endif
                            </tbody>
                            <tfoot align="right">
                            <tr>
                                <td colspan="100%">
                                    <ul class="pagination pull-right"></ul>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                        <table class="footable table table-bordered" id="filter_table" data-page-size="10" data-limit-navigation="5"
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
    <script>
        var at_page= "zanting";
    </script>
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/order_select_export_print.js')?>"></script>
@endsection