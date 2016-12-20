
$(document).ready(function() {
    $('#order_type_table tr.milk').each(function () {
        var f_total = 0;
        var f_remain = 0;
        $(this).find('.total').each(function () {
            f_total += parseInt($(this).text());
        });
        $(this).find('.remain').each(function () {
            f_remain += parseInt($(this).text());
        });
        $(this).find('.f_total').html(f_total);
        $(this).find('.f_remain').html(f_remain);
    });

    $('#order_type_table tr.milk_amount').each(function () {
        var f_total = 0;
        var f_remain = 0;
        $(this).find('.total').each(function () {
            f_total += parseFloat($(this).text());
        });
        $(this).find('.remain').each(function () {
            f_remain += parseFloat($(this).text());
        });
        $(this).find('.f_total').html(f_total.toFixed(2));
        $(this).find('.f_remain').html(f_remain.toFixed(2));
    });
    $('.footable').footable();
});

$(document).on('click','#search',function () {
    var station_name = $('#station_name').val();
    var area_name = $('#area_name option:selected').val();
    var end_date = $('#end_date').val();
    window.location.href = SITE_URL+"milk/public/gongchang/tongjifenxi/dingdanshengyuliangtongji/?station_name="+station_name+"&area_name="+area_name+"&end_date="+end_date+"";
})

$('#date_1 .input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    calendarWeeks: false,
    autoclose: true
});

$('#data_range_select .input-daterange').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true
});

$('button[data-action = "print"]').click(function () {
    printContent('order_type_table', gnUserTypeFactory, '订单剩余量统计');
});

$('button[data-action = "export_csv"]').click(function () {
    data_export('order_type_table', gnUserTypeFactory, '订单剩余量统计', 2, 5);
});