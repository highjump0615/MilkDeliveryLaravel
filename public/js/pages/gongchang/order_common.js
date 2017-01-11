var gbIsEdit = false;
var gnOrderId = 0;

$(document).ready(function () {
    //Web Camera: this should be here
    Webcam.set({
        width: 400,
        height: 300,
        image_format: 'jpeg',
        jpeg_quality: 90
    });

    // 判断订单录入或修改
    if ($('.current_total_sp').length > 0) {
        gbIsEdit = true;
    }
    else {
        gbIsEdit = false;
    }

    // 获取订单id
    if ($('#input_order_id').length > 0) {
        gnOrderId = parseInt($('#input_order_id').val());
    }

    // 录入订单
    if ($('#my_camera').length) {   // 是否存在
        if ($('#my_camera').html().trim().length == 0) {
            show_camera();
        }
        // 修改订单，如果有图片
        else {
            $('#capture_camera').hide();
            $('#reset_camera').show();
            if ($('video').attr('src') != '')
                $('#check_capture').show();
        }
    }

    $('[data-target="#card_info"]').on("change", function () {
        $('#card_msg').hide();
        var card_check = $(this)[0];
        if ($(card_check).prop('checked')) {
            $('#card_info input').attr("disabled", false);
            $('#card_info').modal('show');
        }
        else {
            $('#card_info input').attr("disabled", true);
            $('#form-card-panel').hide();
            return;
        }
    });

    firstday = startofweek();
    lastday = endofweek();

    firstm = new Date(dateToday.getFullYear(), dateToday.getMonth(), 1);
    lastm = new Date(dateToday.getFullYear(), dateToday.getMonth() + 1, 0);

    // 初始化奶品起送日期
    initStartDateCalendar();

    // 初始化配送规则有关的日历
    $('#product_table').find('tbody > tr').each(function() {
        initBottleNumCalendar($(this));
    })

    //Create Switchery
    if (Array.prototype.forEach) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function (html) {
            var switchery = new Switchery(html);
        });
    } else {
        var elems = document.querySelectorAll('.js-switch');
        for (var i = 0; i < elems.length; i++) {
            var switchery = new Switchery(elems[i]);
        }
    }

    if (!$('#order_checker_list').val()) {
        show_warning_msg('没有征订员');
    }

    if ($('.province_list').val() != "none")
        $('.province_list').trigger('change');

    // 计算单数
    $('#product_table tbody tr').each(function () {
        set_avg_count(this);
    });

    verifyPhone();
});

//Show Camera function
function show_camera() {
    Webcam.attach('#my_camera');
}

$('#reset_camera').click(function () {

    show_camera();
    $(this).hide();
    $('#capture_camera').show();
    $('#check_capture').hide();
});

$('#capture_camera').click(function () {

    take_snapshot();
    $(this).hide();
    $('#reset_camera').show();
    if($('video').attr('src') != '')
        $('#check_capture').show();
});


//Trigger the factory_order_type
function trigger_factory_order_type_change() {

    $('#product_table tbody tr.one_product select.factory_order_type').trigger('change');
}

//As the factory order type changes, we should get the product price again
$('body').on('change', '#product_table tbody tr.one_product select.factory_order_type', function () {

    var current_order_type = $(this).val();
    var tr = $(this).closest('.one_product');

    var count = getOrderTypeBottleNum(tr, current_order_type);
    $(tr).find('.one_product_total_count').val(count);
    // $(tr).find('.one_product_total_count').attr('min', count);

    calculate_current_product_value(tr);
    set_avg_count(tr);

});

/**
 * 获取订单类型相应的数量
 * @param tr - parent tr
 * @param index
 * @returns {Number}
 */
function getOrderTypeBottleNum(tr, index) {
    var select_order_count = $(tr).find('select.one_product_total_count_select');

    var selected_option = $(select_order_count).find('option[data-otid="' + index + '"]');
    selected_option.prop("selected", true);

    return parseInt($(select_order_count).val());
}

//Calculate the current product value
function calculate_current_product_value(tr) {

    var product_count = $(tr).find('.one_product_total_count').val();
    if (product_count == undefined || product_count == 0) {
        return;
    }
    //need info: customer_addr or id, product_id, order_type, order_total_count
    var tt = $(tr).find('td .total_amount_per_product .one_p_amount');

    //customer id
    // var customer_id = $('input#customer_id').val();
    //
    // if (!customer_id)
    //     return;

    //or get addr from customer form's addr
    var province = $('select#province').val();
    var city = $('select#city').val();
    var district = $('select#district').val();

    var order_type = $(tr).find('select.factory_order_type').val();

    var product_id = $(tr).find('select.order_product_id').val();

    var sendData = {
        'product_id': product_id, 'order_type': order_type, 'product_count': product_count,
        /*'customer_id': customer_id,*/ 'province': province, 'city': city, 'district': district
    };
    console.log(sendData);

    $.ajax({
        url: API_URL + 'order/get_order_product_price',
        method: 'GET',
        data: sendData,
        success: function (data) {
            console.log(data);
            if (data.status == "success") {
                tt.val(data.order_product_price);
                get_order_statics();
            } else {
                tt.val('');
                reset_order_statics();

                if (data.message)
                    show_err_msg(data.message);
            }
        },
        error: function (data) {
            console.log(data);
            tt.val('');
        }
    });
}

