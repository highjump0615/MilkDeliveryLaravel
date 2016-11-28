
var firstm = new Date(gDateToday.getFullYear(), gDateToday.getMonth(), 1);
var lastm = new Date(gDateToday.getFullYear(), gDateToday.getMonth() + 1, 0);

$(document).ready(function (){

    // 初始化配送规则日历
    $('#product_table').find('tbody > tr').each(function() {
        initBottleNumCalendar($(this));
    });
});

//Show delivery date on calendar
function initBottleNumCalendar(tr) {
    var calendar = tr.find('.calendar_show')[0];

    var selectDeliveryType = tr.find('.show_delivery_date')[0];
    var pick = tr.find('.picker')[0];

    // 获取输入的每次瓶数
    var inputBottleNum = tr.find('.order_product_count_per')[0];
    var nBottleNum = parseInt($(inputBottleNum).text());

    var id = $(selectDeliveryType).data('type');

    if (id == 3 || id == 4) {

        var initNum = $(calendar).find('input').val();

        if (id == 3) {
            console.log(id);
            //show weekday
            $(pick).datepicker({
                multidate: true,
                todayBtn: false,
                clearBtn: false,
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: false,
                showNum: true,
                bottleNum: nBottleNum,
                initValue: initNum,
                startDate: '2016-11-13',
                endDate: '2016-11-19',
                class:'week_calendar only_show',
            });
        }
        else {
            console.log(id);
            //show monthday
            $(pick).datepicker({
                multidate: true,
                todayBtn: false,
                clearBtn: false,
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: false,
                showNum: true,
                bottleNum: nBottleNum,
                initValue: initNum,
                startDate: firstm,
                endDate: lastm,
                class:'month_calendar only_show',
            });

        }

        $(calendar).find('input').prop('disabled', true);

    } else {
        $(calendar).hide();
    }
}

$('button.show_delivery_date').click(function(){
    var ob1 = $(this).parent().find('.input-group-addon');
    $(ob1).trigger('click');
});