/**
 * Created by Administrator on 16/12/9.
 */

$(document).ready(function () {
    $('.data_range_select .input-daterange').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true,
        clearBtn: true
    });

    $('#filter_factory').trigger('change');

});

//According to factory, show station list
$('#filter_factory').on('change', function () {

    var factory_id = $(this).val();
    $('#factory_id').val(factory_id);

    $('#show_history').attr('href', 'zhangwujiesuan/lishizhuanzhangjiru/' + factory_id);
    $('#show_todo').attr('href', 'zhangwujiesuan/zhangdanzhuanzhang/' + factory_id);
});

$('#filter_station').change(function () {

    var station_id = $(this).val();
    $('#station_id').val(station_id);
});

//Export
$('button[data-action = "export_csv"]').click(function () {

    var od = $('#order_table').css('display');
    var fd = $('#filter_table').css('display');

    if (od != "none") {
        data_export('order_table', gnUserTypeAdmin, '财务结算', 0, 0);
    }
    else if (fd != "none") {
        data_export('filter_table', gnUserTypeAdmin, '财务结算', 0, 0);
    }
});

//Print Table Data
$('button[data-action = "print"]').click(function () {

    var od = $('#order_table').css('display');
    var fd = $('#filter_table').css('display');

    if (od != "none") {
        printContent('order_table', gnUserTypeAdmin, '财务管理');
    }
    else if (fd != "none") {
        printContent('filter_table', gnUserTypeAdmin, '财务管理');
    }
});
