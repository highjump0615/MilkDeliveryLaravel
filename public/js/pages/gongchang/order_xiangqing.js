$(document).ready(function() {
    //
    // 初始化暂停和开始日期选取日历
    //
    var stop_start_able_date = new Date(s_timeCurrent);

    if (order_start_date > stop_start_able_date) {
        stop_start_able_date = order_start_date;
    }

    var restart_able_date = new Date(s_timeCurrent);
    if (order_start_date > restart_able_date) {
        restart_able_date = order_start_date;
    }

    //set the stop modal 's start stop date val as orders's start date at least.
    if ($('#stop_order_modal').length) {
        $('#stop_order_modal').find('.input-daterange #stop_start').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            calendarWeeks: false,
            clearBtn: true,
            startDate: stop_start_able_date,
            endDate: order_end_date
        });

        $('#stop_order_modal').find('.input-daterange #stop_end').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            calendarWeeks: false,
            clearBtn: true,
            startDate: order_start_date
        });
    }

    if ($('#restart_order_modal').length) {
        $('#restart_order_modal').find('.single_date').datepicker({
            keyboardNavigation: false,
            forceParse: false,
            autoclose: true,
            calendarWeeks: false,
            clearBtn: true,
            startDate: restart_able_date
        });
    }

    if (stop_from != "Invalid Date" && stop_to != "Invalid Date") {

        if (stop_from < stop_start_able_date) {
            stop_from = stop_start_able_date;
        }

        // 显示modal事件
        $('#stop_order_modal').on('shown.bs.modal', function () {
            $(this).find('.input-daterange #stop_start').datepicker('setDate', stop_from);
            $(this).find('.input-daterange #stop_end').datepicker('setDate', stop_to);
        });

        // 设置开启选择范围
        $('#restart_order_modal').find('.single_date').datepicker('setEndDate', stop_to);
    }

    // 隐藏modal事件
    $('#stop_order_modal').on('hidden.bs.modal', function () {
        $(this).find('.input-daterange #stop_start').datepicker('setDate', '');
        $(this).find('.input-daterange #stop_end').datepicker('setDate', '');
    });
});

/*
 * Stop Order
 */
$('#stop_order_modal_form').submit(function (e) {

    e.preventDefault();

    var submit = $(this).find('button[type="submit"]');
    $(submit).prop('disabled', true);

    var sendData = $(this).serializeArray();

    var strUrl = SITE_URL + "gongchang/dingdan/zantingdingdan";
    if (gbIsStation) {
        strUrl = SITE_URL + "naizhan/dingdan/zantingliebiao";
    }

    $.ajax({
        type: "POST",
        url: API_URL + "gongchang/dingdan/stop_order_in_gongchang",
        data: sendData,
        success: function (data) {
            if (data.status == 'success') {
                var stop_start_date = data.stop_start;
                var stop_end_date = data.stop_end;

                show_success_msg("停止订单成功: " + stop_start_date + " ~ " + stop_end_date);

                var stopped_start = new Date(stop_start_date);
                var stopped_end = new Date(stop_end_date);
                //compare today with stop start-end: go to zanting
                if (stopped_start < today && stopped_end > today) {
                    window.location = strUrl;
                }

                location.reload();

            } else {
                if (data.message)
                    show_warning_msg("停止订单失败: " + data.message);
                else
                    show_warning_msg("停止订单失败.");
            }

            $('#stop_order_modal').modal('hide');
            $(submit).prop('disabled', false);
        },
        error: function (data) {
            console.log(data);
        }
    });
});

/*
 *  Restart Order
 */

$('body').on('click', 'button#restart_order_bt', function (e) {
    e.preventDefault();

    var order_id = $(this).data('orderid');
    var stop_at = $(this).data('stop-at');
    var restart_at = $(this).data('restart-at');

    $('#restart_order_id').val(order_id);

    var period = stop_at + " ~ " + restart_at;
    $('#stop_period').text(period);

    $('#restart_order_modal').show();

});

$('#restart_order_modal_form').submit(function (e) {

    e.preventDefault();

    var submit = $(this).find('button[type="submit"]');
    $(submit).prop('disabled', true);
    var sendData = $(this).serializeArray();
    console.log(sendData);

    var strUrl = SITE_URL + "gongchang/dingdan/quanbudingdan-liebiao";
    if (gbIsStation) {
        strUrl = SITE_URL + "naizhan/dingdan/quanbuluru";
    }

    $.ajax({
        type: "POST",
        url: API_URL + "gongchang/dingdan/restart_order",
        data: sendData,
        success: function (data) {
            console.log(data);
            if (data.status == 'success') {
                $('#restart_order_modal').modal('hide');
                $(submit).prop('disabled', true);

                show_success_msg("开启订单成功");

                if (data.restart_at && data.restart_at == today) {
                    var url = strUrl;
                    window.location = url;
                }
                else {
                    location.reload();
                }

            } else {
                $('#restart_order_modal').modal('hide');
                $(submit).prop('disabled', false);
                show_warning_msg("开启订单失败. " + data.message);
            }
        },
        error: function (data) {
            console.log(data);
            $(submit).prop('disabled', true);
        }
    });
});


