//First set the Address list
$('.province_list').on('change', function () {

    var current_province = $(this).val();

    var city_list = $(this).parent().parent().find('.city_list');
    city_list.empty();

    if (!current_province) {
        return;
    }

    var dataString = {
        'province': current_province
    };

    $.ajax({
        type: "GET",
        url: API_URL + "active_province_to_city",
        data: dataString,
        success: function (data) {
            if (data.status == "success") {

                var cities, city, citydata;
                cities = data.city;

                for (var i = 0; i < cities.length; i++) {
                    city = cities[i];

                    if (customer_city && customer_city == city) {
                        citydata = '<option value="' + city + '" selected>' + city + '</option>';
                    } else {
                        citydata = '<option value="' + city + '">' + city + '</option>';
                    }
                    city_list.append(citydata);
                }

                var current_city = city_list.val();

                if (current_city)
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

    var district_list = $(this).parent().parent().find('.district_list');
    $(district_list).empty();

    if (!current_city) {
        return;
    }

    var province = $('.province_list').val();

    var dataString = {
        'province': province,
        'city': current_city,
    };

    $.ajax({
        type: "GET",
        url: API_URL + "active_city_to_district",
        data: dataString,
        success: function (data) {
            if (data.status == "success") {
                var districts = data.district;

                for (i = 0; i < districts.length; i++) {
                    var district = districts[i];
                    var districtdata;

                    if (customer_district && customer_district == district) {
                        districtdata = '<option value="' + district + '" selected>' + district + '</option>';
                    } else {
                        districtdata = '<option value="' + district + '">' + district + '</option>';
                    }

                    district_list.append(districtdata);
                }

                var current_district = district_list.val();
                if (current_district)
                    district_list.trigger('change');

            } else {
                show_info_msg('没有区');
            }
        },
        error: function (data) {
            console.log(data);
        }
    })
});

$('.district_list').on('change', function () {

    var current_district = $(this).val();

    var street_list = $(this).parent().parent().find('.street_list');
    street_list.empty();

    if (!current_district) {
        return;
    }

    var province = $('.province_list').val();
    var city = $('.city_list').val();

    var dataString = {
        'province':province,
        'city':city,
        'district':current_district,
    };

    $.ajax({
        type: "GET",
        url: API_URL + "active_district_to_street",
        data: dataString,
        success: function (data) {
            if (data.status == "success") {

                var streets = data.streets;
                var streetdata;

                for (var i = 0; i < streets.length; i++) {
                    var street = streets[i];

                    if(street[1] == customer_street)
                        streetdata = '<option selected data-street-id="'+street[0]+'" value="' + street[1] + '">' + street[1] + '</option>';
                    else
                        streetdata = '<option data-street-id="'+street[0]+'" value="' + street[1] + '">' + street[1] + '</option>';

                    street_list.append(streetdata);
                }
                var current_street = street_list.val();
                if (current_street)
                    street_list.trigger('change');

            } else {
                show_warning_msg("没有关于小区街道");
            }
        },
        error: function (data) {
            console.log(data);
        }
    })

});

$('.street_list').on("change", function () {

    var current_street = $(this).val();

    var xiaoqu_list = $(this).parent().parent().find('.xiaoqu_list');
    $(xiaoqu_list).empty();

    if (!current_street) {
        return;
    }

    var street_id = $(this).find('option:selected').data('street-id');
    var dataString = {
        'street_id':street_id,
    }

    $.ajax({
        type: "GET",
        url: API_URL + "active_street_to_xiaoqu",
        data: dataString,
        success: function (data) {
            var xiaoqus = data.xiaoqus;
            var xiaodata;

            for (var i = 0; i < xiaoqus.length; i++) {
                var xiaoqu = xiaoqus[i];

                if(customer_xiaoqu && customer_xiaoqu == xiaoqu[1])
                    xiaodata = '<option selected data-xiaoqu-id="'+xiaoqu[0]+'" value = "' + xiaoqu[1] + '">' + xiaoqu[1] + '</option>';
                else
                    xiaodata = '<option data-xiaoqu-id="'+xiaoqu[0]+'" value = "' + xiaoqu[1] + '">' + xiaoqu[1] + '</option>';

                xiaoqu_list.append(xiaodata);
            }

            //Reset the Station Info
            // $('#station_list').empty();
        },
        error: function (data) {
            console.log(data);
        }
    });
});


//Insert Customer
$('#customer_form').on("submit", function (e) {

    e.preventDefault();

    customer_data = $('#customer_form').serializeArray();

    $.ajax({
        type: "POST",
        url: API_URL + "naizhan/dingdan/dingdanluru/insert_customer",
        data: customer_data,
        success: function (data) {
            console.log(data);
            if (data.status == 'success') {
                //set customer id
                customer_id = data.customer_id;
                $('#customer_id').val(customer_id);

                //set remaining amount
                var remain = data.remain_amount;
                $('#remaining').val(remain);

                //init station list
                $('#station_list').empty();
                var station_data = '<option data-milkman="' + data.milkman_id + '" value="' + data.station_id + '">' + data.station_name + '</option>';
                $('#station_list').append(station_data);

                //trigger to calculate the product price
                trigger_factory_order_type_change();

            } else {
                if (data.message) {
                    show_err_msg(data.message);
                }
                $('#station_list').empty();
                $('#station_list').val('').trigger('chosen:updated');
                
                if(data.station_id && data.station_name)
                {
                    var station_data = '<option value="' + data.station_id + '">' + data.station_name + '</option>';
                    $('#station_list').append(station_data);
                }

            }

        },
        error: function (data) {
            console.log(data);
        }
    });
});

//Insert Xudan Order
$('#order_xudan_form').on('submit', function (e) {

    e.preventDefault();

    var pass = true;
    //Check the product total amount
    $('.one_product .one_p_amount').each(function(){
        if(!$(this).val())
            pass = false;
    });

    if(!pass)
    {
        show_warning_msg("产品价格不计算")
        return;
    }

    $('#order_xudan_form button[type="submit"]').prop('disabled', true);

    var sendData = $('#order_xudan_form').serializeArray();

    if ($('[data-target="#card_info"]').prop("checked")) {
        //milk card check
        var card_id = $('#card_id').val();
        var card_code = $('#card_code').val();

        sendData.push({'name': 'card_id', 'value': card_id});
        sendData.push({'name': 'card_code', 'value': card_code});
    } else {
        sendData.push({'name': 'milk_card_check', 'value': 'off'});
    }

    if(!$('#milk_box_install').prop("checked")){
        sendData.push({'name': 'milk_box_install', 'value': 'off'});
    }

    //No need for Customer Info
    console.log(sendData);

    $.ajax({
        type: "POST",
        url: API_URL + "naizhan/dingdan/luruxudan/insert_xudan_order",
        data: sendData,
        success: function (data) {
            console.log(data);
            if (data.status == 'success') {
                var order_id = data.order_id;

                show_success_msg('续单录入成功');

                var url = SITE_URL + 'naizhan/dingdan/dingdanluru';
                window.location.replace(url);

            } else {
                if (data.message)
                    alert(data.message);
            }
        },
        error: function (data) {
            console.log(data);
        }
    });
});
