var current_row_number;

$(document).on('click','.validate',function(e){
    var pending_status = 0;
    $('#plan_sent tr:not(:first,:last)').each(function () {
        var value = parseInt($(this).find('.pendding_status').val());
        if(isNaN(value)){
            value = 0;
        }
        pending_status += value;
    })
    if(pending_status >0){
        $('#alert_view').show();
        return;
    }

    var url = API_URL + 'gongchang/shengchan/naizhanjihuashenhe/saveProduct';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })
    e.preventDefault();

    var id = $(this).val();
    var production_period = $('#production_period'+id+'').val()/24;
    var setted_val = $('#produce_amount'+id+'').val();

    var plan_val = parseInt($(this).parent().parent().find('td:eq(2)').val())
    if(isNaN(parseInt(plan_val))){
        plan_val = 0;
    }

    if(setted_val<plan_val){
        $(this).parent().parent().find('td:eq(5)').val('');
        setted_val = $('#produce_amount'+id+'').val('');
        show_info_msg("输入更大量比原计划汇总量");
        return;
    }

    if(!isNaN(parseInt(setted_val))) {
        var formData = {
            product_id: id,
            count: setted_val,
            produce_period: production_period,
        }
        console.log(formData);

        var type = "POST"; //for creating new resource
        var my_url = url;
        current_row_number = $(this).closest('tr').find('td:first').text();

        $.ajax({

            type: type,
            url: my_url,
            data: formData,
            dataType: 'json',
            success: function (data) {
                var new_status='<td>'+setted_val+'</td>';
                $("#check" + id).replaceWith( new_status );
                console.log(data);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    }
    else{
        show_warning_msg("请输入值!");
    }
})

$(document).on('click','.cancel',function(e){
    var id = $(this).val();
    var production_period = $('#production_period'+id+'').val()/24;
    var setted_val = $('#produce_amount'+id+'').val();

    var store_url = API_URL + 'gongchang/shengchan/naizhanjihuashenhe/cancelProduct';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })
    e.preventDefault();

    var store_formData = {
        product_id: id,
        count: setted_val,
        produce_period: production_period,
    }
    console.log(store_formData);

    var store_type = "POST"; //for creating new resource

    $.ajax({

        type: store_type,
        url: store_url,
        data: store_formData,
        dataType: 'json',
        success: function (data) {
            console.log(data);
            var new_status='<td id="check'+id+'">生产取消</td>';
            $("#check" + id).replaceWith( new_status );
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
})

$(document).on('click','.produce_determine',function (e) {
    var station_id = $(this).val();

    var store_url = API_URL + 'gongchang/shengchan/naizhanjihuashenhe/determine_station_plan';
    $('#alert_view').hide();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })
    e.preventDefault();

    var store_formData = {
        station_id: station_id,
    }
    console.log(store_formData);

    var tr = $(this).closest('tr');

    var store_type = "POST"; //for creating new resource

    $.ajax({

        type: store_type,
        url: store_url,
        data: store_formData,
        dataType: 'json',
        success: function (data) {
            console.log(data);
            var new_status='<td class="status'+station_id+'">正常</td>';
            $('#plan_sent').find('td.status'+station_id+'').each(function () {
                $('.status'+station_id+'').replaceWith(new_status);
            });

            tr.find('.produce_determine').hide();
            tr.find('.produce_cancel').hide();
            tr.find('.pendding_status').val('0');
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
})

$(document).on('click','.produce_cancel',function (e) {
    var station_id = $(this).val();

    var store_url = API_URL + 'gongchang/shengchan/naizhanjihuashenhe/cancel_station_plan';
    $('#alert_view').hide();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })
    e.preventDefault();

    var store_formData = {
        station_id: station_id,
    }
    console.log(store_formData);

    var tr = $(this).closest('tr');

    var store_type = "POST"; //for creating new resource

    $.ajax({

        type: store_type,
        url: store_url,
        data: store_formData,
        dataType: 'json',
        success: function (data) {
            console.log(data);
            var new_status='<td class="status'+station_id+'">生产取消</td>';
            $('#plan_sent').find('td.status'+station_id+'').each(function () {
                $('.status'+station_id+'').replaceWith(new_status);
            });

            tr.find('.produce_determine').hide();
            tr.find('.produce_cancel').hide();
            tr.find('.pendding_status').val('0');
            location.reload();
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });

    //window.location = SITE_URL
})