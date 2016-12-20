
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
    printContent('view_table', gnUserTypeStation, '瓶框统计');
});

$('button[data-action = "export_csv"]').click(function () {
    data_export('view_table', gnUserTypeStation, '瓶框统计', 0, 1);
});

$(document).ready(function () {
    $('#view_table tr:not(:first,:last)').each(function () {
        var init = 0;
        var milkman = 0;
        var factory = 0;
        var recipient = 0;
        var damaged = 0;
        if(!isNaN(parseInt($(this).find('.init_val').text()))){
            init = parseInt($(this).find('.init_val').text());
        }
        if(!isNaN(parseInt($(this).find('.milkman').text()))){
            milkman = parseInt($(this).find('.milkman').text());
        }
        if(!isNaN(parseInt($(this).find('.factory').text()))){
            factory = parseInt($(this).find('.factory').text());
        }
        if(!isNaN(parseInt($(this).find('.received').text()))){
            recipient = parseInt($(this).find('.received').text());
        }
        if(!isNaN(parseInt($(this).find('.damage').text()))){
            damaged = parseInt($(this).find('.damage').text());
        }

        var total = init + milkman + recipient - factory - damaged;
        $(this).find('.total').html(total);
    })
});