// jQuery url get parameters function [获取URL的GET参数值]
(function($) {
    $.extend({
        urlGet:function()
        {
            var aQuery = window.location.href.split("?");  //取得Get参数
            var aGET = new Array();
            if(aQuery.length > 1)
            {
                var aBuf = aQuery[1].split("&");
                for(var i=0, iLoop = aBuf.length; i<iLoop; i++)
                {
                    var aTmp = aBuf[i].split("=");  //分离key与Value
                    aGET[aTmp[0]] = aTmp[1];
                }
            }
            return aGET;
        }
    })
})(jQuery);

$(document).ready(function() {

    // 初始化日历
    $('.input-group.date').datepicker({
        autoclose: true,
        clearBtn: true
    });

    // 初始化分页控件
    $('#pagination_data').twbsPagination({
        totalPages: gnTotalPage,
        visiblePages: 5,
        startPage: gnCurrentPage,
        first: '<<',
        prev: '<',
        next: '>',
        last: '>>',
        onPageClick: function (event, page) {

            if (page == gnCurrentPage) {
                return;
            }

            //获取URL的Get参数
            var paramGet = $.urlGet();

            // 组合新的链接
            paramGet['page'] = page;

            var strGetUrl = '?';
            for (var prop in paramGet) {
                if (strGetUrl != '?' ) {
                    strGetUrl += '&';
                }
                strGetUrl += prop + '=' + paramGet[prop];
            }

            var aryUrl = window.location.href.split("?");
            var strUrl = aryUrl[0] + strGetUrl;

            window.location.href = strUrl;
        }
    });
});

/**
 * 导出
 */
$('button[data-action = "export_csv"]').click(function () {

    data_export('table_syslog', gnUserTypeAdmin, '后台日志', 0, 0);
});