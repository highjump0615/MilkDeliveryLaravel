
//Filter Function
$('button[data-action="show_selected"]').click(function () {

    var customer_table = $('#customerTable');
    var filter_table = $('#filteredTable');
    var filter_table_tbody = $('#filteredTable tbody');

    //get all selection
    var f_user = $('#user_name').val().trim().toLowerCase();
    var f_phone = $('#phone_number').val().trim().toLowerCase();
    var f_area = $('#area_address').val().trim().toLowerCase();

    //show only rows in filtered table that contains the above field value
    var filter_rows = [];
    var i = 0;

    $('#customerTable').find('tbody tr').each(function () {
        var tr = $(this);

        user_name = $(this).find('td.user').text().toLowerCase();
        phone_number = $(this).find('td.phone').text().toLowerCase();
        area_address = $(this).find('td.area').text().toLowerCase();

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

        if ((f_area != "" && area_address.includes(f_area)) || (f_area == "")) {
            tr.attr("data-show-3", "1");
        } else {
            tr.attr("data-show-3", "0")
        }

        if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1") && (tr.attr("data-show-3") == "1")) {
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

    if (od != "none") {
        printContent('customerTable', gnUserTypeStation, '客户管理');
    }
    else if (fd != "none") {
        printContent('filteredTable', gnUserTypeStation, '客户管理');
    }
});

$('button[data-action = "export_csv"]').click(function () {

    var od = $('#customerTable').css('display');
    var fd = $('#filteredTable').css('display');

    if (od != "none") {
        data_export('customerTable', gnUserTypeStation, '客户管理', 0, 0);
    }
    else if (fd != "none") {
        data_export('filteredTable', gnUserTypeStation, '客户管理', 0, 0);
    }
});