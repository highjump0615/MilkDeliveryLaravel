/**
 * Created by Administrator on 3/26/17.
 */

/**
 * 初始化订单奶品信息
 * @param fnFreeOrderDayCount 随心送订购天数更新函数
 */
function init_wechat_order_product(fnFreeOrderDayCount)
{
    var total_count = parseInt($('#total_count').val());

    var strDeliveryType = '#delivery_type';
    $(strDeliveryType).find('option[data-value="' + gnDeliveryType + '"]').prop('selected', true);
    $(strDeliveryType).trigger('change');

    if (gnDeliveryType == gnDeliveryTypeWeek) {
        //show custom bottle count on week
        week.custom_dates = $('#custom_order_dates').val();
        week.set_custom_date();
    }
    else if (gnDeliveryType == gnDeliveryTypeFree){
        initBottleCount($('#custom_order_dates').val(), fnFreeOrderDayCount);
    }
}
