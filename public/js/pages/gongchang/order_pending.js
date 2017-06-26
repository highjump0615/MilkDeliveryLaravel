/**
 * Created by Administrator on 6/27/17.
 */

// 解析当前服务器的时间 (2014-08-12 09:25:24)
var gDateToday = new Date(s_timeCurrent);

$('#pass_order').click(function () {
    var pass_bt = $(this);
    var no_pass_bt = $('#no_pass_order');
    var order_id = $(this).data("orderid");
    var sendData = {'order_id_to_pass': order_id};
    $.ajax({
        type: "GET",
        url: API_URL + "gongchang/daishenhedingdan/pass_order",
        data: sendData,
        success: function (data) {
            console.log(data);
            if (data.status == 'success') {
                if (data.message) {
                    show_success_msg(data.message);
                }
                window.location = SITE_URL + "gongchang/dingdan/daishenhedingdan";
            } else {
                if (data.message) {
                    show_warning_msg(data.message);
                }
            }
        },
        error: function (data) {
            console.log(data);
        }
    })
});

$('#no_pass_order').click(function () {
    var no_pass_bt = $(this);
    var pass_bt = $('#pass_order');

    var order_id = $(this).data("orderid");
    var sendData = {'order_id_to_not_pass': order_id};
    $.ajax({
        type: "GET",
        url: API_URL + "gongchang/daishenhedingdan/no_pass_order",
        data: sendData,
        success: function (data) {
            if (data.status == 'success') {
                if (data.message) {
                    show_success_msg(data.message);
                }
                window.location = SITE_URL + "gongchang/dingdan/daishenhedingdan";
            } else {
                if (data.message) {
                    show_warning_msg(data.message);
                }
            }
        },
        error: function (data) {
            //console.log(data);
        }
    })
});

$('#change_sub_addr').click(function () {
    $('#sub_addr').prop('disabled', false);
    $(this).hide();
    $('#save_sub_addr').show();
});

$('#save_sub_addr').click(function () {

    var new_sub_addr = $('#sub_addr').val().trim();
    var origin_sub_addr = $('#sub_addr').data('origin');

    var order_id = $('#pass_order').data('orderid');

    if (new_sub_addr != origin_sub_addr) {
        //submit new name

        $.ajax({
            type: "POST",
            url: API_URL + 'gongchang/daishenhedingdan/change_sub_addr',
            data: {
                'new_sub_addr': new_sub_addr,
                'order_id': order_id,
            },
            success: function (data) {

                if (data.status == "success") {
                    $('#save_sub_addr').hide();
                    $('#change_sub_addr').show();
                    $('#sub_addr').prop('disabled', true);
                } else {
                    if (data.message) {
                        show_warning_msg(data.message);
                        $('#sub_addr').val(origin_sub_addr);
                        $('#sub_addr').prop('disabled', true);
                    }
                }
            },
            error: function (data) {
                console.log(data);
            }
        })

    } else {
        $(this).hide();
        $('#change_sub_addr').show();
    }
});

