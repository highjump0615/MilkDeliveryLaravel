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
    var bSufficient = true;

    // 检查库存
    $('#by_station tr:not(:first,:last)').each(function() {
        var id = $(this).attr('value');

        if ($(this).attr('id') == 'tablerow' + order + '') {
            var nRemain = parseInt($('#rest' + id).html());
            if (nRemain < 0) {
                show_err_msg('库存不足，请重新提交');
                bSufficient = false;
            }
        }
    });

    // 库存不足，直接退出
    if (!bSufficient) {
        return;
    }

    $('#by_station tr:not(:first,:last)').each(function(){
        var id = $(this).attr('value');

        if ($(this).attr('id')=='tablerow'+order+''){
            // 去掉空格
            var strCount = $('#confirm'+order+''+id+'').text().replace(/\s+/g, '');

            var station_id = $('#station_id'+order+'').attr('value');
            var formData = {
                actual_count: strCount,
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


/**
 * 保存实际生产量
 */
$(document).on('click','#but_save',function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    $('#but_save').prop("disabled",true);

    var table_info = [];

    $('#current_status tr:not(:first,:last)').each(function(){
        var strId = $(this).attr('id');
        var strRealCount = $(this).find('td:eq(3)').html();

        if (parseInt(strRealCount) >= 0) {
            var formData = {
                id: parseInt(strId),
                count: parseInt(strRealCount),
            };

            table_info.push(formData);
        }
    });

    // 调用api
    $.ajax({
        type: 'POST',
        url: API_URL + 'gongchang/shengchan/naizhanpeisong/save',
        contentType: 'json',
        processData: false,
        data: JSON.stringify(table_info),
        success: function (data) {
            $('#but_save').prop("disabled", false);
        },
        error: function (data) {
            $('#but_save').prop("disabled", false);

            show_err_msg('Error:', data);
            console.log('Error:', data);
        }
    });
});
