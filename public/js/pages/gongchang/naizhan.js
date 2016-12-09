/**
 * Created by Administrator on 16/12/9.
 */

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
            }
            else {
                $(district_list).empty();
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

    if (od != "none") {
        printContent('order_table', gnUserTypeFactory, '配送范围管理');
    }
    else if (fd != "none") {
        printContent('filter_table', gnUserTypeFactory, '配送范围管理');
    }
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

    window.location.href = url;

});
