/**
 * Created by Administrator on 16/12/9.
 */

$('#data_range_select .input-daterange').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true
});

$(document).on('click','#search',function () {
    var order_type = $('#order_type option:selected').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    window.location.href = SITE_URL+"milk/public/naizhan/tongji/dingdan/?order_type="+order_type+"&start_date="+start_date+"&end_date="+end_date+"";
});

$('button[data-action = "print"]').click(function () {
    printContent('table1', gnUserTypeStation, '订单剩余量统计');
});
