/**
 * Created by Administrator on 16/12/9.
 */

$(document).ready(function () {
});

$('#data_range_select .input-daterange').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true
});

$('button[data-action = "print"]').click(function () {
    printContent('table1', gnUserTypeFactory, '客户订单修改统计');
});