/*
 *  Cancel Order
 */
$('#cancel_order_bt').click(function () {
    $.confirm({
        icon: 'fa fa-warning',
        title: '取消订单',
        text: '您确定要取消此订单吗?',
        confirmButton: "是",
        cancelButton: "不",
        confirmButtonClass: "btn-success",
        confirm: function () {
            cancel_order();
        },
        cancel: function () {
            return;
        }
    });
});

function cancel_order() {
    var order_id = $('#cancel_order_bt').data("orderid");
    var sendData = {'order_id': order_id};

    var strUrl = SITE_URL + "gongchang/dingdan/quanbudingdan-liebiao";
    if (gbIsStation) {
        strUrl = SITE_URL + "naizhan/dingdan/quanbuluru";
    }

    $.ajax({
        type: "POST",
        url: API_URL + "gongchang/dingdan/cancel_order",
        data: sendData,
        success: function (data) {
            console.log(data);
            if (data.status = 'success') {
                show_success_msg('取消订单成功');

                //go to the quanbuluru
                window.location = strUrl;
            }
            else {
                if (data.message)
                    show_warning_msg(data.message);
            }
        },
        error: function (data) {
            console.log(data);
        }
    });
}

/*
 * Postpone Order: 1 day
 */

$('#postpone_order_bt').click(function () {
    $.confirm({
        icon: 'fa fa-warning',
        title: '顺延订单',
        text: '您确定要顺延此订单吗?',
        confirmButton: "是",
        cancelButton: "不",
        confirmButtonClass: "btn-success",
        confirm: function () {
            postpone();
        },
        cancel: function () {
            return;
        }
    });
});

function postpone() {
    var order_id = $('#postpone_order_bt').data("orderid");
    var sendData = {'order_id': order_id};
    $.ajax({
        type: "POST",
        url: API_URL + "gongchang/dingdan/postpone_order",
        data: sendData,
        success: function (data) {
            console.log(data);
            if (data.status = 'success') {
                show_success_msg("顺延订单成功");
                //refresh current page
                location.reload();
            } else {
                if (data.message)
                    show_warning_msg(data.message);
            }
        },
        error: function (data) {
            console.log(data);
        }
    });
}

/*
 * Change delivery plan for one day
 *
 * */
//Change one product's order status
$('body').on('change', 'input.plan_count', function () {

    var tr = $(this).closest('tr');
    var origin_plan_count = $(this).attr("origin_plan_count");
    var change_bt = $(tr).find('.xiugai_plan_bt');

    var changed_plan_count = parseInt($(this).val());

    if (changed_plan_count < 0) {
        $(this).val(origin_plan_count);
        $(change_bt).prop("disabled", true);
    } else {
        if (changed_plan_count != origin_plan_count) {
            $(change_bt).prop("disabled", false);
        } else {
            $(change_bt).prop("disabled", true);
        }
    }

});

$('body').on('click', 'button.xiugai_plan_bt', function () {
    var tr = $(this).closest('tr');
    var plan_id = $(tr).data("planid");
    var plan_input = $(tr).find('input.plan_count');

    var change_bt = $(this);

    var order_id = $('#order_id').val();

    var changed_plan_count = $(plan_input).val();
    var origin_plan_count = $(plan_input).attr("origin_plan_count");

    if (changed_plan_count == origin_plan_count) {
        $(this).prop("disabled", true);
        return;
    } else {
        var sendData = {
            'order_id': order_id,
            'plan_id': plan_id,
            'origin': origin_plan_count,
            'changed': changed_plan_count
        };

        console.log(sendData);

        $.ajax({
            type: "POST",
            url: API_URL + 'gongchang/dingdan/change_delivery_plan_for_one_day_in_xiangqing_and_xiugai',
            data: sendData,
            success: function (data) {
                console.log(data);

                if (data.status == "success") {
                    if (data.message)
                        show_success_msg(data.message);

                    location.reload();
                } else {
                    if(data.message)
                        show_warning_msg(data.message);

                    $(plan_input).val(origin_plan_count);
                    $(change_bt).prop("disabled", true);
                }
            },
            error: function (data) {
                console.log(data);
            }
        });
    }
});