@extends('gongchang.layout.master')
@section('css')
    <style>
        .business_header label {
            width: 100%;
            text-align: center;
        }

        .business_header label.title {
            background-color: #5ce1df;
            color: #333333;
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

        <div class="row border-bottom">
            <ol class="breadcrumb gray-bg" style="padding:5px 0 5px 50px;">
                <li>
                    <a href="">财务管理</a>
                </li>
                <li>
                    <a href=""><strong>自营账户</strong></a>
                </li>
            </ol>
        </div>

        <!-- 面包屑导航 -->
        <div class="row white-bg">
            <!--Table-->
            <div class="ibox-content white-bg business_header">
                <div class="col-md-6 col-md-offset-2">
                    <div class="col-md-4">
                        <label class="title">期初余额</label>
                    </div>
                    <div class="col-md-8">
                        <label class="gray-bg">{{round($station->business_term_start_amount, 2)}}</label>
                    </div>
                    <div class="col-md-4">
                        <label class="title">本期增加</label>
                    </div>
                    <div class="col-md-8">
                        <label class="gray-bg">{{$station->business_in}}</label>
                    </div>
                    <div class="col-md-4">
                        <label class="title">本期减少</label>
                    </div>
                    <div class="col-md-8">
                        <label class="gray-bg">{{$station->business_out}}</label>
                    </div>
                    <div class="col-md-4">
                        <label class="title">期末余额</label>
                    </div>
                    <div class="col-md-8">
                        <label class="gray-bg">{{$station->business_credit_balance}}</label>
                    </div>
                    <div class="col-md-4">
                        <label class="title">信用余额</label>
                    </div>
                    <div class="col-md-8">
                        <label class="gray-bg">{{$station->init_business_credit_amount+$station->business_credit_balance}}</label>
                    </div>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-8"></div>

                @if (!$is_station)
                <div class="col-md-4">
                    <button class="btn btn-md btn-success" data-toggle="modal" href="#self_business_modal" type="button"
                            style="position: absolute; bottom:5px;"><i class="fa fa-plus"></i> 添加收款记录
                    </button>
                </div>
                @endif

            </div>



            <div id="self_business_modal" class="modal fade" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color:#5badd7; color: #fff;">
                            <h4 class="modal-title">收款记录</h4>
                        </div>
                        <form role="form" class="form-horizontal" id="self_business_history_form">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12"><h3 class="m-t-none m-b"></h3>
                                        <input type="hidden" name="io_type" value="{{\App\Model\FinanceModel\DSBusinessCreditBalanceHistory::DSBCBH_IN}}">
                                        <input type="hidden" name="type" value="{{\App\Model\FinanceModel\DSBusinessCreditBalanceHistory::DSBCBH_OUT_STATION_RETAIL_BUSINESS}}">
                                        {{--<div class="feed-element">--}}
                                            {{--<label class="col-md-3">项目:</label>--}}
                                            {{--<div class="col-md-9">--}}
                                                {{--<select required data-placeholder="Choose..." class="chosen-select"--}}
                                                        {{--name="type"--}}
                                                        {{--style="width: 100%; height:35px;">--}}
                                                    {{--<option value="{{\App\Model\FinanceModel\DSBusinessCreditBalanceHistory::DSBCBH_OUT_STATION_RETAIL_BUSINESS}}">--}}
                                                        {{--站内零售业务--}}
                                                    {{--</option>--}}
                                                    {{--<option value="{{\App\Model\FinanceModel\DSBusinessCreditBalanceHistory::DSBCBH_OUT_GROUP_BUY_BUSINESS}}">--}}
                                                        {{--团购业务--}}
                                                    {{--</option>--}}
                                                    {{--<option value="{{\App\Model\FinanceModel\DSBusinessCreditBalanceHistory::DSBCBH_OUT_CHANNEL_SALES_OPERATIONS}}">--}}
                                                        {{--渠道销售业务--}}
                                                    {{--</option>--}}
                                                    {{--<option value="{{\App\Model\FinanceModel\DSBusinessCreditBalanceHistory::DSBCBH_OUT_TRY_TO_DRINK_OR_GIFT}}">--}}
                                                        {{--试饮或赠品--}}
                                                    {{--</option>--}}
                                                {{--</select>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        <div class="feed-element">
                                            <label class="col-md-3">金额:</label>
                                            <div class="col-md-9">
                                                <input type="number" step="0.01" required name="amount" placeholder=""
                                                       class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="feed-element">
                                            <label class="col-md-3">流水号:</label>
                                            <div class="col-md-9">
                                                <input type="" required name="receipt_number" placeholder=""
                                                       class="form-control" id="insert_receipt_number">
                                            </div>
                                        </div>
                                        <div class="feed-element">
                                            <label class="col-md-3">备注:</label>
                                            <div class="col-md-9">
                                                <input type="text" name="comment" placeholder=""
                                                       class="form-control">
                                            </div>
                                        </div>
                                        <input type="hidden" value="{{$station->id}}" name="station_id">

                                    </div>
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
        <div class="feed-element">
            <div class="col-md-5"></div>
            <div class="form-group col-md-5" id="data_range_select">
                <label class="col-sm-2 control-label" style="padding-top:5px;">日期:</label>
                <div class="input-daterange input-group col-md-8" id="datepicker">
                    <input type="text" class="input-sm form-control" id="filter_start_date" name="start"/>
                    <span class="input-group-addon">至</span>
                    <input type="text" class="input-sm form-control" id="filter_end_date" name="end"/>
                </div>
            </div>
            <div class="col-md-2" style="padding-top:5px;">
                <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
                <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
            </div>
        </div>

        <div class="ibox float-e-margins">
            <div class="ibox-content">

                <table class="footable table table-bordered" id="order_table" data-page-size="10">
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
                    @if(isset($self_business_history))
                        @foreach($self_business_history as $sbh)
                            <tr>
                                <td>{{$sbh->io_name}}</td>
                                <td class="o_date">{{$sbh->created_at}}</td>
                                <td>{{$sbh->type_name}}</td>
                                <td>{{$sbh->amount}}</td>
                                <td>{{$sbh->receipt_number}}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="6">
                            <ul class="pagination pull-right"></ul>
                        </td>
                    </tr>
                    </tfoot>
                </table>

                <table class="footable table table-bordered" id="filter_table" data-page-size="10"
                       style="display:none;">
                    <thead>
                    <tr>
                        <th data-sort-ignore="true">摘要</th>
                        <th data-sort-ignore="true">时间</th>
                        <th data-sort-ignore="true">项目</th>
                        <th data-sort-ignore="true">金额</th>
                        <th data-sort-ignore="true">流水号</th>
                        <th data-sort-ignore="true">最新余额</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="6">
                            <ul class="pagination pull-right"></ul>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="application/javascript">
        var gnUserType = gnUserTypeFactory;
        @if ($is_station)
            gnUserType = gnUserTypeStation;
        @endif
    </script>
    <script src="<?=asset('js/pages/gongchang/ziyingzhanghu.js') ?>"></script>
@endsection