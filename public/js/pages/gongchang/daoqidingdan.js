
var city = '';
var district = '';

$(document).ready(function() {
    $('.footable').footable();

    if ($('.province_list').val() != "none")
        $('.province_list').trigger('change');
});

$(document).on('click','#search',function () {
    var station_name = $('#station_name').val();
    var station_number = $('#station_number').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    var province = $('#province option:selected').val();
    window.location.href = SITE_URL+"milk/public/gongchang/tongjifenxi/daoqidingdantongji/?station_name="+station_name+"&station_number="+station_number+"&start_date="+start_date+"&end_date="+end_date+"&province="+province+"&city="+city+"&district="+district+"";
});

$('.province_list').on('change', function () {

    var current_province = $(this).val();
    var city_list = $(this).parent().parent().find('.city_list');
    var district_list = $(this).parent().parent().find('.district_list');

    if (current_province == "") {
        district_list.empty();
        district_list.html('');
        city_list.empty();
        city_list.html('');
        return;
    }

    var dataString = {'province': current_province};

    $.ajax({
        type: "GET",
        url: API_URL + "province_to_city",
        data: dataString,
        success: function (data) {
            if (data.status == "success") {
                city_list.empty();

                var cities, city, citydata;

                cities = data.city;
                citydata = '<option value="" selected>全部</option>';
                city_list.append(citydata);
                for (var i = 0; i < cities.length; i++) {
                    city = cities[i];
                    if($('#input_city').val() == city.name){
                        citydata = '<option value="' + city.name + '" selected>' + city.name + '</option>';
                    }
                    else {
                        citydata = '<option value="' + city.name + '">' + city.name + '</option>';
                    }
                    city_list.append(citydata);
                }

                var current_city = city_list.val();

                city_list.trigger('change');
            }
        },
        error: function (data) {
            console.log(data);
        }
    })
});

$('.city_list').on('change', function () {

    var current_city = $(this).val();
    city = current_city;

    var current_province = $('.province_list').val();

    var district_list = $(this).parent().parent().find('.district_list');

    if (current_city == "") {
        district_list.empty();
        return;
    }

    var dataString = {'city': current_city, 'province': current_province};

    $.ajax({
        type: "GET",
        url: API_URL + "city_to_district",
        data: dataString,
        success: function (data) {
            if (data.status == "success") {
                district_list.empty();

                $('li.search-choice').remove();
                $('ul.chosen-results li').remove();

                var districts = data.district;

                var districtdata,district;

                districtdata = '<option value="" selected>全部</option>';
                district_list.append(districtdata);

                for (i = 0; i < districts.length; i++) {
                    var district = districts[i];
                    var districtdata;
                    if($('#input_district').val() == district.name){
                        districtdata = '<option value="' + district.name + '" selected>' + district.name + '</option>';
                    }
                    else {
                        districtdata = '<option value="' + district.name + '">' + district.name + '</option>';
                    }

                    district_list.append(districtdata);
                }
            }
        },
        error: function (data) {
            console.log(data);
        }
    })
});

$('.district_list').on('change', function () {

    var current_district = $(this).val();
    district = current_district;
});

$('.footable').footable();

$('#data_range_select .input-daterange').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true
});

$('button[data-action = "print"]').click(function () {
    printContent('table1', gnUserTypeFactory, '到期订单统计');
});