
//Filter Function
$('button[data-action="show_selected"]').click(function () {

    var customer_table = $('#customerTable');
    var filter_table = $('#filteredTable');
    var filter_table_tbody = $('#filteredTable tbody');

    //get all selection
    var f_user = $('#user_name').val().trim().toLowerCase();
    var f_phone = $('#phone_number').val().trim().toLowerCase();

    //show only rows in filtered table that contains the above field value
    var filter_rows = [];
    var i = 0;

    $('#customerTable').find('tbody tr').each(function () {
        var tr = $(this);

        user_name = $(this).find('td.user').text().toLowerCase();
        phone_number = $(this).find('td.phone').text().toLowerCase();

        //customer
        if ((f_user != "" && user_name.includes(f_user)) || (f_user == "")) {
            tr.attr("data-show-1", "1");
        } else {
            tr.attr("data-show-1", "0")
        }

        if ((f_phone != "" && phone_number.includes(f_phone)) || (f_phone == "")) {
            tr.attr("data-show-2", "1");
        } else {
            tr.attr("data-show-2", "0")
        }

        if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1")) {
            //tr.removeClass('hide');
            $(tr).find('td:eq(0)').html(i+1);
            filter_rows[i] = $(tr)[0].outerHTML;
            i++;
            //filter_rows += $(tr)[0].outerHTML;

        } else {
            //tr.addClass('hide');
        }
    });

    $(customer_table).hide();
    $(filter_table_tbody).empty();

    var length = filter_rows.length;

    var footable = $('#filteredTable').data('footable');

    for (i = 0; i < length; i++) {
        var trd = filter_rows[i];
        footable.appendRow(trd);
    }

    $(filter_table).show();

});

$('button[data-action = "print"]').click(function () {

    var od = $('#customerTable').css('display');
    var fd = $('#filteredTable').css('display');

    var sendData = [];

    var printContents;
    if (od != "none") {
        //print order data
        printContents = document.getElementById("customerTable").outerHTML;

    } else if (fd != "none") {
        //print filter data
        printContents = document.getElementById("filteredTable").outerHTML;
    } else {
        return;
    }

    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;

    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
});

$('button[data-action = "export_csv"]').click(function () {

    var od = $('#customerTable').css('display');
    var fd = $('#filteredTable').css('display');

    var sendData = [];

    var i = 0;
    if (od != "none") {
        //send order data
        $('#customerTable thead tr').each(function () {
            var tr = $(this);
            var trdata = [];

            var j = 0;
            $(tr).find('th').each(function () {
                var td = $(this);
                var td_data = td.html().toString().trim();
                td_data =td_data.split("<");
                // if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
                //     td_data = "";
                trdata[j] = td_data[0];
                j++;
            });
            sendData[i] = trdata;
            i++;
        });

        $('#customerTable tbody tr').each(function () {
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

    } else if (fd != "none") {
        //send filter data
        $('#filteredTable thead tr').each(function () {
            var tr = $(this);
            var trdata = [];

            var j = 0;
            $(tr).find('th').each(function () {
                var td = $(this);
                var td_data = td.html().toString().trim();
                td_data =td_data.split("<");
                // if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
                //     td_data = "";
                trdata[j] = td_data[0];
                j++;
            });
            sendData[i] = trdata;
            i++;
        });

        $('#filteredTable tbody tr').each(function () {
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