/**
 * Created by Administrator on 16/12/9.
 */

$(document).ready(function () {
    if ($('.province_list').val() != "none")
        $('.province_list').trigger('change');
});

$('#data_range_select .input-daterange').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true
});

$('#search').click(function () {
    var station_name = $('#station_name').val();
    var station_number = $('#station_number').val();
    var province =$('#province option:selected').val();
    if(province == 'none'){
        province = '';
    }
    var city=$('#city option:selected').val();
    if(city == 'none'){
        city = '';
    }
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();

    window.location.href = SITE_URL+"zongpingtai/tongji/kehudingdanxiugai?station_name="+station_name+"&station_number="+station_number+
        "&province="+province+"&city="+city+"&start_date="+start_date+"&end_date="+end_date+"";
});

$('button[data-action = "print"]').click(function () {
    printContent('table1', gnUserTypeAdmin, '客户订单修改统计');
});

$('.province_list').on('change', function () {

    var current_province = $(this).val();
    var city_list = $(this).parent().find('.city_list');
    if (current_province == "none" || current_province == null) {
        $(city_list).empty();
        $(city_list).append('<option value="none">全部</option>');
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

                var cities, city, citydata,inputdata;

                cities = data.city;

                city_list.append('<option value="none">全部</option>');

                for (var i = 0; i < cities.length; i++) {
                    var citydata = cities[i];
                    if($('#currrent_city').val() == citydata.name){
                        inputdata = '<option value="' + citydata.name + '" selected>' + citydata.name + '</option>';
                    }else {
                        inputdata = '<option value="' + citydata.name + '" >' + citydata.name + '</option>';
                    }

                }
                city_list.append(inputdata);
            }
        },
        error: function (data) {
            console.log(data);
        }
    })
});
