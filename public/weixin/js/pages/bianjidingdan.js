/**
 * Created by Administrator on 3/25/17.
 */

var week;

$(document).ready(function () {
    initProductInfo();

    // 初始化周日历
    week = new showmyweek2("week", change_order_day_num);

    // 初始化订单奶品数据
    init_wechat_order_product();
});

/**
 * 计算订购天数
 */
function change_order_day_num()
{
    //get total count
    var total_count = parseInt($('#total_count').val());

    var delivery_type = $('#delivery_type option:selected').data('value');

    var order_day_num=0;

    if (delivery_type == gnDeliveryTypeEveryDay)
    {
        var count_per = parseInt($('#dnsel_item0 .deliver_count_per_day').val());
        order_day_num = Math.ceil(total_count/count_per, 1);

    } else if ( delivery_type == gnDeliveryTypeTwice)
    {
        var count_per = parseInt($('#dnsel_item1 .deliver_count_per_day').val());
        order_day_num = Math.round(total_count/count_per, 1);

    } else if ( delivery_type == gnDeliveryTypeWeek)
    {
        //show custom bottle count on week
        var custom_date = week.get_submit_value();
        if(!custom_date || custom_date=="" || custom_date==undefined)
            order_day_num="";
        else
            order_day_num = get_order_days_from_week(total_count, custom_date);

    } else {
        return;
    }

    updateOrderDateCount(order_day_num)
}

/**
 * 更新订购天数数量
 * @param value
 */
function updateOrderDateCount(value) {
    $('#order_day_num').text(value);
}

function get_order_days_from_week( total_count, custom_date){

    var order_dates = 0;

    custom_date = custom_date.slice(0,-1);
    var custom_array = custom_date.split(',');
    var value_array = [];
    for(var i = 0 ; i <custom_array.length; i++ )
    {
        var one_arr = custom_array[i].split(':');
        var index =one_arr[0];
        if(index == "0")
            index = 7;
        value_array [index] = one_arr[1];
    }
    //set start_date based on start_at day of week
    var start_at = $('#start_at').val();
    var start_day =  new Date(start_at).getDay();
    if (start_day == 0)
    {
        start_day = 7;
    }

    i = get_available_week_index( value_array, start_day);

    do
    {
        if(i > 7)
        {
            i = 1;
        }

        i = get_available_week_index(value_array, i);
        var value = value_array[i];
        total_count -= value;
        order_dates ++;
        i++;

    }while(total_count>0)

    return order_dates;
}

/**
 * 获取随心送的订购天数
 * @param dayCount
 * @returns {*}
 */
function get_order_days_from_calendar(dayCount){
    updateOrderDateCount(dayCount);
}

function get_available_week_index(array, start)
{
    if(array.hasOwnProperty(start))
        return start;
    else {
        do{
            start++;
            if(start>7)
            {
                start  = 0;
            }

        }while(! array.hasOwnProperty(start));

        return start;
    }
}

function init_wechat_order_product()
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
        initBottleCount($('#custom_order_dates').val(), get_order_days_from_calendar);
    }
}

$('button#cancel').click(function(){
    if(previous == "queren")
    {
        var group_id = $('#group_id').val();
        window.location.href = SITE_URL + "weixin/querendingdan?group_id="+group_id;
    } else {
        window.location.href = SITE_URL + "weixin/qouwuche";
    }
});

/**
 * 点击保存按钮
 */
$('button#submit_order').click(function (e) {

    e.preventDefault();

    var send_data = makeFormData();
    if (send_data != null) {
        //wechat order product id
        var wechat_order_product_id = $('#wechat_order_product_id').val();
        send_data.append('wechat_order_product_id', wechat_order_product_id);

        if (previous == "queren")  {
            var group_id = $('#group_id').val();
        }

        $.ajax({
            type: "POST",
            url: SITE_URL + "weixin/bianjidingdan/save_changed_order_item",
            data: send_data,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.status == "success") {
                    show_success_msg("变化产品成功");

                    if(previous == "queren")
                    {
                        //go to shanpin qurendingdan
                        window.location.href = SITE_URL + "weixin/querendingdan?group_id="+group_id;
                    } else {
                        window.location.href = SITE_URL + "weixin/gouwuche";
                    }

                } else
                {
                    if(data.message)
                    {
                        show_warning_msg(data.message);
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

//calculate order days for this products
$('input#total_count').change(function(){

    change_order_day_num();
});

$('.deliver_count_per_day').change(function(){

    change_order_day_num();
});



$('#start_at').change(function(){
    change_order_day_num();
});

$(document).on('click', '#week td', function(){
    change_order_day_num();
});

/**
 * 点击数量增加按钮
 */
$(".plus").click(function () {
    incrementCount($(this));
    change_order_day_num();
});

/**
 * 点击数量减少按钮
 */
$(".minus").click(function () {
    decrementCount($(this));
    change_order_day_num();
});