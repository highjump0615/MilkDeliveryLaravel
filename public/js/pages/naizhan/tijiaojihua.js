/**
 * Created by Administrator on 3/11/17.
 */

/**
 * 初始化日期选择器
 */
$('.input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    calendarWeeks: false,
    autoclose: true,
    startDate: new Date(s_timeCurrent)
}).on('changeDate', function(e) {
    // 用新的日期刷新页面
    var strDate = $('#search_date').val();

    window.location.href = SITE_URL + "naizhan/shengchan/tijiaojihua?current_date=" + strDate;
});