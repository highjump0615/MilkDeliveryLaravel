/**
 * Created by Administrator on 3/25/17.
 */

var obj = $('#uecontent');
// 详细内容空间是否存在
if (obj.length > 0) {
    obj.html(contentDetail);

    $(obj).each(function () {
        var $this = $(this);
        var t = $this.text();
        $this.html(t.replace('&lt;', '<').replace('&gt;', '>'));
    });
}

// 选择数量(月单、季单、半年单)
$('select#order_type').change(function () {

    var nCount = $(this).find('option:selected').data('content');
    var count_input = $('#total_count');

    count_input.attr('min', nCount);
    count_input.val(nCount);
});

function dnsel_changed(id) {
    $(".dnsel_item").css("display", "none");
    $("#" + id).css("display", "block");
}

function pad(number){
    var r= String(number);
    if(r.length === 1){
        r= '0'+r;
    }
    return r;
}

function check_bottle_count() {
    var count_input = $('#total_count');
    var min_b = parseInt($(count_input).attr('min'));
    var current_b = $(count_input).val();
    if (current_b < min_b) {
        return true;
    }
    return false;
}

var able_date, default_start_date;

function initProductInfo() {
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        spaceBetween: 30
    });

    able_date = today;
    able_date.setDate(today.getDate() + gap_day);

    Date.prototype.toISOString = function(){
        return this.getUTCFullYear() + '-' + pad(this.getUTCMonth() +1) + '-'+pad(this.getUTCDate());
    };

    //set default day for start at
    default_start_date = able_date.toISOString();

    var strStartAtId = '#start_at';
    $(strStartAtId).val(default_start_date);
    $(strStartAtId).attr('min', default_start_date);

    dnsel_changed("dnsel_item0");
}

/**
 * 数量增加
 * @param objButton jquery Object
 */
function incrementCount(objButton) {
    objButton.prev().val(parseInt(objButton.prev().val()) + 1);
    if(parseInt(objButton.prev().val()) >1 )
    {
        objButton.parent().find('.minus').removeClass("minusDisable");
    }
}

/**
 * 数量减少
 * @param objButton
 */
function decrementCount(objButton) {
    if (parseInt(objButton.next().val()) > 1) {
        objButton.next().val(parseInt(objButton.next().val()) - 1);
        objButton.removeClass("minusDisable");
    }
    if (parseInt(objButton.next().val()) <= 1) {
        objButton.addClass("minusDisable");
    }
}

/**
 * 搭建基础提交数据
 * @returns {*}
 */
function makeBaseFormData() {
    var send_data = new FormData();

    var delivery_type = $('#delivery_type option:selected').data('value');
    send_data.append('delivery_type', delivery_type);

    var count = 0;
    var custom_date = "";

    // 天天送
    if (($('#dnsel_item0')).css('display') != "none") {
        count = $('#dnsel_item0 input').val();
        if (!count) {
            show_warning_msg('请填写产品的所有字段');
            return null;
        }
        send_data.append('count_per', count);
    }
    // 隔日送
    else if (($('#dnsel_item1')).css('display') != "none") {
        count = $('#dnsel_item1 input').val();
        if (!count) {
            show_warning_msg('请填写产品的所有字段');
            return null;
        }
        send_data.append('count_per', count);
    }
    // 按周送
    else if (($('#dnsel_item2')).css('display') != "none") {
        //week dates
        custom_date = week.get_submit_value();
        if (!custom_date) {
            show_warning_msg('请填写产品的所有字段');
            return null;
        }
        send_data.append('custom_date', custom_date);
    }
    // 随心送
    else {
        var strFreeOrderData = getFormattedFreeOrderData();

        if (strFreeOrderData.length == 0) {
            show_warning_msg('请填写产品的所有字段');
            return null;
        }

        send_data.append('custom_date', strFreeOrderData);
    }

    return send_data;
}


/**
 * 验证各项输入端
 * @returns {*}
 */
function makeFormData() {
    var send_data = makeBaseFormData();

    if (send_data != null) {
        //product_id
        var product_id = $('#product_id').val();
        send_data.append('product_id', product_id);

        //order_type
        var order_type = $('#order_type').val();
        send_data.append('order_type', order_type);
        //total_count
        var total_count = $('#total_count').val();
        send_data.append('total_count', total_count);

        var start_at = $('#start_at').val();
        if (!start_at) {
            show_warning_msg("请选择起送时间");
            return null;
        }

        var start_time = new Date(start_at);
        if(start_time < able_date)
        {
            show_warning_msg("选择"+default_start_date+"之后的日期.");
            return null;
        }

        send_data.append('start_at', start_at);
    }

    return send_data;
}
