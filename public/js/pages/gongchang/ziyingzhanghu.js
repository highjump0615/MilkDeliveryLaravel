/**
 * Created by Administrator on 16/12/9.
 */

var date = new Date();
firstm = new Date(date.getFullYear(), date.getMonth(), 1);
lastm = new Date(date.getFullYear(), date.getMonth() + 1, 0);

$('#insert_io_type').change(function () {
    if ($(this).val() == "{{\App\Model\FinanceModel\DSBusinessCreditBalanceHistory::DSBCBH_OUT}}") {
        $('#insert_receipt_number').prop("disabled", true);
    }
    else {
        $('#insert_receipt_number').prop("disabled", false);
    }
});

$(document).ready(function () {
    $('#data_range_select .input-daterange').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true,
        clearBtn: true,
        startDate: firstm,
        endDate: lastm
    });
});

$('#self_business_history_form').on('submit', function (e) {
    e.preventDefault();

    var sendData = $('#self_business_history_form').serializeArray();
    console.log(sendData);

    $.ajax({
        type: "GET",
        url: API_URL + "gongchang/caiwu/ziyingzhanghu/add_self_business_history",
        data: sendData,
        success: function (data) {
            console.log(data);
            if (data.status == "success") {
                $('#self_business_modal').modal("hide");
                location.reload();
            } else {
                alert(data.message);
                return;
            }
        },
        error: function (data) {
            console.log(data);
            alert(data.message);
        }
    })
});

//Show selected
$('button[data-action="show_selected"]').click(function () {

    var order_table = $('#order_table');
    var filter_table = $('#filter_table');
    var filter_table_tbody = $('#filter_table tbody');

    var f_start_date = $('#filter_start_date').val();
    var f_end_date = $('#filter_end_date').val();

    //show only rows in filtered table that contains the above field value
    var filter_rows = [];
    var i = 0;

    $('#order_table').find('tbody tr').each(function () {
        var tr = $(this);

        o_date = tr.find('td.o_date').html();

        if ((f_start_date == "" && f_end_date == "")) {
            tr.attr("data-show-1", "1");

        } else if (f_start_date == "" && f_end_date != "") {

            var f2 = new Date(f_end_date);
            var oo = new Date(o_date);
            if (oo <= f2) {
                tr.attr("data-show-1", "1");
            } else {
                tr.attr("data-show-1", "0");
            }

        } else if (f_start_date != "" && f_end_date == "") {

            var f1 = new Date(f_start_date);
            var oo = new Date(o_date);
            if (oo >= f1) {
                tr.attr("data-show-1", "1");
            } else {
                tr.attr("data-show-1", "0");
            }
        } else {
            //f_start_date, f_end_date, o_date
            var f1 = new Date(f_start_date);
            var f2 = new Date(f_end_date);
            var oo = new Date(o_date);
            if (f1 <= f2 && f1 <= oo && oo <= f2) {
                tr.attr("data-show-1", "1");

            } else if (f1 >= f2 && f1 >= oo && oo >= f2) {
                tr.attr("data-show-1", "1");

            } else {
                tr.attr("data-show-1", "0");
            }
        }

        if ((tr.attr("data-show-1") == "1" )) {
            //tr.removeClass('hide');
            filter_rows[i] = $(tr)[0].outerHTML;
            i++;
            //filter_rows += $(tr)[0].outerHTML;
        }
        else {
            //tr.addClass('hide');
        }
    });

    $(order_table).hide();
    $(filter_table_tbody).empty();

    var length = filter_rows.length;

    var footable = $('#filter_table').data('footable');

    for (i = 0; i < length; i++) {
        var trd = filter_rows[i];
        footable.appendRow(trd);
    }
    $(filter_table).show();
});

//Export
$('button[data-action = "export_csv"]').click(function () {

    var od = $('#order_table').css('display');
    var fd = $('#filter_table').css('display');

    if (od != "none") {
        data_export('order_table', gnUserTypeStation, '自营账户记录', 0, 0);
    }
    else if (fd != "none") {
        data_export('filter_table', gnUserTypeStation, '自营账户记录', 0, 0);
    }
});

//Print Table Data
$('button[data-action = "print"]').click(function () {

    var od = $('#order_table').css('display');
    var fd = $('#filter_table').css('display');

    if (od != "none") {
        printContent('order_table', gnUserTypeStation, '自营账户记录');
    }
    else if (fd != "none") {
        printContent('filter_table', gnUserTypeStation, '自营账户记录');
    }
});