//Set DanSu for the product line
function set_avg_count(tr) {
    var type = $(tr).find('.order_delivery_type').val();
    var avg = 0;
    var avg_input = $(tr).find('input.avg');

    var origin_count = $(tr).find('.one_product_total_count_select').val();

    var inputed_count = $(tr).find('.one_product_total_count').val();

    // if (inputed_count % 30 != 0) {
    //     show_dismissable_warning_msg('数量应为30的倍数');
    //
    //     $(tr).find('.one_product_total_count').val(origin_count);
    //     avg_input.val(1);
    //     return;
    // }

    if (origin_count) {
        avg = (inputed_count / origin_count).toPrecision(2);
        avg_input.val(avg);
    }
}

//Add Order Product
function add_product() {

    var tr = $('#product_table tbody tr:last')

    if (check_input_empty_for_one_product(tr)) {
        show_warning_msg('请填写产品的所有字段');
        return;
    }

    add_new_product_line();
}

//check whether the last one product line has empty value
function check_input_empty_for_one_product(tr) {
    var result = false;
    var order_type = $(tr).find('select.order_delivery_type').val();
    if (order_type == 1 || order_type == 2) {   // 天天送、隔日送
        $(tr).find('input').each(function () {
            if (($(this).val() == "" || !$(this).val()) && (!$(this).hasClass('delivery_dates'))) {
                result = true;
            }
        });
    }
    else {  // 按周送、随心送
        $(tr).find('input').each(function () {
            if (($(this).val() == "" || !$(this).val()) && (!$(this).hasClass('order_product_count_per'))) {
                result = true;
            }
        });
    }
    return result;
}

//get Total Order statics under the product table
function get_order_statics() {

    var fRemainCost;
    var fCurrentCost = parseFloat($('.current_total_sp').html());

    var pamounts = $('#product_table tbody tr.one_product input.one_p_amount');

    var gos = 0;
    for (var i = 0; i < pamounts.length; i++) {
        var pa = pamounts[i];
        gos += parseFloat($(pa).val());
    }

    $('#total_amount').val(gos.toFixed(1));

    // 修改订单
    if (gbIsEdit) {
        fRemainCost = fCurrentCost - gos;
        $('#remaining_after').val(fRemainCost.toFixed(1));
    }
    // 录入订单
    else {
        fRemainCost = $('#remaining').val();
        var acceptable_amount = gos - fRemainCost;
        $('#acceptable_amount').val(acceptable_amount.toFixed(1));
    }
}

//Reset Order Statics
function reset_order_statics() {
    $('#total_amount').val(0);
    $('#acceptable_amount').val(0);
}


//If the user changed the bottle count, we should recalculate the product price and avg(Dansu)
$('body').on('change', '#product_table tbody tr.one_product input.one_product_total_count', function () {

    var tr = $(this).closest('.one_product');
    calculate_current_product_value(tr);
    set_avg_count(tr);

});

//According to the delivery type, we should show  or hide the calendar or product_count_per_day
$('body').on('change', 'select.order_delivery_type', function () {

    var tr = $(this).parent().parent();
    var calendar = tr.find('.calendar_show')[0];

    //remove all previous data and set new data
    $(calendar).html('');
    var dd = '<div class="input-group date picker">'
        + '<input type="text" class="form-control delivery_dates" name="delivery_dates[]"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>'
        + '</div>';
    $(calendar).html(dd);

    initBottleNumCalendar(tr);
});

$('body').on('change', '#product_table tbody tr.one_product select.order_product_id', function () {
    var tr = $(this).closest('.one_product');
    // $(tr).find('.factory_order_type').trigger('change');

    // 重新计算价格和单数
    calculate_current_product_value(tr);
    set_avg_count(tr);
});

// 按周送和随心送也需要每次数量
$('body').on('change', 'input.order_product_count_per', function () {
    var tr = $(this).closest('tr');
    var pick = tr.find('.picker')[0];
    if (pick) {
        $(pick).datepicker('setBottleNum', $(this).val());
    }
});

// 订单起送日期为奶品的起送日期中最早的
$('body').on('change', '.single_date', function () {
    var tr = $(this).closest('tbody');
    var dateMin = $(this).datepicker('getDate');

    $(tr).find('.single_date').each(function () {
        var dateEach = $(this).datepicker('getDate');

        if (dateMin > dateEach) {
            dateMin = dateEach;
        }
    });

    $('#order_start_at').val(dateToString(dateMin));
});

