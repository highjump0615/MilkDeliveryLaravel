
$(document).ready(function() {

    // 初始化日历
    $('.input-group.date').datepicker({
        autoclose: true,
        clearBtn: true
    });

});

/**
 * 导出
 */
$('button[data-action = "export_csv"]').click(function () {

    data_export('table_syslog', gnUserTypeAdmin, '后台日志', 0, 0);
});