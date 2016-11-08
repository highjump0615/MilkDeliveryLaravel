
//Filter Function
$('button[data-action="show_selected"]').click(function () {
    
    var view_table = $('#view_table');
    var filter_table = $('#filter_table');
    var filter_table_tbody = $('#filter_table tbody');

    //get all selection
    var f_month = $('#month').val().trim().toLowerCase();

    //show only rows in filtered table that contains the above field value
    var filter_rows = [];
    var i = 0;

    $('#view_table').find('tbody tr').each(function () {
        var tr = $(this);

        month_val = $(this).attr('value').toLowerCase();

        //customer
        if ((f_month != "" && month_val==f_month) || (f_month == "")) {
            tr.attr("data-show-1", "1");
        } else {
            tr.attr("data-show-1", "0")
        }

        if (tr.attr("data-show-1") == "1" ) {
            //tr.removeClass('hide');
            filter_rows[i] = $(tr)[0].outerHTML;
            i++;
            //filter_rows += $(tr)[0].outerHTML;

        } else {
            //tr.addClass('hide');
        }
    });

    $(view_table).hide();
    $(filter_table_tbody).empty();

    var length = filter_rows.length;

    var footable = $('#filter_table').data('footable');

    for (i = 0; i < length; i++) {
        var trd = filter_rows[i];
        footable.appendRow(trd);
    }

    $(filter_table).show();

});

$('button[data-action = "print"]').click(function () {

    var sendData = [];

    var printContents;

    printContents = document.getElementById("view_table").outerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;

    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
});

$('button[data-action = "export_csv"]').click(function () {

    var sendData = [];

    var i = 0;
    //send order data
    $('#view_table thead tr').each(function () {
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

    $('#view_table tbody tr').each(function () {
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