@extends('gongchang.layout.master')

@section('css')
    <style>
        label {
            text-align: right;
            padding-top: 5px;
        }

        .text-bold {
            font-weight: bold;
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
                    <a href="">基础信息管理</a>
                </li>
                <li>
                    <a href=""><strong>奶站管理</strong></a>
                </li>
            </ol>
        </div>

        <div class="row white-bg">
            <div class="ibox-content">
                <div class="feed-element">

                    <label class="col-md-1">奶站名称:</label>
                    <div class="col-md-1">
                        <input class="form-control" type="text" id="filter_station_name" value="">
                    </div>

                    <label class="col-md-1">编号:</label>
                    <div class="col-md-1">
                        <input class="col-md-1 form-control" type="text" id="filter_series_no" value="">
                    </div>

                    <label class="col-md-1" style="padding-top: 5px; padding-left: 20px;">范围:</label>
                    <div class="col-md-5">
                        <div class="col-md-4">
                            <select required id="filter_province" name="province"
                                    class="province_list form-control col-md-3" style="width: 150px;">
                                <option value="none">全部</option>
                                @if (isset($provinces))
                                    @foreach($provinces as $province)
                                        <option value="{{$province->name}}">{{$province->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select required id="filter_city" name="city" class="city_list col-md-2 form-control">
                                <option value="none">全部</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select required id="filter_district" name="district" class="district_list form-control">
                                <option value="none">全部</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <button type="button" class="btn btn-success btn-outline" data-action="show_selected">筛选
                        </button>
                        <button class="btn btn-success btn-outline" type="button" data-action="print">打印
                        </button>
                    </div>
                </div>

                <div class="ibox float-e-margins" style="padding-top: 10px;">
                    <div class="ibox-content">
                        <table id="order_table" class="footable table table-bordered table-hover" data-page-size="10">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">图片</th>
                                <th data-sort-ignore="true">编号</th>
                                <th data-sort-ignore="true">名称</th>
                                <th data-sort-ignore="true">配送范围</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $i = 0; ?>
                            @foreach($stations as $station)
                                <?php $i++; ?>
                                <tr data-url="{{ url('/gongchang/jichuxinxi/naizhan/peisongfanwei-chakanbianji/'.$station->id) }}" style="cursor:pointer">
                                    <td>{{$i}}</td>
                                    <td><img src="<?=asset('/img/station/logo/' . $station->image_url) ?>"
                                             class="img-responsive" width="100px;"/></td>
                                    <td class="station_series">{{$station->number}}</td>
                                    <td>
                                        <span class="station_name text-bold">{{$station->name}}</span><br>{{$station->address}}
                                    </td>
                                    <td class="address" data-province="{{$station->province_name}}" data-city="{{$station->city_name}}" data-district="{{$station->district_name}}">
                                        @if (isset($area_address[$station->id]))
                                        @foreach($area_address[$station->id] as $street_id=>$street)
                                            {{$street[0]}}:
                                            @foreach($street[1] as $xiaoqu_id => $xiaoqu_name)
                                                {{$xiaoqu_name}}
                                            @endforeach
                                            <br>
                                        @endforeach
                                        @endif
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
                                <th data-sort-ignore="true">序号</th>
                                <th data-sort-ignore="true">图片</th>
                                <th data-sort-ignore="true">编号</th>
                                <th data-sort-ignore="true">名称</th>
                                <th data-sort-ignore="true">配送范围</th>
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
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="<?=asset('js/pages/gongchang/naizhan.js')?>"></script>
@endsection