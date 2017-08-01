
$('#active').click(function () {

    var checkValues = $('input[name=change_status]:checked').map(function()
    {
        return $(this).val();
    }).get();

    var url = API_URL + 'naizhan/xiaoxi/zhongxin/changeActiveStatus';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })

    for(i = 0; i < checkValues.length; i++){

        var form_data = {
            id: checkValues[i],
        }

        var type = "POST";

        $.ajax({
            type: type,
            url: url,
            data: form_data,
            dataType:'json',
            success: function (data) {
                var role = '<td id="status'+data.id+'">已读</td>';
                var notification = '<span id="notification" class="label label-danger">'+data.unread+'</span>';
                $('#status'+data.id).replaceWith(role);
                $('#'+data.id).css("font-weight","");
                $('.i-checks').each(function () {
                    $(this).prop("checked", false);
                    $(this).closest('.icheckbox_square-green').removeClass('checked');
                })
                $('#notification').replaceWith(notification);

            },
            error:function (data) {
                console.log('Error:',data);
            }
        });
    }
})

$('#inactive').click(function () {

    var checkValues = $('input[name=change_status]:checked').map(function()
    {
        return $(this).val();
    }).get();

    var url = API_URL + 'naizhan/xiaoxi/zhongxin/changeInActiveStatus';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    })

    for(i = 0; i < checkValues.length; i++){

        var form_data = {
            id: checkValues[i],
        }

        var type = "POST";

        $.ajax({
            type: type,
            url: url,
            data: form_data,
            dataType:'json',
            success: function (data) {
                var role = '<td id="status'+data.id+'" class="status">未读</td>';
                var notification = '<span id="notification" class="label label-danger">'+data.unread+'</span>'
                $('#status'+data.id).replaceWith(role);
                $('#'+data.id).css("font-weight","bold");
                $('.i-checks').each(function () {
                    $(this).prop("checked", false);
                    $(this).closest('.icheckbox_square-green').removeClass('checked');
                });
                $('#notification').replaceWith(notification);
            },
            error:function (data) {
                console.log('Error:',data);
            }
        });
    }
});