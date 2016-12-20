$(document).ready(function () {
    $('#data_range_select .input-daterange').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true,
        calendarWeeks: false,
        clearBtn: true,
    });

    $('.single_date').datepicker({
        todayBtn: false,
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: false,
        autoclose: true,
        clearBtn:true,
        startDate: new Date(),
    });
});

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
    var f_number = $('#filter_number').val().trim();
    var f_checker = $('#filter_order_checker').val().trim();
    var f_term_kind = $('#filter_term_kind').val();
    var f_payment_type = $('#filter_payment_type').val();
    var f_order_start_date = $('#filter_order_start_date').val();
    var f_order_end_date = $('#filter_order_end_date').val();

    //show only rows in filtered table that contains the above field value
    var filter_rows = [];
    var i = 0;

    $('#order_table').find('tbody tr').each(function () {
        var tr = $(this);

        o_customer_name = tr.find('td.o_customer_name').html().toString().toLowerCase();
        o_phone = tr.find('td.o_phone').html().toString().toLowerCase();
        o_station = tr.find('td.o_station').html().toString().toLowerCase();
        o_property = tr.find('td.o_property').html().toString().toLowerCase();

        o_number = tr.find('td.o_number').html().toString().toLowerCase();
        o_checker = tr.find('td.o_checker').html().toString().toLowerCase();
        o_type = tr.find('td.o_type').html().toString().toLowerCase();
        o_paytype = tr.find('td.o_paytype').html().toString().toLowerCase();
        o_ordered = tr.find('td.o_ordered').html().toString().toLowerCase();

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

        if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1") && (tr.attr("data-show-3") == "1") && (tr.attr("data-show-4") == "1" ) && (tr.attr("data-show-5") == "1" ) && (tr.attr("data-show-6") == "1" ) && (tr.attr("data-show-7") == "1" ) && (tr.attr("data-show-8") == "1" ) && (tr.attr("data-show-10") == "1" )) {
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

//Export
$('button[data-action = "export_csv"]').click(function () {

    var od = $('#order_table').css('display');
    var fd = $('#filter_table').css('display');

    if (od != "none") {
        data_export('order_table', 0, '', 0, 0);
    }
    else if (fd != "none") {
        data_export('filter_table', 0, '', 0, 0);
    }
});

//Pring
$('button[data-action = "print"]').click(function () {

    var od = $('#order_table').css('display');
    var fd = $('#filter_table').css('display');

    if (od != "none") {
        printContent('order_table', 0, '');
    }
    else if (fd != "none") {
        printContent('filter_table', 0, '');
    }
});


/*
 *Redirect Rule
 *
 * Default: xiangqing
 * all page: daishenhe-> daishenhe-dingdanxiangqing
 *           others -> xianqing
 * daishenhe -> daishenhe-dingdanxiangqing
 * weitonggueo -> xiangqing
 * zaipeisong page -> xiangqing
 * xudan page:  -> xudan
 * weiqinai page-> xiangqing
 * zanting page: -> xiangqing
 * weitongguo page-> xiangqing
 *
 */

var url1 = SITE_URL + 'gongchang/dingdan/dingdanluru/xiangqing/';
var url2 = SITE_URL + 'gongchang/dingdan/xudanliebiao/xudan/';
var url3 = SITE_URL + 'gongchang/dingdan/daishenhedingdan/daishenhe-dingdanxiangqing/';

$('body').on('click', '#order_table tbody tr', function () {
    var order_id = $(this).data('orderid');

    var status = $(this).data('status');

    //weiqinai, zanting
    var url = url1 + order_id;

    if (at_page == 'daishenhe' || at_page == "quanbu" && (status == "1" || status == "8"))
    {
        url = url3+order_id;
    }
    else if( at_page == "xudan")
    {
        url =url2+order_id;
    }

    window.location.href = url;

});

$('body').on('click', ('#filter_table tbody tr'), function () {
    var order_id = $(this).data('orderid');

    var status = $(this).data('status');

    //weiqinai, zanting
    var url = url1 + order_id;

    if(at_page == 'daishenhe' || at_page == "quanbu" && status == "1")
    {
        url = url3+order_id;

    } else if( at_page == "xudan")
    {
        url =url2+order_id;
    }

    window.location.href = url;

});
