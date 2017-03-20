
$(document).ready(function() {
    $('#date_select .date').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true,
        calendarWeeks: false,
        clearBtn: true
    }).on('changeDate', function(e) {
        // 用新的日期刷新页面
        var strDate = $('#search_date').val();
        window.location.href = SITE_URL + "gongchang/shengchan/naizhanpeisong?date=" + strDate;
    });

    $('.footable').footable();

    $('#current_status tr:not(:first,:last)').each(function(){
        var id=$(this).attr('id');

        calcRemainAfter(id);
        calcRemaining(this);
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