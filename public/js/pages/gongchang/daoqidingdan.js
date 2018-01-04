
$(document).ready(function() {
});

$('#data_range_select .input-daterange').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true
});

$('button[data-action = "print"]').click(function () {
    printContent('table1', gnUserTypeFactory, '到期订单统计');
});

$('button[data-action = "export_csv"]').click(function () {
    data_export('table1', gnUserTypeStation, '奶品配送日统计', 0, 1);
});