
$(document).ready(function () {
    // 显示modal事件
    $('#insert_order').on('hidden.bs.modal', function () {
        $(this).find('input').val('');
        $(this).find('textarea').val('');
    });
});

$('#insert_order_receipt_form').on('submit', function (e) {
    e.preventDefault();

    var submit = $(this).find('button[type="submit"]');

    // 禁止确定按钮，一遍防止两次点击
    $(submit).attr('disabled', 'disabled');

    var sendData = $('#insert_order_receipt_form').serializeArray();
    console.log(sendData);

    $.ajax({
        type: "POST",
        url: API_URL + "gongchang/caiwu/taizhang/insert_money_order_received",
        data: sendData,
        success: function (data) {
            console.log(data);
            if (data.status == "success") {
                //add amount to delivery credit balance input
                if (data.station_id && data.amount)
                    increase_delivery_credit_balance(data.station_id, data.amount);

                show_success_msg('收款成功');
                $('#insert_order').modal("hide");

            } else {
                if (data.message)
                    show_err_msg(data.message);
                $('#insert_order').modal("hide");
            }

            // 恢复确定按钮
            $(submit).removeAttr('disabled');
        },
        error: function (data) {
            console.log(data);
            show_err_msg("在插入订单信息，发生错误");
            $('#insert_order').modal("hide");

            // 恢复确定按钮
            $(submit).removeAttr('disabled');
        }
    })
});

function increase_delivery_credit_balance(station_id, amount) {
    var station = $('.station[data-sid="' + station_id + '"]')[0];
    var dcb = $(station).find('.delivery_credit_balance');
    var ccb = $(station).find('.credit_balance');
    var rcb = $(station).find('.receivable_order_money');

    var new_dcbal = parseFloat($(dcb).val()) + parseFloat(amount);
    $(dcb).val(new_dcbal);

    var new_ccbal = parseFloat($(ccb).val()) + parseFloat(amount);
    $(ccb).val(new_ccbal);

    var new_rcbal = parseFloat($(rcb).val()) - parseFloat(amount);
    $(rcb).val(new_rcbal);

    return;
}

$('[data-action="show_selected"]').click(function () {
    var current_station = $('.choose_station').val();

    $('.station').each(function () {
        if ($(this).data('sid') == current_station || current_station == "none") {
            $(this).show();
        }
        else {
            $(this).hide();
        }
    });
});


$('button[data-action = "print"]').click(function () {

    var printContents = $('#station_list')[0].innerHTML;

    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;

    window.print();
    document.body.innerHTML = originalContents;

});