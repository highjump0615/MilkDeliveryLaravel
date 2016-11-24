
$(document).ready(function() {
    $('#date_2 .input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: false,
        autoclose: true
    });

    $('.footable').footable();

    $('#current_status tr:not(:first,:last)').each(function(){
        var id=$(this).attr('id');

        calcRemainAfter(id);
        calcRemaining(this);
    });

    // 隐藏打印出库单按钮
    $('#by_station tr:not(:first,:last)').each(function() {
        var order = $(this).attr('order');
        $('#f_detail'+order+'').hide();
    });
});

/**
 * 计算富余量
 * @param tr
 */
function calcRemaining(tr) {
    var plan_count = parseInt($(tr).find("td").eq(2).html());
    var produce_count = parseInt($(tr).find("td").eq(3).html());
    $(tr).find("td").eq(4).html(produce_count-plan_count);
}

/**
 * 计算库存结余
 * @param id
 */
function calcRemainAfter(id) {
    var total_sum = 0;
    $('#by_station tr:not(:first,:last)').each(function() {
        var trval = $(this).attr('value');
        var order = $(this).attr('order');
        var content = parseInt($(this).find('#confirm'+order+''+trval+'').text());
        if(isNaN(content)){
            content = 0;
        }
        else {
            if (trval == id) {
                total_sum += content;
            }
        }
    });
    $('#total_confirm'+id+'').html(total_sum);
    var produced_count = parseInt($('#produce_count'+id+'').text());
    $('#rest'+id+'').html(produced_count-total_sum);
}

$(document).on('keyup','.confirm_count',function(){
    var id = $(this).attr('value');
    var total_sum = 0;
    $('#by_station tr:not(:first,:last)').each(function() {
        var trval = $(this).attr('value');
        var order = $(this).attr('order');
        var content = parseInt($(this).find('#confirm'+order+''+trval+'').text());
        if(isNaN(content)){
            content = 0;
        }
        else {
            if (trval == id) {
                total_sum += content;
            }
        }
    });
    $('#total_confirm'+id+'').html(total_sum);
    var produced_count = parseInt($('#produce_count'+id+'').text());
    $('#rest'+id+'').html(produced_count-total_sum);
});

$(document).on('keyup','.product_count',function() {
    var nCountProduct = parseInt($(this).html());
    var trParent = $(this).parent();

    // 计算富余量
    calcRemaining(trParent);

    // 计算库存结余
    var id=$(trParent).attr('id');
    calcRemainAfter(id);
});