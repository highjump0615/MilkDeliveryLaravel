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
                                        @foreach($area_address[$station->id] as $street_id=>$street)
                                            {{$street[0]}}:
                                            @foreach($street[1] as $xiaoqu_id => $xiaoqu_name)
                                                {{$xiaoqu_name}}
                                            @endforeach
                                            <br>
                                        @endforeach
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
                                <th data-sort-ignore="true">负责人</th>
                                <th data-sort-ignore="true">电话</th>
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
    <script type="text/javascript">

        $(document).ready(function () {
            if ($('.province_list').val() != "none")
                $('.province_list').trigger('change');
        });

        $('.province_list').on('change', function () {

            var current_province = $(this).val();
            var city_list = $(this).parent().parent().find('.city_list');
            var district_list = $(this).parent().parent().find('.district_list');

            if (current_province == "none" || current_province == null) {
                $(city_list).empty();
                $(city_list).append('<option value="none">全部</option>');

                $(district_list).empty();
                $(district_list).append('<option value="none">全部</option>');
                return;
            }

            var dataString = {'province': current_province};

            $.ajax({
                type: "GET",
                url: API_URL + "active_province_to_city",
                data: dataString,
                success: function (data) {
                    if (data.status == "success") {
                        city_list.empty();

                        var cities, city, citydata;

                        cities = data.city;

                        city_list.append('<option value="none">全部</option>');

                        for (var i = 0; i < cities.length; i++) {
                            var city_name = cities[i];
                            citydata = '<option value="' + city_name + '">' + city_name + '</option>';
                        }
                        city_list.append(citydata);

                        $(district_list).empty();
                        $(district_list).append('<option value="none">全部</option>');
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            })
        });

        $('.city_list').on('change', function () {

            var current_city = $(this).val();
            var district_list = $(this).parent().parent().find('.district_list');

            var province = $('.province_list').val();

            if (current_city == "none" || current_city == null) {
                $(district_list).empty();
                $(district_list).append('<option value="none">全部</option>');
                return;
            }

            var dataString = {'city': current_city, 'province': province};

            $.ajax({
                type: "GET",
                url: API_URL + "active_city_to_district",
                data: dataString,
                success: function (data) {
                    if (data.status == "success") {
                        district_list.empty();

                        var districts = data.district;

                        district_list.append('<option value="none">全部</option>');

                        for (var i = 0; i < districts.length; i++) {
                            var district_name = districts[i];
                            var districtdata;
                            districtdata = '<option value="' + district_name + '">' + district_name + '</option>';
                            district_list.append(districtdata);
                        }

                    } else {
                        $(district_list).empty();
                        return;
                    }
                },
                error: function (data) {
                    console.log(data);
                }
            })
        });

        $('button[data-action = "print"]').click(function () {

            var od = $('#order_table').css('display');
            var fd = $('#filter_table').css('display');

            var sendData = [];

            var printContents;
            if (od != "none") {
                //print order data
                printContents = $("#order_table")[0].outerHTML;

            } else if (fd != "none") {
                //print filter data
                printContents = $("#filter_table")[0].outerHTML;
            } else {
                return;
            }

            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;

            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        });

        //Search Table
        $('button[data-action = "show_selected"]').click(function () {

            var order_table = $('#order_table');
            var filter_table = $('#filter_table');
            var filter_table_tbody = $('#filter_table tbody');

            var f_station_name = $('#filter_station_name').val();
            var f_series = $('#filter_series_no').val();
            var f_province = $('#filter_province').val();
            var f_city = $('#filter_city').val();
            var f_district = $('#filter_district').val();

            var filter_rows = [];
            var i = 0;

            $('table#order_table').find('tbody tr').each(function () {
                var tr = $(this);

                o_station_name = tr.find('td span.station_name').html().toString().toLowerCase();
                o_series = tr.find('td.station_series').html().toString().toLowerCase();

                o_province = tr.find('td.address').data('province');
                o_city = tr.find('td.address').data('city');
                o_district = tr.find('td.address').data('district');

                console.log(o_station_name);
                console.log(f_station_name);

                if ((f_station_name != "" && o_station_name.includes(f_station_name)) || (f_station_name == "")) {
                    tr.attr("data-show-1", "1");
                } else {
                    tr.attr("data-show-1", "0")
                }

                if ((f_series != "" && o_series.includes(f_series)) || (f_series == "")) {
                    tr.attr("data-show-2", "1");
                } else {
                    tr.attr("data-show-2", "0")
                }

                if ((f_province != "none" && o_province.includes(f_province)) || (f_province == "none")) {
                    tr.attr("data-show-3", "1");
                } else {
                    tr.attr("data-show-3", "0")
                }

                if ((f_city != "none" && o_city.includes(f_city)) || (f_city == "none")) {
                    tr.attr("data-show-4", "1");
                } else {
                    tr.attr("data-show-4", "0")
                }

                if ((f_district != "none" && o_district.includes(f_district)) || (f_district == "none")) {
                    tr.attr("data-show-5", "1");
                } else {
                    tr.attr("data-show-5", "0")
                }

                if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1") && (tr.attr("data-show-3") == "1") && (tr.attr("data-show-4") == "1" ) && (tr.attr("data-show-5") == "1" )) {
                    filter_rows[i] = $(tr)[0].outerHTML;
                    i++;
                } else {

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


        $('body').on('click', '#order_table tbody tr', function () {
            var url = $(this).data('url');

            window.location.replace(url);

        });
    </script>
@endsection