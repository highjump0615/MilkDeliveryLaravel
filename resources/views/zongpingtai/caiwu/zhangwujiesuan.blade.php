@extends('zongpingtai.layout.master')
@section('css')
    <style>
        select {
            width: 100%;
            height: 30px;
        }

        label {
            padding-top: 5px;
        }

        button#create_transaction {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 50px;
        }

        form#create_transaction_form {
            padding: 0;
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
                    <a href="{{ url('zongpingtai/caiwu/zhangwujiesuan/zhangwujiesuan') }}">财务管理</a>
                </li>
                <li>
                    <a href="{{ url('zongpingtai/caiwu/zhangwujiesuan/zhangwujiesuan') }}"><strong>账务结算</strong></a>
                </li>
            </ol>
        </div>

        <div class="row wrapper">
            <div class="wrapper-content">

                <div class="ibox-content">
                    <label class="col-md-1 text-right">公司</label>
                    <div class="col-md-2">
                        <select id="filter_factory" class="chosen-select form-control">
                            @if(isset($factories))
                                @foreach($factories as $factory)
                                    <option value="{{$factory->id}}">{{$factory->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
<!--                    <label class="col-md-1 text-right">奶站</label>
                    <div class="col-md-2">
                        <select id="filter_station" class="chosen-select form-control">
                            <option value="none">全部</option>
                        </select>
                    </div>
-->
                    <label class="col-md-offset-3 col-md-1 control-label text-right" style="padding-top:5px;">日期:</label>
                    <div class="col-md-3 data_range_select">
                        <div class="input-daterange input-group">
                            <input id="filter_start_date" type="text" class="input-md form-control" name="start"/>
                            <span class="input-group-addon">至</span>
                            <input id="filter_end_date" type="text" class="input-md form-control" name="end"/>
                        </div>
                    </div>

                    <div class="col-md-2" style="padding-top:5px;">
                        <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
                    </div>
                </div>

                <div class="ibox-content">
                    <form class="col-md-7" method="post" id="create_transaction_form" action="create_transaction">
                        <input type="hidden" id="factory_id" name="factory_id" value="none">
                        <input type="hidden" id="station_id" name="station_id" value="none">
                        <div class="col-md-8 form-group data_range_select">
                            <label class="col-md-4 control-label" style="padding-top:5px;">账单日期:</label>
                            <div class="input-daterange input-group col-md-8">
                                <input type="text" class="input-md form-control" name="start"/>
                                <span class="input-group-addon">至</span>
                                <input type="text" class="input-md form-control" name="end"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-md btn-warning" id="create_transaction">生成账单</button>
                        </div>
                    </form>

                    <div class="col-md-4 col-md-offset-1">
                        <a type="button" class="btn btn-md btn-success" id="show_history"
                           href="{{ url('/zongpingtai/caiwu/zhangwujiesuan/lishizhuanzhangjiru/')}}">查看历史转账记录</a>
                        <a type="button" class="btn btn-md btn-success" id="show_todo"
                           href="{{ url('/zongpingtai/caiwu/zhangwujiesuan/zhangdanzhuanzhang/')}}">查看未转记录</a>
                    </div>

                </div>

                <div class="ibox float-e-margins">
                    <div class="ibox-content">

                        <table id="order_table" class="footable table table-bordered" data-page-size="10">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">时间</th>
                                <th data-sort-ignore="true">客户名</th>
                                <th data-sort-ignore="true">金额</th>
                                <th data-sort-ignore="true">订单号</th>
                                <th data-sort-ignore="true">收款方</th>
                                <th data-sort-ignore="true">状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($wechat_orders))
                                @for($i= 0; $i< count($wechat_orders); $i++)
                                    <tr>
                                        <td>{{$i+1}}</td>
                                        <td class="o_date">{{$wechat_orders[$i]->created_at}}</td>
                                        <td>{{$wechat_orders[$i]->customer_name}}</td>
                                        <td>{{$wechat_orders[$i]->total_amount}}</td>
                                        <td>{{$wechat_orders[$i]->number}}</td>
                                        <td class="o_factory_station" data-fid="{{$wechat_orders[$i]->factory_id}}"
                                            data-sid="{{$wechat_orders[$i]->delivery_station_id}}">{{$wechat_orders[$i]->delivery_station_name}}</td>
                                        @if($wechat_orders[$i]->transaction_id)
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
                                <td colspan="100%">
                                    <ul class="pagination pull-right"></ul>
                                </td>
                            </tr>
                            </tfoot>
                        </table>

                        <table id="filter_table" class="footable table table-bordered" data-page-size="10">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">时间</th>
                                <th data-sort-ignore="true">客户名</th>
                                <th data-sort-ignore="true">金额</th>
                                <th data-sort-ignore="true">订单号</th>
                                <th data-sort-ignore="true">收款方</th>
                                <th data-sort-ignore="true">状态</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
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

    <script type="text/javascript" src="<?=asset('js/pages/zongpingtai/zhangwujiesuan.js') ?>"></script>

@endsection