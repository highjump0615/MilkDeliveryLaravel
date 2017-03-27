/**
 * Created by Administrator on 3/26/17.
 */

var calen, week;

$(function () {
    week = new showmyweek2("week");

    dnsel_changed("dnsel_item0");
    init_wechat_order_product(null);

});

function set_original_product()
{
    var origin_img_url = logo_base_url + '/' + gstrCurProductPhotoUrl;
    $('#pimg').attr('src', origin_img_url);
    $('#pname').html(gstrCurProductName);
    $('#product_count').html(gstrCurProductCount);
    $('#changed_product_count').val(gstrCurProductCount).attr('max', gstrCurProductCount);
    $('#product_price').html(gstrCurProductPrice);

    $('#after_changed_amount').html(gstrCurProductAmount);
    $('#left_amount').html(gstrCurProductRemainAmount);
}

//show current selected or origin product info above
function reset_product_above() {
    if(current_product_id == selected_product_id)
    {
        //show origin product
        set_original_product();
    } else {
        //show selected new product
        var current_p = products[selected_product_id];
        var new_img_url = logo_base_url + '/' + current_p[2];
        $('#pimg').attr('src', new_img_url);
        $('#pname').html(current_p[1]);
        $('#product_count').html(current_p[4]);
        $('#changed_product_count').val(current_p[4]);
        $('#changed_product_count').attr('max', current_p[4]);
        $('#product_price').html(current_p[3]);

        //order product amount after changed
        var after_changed_amount = parseFloat(current_p[4]) * parseFloat(current_p[3]);
        $('#after_changed_amount').html(after_changed_amount.toFixed(2));

        var left_amount = current_product_amount + current_order_remain_amount - after_changed_amount;
        $('#left_amount').html(left_amount.toFixed(2));
    }

}

function deselect_all() {
    $('.cart_check').each(function () {
        $(this).prop('checked', false);
        $(this).parent().parent().find('.spp3').css('visibility', "hidden");
    });

    selected_product_id = current_product_id;
}

var check_count = 0;
//select other product
$('.cart_check').click(function () {
    if ($(this).prop('checked')) {
        if (check_count) {
            //deselect all
            deselect_all();

            //select this
            $(this).prop('checked', true);
            check_count++;

        } else {
            //choose new
            check_count++;
        }

        //show beside bottle count
        $(this).parent().parent().find('.spp3').css('visibility', "visible");

        selected_product_id = $(this).data('id');
        //show selected product info
        reset_product_above();

    } else {
        //no choose
        check_count--;
        $(this).parent().parent().find('.spp3').css('visibility', "hidden");

        selected_product_id = current_product_id;
        reset_product_above();
    }
});

//change order product count

function reset_order_info(){
    var pcount = $('#changed_product_count').val();
    var price = parseFloat($('#product_price').html());

    var after_changed_amount = parseFloat(pcount * price).toFixed(2);
    $('#after_changed_amount').html(after_changed_amount);

    var left_amount = current_product_amount + current_order_remain_amount - after_changed_amount;
    $('#left_amount').html(left_amount.toFixed(2));
}

$('#changed_product_count').change(function(){
    reset_order_info();
});

//cancel change of order product
$('#cancel_change_order_product').click(function () {
    //return to dingdanxiugai page
    history.back();
});

//change order product
$('#change_order_product').click(function () {

    //check left amount
//            if( parseFloat($('#left_amount').html()) < 0 )
//            {
//                show_err_msg('更改后金额不能超过订单余额');
//                return;
//            }

    var send_data = makeBaseFormData();

    if (send_data != null) {
        //order_id
        send_data.append('order_id', order_id);

        //origin order product id
        send_data.append('index', index);

        //current origin product id
        send_data.append('current_product_id', current_product_id);

        //current selected product id
        send_data.append('new_product_id', selected_product_id);

        var product_count = $('#changed_product_count').val();
        send_data.append('product_count', product_count);

        var product_amount = parseFloat($('#after_changed_amount').html()).toFixed(2);
        send_data.append('product_amount', product_amount);

        var product_price = parseFloat($('#product_price').html()).toFixed(2);
        send_data.append('product_price', product_price);
    }

    $.ajax({
        type: "POST",
        url: SITE_URL + "weixin/api/change_temp_order_product",
        data: send_data,
        processData: false,
        contentType: false,
        success: function (data) {
            if (data.status == "success") {
                show_success_msg("更改奶品成功");

                //go to dingdan xiangqing
                var strResultUrl = SITE_URL + "weixin/dingdanxiugai?order=" + order_id;

                if (type.length) {
                    strResultUrl += "&type=" + type;
                }

                window.location.href = strResultUrl;
            } else {
                if (data.message) {
                    show_warning_msg(data.message);
                }
            }
        },
        error: function (data) {
            console.log(data);
            show_warning_msg("附加产品失败");
        }
    });

});

/**
 * 点击数量增加按钮
 */
$(".plus").click(function () {
    incrementCount($(this));
    reset_order_info();
});

/**
 * 点击数量减少按钮
 */
$(".minus").click(function () {
    decrementCount($(this));
    reset_order_info();
});