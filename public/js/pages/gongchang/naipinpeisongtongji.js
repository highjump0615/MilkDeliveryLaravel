/**
 * Created by Administrator on 16/12/9.
 */

$(document).ready(function(){
});

$(document).on('click','#search',function () {
    var station_name = $('#station_name').val();
    var area_name = $('#area_name option:selected').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    window.location.href = SITE_URL+"milk/public/gongchang/tongjifenxi/naipinpeisongtongji/?station_name="+station_name+"&area_name="+area_name+"&start_date="+start_date+"&end_date="+end_date+"";
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
