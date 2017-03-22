/**
 * Created by Administrator on 2/27/17.
 */

/**
 * 初始化日期选择器
 */
$('.input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    calendarWeeks: false,
    autoclose: true
}).on('changeDate', function(e) {
    // 用新的日期刷新页面
    var strDate = $('#search_date').val();

    window.location.href = SITE_URL + "naizhan/shengchan/qianshoujihua?current_date=" + strDate;
});