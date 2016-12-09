/**
 * Created by Administrator on 16/12/9.
 */

$(document).ready(function () {
    $('#today_input tr:not(:first)').each(function () {
        var init = 0;
        var milkman = 0;
        var factory = 0;
        if(!isNaN(parseInt($(this).find('td:eq(1)').text()))){
            init = parseInt($(this).find('td:eq(1)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(2)').text()))){
            milkman = parseInt($(this).find('td:eq(2)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(3)').text()))){
            factory = parseInt($(this).find('td:eq(3)').text());
        }
        var total = init + milkman - factory;
        $(this).find('td:eq(5)').html(total);
    })
});

$(document).on('keyup','.damaged',function () {
    $('#today_input tr:not(:first)').each(function () {
        var init = 0;
        var milkman = 0;
        var factory = 0;
        var damage = 0;
        if(!isNaN(parseInt($(this).find('td:eq(1)').text()))){
            init = parseInt($(this).find('td:eq(1)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(2)').text()))){
            milkman = parseInt($(this).find('td:eq(2)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(3)').text()))){
            factory = parseInt($(this).find('td:eq(3)').text());
        }
        if(!isNaN(parseInt($(this).find('td:eq(4)').text()))){
            damage = parseInt($(this).find('td:eq(4)').text());
        }
        var total = init + milkman - factory - damage;
        $(this).find('td:eq(5)').html(total);
    })
});

$(document).on('click','#save',function () {
    var start_date = $('#start').val();
    var end_date = $('#end').val();
    var confirm_url = API_URL + 'naizhan/pingkuang/pingkuangshouhui/confirm';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    $('#today_input tr:not(:first)').each(function () {

        var type = $(this).find('#type').val();
        var type_id =$(this).find('td:eq(0)').attr('value');
        var init_store = 0;
        if(!isNaN(parseInt($(this).find('td:eq(1)').text()))){
            init_store = parseInt($(this).find('td:eq(1)').text());
        }
        var milkman_refund = 0;
        if(!isNaN(parseInt($(this).find('td:eq(2)').text()))){
            milkman_refund = parseInt($(this).find('td:eq(2)').text());
        }
        var station_damaged = 0;
        if(!isNaN(parseInt($(this).find('td:eq(4)').text()))){
            station_damaged = parseInt($(this).find('td:eq(4)').text());
        }
        var recipient = 0;
        if(!isNaN(parseInt($(this).find('td:eq(6)').text()))){
            recipient = parseInt($(this).find('td:eq(6)').text());
        }
        var end_store = 0;
        if(!isNaN(parseInt($(this).find('td:eq(5)').text()))){
            end_store = parseInt($(this).find('td:eq(5)').text());
        }

        var data = {
            type:type,
            bottle_type: type_id,
            init_store: init_store,
            milkman_refund: milkman_refund,
            station_damaged: station_damaged,
            end_store: end_store,
            received: recipient,
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

    window.location.href = SITE_URL+"milk/public/naizhan/pingkuang/pingkuangshouhui/?start_date="+start_date+"&end_date="+end_date+"";
});

$('#data_range_select .input-daterange').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true
});
$(document).on('click','#find',function () {
    var start_date = $('#start').val();
    var end_date = $('#end').val();
    window.location.href = SITE_URL+"milk/public/naizhan/pingkuang/pingkuangshouhui/?start_date="+start_date+"&end_date="+end_date+"";
});

$('button[data-action = "print"]').click(function () {
    printContent('table1', gnUserTypeStation, '瓶框收回记录');
});

$('button[data-action = "export_csv"]').click(function () {

    var sendData = [];

    var i = 0;
    //send order data
    $('#table1 thead tr').each(function () {
        var tr = $(this);
        var trdata = [];

        var j = 0;
        $(tr).find('th').each(function () {
            var td = $(this);
            var td_data = td.html().toString().trim();
            td_data =td_data.split("<");
            // if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
            //     td_data = "";
            trdata[j] = td_data[0];
            j++;
        });
        sendData[i] = trdata;
        i++;
    });

    $('#table1 tbody tr').each(function () {
        var tr = $(this);
        var trdata = [];

        var j = 0;
        $(tr).find('td').each(function () {
            var td = $(this);
            var td_data = td.html().toString().trim();
            if (td_data.includes('span') || td_data.includes('button') || td_data.includes('href'))
                td_data = "";
            trdata[j] = td_data;
            j++;
        });
        sendData[i] = trdata;
        i++;
    });

    var send_data = {"data": sendData};
    console.log(send_data);

    $.ajax({
        type: 'POST',
        url: API_URL + "export",
        data: send_data,
        success: function (data) {
            console.log(data);
            if (data.status == 'success') {
                var path = data.path;
                location.href = path;
            }
        },
        error: function (data) {
            //console.log(data);
        }
    })
});
