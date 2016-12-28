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
                                        class="province_list form-control col-md-3">
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
                                <td><input type="checkbox" class="js-switch"
                                           @if($station->getUser()->status == \App\Model\UserModel\User::USER_STATUS_ACTIVE)checked @endif />
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

    <script src="<?=asset('js/pages/gongchang/station_list.js') ?>"></script>

@endsection