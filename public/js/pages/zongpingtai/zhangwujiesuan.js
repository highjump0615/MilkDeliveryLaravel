/**
 * Created by Administrator on 16/12/9.
 */

var date = new Date();
firstm = new Date(date.getFullYear(), date.getMonth(), 1);
lastm = new Date(date.getFullYear(), date.getMonth() + 1, 0);

$(document).ready(function () {
    $('.data_range_select .input-daterange').datepicker({
        keyboardNavigation: false,
        forceParse: false,
        autoclose: true,
        clearBtn: true,
        startDate: firstm,
        endDate: lastm,
    });

    $('#filter_factory').trigger('change');

});

//        $('#create_transaction_form').submit(function (e) {
//            e.preventDefault();
//
//            var sendData = $(this).serializeArray();
//            $.ajax({
//                type: "POST",
//                url: API_URL + 'zongpingtai/caiwu/zhangwu/create_transaction',
//                data: sendData,
//                success: function (data) {
//                    console.log(data);
//                    if (data.status == "success") {
//                        var fid = data.factory_id;
//                        window.location = SITE_URL + "zongpingtai/caiwu/zhangwujiesuan/zhangdanzhuanzhang/" + fid;
//                    } else {
//                        return;
//                    }
//                },
//                error: function (data) {
//                    console.log(data);
//
//                }
//            })
//
//        });

//According to factory, show station list
$('#filter_factory').on('change', function () {

    var factory_id = $(this).val();
    $('#factory_id').val(factory_id);

    $('#show_history').attr('href', 'zhangwujiesuan/lishizhuanzhangjiru/' + factory_id);
    $('#show_todo').attr('href', 'zhangwujiesuan/zhangdanzhuanzhang/' + factory_id);
    /*
     var station_list = $('#filter_station');
     $(station_list).empty();

     var sendData = {'factory_id': factory_id};
     $.ajax({
     type: "POST",
     url: API_URL + 'factory_to_station',
     data: sendData,
     success: function (data) {
     console.log(data);
     var stations = data.stations;
     if (stations) {
     station_list.append('<option value="none">全部</option>');

     for (var i = 0; i < stations.length; i++) {
     var sid = stations[i][0];
     var sname = stations[i][1];

     var option = '<option value="' + sid + '">' + sname + '</option>';
     station_list.append(option);
     }

     $(station_list).trigger('change');
     }
     },
     error: function (data) {
     console.log(data);
     }
     })
     */
});

$('#filter_station').change(function () {

    var station_id = $(this).val();
    $('#station_id').val(station_id);
});

//Export
$('button[data-action = "export_csv"]').click(function () {

    var od = $('#order_table').css('display');
    var fd = $('#filter_table').css('display');

    if (od != "none") {
        data_export('order_table', gnUserTypeAdmin, '财务结算', 0, 0);
    }
    else if (fd != "none") {
        data_export('filter_table', gnUserTypeAdmin, '财务结算', 0, 0);
    }
});

//Print Table Data
$('button[data-action = "print"]').click(function () {

    var od = $('#order_table').css('display');
    var fd = $('#filter_table').css('display');

    if (od != "none") {
        printContent('order_table', gnUserTypeAdmin, '财务管理');
    }
    else if (fd != "none") {
        printContent('filter_table', gnUserTypeAdmin, '财务管理');
    }
});
