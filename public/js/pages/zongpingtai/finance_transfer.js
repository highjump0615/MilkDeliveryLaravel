/**
 * Created by Administrator on 16/12/10.
 */

$(document).ready(function () {
    $('#all_check .iCheck-helper').css('display', 'none');

    $('#all_check').click(function () {
        var check = $(this).find('.i-checks')[0];
        var checked = $(check).prop('checked');
        if (checked) {
            $('#order_table input.i-checks').each(function () {
                $(this).prop("checked", true);
                $(this).closest('.icheckbox_square-green').addClass('checked');
            });
        }
        else {
            $('#order_table input.i-checks').each(function () {
                $(this).prop("checked", false);
                $(this).closest('.icheckbox_square-green').removeClass('checked');
            });
        }
    });

    $('.icheckbox_square-green').click(function () {

        var check = $(this).find('.i-checks')[0];
        var checked = $(check).prop('checked');

        if (checked) {
            $('#order_table input.i-checks').each(function () {
                $(this).prop("checked", true);
                $(this).addClass('checked');
            });
        } else {
            $('#order_table input.i-checks').each(function () {
                $(this).prop("checked", false);
                $(this).removeClass('checked');
            });
        }
    });

    $('#insert_modal_form').submit(function (e) {
        e.preventDefault();
        var sendData = $(this).serializeArray();
        console.log(sendData);

        $.ajax({
            type: 'POST',
            url: API_URL + 'zongpingtai/caiwu/zhangwujiesuan/zhangdanzhuanzhang/complete_trans',
            data: sendData,
            success: function (data) {
                if (data.status = "success") {
                    show_success_msg(" 转账账单成功");

                    $('#insert_modal_form').modal('hide');
                    window.location.href = SITE_URL + "zongpingtai/caiwu/zhangwujiesuan/lishizhuanzhangjiru/" + data.factory;
                }
            },
            error: function (data) {
                console.log(data);
            }
        })

    });

    $('button[data-action="insert_modal"]').click(function () {
        var trans_id_list = [];
        //get selected trans id

        $('#order_table input.i-checks').each(function () {
            var checked = $(this).prop("checked");
            if (checked) {
                var sid = $(this).data('station-id');

                $('#order_table tbody tr td.o_station[data-sid="' + sid + '"]').each(function () {
                    var tid = $(this).data('trsid');
                    trans_id_list.push(tid);
                });
            }
        });

        if (trans_id_list.length == 0) {
            return;
        }

        $('#insert_modal').modal('show');

        var sendData = {'tids': trans_id_list};
        console.log(sendData);

        $.ajax({
            type: "POST",
            url: API_URL + 'zongpingtai/caiwu/zhangwujiesuan/zhangdanzhuanzhang/get_trans_data',
            data: sendData,
            success: function (data) {
                console.log(data);
                if (data.status == "success") {
                    var trs_list = data.trs;

                    var ptable = $('#insert_modal_table');
                    var ptbody = $('#insert_modal_table tbody');
                    $(ptbody).empty();

                    for (var i = 0; i < trs_list.length; i++) {
                        var trs = trs_list[i];
                        var trd = '<tr><td>' + (i + 1) + '</td><td>' + trs[0] + '<input type="hidden" name="delivery_station_id[]" value="' + trs[5] + '">' + '<input type="hidden" name="trs_ids[]" value="' + trs[4] + '"></td><td>' + trs[1] + '</td><td>' + trs[2] + '</td>';
                        trd += '<td>' + trs[3] + '<input type="hidden" name="total_amount[]" value="'+trs[3]+'"></td>';
                        trd += '<td><input type="number" required min="0" step="0.01" name="real_input[]" type="text"/></td><td><input required  name="trans_number[]" type="text"/></td><td><input name="comment[]" type="text"/></td>';
                        ptbody.append(trd);
                    }
                }

            },
            error: function (data) {
                console.log(data);
            }
        });
    });

    $('button[data-action = "print_modal_data"]').click(function () {
        printContent('print_modal_table', 0, '');
    });

    $('button[data-action="print_modal"]').click(function () {

        var trans_id_list = [];
        //get selected trans id

        $('#order_table input.i-checks').each(function () {
            var checked = $(this).prop("checked");
            if (checked) {
                var sid = $(this).data('station-id');

                $('#order_table tbody tr td.o_station[data-sid="' + sid + '"]').each(function () {
                    var tid = $(this).data('trsid');
                    trans_id_list.push(tid);
                });
            }
        });

        if (trans_id_list.length == 0) {
            return;
        }

        $('#print_modal').modal('show');

        var sendData = {'tids': trans_id_list};
        console.log(sendData);

        $.ajax({
            type: "POST",
            url: API_URL + 'zongpingtai/caiwu/zhangwujiesuan/zhangdanzhuanzhang/get_trans_data',
            data: sendData,
            success: function (data) {
                console.log(data);
                if (data.status == "success") {
                    var trs_list = data.trs;

                    var ptable = $('#print_modal_table');
                    var ptbody = $('#print_modal_table tbody');
                    $(ptbody).empty();

                    for (var i = 0; i < trs_list.length; i++) {
                        var trs = trs_list[i];
                        var trd = '<tr><td>' + (i + 1) + '</td><td>' + trs[0] + '</td><td>' + trs[1] + '</td><td>' + trs[2] + '</td><td>' + trs[3] + '</td>';
                        trd += '<td></td><td></td><td></td>';
                        ptbody.append(trd);
                    }
                }

            },
            error: function (data) {
                console.log(data);
            }
        });
    });

    //Export
    $('button[data-action = "export_csv"]').click(function () {
        data_export('order_table', 0, '', 0, 0);
    });

    //Print Table Data
    $('button[data-action = "print"]').click(function () {
        printContent('order_table', 0, '');
    });

});

