var current_row_number;

$(document).on('click','.determine_count',function(e){
    var order = $(this).val();
    var url = API_URL + 'gongchang/shengchan/naizhanpeisong';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    var butSubmit = $(this);

    $('#by_station tr:not(:first,:last)').each(function(){
        var id = $(this).attr('value');

        var nRemain = parseInt($('#rest'+id).html());
        if (nRemain < 0) {
            show_err_msg('库存不足，请重新提交');
            return;
        }

        if($(this).attr('id')=='tablerow'+order+''){
            var station_id = $('#station_id'+order+'').attr('value');
            var formData = {
                actual_count: $('#confirm'+order+''+id+'').text(),
                product_id: id,
                station_id: station_id
            };
            console.log(formData);
            var type = "PUT";
            current_row_number = $(this).closest('tr').find('td:first').text();

            butSubmit.prop("disabled",true);

            $.ajax({
                type: type,
                url: url,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    $('#f_detail'+order+'').show();
                    $('#detail'+order+'').hide();
                    $('#detail'+order+'').parent().parent().find('td:eq(13)').html('已发货');
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }
    });
});