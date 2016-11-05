@extends('zongpingtai.layout.master')
@section('css')
    <style>
        label.content {
            text-align: center;
            width: 100%;
            color: red;
            font-size: 25px;
            padding: 10px;
        }

        label.title {
            text-align: center;
            width: 100%;
            color: black;
            padding: 10px;
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
                    <a href="{{ url('zongpingtai/caiwu/zhanghuguanli/zhanghuguanli') }}">财务管理</a>
                </li>
                <li>
                    <a href="{{ url('zongpingtai/caiwu/zhanghuguanli/zhanghuguanli') }}">账户管理</a>
                </li>
                <li class="active">
                    <a href="{{ url('zongpingtai/caiwu/zhanghuguanli/zhanghugaikuang')}}"><strong>账户概况</strong></a>
                </li>
            </ol>
        </div>

        <div class="row wrapper">
            <div class="wrapper-content">

                <div class="ibox-content">
                    @if(isset($factory_info))
                        <div class="feed-element">
                            <div class="col-md-1 col-md-2">
                                <img alt="img" class="img-responsive" src="{{asset($factory_info[0])}}"
                                     style="height:60px;">
                            </div>
                            <label class="col-md-1 col-md-2" style="padding-top: 20px;">{{$factory_info[1]}}</label>
                        </div>
                        <div class="feed-element">
                            <div class="col-md-3 col-md-offset-1 text-center">
                                <div>
                                    <label class="content">{{$factory_info[2]}}</label>
                                    <label class="title">昨日订单</label>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div>
                                    <label class="content">{{$factory_info[3]}}</label>
                                    <label class="title">昨交订单总额</label>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div>
                                    <label class="content">{{$factory_info[4]}}</label>
                                    <label class="title">可转出金额</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 gray-bg">
                            <label class="col-md-10" style="padding:5px;">今日订单汇总</label>
                            <a class="col-md-2" href="{{ url('zongpingtai/caiwu/zhanghu/zhanghujiru/'.$factory_id)}}"
                               style="padding:5px;">查看账户记录</a>
                        </div>
                    @endif

                    <div class="ibox float-e-margins">
                        <div class="ibox-content">
                            <table class="table footable table-bordered">
                                <thead>
                                <tr>
                                    <th data-sort-ignore="true">序号</th>
                                    <th data-sort-ignore="true">奶站名称</th>
                                    <th data-sort-ignore="true">订单笔数</th>
                                    <th data-sort-ignore="true">金额</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($stations))
                                    <?php $i = 0;?>
                                    @foreach($stations as $st)
                                        <tr>
                                            <td>{{$i+1}}</td>
                                            <td>{{$st->name}}</td>
                                            <td>{{$st->today_wechat_order_count}}</td>
                                            <td>{{$st->today_wechat_order_amount}}</td>
                                        </tr>
                                        <?php $i++;?>
                                    @endforeach
                                @endif
                                <tr>
                                    <td colspan="2">合计</td>
                                    <td>{{$today_total_wechat_count}}</td>
                                    <td>{{$today_total_wechat_amount}}</td>
                                </tr>
                                </tbody>
                                <tr>
                                    <td colspan="100%">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
