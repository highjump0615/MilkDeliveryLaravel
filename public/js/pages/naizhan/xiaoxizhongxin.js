
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

//Filter Function
$('button[data-action="show_selected"]').click(function () {
    var view_table = $('#notification_table');
    var filter_table = $('#filter_table');
    var filter_table_tbody = $('#filter_table tbody');

    //get all selection
    var f_status = $('#status').val().trim().toLowerCase();
    var f_type = $('#type').val().trim().toLowerCase();
    var f_start_date = $('#start_date').val().trim().toLowerCase();
    var f_end_date = $('#end_date').val().trim().toLowerCase();
    //show only rows in filtered table that contains the above field value
    var filter_rows = [];
    var i = 0;

    $('#notification_table').find('tbody tr').each(function () {
        var tr = $(this);

        status = $(this).find('td.status').text().toLowerCase();
        type = $(this).find('td.type').text().toLowerCase();
        setted_date = $(this).find('td.current_date').text().toLowerCase();
        date1 = setted_date.split(" ");
        current_date = date1[0];

        if ((f_status != "" && status.includes(f_status)) || (f_status == "")) {
            tr.attr("data-show-1", "1");
        } else {
            tr.attr("data-show-1", "0")
        }

        if ((f_type != "" && type.includes(f_type)) || (f_type == "")) {
            tr.attr("data-show-2", "1");
        } else {
            tr.attr("data-show-2", "0")
        }

        if ((f_start_date == "" && f_end_date == "") || (!current_date)) {
            tr.attr("data-show-3", "1");
        } else if (f_start_date == "" && f_end_date != "") {

            var f2 = new Date(f_end_date);
            var oo = new Date(current_date);
            if (oo <= f2) {
                tr.attr("data-show-3", "1");
            } else {
                tr.attr("data-show-3", "0");
            }

        } else if (f_start_date != "" && f_end_date == "") {

            var f1 = new Date(f_start_date);
            var oo = new Date(current_date);
            if (oo >= f1) {
                tr.attr("data-show-3", "1");
            } else {
                tr.attr("data-show-3", "0");
            }
        } else {
            //f_order_start_date, f_order_end_date, o_ordered
            var f1 = new Date(f_start_date);
            var f2 = new Date(f_end_date);
            var oo = new Date(current_date);
            if (f1 <= f2 && f1 <= oo && oo <= f2) {
                tr.attr("data-show-3", "1");

            } else if (f1 >= f2 && f1 >= oo && oo >= f2) {
                tr.attr("data-show-3", "1");

            } else {

                tr.attr("data-show-3", "0");
            }
        }

        if ((tr.attr("data-show-1") == "1" ) && (tr.attr("data-show-2") == "1") && (tr.attr("data-show-3") == "1")) {
            //tr.removeClass('hide');
            $(tr).find('td:eq(0)').html(i+1);
            filter_rows[i] = $(tr)[0].outerHTML;
            i++;
            //filter_rows += $(tr)[0].outerHTML;

        } else {
            //tr.addClass('hide');
        }
    });

    $(view_table).hide();
    $(filter_table_tbody).empty();

    var length = filter_rows.length;

    var footable = $('#filter_table').data('footable');

    for (i = 0; i < length; i++) {
        var trd = filter_rows[i];
        footable.appendRow(trd);
    }

    $(filter_table).show();
});