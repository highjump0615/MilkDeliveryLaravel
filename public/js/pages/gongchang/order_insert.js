var g_aryCardId = [];
var gbProductChanged = false;

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

                    if (city == city_name) {
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

                for (var i = 0; i < districts.length; i++) {
                    var district = districts[i];
                    var districtdata;

                    if (district == district_name) {
                        districtdata = '<option value="' + district + '" selected>' + district + '</option>';
                    }
                    else {
                        districtdata = '<option value="' + district + '">' + district + '</option>';
                    }

                    district_list.append(districtdata);
                }

                var current_district = district_list.val();
                if (current_district)
                    district_list.trigger('change');

                // 计算价格
                $('#product_table tbody tr').each(function () {
                    calculate_current_product_value(this, true);
                });

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

    // 清空街道列表
    var street_list = $(this).parent().parent().find('.street_list');
    street_list.empty();

    // 清空小区列表
    var xiaoqu_list = $(this).parent().parent().find('.xiaoqu_list');
    $(xiaoqu_list).empty();

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

                    if (street[1] == street_name) {
                        streetdata = '<option data-street-id="' + street[0] + '" value="' + street[1] + '" selected>' + street[1] + '</option>';
                    }
                    else {
                        streetdata = '<option data-street-id="' + street[0] + '" value="' + street[1] + '">' + street[1] + '</option>';
                    }

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
    };

    $.ajax({
        type: "GET",
        url: API_URL + "active_street_to_xiaoqu",
        data: dataString,
        success: function (data) {
            var xiaoqus = data.xiaoqus;
            var xiaodata;

            for (var i = 0; i < xiaoqus.length; i++) {
                var xiaoqu = xiaoqus[i];

                if (xiaoqu[1] == village_name) {
                    xiaodata = '<option data-xiaoqu-id="' + xiaoqu[0] + '" value = "' + xiaoqu[1] + '" selected>' + xiaoqu[1] + '</option>';
                }
                else {
                    xiaodata = '<option data-xiaoqu-id="' + xiaoqu[0] + '" value = "' + xiaoqu[1] + '">' + xiaoqu[1] + '</option>';
                }

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

    var customer_data = $('#customer_form').serializeArray();
    var strApiUrl = API_URL + "gongchang/dingdan/dingdanluru/insert_customer";

    if (gbIsStation) {
        strApiUrl = API_URL + "naizhan/dingdan/dingdanluru/insert_customer";
    }

    $.ajax({
        type: "POST",
        url: strApiUrl,
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
                var station_data = '<option data-milkman="' + data.milkman_id + '" data-deliveryarea="' + data.deliveryarea_id + '" value="' + data.station_id + '">' +
                    data.station_name +
                    '</option>';
                $('#station_list').append(station_data);

                // 重新初始化起送日期
                dateToday = new Date(data.date_start);
                var dateStart = getStartDate();
                $('.single_date').datepicker('setStartDate', dateStart);

                //trigger to calculate the product price
                // init_product_lines();
            }
            else {
                if (data.message) {
                    show_err_msg(data.message);
                }

                // update station list
                $('#station_list').empty();
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

/**
 * 随心送时确保总数量的匹配
 * @returns {boolean} true 数量跟配送日期的数量的和一只
 */
function isFreeOrderValid() {
    var bRes = true;

    $('#product_table tbody tr.one_product').each(function () {
        var type = parseInt($(this).find('.order_delivery_type').val());

        // 只考虑随心送的奶品
        if (type != 4) {
            return true;
        }

        var nOrderCount = $(this).find('.picker').datepicker('getTotalCount');
        var nTotalCount = parseInt($(this).find('select.one_product_total_count_select').val());

        // 数量不匹配, 退出
        if (nOrderCount != nTotalCount) {
            bRes = false;
            return false;
        }
    });

    return bRes;
}

//Insert Order
$('#product_form').on('submit', function (e) {
    var that = this;

    e.preventDefault();

    // 检查客户表单
    if (!$('#customer_form')[0].reportValidity()) {
        return false;
    }

    // 检查订单参数表单
    if (!$('#order_form')[0].reportValidity()) {
        return false;
    }

    //card check
    var card_suc = $('#card_check_success').val();
    if(card_suc == '0')
    {
        show_warning_msg('卡信息不正确');
        return;
    }

    var empty_tr = false;
    $('#product_table tbody tr').each(function(){
        if(check_input_empty_for_one_product(this))
            empty_tr = true;
    });

    if (empty_tr)
    {
        show_warning_msg('请填写产品的所有字段');
        return;
    }

    var pass = true;
    //Check the product total amount
    $('.one_product .one_p_amount').each(function(){
        if(!$(this).val())
            pass = false;
    });

    if(!pass)
    {
        show_warning_msg("产品价格不计算");
        return;
    }

    // 验证产品数量和订单类型的规则
    var nMinBottleNum = 0;
    $('.factory_order_type').each(function(){

        var tr = $(this).closest('.one_product');
        var index = $(this).val();

        var nBottleNum = getOrderTypeBottleNum(tr, index);
        nMinBottleNum = Math.max(nMinBottleNum, nBottleNum)
    });

    // 数量总和
    if (!gbIsEdit) {
        var nTotalBottleNum = 0;
        $('.one_product_total_count').each(function () {
            nTotalBottleNum += parseInt($(this).val());
        });

        if (nTotalBottleNum < nMinBottleNum) {
            show_err_msg("订单数量总合得符合订单类型条件");
            return;
        }
    }

    // 随心送选择日期的数量总和是否总数量相同
    // if (!isFreeOrderValid()) {
    //     show_err_msg("随心送配送规则的总数量和该奶品的数量不匹配");
    //     return;
    // }

    // 订单修改，更改后金额不能超过订单余额
    if (gbIsEdit) {
        var fRemainCost = $('#remaining_after').val();
        if (fRemainCost < 0) {
            show_err_msg("更改后金额不能超过订单余额");
            return;
        }
    }

    // 订单参数信息
    var sendData = $('#order_form').serializeArray();

    // 产品信息
    if (gbProductChanged) {
        sendData = sendData.concat($(this).serializeArray());
    }

    if ($('input[name="milk_card_check"]').prop("checked")) {

        // 检查奶卡金额足够
        var nTotalValue = getCardValue();
        var dOrderValue = parseFloat($('#acceptable_amount').val());

        if (nTotalValue < dOrderValue) {
            show_err_msg("订单金额已超过奶卡金额，不足于支付");
            return;
        }

        sendData.push({'name': 'card_id', 'value': g_aryCardId});
    } else {
        sendData.push({'name': 'milk_card_check', 'value': 'off'});
    }

    // 验证配送区域与配送员
    var milkman_id = $('#station_list option:selected').data('milkman');
    var deliveryarea_id = $('#station_list option:selected').data('deliveryarea');

    if (!milkman_id || !deliveryarea_id) {
        show_err_msg("奶站没有配送员或该地区没有覆盖可配送的范围");
        return;
    }

    enableSubmitButton(that, false);

    sendData.push({'name': 'milkman_id', 'value': milkman_id});
    sendData.push({'name': 'deliveryarea_id', 'value': deliveryarea_id});

    //Customer Info
    var customer_data = $('#customer_form').serializeArray();
    sendData = sendData.concat(customer_data);

    // 把数据转成FormData
    var fd = new FormData();
    $.each(sendData, function(key, input) {
        fd.append(input.name, input.value);
    });

    // 票据号图片
    var fileData = $('input[name="input-receipt"]')[0].files;
    if (fileData.length > 0) {
        fd.append('receipt_img', fileData[0]);
    }

    // API链接
    var strApiUrl, strUrlAfter;

    strApiUrl = API_URL + 'gongchang/dingdan/dingdanluru/insert_order';
    if (gbIsStation) {
        strApiUrl = API_URL + 'naizhan/dingdan/dingdanluru/insert_order';
    }

    $.ajax({
        type: "POST",
        url: strApiUrl,
        data: fd,
        contentType: false,
        processData: false,
        success: function (data) {
            console.log(data);
            if (data.status == 'success') {
                var order_id = data.order_id;

                if (gbIsEdit) {
                    show_success_msg('订单修改成功');

                    // 如果是订单修改，跳转到待审核订单
                    strUrlAfter = SITE_URL + 'gongchang/dingdan/daishenhedingdan/daishenhe-dingdanxiangqing/' + order_id;
                    if (gbIsStation) {
                        strUrlAfter = SITE_URL + 'naizhan/dingdan/xiangqing/' + order_id;
                    }
                }
                else {
                    show_success_msg('订单录入成功');

                    // 如果是订单录入，清空页面，易于录入别的
                    strUrlAfter = SITE_URL + 'gongchang/dingdan/dingdanluru';
                    if (gbIsStation) {
                        strUrlAfter = SITE_URL + 'naizhan/dingdan/dingdanluru';
                    }
                }

                window.location.replace(strUrlAfter);

            } else {
                if (data.message)
                    show_err_msg(data.message);

                enableSubmitButton(that, true);
            }
        },
        error: function (data) {
            console.log(data);
            enableSubmitButton(that, true);
        }
    });
});

/**
 * 禁用/使用提交按钮
 * @param form
 * @param isEnable
 */
function enableSubmitButton(form, isEnable) {
    $(form).find('button[type="submit"]').prop('disabled', !isEnable);
}

// Card Verfiy
$('.verify-card').click(function () {
    var card_id = $('#card_id').val();
    var card_code = $('#card_code').val();

    $.ajax({
        type: "POST",
        url: API_URL + "gongchang/dingdan/dingdanluru/verify_card",
        data: {
            'card_id': card_id,
            'card_code': card_code,
        },
        success: function (data) {
            console.log(data);
            if (data.status == 'success') {
                var nId = parseInt(data.id);

                // 检查该奶卡是否已选择
                if ($.inArray(nId, g_aryCardId) >= 0) {
                    setCardModalNotice("已选择了这张卡");
                    return;
                }

                // 累加奶卡面值
                var balance = parseInt(data.balance);
                var nTotalValue = getCardValue();
                nTotalValue += balance;
                $('#form-card-balance').html(nTotalValue);

                // 奶卡id添加到数组
                g_aryCardId.push(nId);

                $('#card_info').modal('hide');

                setCardModalNotice('');

                $('#form-card-id').text(card_id);

                $('#form-card-panel').show();
                $('#card_check_success').val(1);

                // 打开奶卡开关
                var objSwitch = $('#milk_card_check');
                if (!objSwitch.is(':checked')) {
                    $('#milk_card_check').trigger('click');
                }
            }
            else {
                console.log(data.msg);

                setCardModalNotice(data.msg);
                $('#card_check_success').val(0);
            }
        },
        error: function (data) {
            console.log(data);
            $('#form-card-panel').hide();
        }
    });
});

/**
 * 显示/隐藏奶卡modal的提示
 * @param msg
 */
function setCardModalNotice(msg) {
    var objMsg = $('#card_msg');
    if (msg.length > 0) {
        objMsg.show();
    }
    else {
        objMsg.hide();
    }

    objMsg.text(msg);
}

/**
 * 获取奶卡金额
 * @returns {Number}
 */
function getCardValue() {
    var nTotalValue = parseInt($('#form-card-balance').html());
    if (isNaN(nTotalValue)) {
        nTotalValue = 0;
    }

    return nTotalValue;
}

/**
 * 使用/禁用票据号
 * @param enabled
 */
function enableReceipt(enabled) {
    $('#receipt_number').prop('disabled', enabled);
    $('#reset_camera').prop('disabled', enabled);
    $('#capture_camera').prop('disabled', enabled);
    $('#btn-upload').prop('disabled', enabled);
}

$(document).ready(function () {
    // 显示奶卡modal事件

    $('#card_info').on('hidden.bs.modal', function () {
        $('#card_id').val('');
        $('#card_code').val('');
        setCardModalNotice('');
    });

    var objSwitch = document.querySelector('#milk_card_check');
    objSwitch.onchange = function() {
        // 如果是奶卡订单，不使用票据号
        if (objSwitch.checked) {
            enableReceipt(true);
        }
        else {
            enableReceipt(false);
        }
    };
});