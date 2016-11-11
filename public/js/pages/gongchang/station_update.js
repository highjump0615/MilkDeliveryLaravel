/**
 * Created by Administrator on 10/21/2016.
 */

var init = true;

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
        $('.province_list').trigger('change');

    $('#user_pwd').val('');
    $('#user_repwd').val('');
});

$('#change_delivery_area').click(function(){
   $('#origin_delivery_area').hide();
    $('#new_delivery_area').css('display', 'visible');
});


//When Select Station Changed
$(document).on('change', '#select_province', function () {

    station_province = $(this).val();

    if (!station_province) {
        return;
    }

    var city_list = $('#select_city');
    $(city_list).empty();

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

                cities = data.city;

                for (var i = 0; i < cities.length; i++) {
                    var city_name = cities[i];

                    if(init && station_city == city_name)
                    {
                        citydata = '<option value="' + city_name + '" selected>' + city_name + '</option>';
                    }
                    else if (station_city && station_city == city_name) {
                        citydata = '<option value="' + city_name + '" selected>' + city_name + '</option>';
                    } else if( i == 0) {
                        citydata = '<option value="' + city_name + '" selected>' + city_name + '</option>';
                    } else {
                        citydata = '<option value="' + city_name + '">' + city_name + '</option>';
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

    $(district_list).empty();

    $.ajax({
        type: "GET",
        url: API_URL + "active_city_to_district",
        data: {
            'province': province,
            'city': station_city,
        },
        success: function (data) {
            if (data.status == "success") {

                var districts = data.district;

                for (var i = 0; i < districts.length; i++) {
                    var district_name = districts[i];

                    var districtdata;
                    if(init && station_district == district_name)
                    {
                        districtdata = '<option value="' + district_name + '" selected>' + district_name + '</option>';
                    }
                    else if (station_district && station_district == district_name) {
                        districtdata = '<option value="' + district_name + '" selected>' + district_name + '</option>';
                    } else if( i== 0){
                        districtdata = '<option value="' + district_name + '" selected>' + district_name + '</option>';
                    }
                    else {
                        districtdata = '<option value="' + district_name + '">' + district_name + '</option>';
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
    if(init)
    {
        init = false;
        init_delivery_area();
        return;
    }
    else {
        station_district = $(this).val();
        // set_station_admin_name();
        init_delivery_area();

        if($('#new_delivery_area').css('display') == "none")
        {
            $('#change_delivery_area').trigger('click');
        }
    }
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

    var strStreetListId = '#area_street_list';
    var street_list = $(strStreetListId);

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
                'city': station_city,
            },
            success: function (data) {
                if (data.status == "success") {

                    var streets = data.streets;

                    for (var i = 0; i < streets.length; i++) {
                        var street = streets[i];

                        var streetdata = '<option data-id="' + street[0] + '" value="' + street[1] + '">' + street[1] + '</option>';
                        street_list.append(streetdata);
                    }

                    // 添加已设置的配送范围街道
                    for (var i = 0; i < ary_deliver_street.length; i++) {
                        var street_info = ary_deliver_street[i];
                        $(strStreetListId + " option[value='" + street_info.name + "']").attr('selected', 'selected');
                    }
                    $(strStreetListId).trigger("liszt:updated");
                }

                show_chosen();
                street_list.change();
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

    deliver_result_div.html("");

    var streets = $(this).find('option:selected');
    var length = streets.length;

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

                // 如果失败，直接返回
                if (data.status != "success") {
                    return;
                }

                var xiaoqus = data.xiaoqus;

                var current_street = data.current_street;

                var xiaodata = '<td class="col-sm-9">';

                for (var j = 0; j < xiaoqus.length; j++) {
                    var xiaoqu = xiaoqus[j];

                    if(ary_deliver_street.length != 0)
                    {
                        // 获取街道索引
                        var nIndexStreet = 0;
                        for (var m = 0; m < ary_deliver_street.length; m++) {
                            var objStreet = ary_deliver_street[m];

                            if (objStreet.name == current_street) {
                                nIndexStreet = m;
                                break;
                            }
                        }

                        // 是不是已经选好的
                        var strChecked = '';
                        for (var k = 0; k < ary_deliver_street[nIndexStreet].xiaoqu.length; k++) {
                            var xiaoqu_info = ary_deliver_street[nIndexStreet].xiaoqu[k];
                            if (xiaoqu_info.name == xiaoqu[1]) {
                                strChecked = 'checked';
                                break;
                            }
                        }

                    }

                    xiaodata += '<div class="col-sm-3" style="padding-bottom:5px;"><input type="checkbox" name="area_xiaoqu[]" value="' + xiaoqu[0] + '" xiaoqu-id="' + xiaoqu[0] + '" ' + strChecked + ' class="i-checks">' + xiaoqu[1] + '</div>';
                }
                xiaodata += "</td>";

                var tdata = '<tr><td class="col-sm-3">' + current_street + '</td>' + xiaodata + '</tr>';


                delivery_table.append(tdata);

                $(delivery_table).find('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
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
    if(check_password())
    {
        return;
    }

    var sendData = $('#station_update_form').serializeArray();

    console.log(sendData);

    $.ajax({
        type: "POST",
        url: API_URL + "gongchang/xitong/tianjianaizhanzhanghu/update_station",
        data: sendData,
        success: function (data) {
            console.log(data);
            if (data.status == "success") {

                var sid = data.sid;
                if ($('#st_img_upload')[0].files[0]) {
                    insert_station_image(sid);
                } else {
                    show_success_msg('站改变了');
                    window.location = SITE_URL+'gongchang/xitong/naizhanzhanghao';
                }

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
    {

        img_file = $('#st_img_upload')[0].files[0];
    }
    else
    {
        show_success_msg('站改变了');
        // location.reload();
        window.location = SITE_URL+'gongchang/xitong/naizhanzhanghao';
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
                // location.reload();
                show_success_msg('站改变了');
                window.location = SITE_URL+'gongchang/xitong/naizhanzhanghao';

            } else {
                if(data.message)
                    show_err_msg(data.message);
            }
        },
        error: function(data)
        {
            console.log(data);
        }
    })

}

//while insert station, check password
function check_password(){
    //password same check
    var pwd = $('#user_pwd').val();
    var repwd = $('#user_repwd').val();

    if(pwd == "" && repwd == "")
     return false;

    if (pwd != repwd) {
        $('#errmsg').show();
        setTimeout(function() { $("#errmsg").hide(); }, 5000);
        return true;
    } else
        return false;
}

function show_chosen() {
    var config = {
        '#area_street_list': {placeholder_text_multiple: '选择一些选项...'},
        '.chosen-select-deselect': {allow_single_deselect: true},
        '.chosen-select-no-single': {disable_search_threshold: 10},
        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
        '.chosen-select-width': {width: "95%"}
    }

    for (var selector in config) {
        $(selector).trigger("chosen:updated");
        $(selector).chosen(config[selector]);
    }
}
