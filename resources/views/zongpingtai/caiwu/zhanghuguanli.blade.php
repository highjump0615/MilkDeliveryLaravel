@extends('zongpingtai.layout.master')
@section('css')
    <style>
        .filter_table{
            display:none;
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
                    财务管理
                </li>
                <li>
                    <a href="{{ url('zongpingtai/caiwu/zhanghuguanli')}}"><strong>账户管理</strong></a>
                </li>
            </ol>
        </div>

        <div class="row wrapper">
            <div class="wrapper-content">

                <div class="ibox-content">
                    <label class="col-md-1" style="padding-top:5px;">公司名称</label>
                    <div class="col-md-2">
                        <select id="filter_factory" class="chosen-select form-control">
                            <option value="none">全部</option>
                            @if(isset($fac_sta))
                                @foreach($fac_sta as $fs)
                                    <option value="{{$fs[0]}}">{{$fs[1]}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <label class="col-md-1" style="padding-top:5px;">奶站</label>
                    <div class="col-md-2">
                        <select id="filter_station" class="chosen-select form-control">
                            <option value="none">全部</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-md-offset-3">
                        <button class="btn btn-success btn-outline" type="button" data-action="show_selected">筛选</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="export_csv">导出</button>
                        <button class="btn btn-success btn-outline" type="button" data-action="print">打印</button>
                    </div>
                </div>

                @if(isset($fac_sta))
                    @foreach($fac_sta as $fs)
                        <div class="one_factory" data-fid="{{$fs[0]}}">
                            <div class="ibox-content">
                                <div class="feed-element">
                                    <div class="col-md-1 col-md-2">
                                        <img alt="img" class="img-responsive" src="{{asset($fs[2])}}"
                                             style="height:60px;">
                                    </div>
                                    <label class="col-md-1 col-md-2" style="padding-top: 20px;">{{$fs[1]}}</label>
                                    <div class="col-md-2" style="padding-top:15px;">
                                        <a type="button"
                                           href="{{ url('zongpingtai/caiwu/zhanghu/zhanghugaikuang/'.$fs[0])}}"
                                           class="btn btn-success btn-outline" style="width:100%;">查看账户概况</a>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox float-e-margins">
                                <div class="ibox-content">
                                    <table id="table_data" class="table footable table-bordered" data-page-size="10">
                                        <thead>
                                        <tr>
                                           <th data-sort-ignore="true">序号</th>
                                           <th data-sort-ignore="true">账户名称</th>
                                           <th data-sort-ignore="true">开户行</th>
                                           <th data-sort-ignore="true">开户人</th>
                                           <th data-sort-ignore="true">卡号</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 0;?>
                                        @if(isset($fs[3]))
                                            @foreach($fs[3] as $station)
                                                <tr data-sid="{{$station[0]}}">
                                                    <td>{{$i+1}}</td>
                                                    <td>{{$station[1]}}</td>
                                                    <td>交行</td>
                                                    <td>{{$station[2]}}</td>
                                                    <td>{{$station[3]}}</td>
                                                </tr>
                                                <?php $i++;?>
                                            @endforeach
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
                                    <table id="table_filter" class="table footable table-bordered filter_table" data-page-size="10">
                                        <thead>
                                        <tr>
                                           <th data-sort-ignore="true">序号</th>
                                           <th data-sort-ignore="true">账户名称</th>
                                           <th data-sort-ignore="true">开户行</th>
                                           <th data-sort-ignore="true">开户人</th>
                                           <th data-sort-ignore="true">卡号</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 0;?>
                                        @if(isset($fs[3]))
                                            @foreach($fs[3] as $station)
                                                <tr data-sid="{{$station[0]}}">
                                                    <td>{{$i+1}}</td>
                                                    <td>{{$station[1]}}</td>
                                                    <td>交行</td>
                                                    <td>{{$station[2]}}</td>
                                                    <td>{{$station[3]}}</td>
                                                </tr>
                                                <?php $i++;?>
                                            @endforeach
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
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script type="text/javascript" src="<?=asset('js/pages/zongpingtai/zhanghuguanli.js') ?>"></script>

@endsection
