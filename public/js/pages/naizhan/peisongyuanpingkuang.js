/**
 * Created by Administrator on 16/12/9.
 */

$('#data_range_select .input-daterange').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true
});

$(document).on('click','#find',function () {
    var milkman_id = $('#milkman option:selected').val();
    var start_date = $('#start').val();
    var end_date = $('#end').val();
    window.location.href = SITE_URL+"milk/public/naizhan/pingkuang/peisongyuanpingkuang/?milkman_id="+milkman_id+"&start_date="+start_date+"&end_date="+end_date+"";
});

$('button[data-action = "print"]').click(function () {
    printContent('table1', gnUserTypeStation, '配送员瓶框回收记录');
});

$('button[data-action = "export_csv"]').click(function () {
    data_export('table1', gnUserTypeStation, '配送员瓶框回收记录', 0, 1);
});
