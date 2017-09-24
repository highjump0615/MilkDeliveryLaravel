/**
 * Created by Administrator on 16/12/9.
 */

$(document).ready(function(){
});

$('.footable').footable();

$('#data_range_select .input-daterange').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true
});

$('button[data-action = "print"]').click(function () {
    printContent('table1', gnUserTypeFactory, '奶品配送统计');
});

$('button[data-action = "export_csv"]').click(function () {
    data_export('table1', gnUserTypeFactory, '奶品配送统计', 1, 4);
});
