var current_row_number;

/**
 * 日期、配送员筛选
 */
function filterMilkmanDate() {
    var milkman_id = $('#milkman_name option:selected').val();
    var current_date = $('#search_date').val();

    // 日期筛选
    var strUrl = SITE_URL+"naizhan/shengchan/peisongfanru?current_date="+current_date + "";

    // 配送员筛选
    if (parseInt(milkman_id) > 0) {
        strUrl += "&milkman_id=" + milkman_id + "";
    }

    window.location.href = strUrl;
}

$(document).on('change','#milkman_name',function(){
    filterMilkmanDate();
});

$(document).on('change','#date_select',function(){
    filterMilkmanDate();
});

$(document).on('click','#save',function(){
    var bValid = true;

    // check validation
    $('.delivered_count').each(function() {
        // 只检查输入框
        if (!$(this).attr('contenteditable')) {
            return;
        }

        // not number, return with error
        if (isNaN(parseInt($(this).text()))){
            show_err_msg('配送数量只能输入数字');
            $(this).focus();
            bValid = false;

            return false;
        }
    });

    if (!bValid) {
        return;
    }

    confirmdelivery();

    $('body').css({'cursor': 'progress'});
    $(this).prop('disabled', true);
    $(this).html('正在保存...');
});

function savebottlebox() {

    var url = API_URL + 'naizhan/shengchan/peisongfanru/bottleboxsave';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    var milkman_id = $('#current_milkman_id').val();
    $('#refund_bottle tr:not(:first)').each(function () {
        var bottle_type = $(this).attr('id');
        var count = parseInt($(this).find('td:eq(1)').text());

        var form_data = {
            milkman_id: milkman_id,
            bottle_type: bottle_type,
            count: count
        };

        var type = "POST";

        $.ajax({
            type: type,
            url: url,
            data: form_data,
            dataType:'json',
            success: function (data) {
                console.log(data);

                location.reload();
            },
            error:function (data) {
                console.log('Error:',data);

                restoreSaveButton();
            }
        });
    })
}

function confirmdelivery() {

    var url = API_URL + 'naizhan/shengchan/peisongfanru/confirmdelivery';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    var table_info = [];

    $('#delivery_table tr.order_info').each(function () {

        // 只添加输入框的内容
        if (!$(this).find('.delivered_count').attr('contenteditable')) {
            return;
        }

        var mdp_id = $(this).find('.delivered_count').attr('id');
        var delivered_product_count = parseInt($(this).find('.delivered_count').text());
        var report = $(this).find('.report').text();

        var formData = {
            mdp_id: mdp_id,
            delivered_count: delivered_product_count,
            report: report
        };

        table_info.push(formData);
    });
    var send_type = "POST";

    $.ajax({
        type: send_type,
        url: url,
        contentType: 'json',
        processData: false,
        data: JSON.stringify(table_info),
        success: function (data) {
            console.log(data);

            savebottlebox();
        },
        error:function (data) {
            console.log('Error:',data);

            restoreSaveButton();
        }
    });
}

/**
 * 恢复保存按钮
 */
function restoreSaveButton() {
    $('#save').prop('disabled', false);
    $('#save').html('保存');
    $('body').css({'cursor': 'default'});
}

$('#date_select .input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    calendarWeeks: false,
    autoclose: true
});

$('button[data-action = "print"]').click(function () {
    printContent('delivered_info', 0, '');
});

$('#return').click(function () {
    window.location = SITE_URL + "naizhan/shengchan/peisongliebiao";
});