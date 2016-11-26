$(document).ready(function () {
    //Web camera: this should be here
    Webcam.set({
        width: 400,
        height: 300,
        image_format: 'jpeg',
        jpeg_quality: 90
    });

    show_camera();

    firstday = startofweek();
    lastday = endofweek();

    // 解析当前服务器的时间 (2014-08-12 09:25:24)
    var time = s_timeCurrent.replace(/-/g,':').replace(' ',':');
    time = time.split(':');
    var date = new Date(time[0], (time[1]-1), time[2], time[3], time[4], time[5]);

    alert(date);
    firstm = new Date(date.getFullYear(), date.getMonth(), 1);
    lastm = new Date(date.getFullYear(), date.getMonth() + 1, 0);

    var today = date;
    var able_date = date;
    if(gap_day)
        able_date.setDate(today.getDate() + gap_day);
    else
    {
        gap_day = 3; //default
        able_date.setDate(today.getDate() + 3);
    }

    //Single and Multiple Datepicker
    $('.single_date').datepicker({
        todayBtn: false,
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: false,
        autoclose: true,
        startDate: able_date,
    });

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

    if(! $('#order_checker_list').val())
    {
        show_warning_msg('没有征订员');
    }

    if($('.province_list').val()!="none")
        $('.province_list').trigger('change');

});

//Show Camera function
function show_camera() {
    Webcam.attach('#my_camera');
}

$('#reset_camera').click(function(){

    show_camera();
    $(this).hide();
    $('#capture_camera').show();
    $('#check_capture').hide();
});

$('#capture_camera').click(function(){

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
    var select_order_count = $(tr).find('select.one_product_total_count_select');

    var selected_option = $(select_order_count).find('option[data-otid="' + current_order_type + '"]');
    selected_option.prop("selected", true);

    //Set New Min value for one product total count
    var count = $(select_order_count).val();
    $(tr).find('.one_product_total_count').val(count);
    $(tr).find('.one_product_total_count').attr('min', count);

    calculate_current_product_value(tr);
    set_avg_count(tr);

});

//Calculate the current product value
function calculate_current_product_value(tr) {

    var product_count = $(tr).find('.one_product_total_count').val();
    if (product_count == undefined || product_count == 0) {
        return;
    }
    //need info: customer_addr or id, product_id, order_type, order_total_count
    var tt = $(tr).find('td .total_amount_per_product .one_p_amount');

    //customer id
    var customer_id = $('input#customer_id').val();

    if (!customer_id)
        return;

    //or get addr from customer form's addr
    var province = $('select#province').val();
    var city = $('select#city').val();
    var district = $('select#district').val();

    var order_type = $(tr).find('select.factory_order_type').val();

    var product_id = $(tr).find('select.order_product_id').val();

    var sendData = {
        'product_id': product_id, 'order_type': order_type, 'product_count': product_count,
        'customer_id': customer_id, 'province': province, 'city': city, 'district': district
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

                if(data.message)
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
    if (inputed_count % 30 != 0) {
        show_dismissable_warning_msg('数量应为30的倍数');

        $(tr).find('.one_product_total_count').val(origin_count);
        avg_input.val(1);
        return;
    }

    avg = (inputed_count / origin_count).toPrecision(2);
    avg_input.val(avg);
}

//Add Order Product
function add_product() {

    var tr = $('#product_table tbody tr:last')

    if (check_input_empty_for_one_product(tr))
    {
        show_warning_msg('请填写产品的所有字段');
        return;
    }

    if (!copy_tr_data)
        copy_tr_data = $("#first_data").html();

    var rowCount = $('#product_table tbody tr').length;
    var trclass = "footable-even";
    if (rowCount % 2 !== 0)
        trclass = "footable-odd";
    var content = '<tr class="' + trclass + ' one_product">' + copy_tr_data + '</tr>';

    $("#product_table tbody").append(content);

    //after add new product, caculate the product price automatically
    tr = $('#product_table tbody tr:last');
    $(tr).find('.factory_order_type').trigger('change');
}

//check whether the last one product line has empty value
function check_input_empty_for_one_product(tr) {
    var result = false;

    var order_type = $(tr).find('select.order_delivery_type').val();
    if (order_type == 1 || order_type == 2) {
        $(tr).find('input').each(function () {
            if (($(this).val() == "" || !$(this).val()) && (!$(this).hasClass('delivery_dates'))) {
                result = true;
            }
        });

    } else {
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
    var pamounts = $('#product_table tbody tr.one_product input.one_p_amount');

    var gos = 0;
    for (var i = 0; i < pamounts.length; i++) {
        var pa = pamounts[i];
        gos += parseInt($(pa).val());
    }

    var remaining = $('#remaining').val();
    var acceptable_amount = gos - remaining;

    $('#total_amount').val(gos);
    $('#acceptable_amount').val(acceptable_amount);

}

//Reset Order Statics
function reset_order_statics(){
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

    var tr = $(this).parent().parent().parent();
    var calendar = tr.find('.calendar_show')[0];
    var bottle_number = tr.find('div.bottle_number')[0];

    //remove all previous data and set new data
    $(calendar).html('');
    var dd = '<div class="col-sm-4"> <label class="control-label">配送日期</label> </div> <div class="input-group date col-sm-8 picker">' +
        '<input type="text" class="form-control delivery_dates" name="delivery_dates[]"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>';
    $(calendar).html(dd);

    var pick = tr.find('.picker')[0];

    var id = $(this).find('option:selected').val();

    if (id == 3 || id == 4) {
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
                startDate: firstday,
                endDate: lastday,
                class:'week_calendar',
            });
        } else {
            //show monthday
            $(pick).datepicker({
                multidate: true,
                todayBtn: false,
                clearBtn: true,
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: false,
                showNum: true,
                startDate: firstm,
                endDate: lastm,
                class:'month_calendar',
            });
        }

        $(calendar).show();
        $(calendar).find('input').attr('required', true);
        $(bottle_number).hide();
        $(bottle_number).find('input').removeAttr('required');
    } else {

        $(calendar).hide();
        $(bottle_number).show();
        $(bottle_number).find('input').attr('required', true);
        $(calendar).find('input').removeAttr('required');
    }
});

$('body').on('change', '#product_table tbody tr.one_product select.order_product_id', function () {
    var tr = $(this).closest('.one_product');
    $(tr).find('.factory_order_type').trigger('change');
    set_avg_count(tr);
});

//Remove one product line
$(document).on('click', 'button.remove_one_product', function(){

    var tr = $(this).closest('tr');
    var count = $('#product_table tbody tr.one_product').length;
    if(count>1)
        $(tr).remove();
    else
    {
        show_warning_msg('您无法删除所有产品');
        return;
    }
});

function init_product_lines()
{
    if (!copy_tr_data)
        copy_tr_data = $("#first_data").html();

    $('#product_table').find('tbody > tr').remove();

    var trclass = "footable-odd";

    var content = '<tr class="' + trclass + ' one_product">' + copy_tr_data + '</tr>';

    $("#product_table tbody").append(content);

    //after add new product, caculate the product price automatically
    tr = $('#product_table tbody tr:last');
    $(tr).find('.factory_order_type').trigger('change');
}
