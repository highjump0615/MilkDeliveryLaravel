/**
 * Created by Administrator on 16/12/9.
 */

$(document).on('click','#search',function () {
    var factory_name = $('#factory_name').val();
    var factory_number = $('#factory_number').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    window.location.href = SITE_URL+"milk/public/zongpingtai/tongji/naipinpeisong/?factory_name="+factory_name+"&factory_number="+factory_number+"&start_date="+start_date+"&end_date="+end_date+"";
});

$(document).ready(function () {
    $('#total_balance tr:not(:first)').each(function () {
        $(this).find('td:eq(7)').html(parseInt($(this).find('td:eq(2)').text())+parseInt($(this).find('td:eq(3)').text())+
            parseInt($(this).find('td:eq(4)').text())+parseInt($(this).find('td:eq(5)').text())+parseInt($(this).find('td:eq(6)').text()));
    })
});

$('#data_range_select .input-daterange').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true
});

$('button[data-action = "print"]').click(function () {
    printContent('total_balance', gnUserTypeAdmin, '奶品配送统计');
});

$('button[data-action = "export_csv"]').click(function () {
    data_export('total_balance', gnUserTypeAdmin, '奶品配送统计', 0, 0);
});
