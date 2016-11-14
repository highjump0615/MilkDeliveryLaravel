var current_row_number;

$(document).on('click','.confirm_values',function(e){
    $(this).prop("disabled",true);
    $(this).html("已签收");

    var update_url = API_URL + 'naizhan/shengchan/qianshoujihua/confirm_product';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })

    $('#confrim_arrived_product tr:not(:first)').each(function(){
        var id = $(this).attr('id');
        var station_id = $(this).attr('value');
        var formData = {
            confirm_count: $('#product'+id+'').val(),
            product_id: id,
            station_id: station_id,
        }
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
                tdEdit.removeClass('editfill');
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    })

    var store_url = API_URL + 'naizhan/shengchan/qianshoujihua/refund_bb';
    $('#refund_table tr:not(:first) td:not(:first)').each(function(){
        var object_type = $(this).attr('object_type');
        var types = $(this).attr('types');
        var formData = {
            types: types,
            object_type: object_type,
            return_to_factory: $('#'+types+''+object_type+'').val(),
        }
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
                // $('.confirm_values').hide();
                tdEdit.removeClass('editfill');
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    })
})