/**
 * Created by Administrator on 16/12/9.
 */

$('#date_1 .input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    calendarWeeks: false,
    autoclose: true
});

$(document).ready(function(){
    $('tr').each(function(){
        var sum = 0;
        $(this).find('.plan_val').each(function(){
            var plan_val = $(this).text();
            if(!isNaN(plan_val)&& plan_val.length!==0){
                sum+=parseInt(plan_val);
            }
        });
        $('.total_sum',this).html(sum);
    })
});

$('#search').click(function () {
    var current_date = $('#search_date').val();
    window.location.href = SITE_URL+"milk/public/naizhan/shengchan/jihuaguanli/?current_date="+current_date+"";

});

$('.footable').footable();

$('button[data-action = "print"]').click(function () {
    printContent('table1', gnUserTypeStation, '计划管理');
});

$('button[data-action = "export_csv"]').click(function () {
    data_export('table1', gnUserTypeStation, '计划管理', 0, 1);
});
