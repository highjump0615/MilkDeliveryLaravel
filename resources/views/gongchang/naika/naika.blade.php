@extends('gongchang.layout.master')
@section('css')
    <style>
        input[type=file] {
            width: 1px;
            /*visibility: hidden;*/
            display:none;
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
                    <a href="">奶卡管理</a>
                </li>
                <li class="active">
                    <strong>奶卡数据列表</strong>
                </li>
            </ol>
        </div>

        <div class="row border-bottom">
            <div class="col-md-4">
                <div style="background-color:#5badd7;">
                    <label style="text-align:center; width:100%; color: black; padding:10px;">奶卡数量</label>
                    <label style="text-align:center; width:100%; color: white; font-size: 25px; padding:10px;">{{$milkcard_count}}</label>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background-color:#5badd7;">
                    <label style="text-align:center; width:100%; color: black; padding:10px;">已领数量</label>
                    <label style="text-align:center; width:100%; color: white; font-size: 25px; padding:10px;">{{$milkcard_used}}</label>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background-color:#5badd7;">
                    <label style="text-align:center; width:100%; color: black; padding:10px;">未领数量</label>
                    <label style="text-align:center; width:100%; color: white; font-size: 25px; padding:10px;">{{$milkcard_count-$milkcard_used}}</label>
                </div>
            </div>
        </div>

        <div class="row">
            <!--Table-->
            <div class="ibox-content">
                <div class="feed-element">
                    <div class="col-md-3">
                        <label>卡号:</label>
                        <input type="text" id="card_number" value="">
                    </div>
                    <div class="col-md-3">
                        <label>面值:</label>
                        <input type="text" id="balance" value="">
                    </div>
                    <div class="col-md-3">
                        <label>领用方:</label>
                        <input type="text" id="recipient" value="">
                    </div>
                    <div class="col-md-1 col-md-offset-1">
                        <button type="button" id="search" class="btn btn-success btn-md text-right">筛选</button>
                    </div>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-content">

                    <div class="feed-element">
                        <div class="col-md-9">
                            <button data-toggle="modal" href="#modal-form" type="button" class="btn btn-success btn-md" id="but_sell">领用</button>
                        </div>

                        <div class="col-md-3">
                            <form id="upload-form" method="post" action="{{url('gongchang/naika/naika/import')}}" enctype="multipart/form-data">

                                <button type="button" class="btn btn-success btn-outline" id="csv_file_upload_btn">
                                    数据导入
                                </button>
                                <input type="file" name="csv_file" class="csv-upload"
                                       id="csv_file_upload_input" accept=".csv"/>
                                &nbsp;
                                <button class="btn btn-success btn-md" data-action = "print">打印</button>
                            </form>
                        </div>
                    </div>
                    @if(Session::has('card_order_status'))
                        <div id="card_number_alert" style="padding-top: 10px; padding-left: 20px;">
                            <label style="color: red;">所选卡号不存在！</label>
                        </div>
                    @endif

                    @if(Session::has('status'))
                        <div class="row">
                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-1">
                                    <div class="alert alert-success">
                                        {{ Session::get('status') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div></div>
                    <div id="modal-form" class="modal fade" aria-hidden="true">
                        <form id="confirm_card" action="{{ url('api/gongchang/naika/naika/register') }}" role="form" class="form-horizontal"
                              method="post">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <h3 class="m-t-none m-b"></h3>
                                                <div class="form-group">
                                                    <label class="col-sm-3 label-padding">领卡人</label>
                                                    <div class="col-sm-9">
                                                        <input type="text" id="name" name="user" placeholder=""
                                                                                 class="form-control" value="" onkeypress="hide_alert();">
                                                    </div>
                                                </div>
                                                <label id="name_alert" class="col-md-offset-3 col-md-9" style="color: red; display: none">(输入名称!)</label>
                                                <div class="form-group"><label class="col-sm-3 label-padding">卡 号</label>
                                                    <div class="col-sm-4">
                                                        <input id="start_number" name="start_num" class="form-control"
                                                               style="width: 100%;">
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <label class="control-label">到</label>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <input id="end_number" name="end_num" class="form-control"
                                                               style="width: 100%;">
                                                    </div>
                                                </div>
                                                <div class="form-group"><label class="col-sm-3 label-padding">数 量</label>
                                                    <div class="col-sm-3">
                                                        <input id="quantity" name="quantity" onkeypress="return isNumber(event)"
                                                               class="form-control" readonly>
                                                        <input type="hidden" id="max_quantity">
                                                    </div>
                                                    &nbsp;
                                                    <label class="col-sm-3 label-padding">金额:</label>
                                                    <label id="total_amount" class="col-sm-2 label-padding"></label>
                                                </div>
                                                <label id="quantity_alert" class="col-md-offset-3 col-md-9" style="color: red; display: none">(多数量!)</label>


                                                <div class="form-group"><label class="col-sm-3 label-padding">收款方式</label>
                                                    <div class="col-sm-9">
                                                        <div class="input-group">
                                                            <select name="payment_method" data-placeholder="Choose..."
                                                                    class="chosen-select"
                                                                    style="min-width:195px; height:35px;" tabindex="2">
                                                                <option value="1">现金</option>
                                                                <option value="2">借款</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button id="submit" type="submit" class="btn btn-white">确定</button>
                                        <button type="button" class="btn btn-white" data-dismiss="modal">取消</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <table id="cardTable" class="footable table table-bordered" data-page-size="15">
                        <thead>
                        <tr>
                            {{--<th data-sort-ignore="true">批次</th>--}}
                            <th data-sort-ignore="true">序号</th>
                            <th data-sort-ignore="true">卡号</th>
                            <th data-sort-ignore="true">面值</th>
                            <th data-sort-ignore="true">商品</th>
                            <th data-sort-ignore="true">验证码</th>
                            <th data-sort-ignore="true">激活状态</th>
                            <th data-sort-ignore="true">领用状态</th>
                            <th data-sort-ignore="true">领用方</th>
                            <th data-sort-ignore="true">领用时间</th>
                            <th data-sort-ignore="true">收款方式</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i = 0; $k = 0; ?>
                        @foreach($milkcards as $mc)
                            <?php $i++; $j = 0;?>
                            @foreach($mc as $m)
                                <?php $j++; $k++; ?>
                                <tr id="{{$m->batch_number}}">
                                    @if($j == 1)
                                        {{--<td rowspan="{{count($mc)}}">{{$m->batch_number}}</td>--}}
                                    @endif
                                    <td>{{$k}}</td>
                                    <td class="number">{{$m->number}}</td>
                                    <td class="balance">{{$m->balance}}</td>
                                    <td class="balance">{{$m->product}}</td>
                                    <td>{{$m->password}}</td>
                                    <td>
                                        @if($m->pay_status == 1)
                                            己激活
                                        @elseif($m->pay_status == 0)
                                            未激活
                                        @endif
                                    </td>
                                    <td>@if($m->sale_status == 1)
                                            已领
                                        @elseif($m->sale_status == 0)
                                            未领
                                        @endif</td>
                                    <td class="recipient">{{$m->recipient}}</td>
                                    <td>{{$m->sale_date}}</td>
                                    <td>
                                        @if($m->payment_method == 1)
                                            现金
                                        @elseif($m->payment_method == 2)
                                            借款
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="10">
                                <ul class="pagination pull-right"></ul>
                            </td>
                        </tr>
                        </tfoot>
                    </table>

                    <table id="filteredTable" class="footable table table-bordered" data-page-size="15"
                           style="display: none">
                        <thead>
                        <tr>
                            {{--<th data-sort-ignore="true">批次</th>--}}
                            <th data-sort-ignore="true">序号</th>
                            <th data-sort-ignore="true">卡号</th>
                            <th data-sort-ignore="true">面值</th>
                            <th data-sort-ignore="true">商品</th>
                            <th data-sort-ignore="true">验证码</th>
                            <th data-sort-ignore="true">激活状态</th>
                            <th data-sort-ignore="true">领用状态</th>
                            <th data-sort-ignore="true">领用方</th>
                            <th data-sort-ignore="true">领用时间</th>
                            <th data-sort-ignore="true">收款方式</th>
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
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/naika_admin.js')?>"></script>
@endsection