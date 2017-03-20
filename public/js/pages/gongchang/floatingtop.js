/**
 * Created by Administrator on 3/10/17.
 */

$(document).ready(function() {
    var divParent = $('div.floating');
    var divContent = divParent.find('.ibox-content');
    var nTop = divContent.position().top;

    /**
     * 滚动事件
     */
    $(window).scroll(function() {

        // 挡住了内容
        if ($(this).scrollTop() > nTop) {
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
