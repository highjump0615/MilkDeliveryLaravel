/**
 * Created by Administrator on 3/23/17.
 */

var week;

$(document).ready(function () {
    initProductInfo();

    // 初始化周日历
    week = new showmyweek2("week");
});

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
                    show_success_msg("添加产品成功");
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
        var order_id = $(this).data('order-id');
        send_data.append('order_id', order_id);
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
                        strResultUrl += "&type=" + type;
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

/**
 * 点击数量增加按钮
 */
$(".plus").click(function () {
    incrementCount($(this));
});

/**
 * 点击数量减少按钮
 */
$(".minus").click(function () {
    decrementCount($(this));
});