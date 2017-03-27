/**
 * Created by Administrator on 2/26/17.
 */

//Filter Function
$('button[data-action="show_selected"]').click(function () {

    var order_table = $('#order_table');
    var filter_table = $('#filter_table');
    var filter_table_tbody = $('#filter_table tbody');

    //get all selection
    var f_customer = $('#filter_customer').val().trim().toLowerCase();
    var f_phone = $('#filter_phone').val().trim().toLowerCase();
    var f_station = $('#filter_station').val();
    var f_delivery_property = $('#filter_delivery_property').val();
    var f_number = $('#filter_number').val().trim().toLowerCase();
    var f_checker = $('#filter_order_checker').val().trim();
    var f_term_kind = $('#filter_term_kind').val();
    var f_payment_type = $('#filter_payment_type').val();
    var f_order_start_date = $('#filter_order_start_date').val();
    var f_order_end_date = $('#filter_order_end_date').val();

    // 订单状态
    var f_status = $('#filter_status').val();

    // 到期日期
    var f_end_date = $('#end_date').val();

    //show only rows in filtered table that contains the above field value
    var filter_rows = [];
    var i = 0;

    $('#order_table').find('tbody tr').each(function () {
        var tr = $(this);

        o_customer_name = tr.find('td.o_customer_name').html().toString().toLowerCase();
        o_phone = tr.find('td.o_phone').html().toString().toLowerCase();
        o_station = tr.find('td.o_station').html().toString();
        o_property = tr.find('td.o_property').html().toString();

        o_number = tr.find('td.o_number').html().toString().toLowerCase();
        o_checker = tr.find('td.o_checker').html().toString().toLowerCase();
        o_type = tr.find('td.o_type').html().toString();
        o_paytype = tr.find('td.o_paytype').html().toString();
        o_ordered = tr.find('td.o_ordered').html().toString();

        // 到期日期
        o_end = tr.find('td.o_end').html();

        //status
        o_status = tr.find('td.o_status').data('status');

        //customer
        if ((f_customer != "" && o_customer_name.includes(f_customer)) || (f_customer == "")) {
            tr.attr("data-show-1", "1");
        } else {
            tr.attr("data-show-1", "0")
        }

        if ((f_phone != "" && o_phone.includes(f_phone)) || (f_phone == "")) {
            tr.attr("data-show-2", "1");
        } else {
            tr.attr("data-show-2", "0")
        }

        if ((f_station != "none" && o_station.includes(f_station)) || (f_station == "none")) {
            tr.attr("data-show-3", "1");
        } else {
            tr.attr("data-show-3", "0")
        }

        if ((f_delivery_property != "none" && o_property.includes(f_delivery_property)) || (f_delivery_property == "none")) {
            tr.attr("data-show-4", "1");
        } else {
            tr.attr("data-show-4", "0")
        }

        if ((f_number != "" && o_number.includes(f_number)) || (f_number == "")) {
            tr.attr("data-show-5", "1");
        } else {
            tr.attr("data-show-5", "0")
        }
        if ((f_checker != "" && o_checker.includes(f_checker)) || (f_checker == "")) {
            tr.attr("data-show-6", "1");
        } else {
            tr.attr("data-show-6", "0")
        }
        if ((f_term_kind != "none" && o_type.includes(f_term_kind)) || (f_term_kind == "none")) {
            tr.attr("data-show-7", "1");
        } else {
            tr.attr("data-show-7", "0")
        }
        if ((f_payment_type != "none" && o_paytype.includes(f_payment_type)) || (f_payment_type == "none")) {
            tr.attr("data-show-8", "1");
        } else {
            tr.attr("data-show-8", "0");
        }

        // 状态比较
        if ((typeof(f_status) == "undefined") ||
            (f_status != "none" && o_status == f_status) ||
            (f_status == "none")) {
            tr.attr("data-show-9", "1");
        } else {
            tr.attr("data-show-9", "0");
        }

        if ((f_order_start_date == "" && f_order_end_date == "") || (!o_ordered)) {
            tr.attr("data-show-10", "1");
        } else if (f_order_start_date == "" && f_order_end_date != "") {

            var f2 = new Date(f_order_end_date);
            var oo = new Date(o_ordered);
            if (oo <= f2) {
                tr.attr("data-show-10", "1");
            } else {
                tr.attr("data-show-10", "0");
            }

        } else if (f_order_start_date != "" && f_order_end_date == "") {

            var f1 = new Date(f_order_start_date);
            var oo = new Date(o_ordered);
            if (oo >= f1) {
                tr.attr("data-show-10", "1");
            } else {
                tr.attr("data-show-10", "0");
            }
        } else {
            //f_order_start_date, f_order_end_date, o_ordered
            var f1 = new Date(f_order_start_date);
            var f2 = new Date(f_order_end_date);
            var oo = new Date(o_ordered);
            if (f1 <= f2 && f1 <= oo && oo <= f2) {
                tr.attr("data-show-10", "1");

            } else if (f1 >= f2 && f1 >= oo && oo >= f2) {
                tr.attr("data-show-10", "1");

            } else {

                tr.attr("data-show-10", "0");
            }
        }

        // 到期日期比较
        if (typeof(f_end_date) == "undefined" ||
            o_end.includes(f_end_date)) {
            tr.attr("data-show-11", "1");
        } else {
            tr.attr("data-show-11", "0");
        }

        if ((tr.attr("data-show-1") == "1") &&
            (tr.attr("data-show-2") == "1") &&
            (tr.attr("data-show-3") == "1") &&
            (tr.attr("data-show-4") == "1") &&
            (tr.attr("data-show-5") == "1") &&
            (tr.attr("data-show-6") == "1") &&
            (tr.attr("data-show-7") == "1") &&
            (tr.attr("data-show-8") == "1") &&
            (tr.attr("data-show-9") == "1") &&
            (tr.attr("data-show-10") == "1") &&
            (tr.attr("data-show-11") == "1")) {

            //tr.removeClass('hide');

            filter_rows[i] = $(tr)[0].outerHTML;
            i++;
            //filter_rows += $(tr)[0].outerHTML;

        } else {
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