//Remove one product line
$(document).on('click', 'button.remove_one_product', function () {

    var tr = $(this).closest('tr');
    var count = $('#product_table tbody tr.one_product').length;
    if (count > 1) {
        $(tr).remove();

        // 重新计算订单金额
        get_order_statics();
    }
    else {
        show_warning_msg('至少要有一种奶品');
        return;
    }
});

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
                var balance = data.balance;
                var product = data.product;
                console.log("balance:" + balance);

                $('#card_info').modal('hide');

                $('#card_msg').hide();
                $('#card_msg').text('');

                $('#form-card-id').text(card_id);
                $('#form-card-balance').text(balance);
                $('#form-card-product').text(product);
                $('#form-card-panel').show();
                $('#card_check_success').val(1);


            } else {
                console.log(data.msg);
                $('#card_msg').text(data.msg);
                $('#card_msg').show();
                $('#form-card-panel').hide();
                $('#card_check_success').val(0);
                
            }
        },
        error: function (data) {
            console.log(data);
            $('#form-card-panel').hide();
        }
    });
});

$('.cancel-card').click(function () {
    $('#milk_card_check').trigger('click');
    $('#form-card-panel').hide();
    $('#card_info').modal('hide');
    $('#card_check_success').val(1);
});

function init_product_lines() {
    $('#product_table').find('tbody > tr').remove();
    add_new_product_line();
}

/**
 * 添加新的产品
 */
function add_new_product_line() {
    if (!copy_tr_data)
        copy_tr_data = $("#first_data").html();

    var content = '<tr class="one_product">' + copy_tr_data + '</tr>';

    $("#product_table tbody").append(content);

    //after add new product, caculate the product price automatically
    tr = $('#product_table tbody tr:last');
    $(tr).find('.factory_order_type').trigger('change');

    initStartDateCalendar();
}

/**
 * 初始化起送日期控制
 */
function initStartDateCalendar() {

    var dateStart = getStartDate();

    //Single and Multiple Datepicker
    $('.single_date').datepicker({
        todayBtn: false,
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: false,
        autoclose: true,
        startDate: dateStart
    });

    $('.single_date').each(function () {

        var input = $(this).find('.start_at');
        var dateVal = new Date($(input).val());

        // 修改要改成以保存的, 过了保存的时期，只能选择今天
        if ($(input).val().length > 0 && dateVal > dateStart) {
            $(this).datepicker('setDate', dateVal);
            // $(this).datepicker('setStartDate', dateVal);
        }
        else {
            // 默认选择第一天
            $(this).datepicker('setDate', dateStart);
        }
    });
}

/**
 * 获取默认起送日期
 * @returns {Date}
 */
function getStartDate() {
    var able_date = new Date(dateToday);

    // 只有新订单才考虑3天后的问题
    if (!gbIsEdit && gnOrderId == 0) {
        if (gap_day)
            able_date.setDate(dateToday.getDate() + gap_day);
        else {
            gap_day = 3; //default
            able_date.setDate(dateToday.getDate() + 3);
        }
    }

    return able_date;
}

/**
 * 日期格式化
 * @param date
 * @returns {string}
 */
function dateToString(date) {
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear();
    var strDate = year + '-' + month + '-' + day;

    return strDate;
}

/**
 * 初始化按周送、随心送日历
 */
function initBottleNumCalendar(tr) {
    var calendar = tr.find('.calendar_show')[0];
    var selectDeliveryType = tr.find('select.order_delivery_type')[0];
    var bottle_number = tr.find('div.bottle_number')[0];

    var pick = tr.find('.picker')[0];

    // 获取输入的每次瓶数
    var inputBottleNum = tr.find('.order_product_count_per')[0];
    var nBottleNum = parseInt($(inputBottleNum).val());

    var id = $(selectDeliveryType).find('option:selected').val();

    if (id == 3 || id == 4) {

        var initNum = $(calendar).find('input').val();

        if (id == 3) {
            console.log(id);
            //show weekday
            $(pick).datepicker({
                multidate: true,
                todayBtn: false,
                clearBtn: true,
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: false,
                showNum: true,
                bottleNum: nBottleNum,
                initValue: initNum,
                startDate: '2016-11-13',
                endDate: '2016-11-19',
                class:'week_calendar',
            });
        }
        else {
            console.log(id);
            //show monthday
            $(pick).datepicker({
                multidate: true,
                todayBtn: false,
                clearBtn: true,
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: false,
                showNum: true,
                bottleNum: nBottleNum,
                initValue: initNum,
                startDate: firstm,
                endDate: lastm,
                class:'month_calendar',
            });
        }

        $(calendar).show();
        $(calendar).find('input').attr('required', true);
        // $(bottle_number).hide();
        // $(bottle_number).find('input').removeAttr('required');
    } else {

        $(calendar).hide();
        // $(bottle_number).show();
        // $(bottle_number).find('input').attr('required', true);
        $(calendar).find('input').removeAttr('required');
    }
}