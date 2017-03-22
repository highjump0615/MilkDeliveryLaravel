/**
 * Created by Administrator on 3/23/17.
 */

var week;

var obj = $('#uecontent');

$(obj).each(function () {
    var $this = $(this);
    var t = $this.text();
    $this.html(t.replace('&lt;', '<').replace('&gt;', '>'));
});

$(function () {
    week = new myweek("week");
    dnsel_changed("dnsel_item0");
});

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


var able_date, default_start_date;

$(document).ready(function () {
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

    $('#start_at').val(default_start_date);
    $('#start_at').attr('min', default_start_date);

    $('select#order_type').trigger('change');
});

function check_bottle_count() {
    var count_input = $('#total_count');
    var min_b = parseInt($(count_input).attr('min'));
    var current_b = $(count_input).val();
    if (current_b < min_b) {
        return true;
    }
    return false;
}

/**
 * 验证各项输入端
 * @returns {*}
 */
function makeFormData() {
    var send_data = new FormData();

    //product_id
    var product_id = $('#product_id').val();
    send_data.append('product_id', product_id);

    //order_type
    var order_type = $('#order_type').val();
    send_data.append('order_type', order_type);
    //total_count
    var total_count = $('#total_count').val();
    send_data.append('total_count', total_count);

    var delivery_type = $('#delivery_type option:selected').data('value');
    send_data.append('delivery_type', delivery_type);

    var count = 0;
    var custom_date = "";
    if (($('#dnsel_item0')).css('display') != "none") {
        count = $('#dnsel_item0 input').val();
        if (!count) {
            show_warning_msg('请填写产品的所有字段');
            return null;
        }
        send_data.append('count_per', count);

    }
    else if (($('#dnsel_item1')).css('display') != "none") {
        count = $('#dnsel_item1 input').val();
        if (!count) {
            show_warning_msg('请填写产品的所有字段');
            return null;
        }
        send_data.append('count_per', count);

    }
    else if (($('#dnsel_item2')).css('display') != "none") {
        //week dates
        custom_date = week.get_submit_value();
        if (!custom_date) {
            show_warning_msg('请填写产品的所有字段');
            return null;
        }
        send_data.append('custom_date', custom_date);

    }
    else {
        //month dates
        custom_date = calen.get_submit_value();
        if (!custom_date) {
            show_warning_msg('请填写产品的所有字段');
            return null;
        }
        send_data.append('custom_date', custom_date);
    }

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

    return send_data;
}

$('button#make_order').click(function () {

    if (check_bottle_count()) {
        show_info_msg('请正确设置订奶数量');
        return;
    }

    var send_data = makeFormData();
    if (send_data != null) {
        console.log(send_data);

        $.ajax({
            type: "POST",
            url: SITE_URL + "weixin/api/make_order_directly",
            data: send_data,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.status == "success") {
                    window.location.href = SITE_URL + "weixin/querendingdan";
                } else {
                    if (data.redirect_path == "phone_verify") {
                        window.location.href = SITE_URL + "weixin/dengji";
                    }
                }
            },
            error: function (data) {
                console.log(data);
                show_warning_msg("附加产品失败");
            }
        });
    }
});

$('button#submit_order').click(function (e) {

    e.preventDefault();

    var send_data = makeFormData();
    if (send_data != null) {
        console.log(send_data);

        $.ajax({
            type: "POST",
            url: SITE_URL + "weixin/api/insert_order_item_to_cart",
            data: send_data,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.status == "success") {
                    show_success_msg("附加产品成功");
                    //go to shanpin liebiao
                    window.location.href = SITE_URL + "weixin/shangpinliebiao";
                }
            },
            error: function (data) {
                console.log(data);
                show_warning_msg("附加产品失败");
            }
        });
    }
});

$('button#add_order').click(function () {

//            if (check_bottle_count()) {
//                show_info_msg('请正确设置订奶数量');
//                return;
//            }

    var send_data = makeFormData();
    if (send_data != null) {
        console.log(send_data);

        $.ajax({
            type: "POST",
            url: SITE_URL + "weixin/api/add_product_to_order_for_xiugai",
            data: send_data,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.status == "success") {
                    var strResultUrl = SITE_URL + "weixin/dingdanxiugai?order=" + order_id;

                    if (type.length) {
                        strResultUrl += "type=" + type;
                    }

                    window.location.href = strResultUrl;
                }
                else {
                    show_warning_msg("添加产品失败");
                }
            },
            error: function (data) {
                console.log(data);
                show_warning_msg("添加产品失败");
            }
        });
    }
});

$(".plus").click(function () {
    $(this).prev().val(parseInt($(this).prev().val()) + 1);
    if(parseInt($(this).prev().val()) >1 )
    {
        $(this).parent().find('.minus').removeClass("minusDisable");
    }
});
$(".minus").click(function () {
    if (parseInt($(this).next().val()) > 1) {
        $(this).next().val(parseInt($(this).next().val()) - 1);
        $(this).removeClass("minusDisable");
    }
    if (parseInt($(this).next().val()) <= 1) {
        $(this).addClass("minusDisable");
    }
});
