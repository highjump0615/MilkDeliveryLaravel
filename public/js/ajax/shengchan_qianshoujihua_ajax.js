var current_row_number;

$(document).on('click','.confirm_values',function(e){

    var update_url = API_URL + 'naizhan/shengchan/qianshoujihua/confirm_product';
    var bSent = false;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    $('#confrim_arrived_product tr:not(:first)').each(function(){
        var id = $(this).attr('id');
        var station_id = $(this).attr('value');

        // 去掉空格
        var strCount = $('#product'+id+'').val().replace(/\s+/g, '');

        var formData = {
            confirm_count: strCount,
            product_id: id,
            station_id: station_id
        };
        console.log(formData);

        var type = "PUT";
        var tdEdit = $(this).find('.editfill');

        $.ajax({
            type: type,
            url: update_url,
            data: formData,
            dataType: 'json',
            success: function (data) {
                console.log('Success:', data);

                if (parseInt(data) > 0) {   // 收货成功
                    tdEdit.removeClass('editfill');
                }
                else {
                    if (!bSent) {
                        show_warning_msg("奶厂还没发货!");
                        bSent = true;
                    }
                }
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

    // 如果奶厂还没发货, 退出
    if (bSent) {
        return;
    }

    var store_url = API_URL + 'naizhan/shengchan/qianshoujihua/refund_bb';
    $('#refund_table tr:not(:first) td:not(:first)').each(function(){
        var object_type = $(this).attr('object_type');
        var types = $(this).attr('types');
        var formData = {
            types: types,
            object_type: object_type,
            return_to_factory: $('#'+types+''+object_type+'').val(),
        };
        console.log(formData);

        var type = "POST";
        var tdEdit = $(this);

        $.ajax({
            type: type,
            url: store_url,
            data: formData,
            dataType: 'json',
            success: function (data) {
                console.log(data);
                tdEdit.removeClass('editfill');

                // 确定按钮状态变化
                $('.confirm_values').html("已签收");
                $('.confirm_values').prop("disabled",true);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    })
});