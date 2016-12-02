var date = new Date();
firstm = new Date(date.getFullYear(), date.getMonth(), 1);
lastm = new Date(date.getFullYear(), date.getMonth() + 1, 0);

$(document).ready(function () {
    $('.input-daterange').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true,
        clearBtn: true,
        startDate: firstm,
        endDate: lastm,
    });
});

//show selected
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
        o_date = tr.data('trstime');

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

        if (tr.attr("data-show-1") == "1" ) {
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

    var sendData = [];

    var i = 0;
    if (od != "none") {
        //send order data
        $('#order_table thead tr').each(function () {
            var tr = $(this);
            var trdata = [];

            var j = 0;
            $(tr).find('th').each(function () {
                var td = $(this);
                var td_data = td.html().toString().trim();
                trdata[j] = td_data;
                j++;
            });
            sendData[i] = trdata;
            i++;
        });

        $('#order_table tbody tr').each(function () {
            var tr = $(this);
            var trdata = [];

            var j = 0;
            $(tr).find('td').each(function () {
                var td = $(this);
                var td_data = td.html().toString().trim();
                if (td_data.includes('button') || td_data.includes('href'))
                    td_data = "";
                trdata[j] = td_data;
                j++;
            });
            sendData[i] = trdata;
            i++;
        });


    } else if (fd != "none") {
        //send filter data
        $('#filter_table thead tr').each(function () {
            var tr = $(this);
            var trdata = [];

            var j = 0;
            $(tr).find('th').each(function () {
                var td = $(this);
                var td_data = td.html().toString().trim();
                if (td_data.includes('button') || td_data.includes('href'))
                    td_data = "";
                trdata[j] = td_data;
                j++;
            });
            sendData[i] = trdata;
            i++;
        });

        $('#filter_table tbody tr').each(function () {
            var tr = $(this);
            var trdata = [];

            var j = 0;
            $(tr).find('td').each(function () {
                var td = $(this);
                var td_data = td.html().toString().trim();
                if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
                    td_data = "";

                trdata[j] = td_data;
                j++;
            });
            sendData[i] = trdata;
            i++;
        });

    } else {
        return;
    }

    var send_data = {"data": sendData};
    console.log(send_data);

    $.ajax({
        type: 'POST',
        url: API_URL + "export",
        data: send_data,
        success: function (data) {
            console.log(data);
            if (data.status == 'success') {
                var path = data.path;
                location.href = path;
            }
        },
        error: function (data) {
            //console.log(data);
        }
    })
});

//Print Table Data
$('button[data-action = "print"]').click(function () {

    var od = $('#order_table').css('display');
    var fd = $('#filter_table').css('display');
    var sendData = [];
    var printContents;
    if (od != "none") {
        //print order data
        printContents = document.getElementById("order_table").outerHTML;
    } else if (fd != "none") {
        //print filter data
        printContents = document.getElementById("filter_table").outerHTML;
    } else {
        return;
    }
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
});
