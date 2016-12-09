/**
 * Created by Administrator on 16/12/9.
 */

$('#date_1 .input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    calendarWeeks: false,
    autoclose: true
});

$(document).ready(function () {
    $('#statistics_by_day tr:not(:first,:last)').each(function () {
        var f_yuedan = 0;
        $(this).find('td.yuedan').each(function () {
            var y_val = parseInt($(this).text());
            if(isNaN(y_val)){
                y_val = 0;
            }
            f_yuedan +=y_val;
        });
        $(this).find('td.f_yuedan').html(f_yuedan);

        var f_jidan = 0;
        $(this).find('td.jidan').each(function () {
            var j_val = parseInt($(this).text());
            if(isNaN(j_val)){
                j_val = 0;
            }
            f_jidan +=j_val;
        });
        $(this).find('td.f_jidan').html(f_jidan);

        var f_banniandan = 0;
        $(this).find('td.banniandan').each(function () {
            var b_val = parseInt($(this).text());
            if(isNaN(b_val)){
                b_val = 0;
            }
            f_banniandan +=b_val;
        });
        $(this).find('td.f_banniandan').html(f_banniandan);

        var f_gift = 0;
        $(this).find('td.gift').each(function () {
            var g_val = parseInt($(this).text());
            if(isNaN(g_val)){
                g_val = 0;
            }
            f_gift +=g_val;
        });
        $(this).find('td.f_gift').html(f_gift);

        var f_channel = 0;
        $(this).find('td.channel').each(function () {
            var c_val = parseInt($(this).text());
            if(isNaN(c_val)){
                c_val = 0;
            }
            f_channel +=c_val;
        });
        $(this).find('td.f_channel').html(f_channel);

        var product_type = [];
        var j=0;
        $(this).find('td.total').each(function () {
            product_type[j] = $(this).attr('product_type');
            j++;
        });

        for(i = 0; i<product_type.length; i++)
        {
            var f_subtotal = 0;
            $(this).find('td.'+product_type[i]+'').each(function () {
                var t_val = parseInt($(this).text());
                if(isNaN(t_val)){
                    t_val = 0;
                }
                f_subtotal +=t_val;
            });
            $(this).find('td.f_'+product_type[i]+'').html(f_subtotal);
        }

        var f_total = 0;
        $(this).find('td.total').each(function () {
            var total_val = parseInt($(this).text());
            if(isNaN(total_val)){
                total_val = 0;
            }
            f_total +=total_val;
        });
        $(this).find('td.f_totalsum').html(f_total);
    });
});

$(document).on('click','#search',function () {
    var start_date = $('#start_date').val();
    window.location.href = SITE_URL+"milk/public/naizhan/tongji/naipinpeisongri/?start_date="+start_date+"";
});

$('button[data-action = "print"]').click(function () {
    printContent('statistics_by_day', gnUserTypeStation, '奶品配送日统计');
});
