var delivery_area_data;
var station_province, station_city, station_district;

$(document).ready(function () {

    //Image File Upload
    $.simpleimgupload({
        input_field: ".img-upload",
        preview_box: ".img-preview",
        file_panel: ".file-panel",
        cancel_bt: ".file-panel span.cancel",
    });

    // 初始化配送范围街道
    show_chosen();

    //init province, city and district
    if ($('#select_province').val() != "none")
        $('#select_province').trigger('change');

});

//When Select Station Changed
$(document).on('change', '#select_province', function () {

    station_province = $(this).val();

    if (!station_province) {
        return;
    }

    var city_list = $('#select_city');

    $.ajax({
        type: "GET",
        url: API_URL + "active_province_to_city",
        data: {
            'province':station_province,
        },
        success: function (data) {
            if (data.status == "success") {
                city_list.empty();

                var cities, city, citydata;

                cities = data.city

                for (var i = 0; i < cities.length; i++) {

                    city = cities[i];

                    if (station_city && station_city == city) {
                        citydata = '<option value="' + city + '" selected>' + city + '</option>';
                    } else if( i == 0) {
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

//When Select City Changed
$(document).on('change', '#select_city', function () {

    station_city = $(this).val();
    if (!station_city) {
        return;
    }

    var province = $('#select_province').val();

    var district_list = $('#select_district');

    $.ajax({
        type: "GET",
        url: API_URL + "active_city_to_district",
        data: {
            'province': province,
            'city': station_city,
        },
        success: function (data) {
            if (data.status == "success") {
                district_list.empty();

                var districts = data.district;

                console.log(districts);

                for (var i = 0; i < districts.length; i++) {
                    var district = districts[i];
                    var districtdata;

                    if (station_district && station_district == district) {
                        districtdata = '<option value="' + district + '" selected>' + district + '</option>';
                    } else if( i== 0){
                        districtdata = '<option value="' + district + '" selected>' + district + '</option>';
                    }
                    else {
                        districtdata = '<option value="' + district + '">' + district + '</option>';
                    }

                    district_list.append(districtdata);

                }

                var current_district = district_list.val();

                if (current_district)
                {
                    district_list.trigger('change');
                }

            } else {
                return;
            }
        },
        error: function (data) {
            console.log(data);
        }
    })
});

//When Select District changed
$(document).on('change', '#select_district', function () {
    //Init the delivery area
    station_district = $(this).val();
    set_station_admin_name();
    init_delivery_area();
});

function set_station_admin_name(){

    if(station_city)
    {
        $.ajax({
            type: "POST",
            url: API_URL + "gongchang/xitong/naizhanzhanghao/tianjianaizhanzhanghu/get_station_admin_name",
            data: {
                'city_name': station_city,
            },
            success: function (data) {
                console.log(data);
                if(data.status == 'success')
                {
                    if(data.name)
                    {
                        $('#user_number').val(data.name);
                    }

                }
            },
            error: function () {
                console.log(data);
            },
        });
    }
}

//Init Delivery Area
function init_delivery_area(){

    var area = $('#delivery_area_one');

    $(area).find('.province_name').val(station_province);
    $(area).find('.city_name').val(station_city);
    $(area).find('.district_name').val(station_district);

    if (!station_district) {
        return;
    }
    var street_list = $('#area_street_list');

    //delete the xiaoqu data under the street
    var deliver_result_div = $('#delivery_result');

    deliver_result_div.html("");

    if (street_list) {

        street_list.empty();

        $.ajax({
            type: "GET",
            url: API_URL + "active_district_to_street",
            data: {
                'district':station_district,
                'province': station_province,
                'city':station_city,
            },
            success: function (data) {
                if (data.status == "success") {

                    var streets = data.streets;

                    for (var i = 0; i < streets.length; i++) {
                        var street = streets[i];

                        var streetdata = '<option data-id="' + street[0] + '" value="' + street[1] + '">' + street[1] + '</option>';
                        street_list.append(streetdata);
                    }
                }
                else {
                    // alert("没有关于小区街道");
                }

                show_chosen();
            },
            error: function (data) {
                console.log(data);

                // 查不到街道信息也要重新设置chosen
                show_chosen();
            }
        })
    }
}

//When the Street Changed
$(document).on('change', '#area_street_list', function () {

    var deliver_result_div = $('#delivery_result');
    var streets = $(this).find('option:selected');
    var length = streets.length;
    
    deliver_result_div.html("");

    // 如果没有选择街道，则不用显示小区信息
    if (length == 0) {
        return;
    }

    var wrapper_data = '<div class="col-md-8 col-md-offset-2">\
                        <div class="wrapper-content">\
                            <label class="control-label">小区添加</label>\
                            <table class="table white-bg delivery_xiaoqu_tb">\
                                <tbody>\
                                </tbody>\
                            </table>\
                        </div>\
                    </div>';

    deliver_result_div.append(wrapper_data);

    var delivery_table = deliver_result_div.find('.delivery_xiaoqu_tb');

    for (var i = 0; i < length; i++) {
        var street = streets.eq(i).val();

        var id = streets.eq(i).data("id");

        if (street == "" || id == undefined) {
            break;
        }

        var dataString = {"street_name": street, "street_id": id};

        $.ajax({
            type: "GET",
            url: API_URL + "active_street_to_xiaoqu",
            data: dataString,
            success: function (data) {
                var xiaoqus = data.xiaoqus;

                var current_street = data.current_street;

                var xiaodata = '<td class="col-sm-9">';

                for (j = 0; j < xiaoqus.length; j++) {
                    xiaoqu = xiaoqus[j];
                    xiaodata += '<div class="col-sm-3" style="padding-bottom:5px;"><input type="checkbox" name="area_xiaoqu[]" value="' + xiaoqu[0] + '" xiaoqu-id="' + xiaoqu[0] + '" checked class="i-checks">' + xiaoqu[1] + '</div>';
                }
                xiaodata += "</td>";

                var tdata = '<tr><td class="col-sm-3">' + current_street + '</td>' + xiaodata + '</tr>';


                delivery_table.append(tdata);

                $(delivery_table).find('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green'
                });

            },
            error: function (data) {
                console.log(data);
            }
        });
    }


});

//update station info
$('#station_update_form').on('submit', function (e) {
    e.preventDefault(e);
    $.ajaxSetup({
        header: $('meta[name="csrf-token"]').attr("content")
    });


    //radio button check
    var settle_check = $('input[name="settle_check"]:checked').val();
    if (settle_check == undefined) {
        alert("请选择结算账户");
        return;
    }

    var free_pay_check = $('input[name="free_pay_check"]:checked').val();
    if (free_pay_check == undefined) {
        alert("请选择自由支付账户");
        return;
    }

    //password same check
    var pwd = $('user_pwd').val();
    var repwd = $('user_repwd').val();

    if (pwd != repwd) {
        alert(" 密码不匹配");
        return;
    }

    var sendData = new FormData();

    var data = $('#station_update_form').serializeArray();

    console.log(data);

    $.each(data, function (i, field) {
        sendData.append(field.name, field.value);
    });

    var img_file = null;
    if ($('#st_img_upload')[0].files[0])
        img_file = $('#st_img_upload')[0].files[0];

    sendData.append('station_img', img_file);

    $.ajax({
        type: "POST",
        url: API_URL + "gongchang/xitong/tianjianaizhanzhanghu/update_station",
        data: sendData,
        processData: false,
        contentType: false,
        success: function (data) {
            console.log(data);
            if (data.status == "success") {
                show_success_msg('插入站成功');
                //save delivery area
                var sid = data.sid;
                save_delivery_area(sid);
                location.reload();
            }
        },
        error: function (data) {
            console.log(data);
        }
    });

});

//insert station info
$('#station_insert_form').on('submit', function (e) {
    e.preventDefault(e);

    if(check_password())
    {
        return;
    }

    var sendData = $('#station_insert_form').serializeArray();

    console.log(sendData);

    $.ajax({
        type: "POST",
        url: API_URL + "gongchang/xitong/tianjianaizhanzhanghu/insert_station",
        data: sendData,
        success: function (data) {
            console.log(data);
            if (data.status == "success") {
                var sid = data.sid;
                if ($('#st_img_upload')[0].files[0])
                    insert_station_image(sid);
                else
                    window.location = SITE_URL + "gongchang/xitong/naizhanzhanghao";
            }
        },
        error: function (data) {
            console.log(data);
        }
    });

});

//after save station info , upload image
function insert_station_image(sid){

    var sendData = new FormData();

    var img_file = null;
    if ($('#st_img_upload')[0].files[0])
        img_file = $('#st_img_upload')[0].files[0];
    else
    {
        window.location = SITE_URL + "gongchang/xitong/naizhanzhanghao";
        return;
    }
    sendData.append('station_img', img_file);
    sendData.append('sid', sid);

    $.ajax({
        url: API_URL+"gongchang/xitong/tianjianaizhanzhanghu/insert_station_image",
        type: "POST",
        data: sendData,
        processData: false,
        contentType: false,
        success:function(data){
            console.log(data);
            if(data.status == "success")
            {
                show_success_msg('添加成功');
                window.location = SITE_URL + "gongchang/xitong/naizhanzhanghao";

            } else {
                show_warning_msg('添加失败')
            }
        },
        error: function(data)
        {
            console.log(data);
            show_err_msg('添加失败')
        }

    })

}

//while insert station, check password
function check_password(){
    //password same check
    var pwd = $('#user_pwd').val();
    var repwd = $('#user_repwd').val();

    if (pwd != repwd) {
        $('#errmsg').show();
        setTimeout(function() { $("#errmsg").hide(); }, 5000);
        return true;
    } else
        return false;
}

function show_chosen() {
    var config = {
        '#area_street_list'        : {placeholder_text_multiple: '选择一些选项...'},
        '.chosen-select-deselect'  : {allow_single_deselect:true},
        '.chosen-select-no-single' : {disable_search_threshold:10},
        '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
        '.chosen-select-width'     : {width:"95%"}
    };

    for (var selector in config) {
        $(selector).trigger("chosen:updated");
        $(selector).chosen(config[selector]);
    }
}