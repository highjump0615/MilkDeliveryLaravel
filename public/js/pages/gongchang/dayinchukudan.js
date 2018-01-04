/**
 * Created by Administrator on 2/26/17.
 */

$(document).ready(function(){
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green'
        });
    });
$('.footable').footable();

$('#date_2 .input-group.date').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    calendarWeeks: false,
    autoclose: true
});

$('#search').click(function () {
    var station_name = $('#station_name').val();
    var station_number = $('#station_number').val();
    var address = $('#address').val();
    var date = $('#date').val();
    window.location.href = SITE_URL+"milk/public/gongchang/shengchan/naizhanpeisong/dayinchukuchan/?station_name="+station_name+"&date="+date+"&station_number="+station_number+"&address="+address+"";
});

/**
 * 保存 & 打印出库单内容
 */
$('button[data-action = "print"]').click(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    // 处理过程中，禁用该按钮
    var butPrint = $(this);
    butPrint.prop("disabled",true);

    var aryBoxData = [];

    // 遍历奶框信息
    $('tr.boxtype').each(function() {
        // 获取奶框数量
        var nBoxCount = parseInt($(this).find('.boxcount').html());
        var nBoxId = parseInt($(this).attr('value'));
        if (nBoxCount > 0) {
            var boxData = {
                id: nBoxId,
                count: nBoxCount
            };

            aryBoxData.push(boxData);
        }
    });

    var formData = {
        station_id: parseInt($('#input_stationid').val()),
        sender_name: $('#input_name').val(),
        car_num: $('#input_carnum').val(),
        naizhantel: $('#input_naizhantel').val(),
        tel: $('#input_tel').val(),
        box_data: aryBoxData
    };

    // 调用保存api
    $.ajax({
        type: 'POST',
        url: API_URL + 'gongchang/shengchan/naizhanpeisong/dayinchukuchan/save',
        dataType: 'json',
        data: formData,
        success: function (data) {
            butPrint.prop("disabled", false);
        },
        error: function (data) {
            butPrint.prop("disabled", false);

            show_err_msg('Error:', data);
            console.log('Error:', data);
        }
    });

    //
    // 打印
    //

    // 填写里面的文字输入框
    $('#table1 input[type=text]').each(function () {
        $(this).prop('outerHTML', $(this).val());
    });

    printContent('table1');
});

$('#return').click(function () {
    window.location.href = SITE_URL + "gongchang/shengchan/naizhanpeisong";
});
