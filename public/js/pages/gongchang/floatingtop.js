/**
 * Created by Administrator on 3/10/17.
 */

$(document).ready(function() {
    /**
     * 滚动事件
     */
    $(window).scroll(function() {
        var divParent = $('div.floating');
        var divContent = divParent.find('.ibox-content');

        // 挡住了内容
        if ($(this).scrollTop() > 180) {
            divParent.height(divContent.height());

            // 添加浮层
            divContent.addClass('fixedintop');
        }
        else {
            // 删除浮层
            divContent.removeClass('fixedintop');
            divParent.css('height', '');
        }
    });
});
