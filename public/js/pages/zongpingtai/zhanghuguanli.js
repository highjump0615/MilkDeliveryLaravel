/**
 * Created by Administrator on 16/12/8.
 */


var first = true;
$(document).ready(function () {

});

$('button[data-action = "show_selected"]').click(function () {

    var fid = $('#filter_factory').val();
    var sid = $('#filter_station').val();

    if ((fid == "none")) {
        if (!first) {
            location.reload();
        }
    } else if (fid != "none") {

        first = false;

        $('div.one_factory').each(function () {
            if ($(this).data('fid') == fid) {
                $(this).show();

                var otable = $(this).find('.order_table');
                var ftable = $(this).find('.filter_table');

                if(sid != "none")
                {
                    $(ftable).find('tbody').empty();

                    var tr = $(otable).find('tbody tr[data-sid="'+sid+'"]');
                    var trd = $(tr)[0].outerHTML;

                    var footable = $(ftable).data('footable');
                    footable.appendRow(trd);

                    $(otable).hide();
                    $(ftable).show();

                } else {
                    $(otable).show();
                    $(ftable).hide();
                }

            } else {
                $(this).hide();
            }
        });


    }


});

//According to factory, show station list
$('#filter_factory').on('change', function () {

    var factory_id = $(this).val();
    $('#factory_id').val(factory_id);

    var station_list = $('#filter_station');
    $(station_list).empty();

    if (factory_id == "none") {
        station_list.append('<option value="none">全部</option>');
        return;
    }

    var sendData = {'factory_id': factory_id};
    $.ajax({
        type: "POST",
        url: API_URL + 'factory_to_station',
        data: sendData,
        success: function (data) {
            console.log(data);
            var stations = data.stations;
            if (stations) {
                station_list.append('<option value="none">全部</option>');

                for (var i = 0; i < stations.length; i++) {
                    var sid = stations[i][0];
                    var sname = stations[i][1];

                    var option = '<option value="' + sid + '">' + sname + '</option>';
                    station_list.append(option);
                }

                $(station_list).trigger('change');
            }
        },
        error: function (data) {
            console.log(data);
        }
    })
});

$('button[data-action="show_selected"]').click(function () {

    var order_table = $('#order_table');

    var filter_table = $('#filter_table');
    var filter_table_tbody = $('#filter_table tbody');

    var f_factory = $('#filter_factory').val();
    var f_station = $('#filter_station').val();

    var f_start_date = $('#filter_start_date').val();
    var f_end_date = $('#filter_end_date').val();

    //show only rows in filtered table that contains the above field value
    var filter_rows = [];
    var i = 0;

    $('#order_table').find('tbody tr').each(function () {
        var tr = $(this);

        o_date = tr.find('td.o_date').html();
        o_factory = tr.find('td.o_factory_station').data('fid');
        o_station = tr.find('td.o_factory_station').data('sid');


        if (f_factory == "none" || f_factory == o_factory) {
            tr.attr('data-show-1', '1');
        } else {
            tr.attr('data-show-1', '0');
        }

        if (f_station == "none" || f_station == o_station) {
            tr.attr('data-show-2', '1');
        } else {
            tr.attr('data-show-2', '0');
        }


        if ((f_start_date == "" && f_end_date == "")) {
            tr.attr("data-show-3", "1");

        } else if (f_start_date == "" && f_end_date != "") {

            var f2 = new Date(f_end_date);
            var oo = new Date(o_date);
            if (oo <= f2) {
                tr.attr("data-show-3", "1");
            } else {
                tr.attr("data-show-3", "0");
            }

        } else if (f_start_date != "" && f_end_date == "") {

            var f1 = new Date(f_start_date);
            var oo = new Date(o_date);
            if (oo >= f1) {
                tr.attr("data-show-3", "1");
            } else {
                tr.attr("data-show-3", "0");
            }
        } else {
            //f_start_date, f_end_date, o_date
            var f1 = new Date(f_start_date);
            var f2 = new Date(f_end_date);
            var oo = new Date(o_date);
            if (f1 <= f2 && f1 <= oo && oo <= f2) {
                tr.attr("data-show-3", "1");

            } else if (f1 >= f2 && f1 >= oo && oo >= f2) {
                tr.attr("data-show-3", "1");

            } else {
                tr.attr("data-show-3", "0");
            }
        }

        if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1" ) && (tr.attr("data-show-3") == "1" )) {
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

    var od = $('#table_data').css('display');
    var fd = $('#table_filter').css('display');

    if (od != "none") {
        data_export('table_data', gnUserTypeAdmin, '财务管理', 0, 0);
    }
    else if (fd != "none") {
        data_export('table_filter', gnUserTypeAdmin, '财务管理', 0, 0);
    }
});

//Print Table Data
$('button[data-action = "print"]').click(function () {

    var od = $('#order_table').css('display');
    var fd = $('#filter_table').css('display');

    if (od != "none") {
        printContent('table_data', gnUserTypeAdmin, '财务管理');
    }
    else if (fd != "none") {
        printContent('table_filter', gnUserTypeAdmin, '财务管理');
    }
});

