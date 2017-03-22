/**
 * Created by Administrator on 16/12/9.
 */

$(document).ready(function () {
    $('.footable').footable();
    $('#today_input tr:not(:first)').each(function () {
        var init = 0;
        var station_refunds = 0;
        if(!isNaN(parseInt($(this).find('td:eq(1)').text()))){
            init = parseInt($(this).find('td:eq(1)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(2)').text()))){
            station_refunds = parseInt($(this).find('td:eq(2)').text());
        }
        var total = init + station_refunds;
        $(this).find('td:eq(6)').html(total);
    })
});

$(document).on('keyup','.inputable_cell',function () {
    $('#today_input tr:not(:first)').each(function () {
        var init = 0;
        var station_refunds = 0;
        var etc_refunds = 0;
        var production = 0;
        var damage = 0;
        if(!isNaN(parseInt($(this).find('td:eq(1)').text()))){
            init = parseInt($(this).find('td:eq(1)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(2)').text()))){
            station_refunds = parseInt($(this).find('td:eq(2)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(3)').text()))){
            etc_refunds = parseInt($(this).find('td:eq(3)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(4)').text()))){
            production = parseInt($(this).find('td:eq(4)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(5)').text()))){
            damage = parseInt($(this).find('td:eq(5)').text());
        }
        var total = init + station_refunds + etc_refunds + production - damage;
        $(this).find('td:eq(6)').html(total);
    })
});

$(document).on('click','#save',function () {
    var start_date = $('#start').val();
    var end_date = $('#end').val();
    var confirm_url = API_URL + 'gongchang/pingkuang/pingkuang/save';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })

    $('#today_input tr:not(:first)').each(function () {

        var type = $(this).find('#type').val();
        var type_id =$(this).find('td:eq(0)').attr('value');
        var init_store_count = 0;
        if(!isNaN(parseInt($(this).find('td:eq(1)').text()))){
            init_store_count = parseInt($(this).find('td:eq(1)').text());
        }
        var station_refunds_count = 0;
        if(!isNaN(parseInt($(this).find('td:eq(2)').text()))){
            station_refunds_count = parseInt($(this).find('td:eq(2)').text());
        }
        var etc_refunds_count = 0;
        if(!isNaN(parseInt($(this).find('td:eq(3)').text()))){
            etc_refunds_count = parseInt($(this).find('td:eq(3)').text());
        }
        var production_count = 0;
        if(!isNaN(parseInt($(this).find('td:eq(4)').text()))){
            production_count = parseInt($(this).find('td:eq(4)').text());
        }
        var store_damage_count = 0;
        if(!isNaN(parseInt($(this).find('td:eq(5)').text()))){
            store_damage_count = parseInt($(this).find('td:eq(5)').text());
        }
        var final_count = 0;
        if(!isNaN(parseInt($(this).find('td:eq(6)').text()))){
            final_count = parseInt($(this).find('td:eq(6)').text());
        }

        var data = {
            type:type,
            object_type: type_id,
            init_store_count: init_store_count,
            station_refunds_count: station_refunds_count,
            etc_refunds_count: etc_refunds_count,
            production_count: production_count,
            store_damage_count: store_damage_count,
            final_count: final_count,
        }

        var send_type = "POST";

        $.ajax({
            type: send_type,
            url: confirm_url,
            data: data,
            dataType:'json',
            success: function (data) {
                console.log(data);
            },
            error:function (data) {
                console.log('Error:',data);
            }
        });

    });

    window.location.href = SITE_URL+"gongchang/pingkuang/pingkuang";
});

$('#data_calendar .input-daterange').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true
});

$(document).on('click','#search',function () {
    var bottle_name = $('#bottle_type').val();
    var box_name = $('#box_type').val();
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();
    window.location.href = SITE_URL+"gongchang/pingkuang/pingkuang/?bottle_name="+bottle_name+"&box_name="+box_name+"&start_date="+start_date+"&end_date="+end_date+"";
});

$('button[data-action = "print"]').click(function () {
    printContent('table1', gnUserTypeFactory, '瓶框库存管理');
});

$('button[data-action = "export_csv"]').click(function () {
    data_export('table1', gnUserTypeFactory, '瓶框库存管理', 0, 0);
});
